<?php get_header() ?>

<?php
    // show admin employees not projects
    if (is_user_in_role(wp_get_current_user(), 'administrator')) {

        $users = [
            [
                'fullname' => 'Eliot Alderson',
                'email' => 'Eliot@mrrobot.com',
                'role' => 'Program Manager'
            ],
            [
                'fullname' => 'John Wick',
                'email' => 'johnwick@localhost.com',
                'role' => 'Trainer'
            ],
            [
                'fullname' => 'Kevin Mitnick',
                'email' => 'kevinmitnick@darkweb.com',
                'role' => 'Trainee'
            ]
        ];


    ?>
        <div class="employees-con">
            <div class="e-header">
                <h3>Active Users</h3>
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