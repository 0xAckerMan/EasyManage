<?php
/**
 * package EasyManage
 */

namespace Inc\Pages;

use WP_Error;

class ProjectsRoutes{

    public function register(){
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('api/v1', '/projects/', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_projects'),
            'permission_callback' => function() {
                // return $this->is_user_in_role($request->get_user(), 'ProjectManager');
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_project'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/', array(
            'methods' => 'POST',
            'callback' => array($this, 'post_project'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/(?P<id>[\d]+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_project'),
            'permission_callback' => function() {
                // return $this->is_user_in_role($request->get_user(), 'ProjectManager');
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/(?P<id>[\d]+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_project'),
            'permission_callback' => function() {
                // return $this->is_user_in_role($request->get_user(), 'ProjectManager');
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/trainer/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_trainer_projects'),
            'permission_callback' => function ($request) {
                $user = get_user_by('ID', $request['id']);
                if (!$user || !in_array('trainer', $user->roles)) {
                    return new WP_Error('rest_forbidden', 'Sorry, you are not allowed to do that.', array('status' => 403));
                }
                return true;
            }
        ));



        register_rest_route('api/v1', '/projects/trainee/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_trainee_projects'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/trainee/(?P<id>[\d]+)/completed', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_trainee_completed_projects'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/(?P<id>[\d]+)/complete', array(
            'methods' => 'POST',
            'callback' => array($this, 'complete_project'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

        register_rest_route('api/v1', '/projects/unassigned/', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_unassigned_users'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));

    }
    public function get_projects($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
        $query = "SELECT * FROM $table_name";
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            return new WP_Error('projects_not_found', 'No projects found', ['status' => 404]);
        }
    
        return rest_ensure_response($projects);
    }
    
    
    
    
    // Call for the single_project route
    public function get_project($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
        $query = "SELECT * FROM $table_name WHERE p_id = $request[id]";
        $project = $wpdb->get_results($query);
    
        if (!$project) {
            return new WP_Error('project_not_found', 'Project not found', ['status' => 404]);
        }
    
        return $project;
    }
    
    
    // Call for the post_projects route
    public function post_project($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
    
        // Get the current user ID
        $current_user_id = get_current_user_id();
    
        // Check the number of projects allocated to the current user
        $projects_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE p_assigned_to = %d",
                $current_user_id
            )
        );
    
        // Check if the user has already reached the maximum allocation of 3 projects
        if ($projects_count >= 3) {
            return new WP_Error('max_project_allocation_reached', 'Maximum project allocation reached', ['status' => 400]);
        }
    
        $rows = $wpdb->insert($table_name, array(
            'p_name' => $request['p_name'],
            'p_description' => $request['p_description'],
            'p_category' => $request['p_category'],
            'p_excerpt' => $request['p_excerpt'],
            'p_assigned_to' => $request['p_assigned_to'],
            'p_assigned_by' => $current_user_id, // Assign the current user ID as the assigned by
            'p_created_date' => current_time('mysql'),
            'p_due_date' => $request['p_due_date'],
            'p_cohort_id' => $request['p_cohort_id'], // Updated column name
        ));
    
        if ($rows == 1) {
            return 'Project created successfully';
        } else {
            return new WP_Error('project_creation_failed', 'Project creation failed', ['status' => 500]);
        }
    }
    
    
    
    
    // Call for the update_projects route
    public function update_project($request) {
        $id = $request['p_id'];
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
        $rows = $wpdb->update(
            $table_name,
            array(
                'p_name' => $request['p_name'],
                'p_category' => $request['p_category'],
                'p_excerpt' => $request['p_excerpt'],
                'p_description' => $request['p_description'],
                'p_assigned_to' => $request['p_assigned_to'],
                'p_due_date' => $request['p_due_date'],
            ),
            array('p_id' => $id)
        );
    
        if ($rows === false) {
            return new WP_Error('project_update_failed', 'Project update failed', ['status' => 500]);
        } else {
            return 'Project updated successfully';
        }
    }
    
    
    // Call for the delete_projects route
    public function delete_project($request) {
        $id = $request['id'];
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
        $rows = $wpdb->delete($table_name, array('p_id' => $id));
    
        if ($rows === false) {
            return new WP_Error('project_deletion_failed', 'Project deletion failed', ['status' => 500]);
        } else {
            return 'Project deleted successfully';
        }
    }
    
    //trainer projects
    public function get_trainer_projects($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE p_assigned_by = %d", $request['id']);
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            return new WP_Error('projects_not_found', 'No projects found', array('status' => 404));
        }
    
        return rest_ensure_response($projects);
    }
    
    public function get_trainee_projects($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
        $query = "SELECT * FROM $table_name WHERE p_assigned_to = $request[id] AND p_status = 0";
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            $error = new WP_Error('no_projects_found', 'No projects found for the trainee', ['status' => 404]);
            return $error;
        }
    
        return rest_ensure_response($projects);
    }
    
    
    public function complete_project($request) {
        $id = $request['id'];
        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $project_table = $wpdb->prefix . 'projects';
    
        // Update all tasks associated with the project
        $rows = $wpdb->update($tasks_table, array(
            't_status' => 1
        ), array(
            't_project_id' => $id
        ));
    
        if ($rows === false) {
            return new WP_Error('task_completion_failed', 'Task completion failed', ['status' => 500]);
        }
    
        // Update the project as well
        $rows = $wpdb->update($project_table, array(
            'p_status' => 1
        ), array(
            'p_id' => $id
        ));
    
        if ($rows === false) {
            return new WP_Error('project_completion_failed', 'Project completion failed', ['status' => 500]);
        } else {
            return 'Project completed successfully';
        }
    }
    public function get_trainee_completed_projects($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
        $query = "SELECT * FROM $table_name WHERE p_assigned_to = $request[id] AND p_status = 1";
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            return new WP_Error('no_completed_projects', 'No completed projects found for the trainee', ['status' => 404]);
        }
    
        return $projects;
    }
    
    public function get_unassigned_users($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'users';
        $query = "SELECT ID, user_nicename, user_email FROM $table_name WHERE ID NOT IN (SELECT p_assigned_to FROM wp_projects)";
        $users = $wpdb->get_results($query);
    
        if (empty($users)) {
            return new WP_Error('no_unassigned_users', 'No unassigned users found', ['status' => 404]);
        }
    
        $modified_users = array_map(function($user){
            $user->fullname = $user->user_nicename;
            $user->email = $user->user_email;
            $user->id = $user->ID;
            
            unset($user->user_nicename);
            unset($user->user_email);
            unset($user->ID);
            return $user;
        }, $users);
    
        return $modified_users;
    }    
    
}