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
                return current_user_can('manage_options');
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

        register_rest_route('api/v1', '/projects/group/', array(
            'methods' => 'POST',
            'callback' => array($this, 'post_group_project'),
            'permission_callback' => function() {
                return current_user_can('read');
            },
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
            // 'permission_callback' => function() {
            //     return current_user_can('read');
            // }
        ));

        register_rest_route('api/v1', '/projects/trainee/(?P<id>[\d]+)/completed', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_trainee_completed_projects'),
            // 'permission_callback' => function() {
            //     return current_user_can('read');
            // }
        ));

        register_rest_route('api/v1', '/projects/trainee/(?P<id>[\d]+)/active', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_trainee_active_projects'),
            // 'permission_callback' => function() {
            //     return current_user_can('read');
            // }
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
    
        // Format the response with assigned users
        $formatted_projects = array();
        foreach ($projects as $project) {
            $project_data = (array) $project;
            $project_id = $project_data['p_id'];
            $assigned_users = $wpdb->get_col("SELECT user_id FROM {$wpdb->prefix}group_projects WHERE project_id = $project_id");
            $project_data['assigned_users'] = $assigned_users;
            $formatted_projects[] = $project_data;
        }
    
        return rest_ensure_response($formatted_projects);
    }
    
    
    
    
    
    
    // Call for the single_project route
    public function get_project($request) {
        global $wpdb;
        $projects_table_name = $wpdb->prefix . 'projects';
        $group_projects_table_name = $wpdb->prefix . 'group_projects';
    
        $project_id = absint($request['id']);
    
        $query = "SELECT p.*, g.user_id AS assigned_user_id
                  FROM $projects_table_name AS p
                  LEFT JOIN $group_projects_table_name AS g ON p.p_id = g.project_id
                  WHERE p.p_id = $project_id";
    
        $project = $wpdb->get_results($query);
    
        if (!$project) {
            return new WP_Error('project_not_found', 'Project not found', ['status' => 404]);
        }
    
        // Group the assigned users by project
        $grouped_project = array();
        foreach ($project as $row) {
            $project_id = $row->p_id;
    
            // Skip rows where assigned_user_id is null
            if ($row->assigned_user_id === null) {
                continue;
            }
    
            if (!isset($grouped_project[$project_id])) {
                $grouped_project[$project_id] = (array) $row;
                $grouped_project[$project_id]['assigned_users'] = array();
            }
    
            // Add assigned users to the assigned_users array
            $grouped_project[$project_id]['assigned_users'][] = $row->assigned_user_id;
        }
    
        // Convert the grouped project array to a simple array
        $project = array_values($grouped_project);
    
        return rest_ensure_response($project);
    }
    
    
    
    
    // Call for the post_projects route
    public function post_project($request) {
        global $wpdb;
        $projects_table_name = $wpdb->prefix . 'projects';
        $group_projects_table_name = $wpdb->prefix . 'group_projects';
    
        // Get the current user ID
        $current_user_id = get_current_user_id();
    
        // Check the number of projects allocated to the current user
        $projects_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $projects_table_name WHERE p_assigned_by = %d",
                $current_user_id
            )
        );
    
        // Check if the user has already reached the maximum allocation of 3 projects
        if ($projects_count > 4) {
            return new WP_Error('max_project_allocation_reached', 'Maximum project allocation reached', ['status' => 400]);
        }
    
        // Validate and sanitize the input data
        $p_name = sanitize_text_field($request['p_name']);
        $p_description = sanitize_textarea_field($request['p_description']);
        $p_category = sanitize_text_field($request['p_category']);
        $p_excerpt = sanitize_text_field($request['p_excerpt']);
        $p_due_date = sanitize_text_field($request['p_due_date']);
        $p_cohort_id = absint($request['p_cohort_id']);
        $assigned_users = (array) $request['p_assigned_to'];
        $assigned_users = array_slice($assigned_users, 0, 3); // Limit the number of assigned users to 3
    
        // Insert the project into the projects table
        $project_data = array(
            'p_name' => $p_name,
            'p_description' => $p_description,
            'p_category' => $p_category,
            'p_excerpt' => $p_excerpt,
            'p_assigned_by' => $current_user_id,
            'p_created_date' => current_time('mysql'),
            'p_due_date' => $p_due_date,
            'p_cohort_id' => $p_cohort_id,
        );
    
        $project_rows = $wpdb->insert($projects_table_name, $project_data);
    
        if ($project_rows == 1) {
            $project_id = $wpdb->insert_id;
    
            // Insert assigned users into the group_projects table
            foreach ($assigned_users as $assigned_user_id) {
                $wpdb->insert($group_projects_table_name, array(
                    'user_id' => $assigned_user_id,
                    'project_id' => $project_id,
                ));
            }
    
            return 'Project created successfully';
        } else {
            return new WP_Error('project_creation_failed', 'Project creation failed', ['status' => 500]);
        }
    }
    

    
    
    
    
    
    
    
    // Call for the update_projects route
    public function update_project($request) {
        $id = $request->get_param('id');
        global $wpdb;
        $projects_table_name = $wpdb->prefix . 'projects';
        $group_projects_table_name = $wpdb->prefix . 'group_projects';
    
        // Update project details in the projects table
        $project_data = array(
            'p_name' => sanitize_text_field($request->get_param('p_name')),
            'p_category' => sanitize_text_field($request->get_param('p_category')),
            'p_excerpt' => sanitize_text_field($request->get_param('p_excerpt')),
            'p_description' => sanitize_textarea_field($request->get_param('p_description')),
            'p_due_date' => sanitize_text_field($request->get_param('p_due_date')),
        );
    
        $rows_updated = $wpdb->update(
            $projects_table_name,
            $project_data,
            array('p_id' => $id)
        );
    
        if ($rows_updated === false) {
            return new WP_Error('project_update_failed', 'Project update failed', ['status' => 500]);
        }
    
        // Update assigned users in the group_projects table
        if ($request->get_param('p_assigned_to')) {
            $assigned_users = (array) $request->get_param('p_assigned_to');
            $assigned_users = array_slice($assigned_users, 0, 3); // Limit the number of assigned users to 3
    
            // Delete existing assigned users for the project
            $wpdb->delete(
                $group_projects_table_name,
                array('project_id' => $id)
            );
    
            // Insert updated assigned users into the group_projects table
            foreach ($assigned_users as $assigned_user_id) {
                $wpdb->insert(
                    $group_projects_table_name,
                    array(
                        'user_id' => $assigned_user_id,
                        'project_id' => $id,
                    )
                );
            }
        }
    
        return 'Project updated successfully';
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
        $projects_table_name = $wpdb->prefix . 'projects';
        $group_projects_table_name = $wpdb->prefix . 'group_projects';
    
        $trainer_id = absint($request['id']);
    
        $query = "SELECT p.*, g.user_id AS assigned_user_id
                  FROM $projects_table_name AS p
                  LEFT JOIN $group_projects_table_name AS g ON p.p_id = g.project_id
                  WHERE p.p_assigned_by = $trainer_id";
    
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            return new WP_Error('projects_not_found', 'No projects found', ['status' => 404]);
        }
    
        // Group the assigned users by project
        $grouped_projects = array();
        foreach ($projects as $project) {
            $project_id = $project->p_id;
    
            // Skip rows where assigned_user_id is null
            if ($project->assigned_user_id === null) {
                continue;
            }
    
            if (!isset($grouped_projects[$project_id])) {
                $grouped_projects[$project_id] = (array) $project;
                $grouped_projects[$project_id]['assigned_users'] = array();
            }
    
            // Add assigned users to the assigned_users array
            $grouped_projects[$project_id]['assigned_users'][] = $project->assigned_user_id;
        }
    
        // Convert the grouped projects array to a simple array
        $projects = array_values($grouped_projects);
    
        return rest_ensure_response($projects);
    }
    
    
    public function get_trainee_projects($request) {
        global $wpdb;
        $projects_table_name = $wpdb->prefix . 'projects';
        $group_projects_table_name = $wpdb->prefix . 'group_projects';
    
        $trainee_id = absint($request['id']);
    
        $query = "SELECT p.*, g.user_id AS assigned_user_id
                  FROM $projects_table_name AS p
                  LEFT JOIN $group_projects_table_name AS g ON p.p_id = g.project_id
                  WHERE p.p_assigned_to = $trainee_id";
    
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            return new WP_Error('no_projects_found', 'No projects found for the trainee', ['status' => 404]);
        }
    
        // Group the assigned users by project
        $grouped_projects = array();
        foreach ($projects as $project) {
            $project_id = $project->p_id;
    
            // Skip rows where assigned_user_id is null
            if ($project->assigned_user_id === null) {
                continue;
            }
    
            if (!isset($grouped_projects[$project_id])) {
                $grouped_projects[$project_id] = (array) $project;
                $grouped_projects[$project_id]['assigned_users'] = array();
            }
    
            // Add assigned users to the assigned_users array
            $grouped_projects[$project_id]['assigned_users'][] = $project->assigned_user_id;
        }
    
        // Convert the grouped projects array to a simple array
        $projects = array_values($grouped_projects);
    
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
        $projects_table_name = $wpdb->prefix . 'projects';
        $group_projects_table_name = $wpdb->prefix . 'group_projects';
    
        $trainee_id = absint($request['id']);
    
        $query = "SELECT p.*, g.user_id AS assigned_user_id
                  FROM $projects_table_name AS p
                  LEFT JOIN $group_projects_table_name AS g ON p.p_id = g.project_id
                  WHERE p.p_assigned_to = $trainee_id AND p.p_status = 1";
    
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            return new WP_Error('no_completed_projects', 'No completed projects found for the trainee', ['status' => 404]);
        }
    
        // Group the assigned users by project
        $grouped_projects = array();
        foreach ($projects as $project) {
            $project_id = $project->p_id;
    
            // Skip rows where assigned_user_id is null
            if ($project->assigned_user_id === null) {
                continue;
            }
    
            if (!isset($grouped_projects[$project_id])) {
                $grouped_projects[$project_id] = (array) $project;
                $grouped_projects[$project_id]['assigned_users'] = array();
            }
    
            // Add assigned users to the assigned_users array
            $grouped_projects[$project_id]['assigned_users'][] = $project->assigned_user_id;
        }
    
        // Convert the grouped projects array to a simple array
        $projects = array_values($grouped_projects);
    
        return $projects;
    }
    
    public function get_trainee_active_projects($request) {
        global $wpdb;
        $projects_table_name = $wpdb->prefix . 'projects';
        $group_projects_table_name = $wpdb->prefix . 'group_projects';
    
        $trainee_id = absint($request['id']);
    
        $query = "SELECT p.*, g.user_id AS assigned_user_id
                  FROM $projects_table_name AS p
                  LEFT JOIN $group_projects_table_name AS g ON p.p_id = g.project_id
                  WHERE p.p_assigned_to = $trainee_id AND p.p_status = 0";
    
        $projects = $wpdb->get_results($query);
    
        if (empty($projects)) {
            return new WP_Error('no_active_projects', 'No active projects found for the trainee', ['status' => 404]);
        }
    
        // Group the assigned users by project
        $grouped_projects = array();
        foreach ($projects as $project) {
            $project_id = $project->p_id;
    
            // Skip rows where assigned_user_id is null
            if ($project->assigned_user_id === null) {
                continue;
            }
    
            if (!isset($grouped_projects[$project_id])) {
                $grouped_projects[$project_id] = (array) $project;
                $grouped_projects[$project_id]['assigned_users'] = array();
            }
    
            // Add assigned users to the assigned_users array
            $grouped_projects[$project_id]['assigned_users'][] = $project->assigned_user_id;
        }
    
        // Convert the grouped projects array to a simple array
        $projects = array_values($grouped_projects);
    
        return $projects;
    }
    
    
    public function get_unassigned_users($request) {
        global $wpdb;
        $users_table = $wpdb->prefix . 'users';
        $usermeta_table = $wpdb->prefix . 'usermeta';
        $group_projects_table = $wpdb->prefix . 'group_projects';
        $current_user_id = get_current_user_id(); // Get the ID of the current user
    
        $query = "SELECT u.ID, u.user_nicename, u.user_email
                  FROM $users_table AS u
                  INNER JOIN $usermeta_table AS um ON u.ID = um.user_id
                  WHERE um.meta_key = 'wp_capabilities' AND um.meta_value LIKE '%trainee%'
                    AND u.ID NOT IN (
                      SELECT user_id
                      FROM $group_projects_table
                      GROUP BY user_id
                      HAVING COUNT(*) >= 3
                  )
                  AND (u.ID IN (
                      SELECT user_id
                      FROM $group_projects_table
                      WHERE project_id IN (
                          SELECT project_id
                          FROM $group_projects_table
                          WHERE user_id = $current_user_id
                      )
                  ) OR u.ID IN (
                      SELECT user_id
                      FROM $group_projects_table
                      WHERE project_id IN (
                          SELECT p_id
                          FROM {$wpdb->prefix}projects
                          WHERE p_assigned_by = $current_user_id
                      )
                  ))";
    
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
    
    
      

    public function post_group_project($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';
    
        $user_ids = $request['user_ids'];
        $project_id = $request['project_id'];
    
        foreach ($user_ids as $user_id) {
            // Check the number of projects allocated to the user
            $projects_count = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE p_assigned_to = %d",
                    $user_id
                )
            );
    
            // Check if the user has already reached the maximum allocation of 3 projects
            if ($projects_count >= 3) {
                return new WP_Error('user_project_assignment_limit_exceeded', 'User project assignment limit exceeded. A user cannot be assigned more than two projects.', ['status' => 400]);
            }
        }
    
        // Validate and sanitize the input data
        $p_name = sanitize_text_field($request['p_name']);
        $p_description = sanitize_textarea_field($request['p_description']);
        $p_category = sanitize_text_field($request['p_category']);
        $p_excerpt = sanitize_text_field($request['p_excerpt']);
        $p_due_date = sanitize_text_field($request['p_due_date']);
        $p_cohort_id = absint($request['p_cohort_id']);
    
        // Insert the project into the projects table
        $project_rows = $wpdb->insert($table_name, array(
            'p_name' => $p_name,
            'p_description' => $p_description,
            'p_category' => $p_category,
            'p_excerpt' => $p_excerpt,
            'p_assigned_by' => $project_id,
            'p_created_date' => current_time('mysql'),
            'p_due_date' => $p_due_date,
            'p_cohort_id' => $p_cohort_id,
        ));
    
        if ($project_rows == 1) {
            return 'Project created successfully';
        } else {
            return new WP_Error('project_creation_failed', 'Project creation failed', ['status' => 500]);
        }
    }
    
    
    
    

//     public function post_group_project($request) {
//         // Validate and sanitize the input data
//         $p_name = sanitize_text_field($request->get_param('p_name'));
//         $p_description = sanitize_textarea_field($request->get_param('p_description'));
//         $p_category = sanitize_text_field($request->get_param('p_category'));
//         $p_excerpt = sanitize_text_field($request->get_param('p_excerpt'));
//         $p_assigned_to = $request->get_param('p_assigned_to'); // Array of trainee IDs
//         $cohort_id = $request->get_param('cohort_id'); // Cohort ID from the user
    
//         // Validate the number of projects allocated to the trainees
//         $max_projects = 3; // Maximum number of projects per trainee
//         foreach ($p_assigned_to as $trainee_id) {
//             $trainee_projects_count = $this->get_trainee_projects_count($trainee_id);
//             if ($trainee_projects_count >= $max_projects) {
//                 return new WP_Error('max_project_allocation_reached', 'Maximum project allocation reached for one or more trainees.', array('status' => 400));
//             }
//         }
    
//         // Create the group project entry in the database
//         $project_data = array(
//             'p_name' => $p_name,
//             'p_description' => $p_description,
//             'p_category' => $p_category,
//             'p_excerpt' => $p_excerpt,
//             'p_assigned_by' => get_current_user_id(), // Assign the current user as the project creator
//             'p_created_date' => current_time('mysql'),
//             'p_due_date' => current_time('mysql'), // Set the due date as the current time for illustration purposes
//             'p_cohort_id' => $cohort_id, // Set the cohort ID from the user
//         );
    
//         global $wpdb;
//         $table_name = $wpdb->prefix . 'projects';
//         $insert_result = $wpdb->insert($table_name, $project_data);
    
//         if ($insert_result === false) {
//             return new WP_Error('project_creation_failed', 'Failed to create the group project.', array('status' => 500));
//         }
    
//         // Retrieve the generated project ID
//         $project_id = $wpdb->insert_id;
    
//         // Assign the group project to the selected trainees
//         foreach ($p_assigned_to as $trainee_id) {
//             $this->assign_project_to_trainee($project_id, $trainee_id);
//         }
    
//         return 'Group project created successfully.';
//     }
    
//     // Helper method to get the number of projects allocated to a trainee
// // Helper method to get the number of projects allocated to a trainee
// private function get_trainee_projects_count($trainee_id) {
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'projects';

//     return $wpdb->get_var(
//         $wpdb->prepare(
//             "SELECT COUNT(*) FROM $table_name WHERE p_assigned_to = %d",
//             $trainee_id
//         )
//     );
// }

// // Helper method to assign a project to a trainee
// private function assign_project_to_trainee($project_id, $trainee_id) {
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'projects';

//     $wpdb->update(
//         $table_name,
//         array('p_assigned_to' => $trainee_id),
//         array('p_id' => $project_id)
//     );
// }

    
}