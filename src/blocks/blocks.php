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
	wp_enqueue_script(
		WPM_PREFIX . 'blocks',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path ),
		true
	);
	wp_enqueue_style( 
		WPM_PREFIX . 'block-styles',
		plugins_url( 'editor-style.css', __FILE__ ),
		[ 'wp-blocks', 'wp-components' ],
		filemtime( plugin_dir_path( __FILE__ ) . 'editor-style.css' )
	);
}

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_scripts' );
