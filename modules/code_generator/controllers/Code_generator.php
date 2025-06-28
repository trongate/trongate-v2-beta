<?php
class Code_generator extends Trongate {

	private $api_base_url = 'http://localhost/trongate_live5/';

    public function index() {
    	$data['api_base_url'] = $this->api_base_url;
        $this->view('code_generator_template', $data);
    }

    public function fetch_starter_content() {
    	$target_url = $this->api_base_url.'t2_api-code_generator/home';
    	$response = $this->perform_get_request($target_url);
    	http_response_code($response['status_code']);
    	echo $response['response_body'];
    }

	/**
	 * Performs a POST request to the specified URL with optional parameters and headers.
	 *
	 * @param string $url The URL to send the POST request to.
	 * @param array $params An associative array of POST data.
	 * @param array $headers Optional associative array of headers (e.g. ['Content-Type' => 'application/x-www-form-urlencoded']).
	 *
	 * @return array An array containing 'response_body', 'status_code', and 'curl_error'.
	 */
	private function perform_post_request(string $url, array $params = [], array $headers = []): array {
	    // Prepare headers
	    $formatted_headers = [];
	    foreach ($headers as $key => $value) {
	        $formatted_headers[] = $key . ': ' . $value;
	    }

	    // Initialize cURL session
	    $ch = curl_init();

	    // Set cURL options
	    curl_setopt_array($ch, [
	        CURLOPT_URL => $url,
	        CURLOPT_POST => true,
	        CURLOPT_POSTFIELDS => http_build_query($params),
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_HTTPHEADER => $formatted_headers,
	        CURLOPT_TIMEOUT => 60,
	        CURLOPT_FOLLOWLOCATION => true,
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
	 * Performs a GET request to the specified URL with optional headers.
	 *
	 * @param string $url The URL to send the GET request to.
	 * @param array $headers Optional associative array of headers.
	 *
	 * @return array An array containing 'response_body', 'status_code', and 'curl_error'.
	 */
	private function perform_get_request(string $url, array $headers = []): array {
	    // Prepare headers
	    $formatted_headers = [];
	    foreach ($headers as $key => $value) {
	        $formatted_headers[] = $key . ': ' . $value;
	    }

	    // Initialize cURL session
	    $ch = curl_init();

	    // Set cURL options
	    curl_setopt_array($ch, [
	        CURLOPT_URL => $url,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_HTTPHEADER => $formatted_headers,
	        CURLOPT_TIMEOUT => 30,
	        CURLOPT_FOLLOWLOCATION => true,
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