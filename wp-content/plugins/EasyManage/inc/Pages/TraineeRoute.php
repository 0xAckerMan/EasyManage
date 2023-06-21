<?php

namespace Inc\Pages;

use WP_Error;

class TraineeRoute{
    public function register(){
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        // Endpoint for getting trainees
        register_rest_route('api/v1', 'users/trainees', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_trainees'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));


    }



    public function get_trainees()
    {
        $trainees = get_users([
            'role'         => 'trainee',
            'meta_key'     => 'is_active',
            'meta_value'   => true,
            'meta_compare' => '=',
        ]);

        if (empty($trainees)) {
            return new WP_Error('no_trainees_found', 'No trainees found', ['status' => 404]);
        }

        $modified_trainees = array_map(function ($trainee) {
            $cohort = get_user_meta($trainee->ID, 'cohort', true);

            return [
                'ID'       => $trainee->ID,
                'id'       => $trainee->ID,
                'fullname' => $trainee->user_nicename,
                'email'    => $trainee->user_email,
                'roles'    => $trainee->roles,
                'cohort'   => $cohort
            ];
        }, $trainees);

        return $modified_trainees;
    }


}