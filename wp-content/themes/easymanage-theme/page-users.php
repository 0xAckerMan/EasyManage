<?php get_header(); ?>

<?php
// Define the array of sample users accessible to all roles
// $users = [
//     [
//         'fullname' => 'Eliot Alderson',
//         'email' => 'Eliot@mrrobot.com',
//         'role' => 'Program Manager'
//     ],
//     [
//         'fullname' => 'John Wick',
//         'email' => 'johnwick@localhost.com',
//         'role' => 'Trainer'
//     ],
//     [
//         'fullname' => 'Kevin Mitnick',
//         'email' => 'kevinmitnick@darkweb.com',
//         'role' => 'Trainee'
//     ]
// ];

// show admin employees not projects
// $userRole = '';
// if (current_user_can('administrator')) {
//     $userRole = 'admin';
// } elseif (current_user_can('program-manager')) {
//     $userRole = 'program_manager';
// } elseif (current_user_can('trainer')) {
//     $userRole = 'trainer';
// } elseif (current_user_can('trainee')) {
//     $userRole = 'trainee';
// }

// $users = get_all_users();


if(is_user_in_role(wp_get_current_user(), 'administrator')){
    $users = get_all_users();
    var_dump($users);
}elseif(is_user_in_role(wp_get_current_user(),'trainer')){
    $users = get_all_trainees();
    var_dump($users);
}elseif(is_user_in_role(wp_get_current_user(),'trainee')){
    $users = get_all_trainees();
    var_dump($users);
}elseif(is_user_in_role(wp_get_current_user(),'program-manager')){
    $users = get_all_users();
    var_dump($users);
}

?>
<div class="employees-con">
<div class="section-header">
    <h3>Active Users</h3>
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search...">
        <ion-icon name="search-outline" class="search-icon"></ion-icon>
    </div>
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
            <div class="e-options">
                Options
            </div>
        </div>

        <?php
        $i = 0;
        foreach ($users as $user) {
        ?>


            <div class="employee-d">
                    <div class="e-index"><?php echo ++$i; ?>.</div>
                    <div class="e-fullname"><?php echo $user->fullname ?></div>
                    <div class="e-role"><?php echo $user->roles[0] ?></div>

                    <div class="e-options">
                        <?php
                        if (current_user_can('administrator')) {
                        ?>
                            <a href="<?php echo site_url('/update-employee?id=' . $user->id) ?>"><ion-icon name='create' class="edit"></ion-icon></a>

                            <form action="" method="post">
                                <input type="hidden" name="delete-id" value="<?php echo $user->id ?>">
                                <button type="submit" name="delete-employee">
                                    <ion-icon name='trash' class="delete"></ion-icon>
                                </button>
                            </form>
                        <?php
                        }
                        ?>
                    </div>
                </div>
        <?php
        }
        ?>
    </div>
</div>

<?php get_footer(); ?>
