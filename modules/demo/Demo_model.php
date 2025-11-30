v2 Checklist

1).  Remove controllers folders from modules.  *
2).  Remove assets folders from modules.  *
3).  Replace  $this->model   with   $this->db  





















<?php
class Demo_model extends Model {

    public function fetch_rows() {
        $rows = $this->db->get('id', 'endpoint_listener');
        $rows = $this->embellish_rows($rows);
        $rows = $this->prep_rows_for_display($rows);
        return $rows;
    }

    private function embellish_rows($rows) {
    	// do stuff
    	return $rows;
    }

    private function prep_rows_for_display($rows) {
    	// do stuff
    	return $rows;
    }

}