<?php

namespace Inc\Pages;
use WP_Error;

class CohortRoutes{
    public function register(){
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes(){
        register_rest_route('api/v1', '/cohorts/', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_cohorts'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_cohort'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/', array(
            'methods' => 'POST',
            'callback' => array($this, 'post_cohort'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/(?P<id>[\d]+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_cohort'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/(?P<id>[\d]+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_cohort'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/trainer/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_cohort_trainers'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/trainees/(?P<id>[\d]+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'post_cohort_trainees'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

    }

    public function get_cohorts(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'cohorts';

        $cohorts = $wpdb->get_results("SELECT * FROM $table_name");

        return $cohorts;
    }

    
}