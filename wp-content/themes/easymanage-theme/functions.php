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

