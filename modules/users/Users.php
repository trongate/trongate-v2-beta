<?php

/**
 * Users Controller (Trongate v2 - Using Model)
 *
 * This demonstrates the new, cleaner controller approach where
 * business logic is delegated to the Users_model.php model file.
 */
class Users extends Trongate {

    /**
     * Display the users management page.
     *
     * Notice how clean this is compared to the old version!
     * All the data fetching and manipulation logic is now in Users_model.php
     */
    function manage() {
        // All the complexity is now handled in the model
        $data['rows'] = $this->model->get_users();
        $data['view_file'] = 'manage_users';
        $this->template('public', $data);
    }

    /**
     * Display a single user.
     *
     * @param int $user_id The ID of the user to display.
     */
    function show($user_id) {
        $data['user'] = $this->model->get_user($user_id);

        if ($data['user'] === false) {
            $this->template('error_404');
            return;
        }

        $data['view_file'] = 'show_user';
        $this->template('public', $data);
    }

    /**
     * Display users by role.
     *
     * @param string $role The role to filter by.
     */
    function by_role($role) {
        $data['rows'] = $this->model->get_users_by_role($role);
        $data['role'] = $role;
        $data['view_file'] = 'users_by_role';
        $this->template('public', $data);
    }

    /**
     * Create a new user (form processing).
     */
    function create() {
        $this->validation->set_rules('username', 'Username', 'required|min_length[3]');
        $this->validation->set_rules('email', 'Email', 'required|valid_email');

        $result = $this->validation->run();

        if ($result === true) {
            $data = $this->validation->get_validated_data();
            $user_id = $this->model->create_user($data);

            set_flashdata('success', 'User created successfully!');
            redirect('users/show/' . $user_id);
        } else {
            // Show form with validation errors
            $data['view_file'] = 'create_user';
            $this->template('public', $data);
        }
    }

    /**
     * Update an existing user.
     *
     * @param int $user_id The ID of the user to update.
     */
    function update($user_id) {
        $this->validation->set_rules('username', 'Username', 'required|min_length[3]');
        $this->validation->set_rules('email', 'Email', 'required|valid_email');

        $result = $this->validation->run();

        if ($result === true) {
            $data = $this->validation->get_validated_data();
            $this->model->update_user($user_id, $data);

            set_flashdata('success', 'User updated successfully!');
            redirect('users/show/' . $user_id);
        } else {
            // Show form with validation errors
            $data['user'] = $this->model->get_user($user_id);
            $data['view_file'] = 'edit_user';
            $this->template('public', $data);
        }
    }

    /**
     * Delete a user.
     *
     * @param int $user_id The ID of the user to delete.
     */
    function delete($user_id) {
        $this->model->delete_user($user_id);
        set_flashdata('success', 'User deleted successfully!');
        redirect('users/manage');
    }

}
