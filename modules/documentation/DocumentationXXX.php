<?php
class Documentation extends Trongate {

    /**
     * Home page – shows all documentation books
     */
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

    private function which_method() {

        $target_method = 'display'; // The default method (it displays the cover).

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

    /**
     * Display cover page of a documentation book
     */
    private function display($data) {

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

    /**
     * Table of Contents page (with pages nested under chapters)
     */
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



    private function display_page() {

        $book_url = segment(3);
        $chapter_url = segment(4);
        $page_url = segment(5);

        if (empty($book_url) || empty($chapter_url) || empty($page_url)) {
            show_404();
            return;
        }

        // Fetch all chapters with pages (reuse existing method!)
        $chapters = $this->model->get_chapters($book_url, true);

        if (empty($chapters)) {
            show_404();
            return;
        }

        // Find the specific page
        $page_data = $this->model->find_page_in_chapters($chapters, $page_url);

        if (empty($page_data)) {
            show_404();
            return;
        }

        // Find the chapter for this page
        $current_chapter = null;
        foreach ($chapters as $chapter) {
            if ($chapter->chapter_url_string === $chapter_url) {
                $current_chapter = $chapter;
                break;
            }
        }

        $first_chapter = $chapters[0];

        $data['page'] = $page_data;
        $data['chapters'] = $chapters;
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => $first_chapter->book_title, 'url' => BASE_URL . 'documentation/display/' . $book_url],
            ['title' => $current_chapter->chapter_title, 'url' => BASE_URL . 'documentation/chapter/' . $book_url . '/' . $chapter_url],
            ['title' => $page_data->headline, 'url' => '']
        ];

        $data['additional_includes_top'] = [
            '<link rel="stylesheet" href="http://localhost/trongate_live5/css/documentation.css">',
            '<link rel="stylesheet" href="http://localhost/trongate_live5/css/tronpro-docs.css">',
            '<link rel="stylesheet" href="http://localhost/trongate_live5/css/prism.css">',
            '<link rel="stylesheet" href="http://localhost/trongate_live5/css/tronpro-docs-php-framework.css">'
        ];

        $data['page_content'] = $this->hide_code_blocks($data['page']->page_content);
        $data['table_of_contents_url'] = BASE_URL . 'documentation/table_of_contents/' . $book_url;
        $data['view_file'] = 'page_content';
        $this->template('docs_ahoy', $data);
    }

    /**
     * Render the search button partial
     */
    public function _draw_search_btn() {
        $this->view('search_btn');
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

}