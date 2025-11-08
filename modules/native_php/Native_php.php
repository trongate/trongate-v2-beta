<?php
class Native_php extends Trongate {

    public function index() {

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'What is Native PHP?', 'url' => BASE_URL . 'native_php']
        ];

        $data['view_file'] = 'native_php';
        $this->template('stealth', $data);
    }

}