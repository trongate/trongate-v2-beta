<?php

/**
 * Users Model
 *
 * Handles data fetching and manipulation for the Users module.
 * This file demonstrates how to structure a module-specific model.
 */
class Users_model extends Model {

    /**
     * Get all non-banned users with formatted join dates.
     *
     * This method encapsulates business logic that would otherwise
     * clutter the controller.
     *
     * @return array Array of user objects with embellished data.
     */
    function get_users(): array {
        // Fetch all users from database
        $rows = $this->db->get('id', 'users');

        // Remove banned users
        $rows = $this->eliminate_banned_users($rows);

        // Add formatted date information
        $rows = $this->embellish_rows($rows);

        return $rows;
    }

    /**
     * Get a single user by ID with embellished data.
     *
     * @param int $user_id The ID of the user to fetch.
     * @return object|false The user object or false if not found.
     */
    function get_user(int $user_id): object|false {
        $user = $this->db->get_where($user_id, 'users');

        if ($user === false) {
            return false;
        }

        // Add formatted date
        $date_joined = (int) $user->date_joined;
        $user->date_joined_nice = date('l jS F Y', $date_joined);

        return $user;
    }

    /**
     * Get users by role.
     *
     * @param string $role The role to filter by (e.g., 'admin', 'member').
     * @return array Array of user objects.
     */
    function get_users_by_role(string $role): array {
        $rows = $this->db->get_where_custom('role', $role, '=', 'id', 'users');
        return $this->embellish_rows($rows);
    }

    /**
     * Create a new user.
     *
     * @param array $data User data to insert.
     * @return int The ID of the newly created user.
     */
    function create_user(array $data): int {
        // Add timestamp if not provided
        if (!isset($data['date_joined'])) {
            $data['date_joined'] = time();
        }

        return $this->db->insert($data, 'users');
    }

    /**
     * Update a user's information.
     *
     * @param int $user_id The ID of the user to update.
     * @param array $data The data to update.
     * @return void
     */
    function update_user(int $user_id, array $data): void {
        $this->db->update($user_id, $data, 'users');
    }

    /**
     * Delete a user.
     *
     * @param int $user_id The ID of the user to delete.
     * @return void
     */
    function delete_user(int $user_id): void {
        $this->db->delete($user_id, 'users');
    }

    /**
     * Remove banned users from a result set.
     *
     * @param array $rows Array of user objects.
     * @return array Filtered array with banned users removed.
     */
    private function eliminate_banned_users(array $rows): array {
        foreach ($rows as $key => $row) {
            $banned = (int) $row->banned;
            if ($banned === 1) {
                unset($rows[$key]);
            }
        }
        return $rows;
    }

    /**
     * Add formatted date information to user objects.
     *
     * @param array $rows Array of user objects.
     * @return array Array of user objects with added date_joined_nice property.
     */
    private function embellish_rows(array $rows): array {
        foreach ($rows as $key => $row) {
            $date_joined = (int) $row->date_joined;
            $rows[$key]->date_joined_nice = date('l jS F Y', $date_joined);
        }
        return $rows;
    }

}
