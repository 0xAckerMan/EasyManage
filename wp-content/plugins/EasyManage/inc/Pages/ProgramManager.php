<?php

namespace Inc\Pages;

use WP_Error;

class ProgramManager
{
    public function register()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        // Endpoint for getting trainers
        register_rest_route('api/v1', 'users/trainers', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_trainers'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', 'users/trainers/(?P<id>[\d]+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_trainers'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', 'users/trainers/(?P<id>[\d]+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_user'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        // Endpoint for creating trainers
        register_rest_route('api/v1', 'users/trainers', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_trainer'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));
    }

    public function create_trainer($request)
    {
        $user_data = $request->get_json_params();
    
        // Validate required fields
        if (empty($user_data['fullname']) || empty($user_data['email']) || empty($user_data['password']) || empty($user_data['cohort'])) {
            return new WP_Error('create_failed', 'Missing required fields', ['status' => 400]);
        }
    
        $fullname = $user_data['fullname'];
        $email = $user_data['email'];
        $password = $user_data['password'];
        $cohort = $user_data['cohort']; // Added cohort field
        $is_active = true; // Set the user as active by default
    
        // Validate email address
        if (!is_email($email)) {
            return new WP_Error('create_failed', 'Invalid email address', ['status' => 400]);
        }
    
        // Check if user with the same email already exists
        if (email_exists($email)) {
            return new WP_Error('create_failed', 'User with the same email already exists', ['status' => 409]);
        }
    
        // Check if the cohort exists
        global $wpdb;
        $cohort_table = $wpdb->prefix . 'cohorts';
        $cohort_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $cohort_table WHERE c_id = %d", $cohort));
    
        if (!$cohort_exists) {
            return new WP_Error('create_failed', 'Assigned cohort does not exist', ['status' => 400]);
        }
    
        $role = isset($user_data['role'][0]) ? $user_data['role'][0] : 'trainer'; // Set the role as "trainee" if not provided
    
        $user_id = wp_insert_user([
            'user_nicename' => $fullname,
            'user_login' => $email, // Use email as the login name
            'user_pass' => $password,
            'user_email' => $email,
            'role' => $role,
        ]);
    
        if (is_wp_error($user_id)) {
            return new WP_Error('create_failed', $user_id->get_error_message(), ['status' => 500]);
        }
    
        // Update the user meta for the cohort field
        update_user_meta($user_id, 'cohort', $cohort);
    
        // Update the user meta for the is_active field
        update_user_meta($user_id, 'is_active', $is_active);
    
        return 'User created successfully';
    }
    

    public function delete_user($request)
    {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        $user_id = $request['id'];
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }
        $is_deleted = wp_delete_user($user_id);
        if (!$is_deleted) {
            return new WP_Error('delete_failed', 'User not deleted', ['status' => 500]);
        }
        return 'User deleted successfully';
    }

    public function get_trainers()
    {
        $trainers = get_users([
            'role'         => 'trainer',
            'meta_key'     => 'is_active',
            'meta_value'   => true,
            'meta_compare' => '=',
        ]);

        if (empty($trainers)) {
            return new WP_Error('no_active_trainers_found', 'No active trainers found', ['status' => 404]);
        }

        $modified_trainers = array_map(function ($trainer) {
            return [
                'ID'       => $trainer->ID,
                'id'       => $trainer->ID,
                'fullname' => $trainer->user_nicename,
                'email'    => $trainer->user_email,
                'roles'    => $trainer->roles,
                'cohort'   => (string) get_user_meta($trainer->ID, 'cohort', true),
            ];
        }, $trainers);

        return $modified_trainers;
    }

    public function update_trainers($request)
    {
        $user_data = $request->get_json_params();

        $user_id = $user_data['id'];
        $fullname = $user_data['fullname'];
        $email = $user_data['email'];
        $role = $user_data['role'];
        $password = $user_data['password'];
        $cohort = $user_data['cohort']; // Added cohort field

        $user = wp_update_user([
            'ID' => $user_id,
            'user_nicename' => $fullname,
            'user_login' => $email,
            'user_pass' => $password,
            'user_email' => $email,
            'role' => $role,
            'cohort' => $cohort // Update the cohort value
        ]);

        if (is_wp_error($user)) {
            return new WP_Error('update_failed', $user->get_error_message(), ['status' => 404]);
        }

        return 'User updated successfully';
    }
}
