<?php

/**
 * @package EasyManage
 */

/*
Plugin Name: EasyManage Plugin
Plugin URI: http://k0r3s.me
Description: My final project at the Jitu bootcamp
Version: 1.0.0
Author: Joel Kores
Author URI: http://github.com/0xAckerMan
License: GPLv2 or Later
Text Domain: easymanage-plugin
*/

//Security Check here and there
defined('ABSPATH') or die("Caught you hacker");

if(file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
}


function activate_easymanage_plugin(){
    Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_easymanage_plugin');

function deactivate_easymanage_plugin(){
    Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_easymanage_plugin');

if (class_exists( 'Inc\\Init')){
    Inc\Init::register_sevices();
}