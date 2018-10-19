<?php

/**
 * Functions for compatibility with new version of museum plugin.
 */

if ( !defined('WPM_PREFIX') ) define ('WPM_PREFIX', 'wpm_');

/**
 * Loads fancybox jquery and css. Used to display lightboxes on instrument pages. Past versions
 * used a separate plugin. Now directly loading fancybox scripts.
 *
 * @see https://cdnjs.com/libraries/fancybox
 * @see http://fancyapps.com/fancybox/3/docs/
 */
function register_fancybox() {
    wp_enqueue_script( 'fancybox-jq', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.4.1/jquery.fancybox.min.js', ['jquery'] );
    wp_enqueue_style( 'fancybox-style', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.4.1/jquery.fancybox.min.css' );
}
add_action( 'wp_enqueue_scripts', 'register_fancybox');

/**
 * Gets an array of museum object post types. This theme only designed to work
 * with the instrument post type (wpm_instrument).
 */
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