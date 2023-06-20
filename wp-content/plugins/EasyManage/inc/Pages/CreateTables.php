<?php
/**
 * @package EasyManage
 */

namespace Inc\Pages;


class CreateTables {
    public function register() {
        $this->create_table_projects();
        $this->create_table_tasks();
        $this->create_table_cohorts();
    }

    function create_table_projects() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'projects';

        $project_details = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
            p_id INT AUTO_INCREMENT PRIMARY KEY,
            p_name VARCHAR(255) NOT NULL,
            p_description TEXT NOT NULL,
            p_category VARCHAR(255) NOT NULL,
            p_exerpt VARCHAR(255) NOT NULL,
            p_status INT DEFAULT 0 NOT NULL,
            p_created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            p_end_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            p_assigned_to mediumint(9) NOT NULL,
            p_assigned_by mediumint(9) NOT NULL,
            p_cohort_id INT NOT NULL
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($project_details);
    }

    function create_table_tasks(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'tasks';

        $task_details = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
            t_id INT AUTO_INCREMENT PRIMARY KEY,
            t_name VARCHAR(255) NOT NULL,
            t_status INT DEFAULT 0 NOT NULL,
            t_due_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            t_project_id INT NOT NULL
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($task_details);
    }

    function create_table_cohorts(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'cohorts';

        $cohort_details = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
            c_id INT AUTO_INCREMENT PRIMARY KEY,
            c_name VARCHAR(255) NOT NULL,
            c_created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            c_end_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            c_status INT DEFAULT 0 NOT NULL,
            c_trainer VARCHAR(255) NOT NULL
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($cohort_details);
    }
}
