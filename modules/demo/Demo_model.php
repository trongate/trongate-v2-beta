<?php
class Demo_model extends Trongate {

    public function fetch_rows() {
        $rows = $this->alt->get('id', 'endpoint_listener');
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