<?php
class Ref extends Trongate {

    /**
     * Display reference information for a given feature.
     *
     * Extracts the reference name from the URI segment, determines the path
     * of the corresponding reference file, reads its contents, applies code-block
     * hiding, and loads the view.
     *
     * @return void
     */
    public function display_info(): void {
        
        if (strpos(segment(3), '-')) {
            $ref_name = get_last_part(segment(3), '-');
            $base_dir = APPPATH.'modules/documentation/assets/reference/';
            $file_path = $base_dir.str_replace('-', '/', segment(3)).'.html';
        } else {
            $ref_name = segment(3);
            $file_path = $this->find_file_path($ref_name);
        }

        try {
            if (!file_exists($file_path)) {
                throw new Exception("The file {$file_path} was not found.");
            }
            $data['feature_ref_info'] = file_get_contents($file_path);
        } catch (Exception $e) {
            $data['feature_ref_info'] = '<p>Sorry, the requested information is not available at this time.</p>';
            //$data['feature_ref_info'] .= '<p>Ref Path: '.$ref_path.'.</p>';
            $data['feature_ref_info'] .= '<p>Ref Name: <code>'.$ref_name.'()</code></p>';
        }

        $data['feature_ref_info'] = $this->documentation->hide_code_blocks($data['feature_ref_info']);
        $this->view('feature_ref', $data);
    }

    /**
     * Locate the full file system path for a given reference file name.
     *
     * Searches multiple documentation subdirectories recursively to find
     * the matching .html reference file.
     *
     * @param string $target_file  The base filename (without extension).
     *
     * @return string|false  Returns the full path if found, otherwise false.
     */
    public function find_file_path(string $target_file): string|false {

        $base_dir = APPPATH.'modules/documentation/assets/reference/';

        $target_file .= '.html';

        $main_dirs = [
            'helpers',
            'class_reference',
            'pre_installed'
        ];

        foreach ($main_dirs as $dir) {
            $dir_path = $base_dir.$dir;

            if (is_dir($dir_path)) {

                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir_path),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getFilename() === $target_file) {
                        return $file->getPathname();
                    }
                }
            }
        }

        return false;
    }

}
