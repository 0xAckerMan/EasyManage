<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>
<?php

get_header();
?>
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



    <div class="page-home">
    <div class="top-cards-con">

        <div class="brief-info-card">
            <div class="icon">
                <ion-icon name='people-outline'></ion-icon>
            </div>

            <div class="bi-right">
                <p>Total Users</p>
                <span>22</span>
            </div>
        </div>
        <div class="brief-info-card">
            <div class="icon">
                <ion-icon name='people-outline'></ion-icon>
            </div>

            <div class="bi-right">
                <p>Program Managers</p>
                <span>1</span>
            </div>
        </div>
        <div class="brief-info-card">
            <div class="icon">
                <ion-icon name='people-outline'></ion-icon>
            </div>

            <div class="bi-right">
                <p>Total Trainers</p>
                <span>2</span>
            </div>
        </div>
    </div>
    <div class="overview-flex">

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
    </div>
            <div class="project-summary-con" style="margin-top: 2%;">
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

</div>
<?php get_footer() ?>

