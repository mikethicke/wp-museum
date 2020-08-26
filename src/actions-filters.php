<?php
/**
 * All actions and filters should go here, except those that are part of classes
 * (@see class-customposttype.php for example) or where it would cause unnessessary
 * complication (@see collection-post-type.php).
 *
 * @link https://developer.wordpress.org/reference/functions/add_action/
 * @link https://developer.wordpress.org/reference/functions/add_filter/
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference Actions
 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference Filters
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/*****************************************************************************
 *
 * Global Actions
 *
 *****************************************************************************/

// Stop auto refresh of pages when debugging.
if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
	add_action(
		'init',
		function () {
			wp_deregister_script( 'heartbeat' );
		},
		1
	);
	add_action(
		'init',
		function() {
			flush_rewrite_rules();
		}
	);
}

/**
 * Flush rewrite rules on plugin activation.
 */
register_activation_hook(
	__FILE__,
	function () {
		add_action(
			'init',
			function() {
				flush_rewrite_rules();
			}
		);
	}
);

/**
 * Remove capabilities upon plugin deactivation (cleans up db).
 *
 * @see capabilities.php::remove_museum_capabilities()
 */
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\remove_museum_capabilities' );

/**
 * Creates and registers museum object post types from database.
 *
 * @see object-post-types.php::create_mobject_post_types()
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\create_mobject_post_types' );

/**
 * Check database version and update table schemas if necessary
 *
 * @see database-functions.php::db_version_check()
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\db_version_check' );

/**
 * Generate image sizes for post row thumbnails.
 *
 * @see display.php::generat_image_sizes()
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\generate_image_sizes' );

/**
 * Register REST routes.
 *
 * @see rest.php::rest_routes()
 */
add_action( 'rest_api_init', __NAMESPACE__ . '\rest_routes' );


/*****************************************************************************
 *
 * Global Filters
 *
 *****************************************************************************/

/**
 * Allows for targetted searches of post_title and post_content.
 *
 * @see custom-post-type-functions.php::post_search_filter()
 */
add_filter( 'posts_where', __NAMESPACE__ . '\post_search_filter', 10, 2 );

/**
 * Add post_title and post_content to WP_QUERY query vars.
 *
 * @see custom-post-type-functions.php::add_title_content_query_vars()
 * @see custom-post-type-functions.php::post_search_filter()
 */
add_filter( 'query_vars', __NAMESPACE__ . '\add_title_content_query_vars' );

/*****************************************************************************
 *
 * Admin Actions
 *
 *****************************************************************************/

/**
 * Update CSS in Admin.
 */
add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'admin-styles',
			plugin_dir_url( __FILE__ ) . 'admin/admin-style.css',
			[],
			CSS_VERSION
		);
		wp_enqueue_style(
			'admin-fancybox',
			'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css',
			[],
			CSS_VERSION
		);
	}
);

/**
 * Enqueue javascript for admin pages.
 */
$wpm_javascript_dir = plugin_dir_url( __FILE__ ) . 'javascript/';
add_action(
	'admin_enqueue_scripts',
	function () use ( $wpm_javascript_dir ) {
		wp_enqueue_script(
			'jquery-wp-uploader',
			$wpm_javascript_dir . 'jquery-wp-uploader.js',
			[],
			SCRIPT_VERSION,
			true
		);
		wp_enqueue_script(
			'wpm-admin-js',
			$wpm_javascript_dir . 'admin.js',
			[],
			SCRIPT_VERSION,
			true
		);
		// For nonce verification.
		wp_localize_script(
			'wpm-admin-js',
			'admin_ajax_data',
			[ 'nonce' => wp_create_nonce( 'kcDbrTMMfFqh6jy8&LrCGoH7p' ) ]
		);
		wp_localize_script(
			'jquery-wp-uploader',
			'wp_uploader_data',
			[ 'nonce' => wp_create_nonce( 'kcDbrTMMfFqh6jy8&LrCGoH7p' ) ]
		);
	}
);

/**
 * Enqueue javascript for uploading images when editing musuem objects.
 *
 * @see object-ajax.php::wpm_media_box_enqueue
 */
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\wpm_media_box_enqueue' );

/**
 * Add appropriate capabilities for museum object post types.
 *
 * @see capabilities.php::add_museum_capabilities()
 */
add_action( 'admin_init', __NAMESPACE__ . '\add_museum_capabilities' );

/**
 * Creates the admin page and adds it to the Settings menu.
 *
 * @see object-admin.php::add_object_admin_page()
 */
add_action( 'admin_menu', __NAMESPACE__ . '\add_object_admin_page' );

/**
 * Adds a link to the parent post for child posts.
 *
 * @see object-post-types.php::add_object_parent_link
 */
add_action( 'edit_form_top', __NAMESPACE__ . '\add_object_parent_link' );

/**
 * Adds a div to top of edit post pages for museum object post types to report
 * problems (eg. failing to assign post to a category).
 *
 * @see object-post-types.php::add_object_problem_div()
 */
add_action( 'admin_notices', __NAMESPACE__ . '\add_object_problem_div' );

/**
 * Adds quick browse page to all museum object post types.
 *
 * @see quick-browse.php::add_quick_browse()
 */
add_action( 'admin_menu', __NAMESPACE__ . '\add_quick_browse' );

/**
 * Output csv to save.
 *
 * @see import-export.php::export_csv
 */
add_action( 'admin_menu', __NAMESPACE__ . '\export_csv' );

/**
 * Adds javascript to upload image attachments to object posts.
 *
 * @see object-ajax.php::wpm_media_box_enqueue
 */
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\wpm_media_box_enqueue' );

/**
 * Creates settings for customizer.
 */
add_action( 'customize_register', __NAMESPACE__ . '\register_customization' );

/**
 * Load block scripts for editor.
 *
 * @see blocks.php::enqueue_block_scripts()
 */
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_scripts' );

/**
 * Enqueues javascript to de-register Object Meta Fields and Object Image
 * Gallery blocks for non Museum Objects, becaue they don't make sense for
 * other post types.
 *
 * @see blocks.php
 */
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\unregister_object_blocks_for_non_objects' );

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_react' );

add_action( 'admin_menu', __NAMESPACE__ . '\create_admin_react_pages' );

/*****************************************************************************
 *
 * Admin Filters
 *
 *****************************************************************************/

/**
 * Add 'Museum' category to Gutenberg block categories.
 *
 * @see blocks.php::add_museum_block_category()
 */
add_filter( 'block_categories', __NAMESPACE__ . '\add_museum_block_category' );

/*****************************************************************************
 *
 * Frontend Actions
 *
 *****************************************************************************/

/**
 * Enqueue fancybox javascript for frontend.
 *
 * @see object-ajax::enqueue_javascript()
 */
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_javascript' );

/**
 * Enqueue fancybox CSS for frontend.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_style(
			'fancybox-css',
			'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css',
			[],
			CSS_VERSION
		);
	}
);

/**
 * Generates CSS for museum objects, and inserts into page header
 * for front end display.
 *
 * @see display.php::object_css()
 */
add_action( 'wp_head', __NAMESPACE__ . '\object_css' );

/**
 * Generates CSS for collections, and inserts into page header
 * for front end display.
 *
 * @see display.php::object_css()
 */
add_action( 'wp_head', __NAMESPACE__ . '\collection_css' );

/**
 * Load block scripts for frontend.
 *
 * @see blocks.php::enqueue_block_scripts()
 */
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_block_frontend_scripts' );

/*****************************************************************************
 *
 * Frontend Filters
 *
 *****************************************************************************/

/**
 * Filter to add link text to content containing text patterns matching
 * schema (regular expression) of object's id field as set in object
 * admin page.
 *
 * @see object-post-types.php::link_objects_by_id()
 */
add_filter( 'the_content', __NAMESPACE__ . '\link_objects_by_id' );

/**
 * Filters content for museum objects to display custom fields, image gallery, etc.
 *
 * @see display.php::object_content_filter()
 */
add_filter( 'the_content', __NAMESPACE__ . '\object_content_filter' );

/**
 * Filters content for collections.
 *
 * @see display.php::collection_content_filter()
 */
//add_filter( 'the_content', __NAMESPACE__ . '\collection_content_filter' );

/**
 * Generates excerpts for museum objects.
 *
 * @see display.php::mobject_excerpt_filter()
 */
add_filter( 'get_the_excerpt', __NAMESPACE__ . '\mobject_excerpt_filter', 10, 2 );

 /*****************************************************************************
 *
 * AJAX
 *
 *****************************************************************************/

/**
 * Creates a new post with same type as current post and sets current post as its parent.
 *
 * @see object-ajax.php::create_new_obj_aj()
 */
add_action( 'wp_ajax_create_new_obj_aj', __NAMESPACE__ . '\create_new_obj_aj' );

/**
 * Remove an image attachment from an object.
 *
 * @see object-ajax.php::remove_image_attachment_aj()
 */
add_action( 'wp_ajax_remove_image_attachment_aj', __NAMESPACE__ . '\remove_image_attachment_aj' );

/**
 * Moves an image attachment for object post types when the left or right arrows are clicked.
 *
 * @see object-ajax.php::swap_image_order_aj()
 */
add_action( 'wp_ajax_swap_image_order_aj', __NAMESPACE__ . '\swap_image_order_aj' );

/**
 * Adds images to object image box after selection/upload through WP Media popup.
 *
 * @see object-ajax.php::add_gallery_images_aj()
 */
add_action( 'wp_ajax_add_gallery_images_aj', __NAMESPACE__ . '\add_gallery_images_aj' );

/**
 * Generates zip file of object images and makes available for download.
 *
 * @see import-export.php::export_images_aj()
 */
add_action( 'wp_ajax_export_images_aj', __NAMESPACE__ . '\export_images_aj' );

/**
 * Deletes a kind from the museum administration page.
 *
 * @see object-admin-functions.php::delete_kind_aj()
 */
add_action( 'wp_ajax_delete_kind_aj', __NAMESPACE__ . '\delete_kind_aj' );

/**
 * Deletes an image backup zip from the museum administration page.
 *
 * @see object-admin-functions.php::delete_image_zip_aj()
 */
add_action( 'wp_ajax_delete_image_zip_aj', __NAMESPACE__ . '\delete_image_zip_aj' );

/**
 * Redirects cateogry pages to collection pages.
 *
 * @see collection-functions.php::collection_redirect()
 */
add_action( 'template_redirect', __NAMESPACE__ . '\collection_redirect' );

