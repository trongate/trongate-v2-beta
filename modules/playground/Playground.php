<?php
class Playground extends Trongate {

    public function test() {
    	$data['rows'] = $this->db->get('id', 'plural_maker');

		$pagination_data["total_rows"] = count($data['rows']);
		$pagination_data["limit"] = 10;
		$pagination_data["record_name_plural"] = "books";
		$pagination_data["include_showing_statement"] = true;
		$pagination_data["include_css"] = true;
		$data["pagination_data"] = $pagination_data;


        $this->view('test', $data);
    }

}