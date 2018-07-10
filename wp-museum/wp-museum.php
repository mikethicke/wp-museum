<?php
/*
Plugin Name: Museum Database (2017)
Description: Manages a database of scientific instruments
Version: 0.1
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
$wpm_javascript_dir = plugin_dir_url( __FILE__ ).'javascript/';


// Update CSS in Admin
function admin_style() {
  wp_enqueue_style('admin-styles', plugin_dir_url( __FILE__ ).'admin-style.css');
  wp_enqueue_style('admin-fancybox', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" );
}
add_action('admin_enqueue_scripts', 'admin_style');

include_once ( 'database.php' );
include_once ( 'capabilities.php' );
include_once ( 'exhibit.php' );
include_once ( 'object_admin.php' );
include_once ( 'object_post_types.php' );
include_once ( 'collection_post_type.php' );
include_once ( 'quick_browse.php' );