<?php
class Documentation extends Trongate {

	/**
	 * Displays the documentation home page.
	 *
	 * Prepares breadcrumb navigation, retrieves theme and book data,
	 * and loads the documentation template.
	 *
	 * @return void
	 */
	public function index(): void {
	    $data['breadcrumbs'] = [
	        ['title' => 'Home', 'url' => BASE_URL],
	        ['title' => 'Documentation', 'url' => current_url()]
	    ];

	    $data['theme'] = $this->get_theme(1);
	    $data['books'] = $this->model->get_books();
	    $data['view_module'] = 'documentation';
	    $data['view_file'] = 'documentation_home';
	    $this->template('docs_ahoy', $data);
	}

    public function _draw_search_btn() {
        $this->view('search_btn');
    }

    public function trongate_php_framework() {
        $data['theme'] = $this->get_theme(1);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function trongate_mx() {
        $data['theme'] = $this->get_theme(2);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function api_ref() {
        $data['theme'] = $this->get_theme(3);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function trongate_css() {
        $data['theme'] = $this->get_theme(4);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    public function display_table_of_contents($data) {
    	$chapters = $this->model->get_chapters(segment(2));

    	if ($chapters === false) {
    		redirect('documentation');
    	}
    	
    	$current_book_obj = $chapters[1]->book_obj;
    	$data['book_title'] = $current_book_obj->book_title;
    	$data['chapters'] = $chapters;
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => $data['book_title'], 'url' => BASE_URL . 'documentation/'.segment(2)]
        ];

        $data['view_file'] = 'table_of_contents';
    	$this->template('docs_ahoy', $data);    	
    }

    public function display_chapter_intro_page($data) {
    	$chapters = $this->model->get_chapters(segment(2));

    	if ($chapters === false) {
    		redirect('documentation');
    	}
    	
    	$chapter_url_string = segment(3);
    	$current_chapter_obj = $this->model->get_current_chapter($chapter_url_string, $chapters);

    	if ($current_chapter_obj === false) {
    		redirect('documentation');
    	}

    	$current_book_obj = $chapters[1]->book_obj;
    	$book_title = $current_book_obj->book_title;
    	$data['chapter_title'] = $current_chapter_obj->chapter_title;
    	$data['chapter_number'] = $current_chapter_obj->chapter_number;
    	
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => $book_title, 'url' => BASE_URL . 'documentation/'.segment(2)],
            ['title' => $data['chapter_title'], 'url' => BASE_URL . 'documentation/'.segment(2).'/'.segment(3)],
        ];

        $data['view_file'] = 'chapter_intro_page';

        $next_prev_array = $this->model->build_prev_next_array($current_chapter_obj, $chapters, $data);
        $data['prev_url'] = $next_prev_array['prev_url'];
        $data['next_url'] = $next_prev_array['next_url'];

    	$this->template('docs_ahoy', $data);    	
    }

    public function display_docs_page($data) {
    	$chapters = $this->model->get_chapters(segment(2));

    	if ($chapters === false) {
    		redirect('documentation');
    	}
    	
    	$chapter_url_string = segment(3);
    	$current_chapter_obj = $this->model->get_current_chapter($chapter_url_string, $chapters);

    	if ($current_chapter_obj === false) {
    		redirect('documentation');
    	}

    	$current_book_obj = $chapters[1]->book_obj;
    	$page_url_string = segment(4);
    	$current_page_obj = $this->model->get_current_page($page_url_string, $current_chapter_obj);

    	if ($current_page_obj === false) {
    		redirect('documentation');
    	}

    	$book_title = $current_book_obj->book_title;
    	$chapter_title = $current_chapter_obj->chapter_title;
    	$data['page_headline'] = $current_page_obj->headline;
    	$data['page_content'] = $current_page_obj->page_content;
    	$ditch = 'class="feature-ref"';
    	$replace = 'class="feature-ref" style="display: none;"';
    	$data['page_content'] = str_replace($ditch, $replace, $data['page_content']);

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => $book_title, 'url' => BASE_URL . 'documentation/'.segment(2)],
            ['title' => $chapter_title, 'url' => BASE_URL . 'documentation/'.segment(2).'/'.segment(3)],
            ['title' => $data['page_headline'], 'url' => current_url()]
        ];

        $data['view_file'] = 'docs_page';
        $next_prev_array = $this->model->build_prev_next_array($current_chapter_obj, $chapters, $data);
        $data['prev_url'] = $next_prev_array['prev_url'];
        $data['next_url'] = $next_prev_array['next_url'];
    	$this->template('docs_ahoy', $data);
    }

    private function which_method() {

    	if (segment(4) !== '') {
    		$target_method = 'display_docs_page';
    	} elseif (segment(3) === '') {
    		$target_method = 'display_table_of_contents';
    	} elseif(segment(3) !== '') {
    		$target_method = 'display_chapter_intro_page';
    	}

    	return $target_method;
    }

	/**
	 * Retrieves the theme color associated with a given book ID.
	 *
	 * If no book ID is provided, defaults to book ID 1.
	 *
	 * @param int|null $book_id The ID of the book (optional).
	 * @return string The corresponding theme color.
	 */
	private function get_theme(?int $book_id = null): string {

	    if (!isset($book_id)) {
	        $book_id = 1;
	    }

	    switch ($book_id) {
	        case 1:
	            $theme = 'blue';
	            break;
	        case 2:
	            $theme = 'purple';
	            break;
	        case 3:
	            $theme = 'orange';
	            break;
	        case 4:
	            $theme = 'green';
	            break;
	        default:
	            $theme = 'default';
	            break;
	    }

	    return $theme;
	}

	/**
	 * Renders the CSS view for a specific theme color.
	 *
	 * Generates the view filename dynamically based on the provided theme color
	 * and renders the corresponding CSS view file.
	 *
	 * @param string $theme_color The name of the theme color (e.g., 'blue', 'purple').
	 * @return void
	 */
	public function _render_theme_color_css(string $theme_color): void {
	    $view_file = 'theme_css_' . $theme_color;
	    $this->view($view_file);
	}

}