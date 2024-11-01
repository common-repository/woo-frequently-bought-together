<?php
/**
*Plugin Name: Woo Frequently Bought Together
*Description: This plugin allows you to Create Combo Prducts.
* Version: 1.0
* Author: Ocean Infotech
* Author URI: https://www.xeeshop.com
* Copyright: 2019 
*/

if (!defined('ABSPATH')) {
    die('-1');
}
if (!defined('OCCP_PLUGIN_NAME')) {
    define('OCCP_PLUGIN_NAME', 'Combo Products');
}
if (!defined('OCCP_PLUGIN_VERSION')) {
    define('OCCP_PLUGIN_VERSION', '1.0.0');
}
if (!defined('OCCP_PLUGIN_FILE')) {
    define('OCCP_PLUGIN_FILE', __FILE__);
}
if (!defined('OCCP_PLUGIN_DIR')) {
    define('OCCP_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('OCCP_DOMAIN')) {
    define('OCCP_DOMAIN', 'occp');
}



if (!class_exists('OCCPMAIN')) {

  class OCCPMAIN {

    protected static $instance;

    function includes() {
      include_once('admin/occp-backend.php');
      include_once('front/occp-front.php');
    }

    function init() {
      add_action('admin_enqueue_scripts', array($this, 'OCCP_load_admin_script_style'));
      add_action( 'wp_enqueue_scripts',  array($this, 'OCCP_load_script_style'));
    }

	
    function OCCP_load_admin_script_style() {
        wp_enqueue_style( 'OCCP_back-css', OCCP_PLUGIN_DIR . '/includes/css/OCCP_back.css', false, '1.0.0' );
        $screen = get_current_screen();
        if($screen->id == 'product') {
          wp_enqueue_script('backend-jsaa', OCCP_PLUGIN_DIR .'/includes/js/OCCP_backend.js', array( 'jquery', 'select2'));
          wp_localize_script( 'ajaxloadpost', 'ajax_postajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }
    }

    
    function OCCP_load_script_style() {
      wp_enqueue_style( 'OCCP_front-css', OCCP_PLUGIN_DIR . '/includes/css/OCCP_front.css', false, '1.0.0' );
      wp_enqueue_script( 'OCCP_front-js', OCCP_PLUGIN_DIR . '/includes/js/OCCP_front.js', false, '1.0.0' );
    }

    

    //Plugin Rating
    public static function do_activation() {
      set_transient('occp-first-rating', true, MONTH_IN_SECONDS);
    }

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
        self::$instance->includes();
      }
      return self::$instance;
    }

  }

  add_action('plugins_loaded', array('OCCPMAIN', 'instance'));
  register_activation_hook(OCCP_PLUGIN_FILE, array('OCCPMAIN', 'do_activation'));
}
