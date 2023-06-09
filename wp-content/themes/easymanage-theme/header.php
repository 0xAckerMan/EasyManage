<?php if (isset($_POST['logout'])) wp_logout(); ?>

<?php
$slug = basename(get_permalink());
if (!is_user_logged_in() && $slug != 'login') {
    wp_redirect(site_url('/login'));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head() ?>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <?php
    $slug = basename(get_permalink());

    $home_routes = ['schedulr'];
    $employee_routes = ['employees', 'update-employee', 'create-employee'];
    $project_routes = ['projects', 'project', 'update-project', 'create-project', 'update-task', 'create-task'];
    ?>

    <?php
    if (is_user_logged_in()) {
    ?>
        <div class="nav">
            <div class="left-nav">
                <h1><a href="">EasyManage</a></h1>
            </div>
            <div class="right-nav">
                <a href="#">admin</a>
            </div>
        </div>
        <div class="container">
            <style>
                .right-container {
                    /* background-color: aqua; */
                    width: 100%;
                    height: 100vh;
                }
            </style>
            <div class="sidebar">
                <ul>
                    <li><a href="#">Home</a></li>
                    <li class="expandable">
                        <a href="#">Trainees</a>
                        <ul class="sub-menu">
                            <li><a href="#">Active Users</a></li>
                            <li><a href="#">Inactive Users</a></li>
                        </ul>
                    </li>
                    <li class="expandable">
                        <a href="#">Members</a>
                        <ul class="sub-menu">
                            <li><a href="#">Active Users</a></li>
                            <li><a href="#">Inactive Users</a></li>
                        </ul>
                    </li>
                    <li class="expandable">
                        <a href="#">Projects</a>
                        <ul class="sub-menu">
                            <li><a href="#">Project</a></li>
                            <li><a href="#">Update Project</a></li>
                            <li><a href="#">Create Project</a></li>
                            <li><a href="#">Update Task</a></li>
                            <li><a href="#">Create Task</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="right-container">
            <?php
        }else{
            ?>
                    <div class="nav">
            <div class="left-nav">
                <h1><a href="">EasyManage</a></h1>
            </div>
            </div>
            <?php
        }
        ?>

            <script>
                const expandableItems = document.querySelectorAll('.expandable');

                expandableItems.forEach(item => {
                    const submenu = item.querySelector('.sub-menu');
                    const arrow = item.querySelector('a::after');

                    item.addEventListener('click', () => {
                        item.classList.toggle('active');
                        submenu.style.maxHeight = item.classList.contains('active') ? submenu.scrollHeight + 'px' : '0';
                    });
                });
            </script>