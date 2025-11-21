<?php
class Templates extends Trongate {

    // function bootstrappy($data) {
    //     $data['additional_includes_top'] = $this->build_additional_includes($data['additional_includes_top'] ?? []);
    //     $data['additional_includes_btm'] = $this->build_additional_includes($data['additional_includes_btm'] ?? []);
    //     load('bootstrappy', $data);
    // }

    public function admin(array $data): void {
        $data['additional_includes_top'] = $this->build_additional_includes($data['additional_includes_top'] ?? []);
        $data['additional_includes_btm'] = $this->build_additional_includes($data['additional_includes_btm'] ?? []);
        load('admin', $data);
    }

    /**
     * Loads the 'docs_ahoy' view with provided data.
     *
     * @param mixed $data Data array to be passed to the view.
     * @return void
     */
    public function docs_ahoy($data): void {
        $data['theme'] = (isset($data['theme'])) ? $data['theme'] : 'blue';
        $data['additional_includes_top'] = $this->build_additional_includes($data['additional_includes_top'] ?? []);
        $data['additional_includes_btm'] = $this->build_additional_includes($data['additional_includes_btm'] ?? []);
        load('docs_ahoy', $data);
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