<?php
/**
 * @package EasyManage
 */

namespace Inc\Pages;

use \Inc\Api\Callbacks\AdminCallbacks;

class CreateEmployee extends AdminCallbacks {
    public function register() {
        $this->create_table_to_db();
        $this->add_member_to_db();
    }

    function create_table_to_db() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'employees';

        $employee_details = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
            id INT AUTO_INCREMENT PRIMARY KEY,
            names VARCHAR(255) NOT NULL,
            phone INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            department VARCHAR(255) NOT NULL,
            role VARCHAR(255) NOT NULL
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($employee_details);
    }

    function add_member_to_db() {
        if (isset($_POST['submit'])) {
            $data = [
                'names' => $_POST['names'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'department' => $_POST['department'],
                'role' => $_POST['role'],
            ];
    
            global $wpdb;
            $table_name = $wpdb->prefix . 'employees';
    
            // Insert data into the table
            $result = $wpdb->insert($table_name, $data);
    
            if ($result !== false) {
                // Successful insertion
                echo '<div class="success-message">Data inserted successfully.</div>';
            } else {
                // Error during insertion
                echo '<div class="error-message">Failed to insert data.</div>';
            }
        }
    }
    
}
