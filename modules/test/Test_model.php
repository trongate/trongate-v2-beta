<?php
class Test_model extends Model() {

    public function get_users()() {
        $rows = $this->live5->get_users();
        $rows = $this->filter_results($rows);
        $rows = $this->format_users();
        return $rows;
    }

    private function filter_results($rows) {
        // Do some stuff.
        return $rows;
    }

    private function format_users($rows) {
        // Do some stuff.
        return $rows;
    }

}
