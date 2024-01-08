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
	$asset_file = include WPM_BUILD_DIR . 'blocks-edit.asset.php';
	wp_enqueue_script(
		WPM_PREFIX . 'blocks-edit',
		WPM_BUILD_URL . 'blocks-edit.js',
		$asset_file['dependencies'],
		$asset_file['version'],
		false
	);
	wp_enqueue_style(
		WPM_PREFIX . 'style-blocks-front',
		WPM_BUILD_URL . 'style-blocks-front.css',
		[],
		filemtime( WPM_BUILD_DIR . 'style-blocks-front.css' )
	);
	wp_enqueue_style(
		WPM_PREFIX . 'blocks-edit',
		WPM_BUILD_URL . 'blocks-edit.css',
		[],
		filemtime( WPM_BUILD_DIR . 'blocks-edit.css' )
	);
}

/**
 * Callback to load block scripts for frontend.
 */
function enqueue_block_frontend_scripts() {

	$asset_file = include WPM_BUILD_DIR . 'blocks-front.asset.php';
	wp_enqueue_script(
		WPM_PREFIX . 'blocks',
		WPM_BUILD_URL . 'blocks-frontend.js',
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);
	wp_enqueue_style(
		WPM_PREFIX . 'style-blocks-front',
		WPM_BUILD_URL . 'style-blocks-front.css',
		[],
		filemtime( WPM_BUILD_DIR . 'style-blocks-front.css' )
	);
	wp_enqueue_style(
		'wordpress-components-styles',
		includes_url( '/css/dist/components/style.min.css' )
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
