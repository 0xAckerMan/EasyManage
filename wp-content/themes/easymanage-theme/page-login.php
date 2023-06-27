<?php
/**
 * Template Name: Login Template
 */

 global $form_error;

// Check if user is already logged in and redirect
if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

// Process login form
if (isset($_POST['login-submit'])) {
    $email_username = $_POST['email_username'];
    $password = $_POST['password'];

    if (empty($email_username) || empty($password)) {
        $form_error = 'Please enter both email and password.';
    } else {
        $user = wp_signon([
            'user_login' => $email_username,
            'user_password' => $password
        ]);

        if (is_wp_error($user)) {
            $form_error =  $user->get_error_message();
        } else {
            // Redirect user after successful login
            wp_redirect(home_url());
            exit;
        }
    }
}
// if(isset($_POST['login-submit'])){
//     $data = [
//         'email' => $_POST['email_username'],
//         'password' => $_POST['password']
//     ];
//     var_dump($data);
// }
// Display login form
date_default_timezone_set('Africa/Nairobi');
$current_time = date('H');
if ($current_time >= 5 && $current_time < 12) {
    $greeting = 'Good morning';
} elseif ($current_time >= 12 && $current_time < 17) {
    $greeting = 'Good afternoon';
} elseif ($current_time >= 17 || $current_time < 5) {
    $greeting = 'Good evening';
}
?>

<?php
wp_head();
get_header();
?>

<form action="" method="post">
    <div class="page-login">
        <div class="inner-form">
            <h2><?php echo $greeting ?></h2>
            <p class="subtext">Welcome back! Please enter your details </p>

            <p class="error"><?php echo $form_error; ?></p>

            <?php echo do_shortcode("[input_tag name='email_username' label='Email or username' placeholder='Enter your email or username']") ?>
            <?php echo do_shortcode("[input_tag name='password' label='Password' input_type='password' placeholder='Enter your password']") ?>

            <button class="custom-btn" type="submit" name="login-submit">Login</button>
        </div>
    </div>
</form>

<?php
get_footer();
// Flush the output buffer
ob_flush();
?>
