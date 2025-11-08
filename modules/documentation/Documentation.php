<?php
class Documentation extends Trongate {

    public function index() {
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => 'Table of Contents', 'url' => BASE_URL . 'documentation/table_of_contents']
        ];

        $docs_books = $this->model->fetch_docs_books();
        $data['docs_books'] = !empty($docs_books) ? $docs_books : [];
        $data['theme_color'] = 'blue'; // Valid values are; blue, green, orange, purple
        $data['view_file'] = 'documentation_home';
        $this->template('docs_ahoy', $data);
    }

    public function trongate_php_framework() {
        $data = [
            'book_id' => 1,
            'theme' => 'blue'
        ];

        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function trongate_mx() {
        $data = [
            'book_id' => 2,
            'theme' => 'orange'
        ];

        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function api_ref() {
        $data = [
            'book_id' => 3,
            'theme' => 'green'
        ];

        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function trongate_css() {
        $data = [
            'book_id' => 4,
            'theme' => 'purple'
        ];

        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    private function display_cover_page($data) {

        $book_id = (int) ($data['book_id'] ?? 1);
        if (($book_id > 4) || ($book_id < 1)) {
            redirect('documentation');
        }

        $chapters = $this->model->get_chapters($book_id);

        if (empty($chapters) || !is_array($chapters)) {
            show_404();
            return;
        }

        $first_chapter = $chapters[0];

        $data['chapters'] = $chapters;
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => $first_chapter->book_title, 'url' => current_url()]
        ];

        $data['table_of_contents_url'] = BASE_URL . 'documentation/' . segment(2) . '/table_of_contents';
        $data['cover'] = $first_chapter->cover ?? '';
        $data['view_file'] = 'cover_page';

        $this->template('docs_ahoy', $data);
    }

    public function table_of_contents($data) {

        $book_id = (int) ($data['book_id'] ?? 1);
        if (($book_id > 4) || ($book_id < 1)) {
            redirect('documentation');
        }

        $chapters = $this->model->get_chapters($book_id, true);
        $first_chapter = $chapters[0];

        $data['chapters'] = $chapters;
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => $first_chapter->book_title, 'url' => BASE_URL . 'documentation/'.segment(2).'/' . segment(3)],
            ['title' => 'Table of Contents', 'url' => current_url()]
        ];

        $data['cover'] = $first_chapter->cover ?? '';
        $data['view_file'] = 'table_of_contents';
        $this->template('docs_ahoy', $data);
    }

    public function display_page($data) {
    	$params = [
    		'book_url_string' => segment(2),
    		'chapter_url_string' => segment(3),
    		'page_url_string' => segment(4)
    	];

    	$page_obj = $this->model->fetch_page_obj($params);
    }

    public function _draw_search_btn() {
        $this->view('search_btn');
    }

    private function which_method() {

        $target_method = 'display_cover_page'; // The default method (it displays the cover).

        if (segment(3) === '') {
            return $target_method;
        }

        if (segment(3) === 'table_of_contents') {
            $target_method = 'table_of_contents';
            return $target_method;
        }

        if ((segment(4) !== '') && (segment(5) === '')) {
            $target_method = 'display_page';
        }

        return $target_method;

    }

}