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
		$block_path = '/build/';
	} else {
		$block_path = '';
	}

	wp_enqueue_script(
		WPM_PREFIX . 'blocks',
		plugins_url( $block_path . 'index.js', __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'index.js' ),
		true
	);
	wp_enqueue_style(
		WPM_PREFIX . 'block-style-front',
		plugins_url( $block_path . 'style.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'style.css' )
	);
	wp_enqueue_style(
		WPM_PREFIX . 'block-style-editor',
		plugins_url( $block_path . 'editor.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'editor.css' )
	);
}

/**
 * Callback to load block styles on frontend.
 */
function enqueue_block_style_frontend() {
	if ( DEV_BUILD ) {
		$block_path = '/build/';
	} else {
		$block_path = '';
	}

	wp_enqueue_style(
		WPM_PREFIX . 'block-styles',
		plugins_url( $block_path . 'style.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'style.css' )
	);
}

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_scripts' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_block_scripts' );

