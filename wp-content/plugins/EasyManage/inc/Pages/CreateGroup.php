<?php
/**
 * @package EasyManage
 */

namespace Inc\Pages;
use WP_Error;


class CreateGroup{
    public function register(){
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes(){
        // Endpoint for creating groups
        register_rest_route('api/v1', 'groups', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_group_project_user'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        // Endpoint for getting groups
        register_rest_route('api/v1', 'groups/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_groups'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        // Endpoint for updating groups
        register_rest_route('api/v1', 'groups/(?P<id>[\d]+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_group'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

        // Endpoint for deleting groups
        register_rest_route('api/v1', 'groups/(?P<id>[\d]+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_group_project_user'),
            'permission_callback' => function ($request) {
                return current_user_can('read');
            }
        ));
        

        // Endpoint for getting group by id
        register_rest_route('api/v1', 'groups/(?P<id>[\d]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_group_by_id'),
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ));

    }

    public function create_group_project_user($request) {
        // Validate and sanitize the input data
        $group_name = sanitize_text_field($request->get_param('group_name'));
        $user_ids = $request->get_param('user_ids');
        $project_id = intval($request->get_param('project_id'));
    
        // Validate input
        if (empty($group_name) || empty($user_ids) || empty($project_id)) {
            return new WP_Error('invalid_group_project_user_data', 'Invalid group project user data.', array('status' => 400));
        }
    
        // Ensure user IDs is an array
        if (!is_array($user_ids)) {
            return new WP_Error('invalid_user_ids', 'User IDs must be an array.', array('status' => 400));
        }
    
        // Check if users exist and are trainees
        foreach ($user_ids as $user_id) {
            $user = get_userdata($user_id);
            if (!$user || !in_array('trainee', $user->roles)) {
                return new WP_Error('invalid_user_ids', 'Invalid user IDs. All users must be existing trainees.', array('status' => 400));
            }
        }
    
        // Check if the user has already been assigned to two projects
        foreach ($user_ids as $user_id) {
            $assigned_projects = $this->get_user_assigned_projects($user_id);
            if (count($assigned_projects) >= 2) {
                return new WP_Error('user_project_assignment_limit_exceeded', 'User project assignment limit exceeded. A user cannot be assigned more than two projects.', array('status' => 400));
            }
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'group_projects';
    
        // Check if the project exists
        $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}projects WHERE p_id = %d", $project_id));
        if (!$project) {
            return new WP_Error('invalid_project_id', 'Invalid project ID. The project does not exist.', array('status' => 400));
        }
    
        // Check if the project is already assigned to three users
        $project_users_count = $this->get_project_users_count($project_id);
        if ($project_users_count >= 3) {
            return new WP_Error('project_user_assignment_limit_exceeded', 'Project user assignment limit exceeded. A project cannot be assigned to more than three users.', array('status' => 400));
        }
    
        // Create the group project users in the group_project table
        foreach ($user_ids as $user_id) {
            $group_project_user_data = array(
                'group_name' => $group_name,
                'user_id' => $user_id,
                'project_id' => $project_id,
            );
    
            $group_project_user_data_types = array(
                '%s', // group_name
                '%d', // user_id
                '%d', // project_id
            );
    
            $group_project_user_created = $wpdb->insert($table_name, $group_project_user_data, $group_project_user_data_types);
    
            if (!$group_project_user_created) {
                return new WP_Error('group_project_user_creation_failed', 'Group project user creation failed', array('status' => 500));
            }
        }
    
        // Update the p_group_id field in the projects table
        $wpdb->update(
            "{$wpdb->prefix}projects",
            array('p_group_id' => $project_id), 
            array('p_id' => $project_id), 
            array('%d'),
            array('%d') 
        );
    
        // Return success message
        return 'Group project users created successfully';
    }
    
    

    // Helper functions

    
    public function get_user_assigned_projects($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'group_projects';
    
        $assigned_projects = $wpdb->get_col($wpdb->prepare("SELECT project_id FROM $table_name WHERE user_id = %d", $user_id));
    
        return $assigned_projects;
    }
    
    public function get_project($project_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
    
        $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE p_id = %d", $project_id));
    
        return $project;
    }
    
    public function get_project_users_count($project_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'group_projects';
    
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE project_id = %d", $project_id));
    
        return intval($count);
    }
    
    
    public function get_group_project_users() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'group_projects';
    
        $group_project_users = $wpdb->get_results("SELECT * FROM $table_name");
    
        return $group_project_users;
    }
    
    public function update_group_project_user($request) {
        $group_project_user_id = $request->get_param('id');
        $group_name = intval($request->get_param('group_name'));
        $user_id = intval($request->get_param('user_id'));
        $project_id = intval($request->get_param('project_id'));
    
        // Validate input
        if (empty($group_project_user_id) || empty($group_name) || empty($user_id) || empty($project_id)) {
            return new WP_Error('invalid_group_project_user_data', 'Invalid group project user data.', array('status' => 400));
        }
    
        // Check if the group project user exists
        $group_project_user = $this->get_group_project_user_by_id($group_project_user_id);
        if (!$group_project_user) {
            return new WP_Error('group_project_user_not_found', 'Group project user not found', array('status' => 404));
        }
    
        // Check if the user ID is an existing trainee
        $user = get_userdata($user_id);
        if (!$user || !in_array('trainee', $user->roles)) {
            return new WP_Error('invalid_user_id', 'Invalid user ID. User must be an existing trainee.', array('status' => 400));
        }
    
        // Check if the user has already been assigned to two projects
        $assigned_projects = $this->get_user_assigned_projects($user_id);
        if (count($assigned_projects) >= 2 && !in_array($project_id, $assigned_projects)) {
            return new WP_Error('user_project_assignment_limit_exceeded', 'User project assignment limit exceeded. A user cannot be assigned more than two projects.', array('status' => 400));
        }
    
        // Check if the project exists
        $project = $this->get_project($project_id);
        if (!$project) {
            return new WP_Error('invalid_project_id', 'Invalid project ID.', array('status' => 400));
        }
    
        // Check if the project is already assigned to three users
        $project_users_count = $this->get_project_users_count($project_id);
        if ($project_users_count >= 3 && !in_array($user_id, $group_project_user->user_ids)) {
            return new WP_Error('project_user_assignment_limit_exceeded', 'Project user assignment limit exceeded. A project cannot be assigned to more than three users.', array('status' => 400));
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'group_projects';
    
        $group_project_user_updated = $wpdb->update(
            $table_name,
            array(
                'group_name' => $group_name,
                'user_id' => $user_id,
                'project_id' => $project_id,
            ),
            array('id' => $group_project_user_id),
            array('%s', '%d', '%d'),
            array('%d')
        );
    
        if ($group_project_user_updated) {
            return 'Group project user updated successfully';
        } else {
            return new WP_Error('group_project_user_update_failed', 'Group project user update failed', array('status' => 500));
        }
    }

    // public function delete_created_group($group_id) {
    //     global $wpdb;
    //     $group_project_table = $wpdb->prefix . 'group_projects';
    //     $project_table = $wpdb->prefix . 'projects';
    
    //     // Delete group project users from the group_projects table
    //     $wpdb->delete(
    //         $group_project_table,
    //         array('group_id' => $group_id),
    //         array('%d')
    //     );
    
    //     // Update p_group_id field in the projects table to null
    //     $wpdb->update(
    //         $project_table,
    //         array('p_group_id' => null),
    //         array('p_group_id' => $group_id),
    //         array('%s'),
    //         array('%d')
    //     );
    // }
    
    
    
    public function delete_group_project_user($request) {
        $group_id = $request->get_param('group_id');
        
        // Validate input
        if (empty($group_id)) {
            return new WP_Error('invalid_group_id', 'Invalid group ID.', array('status' => 400));
        }
        
        global $wpdb;
        $group_project_table = $wpdb->prefix . 'group_projects';
        $projects_table = $wpdb->prefix . 'projects';
        
        // Get the project ID and group name
        $group_project = $wpdb->get_row($wpdb->prepare("SELECT * FROM $group_project_table WHERE group_id = %d", $group_id));
        
        if (!$group_project) {
            return new WP_Error('group_project_not_found', 'Group project not found', array('status' => 404));
        }
        
        $project_id = $group_project->project_id;
        $group_name = $group_project->group_name;
        
        // Delete the group project users
        $group_project_users_deleted = $wpdb->delete(
            $group_project_table,
            array('group_id' => $group_id),
            array('%d')
        );
        
        if ($group_project_users_deleted === false) {
            return new WP_Error('group_project_user_delete_failed', 'Group project user delete failed', array('status' => 500));
        }
        
        // Update the p_group_id field in the projects table
        $wpdb->update(
            $projects_table,
            array('p_group_id' => null),
            array('p_id' => $project_id),
            array('%s'),
            array('%d')
        );
        
        // Return success message
        return 'Group project and users deleted successfully';
    }
    
    
    
    
    
    
    public function get_group_project_user_by_id($request) {
        $group_project_user_id = $request->get_param('id');
    
        // Validate input
        if (empty($group_project_user_id)) {
            return new WP_Error('invalid_group_project_user_id', 'Invalid group project user ID.', array('status' => 400));
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'group_projects';
    
        $group_project_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $group_project_user_id));
    
        if ($group_project_user) {
            return $group_project_user;
        } else {
            return new WP_Error('group_project_user_not_found', 'Group project user not found', array('status' => 404));
        }
    }
    
    // Helper functions
    
    // public function get_user_assigned_projects($user_id) {
    //     global $wpdb;
    //     $table_name = $wpdb->prefix . 'group_projects';
    
    //     $assigned_projects = $wpdb->get_col($wpdb->prepare("SELECT project_id FROM $table_name WHERE user_id = %d", $user_id));
    
    //     return $assigned_projects;
    // }
    
    // public function get_project($project_id) {
    //     global $wpdb;
    //     $table_name = $wpdb->prefix . 'projects';
    
    //     $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $project_id));
    
    //     return $project;
    // }
    
    // public function get_project_users_count($project_id) {
    //     global $wpdb;
    //     $table_name = $wpdb->prefix . 'group_projects';
    
    //     $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE project_id = %d", $project_id));
    
    //     return intval($count);
    // }
    
    

}