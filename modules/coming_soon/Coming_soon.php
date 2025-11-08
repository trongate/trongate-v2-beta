<?php
class Coming_soon extends Trongate {

    public function index() {

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => BASE_URL],
            ['title' => 'Module Market', 'url' => BASE_URL . 'coming_soon']
        ];

        $data['view_file'] = 'coming_soon';
        $this->template('stealth', $data);
    }

}