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

}