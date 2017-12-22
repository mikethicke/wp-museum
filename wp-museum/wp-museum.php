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

include_once ( 'exhibit.php' );

?>