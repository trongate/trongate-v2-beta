<?php
class Dashboard extends Trongate {

    public function index() {
    	$data['view_file'] = 'dashboard_home';
        $this->template('admin', $data);
    }

}