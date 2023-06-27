<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php get_header(); ?>

<?php
// $trainees = array();
$users = get_all_users();
$pms = array_filter($users, function ($user) {
    return  is_user_in_role($user, 'program-manager');
});
$trainers = array_filter($users, function ($trainer) {
    return  is_user_in_role($trainer, 'trainer');
});
$trainees = array_filter($users, function ($trainee) {
    return is_user_in_role($trainee, 'trainee');
});

// $projects = [];
if (is_user_in_role(wp_get_current_user(), 'administrator')) {
    $projects = get_all_projects();
} else if (is_user_in_role(wp_get_current_user(), 'ProjectManager')) {
    $projects = get_trainers_projects(get_current_user_id());
} else {
    $projects = get_trainee_projects(get_current_user_id());
}

// $projects = get_all_projects();
$ongoing = array_filter($projects, function ($project) {
    return $project->p_status == 0;
});
$completed = array_filter($projects, function ($project) {
    return $project->p_status == 1;
});
?>

<pre>
    <?php
    // var_dump($projects);
    ?>
</pre>

<div class="project-summary-con">
    <div class="section-header">
        <h3>Active Projects</h3>

        <?php if (current_user_can('trainer')) : ?>
            <a href="<?php echo site_url('/create-project') ?>"><button class="custom-btn secondary"><ion-icon name='add'></ion-icon>Add Project</button></a>
        <?php endif; ?>

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
foreach ($projects as $project) {
?>
    <div class="project-summary-d">
        <a href="<?php echo site_url('/detailed-project?id='.$project->p_id) ?>" class="ps-name"><?php echo $project->p_name ?></a>
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
                <div class="progress-bar" style="width: <?php echo $project->progress ?>"></div>
            </div>
        </span>
    </div>
<?php
}
?>
    </div>
</div>

<div class="project-summary-con">
    <div class="section-header" style="margin-top: 2%;">
        <h3>Completed Projects</h3>
    </div>

    <div class="project-summary-list">
        <div class="project-summary-h">
            <span class="ps-name">Project Name</span>
            <span class="ps-duedate">Due Date</span>
            <span class="ps-status">Status</span>
            <span class="ps-assignee">Assignee</span>
            <span class="ps-detail">Project Detail</span>
        </div>

        <?php
        $projects = array_fill(0, 6, [
            'title' => 'Plana - Event Management System',
            'progress' => '75%',
            'assigned_to' => 'John D',
            'due_date' => 'Jul 23',
            'status' => 'completed',
            'category' => 'Web App',
            'tags' => 'WordPress, plugins'
        ]);

        foreach ($projects as $project) {
        ?>
            <div class="project-summary-d">
                <a href="<?php echo site_url('/detailed-project?id=1') ?>" class="ps-name"><?php echo $project['title'] ?></a>
                <span class="ps-duedate"><?php echo $project['due_date'] ?></span>
                <span class="ps-status"><span><?php echo $project['status'] ?></span></span>
                <span class="ps-assignee"><?php echo $project['assigned_to'] ?></span>
                <div class="ps-detail">
                    <span><?php echo $project['category'] ?></span>
                    <span><?php echo $project['tags'] ?></span>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php get_footer(); ?>
