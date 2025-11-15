<?php
class Ref extends Trongate {

	public function display_info() {
		$ref_name = segment(3);
		$file_path = $this->find_file_path($ref_name);
	    try {
	        // Check if the file exists
	        if (!file_exists($file_path)) {
	            throw new Exception("The file {$file_path} was not found.");
	        }
	        // Read the file content
	        $data['feature_ref_info'] = file_get_contents($file_path);
	    } catch (Exception $e) {
	        // Fallback content or error handling
	        $data['feature_ref_info'] = '<p>Sorry, the requested information is not available at this time.</p>';
	        $data['feature_ref_info'].= '<p>Ref Path: '.$ref_path.'.</p>';
	        $data['feature_ref_info'].= '<p>Ref Name: '.$ref_name.'.</p>';
            $data['feature_ref_info'].= '<p>File path: '.$file_path.'</b>';
	    }

        $this->view('feature_ref', $data);
	}

	function find_file_path($target_file) {

		$base_dir = APPPATH.'modules/documentation/assets/reference/';

	    // Add the .html extension to the target file
	    $target_file .= '.html';
	    
	    // Define the main directories to search in
	    $main_dirs = [
	        'helpers',
	        'class_reference',
	        'pre_installed'
	    ];
	    
	    // Loop through each main directory
	    foreach ($main_dirs as $dir) {
	        // Build the full path to the current subdirectory (correct path construction)
	        $dir_path = $base_dir.$dir;

	        // Check if the directory exists
	        if (is_dir($dir_path)) {
	            
	            // Scan the directory recursively for files
	            $iterator = new RecursiveIteratorIterator(
	                new RecursiveDirectoryIterator($dir_path),
	                RecursiveIteratorIterator::LEAVES_ONLY
	            );
	            
	            // Iterate over all files and directories
	            foreach ($iterator as $file) {
	                // Only process files (not directories)
	                if ($file->isFile() && $file->getFilename() === $target_file) {
	                    // Return the path to the target file
	                    return $file->getPathname();
	                }
	            }
	        }
	    }
	    
	    // Return false if the file is not found
	    return false;
	}

}