<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>
<?php

get_header();
?>
     <div class="overview">
                <p id="overview">Overview</p>
                <p>Thursday May 23, 2023</p>
            </div>

            <div class="small-cards">
                <div class="sm-card1">
                    <div class="icon">
                        icon
                    </div>
                    <div class="details">
                        <p>Total users</p><br>
                        <p>20</p>
                    </div>
                </div>

                <div class="sm-card1">
                    <div class="icon">
                        icon
                    </div>
                    <div class="details">
                        <p>Program Manager</p><br>
                        <p>20</p>
                    </div>
                </div>

                <div class="sm-card1">
                    <div class="icon">
                        icon
                    </div>
                    <div class="details">
                        <p>Total Trainers</p><br>
                        <p>20</p>
                    </div>
                </div>
            </div>


    <?php
    // show admin employees not projects
    if (is_user_in_role(wp_get_current_user(), 'administrator')) {

        $users = [
            [
                'fullname' => 'John Smith',
                'email' => 'john.smith@example.com',
                'role' => 'Program Manager'
            ],
            [
                'fullname' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'role' => 'Trainer'
            ],
            [
                'fullname' => 'Michael Johnson',
                'email' => 'michael.johnson@example.com',
                'role' => 'Trainee'
            ]
        ];


    ?>
        <div class="employees-con">
            <div class="e-header">
                <h3>Users</h3>
                <!-- <a href="<?php echo site_url('/create-employee') ?>"> <button class="custom-btn secondary"><ion-icon name='add'></ion-icon>Add Employee</button></a> -->
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
                        <div class="e-fullname"><?php echo $user['fullname'] ?></div>
                        <!-- <div class="e-email"><?php // echo $employee['email'] 
                                                    ?></div> -->
                        <div class="e-role"><?php echo $user['role'] ?></div>

                        <div class="e-options">
                            <ion-icon name='create' class="edit"></ion-icon>
                            <ion-icon name='trash' class="delete"></ion-icon>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    <?php

    } ?>

</div>
<?php get_footer() ?>

