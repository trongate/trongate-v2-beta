<?php
class Documentation_model extends Model {

    public function fetch_docs_books() {
        $sql = 'SELECT * FROM documentation_books ORDER BY priority';
        return $this->live5->query($sql, 'object');
    }

    public function get_chapters($book_id, $include_pages = false) {
        $params = ['book_id' => $book_id];

        $sql = '
            SELECT
                b.book_title, b.url_string, b.description, b.cover,
                c.chapter_number, c.chapter_title, c.chapter_url_string, c.book_id
            FROM documentation_books b
            INNER JOIN documentation_chapters c ON b.id = c.book_id
            WHERE b.id = :book_id
            ORDER BY c.chapter_number
        ';

        $rows = $this->live5->query_bind($sql, $params, 'object');

        if ($include_pages && !empty($rows)) {
            $rows = $this->add_pages_to_chapters($rows);
            $rows = $this->add_page_numbers($rows);
        }

        return $rows ?: [];
    }

    private function add_pages_to_chapters($chapter_rows) {
        $chapters = [];

        foreach ($chapter_rows as $row) {
            $num = (int) $row->chapter_number;
            $row->pages = [];
            $chapters[$num] = $row;
        }

        $book_id = (int) reset($chapter_rows)->book_id;
        $book_url_string = reset($chapter_rows)->url_string; // Get the book's url_string

        // FIXED: Order by page_number, NOT id
        $sql = "SELECT * FROM documentation_pages WHERE book_id = ? ORDER BY page_number";

        $pages = $this->live5->query_bind($sql, [$book_id], 'object');

        foreach ($pages as $page) {
            // Add the book_url_string to each page object
            $page->book_url_string = $book_url_string;
            $num = (int) $page->chapter_number;
            if (isset($chapters[$num])) {
                $chapter_url_string = $chapters[$num]->chapter_url_string;
                $book_url_string = str_replace('-', '_', $book_url_string);
                $page->page_url = BASE_URL.'documentation/'.$book_url_string.'/'.$chapter_url_string.'/'.$page->page_url_string;
                $chapters[$num]->pages[] = $page;
            }
        }

        ksort($chapters);
        return array_values($chapters);
    }

    /**
     * Adds sequential page numbers across all chapters in the documentation.
     *
     * This method assigns a continuous page number to each page across all chapters,
     * starting from 1 for the first page of the first chapter and incrementing
     * sequentially through all pages in all chapters. It also accounts for chapter
     * introduction pages by incrementing the page number before each chapter's pages.
     *
     * @param array $chapter_rows Array of chapter objects, each containing a 'pages' array
     *
     * @return array The modified chapter rows with sequential page numbers added to each page
     */
    private function add_page_numbers(array $chapter_rows): array {
        $global_page_number = 1;
        
        foreach ($chapter_rows as $chapter) {
            // Increment for the chapter introduction page
            $chapter->intro_page_number = $global_page_number;
            $global_page_number++;
            
            // Now number the actual pages in the chapter
            if (!empty($chapter->pages) && is_array($chapter->pages)) {
                foreach ($chapter->pages as $page) {
                    $page->global_page_number = $global_page_number;
                    $global_page_number++;
                }
            }
        }
        
        return $chapter_rows;
    }

    public function extract_page_obj($data, $target_chapter_url_string, $target_page_url_string) {

        $chapters = $data['chapters'];
        foreach($chapters as $chapter) {
            $chapter_url_string = $chapter->chapter_url_string;
            if ($chapter_url_string === $target_chapter_url_string) {
                $chapter_pages = $chapter->pages;
                foreach($chapter_pages as $chapter_page) {
                    $page_url_string = $chapter_page->page_url_string;
                    if ($page_url_string === $target_page_url_string) {
                        $chapter_page->page_content = $this->hide_code_blocks($chapter_page->page_content);
                        $chapter_page->chapter_title = $chapter->chapter_title;
                        return $chapter_page;
                    }
                }
                
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
    private function hide_code_blocks(string $comment): string {
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

    public function build_prev_next_array($data) {
        $prev_url = '';
        $next_url = '';

        $current_chapter = $this->extract_current_chapter($data); 
        $current_chapter_url_string = $current_chapter->chapter_url_string;
        $current_chapter_number = (int) $current_chapter->chapter_number;
        $num_pages_in_chapter = count($current_chapter->pages);

        if (!isset($current_page_number)) {
            // This must be a chapter intro page.
            $page_type = 'chapter intro';
        } else {
            $page_type = $this->get_page_type($data['current_page_number'], $num_pages_in_chapter);
        }

        if ($page_type === 'first') {
            $prev_next_array = $this->est_prev_next_first($current_chapter_number, $data);
        } elseif($page_type === 'middle') {
            $prev_next_array = $this->est_prev_next_middle($data['current_page_number'], $current_chapter);
        } elseif($page_type === 'last') {
            $prev_next_array = $this->est_prev_next_last($data['current_page_number'], $current_chapter, $data);
        } else {
            // Chapter intro page
            $prev_next_array = $this->est_prev_next_chapter_intro($current_chapter, $data);
        }

        return $prev_next_array;
    }

    private function get_page_type($page_number, $num_pages_in_chapter) {
        switch ($page_number) {
            case 1:
                $page_type = 'first';
                break;
            case $num_pages_in_chapter:
                $page_type = 'last';
                break;
            default:
                $page_type = 'middle';
                break;
        }

        return $page_type;
    }

    /**
     * Extracts the current chapter object from the data array based on URL string match.
     *
     * @param array $data Associative array containing chapter data and target URL string
     *                    Expected keys:
     *                    - 'chapter_url_string' (string): The target chapter URL string to match
     *                    - 'chapters' (array): Array of chapter objects to search through
     * 
     * @return object|false Returns the matching chapter object (with cover and pages unset) 
     *                      if found, false otherwise
     */
    public function extract_current_chapter(array $data): object|false {
        $target_chapter_url_string = $data['chapter_url_string'];
        foreach($data['chapters'] as $key => $chapter_obj) {
            $chapter_url_string = $chapter_obj->chapter_url_string;
            if ($target_chapter_url_string === $chapter_url_string) {
                return $chapter_obj;
            }
        }

        return false;
    }

    private function est_prev_next_chapter_intro($current_chapter, $data) {

        $current_chapter_number = (int) $current_chapter->chapter_number;
        $preceding_chapter = $this->get_preceding_chapter($current_chapter_number, $data);
        $last_page = $this->get_last_page($preceding_chapter);
        $result['prev_url'] = $last_page->page_url;
        $first_page = $this->get_first_page($current_chapter);
        $result['next_url'] = $first_page->page_url;
        return $result;
    }

    private function est_prev_next_first($current_chapter_number, $data) {

        // If on first page, the previous page should be the chapter intro
        $current_page_number = 1;
        $result['prev_url'] = BASE_URL.'documentation/'.segment(2).'/'.segment(3);

        // The next link should be the next page in the chapter (assuming more than one page in each chapter)
        $current_chapter = $this->get_current_chapter($current_chapter_number, $data);
        $next_page = $this->get_next_page($current_chapter, $current_page_number);
        $result['next_url'] = $next_page->page_url;
        return $result;
    }

    private function est_prev_next_middle($current_page_number, $current_chapter) {

        // Previous page is just the preceding page.
        $target_page_number_prev = $current_page_number - 1;
        $target_page_number_next = $current_page_number + 1;
        foreach($current_chapter->pages as $page) {
            $page_number = (int) $page->page_number;
            if ($page_number === $target_page_number_prev) {
                $result['prev_url'] = $page->page_url;
            } elseif ($page_number === $target_page_number_next) {
                $result['next_url'] = $page->page_url;
            }
        }

        return $result;
    }

    private function est_prev_next_last($current_page_number, $current_chapter, $data) {
        // If on last page of a chapter, the previous page should be the chapter intro

        // Previous page is just the preceding page.
        $target_page_number_prev = $current_page_number - 1;
        $target_page_number_next = $current_page_number + 1;
        foreach($current_chapter->pages as $page) {
            $page_number = (int) $page->page_number;
            if ($page_number === $target_page_number_prev) {
                $result['prev_url'] = $page->page_url;
            }
        }

        // Now get the next chapter
        $next_chapter = $this->get_next_chapter($current_chapter->chapter_number, $data);
        $next_chapter_url_string = str_replace('-', '_', $next_chapter->url_string);
        $result['next_url'] = BASE_URL.'documentation/'.$next_chapter_url_string.'/'.$next_chapter->chapter_url_string;
        return $result;
    }

    /**
     * Retrieve the chapter object that precedes the current chapter.
     *
     * @param int   $current_chapter_number The current chapter number.
     * @param array $data                   The dataset containing chapter objects under 'chapters'.
     *
     * @return object|false Returns the preceding chapter object if found, or false otherwise.
     */
    private function get_preceding_chapter(int $current_chapter_number, array $data): object|false {
        $target_chapter_number = $current_chapter_number - 1;
        foreach($data['chapters'] as $chapter_obj) {
            $chapter_number = (int) $chapter_obj->chapter_number;
            if ($chapter_number === $target_chapter_number) {
                return $chapter_obj;
            }
        }

        return false;
    }

    /**
     * Retrieve the current chapter object.
     *
     * @param int   $current_chapter_number The current chapter number.
     * @param array $data                   The dataset containing chapter objects under 'chapters'.
     *
     * @return object|false Returns the current chapter object if found, or false otherwise.
     */
    private function get_current_chapter(int $current_chapter_number, array $data): object|false {
        foreach ($data['chapters'] as $chapter_obj) {
            if ((int) $chapter_obj->chapter_number === $current_chapter_number) {
                return $chapter_obj;
            }
        }

        return false;
    }

    /**
     * Retrieve the chapter object that follows the current chapter.
     *
     * @param int   $current_chapter_number The current chapter number.
     * @param array $data                   The dataset containing chapter objects under 'chapters'.
     *
     * @return object|false Returns the next chapter object if found, or false otherwise.
     */
    private function get_next_chapter(int $current_chapter_number, array $data): object|false {
        $target_chapter_number = $current_chapter_number + 1;
        foreach($data['chapters'] as $chapter_obj) {
            $chapter_number = (int) $chapter_obj->chapter_number;
            if ($chapter_number === $target_chapter_number) {
                return $chapter_obj;
            }
        }

        return false;
    }

    /**
     * Retrieve the first page object of a chapter.
     *
     * @param object $chapter_obj The chapter object containing a 'pages' array.
     *
     * @return object|false Returns the first page object if available, or false if no pages exist.
     */
    private function get_first_page(object $chapter_obj): object|false {
        if (!empty($chapter_obj->pages) && is_array($chapter_obj->pages)) {
            return $chapter_obj->pages[0];
        }
        return false;
    }

    /**
     * Retrieve the last page object of a chapter.
     *
     * @param object $chapter_obj The chapter object containing a 'pages' array.
     *
     * @return object|false Returns the last page object if available, or false if no pages exist.
     */
    private function get_last_page(object $chapter_obj): object|false {
        if (!empty($chapter_obj->pages) && is_array($chapter_obj->pages)) {
            return $chapter_obj->pages[count($chapter_obj->pages) - 1];
        }
        return false;
    }

    /**
     * Retrieve the next page object within the current chapter.
     *
     * @param object $chapter_obj The current chapter object containing a 'pages' array.
     * @param int    $current_page_number The current page number within the chapter.
     *
     * @return object|false Returns the next page object if available, or false if on the last page.
     */
    private function get_next_page(object $chapter_obj, int $current_page_number): object|false {
        if (empty($chapter_obj->pages) || !is_array($chapter_obj->pages)) {
            return false;
        }

        foreach ($chapter_obj->pages as $index => $page) {
            if ((int) $page->page_number === $current_page_number) {
                return $chapter_obj->pages[$index + 1] ?? false;
            }
        }

        return false;
    }

}