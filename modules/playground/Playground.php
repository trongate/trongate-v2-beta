<?php
class Playground extends Trongate {

    public function test() {
        $data = $this->get_data_from_post();
    	$this->view('test', $data);
    }

    public function submit() {

        $this->validation->set_rules('first_name', 'first name', 'required|min_length[5]|callback_firstname_check');
        $this->validation->set_rules('last_name', 'first name', 'required');

        $result = $this->validation->run();
        if ($result === true) {
            echo 'well done<br>';
            echo anchor('playground/test', 'Try Again');
        } else {
            $this->test();
        }
    }

    private function get_data_from_post() {
        $data = [
            'first_name' => post('first_name'),
            'last_name' => post('last_name')
        ];
        return $data;
    }

    public function firstname_check($str) {
        if ($str === 'David') {
            $error_msg = 'That name is not allowed!';
            return $error_msg;
        } else {
            return true;
        }
    }




}