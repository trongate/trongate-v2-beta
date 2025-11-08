<?php
class Demo extends Trongate {

    public function hello() {
        $rows = $this->db->fetch_rows();
        json($rows);
    }







}