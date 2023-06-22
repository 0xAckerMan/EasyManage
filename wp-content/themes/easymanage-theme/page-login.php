<?php
/**
 * Template Name: Login Template
 */

// Start output buffering
ob_start();

// Check if the user is already logged in
if (is_user_logged_in()) {
    wp_safe_redirect(home_url());
    exit;
}

get_header();

if (have_posts()) {
    while (have_posts()) : the_post();
        the_content();
    endwhile;
}




get_footer();

// Flush the output buffer
ob_flush();
?>
