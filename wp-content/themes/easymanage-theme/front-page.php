<?php
if (!is_user_logged_in()) wp_redirect(site_url('/login'));
?>

<?php
$users = get_all_users();
// var_dump($users);
$pms = array_filter($users, function ($user) {
    return  is_user_in_role($user, 'program-manager');
});
$trainers = array_filter($users, function ($trainer) {
    return  is_user_in_role($trainer, 'trainer');
});
$trainees = array_filter($users, function ($trainee) {
    return is_user_in_role($trainee, 'trainee');
});
?>


<?php
if (is_user_in_role(wp_get_current_user(), 'administrator')) {
    $projects = get_all_projects();
    $ongoing = array_filter($projects, function ($project) {
        return $project->p_status == 0;
    });
    $completed = array_filter($projects, function ($project) {
        return $project->p_status == 1;
    });
    
    // var_dump($projects);
}
if (is_user_in_role(wp_get_current_user(), 'program-manager')) {
    $projects = get_all_projects();
    $ongoing = array_filter($projects, function ($project) {
        return $project->p_status == 0;
    });
    $completed = array_filter($projects, function ($project) {
        return $project->p_status == 1;
    });
}


    // $all_tasks = get_trainee_projects(get_current_user_id());
    // $my_tasks = [];

    // foreach ($all_tasks as $all_task) {
    //     $p_tasks = get_project_tasks($all_task->p_id);
    //     $my_tasks = array_merge($my_tasks, $p_tasks);
    // }
    // var_dump($my_tasks);
    // $my_ongoing_tasks = array_filter($my_tasks, function ($task) {
    //     return $task->t_done == 0;
    // });
    // $my_completed_tasks = array_filter($my_tasks, function ($task) {
    //     return $task->t_done == 1;
    // });

?>


<?php
// foreach ($projects as $project) {
//     if (isset($project->assigned_users)) {
//         foreach ($project->assigned_users as $user) {
//             echo $user;
//         }
//         
?>
<!-- <br> -->
<?php
//     }
// }

?>


<?php
get_header();
?>

<div class="page-home">
    <?php if (current_user_can('manage_options')) { ?>
        <div class="top-cards-con" style="width: 118%; margin-left: -14%;">

            <div class="brief-info-card">
                <div class="icon">
                    <ion-icon name='people-outline'></ion-icon>
                </div>

                <div class="bi-right">
                    <p>Total Users</p>
                    <span><?php echo count($users) ?></span>
                </div>
            </div>

            <div class="brief-info-card">
                <div class="icon">
                    <ion-icon name='people-outline'></ion-icon>
                </div>

                <div class="bi-right">
                    <p>Program Managers</p>
                    <span><?php echo count($pms) ?></span>
                </div>
            </div>

            <div class="brief-info-card">
                <div class="icon">
                    <ion-icon name='people-outline'></ion-icon>
                </div>

                <div class="bi-right">
                    <p>Total Trainers</p>
                    <span><?php echo count($trainers) ?></span>
                </div>
            </div>
        </div>

        <div class="overview-flex" style="width: 118%; margin-left: -14%;">

            <div class="overview-card">
                <p class="overview-title">Trainees Overview</p>
                <p class="overview-total"><?php echo count($trainees) ?></p>
                <div class="overview-percent-con" style="grid-template-columns: 30% 70%;">
                    <div></div>
                    <div></div>
                </div>

                <div class="overview-labels">

                    <div>
                        <!-- <div class="ol-title">Wordpress</div>
                        <div class="ol-val">10</div> -->
                    </div>
                    <div>
                        <!-- <div class="ol-title">Angular</div>
                        <div class="ol-val">14</div> -->
                    </div>
                </div>
            </div>

            <div class="overview-card">
                <p class="overview-title">Projects Overview</p>
                <p class="overview-total"><?php echo count($projects) ?></p>
                <div class="overview-percent-con" style="grid-template-columns: <?php echo calculate_completion_percentage($ongoing, $completed) ?>">
                    <div></div>
                    <div></div>
                </div>

                <div class="overview-labels">

                    <div>
                        <div class="ol-title">In progress</div>
                        <div class="ol-val"><?php echo count($ongoing) ?></div>
                    </div>
                    <div>
                        <div class="ol-title">Completed</div>
                        <div class="ol-val"><?php echo count($completed) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="project-summary-con" style="margin-top: 2%; width: 118%; margin-left: -14%;">
            <div class="section-header">
                <h3>Projects Summary</h3>
                <a href="<?php echo site_url('/projects') ?>">View All</a>
            </div>

            <div class="project-summary-list">
                <div class="project-summary-h">
                    <span class="ps-name">Project Name</span>
                    <span class="ps-duedate">Due Date</span>
                    <span class="ps-status">Status</span>
                    <span class="ps-assignee">Assignee</span>
                    <span class="ps-detail">Project Detail</span>
                    <span class="ps-progress">Progress</span>
                </div>

                <?php
                if (count($projects) === 0) {
                ?>
                    <div class="project-task list-border list-empty">
                        No Projects Found
                    </div>
                <?php
                }
                ?>
                <?php
                $limitedProjects = array_slice($ongoing, 0, 3); // Extract the first three projects

                foreach ($limitedProjects as $project) {
                    $tasks = get_project_tasks($project->p_id);
                    $completed_tasks = project_completed_tasks($project->p_id);
                ?>
                    <div class="project-summary-d">
                        <a href="<?php echo site_url('/detailed-project?id=' . $project->p_id) ?>" class="ps-name"><?php echo $project->p_name ?></a>
                        <span class="ps-duedate"><?php echo style_date($project->p_due_date) ?></span>
                        <span class="ps-status"><span><?php echo $project->p_status == 0 ? "pending" : 'completed' ?></span></span>
                        <span class="ps-assignee">
                            <?php
                            if (isset($project->assigned_users)) {
                                foreach ($project->assigned_users as $assignedUser) {
                                    echo get_fullname_from_users($assignedUser, $trainees) . ', ';
                                }
                                echo rtrim(', ', ', '); // remove the trailing comma and space
                            }
                            ?>
                        </span>
                        <div class="ps-detail">
                            <span><?php echo $project->p_category ?></span>
                            <span><?php echo $project->p_excerpt ?></span>
                        </div>
                        <span class="ps-progress">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo calculate_completion_percentage_alt($completed_tasks, $tasks) ?>"></div>
                            </div>
                        </span>
                    </div>
                <?php
                }

                ?>

            </div>
        </div>
    <?php } ?>

    <?php
    if (is_user_in_role(wp_get_current_user(), 'trainer')) {
        $t_projects = get_trainers_projects(get_current_user_id());

        // var_dump($t_projects);
        $t_active = array_filter($t_projects, function ($t_project) {
            return $t_project->p_status == 0;
        });
        $t_completed = array_filter($t_projects, function ($t_project) {
            return $t_project->p_status == 1;
        });
    }
    ?>

<?php if (current_user_can('trainer')) { ?>
    <?php
    // var_dump("$t_active");
    ?>
    <div class="overview-flex" style="width: 118%; margin-left: -14%;">
        <div class="overview-card">
            <p class="overview-title">Trainees Overview</p>
            <p class="overview-total"><?php echo count($trainees); ?></p>
            <div class="overview-percent-con" style="grid-template-columns: 30% 70%;">
                <div></div>
                <div></div>
            </div>
            <div class="overview-labels">
                <div>
                    <!-- <div class="ol-title">Wordpress</div>
                    <div class="ol-val">10</div> -->
                </div>
                <div>
                    <!-- <div class="ol-title">Angular</div>
                    <div class="ol-val">14</div> -->
                </div>
            </div>
        </div>
        <div class="overview-card">
            <p class="overview-title">My Projects Overview</p>
            <p class="overview-total"><?php echo count($t_projects); ?></p>
            <div class="overview-percent-con" style="grid-template-columns: 30% 70%;">
                <div></div>
                <div></div>
            </div>
            <div class="overview-labels">
                <div>
                    <div class="ol-title">In progress</div>
                    <div class="ol-val"><?php echo count($t_active) ?></div>
                </div>
                <div>
                    <div class="ol-title">Completed</div>
                    <div class="ol-val"><?php echo count($t_completed) ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php
    // var_dump($t_projects);
    ?>
    <div class="project-summary-con" style="margin-top: 2%; width: 110%; margin-left: -8%">
        <div class="section-header">
            <h3>Projects Summary</h3>
            <a href="<?php echo site_url('/projects') ?>">View All</a>
        </div>
        <div class="project-summary-list">
            <div class="project-summary-h">
                <span class="ps-name">Project Name</span>
                <span class="ps-duedate">Due Date</span>
                <span class="ps-status">Status</span>
                <span class="ps-assignee">Assignee</span>
                <span class="ps-detail">Project Detail</span>
                <span class="ps-progress">Progress</span>
            </div>
            <?php if (empty($t_projects)) : ?>
                <div class="project-task list-border list-empty">
                    No Projects Found
                </div>
            <?php endif; ?>
            <?php
            $limitedProjects = array_slice($t_projects, 0, 2); // Extract the first two projects
            foreach ($limitedProjects as $project) {
                $tasks = get_project_tasks($project->p_id);
                $completed_tasks = project_completed_tasks($project->p_id);
            ?>
                <div class="project-summary-d">
                    <span class="ps-name"><?php echo $project->p_name ?></span>
                    <span class="ps-duedate"><?php echo style_date($project->p_due_date) ?></span>
                    <span class="ps-status"><span><?php echo $project->p_status == 0 ? "pending" : 'completed' ?></span></span>
                    <span class="ps-assignee">
                        <?php
                        if (isset($project->assigned_users)) {
                            foreach ($project->assigned_users as $assignedUser) {
                                echo get_fullname_from_users($assignedUser, $trainees) . ', ';
                            }
                            echo rtrim(', ', ', '); // remove the trailing comma and space
                        }
                        ?>
                    </span>
                    <div class="ps-detail">
                        <span><?php echo $project->p_category ?></span>
                        <span><?php echo $project->p_excerpt ?></span>
                    </div>
                    <span class="ps-progress">
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo calculate_completion_percentage_alt($completed_tasks, $tasks) ?>"></div>
                        </div>
                    </span>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
<?php } ?>

        <?php
if (current_user_can('trainee')) {
?>

    <?php
    $s_projects = get_trainee_projects(get_current_user_id());
    $s_active = get_trainee_active_project(get_current_user_id());
    $s_completed = get_trainee_completed_project(get_current_user_id());
    // echo count($s_active);
    ?>

    <div class="overview-flex" style="width: 118%; margin-left: -14%;">

        <div class="overview-card">
            <p class="overview-title">My Projects</p>
            <p class="overview-total"><?php echo count($s_projects) ?></p>
            <div class="overview-percent-con" style="grid-template-columns: <?php echo calculate_completion_percentage($s_active, $s_completed) ?>">
                <div></div>
                <div></div>
            </div>

            <div class="overview-labels">

                <div>
                    <div class="ol-title">In progress</div>
                    <div class="ol-val"><?php echo count($s_active) ?></div>
                </div>
                <div>
                    <div class="ol-title">Completed</div>
                    <div class="ol-val"><?php echo count($s_completed) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="project-summary-con" style="margin-top: 2%; width: 118%; margin-left: -14%;">
        <div class="section-header">
            <h3>Projects Summary</h3>
            <a href="<?php echo site_url('/projects') ?>">View All</a>
        </div>

        <div class="project-summary-list">
            <div class="project-summary-h">
                <span class="ps-name">Project Name</span>
                <span class="ps-duedate">Due Date</span>
                <span class="ps-status">Status</span>
                <span class="ps-assignee">Assignee</span>
                <span class="ps-detail">Project Detail</span>
                <span class="ps-progress">Progress</span>
            </div>

            <?php
            // echo count(
            if (count($s_projects) === 0) {
            ?>
                <div class="project-task list-border list-empty">
                    No Projects Found
                </div>
            <?php
            }
            ?>



<?php
                $limitedProjects = array_slice($s_active, 0, 1); // Extract the first three projects

                foreach ($limitedProjects as $project) {
                    $tasks = get_project_tasks($project->p_id);
                    $completed_tasks = project_completed_tasks($project->p_id);

                    // echo($project->p_name);
                ?>
                    <div class="project-summary-d">
                        <span class="ps-name"><?php echo $project->p_name ?></span>
                        <span class="ps-duedate"><?php echo style_date($project->p_due_date) ?></span>
                        <span class="ps-status"><span><?php echo $project->p_status == 0 ? "pending" : 'completed' ?></span></span>
                        <span class="ps-assignee">
                            <?php
                            if (isset($project->assigned_users)) {
                                foreach ($project->assigned_users as $assignedUser) {
                                    echo get_fullname_from_users($assignedUser, $trainees) . ', ';
                                }
                                echo rtrim(', ', ', '); // remove the trailing comma and space
                            }
                            ?>
                        </span>
                        <div class="ps-detail">
                            <span><?php echo $project->p_category ?></span>
                            <span><?php echo $project->p_excerpt ?></span>
                        </div>
                        <span class="ps-progress">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo calculate_completion_percentage_alt($completed_tasks, $tasks) ?>"></div>
                            </div>
                        </span>
                    </div>
            </div>
    <?php
                }
            }

    ?>
</div>
</div>
