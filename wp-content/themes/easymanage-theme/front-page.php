<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')); ?>

<?php
$users = get_all_users();
$pms = array_filter($users, function ($user) {
    return  is_user_in_role($user, 'program-manager');
});
$trainers = array_filter($users, function ($trainer) {
    return  is_user_in_role($trainer, 'trainer');
});
$trainees = array_filter($users, function($trainee){
    return is_user_in_role($trainee, 'trainee');
})

?>


<?php
if (is_user_in_role(wp_get_current_user(), 'administrator')) {
    $projects = get_all_projects();
    $ongoing = array_filter($projects, function ($project) {
        return $project->p_done == 0;
    });
    $completed = array_filter($projects, function ($project) {
        return $project->p_done == 1;
    });
}
?>

<pre>
<?php
// var_dump( get
// var_dump($projects);
?>
</pre>

<?php
get_header();
?>

<div class="page-home">
    <?php if (current_user_can('manage_options')) : ?>
        <div class="top-cards-con" style="width: 118%; margin-left: -14%;">

            <div class="brief-info-card">
                <div class="icon">
                    <ion-icon name='people-outline'></ion-icon>
                </div>

                <div class="bi-right">
                    <p>Total Users</p>
                    <span><?php echo count ($users) ?></span>
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
                        <div class="ol-title">Wordpress</div>
                        <div class="ol-val">10</div>
                    </div>
                    <div>
                        <div class="ol-title">Angular</div>
                        <div class="ol-val">14</div>
                    </div>
                </div>
            </div>

            <div class="overview-card">
                <p class="overview-title">Projects Overview</p>
                <p class="overview-total"><?php echo count($projects)?></p>
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
                    if (count($projects) === 0){
                        ?>
                        <div class="project-task list-border list-empty">
                            No Projects Found
                        </div>
                    <?php
                    }
                    ?>
                <?php
                foreach ($projects as $project) {
                ?>
                    <div class="project-summary-d">
                        <span class="ps-name"><?php echo $project->p_name ?></span>
                        <span class="ps-duedate"><?php echo style_date($project->p_due_date) ?></span>
                        <span class="ps-status"><span><?php echo $project->p_status == 0 ? "pending" : 'completed' ?></span></span>
                        <span class="ps-assignee"><?php echo get_fullname_from_users($project->p_assigned_to, $trainees) ?></span>
                        <div class="ps-detail">
                            <span><?php echo $project->p_category ?></span>
                            <span><?php echo $project->p_excerpt ?></span>
                        </div>
                        <span class="ps-progress">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $project['progress']  ?>"></div>
                            </div>
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
        

    <?php elseif (current_user_can('edit_posts')) : ?>
        <div class="overview-flex" style="width: 118%; margin-left: -14%;">

            <div class="overview-card">
                <p class="overview-title">Trainees Overview</p>
                <p class="overview-total">24</p>
                <div class="overview-percent-con" style="grid-template-columns: 30% 70%;">
                    <div></div>
                    <div></div>
                </div>

                <div class="overview-labels">

                    <div>
                        <div class="ol-title">Wordpress</div>
                        <div class="ol-val">10</div>
                    </div>
                    <div>
                        <div class="ol-title">Angular</div>
                        <div class="ol-val">14</div>
                    </div>
                </div>
            </div>

            <div class="overview-card">
                <p class="overview-title">Projects Overview</p>
                <p class="overview-total">24</p>
                <div class="overview-percent-con" style="grid-template-columns: 30% 70%;">
                    <div></div>
                    <div></div>
                </div>

                <div class="overview-labels">

                    <div>
                        <div class="ol-title">In progress</div>
                        <div class="ol-val">10</div>
                    </div>
                    <div>
                        <div class="ol-title">Completed</div>
                        <div class="ol-val">14</div>
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
                $projects = array_fill(0, 3, [
                    'title' => 'Plana - Event Management System',
                    'progress' => '75%',
                    'assigned_to' => 'John D',
                    'due_date' => 'Jul 23',
                    'status' => 'Pending',
                    'category' => 'Web App',
                    'tags' => 'WordPress, plugins'
                ]);

                foreach ($projects as $project) {
                ?>
                    <div class="project-summary-d">
                        <span class="ps-name"><?php echo $project['title'] ?></span>
                        <span class="ps-duedate"><?php echo $project['due_date'] ?></span>
                        <span class="ps-status"><span><?php echo $project['status'] ?></span></span>
                        <span class="ps-assignee"><?php echo $project['assigned_to'] ?></span>
                        <div class="ps-detail">
                            <span><?php echo $project['category'] ?></span>
                            <span><?php echo $project['tags'] ?></span>
                        </div>
                        <span class="ps-progress">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $project['progress']  ?>"></div>
                            </div>
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php elseif (current_user_can('read')) : ?>
        <?php
        $projects = get_trainee_projects(get_current_user_id());
        $completed = get_trainee_completed_project(get_current_user_id());
        $active = get_trainee_active_project(get_current_user_id());
        var_dump($completed);
        ?>
        <div class="overview-flex" style="width: 118%; margin-left: -14%;">

            <!-- <div class="overview-card">
                <p class="overview-title">Trainees Overview</p>
                <p class="overview-total">24</p>
                <div class="overview-percent-con" style="grid-template-columns: 30% 70%;">
                    <div></div>
                    <div></div>
                </div>

                <div class="overview-labels">

                    <div>
                        <div class="ol-title">Wordpress</div>
                        <div class="ol-val">10</div>
                    </div>
                    <div>
                        <div class="ol-title">Angular</div>
                        <div class="ol-val">14</div>
                    </div>
                </div>
            </div> -->

            <div class="overview-card">
                <p class="overview-title">My Projects</p>
                <p class="overview-total"><?php echo count($projects) ?></p>
                <div class="overview-percent-con" style="grid-template-columns: 30% 70%;">
                    <div></div>
                    <div></div>
                </div>

                <div class="overview-labels">

                    <div>
                        <div class="ol-title">In progress</div>
                        <div class="ol-val"><?php echo count($active) ?></div>
                    </div>
                    <div>
                        <div class="ol-title">Completed</div>
                        <div class="ol-val"><?php echo '' ?></div>
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
                    if (count($projects) === 0){
                        ?>
                        <div class="project-task list-border list-empty">
                            No Projects Found
                        </div>
                    <?php
                    }
                    ?>

                <?php
                // $projects = array_fill(0, 3, [
                //     'title' => 'Plana - Event Management System',
                //     'progress' => '75%',
                //     'assigned_to' => 'John D',
                //     'due_date' => 'Jul 23',
                //     'status' => 'Pending',
                //     'category' => 'Web App',
                //     'tags' => 'WordPress, plugins'
                // ]);

                foreach ($projects as $project) {
                ?>
                    <div class="project-summary-d">
                        <span class="ps-name"><?php echo $project->p_name ?></span>
                        <span class="ps-duedate"><?php echo style_date($project->p_due_date) ?></span>
                        <span class="ps-status"><span><?php echo $project->p_status == 0 ? "pending" : 'completed' ?></span></span>
                        <span class="ps-assignee"><?php echo get_fullname_from_users($project->p_assigned_to, $trainees) ?></span>
                        <div class="ps-detail">
                            <span><?php echo $project->p_category ?></span>
                            <span><?php echo $project->p_excerpt ?></span>
                        </div>
                        <span class="ps-progress">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $project['progress']  ?>"></div>
                            </div>
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php endif; ?>
</div>


<?php get_footer(); ?>