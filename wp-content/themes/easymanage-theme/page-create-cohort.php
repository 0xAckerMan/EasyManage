<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>



<?php
require('wp-load.php');
/**
 * 
 * Template Name: Create cohort Template
 */
get_header();
?>

<?php

$token = ($_COOKIE['token']);

global $form_error;
global $form_success;

if (isset($_POST['create-cohort-submit'])) {

    $data = [
        'fullname' => $_POST['cname'],
        'email' => $_POST['enddate'],
        // 'role' => 'program-manager',
        'password' => $_POST['ctrainer'],
    ];

    // var_dump($data);

    $res = wp_remote_post('http://localhost/EasyManage/wp-json/api/v1/cohorts', [
        'methods' => 'POST',
        'headers' => ['Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$token

    ],
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
            <h2 style="font-size: 23px;">Create Cohort</h2>

            <p class="error"><?php echo $form_error; ?></p>
            <p class="success"><?php echo $form_success; ?></p>

            <?php echo do_shortcode("[input_tag name='cname' label='Cohort Name' placeholder='Enter cohort name']") ?>
            <?php echo do_shortcode("[input_tag name='enddate' label='End Date' input_type='date' placeholder='End date']") ?>
            <?php echo do_shortcode("[input_tag name='ctrainer' label='Trainer' placeholder='Enter trainer name']") ?>

            <!-- <div class="input-con-radio">
                <label for="">Role</label>

                <div class="radios">
                    <input type="radio" name="role" id="project-manager" value="ProjectManager" required>
                    <label for="program-manager">
                        Program Manager
                    </label>
                </div>
            </div> -->

            <button class="custom-btn" type="submit" name="create-cohort-submit">Create</button>
        </div>
    </div>
</form>

<?php get_footer() ?>