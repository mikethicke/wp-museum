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
		$style_path = '/build/style.css';
	} else {
		$block_path = 'index.js';
		$style_path = 'style.css';
	}
	wp_enqueue_script(
		WPM_PREFIX . 'blocks',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path ),
		true
	);
	$a = WPM_PREFIX . 'block-styles';
	$b = plugins_url( $style_path, __FILE__ );
	$c = filemtime( plugin_dir_path( __FILE__ ) . $style_path );
	wp_enqueue_style(
		WPM_PREFIX . 'block-styles',
		plugins_url( $style_path, __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $style_path )
	);
}

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_scripts' );
