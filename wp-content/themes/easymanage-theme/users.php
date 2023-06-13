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





     <!-- <div class="overview">
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
            </div> -->



<!-- Here is the code for dynamic projects page -->
<?php get_header(); ?>

<div class="project-summary-con">
    <div class="section-header">
        <h3>Active Projects</h3>

        <?php if (current_user_can('administrator') || current_user_can('program_manager')) : ?>
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
        $projects = array_fill(0, 1, [
            'title' => 'Plana - Event Management System',
            'progress' => '75%',
            'assigned_to' => 'John Doe, Jane Doe, John Smith',
            'due_date' => 'Jul 23',
            'status' => 'Pending',
            'category' => 'Web App',
            'tags' => 'WordPress, plugins'
        ]);

        foreach ($projects as $project) {
            // Check if the current user can access the project
            if (current_user_can('administrator') || current_user_can('program_manager') || (current_user_can('trainer') && $project['created_by'] === get_current_user_id()) || (current_user_can('trainee') && in_array(get_current_user_id(), $project['assigned_users']))) {
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
                    <span class="ps-progress">
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $project['progress'] ?>"></div>
                        </div>
                    </span>
                </div>
        <?php
            }
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
            // Check if the current user can access the project
            if (current_user_can('administrator') || current_user_can('program_manager') || (current_user_can('trainer') && $project['created_by'] === get_current_user_id()) || (current_user_can('trainee') && in_array(get_current_user_id(), $project['assigned_users']))) {
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
        <?php
            }
        }
        ?>
    </div>
</div>

<?php get_footer(); ?>
