<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

/**
 * 
 * Template Name: Create Project Template
 */
get_header();
?>

<?php
global $form_error;

global $token;

$cohorts = get_all_cohorts();
$cname = $cohorts[0]->c_name;

$unassigned = get_all_unassigned();
// var_dump($unassigned);

if (isset($_POST['create-project'])) {
    require('wp-load.php');
    $data = [
        'p_name' => $_POST['p_name'],
        'p_category' => $_POST['p_category'],
        'p_excerpt' => $_POST['p_excerpt'],
        'p_description' => $_POST['p_description'],
        'p_due_date' => $_POST['p_due_date'],
        'p_assigned_cohort' => $_POST['p_assigned_cohort'],
        'p_assigned_to' => $_POST['assignees']
    ];
    ?>
    <pre>
        <?php
        // var_dump($data);
        ?>
    </pre>
    <?php

    $res = wp_remote_post('http://localhost/EasyManage/wp-json/api/v1/projects/', [
        'methods' => 'POST',
        'headers' => ['Authorization' => 'Bearer '.$token, 'Content-Type' => 'application/json'],
        'body' => json_encode($data),
        'data_format' => 'body'
    ]);
    $res = json_decode(wp_remote_retrieve_body($res));
    $res = json_decode($res);


    if (!is_wp_error($res)) {
        $form_success = 'Project created successfully';
    } else {
        $form_error = 'Project not created';
    }
}

?>

<form action="" method="post" style="margin-top: -10px; width:105%">
    <input type="hidden" name="p_id" value="<?php echo $project_id ?>">
    <div class="page-create-project">
        <div class="inner-form">
            <h2>Create Project</h2>
            <p class="error"><?php echo $form_error; ?></p>

            <?php echo do_shortcode("[input_tag name='p_name' label='Project Name' placeholder='Enter the project name']") ?>
            <?php echo do_shortcode("[input_tag name='p_category' label='Project Category' placeholder='e.g. Mobile App, Web App']") ?>
            <?php echo do_shortcode("[input_tag name='p_excerpt' label='Project excerpt' placeholder='e.g. Ecommerce, Marketing']") ?>

            <div class="input-con">
                <label for="p_description">Project Description</label>
                <textarea name="p_description" id="p_description" placeholder="Briefly explain this project"></textarea>
            </div>

            <?php echo do_shortcode("[input_tag name='p_due_date' input_type='date' label='Project Due Date']") ?>

            <div class="input-con">
                <label for="p_assigned_cohort">Assign Cohort</label>
                <select name="p_assigned_cohort" id="p_assigned_cohort">
                    <option value="" selected disabled hidden>Assign to cohort</option>
                    <?php foreach ($cohorts as $user) : ?>

                        <option value="<?php echo $user->c_id ?>"><?php echo $user->c_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-con">
                <label for="p_assigned_trainees">Assign Project To Trainees</label>
                <select name="assignees[]" id="p_assigned_trainees" multiple>
                    <option value="" selected disabled hidden>Assign to trainees</option>
                    <?php foreach ($unassigned as $user) : ?>
                        <option value="<?php echo $user->id ?>"><?php echo $user->email ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-con">
                <label>Assigned Trainees (Max 3)</label>
                <select id="assigned_users" name="assignees[]" multiple size="3" readonly></select>
            </div>

            <button class="custom-btn" type="submit" name="create-project">Create</button>
        </div>
    </div>
</form>

<script>
    // Track selected assignees and update the assigned users field
    var assignedUsers = [];

    document.getElementById('p_assigned_trainees').addEventListener('change', function(event) {
        var select = event.target;
        var selectedOptions = Array.from(select.selectedOptions);

        // Add selected users to the assignedUsers array
        selectedOptions.forEach(function(option) {
            if (!assignedUsers.includes(option.textContent) && assignedUsers.length < 3) {
                assignedUsers.push(option.textContent);
            }
        });

        // Update assigned users field
        document.getElementById('assigned_users').innerHTML = '';

        assignedUsers.forEach(function(user) {
            var option = document.createElement('option');
            option.value = user;
            option.text = user;
            document.getElementById('assigned_users').appendChild(option);
        });
    });
</script>

<?php get_footer() ?>
