<?php
class Father extends Trongate {

    public function greeting() {
        echo '<h1>Ahoy!  I am a parent module.</h1>';
        $rows = $this->model->fetch_rows();
        json($rows);
    }

}