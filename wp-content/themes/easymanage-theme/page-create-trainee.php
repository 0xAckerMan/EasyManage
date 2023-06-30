<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>



<?php
require('wp-load.php');
/**
 * 
 * Template Name: Create Employee Template
 */
get_header();
?>

<?php
$cohorts = get_all_cohorts();
$cname = $cohorts[0]->c_name;

// var_dump($all_name);
// var_dump($cname);
// echo $all_name;

global $form_error;
global $form_success;

if (isset($_POST['create-trainee-submit'])) {

    $data = [
        'fullname' => $_POST['fullname'],
        'email' => $_POST['email'],
        // 'role' => 'program-manager',
        'password' => $_POST['password'],
        'cohort' => $_POST['cohort'],
    ];

    // var_dump($data);

    $res = wp_remote_post('http://localhost/EasyManage/wp-json/api/v1/users/trainees', [
        'methods' => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($data),
        'data_format' => 'body'
    ]);
    $res = wp_remote_retrieve_body($res);

    // var_dump($res);


    if (!is_wp_error($res)) {
        $form_success = $res;
    }
}

?>


<form action="" method="post">
    <div style="padding: 10px 50px;width:60%; color:dodgerblue" class="span-icon"><ion-icon name='arrow-back'></ion-icon><a href="<?php echo site_url('/users') ?>"> Back to users</a></div>
    <div class="page-create-employee">
        <div class="inner-form" >
            <h2 style="font-size: 23px;">Create Trainee</h2>

            <p class="error"><?php echo $form_error; ?></p>
            <p class="success"><?php echo $form_success; ?></p>

            <?php echo do_shortcode("[input_tag name='fullname' label='Fullname' placeholder='Enter their fullname']") ?>
            <?php echo do_shortcode("[input_tag name='email' label='Email Address' input_type='email' placeholder='Enter their email address']") ?>
            <?php echo do_shortcode("[input_tag name='password' label='Password' input_type='password' placeholder='Enter their password']") ?>
            <?php echo $cname; ?>
            <?php echo do_shortcode("[input_tag name='cohort' label='Cohort' placeholder='Assign to cohort']") ?>






            <button class="custom-btn" type="submit" name="create-trainee-submit">Create</button>
        </div>
    </div>
</form>

<?php get_footer() ?>