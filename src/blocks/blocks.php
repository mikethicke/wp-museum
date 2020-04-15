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

/**
 * Callbacl to add 'Museum' block category.
 *
 * @link https://getwithgutenberg.com/2019/04/creating-a-block-category/
 *
 * @param Array $categories Array of existing categories.
 * @return Array Updated $categories array.
 */
function add_museum_block_category( $categories ) {
	$category_slugs = wp_list_pluck( $categories, 'slug' );
	if ( in_array( 'wp-museum', $category_slugs, true ) ) {
		return $categories;
	} else {
		return array_merge(
			$categories,
			[
				[
					'slug'  => 'wp-museum',
					'title' => __( 'Museum', 'wp-museum' ),
					'icon'  => null,
				],
			]
		);
	}
}



