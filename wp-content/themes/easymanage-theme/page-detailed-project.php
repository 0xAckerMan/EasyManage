<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

/**
 * 
 * Template Name: Project Template
 */
get_header();
?>

<?php

$id = $_GET['id'];

$project = get_project($id);
$project = reset($project);

// var_dump($project);

$tasks = all_project_tasks($id);
$ongoing_tasks = array_filter($tasks, function ($tasks) {
    return $tasks->t_status == 0;
});
$completed_tasks = array_filter($tasks, function ($tasks) {
    return $tasks->t_status == 1;
});

var_dump($tasks);

if (isset($_POST['delete-project'])) {
    $res = delete_project($id);
    if (!is_wp_error($res)) {
        $form_success = 'Project deleted successfully';
    } else {
        $form_error = 'Project not deleted';
    }
}
if (isset($_POST['delete-task'])) {
    $t_id = $_POST['t_id'];
    $res = delete_task($t_id);

    if (!is_wp_error($res)) {
        $form_success = 'Task deleted successfully';
        $tasks = get_project_tasks($id);
        $ongoing_tasks = array_filter($tasks, function ($tasks) {
            return $tasks->t_done == 0;
        });
        $completed_tasks = array_filter($tasks, function ($tasks) {
            return $tasks->t_done == 1;
        });
    } else {
        $form_error = 'Task not deleted';
    }
}

if (isset($_POST['complete_project'])) {
    $res = complete_project($id);
    if (!is_wp_error($res)) {
        $form_success = 'Project completed successfully';
        $project = get_single_project($id);
        $project = reset($project);
    } else {
        $form_error = 'Project not completed';
    }
}
if (isset($_POST['complete_task'])) {
    $res = complete_task($_POST['t_id']);
    $tasks = get_project_tasks($id);
    $ongoing_tasks = array_filter($tasks, function ($tasks) {
        return $tasks->t_done == 0;
    });
    $completed_tasks = array_filter($tasks, function ($tasks) {
        return $tasks->t_done == 1;
    });
}

if (isset($_POST['uncomplete_task'])) {
    $res = uncomplete_task($_POST['t_id']);
    $tasks = get_project_tasks($id);
    $ongoing_tasks = array_filter($tasks, function ($tasks) {
        return $tasks->t_done == 0;
    });
    $completed_tasks = array_filter($tasks, function ($tasks) {
        return $tasks->t_done == 1;
    });
}

?>

<?php
$project = [
    'title' => 'Plana - Event Management System',
    'description' => 'An event management system is a comprehensive software solution designed to facilitate the planning, organization, and execution of events. It serves as a central hub that streamlines various aspects of event management, providing a range of features and functionalities to enhance efficiency and effectiveness.',
    'assigned_to' => 'John D',
    'due_date' => 'Jul 23',
    'tags' => 'WordPress, plugins',
    'category' => 'Web App'
];
$tasks = array_fill(0, 3, [
    'title' => 'Implement payment gateway integration',
    'done' => 0
    // 'due_date' => 'Jul 23',
    // 'tags' => 'WordPress, plugins',
]);
$completed_tasks = array_fill(0, 3, [
    'title' => 'Implement payment gateway integration',
    'done' => 1
    // 'due_date' => 'Jul 23',
    // 'tags' => 'WordPress, plugins',
]);

?>

<div class="page-project">

    <div class="section-header">
        <h4><?php echo $project['title'] ?></h4>
        <div class="project-options">
            <span><ion-icon name="checkmark-circle-outline"></ion-icon>
                <span>Mark As Complete</span></span>
            <a href="<?php echo site_url('/projects/update-project?id=1') ?>">
                <span class="color-blue"><ion-icon name="create"></ion-icon>
                    <span>Update</span></span>
            </a>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo 1 ?>">
                <label for="delete-task">
                    <span class="color-danger normal-text"><ion-icon name="trash-outline"></ion-icon>
                        <input type="submit" name="delete-project" value="Delete"></span>
                </label>
            </form>
        </div>
    </div>

    <p class="project-desc">
        <?php echo $project['description'] ?>
    </p>

    <div class="project-tasks-con">
        <div class="section-header header-bg">
            <h4 class="active-tasks color-success">Active Tasks</h4>

            <a href="<?php echo site_url("/projects/project/create-task?id=1") ?>"><span class="span-icon"><ion-icon name='add'></ion-icon>Add Task</span></a>
        </div>

        <?php
        if (count($tasks) === 0) {
        ?>
            <div class="project-task list-border list-empty">
                No Active Tasks
            </div>
        <?php
        }
        ?>

        <?php
        foreach ($tasks as $task) {
        ?>
            <div class="project-task list-border">
                <ion-icon name="ellipse-outline"></ion-icon>
                <p class="project-task-title"><?php echo $task['title'] ?></p>

                <div class="project-tasks-options">
                    <a href="<?php echo site_url("/projects/project/update-task?id=1") ?>">
                        <span class="span-icon color-info"><ion-icon name='create'></ion-icon><span>Update</span></span>
                    </a>
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo 1; ?>">
                        <!-- TODO: change this to actual id  -->
                        <label for="delete-task">
                            <span class="span-icon color-danger normal-text"><ion-icon name='trash'></ion-icon>
                                <input type="submit" name="delete-task" value="Delete"></span>
                        </label>
                    </form>
                </div>
            </div>
        <?php
        }
        ?>
    </div>


    <div class="project-tasks-con">
        <div class="section-header header-bg">
            <h4 class="active-tasks color-danger">Completed Tasks</h4>

            <span></span>
        </div>

        <?php
        if (count($completed_tasks) === 0) {
        ?>
            <div class="project-task list-border list-empty">
                No Completed Tasks
            </div>
        <?php
        }
        ?>

        <?php
        foreach ($completed_tasks as $task) {
        ?>
            <div class="project-task list-border">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
                <p class="project-task-title"><?php echo $task['title'] ?></p>

                <div class="project-tasks-options">
                    <a href="<?php echo site_url("/projects/project/update-task?id=1") ?>">
                        <span class="span-icon color-info"><ion-icon name='create'></ion-icon><span>Update</span></span>
                    </a>
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo 1; ?>">
                        <!-- TODO: change this to actual id  -->
                        <label for="delete-task">
                            <span class="span-icon color-danger normal-text"><ion-icon name='trash'></ion-icon>
                                <input type="submit" id="delete-task" name="delete-task" value="Delete"></span>
                        </label>
                    </form>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

</div>
</div>
</div>
</div>
