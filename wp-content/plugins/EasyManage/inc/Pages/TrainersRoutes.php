<?php

namespace Inc\Pages;

use WP_Error;

class TrainersRoutes
{
    public function register()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route('api/v1', 'users/inactive/trainees', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_inactive_trainees'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ));

        register_rest_route('api/v1', 'users/trainees', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_trainee'),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('api/v1', 'users/trainees/deactivated', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'get_deactivated_trainees'),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

    }
    
    public function create_trainee($request)
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
            return new WP_Error('create_failed', 'Cohort does not exist', ['status' => 400]);
        }
    
        $user_id = wp_insert_user([
            'user_nicename' => $fullname,
            'user_login' => $email, // Use email as the login name
            'user_pass' => $password,
            'user_email' => $email,
            'role' => 'trainee', // Set the role as "trainee"
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
    


    public function get_deactivated_trainees()
{
    $trainees = get_users([
        'role'         => 'trainee',
        'meta_key'     => 'is_active',
        'meta_value'   => false,
        'meta_compare' => '=',
    ]);

    if (empty($trainees)) {
        return new WP_Error('no_deactivated_trainees_found', 'No deactivated trainees found', ['status' => 404]);
    }

    $modified_trainees = array_map(function ($trainee) {
        return [
            'ID'       => $trainee->ID,
            'id'       => $trainee->ID,
            'fullname' => $trainee->user_nicename,
            'email'    => $trainee->user_email,
            'roles'    => $trainee->roles,
            'cohort'   => get_user_meta($trainee->ID, 'cohort', true),
        ];
    }, $trainees);

    return $modified_trainees;
}

    public function get_inactive_trainees()
    {
        $inactive_trainees = get_users([
            'role'         => 'trainee',
            'meta_key'     => 'is_active',
            'meta_value'   => false,
            'meta_compare' => '=',
        ]);

        if (empty($inactive_trainees)) {
            return new WP_Error('no_inactive_trainees_found', 'No inactive trainees found', ['status' => 404]);
        }

        $modified_trainees = array_map(function ($trainee) {
            $cohort = get_user_meta($trainee->ID, 'cohort', true);

            return [
                'ID'       => $trainee->ID,
                'id'       => $trainee->ID,
                'fullname' => $trainee->user_nicename,
                'email'    => $trainee->user_email,
                'roles'    => $trainee->roles,
                'cohort'   => $cohort,
            ];
        }, $inactive_trainees);

        return $modified_trainees;
    }

}