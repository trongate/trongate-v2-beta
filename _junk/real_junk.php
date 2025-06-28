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

    public function submit_valueX() {
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

        // switch ($posted_key) {
        //     case 'mod_name':

        //         $this->process_submitted_mod_name($posted_value);
        //         break;
            
        //     default:
        //         echo 'ouch';
        //         die();
        //         break;
        // }
 
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