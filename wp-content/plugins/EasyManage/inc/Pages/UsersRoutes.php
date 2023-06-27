<?php

/**
 * @package EasyManage
 */

namespace Inc\Pages;

use WP_Error;

class UsersRoutes
{

    public function register()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route('api/v1', 'users', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_users'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/users/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'single_user'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ));

        register_rest_route('api/v1', '/users/', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_user'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ));

        register_rest_route('api/v1', '/users/(?P<id>[\d]+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_user'),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('api/v1', '/users/(?P<id>[\d]+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_user'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ));

        register_rest_route('api/v1', 'users/admins', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_admins'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ));


        // Endpoint for getting project managers (PMs)
        register_rest_route('api/v1', 'users/pms', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_pms'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ));

        register_rest_route('api/v1', '/users/(?P<id>[\d]+)/activate', array(
            'methods' => 'POST',
            'callback' => array($this, 'activate_user'),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('api/v1', '/users/(?P<id>[\d]+)/deactivate', array(
            'methods' => 'POST',
            'callback' => array($this, 'deactivate_user'),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('api/v1', 'users/deactivated', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_deactivated_users'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ));

        register_rest_route('api/v1', '/users/program_manager', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_program_manager'),
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ));
        
        
    }

    public function get_users()
    {
        $users = get_users([
            'meta_key'     => 'is_active',
            'meta_value'   => true,
            'meta_compare' => '=',
            'fields'       => ['ID', 'user_nicename', 'user_email', 'role', 'cohort'],
        ]);
    
        if (empty($users)) {
            return new WP_Error('no_users_found', 'No users found', ['status' => 404]);
        }
    
        $modified_users = array_map(function ($user) {
            $user->fullname = $user->user_nicename;
            $user->email = $user->user_email;
    
            unset($user->user_nicename);
            unset($user->user_email);
            return $user;
        }, $users);
    
        foreach ($users as &$user) {
            $user_roles = get_userdata($user->ID)->roles;
            $user->roles = $user_roles;
        }
    
        return $modified_users;
    }
    
    public function single_user($request)
    {
        $user_id = $request['id'];
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }
        $user_data = [
            'ID' => $user->ID,
            'fullname' => $user->user_nicename,
            'email' => $user->user_email,
            'roles' => $user->roles,
            'cohort' => $user->cohort
        ];
        return $user_data;
    }
    
    public function create_user($request)
    {
        $user_data = $request->get_json_params();
    
        // Validate required fields
        if (empty($user_data['fullname']) || empty($user_data['email']) || empty($user_data['role']) || empty($user_data['password']) || empty($user_data['cohort'])) {
            return new WP_Error('create_failed', 'Missing required fields', ['status' => 400]);
        }
    
        $fullname = $user_data['fullname'];
        $email = $user_data['email'];
        $role = $user_data['role'];
        $password = $user_data['password'];
        $cohort = $user_data['cohort'];
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
            return new WP_Error('create_failed', 'Cohort does not exist', ['status' => 400]);
        }
    
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
    
        return '200 OK. User created successfully';
    }
    
    
    public function create_program_manager($request)
    {
        $user_data = $request->get_json_params();
    
        // Validate required fields
        if (empty($user_data['fullname']) || empty($user_data['email']) || empty($user_data['password'])) {
            return new WP_Error('create_failed', 'Missing required fields', ['status' => 400]);
        }
    
        $fullname = $user_data['fullname'];
        $email = $user_data['email'];
        $password = $user_data['password'];
        $is_active = true; // Set the user as active by default
    
        // Validate email address
        if (!is_email($email)) {
            return new WP_Error('create_failed', 'Invalid email address', ['status' => 400]);
        }
    
        // Check if user with the same email already exists
        if (email_exists($email)) {
            return new WP_Error('create_failed', 'User with the same email already exists', ['status' => 409]);
        }
    
        $role = 'program-manager'; // Set the role as "trainer"
    
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
    
        // Update the user meta for the is_active field
        update_user_meta($user_id, 'is_active', $is_active);
    
        return 'User created successfully';
    }
     
    
    

    public function update_user($request)
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

    public function get_admins()
    {
        $admins = get_users(['role' => 'administrator']);

        if (empty($admins)) {
            return new WP_Error('no_admins_found', 'No administrators found', ['status' => 404]);
        }

        $modified_admins = array_map(function ($admin) {
            $user_data = [
                'ID' => $admin->ID,
                'id' => $admin->ID,
                'fullname' => $admin->user_nicename,
                'email' => $admin->user_email,
                'roles' => $admin->roles
            ];

            return $user_data;
        }, $admins);

        return $modified_admins;
    }


    public function get_pms()
    {
        $pms = get_users(['role' => 'program-manager']);

        if (empty($pms)) {
            return new WP_Error('no_pms_found', 'No program managers found', ['status' => 404]);
        }

        $modified_pms = array_map(function ($pm) {
            $user_data = [
                'ID' => $pm->ID,
                'id' => $pm->ID,
                'fullname' => $pm->user_nicename,
                'email' => $pm->user_email,
                'roles' => $pm->roles,
            ];

            return $user_data;
        }, $pms);

        return $modified_pms;
    }


    public function deactivate_user($request)
    {
        $user_id = $request['id'];
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }
        $is_deactivated = update_user_meta($user_id, 'is_active', false);
        if (!$is_deactivated) {
            return new WP_Error('deactivate_failed', 'User not deactivated', ['status' => 500]);
        }
        return 'User deactivated successfully';
    }

    public function activate_user($request)
    {
        $user_id = $request['id'];
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }
        $is_activated = update_user_meta($user_id, 'is_active', true);
        if (!$is_activated) {
            return new WP_Error('activate_failed', 'User not activated', ['status' => 500]);
        }
        return 'User activated successfully';
    }

    public function get_deactivated_users()
    {
        $users = get_users([
            'meta_key'     => 'is_active',
            'meta_value'   => false,
            'meta_compare' => '=',
            'role__not_in' => ['administrator'], // Exclude administrators from the results if needed
        ]);

        if (empty($users)) {
            return new WP_Error('no_deactivated_users_found', 'No deactivated users found', ['status' => 404]);
        }

        $modified_users = array_map(function ($user) {
            return [
                'ID' => $user->ID,
                'id' => $user->ID,
                'fullname' => $user->user_nicename,
                'email' => $user->user_email,
                'roles' => $user->roles,
                'cohort' => get_user_meta($user->ID, 'cohort', true),
            ];
        }, $users);

        return $modified_users;
    }
    
}
