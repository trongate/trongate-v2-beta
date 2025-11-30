<?php
class Son_model extends Model {

    public function get_my_rows() {
    	$rows = $this->db->get('id', 'endpoint_listener');
    	return $rows;
    }

}