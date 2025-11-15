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

    public function display_infoXXX() {

	    // Access ref_path and ref_name from HTTP headers
	    $ref_path = $_SERVER['HTTP_REF_PATH'] ?? '';
	    $ref_name = $_SERVER['HTTP_REF_NAME'] ?? '';

	    // Method 2: Fallback to apache_request_headers() if needed
	    if ($ref_path === '' && function_exists('apache_request_headers')) {
	        $headers = apache_request_headers();
	        foreach ($headers as $key => $value) {
	            if (strtolower($key) === 'ref_path') {
	                $ref_path = $value;
	            }
	            if (strtolower($key) === 'ref_name') {
	                $ref_name = $value;
	            }
	        }
	    }

	    if ($ref_path === '') {
	    	$ref_name = str_replace('()', '', $ref_name);
	    	$file_path = $this->find_file_path($ref_name);
	    	$ref_path = str_replace(APPPATH, '', $file_path);
	    	$ref_path = str_replace('modules/documentation/assets/reference/', '', $ref_path);

			$last_pos = strrpos($ref_path, '/'); // Find the last occurrence of '/'
			if ($last_pos !== false) {
			    $ref_path = substr($ref_path, 0, $last_pos); // Remove the part after the last '/'
			}
	    }

	    $ref_name = str_replace('()', '', $ref_name);

	    // Construct the file path based on ref_path and ref_name
	    $target_dir = APPPATH . 'modules/documentation/assets/reference/' . $ref_path;
	    $file_path = $target_dir . '/' . $ref_name . '.html';
$file_path = str_replace('trongate_live6', 'trongate_live5', $file_path);

echo $file_path; die();
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

        $this->view('feature_ref', []);
    }

	function find_file_path($target_file) {

		$base_dir = APPPATH.'modules/documentation/assets/reference/';
$base_dir = str_replace('trongate_live6', 'trongate_live5', $base_dir);
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