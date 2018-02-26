<?php

if ( !defined('WPM_PREFIX') ) define ('WPM_PREFIX', 'wpm_');

function register_fancybox() {
    wp_enqueue_script( 'fancybox-jq', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js', ['jquery'] );
}
add_action( 'wp_enqueue_scripts', 'register_fancybox');

function get_wpm_post_types() {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    $results = $wpdb->get_results( "SELECT * FROM $table_name");
    
    $post_types = [];
    foreach ( $results as $object_type ) {
        $type_name = WPM_PREFIX . $object_type->name;
        if ( strlen( $type_name ) > 20 ) $type_name = substr( $type_name, 0, 19 );
        $post_types[] = $type_name;
    }
    return $post_types;
}