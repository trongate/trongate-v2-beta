<?php
class Demo extends Trongate {

    /**
     * Renders the users table view with filtered and prepared user data.
     *
     * Retrieves user records from the database, applies filtering and preparation,
     * and then renders the 'users_tables' view within the 'forum' template.
     *
     * @return void
     */
    public function hello(): void {
        $rows = $this->model->fetch_rows();
        $data['rows'] = $rows;
        $data['view_file'] = 'users_tables';
        $this->template('forum', $data);
    }

}