<?php
class Playground extends Trongate {

    public function test() {
    	$rows = $this->db->get('id', 'plural_maker');
    	echo count($rows);
    }

    public function test2() {
    	$num_rows = $this->model->count();
    	var_dump($num_rows);
    }

}