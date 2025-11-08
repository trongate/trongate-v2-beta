<?php
class Documentation extends Trongate {

    private $book_params = [
        'trongate_php_framework' => [
            'book_id' => 1,
            'theme_color' => 'blue'
        ],
        'trongate_mx' => [
            'book_id' => 2,
            'theme_color' => 'purple'
        ],
        'api_ref' => [
            'book_id' => 3,
            'theme_color' => 'orange'
        ],
        'trongate_css' => [
            'book_id' => 4,
            'theme_color' => 'green'
        ]
    ];

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
        $data = $this->book_params['trongate_php_framework'];
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function trongate_mx() {
        $data = $this->book_params['trongate_mx'];
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function api_ref() {
        $data = $this->book_params['api_ref'];
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function trongate_css() {
        $data = $this->book_params['trongate_css'];
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

        $data['page_obj'] = $this->model->extract_page_obj($data, segment(3), segment(4));

        if ($data['page_obj'] === false) {
            redirect('documentation');
        }

        $data['cover'] = $first_chapter->cover ?? '';
        $data['view_file'] = 'page_content';
        $this->template('docs_ahoy', $data);
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

    public function _render_theme_color_css($theme_color) {
        $view_file = 'theme_css_'.$theme_color;
        $this->view($view_file);
    }

}