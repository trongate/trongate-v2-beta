<?php
class Code_generator extends Trongate {

    public function index() {
    	$data = [
    		'view_module' => 'code_generator',
    		'view_file' => 'code_generator_home'
    	];

        $this->view('code_generator', $data);
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

    public function submit_value() {
        $from_url = post('from_url');
        $posted_data = $_POST;

        unset($posted_data['from_url']);
        unset($posted_data['csrf_token']);

        // Reset the array pointer to the first element
        reset($posted_data);

        // Get the first key and value
        $posted_key = key($posted_data);
        $posted_value = current($posted_data);

        // Return as an associative array or use however needed
        $result = $this->run_validation_tests($posted_key);

        if ($result === false) {
            $data['from_url'] = $from_url;
            $this->view('validation_errors', $data);
        } else {
            $this->process_submitted_value($posted_key, $posted_value);
        }
    }

    private function process_submitted_value($posted_key, $posted_value) {

        // Display a blinking 'loading' message THEN.

        // Choose nav label

        // *** Choose icon *** (read from the URL)

        // *** Properties Builder *** (read from the URL)

        // Choose URL column

        // Default order by

        // GENERATE NEW MODULE

        switch ($posted_key) {
            case 'mod_name':

                $this->process_submitted_mod_name($posted_value);
                break;
            
            default:
                echo 'ouch';
                die();
                break;
        }
 
    }

    private function process_submitted_mod_name($posted_value) {
        $module_name_plural = 'Companies'; // Fetch this from the website URL!
        
    }

    private function run_validation_tests($posted_key) {

        switch ($posted_key) {
            case 'mod_name':
                $this->validation->set_rules('mod_name', 'module name', 'required');
                break;
            
            default:
                $this->validation->set_rules($posted_key, 'posted value', 'required');
                break;
        }


        $result = $this->validation->run();
        return $result;
    }

    private function fetch_input_instructions($input_code) {
        $input_descriptions = [
            'mod_name' => 'Enter Module Name (singular)'
        ];

        return $input_descriptions[$input_code];
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

}