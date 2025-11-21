<?php
class Templates extends Trongate {

    public function public($data) {
        $this->view('public', $data);
    }

    /**
     * Displays the view file for the specified module.
     *
     * @param array|null $data Data to be passed to the view file.
     * @return void
     */
    public static function display(?array $data = null): void {
        if (!isset($data['view_module'])) {
            $data['view_module'] = self::get_view_module();
        }

        if (!isset($data['view_file'])) {
            $data['view_file'] = 'index';
        }

        $file_path = APPPATH . 'modules/' . $data['view_module'] . '/views/' . $data['view_file'] . '.php';
        self::attempt_include($file_path, $data);
    }

    /**
     * Retrieves the view module from the current URL.
     *
     * @return string The name of the view module.
     */
    public static function get_view_module(): string {
        // Attempt to get view_module from URL
        $url = str_replace(BASE_URL, '', current_url());
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url_bits = explode('/', $url);

        if (isset($url_bits[0])) {
            $view_module = $url_bits[0];
            $view_module = str_replace('-', '/', $view_module);
        } else {
            $view_module = DEFAULT_MODULE;
        }

        return $view_module;
    }

    /**
     * Attempts to include a view file, extracting data variables if provided.
     * If the file does not exist, it terminates the script with an error message.
     *
     * @param string $file_path The path to the view file to include.
     * @param array|null $data Data to be extracted for use in the view file.
     * @return void
     */
    private static function attempt_include(string $file_path, ?array $data = null): void {
        if (file_exists($file_path)) {
            if (isset($data)) {
                extract($data);
            }

            require_once($file_path);
        } else {
            die('<br><b>ERROR:</b> View file does not exist at: ' . $file_path);
        }
    }

    /**
     * Builds HTML code for additional includes based on file types.
     *
     * @param array $files Array of file names.
     * @return string HTML code for additional includes.
     */
    private function build_additional_includes(array|string|null $files): string {
        if (!is_array($files)) {
            return ''; // Return an empty string if $files is not an array
        }

        $html = '';
        $tabs_str = '    '; // Assuming 4 spaces per tab

        foreach ($files as $index => $file) {
            $file_bits = explode('.', $file);
            $filename_extension = end($file_bits);

            if ($index > 0) {
                $html .= $tabs_str; // Add tabs for lines beyond the first
            }

            $html .= match ($filename_extension) {
                'js' => $this->build_js_include_code($file), // Add JS separately without a newline
                'css' => $this->build_css_include_code($file) . PHP_EOL, // Add a newline for CSS files
                default => $file . PHP_EOL, // Add a newline for other file types
            };
        }

        return trim($html) . PHP_EOL;
    }

    /**
     * Builds JavaScript include code for the given file.
     *
     * @param string $file File path for JavaScript include.
     * @return string JavaScript include code.
     */
    private function build_js_include_code(string $file): string {
        $code = '<script src="' . $file . '"></script>';
        $code = str_replace('""></script>', '"></script>', $code);
        return $code;
    }

}