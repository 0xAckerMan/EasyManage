<?php get_header() ?>

<div class="project-summary-con">
        <div class="section-header">
            <h3>Active Projects</h3>


                <a href="<?php echo site_url('/create-project') ?>"> <button class="custom-btn secondary"><ion-icon name='add'></ion-icon>Add Project</button></a>

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
                <!-- <span class="ps-progress">Progress</span> -->
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
                    <span class="ps-name"><?php echo $project['title'] ?></span>
                    <span class="ps-duedate"><?php echo $project['due_date'] ?></span>
                    <span class="ps-status"><span><?php echo $project['status'] ?></span></span>
                    <span class="ps-assignee"><?php echo $project['assigned_to'] ?></span>
                    <div class="ps-detail">
                        <span><?php echo $project['category'] ?></span>
                        <span><?php echo $project['tags'] ?></span>
                    </div>
                    <!-- <span class="ps-progress">
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $project['progress']  ?>"></div>
                        </div>
                    </span> -->
                </div>
            <?php } ?>
        </div>
    </div>

<?php get_footer() ?>