<?php
/**
 * Enqueue block scripts and css.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Callback to load block scripts.
 */
function enqueue_block_scripts() {
	if ( DEV_BUILD ) {
		$block_path = '/build/index.js';
	} else {
		$block_path = 'index.js';
	}
	$file = __FILE__;
	wp_enqueue_script(
		WPM_PREFIX . 'blocks',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path ),
		true
	);
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_block_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_block_scripts' );
