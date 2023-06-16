<?php
/**
 *@package EasyManage
 */

 namespace Inc\Base;

 use \Inc\Base\BaseController;

class Enqueue extends BaseController{
    
    public function register(){
        add_action('admin_enqueue_scripts', array( $this, 'enqueue'));
    }
    function enqueue(){
        // Enqueue all my scripts
        wp_enqueue_style('mypluginstyle', $this->plugin_url.'assests/mystyle.css');
        wp_enqueue_script('mypluginstyle', $this->plugin_url.'assests/myscript.js');
    }
}