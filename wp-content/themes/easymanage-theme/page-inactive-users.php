<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

if (isset($_POST['delete-employee'])) {
    $id = $_POST['delete-id'];

    $res = deactivate_user($id); // Call the deactivate_user() function defined in the backend

    if (!is_wp_error($res)) {
        $form_success = 'User deactivated successfully';
        $users = get_all_users();
        // ...
    } else {
        $form_error = "Error deactivating user";
    }
}




// show admin employees not projects
$userRole = '';

if (current_user_can('administrator')) {
    $userRole = 'admin';
} elseif (current_user_can('program-manager')) {
    $userRole = 'program_manager';
} elseif (current_user_can('trainer')) {
    $userRole = 'trainer';
} elseif (current_user_can('trainee')) {
    $userRole = 'trainee';
}

$users = get_all_inactive(); // Retrieve all users by default

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $searched = search_all_users($search);
} else {
    $searched = $users; // Use all users as the default search result
}


?>

<?php get_header(); ?>

<div class="employees-con">
    <div class="section-header">
        <h3>Active Users</h3>


        <?php if ($userRole == 'admin'): ?>
            <a href="<?php echo site_url('/users') ?>"> <button class="custom-btn secondary"><ion-icon name='play-skip-back-outline'></ion-icon>Back to Users</button></a>

        <?php elseif ($userRole == 'program_manager'): ?>
            <a href="<?php echo site_url('/create-trainer') ?>"> <button class="custom-btn secondary"><ion-icon name='add'></ion-icon>Create Trainer</button></a>
        <?php elseif ($userRole == 'trainer'): ?>
            <a href="<?php echo site_url('/create-trainee') ?>"> <button class="custom-btn secondary"><ion-icon name='add'></ion-icon>Add Trainee</button></a>
        <?php endif; ?>
    </div>

    <div class="e-list">
        <div class="employee-h">
            <div class="e-index">No.</div>
            <div class="e-fullname">Fullname</div>
            <div class="e-role">Role</div>
            <?php if (current_user_can('administrator')) : ?>
                <div class="e-options">
                    Options
                </div>
            <?php endif; ?>
        </div>

        <?php
        $i = 0;
        foreach ($searched as $user) {
            ?>
            <div class="employee-d">
                <div class="e-index"><?php echo ++$i; ?>.</div>
                <div class="e-fullname"><?php echo $user->fullname ?></div>
                <div class="e-role"><?php echo $user->roles[0] ?></div>
                <?php if (current_user_can('administrator')) : ?>
                    <div class="e-options">
                        <!-- <a href="<?php echo site_url('/update-user?id=' . $user->id) ?>"><ion-icon name='create' class="edit"></ion-icon></a> -->
                        <form action="" method="post">
                            <input type="hidden" name="delete-id" value="<?php echo $user->id ?>">
                            <button type="submit" name="delete-employee">
                            <ion-icon name="play-outline" class="delete"></ion-icon>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<?php get_footer(); ?>
