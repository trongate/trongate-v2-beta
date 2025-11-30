<?php
class Documentation_model extends Model {

    public function get_books() {
        $books = $this->live5->get('id', 'documentation_books');
        return $books;
    }

}