<?php


function load_css()
{
    wp_register_style('bootstrap', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css', array(), false, 'all');
    wp_enqueue_style('bootstrap');

    wp_register_style('main', get_template_directory_uri() . '/css/main.css', array(), false, 'all');
    wp_enqueue_style('main');
}
add_action('wp_enqueue_scripts', 'load_css');


function load_js()
{
    wp_enqueue_script('jquery');

    wp_register_script('bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array('jquery'), false, true);
    wp_enqueue_script('bootstrap');
}
add_action('wp_enqueue_scripts', 'load_js');

add_theme_support('custom-logo');

function is_user_in_role($user, $role)
{
    // pass the role you want to check and user object from wp_get_current_user()
    return in_array($role, $user->roles);
}

function custom_get_user_meta($user_id, $key = 'fullname')
{
    return get_user_meta($user_id, $key, true);
}

// creating my custom user roles
add_role('program-manager', 'Program Manager', array(
    'read' => true,
    'edit_posts' => true,
    'delete_posts' => true,
));

add_role('trainer', 'Trainer', array(
    'read' => true,
    'edit_posts' => true,
    'delete_posts' => false,
));

add_role('trainee', 'Trainee', array(
    'read' => true,
    'edit_posts' => false,
    'delete_posts' => false,
));

function input_short_code($attrs)
{
    $a = shortcode_atts([
        'label' => 'Name',
        'value' => '',
        'name' => '',
        'input_type' => 'text',
        'placeholder' => ''
    ], $attrs);

    return "
        <div class='input-con'>
            <label for='{$a['name']}'>{$a['label']}</label>
            <input id='{$a['name']}' name='{$a['name']}' value='{$a['value']}' type='{$a['input_type']}' placeholder='{$a['placeholder']}'/>
        </div>
    ";
}
add_shortcode('input_tag', 'input_short_code');

function login_page_shortcode($attrs)
{
    global $form_error;

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
                $form_error = $user->get_error_message();
            } else {
                // get_token($email_username, $password);
                // $GLOBALS['token'] =  get_token($email_username, $password);
            }
        }
    }

    // Set the time zone to the desired time zone
    date_default_timezone_set('Africa/Nairobi');

    // Define the greeting based on the time of day
    $current_time = date('H');
    if ($current_time >= 5 && $current_time < 12) {
        $greeting = 'Good morning';
    } elseif ($current_time >= 12 && $current_time < 17) {
        $greeting = 'Good afternoon';
    } elseif ($current_time >= 17 || $current_time < 5) {
        $greeting = 'Good evening';
    }

    $output = "";

    $output .= "
    <form action='' method='post'>
        <div class='page-login'>
        
            <div class='inner-form'>
                <h2>{$greeting},</h2>
                <p class='subtext'>Welcome back! Please enter your details</p>

                <p class='error'>$form_error</p>
               
                " . do_shortcode("[input_tag name='email_username' label='Email Address' placeholder='Enter your email address']") . "
                " . do_shortcode("[input_tag name='password' label='Password' input_type='password' placeholder='Enter your password']") . "

                <button class='custom-btn' type='submit' name='login-submit'>Login</button>
            </div>
        </div>
    </form>    
    ";

    return $output;
}
add_shortcode('login_page', 'login_page_shortcode');






function calculate_completion_percentage($arr1, $arr2)
{
    $res = "100% 0%";
    if (count($arr2) > 0) {

        $ongoing_percentage = (count($arr1) / count(array_merge($arr1, $arr2))) * 100;
        $completed_percentage = 100 - $ongoing_percentage;

        $res  = "{$ongoing_percentage}% {$completed_percentage}%";
    }
    return $res;
}