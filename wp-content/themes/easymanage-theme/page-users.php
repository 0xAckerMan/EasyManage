<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
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

$users = get_all_users(); // Retrieve all users by default

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
        <form method="GET" action="">
            <div class="search-container">
                <input type="text" name="search" id="search-input" placeholder="Search..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit"><ion-icon name="search-outline" class="search-icon"></ion-icon></button>
            </div>
        </form>

        <?php if ($userRole == 'admin'): ?>
            <a href="<?php echo site_url('/create-program-manager') ?>"> <button class="custom-btn secondary"><ion-icon name='add'></ion-icon>Create Program Manager</button></a>
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
                        <a href="<?php echo site_url('/update-employee?id=' . $user->id) ?>"><ion-icon name='create' class="edit"></ion-icon></a>
                        <form action="" method="post">
                            <input type="hidden" name="delete-id" value="<?php echo $user->id ?>">
                            <button type="submit" name="delete-employee">
                                <ion-icon name='trash' class="delete"></ion-icon>
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
