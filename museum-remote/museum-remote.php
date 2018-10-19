<?php
/*
Plugin Name: Remote Museum Client (2018)
Description: Allows display of museum objects from an external Wordpress site.
Version: 0.1
Author: Mike Thicke
Author URI: http://www.mikethicke.com
*/

require_once ( 'options-page.php' );
require_once ( 'WPRestSite.php' );
require_once ( 'RemoteCollection.php');
require_once ( 'RemoteObject.php' );
require_once ( 'shortcodes.php' );

function rm_add_style() {
    wp_enqueue_style(
        'wprm-style',
        plugin_dir_url( __FILE__ ) . 'style.css'
    );
    
    $options = get_option ('rm_options');
    if ( isset( $options['style'] ) ) {
        wp_add_inline_style( 'wprm-style', $options['style'] );
    }
}
add_action( 'wp_enqueue_scripts', 'rm_add_style' );

?>