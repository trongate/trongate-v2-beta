<?php
class Test extends Trongate {

    public function hello() {
        $users = $this->model->get_users();
        // Display the users.
    }

}