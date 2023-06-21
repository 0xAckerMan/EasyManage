<?php

namespace Inc\Pages;

use WP_Error;
use WP_REST_Response;
use WP_REST_Server;
use WP_REST_Request;

class CohortRoutes {
    public function register() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('api/v1', '/cohorts', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_cohorts'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/(?P<id>[\d]+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_cohort'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'create_cohort'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/cohorts/(?P<id>[\d]+)', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'update_cohort'),
            'permission_callback' => function () {
                return current_user_can('delete_posts');
            }
        ));

        register_rest_route('api/v1', '/cohorts/(?P<id>[\d]+)', array(
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => array($this, 'delete_cohort'),
            'permission_callback' => function () {
                return current_user_can('delete_posts');
            }
        ));

        register_rest_route('api/v1', '/cohorts/(?P<id>[\d]+)/mark-done', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'mark_cohort_as_done'),
            'permission_callback' => function () {
                return current_user_can('edit_cohort');
            }
        ));
    }

    public function get_cohorts() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'cohorts';

        $cohorts = $wpdb->get_results("SELECT * FROM $table_name");

        if (empty($cohorts)) {
            return new WP_Error('no_cohorts_found', 'No cohorts found', array('status' => 404));
        }

        return new WP_REST_Response($cohorts, 200);
    }

    public function get_cohort(WP_REST_Request $request) {
        global $wpdb;

        $cohort_id = $request->get_param('id');
        $table_name = $wpdb->prefix . 'cohorts';

        $cohort = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE c_id = %d", $cohort_id));

        if (!$cohort) {
            return new WP_Error('cohort_not_found', 'Cohort not found', array('status' => 404));
        }

        return new WP_REST_Response($cohort, 200);
    }

    public function create_cohort(WP_REST_Request $request) {
        global $wpdb;
    
        $table_name = $wpdb->prefix . 'cohorts';
    
        $cohort_data = $request->get_params();
    
        $cohort_name = isset($cohort_data['c_name']) ? sanitize_text_field($cohort_data['c_name']) : '';
        $cohort_created_date = current_time('mysql');
        $cohort_end_date = isset($cohort_data['c_end_date']) ? sanitize_text_field($cohort_data['c_end_date']) : '';
        $cohort_trainer = isset($cohort_data['c_trainer']) ? sanitize_text_field($cohort_data['c_trainer']) : '';
    
        if (empty($cohort_name) || empty($cohort_end_date) || empty($cohort_trainer)) {
            return new WP_Error('invalid_data', 'Invalid cohort data', array('status' => 400));
        }
    
        $insert_result = $wpdb->insert(
            $table_name,
            array(
                'c_name' => $cohort_name,
                'c_created_date' => $cohort_created_date,
                'c_end_date' => $cohort_end_date,
                'c_trainer' => $cohort_trainer
            ),
            array('%s', '%s', '%s', '%s')
        );
    
        if (!$insert_result) {
            return new WP_Error('cohort_create_failed', 'Failed to create cohort', array('status' => 500));
        }
    
        $cohort_id = $wpdb->insert_id;
        $cohort = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE c_id = %d", $cohort_id));
    
        return new WP_REST_Response($cohort, 201);
    }
    
    

    public function update_cohort(WP_REST_Request $request) {
        global $wpdb;
    
        $cohort_id = $request->get_param('id');
        $table_name = $wpdb->prefix . 'cohorts';
    
        $cohort_data = $request->get_params();

        $cohort_name = isset($cohort_data['c_name']) ? sanitize_text_field($cohort_data['c_name']) : '';
        $cohort_end_date = isset($cohort_data['c_end_date']) ? sanitize_text_field($cohort_data['c_end_date']) : '';
        $cohort_status = isset($cohort_data['c_status']) ? intval($cohort_data['c_status']) : 0;
        $cohort_trainer_id = isset($cohort_data['c_trainer_id']) ? intval($cohort_data['c_trainer_id']) : 0;
    
        if (empty($cohort_name) || empty($cohort_end_date) || empty($cohort_trainer_id)) {
            return new WP_Error('invalid_data', 'Invalid cohort data', array('status' => 400));
        }
    
        $update_result = $wpdb->update(
            $table_name,
            array(
                'c_name' => $cohort_name,
                'c_end_date' => $cohort_end_date,
                'c_status' => $cohort_status,
                'c_trainer' => $cohort_trainer_id
            ),
            array('c_id' => $cohort_id),
            array('%s', '%s', '%d', '%d'),
            array('%d')
        );
    
        if ($update_result === false) {
            return new WP_Error('cohort_update_failed', 'Failed to update cohort', array('status' => 500));
        }
    
        $cohort = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE c_id = %d", $cohort_id));
    
        return new WP_REST_Response($cohort, 200);
    }
    

    public function delete_cohort(WP_REST_Request $request) {
        global $wpdb;

        $cohort_id = $request->get_param('id');
        $table_name = $wpdb->prefix . 'cohorts';

        $delete_result = $wpdb->delete(
            $table_name,
            array('c_id' => $cohort_id),
            array('%d')
        );

        if ($delete_result === false) {
            return new WP_Error('cohort_delete_failed', 'Failed to delete cohort', array('status' => 500));
        }

        return new WP_REST_Response(null, 204);
    }

    public function mark_cohort_as_done(WP_REST_Request $request) {
        global $wpdb;

        $cohort_id = $request->get_param('id');
        $table_name = $wpdb->prefix . 'cohorts';

        $cohort = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE c_id = %d", $cohort_id));

        if (!$cohort) {
            return new WP_Error('cohort_not_found', 'Cohort not found', array('status' => 404));
        }

        // Update the cohort status to 1 (done)
        $update_result = $wpdb->update(
            $table_name,
            array('c_status' => 1),
            array('c_id' => $cohort_id),
            array('%d'),
            array('%d')
        );

        if ($update_result === false) {
            return new WP_Error('cohort_update_failed', 'Failed to update cohort', array('status' => 500));
        }

        // Deactivate associated trainees
        $trainees_table = $wpdb->prefix . 'users';
        $trainees_result = $wpdb->update(
            $trainees_table,
            array('is_active' => false),
            array('cohort_id' => $cohort_id),
            array('%d'),
            array('%d')
        );

        if ($trainees_result === false) {
            return new WP_Error('trainees_update_failed', 'Failed to update trainees', array('status' => 500));
        }

        // Return the updated cohort
        $cohort = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE c_id = %d", $cohort_id));

        return new WP_REST_Response($cohort, 200);
    }
}
