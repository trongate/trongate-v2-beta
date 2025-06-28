<?php
class Code_generator extends Trongate {

    private $api_base_url = 'http://localhost/trongate_live5/t2_api-code_generator/';

    public function index() {
    	$data = [
    		'view_module' => 'code_generator',
    		'view_file' => 'code_generator_home'
    	];

        $this->view('code_generator_template', $data);
    }

    public function properties_builder() {
        $this->view('properties_builder');
    }

    public function mod_maker() {
        $this->view('mod_maker_options');
    }

    public function draw_options_page() {
        $options_code = segment(3);
    	$data['options'] = $this->fetch_codegen_options($options_code);
        $this->view('options_page', $data);
    }

    public function test() {
    	//sleep(2);
    	echo 'Test message';
    }

    public function enter_single_value() {
        $input_code = segment(3);
        $data['input_code'] = $input_code;
        $data['input_instructions'] = $this->fetch_input_instructions($input_code);
        $this->view('enter_single_value', $data);
    }

    private function fetch_input_instructions($input_code) {
        $input_descriptions = [
            'mod_name' => 'Enter Module Name (singular)'
        ];

        return $input_descriptions[$input_code];
    }

    public function submit_mod_name() {

        // Define the path to the modules directory
        $path = rtrim(APPPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'modules';

        // Validate directory existence and readability
        if (!is_dir($path) || !is_readable($path)) {
            http_response_code(500);
            echo json_encode(['error' => 'Modules directory is not accessible']);
            return;
        }

        // Get list of valid directories
        $directories = array_filter(scandir($path), function($item) use ($path) {
            return is_dir($path . DIRECTORY_SEPARATOR . $item) && !in_array($item, ['.', '..']);
        });

        $existing_modules = array_values($directories); // Reindex array

        $params = [
            'mod_name' => post('mod_name'),
            'from_url' => post('from_url'),
            'csrf_token' => post('csrf_token'),
            'existing_modules' => $existing_modules
        ];

        $target_url = $this->api_base_url.'submit_mod_name';
        $response = $this->perform_post_request($target_url, $params);
        http_response_code($response['status_code']);
        echo $response['response_body'];
    }

    private function fetch_codegen_options($options_code) {

        $all_options['create_new_mod'] = [
            [
                'target_url' => 'code_generator/enter_single_value/mod_name',
                'value' => 'New Trongate Module'
            ],
            [
                'target_url' => 'open_mod_market',
                'value' => 'Browse The Module Market'
            ]
        ];

        return $all_options['create_new_mod'];
    }

    /**
     * Performs a POST request to the specified URL with optional parameters.
     *
     * @param string $url The URL to send the POST request to.
     * @param array $params An associative array of POST data.
     *
     * @return array An array containing 'response_body', 'status_code', and 'curl_error'.
     */
    private function perform_post_request(string $url, array $params = []): array {
        // Initialize cURL session
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),    // Form-encoded POST data
            CURLOPT_RETURNTRANSFER => true,                     // Return response as string
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_TIMEOUT => 60,                              // Timeout after 60 seconds
            CURLOPT_FOLLOWLOCATION => true,                     // Follow redirects
            CURLOPT_MAXREDIRS => 5,
        ]);
        
        // Execute request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        // Close cURL handle
        curl_close($ch);
        
        // Return structured result
        return [
            'response_body' => $response,
            'status_code' => $http_code,
            'curl_error' => $curl_error
        ];
    }

    /**
     * Performs a GET request to the specified URL.
     *
     * @param string $url The URL to send the GET request to.
     *
     * @return array An array containing 'response_body', 'status_code', and 'curl_error'.
     */
    private function perform_get_request(string $url): array {
        // Initialize cURL session
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,                     // Return response as string
            CURLOPT_TIMEOUT => 30,                              // Timeout after 30 seconds
            CURLOPT_FOLLOWLOCATION => true,                     // Follow redirects
            CURLOPT_MAXREDIRS => 5,
        ]);
        
        // Execute request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        // Close cURL handle
        curl_close($ch);
        
        // Return structured result
        return [
            'response_body' => $response,
            'status_code' => $http_code,
            'curl_error' => $curl_error
        ];
    }

}