<?php
class Manage extends Trongate {

    /**
     * Updates chapters for a given documentation string by reinserting chapter records.
     *
     * This function processes the provided documentation collections and identifies
     * the chapters associated with the given documentation string. It then inserts
     * chapter records into the database with properly formatted chapter URLs.
     *
     * @param array $docs_collections An array of documentation collections, each containing target URLs and other metadata.
     * @param string $docs_string The string identifier for the documentation.
     * 
     * @return array|false An array of chapters if successfully updated, or false if no chapters were found.
     */
    private function reindex_chapters(array $docs_collections, string $docs_string): array|false {

        // Having deleted documentation_chapters records for $doc_string, let's re-insert the chapter records.
        foreach ($docs_collections as $key => $docs_collection) {
            $last_part = get_last_part($docs_collection->target_url, '/');
            if ($last_part = $docs_string) {
                $docs_chapters = $docs_collections[$key];
            }
        }

        if (!isset($docs_chapters)) {
            return false;
        }

        $docs_chapters = $this->documentation->_establish_docs_contents($docs_string);
        $chapter_counter = 0;
        foreach ($docs_chapters as $chapter) {
            $chapter_counter++;
            $data['chapter_number'] = $chapter_counter;
            $data['docs_string'] = $docs_string;
            $data['chapter_title'] = $chapter['chapter_name'];

            $chapter_url_string = $chapter['chapter_title'];
            $first_four = substr($chapter_url_string, 0, 4);

            if (ctype_digit($first_four)) {
                $chapter_url_string = substr($chapter_url_string, 4);
            }

            $chapter_url_string = str_replace('.html', '', $chapter_url_string);
            $filename_str = url_title(strtolower($chapter_url_string));
            $data['chapter_url_string'] = str_replace('_', '-', $filename_str);

            $data['book_id'] = $this->get_book_id($docs_string);
            $this->db->insert($data, 'documentation_chapters');
        }

        return $docs_chapters;
    }

    private function get_book_id($docs_string) {
        switch ($docs_string) {
            case 'php_framework':
                $book_id = 1;
                break;
            default:
                echo 'Please get book id';
                die();
                break;
        }

        return $book_id;
    }

    public function table_summary() {
        $rows = $this->db->get('id', 'documentation_pages');
        http_response_code(200);

        foreach($rows as $key => $value) {
            $rows[$key]->page_content = strlen($value->page_content);
        }

        echo json_encode($rows);
    }

    public function submit_reindex() {

        if (strtolower(ENV) !== 'dev') {
            http_response_code(403);
            die();
        }

        $docs_string = post('docsStr', true);
        $truncation_info = '';

        $this->module('documentation');
        $docs_collections = $this->documentation->docs_collections;  

        // Delete all of the chapters AND all of the pages
        $params['docs_string'] = $docs_string;
        $sql1 = 'DELETE FROM documentation_chapters WHERE docs_string = :docs_string';
        $this->db->query_bind($sql1, $params);

        $sql2 = 'DELETE FROM documentation_pages WHERE docs_string = :docs_string';
        $this->db->query_bind($sql2, $params);

        $docs_chapters = $this->reindex_chapters($docs_collections, $docs_string);
        if ($docs_chapters === false) {
            $this->send_json_response('0');
        }

        $chapter_counter = 0;
        foreach($docs_chapters as $docs_chapter) {
            $chapter_counter++;
            $chapter_data = $this->build_chapter_data($docs_chapter, $docs_string, $chapter_counter);

            foreach($chapter_data as $key => $value) {
                $value['book_id'] = $this->get_book_id($value['docs_string']);
                $chapter_data[$key] = $value;
            }

            $this->db->insert_batch('documentation_pages', $chapter_data);
        }

        $this->db->resequence_ids('documentation_pages');
        $this->db->resequence_ids('documentation_chapters');

        http_response_code(200);
        echo 'great success!';

    }

    private function send_json_response($data) {
        http_response_code(200);
        echo is_array($data) ? json_encode($data) : $data;
        die();
    }

    private function build_chapter_data($docs_chapter, $docs_string, $chapter_number): array {

// if ($chapter_number === 16) {
//     json($docs_chapter);
//     die();
// }

        $chapter_data = [];
        $pages = $docs_chapter['pages'] ?? [];
        settype($pages, 'array');
        $page_counter = 0;
                foreach($pages as $page) {
                    $page_counter++;

     
                    $page_filepath = $page['path'];
                    $file_contents = file_get_contents($page_filepath);
                    $file_contents = str_replace('—', ' - ', $file_contents);
                    $file_contents = $this->clean_php_tags($file_contents);
                    $file_contents = $this->documentation->make_video_containers($file_contents);

                    $filename = get_last_part($page_filepath, '/');
                    $first_four = substr($filename, 0, 4);

                    if (ctype_digit($first_four)) {
                        $filename = substr($filename, 4);
                    }

                    $filename = str_replace('.html', '', $filename);
                    $filename_str = url_title(strtolower($filename));
                    $page_url_string = str_replace('_', '-', $filename_str);

                    // Get the headline
                    $this->module('documentation-calculate_search_scores');
                    $headline_content = $this->calculate_search_scores->_get_element_text_by_selector($file_contents, 'h1', true);

                    $cleaned_page_content = $this->documentation->remove_unwanted_new_lines($file_contents);
                    $cleaned_page_content = $this->documentation->hide_code_blocks($cleaned_page_content);

                    // Remove PHP openings and closings
                    $cleaned_page_content = $this->clean_php_tags($cleaned_page_content);
                    $cleaned_page_content = strip_tags($cleaned_page_content);

                    $row_data = [
                        'docs_string' => $docs_string,
                        'chapter_number' => $chapter_number,
                        'page_number' => $page_counter,
                        'page_url_string' => $page_url_string,
                        'headline' => $headline_content,
                        'page_content' => $file_contents,
                        'headline_sounds_like' => metaphone($headline_content),
                        'page_content_sounds_like' => metaphone($cleaned_page_content)
                    ];

                    $chapter_data[] = $row_data;
                }

        return $chapter_data;   
    }

    private function clean_php_tags($page_content) {
        $ditch = '<?';
        $replace = '&lt;?';
        $page_content = str_replace($ditch, $replace, $page_content);

        $ditch = '?>';
        $replace = '?&gt;';
        $page_content = str_replace($ditch, $replace, $page_content);
        return $page_content;
    }

}