<?php
/**
 * Enqueues javascript and styles for blocks and admin react apps.
 *
 * @package MikeThicke\MuseumRemote
 */

namespace MikeThicke\MuseumRemote;

/**
 * Enqueues scripts and styles for admin.
 */
function enqueue_admin_scripts_and_styles() {
	wp_enqueue_script(
		'museum-remote-react',
		plugins_url( MR_REACT_PATH . 'index.js', __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . MR_REACT_PATH . 'index.js' ),
		true
	);
	wp_enqueue_style(
		'wordpress-components-styles',
		includes_url( '/css/dist/components/style.min.css' ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . MR_REACT_PATH . 'index.css' )
	);
	wp_enqueue_style(
		'museum-remote-style-admin',
		plugins_url( MR_REACT_PATH . 'index.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . MR_REACT_PATH . 'index.css' )
	);
	wp_enqueue_style(
		'museum-remote-style-front',
		plugins_url( MR_REACT_PATH . 'style-index.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . MR_REACT_PATH . 'style-index.css' )
	);
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_scripts_and_styles' );

/**
 * Enqueues scripts and styles for frontend.
 */
function enqueue_frontend_styles() {
	wp_enqueue_style(
		'museum-remote-style-front',
		plugins_url( MR_REACT_PATH . 'style-index.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . MR_REACT_PATH . 'style-index.css' )
	);
	wp_enqueue_style(
		'wordpress-components-styles',
		includes_url( '/css/dist/components/style.min.css' ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . MR_REACT_PATH . 'index.css' )
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_frontend_styles' );


