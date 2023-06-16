<?php
/**
 *@package WK7-ASSESSMENT
 */

 namespace Inc\Pages;

 use \Inc\Api\SettingsApi;
 use \Inc\Base\BaseController;
 use \Inc\Api\Callbacks\AdminCallbacks;

class Admin extends BaseController{
    public $settings;

    public $callbacks;

    public $pages = array();

    public $subpages = array();

    

    //My Api for creating admin pages in seconds
    public function register(){
        $this-> callbacks = new AdminCallbacks();

        $this->settings = new SettingsApi();
        $this->setPages();
        $this->setSubPages();

        $this->settings->addPages($this->pages)->withSubPage('create Employee')-> addSubPages($this->subpages) ->register();
    }

    public function setPages(){
        $this->pages = array(
            [
            'page_title' => 'Employer Plugin',
            'menu_title' => 'Manage Employees',
            'capability' => 'manage_options',
            'menu_slug' => 'employees',
            'callback' => array($this->callbacks, 'adminCreate'),
            'icon_url' => 'dashicons-groups',
            'position' => 110
            ],
        );

    }

    public function setSubPages(){
        $this->subpages = array(
            array(
                'parent_slug' => 'employees',
                'page_title' => 'View Employees',
                'menu_title' => 'View Employees',
                'capability' => 'manage_options',
                'menu_slug' => 'employee',
                'callback' => array( $this->callbacks, 'adminEmployee' ),
            ),

        );
    }
}
