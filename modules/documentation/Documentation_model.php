<?php
class Documentation_model extends Model {

	private $docs_db = 'default';

	public function get_books() {
		$db = $this->docs_db;
		$books = $this->$db->get('priority', 'documentation_books');
		return $books;
	}

	public function get_chapters($book_url_string) {

		// Get the book_id
		$url_string = str_replace('_', '-', $book_url_string);
		$db = $this->docs_db;
		$book_obj = $this->$db->get_one_where('url_string', $url_string, 'documentation_books');
		
		if ($book_obj === false) {
			return false;
		}

		// Get the chapters for this book.
		$params['book_id'] = (int) $book_obj->id;
		$sql = 'SELECT * FROM documentation_chapters WHERE book_id = :book_id ORDER BY chapter_number';
		$db = $this->docs_db;
		$rows = $this->$db->query_bind($sql, $params, 'object');
		$chapters = $this->add_pages_to_chapters($params['book_id'], $rows);

		if (isset($chapters[1])) {
			$chapters[1]->book_obj = $book_obj;
		}

		return $chapters;
	}

	private function add_pages_to_chapters($book_id, $chapters) {

		// Give each chapter an arary of pages.
		$all_chapters = [];
		foreach($chapters as $k => $v) {
			$chapter_number = (int) $v->chapter_number;
			$v->pages = [];
			$all_chapters[$chapter_number] = $v;
		}

		// Fetch all of the pages for this book, ordered by chapter_number, page_number
		$params['book_id'] = $book_id;
		$sql = 'SELECT * FROM documentation_pages WHERE book_id = :book_id ORDER BY chapter_number, page_number';
		$db = $this->docs_db;

		$current_chapter_number = 0;
		$page_counter = 0;
		$all_book_pages = $this->$db->query_bind($sql, $params, 'object');
		foreach($all_book_pages as $key => $page_obj) {
			$page_counter++;
			$this_chapter_number = (int) $page_obj->chapter_number;

			if ($this_chapter_number !== $current_chapter_number) {
				$current_chapter_number = $this_chapter_number;
				$page_counter++;
			}

			$page_obj->page_number = $page_counter;
			if (isset($all_chapters[$this_chapter_number])) {
				$page_obj->page_content = $this->hide_code_blocks($page_obj->page_content);
				$chapter_url_string = $all_chapters[$this_chapter_number]->chapter_url_string;
				$page_obj->page_url = 'documentation/'.segment(2).'/'.$chapter_url_string.'/'.$page_obj->page_url_string;
				$all_chapters[$this_chapter_number]->pages[] = $page_obj;
			}
		}

		return $all_chapters;
	}

	public function get_current_chapter($target_chapter_url_string, $chapters) {
		foreach($chapters as $chapter_obj) {
			$chapter_url_string = $chapter_obj->chapter_url_string;
			if ($chapter_url_string === $target_chapter_url_string) {
				return $chapter_obj;
			}
		}

		return false;
	}

	public function get_current_page($target_page_url_string, $chapter_obj) {
		$chapter_pages = $chapter_obj->pages;
		
		foreach($chapter_pages as $page_obj) {
			$page_url_string = $page_obj->page_url_string;
			if ($page_url_string === $target_page_url_string) {
				return $page_obj;
			}
		}

		return false;
	}

    /**
     * Hides code blocks in the comment by replacing them with a hidden <div> element.
     *
     * This function searches for code blocks marked with the [code]...[/code] tags. It ensures that only
     * properly matched code blocks are processed and replaces them with a hidden <div> element containing
     * the code content. If any nested code blocks are found, they are ignored and left unchanged.
     *
     * @param string $comment The comment containing the [code]...[/code] tags to be processed.
     * @return string The processed comment with code blocks hidden inside <div> elements.
     */
    public function hide_code_blocks(string $comment): string {
        // First, validate and process only properly matched code blocks
        $pattern = '/\[code(?:=(\w+))?\]((?:(?!\[code).)*?)\[\/code\]/si';
        $processed_comment = preg_replace_callback($pattern, function ($matches) {
            // Get the language (default to PHP if not specified)
            $language = isset($matches[1]) ? strtolower($matches[1]) : 'php';
            
            // Normalize JavaScript variations
            if (in_array($language, ['js', 'javascript', 'javascript'])) {
                $language = 'javascript';
            }
            
            // Get the code content
            $code_content = $matches[2];
            
            // Only create code block if we don't have nested tags
            if (strpos($code_content, '[code') === false && strpos($code_content, '[/code]') === false) {
                return '<div class="code-block-pending" style="display: none" data-language="' . 
                       htmlspecialchars($language) . '">' . $code_content . '</div>';
            }
            
            // If we found nested tags, return the original content unchanged
            return $matches[0];
        }, $comment);
        
        return $processed_comment;
    }

    public function build_prev_next_array($current_chapter_obj, $chapters, $data) {

    	$view_file = $data['view_file'];

    	if ($view_file === 'docs_page') {
    		$current_page_url_string = segment(4);
    		foreach($current_chapter_obj->pages as $key => $page_obj) {

    			if ($page_obj->page_url_string === $current_page_url_string) {

    				if (isset($current_chapter_obj->pages[$key-1])) {
    					$prev_url = $current_chapter_obj->pages[$key-1]->page_url;
    				} else {
    					// Must be the first normal page in the chapter -> attempt get intro page of chapter
    					$prev_chapter_number = $current_chapter_obj->chapter_number - 1;
    					$prev_url = 'documentation/'.segment(2).'/'.segment(3);	
    				}

    				if (isset($current_chapter_obj->pages[$key+1])) {
    					$next_url = $current_chapter_obj->pages[$key+1]->page_url;
    				} else {
    					// Must be the last page in the chapter -> attempt get first page of next chapter
    					$next_chapter_number = $current_chapter_obj->chapter_number + 1;
    					$next_url = $this->attempt_get_chapter_intro_page_url($next_chapter_number, $chapters);
    				}
    				
    			}

    		}

    		
    	} else {
    		// Docs intro page
    		$chapter_number = $current_chapter_obj->chapter_number;
    		$prev_chapter_number = $chapter_number - 1;
    		$prev_url = $this->attempt_get_last_chapter_page_url($prev_chapter_number, $chapters);
    		$next_url = $current_chapter_obj->pages[0]->page_url;
    	}

		$result = [
			'prev_url' => $prev_url,
			'next_url' => $next_url
		];

    	return $result;
    }

    private function attempt_get_chapter_intro_page_url($chapter_number, $chapters) {

    	if (isset($chapters[$chapter_number])) {
    		$page_url = 'documentation/'.segment(2).'/'.$chapters[$chapter_number]->chapter_url_string;
    	} else {
    		$page_url = '';
    	}

    	return $page_url;
    }

    private function attempt_get_last_chapter_page_url($chapter_number, $chapters) {

    	if (isset($chapters[$chapter_number])) {
    		$prev_chapter_pages = $chapters[$chapter_number]->pages;
    		$target_index = count($prev_chapter_pages) - 1;
			$last_chapter_page = $prev_chapter_pages[$target_index];
			$page_url = $last_chapter_page->page_url;
    	} else {
    		$page_url = 'documentation/'.segment(2);
    	}

    	return $page_url;
    }

}