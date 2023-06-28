<?php

global $base_api;
$base_api = 'http://localhost/EasyManage/wp-json/';

global $token;

$token = ($_COOKIE['token']);


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
    'manage_options' => true,
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

function redirect_on_logout()
{
    wp_redirect(site_url('/login'));
    exit();
}
add_action('wp_logout', 'redirect_on_logout');

function get_token($email, $password)
{
    global $base_api;

    try {
        $res = wp_remote_post($base_api . 'jwt-auth/v1/token', [
            'body' => [
                'username' => $email,
                'password' => $password
            ]
        ]);

        if (is_wp_error($res)) {
            throw new Exception($res->get_error_message());
        }

        $response_body = wp_remote_retrieve_body($res);
        $response_code = wp_remote_retrieve_response_code($res);

        if ($response_code === 200) {
            $response_data = json_decode($response_body, true);
            if (isset($response_data['token'])) {
                $token = $response_data['token'];
                return $token;
            } else {
                throw new Exception('Token not found in API response. Response: ' . $response_body);
            }
        } else {
            throw new Exception('API request failed with status code ' . $response_code);
        }
    } catch (Exception $e) {
        // Handle the exception or log the error
        echo 'Error: ' . $e->getMessage();
        return false;
    }
}


function style_date($raw_date)
{
    return date('M j', strtotime($raw_date));
}

function get_fullname_from_users($id, $users)
{
    $found = array_filter($users, function ($user) use ($id) {
        return $id == $user->ID;
    });

    if (!empty($found)) {
        return reset($found)->fullname;
    }

    return ''; // or any default value you prefer
}


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

/**
 * Create cohort metadata fields in wp_usermeta table
 */
function create_cohort_metadata_fields() {
    add_user_meta(0, 'cohort_id', '', true); // Add cohort_id field
    add_user_meta(0, 'cohort_name', '', true); // Add cohort_name field
}
register_activation_hook(__FILE__, 'create_cohort_metadata_fields');
// global $response;
// $response = get_token('admin', 'admin123');


function get_all_projects()
{
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . 'api/v1/projects', [
        'method' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]

    ]);

    if (is_wp_error($res)) {
        // Handle the error here
        return false;
    }

    $res_body = wp_remote_retrieve_body($res);
    
    return json_decode($res_body);
}


function get_project($id)
{
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . "api/v1/projects/$id", [
        'method' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]

    ]);

    if (is_wp_error($res)) {
        // Handle the error here
        return false;
    }

    $res_body = wp_remote_retrieve_body($res);
    
    return json_decode($res_body);
}

function all_project_tasks($p_id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . 'api/v1/tasks/' . $p_id, [
        'method' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
        ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_trainee_projects($trainee_id){
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . "api/v1/projects/trainee/$trainee_id",[
        'methods' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);

    $res_body = wp_remote_retrieve_body($res);

    return json_decode($res_body);
}

function get_trainee_completed_project($trainee_id) {
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . "api/v1/projects/trainee/$trainee_id/completed", [
        'methods' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);

    $res_body = wp_remote_retrieve_body($res);

    $decoded_body = json_decode($res_body);
    return ($decoded_body !== null) ? $decoded_body : [];
}


function get_trainee_active_project($trainee_id){
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . "api/v1/projects/trainee/$trainee_id/active", [
        'methods' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);

    $res_body = wp_remote_retrieve_body($res);

    return json_decode($res_body);
}

function get_trainers_projects($trainer_id){
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . "api/v1/projects/trainer/$trainer_id",[
        'methods' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]

    ]);

    $res_body = wp_remote_retrieve_body($res);

    return json_decode($res_body);
}












function get_single_project($id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . 'api/v1/projects/' . $id, [
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

// function create_project($p)
// {
// }
function update_project($p)
{
    global $token;
    global $base_api;

    $data = [
        'p_id' => $p['p_id'],
        'p_name' => $p['p_name'],
        'p_category' => $p['p_category'],
        'p_excerpt' => $p['p_excerpt'],
        'p_description' => $p['p_description'],
        'p_assigned_to' => $p['p_assigned_to'],
        'p_due_date' => $p['p_due_date'],
    ];

    $res = wp_remote_get($base_api . 'api/v1/projects/' . $p['p_id'], [
        'method' => 'PUT',
        'body' => $data,
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_project_tasks($p_id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . 'api/v1/tasks/' . $p_id, [
        'method' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function complete_project($id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . 'api/v1/projects/' . $id . "/complete", [
        'method' => 'POST',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
        'data_format' => 'body'
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function delete_project($id)
{
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . 'api/v1/projects/' . $id, [
        'method' => 'DELETE',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
        'data_format' => 'body'
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_single_task($id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . 'api/v1/tasks/single/' . $id, [
        'method' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function complete_task($id)
{
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . 'api/v1/tasks/' . $id . "/complete", [
        'method' => 'POST',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
        'data_format' => 'body'
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function uncomplete_task($id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . 'api/v1/tasks/' . $id . "/uncomplete", [
        'method' => 'POST',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
        'data_format' => 'body'
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function delete_task($id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . 'api/v1/tasks/' . $id, [
        'method' => 'DELETE',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
        'data_format' => 'body'
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function mark_task_complete($id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . "api/v1/tasks/$id/complete", [
        'method' => 'POST',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function mark_task_uncomplete($id)
{
    global $token;
    global $base_api;

    $res = wp_remote_get($base_api . "api/v1/tasks/$id/uncomplete", [
        'method' => 'POST',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ],
        ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}












// function get_all_projects()
// {
//     global $base_api;

//     $res = wp_remote_get($base_api . 'api/v1/projects', [
//         'method' => 'GET',
//         // 'headers' => ['Authorization' => 'Bearer ' . $GLOBALS['token']]
//     ]);
//     $res = wp_remote_retrieve_body($res);
//     // var_dump($res);
//     return( json_decode($res));
// }


function get_all_users(){
    global $base_api;
    global $token;
    // var_dump($token);

    $res = wp_remote_get($base_api . 'api/v1/users',[
        'method' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);

    $res_body = wp_remote_retrieve_body($res);

    return json_decode($res_body);
}

function get_all_trainees(){
    global $base_api;

    $res = wp_remote_get($base_api.'api/v1/users/trainees',[
        'methods' => 'GET'
    ]);
    $res_body = wp_remote_retrieve_body($res);

    return json_decode($res_body);
}


function get_all_unassigned()
{
    global $base_api;
    global $token;

    $res = wp_remote_get($base_api . 'api/v1/projects/unassigned', [
        'method' => 'GET',
        'headers'=>[
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ]
    ]);

    if (is_wp_error($res)) {
        // Handle the error here
        return false;
    }

    $res_body = wp_remote_retrieve_body($res);
    
    return json_decode($res_body);
}