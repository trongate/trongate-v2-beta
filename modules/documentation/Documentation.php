<?php
class Documentation extends Trongate {

<<<<<<< HEAD
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
        $this->templates->docs_ahoy($data);
    }

    /**
     * Manage the documentation interface.
     *
     * Prepares data for the documentation management view, including
     * additional JavaScript includes and (when appropriate) a list of
     * documentation collections.
     *
     * @return void
     */
    public function manage(): void {

        // $this->module('trongate_security');
        // $token = $this->trongate_security->_make_sure_allowed();

        $additional_includes_top[] = '<script src="js/trongate-mx.js"></script>';
        $data['additional_includes_top'] = $additional_includes_top;

        if (segment(3) === '') {
            $this->module('documentation');
            $collections = $this->documentation->model->get_books();
            $docs_strings = [];
            foreach($collections as $collection) {
                // Extract docs_string from url_string in database
                $docs_strings[] = $collection->url_string;
            }
            $data['docs_strings'] = $docs_strings;
        }

        $data['view_module'] = 'documentation';
        $data['view_file'] = 'manage';
        $this->template('bootstrappy', $data);
=======
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
        $data['view_module'] = 'documentation';
        $data['view_file'] = 'documentation_home'; // <--- New line!
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
            ['title' => $first_chapter->book_title, 'url' => BASE_URL . 'documentation/'.segment(2)],
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
        $data['page_obj'] = $this->model->extract_page_obj($data, segment(3), segment(4));

        if ($data['page_obj'] === false) {
            redirect('documentation');
        }

        $data['chapter_url_string'] = segment(3);
        $data['current_page_number'] = (int) $data['page_obj']->page_number;
        $next_prev_array = $this->model->build_prev_next_array($data);
        $data['prev_url'] = $next_prev_array['prev_url'];
        $data['next_url'] = $next_prev_array['next_url'];

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Documentation', 'url' => BASE_URL . 'documentation'],
            ['title' => $first_chapter->book_title, 'url' => BASE_URL . 'documentation/'.segment(2)],
            ['title' => $data['page_obj']->chapter_title, 'url' => BASE_URL . 'documentation/'.segment(2).'/'.segment(3)],
            ['title' => $data['page_obj']->headline, 'url' => current_url()]
        ];

        $data['cover'] = $first_chapter->cover ?? '';
        $data['view_file'] = 'page_content';
        $this->template('docs_ahoy', $data);
>>>>>>> ditch_templates
    }

    public function _draw_search_btn() {
        $this->view('search_btn');
    }

<<<<<<< HEAD
    /**
     * Render documentation for the Trongate PHP Framework section.
     *
     * Loads theme 1, determines the correct target method,
     * and delegates rendering.
     *
     * @return void
     */
    public function trongate_php_framework(): void {
        $data['theme'] = $this->get_theme(1);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    /**
     * Render documentation for the Trongate MX section.
     *
     * Loads theme 2, determines the correct target method,
     * and delegates rendering.
     *
     * @return void
     */
    public function trongate_mx(): void {
        $data['theme'] = $this->get_theme(2);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    /**
     * Render the API reference documentation section.
     *
     * Loads theme 3, determines the correct target method,
     * and delegates rendering.
     *
     * @return void
     */
    public function api_ref(): void {
        $data['theme'] = $this->get_theme(3);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    /**
     * Render documentation for the Trongate CSS section.
     *
     * Loads theme 4, determines the correct target method,
     * and delegates rendering.
     *
     * @return void
     */
    public function trongate_css(): void {
        $data['theme'] = $this->get_theme(4);
        $target_method = $this->which_method();
        $this->$target_method($data);
    }

    /**
     * Display the table of contents for the selected documentation book.
     *
     * Retrieves chapter information, prepares breadcrumb data,
     * and loads the appropriate view template.
     *
     * @param array $data The data array passed into the view.
     *
     * @return void
     */
    public function display_table_of_contents(array $data): void {
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
        $this->templates->docs_ahoy($data);        
    }

    /**
     * Display the introductory page for a specific documentation chapter.
     *
     * Loads chapter and book metadata, prepares breadcrumb navigation,
     * and determines previous and next chapter URLs before rendering
     * the chapter intro view.
     *
     * @param array $data The data array used when rendering the view.
     *
     * @return void
     */
    public function display_chapter_intro_page(array $data): void {
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

        $this->templates->docs_ahoy($data);        
    }

    /**
     * Display a specific documentation page within a chapter.
     *
     * Retrieves book, chapter, and page data, prepares breadcrumbs,
     * adjusts page content where required, and renders the page
     * through the documentation template.
     *
     * @param array $data The data array passed into the view renderer.
     *
     * @return void
     */
    public function display_docs_page(array $data): void {
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
        $this->templates->docs_ahoy($data);
    }

    /**
     * Determine which documentation method should be invoked.
     *
     * Inspects URI segments to decide the appropriate handler
     * for rendering documentation content.
     *
     * @return string The target method name to call.
     */
    private function which_method(): string {

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

    /**
     * Establishes the documentation contents for a given module segment.
     *
     * This method scans the target directory for chapters and their corresponding pages, 
     * and organizes them into an array structure.
     *
     * @param string $str The module segment identifier (e.g., 'php_framework', 'trongate_mx', etc.).
     * @return array An array containing documentation chapters, their titles, and associated pages.
     */
    public function _establish_docs_contents(string $str): array {

        $str = str_replace('-', '_', $str);

        $target_dir = APPPATH . 'modules/documentation/assets/' . $str;
        $results = [];
        if (!is_dir($target_dir)) {
            return $results;
        }

        $chapters = array_filter(glob($target_dir . '/*'), 'is_dir');
        natsort($chapters);

        foreach ($chapters as $chapter_path) {
            $chapter_name = basename($chapter_path);
            $pages = glob($chapter_path . '/*.html');
            $chapter_pages = [];

            if (!empty($pages)) {
                natsort($pages);

                foreach ($pages as $page_path) {
                    $page_filename = basename($page_path);
                    $page_name = pathinfo($page_filename, PATHINFO_FILENAME);
                    $page_title = ucwords(str_replace(['_', '-'], ' ', $page_name));

                    $chapter_pages[] = [
                        'filename' => $page_filename,
                        'title' => $this->build_nice_label($page_title),
                        'path' => $page_path
                    ];
                }
            }

            $chapter_title = ucwords(str_replace(['_', '-'], ' ', $chapter_name));

            if (!empty($chapter_pages)) {
                $results[] = [
                    'chapter_name' => $this->build_nice_label($chapter_name),
                    'chapter_title' => $chapter_title,
                    'pages' => $chapter_pages
                ];
            }
        }

        return $results;
    }

    /**
     * Converts an 'ugly' filename into a human-friendly label.
     *
     * @param string $str The input string, typically a filename.
     * @return string The formatted, human-friendly label.
     */
    public function build_nice_label(string $str): string {
        // Remove the first four characters if numeric
        $page_label = preg_match('/^\d{4}/', $str) ? substr($str, 4) : $str;

        // Replace underscores, hyphens, and ".html" in one step
        $page_label = preg_replace(['/_/', '/-/', '/\.html$/'], [' ', ' ', ''], $page_label);

        // Capitalize the first letter of each word
        $page_label = ucwords($page_label);

        // Define replacements array (sorted alphabetically by keys)
        $replacements = [
            '  ' => ' ', // Remove double spaces
            ' A ' => ' a ',
            ' An Overview' => ': An Overview',
            ' And' => '&amp;',
            '&amp;' => '&',
            'Api ' => 'API ',
            'Authorization&' => 'Authorization &',
            'Cards&' => 'Cards &',
            'Css' => 'CSS',
            'Css Fundamentals' => 'CSS Fundamentals',
            'Data Analysis' => 'Data Analysis ',
            'Date&' => 'Date &',
            'Events&' => 'Events &',
            'Features&' => 'Features &',
            'Fonts&' => 'Fonts &',
            ' From ' => ' from ',
            'Github' => 'GitHub',
            'Http' => 'HTTP',
            'Media&' => 'Media &',
            'Module To Module' => 'Module-to-Module',
            'Modules&' => 'Modules &',
            'Mx' => 'MX',
            'Non English' => 'Non-English',
            'Objectives&' => 'Objectives &',
            'Parent&' => 'Parent &',
            'Pre Launch' => 'Pre-Launch',
            'Sql' => 'SQL',
            'Templates&' => 'Templates &',
            'TrongateCSS' => 'Trongate CSS',
            'Ui ' => 'UI ',
            'Understanding-tem' => 'Understanding Tem',
            'Url' => 'URL',
            'Url ' => 'URL ',
            'What Are Templates' => 'What Are Templates?',
            'What Is Trongate CSS' => 'What Is Trongate CSS?',
            'What Issue Does The Module Import Wizard Address' => 'What Issue Does The Module Import Wizard Address?',
            'Youtube' => 'YouTube',
            'rongates' => 'rongate\'s'
        ];

        // Perform replacements
        $page_label = array_reduce(
            array_keys($replacements),
            fn($label, $search) => str_replace($search, $replacements[$search], $label),
            $page_label
        );

        // Define lesser words that should be in lowercase
        $lesser_words = [
            'a', 'an', 'the',   // Articles
            'and', 'but', 'or', 'nor', 'for', 'so', 'yet',  // Conjunctions
            'either', 'neither', 'whether', 'as', 'if', 'because', 'although', 'though', 'while', 'until',
            'of', 'in', 'to', 'with', 'for', 'at', 'by', 'on', 'from', 'about', 'as', 'into', 'after', 'before', 
            'during', 'between', 'under', 'over', 'through', 'among', 'alongside', 'behind', 'beyond', 'despite',
            'except', 'inside', 'outside', 'within', 'without',  // Prepositions
            'he', 'she', 'it', 'we', 'they', 'me', 'you', 'us', 'them', 'his', 'her', 'its', 'our', 'their', 'mine', 'yours', 'theirs',  // Pronouns
            'am', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does', 'did', 'doing'  // Auxiliary verbs
        ];

        // Convert lesser words to lowercase except for the first and last word
        $words = explode(' ', $page_label);  // Convert string into an array of words
        $count = count($words);  // Count the number of words

        // Convert lesser words to lowercase except for the first and last word
        $page_label = implode(' ', array_map(function ($word, $index) use ($lesser_words, $count) {
            // Make it lowercase if it's a lesser word and not the first or last word
            if ($index > 0 && $index < $count - 1 && in_array(strtolower($word), $lesser_words)) {
                return strtolower($word);
            }
            return ucfirst($word); // Capitalize other words
        }, $words, array_keys($words)));  // Pass both words and keys to array_map

        return $page_label;
    }

    /**
     * Replaces YouTube placeholders in the comment with video container elements.
     *
     * This function searches for elements marked with [youtube]...[/youtube] placeholders.
     * It ensures that only properly matched placeholders are processed and replaces them 
     * with <div> elements having the class "video-container" and an ID matching the 
     * YouTube video ID.
     *
     * @param string $comment The comment containing the [youtube]...[/youtube] placeholders to be processed.
     * @return string The processed comment with YouTube placeholders replaced by video container elements.
     */
    public function make_video_containers(string $comment): string {
        // Define the pattern to match [youtube]...[/youtube] placeholders
        $pattern = '/\[youtube\](.*?)\[\/youtube\]/si';
        
        // Use preg_replace_callback to process each match
        $processed_comment = preg_replace_callback($pattern, function ($matches) {
            // Extract the YouTube video ID from the placeholder
            $youtube_id = trim($matches[1]);
            
            // Replace with a video container div
            return '<div class="video-container" id="' . htmlspecialchars($youtube_id) . '"></div>';
        }, $comment);
        
        return $processed_comment;
    }

    /**
     * Remove unnecessary new-line characters surrounding code placeholders.
     *
     * Processes a string to eliminate unwanted line breaks that appear
     * directly after opening [code] tags or directly before closing [/code] tags.
     *
     * @param string $content The raw content containing possible code placeholders.
     *
     * @return string The cleaned content with adjusted line breaks.
     */
    public function remove_unwanted_new_lines(string $content): string {

        // Approach 1: Using simple array and array_map
        $languages = ['php', 'javascript', 'js', 'html', 'css', ''];
        $replacements = [];
        
        foreach ($languages as $lang) {
            $code_tag = $lang ? "code=$lang" : 'code';
            $replacements["[$code_tag]\n"] = "[$code_tag]";
        }
        $replacements["\n[/code]"] = '[/code]';
        
        return str_replace(array_keys($replacements), array_values($replacements), $content);
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

=======
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

>>>>>>> ditch_templates
}