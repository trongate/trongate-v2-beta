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
            ['title' => 'Documentation', 'url' => current_url()]
        ];

        $data['books'] = $this->model->get_books();
        $data['view_module'] = 'documentation';
        $data['view_file'] = 'documentation_home';
        $this->template('docs_ahoy', $data);
    }

    public function _render_theme_color_css($theme_color) {
        $view_file = 'theme_css_'.$theme_color;
        $this->view($view_file);
    }

}