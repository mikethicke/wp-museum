<?php
/*
Plugin Name: Museum Database (2017)
Description: Manages a database of scientific instruments
Version: 1.0
Author: Mike Thicke
Author URI: http://www.mikethicke.com
*/

if ( defined('WP_DEBUG') && WP_DEBUG == true) {
    add_action( 'init', 'stop_heartbeat', 1 );
    function stop_heartbeat() {
        wp_deregister_script('heartbeat');
    }   
}

if ( !defined('WPM_PREFIX') ) define ('WPM_PREFIX', 'wpm_');

// Update CSS in Admin
function admin_style() {
  wp_enqueue_style('admin-styles', plugin_dir_url( __FILE__ ).'admin-style.css');
  wp_enqueue_style('admin-fancybox', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" );
}
add_action('admin_enqueue_scripts', 'admin_style');

//enqueue javascript
$wpm_javascript_dir = plugin_dir_url( __FILE__ ).'javascript/';
function wpm_enqueue_javascript() {
    global $wpm_javascript_dir;
    wp_enqueue_script('jquery-wp-uploader', $wpm_javascript_dir.'jquery-wp-uploader.js');
    wp_enqueue_script('wpm-admin-js', $wpm_javascript_dir.'admin.js');
}
add_action('admin_enqueue_scripts', 'wpm_enqueue_javascript');

//Remove capabilities upon plugin deactivation
register_deactivation_hook( __FILE__, 'remove_museum_capabilities' );

require_once ( 'database_functions.php' );
require_once ( 'capabilities.php' );
require_once ( 'exhibit.php' );
require_once ( 'object_admin.php' );
require_once ( 'object_functions.php' );
require_once ( 'object_post_types.php' );
require_once ( 'object_ajax.php' );
require_once ( 'collection_functions.php');
require_once ( 'collection_post_type.php' );
require_once ( 'quick_browse.php' );

//Migrates old image attachment system to new
add_action( 'admin_init', 'fix_object_image_attachments' );
