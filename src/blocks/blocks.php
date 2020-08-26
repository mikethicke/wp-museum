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
		plugins_url( $block_path . 'style-frontend.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'style-frontend.css' )
	);
	wp_enqueue_style(
		WPM_PREFIX . 'block-style-editor',
		plugins_url( $block_path . 'index.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'index.css' )
	);
}

/**
 * Callback to load block scripts for frontend.
 */
function enqueue_block_frontend_scripts() {
	if ( DEV_BUILD ) {
		$block_path = '/build/';
	} else {
		$block_path = '';
	}

	wp_enqueue_script(
		WPM_PREFIX . 'blocks',
		plugins_url( $block_path . 'frontend.js', __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'frontend.js' ),
		true
	);
	wp_enqueue_style(
		WPM_PREFIX . 'block-style-front',
		plugins_url( $block_path . 'style-frontend.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path . 'style-frontend.css' )
	);
}

/**
 * Callback to add 'Museum' block category.
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

/**
 * Enqueues javascript to de-register Object Meta Fields and Object Image
 * Gallery blocks for non Museum Objects, becaue they don't make sense for
 * other post types.
 */
function unregister_object_blocks_for_non_objects() {
	global $post;
	if ( ! in_array( $post->post_type, get_object_type_names() ) ) {
		wp_enqueue_script(
			WPM_PREFIX . 'unregister-object-blocks',
			WPM_BASE_URL . 'javascript/unregister-object-blocks.js',
			array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
			filemtime( plugin_dir_path( __FILE__ ) . '../javascript/unregister-object-blocks.js' ),
			true
		);
	}
}



