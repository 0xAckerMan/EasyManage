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
    <style>
        .right-container {
            width: 100%;
            height: 100%;
        }

        .sidebar {
            justify-content: space-between;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <?php
    $slug = basename(get_permalink());

    $home_routes = ['EasyManage', 'home', 'login', 'register'];
    $employee_routes = ['employees', 'update-employee', 'create-employee'];
    $project_routes = ['projects', 'project', 'update-project', 'create-project', 'update-task', 'create-task'];
    ?>

    <?php if (is_user_logged_in()) : ?>
        <div class="nav">
            <div class="left-nav">
                <h1><a href="<?php echo site_url(); ?>">Easy<span>Manage</span></a></h1>
            </div>
            <div class="right-nav">
                <ion-icon name="person-outline"></ion-icon>
                <?php
                $name = custom_get_user_meta(get_current_user_id());
                echo $name != '' ? $name : get_userdata(get_current_user_id())->user_login;
                ?>
            </div>
        </div>
        <div class="container">
            <div class="sidebar">
                <ul>
                    <li<?php echo in_array($slug, $home_routes) ? ' class="active"' : ''; ?>>
                        <a href="<?php echo site_url(); ?>">
                            <ion-icon name="home-outline"></ion-icon>
                            Home
                        </a>
                    </li>

                    <li class="expandable<?php echo in_array($slug, $project_routes) ? ' active' : ''; ?>">
                        <a href="<?php echo site_url('/projects'); ?>">
                            <ion-icon name="folder-outline"></ion-icon>
                            Projects
                        </a>
                        <ul class="sub-menu">
                            <?php if (current_user_can('trainer')) : ?>
                                <li>
                                    <a href="<?php echo site_url('/create-project'); ?>">
                                        <ion-icon name="add-outline"></ion-icon>
                                        Create Project
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="expandable<?php echo in_array($slug, $employee_routes) ? ' active' : ''; ?>">
                        <a href="<?php echo site_url('/users'); ?>">
                            <ion-icon name="people-outline"></ion-icon>
                            Members
                        </a>
                        <ul class="sub-menu">
                            <li>
                                <a href="<?php echo site_url('/users'); ?>">
                                    <ion-icon name="person-circle-outline"></ion-icon>
                                    Active Users
                                </a>
                            </li>
                            <?php if (current_user_can('trainer') || current_user_can('program_manager') || current_user_can('administrator')) : ?>
                                <li>
                                    <a href="<?php echo site_url('/users'); ?>">
                                        <ion-icon name="person-circle-outline"></ion-icon>
                                        Inactive Users
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                </ul>

                <a href="<?php echo wp_logout_url(); ?>" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <ion-icon name="log-out-outline"></ion-icon>
    Logout
</a>

<form id="logout-form" action="<?php echo wp_logout_url(home_url()); ?>" method="POST" style="display: none;">
    <?php wp_nonce_field('logout', 'logout-nonce'); ?>
</form>



            </div>

            <div class="right-container">
                <?php else : ?>
                    <div class="nav">
                        <div class="left-nav">
                            <h1><a href="<?php echo site_url(); ?>">Easy<span>Manage</span></a></h1>
                        </div>
                    </div>
                <?php endif; ?>

                <script>
                    const expandableItems = document.querySelectorAll('.expandable');
                    const activeItem = document.querySelector('.active');

                    expandableItems.forEach(item => {
                        const submenu = item.querySelector('.sub-menu');
                        const arrow = item.querySelector('a::after');

                        item.addEventListener('click', () => {
                            item.classList.toggle('active');
                            submenu.style.maxHeight = item.classList.contains('active') ? submenu.scrollHeight + 'px' : '0';
                        });
                    });

                    if (activeItem) {
                        const submenu = activeItem.querySelector('.sub-menu');
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    }
                </script>
