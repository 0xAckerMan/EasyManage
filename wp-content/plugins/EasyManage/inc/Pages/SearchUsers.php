<?php
/**
 * package EasyManage
 */

namespace Inc\Pages;

use WP_Error;

class SearchUsers{

    public function register(){
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        //endpoint to search users
        register_rest_route('api/v1', 'users/search/(?P<name>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_users_by_name'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', 'users/trainees/search', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_trainees_by_name'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));
        

    }

    public function search_users_by_name($request) {
        $name = $request->get_param('name');
        $users = get_users(array(
            'search'         => '*' . sanitize_text_field($name) . '*',
            'search_columns' => array('user_nicename', 'user_email'),
            'fields'         => array('ID', 'user_nicename', 'user_email', 'roles', 'cohort'),
            'meta_query'     => array(
                array(
                    'key'     => 'is_active',
                    'value'   => true,
                    'compare' => '=',
                ),
            ),
        ));
    
        if (empty($users)) {
            return new WP_Error('no_users_found', 'No users found', array('status' => 404));
        }
    
        $modified_users = array_map(function ($user) {
            $user_roles = get_userdata($user->ID)->roles;
            return array(
                'ID'       => $user->ID,
                'id'       => $user->ID,
                'fullname' => $user->user_nicename,
                'email'    => $user->user_email,
                'roles'    => $user_roles,
                'cohort'   => get_user_meta($user->ID, 'cohort', true),
            );
        }, $users);
    
        return $modified_users;
    }

    public function search_trainees_by_name($request) {
        $name = $request->get_param('name');
        $trainees = get_users(array(
            'search'         => '*' . sanitize_text_field($name) . '*',
            'search_columns' => array('user_nicename', 'user_email'),
            'role'           => 'trainee',
            'fields'         => array('ID', 'user_nicename', 'user_email', 'cohort'),
            'meta_query'     => array(
                array(
                    'key'     => 'is_active',
                    'value'   => true,
                    'compare' => '=',
                ),
            ),
        ));
    
        if (empty($trainees)) {
            return new WP_Error('no_trainees_found', 'No trainees found', array('status' => 404));
        }
    
        $modified_trainees = array_map(function ($trainee) {
            return array(
                'ID'       => $trainee->ID,
                'id'       => $trainee->ID,
                'fullname' => $trainee->user_nicename,
                'email'    => $trainee->user_email,
                'cohort'   => get_user_meta($trainee->ID, 'cohort', true),
            );
        }, $trainees);
    
        return $modified_trainees;
    }
    
}
