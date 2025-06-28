<?php
class Test extends Trongate {

    public function index() {
        $this->view('test_html_template');
    }

    public function another_page() {
    	$this->view('another_page');
    }

}