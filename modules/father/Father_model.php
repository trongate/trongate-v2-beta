<?php
class Father_model extends Model {

    public function fetch_rows() {
        $rows = $this->db->get('id desc', 'endpoint_listener');
        return $rows;
    }

}