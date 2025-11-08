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

    public function fetch_page_obj($params) {
    	// Get the book record.
    	//echo $params['book_url_string']; die();
    	$book_obj = $this->live5->get_one_where('url_string', $params['book_url_string'], 'documentation_books');
    	var_dump($book_obj); die();
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
                        return $chapter_page;
                    }
                }
                
            }
        }

        return false;
    }

}