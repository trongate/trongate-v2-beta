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
            json($page, true);
            
            $num = (int) $page->chapter_number;
            if (isset($chapters[$num])) {
                $chapters[$num]->pages[] = $page;
            }
        }

        ksort($chapters);
        return array_values($chapters);
    }

    public function fixnd_page_in_chapters($chapters, $page_url) {
        foreach ($chapters as $chapter) {
            if (!empty($chapter->pages)) {
                foreach ($chapter->pages as $page) {
                    if ($page->page_url_string === $page_url) {
                        return $page;
                    }
                }
            }
        }
        return null;
    }
}