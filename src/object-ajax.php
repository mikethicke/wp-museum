<?php
/**
 * Ajax functions that add functionality to edit screens for object post types.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Creates a new post with same type as current post and sets current post as its parent.
 * Called via ajax.
 *
 * @see javascript/admin.js::new_obj()
 */
function create_new_obj_aj() {
	if ( ! check_ajax_referer( 'kcDbrTMMfFqh6jy8&LrCGoH7p', 'nonce' ) ) {
		wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
	}
	if ( ! isset( $_POST['parent'] ) ) {
		wp_die( esc_html__( 'Tried to create child post but parent post not found.', 'wp-museum' ) );
	}
	$parent_id   = intval( $_POST['parent'] );
	$parent_post = get_post( $parent_id );
	$categories  = wp_get_post_categories( $parent_id );
	$args        = [
		'post_title'    => '',
		'post_content'  => '',
		'post_type'     => $parent_post->post_type,
		'post_parent'   => $parent_id,
		'post_category' => $categories,
	];
	$post_id     = wp_insert_post( $args );
	echo esc_html( $post_id );
	wp_die();
}

/**
 * Adds javascript to upload image attachments to object posts.
 */
function wpm_media_box_enqueue() {
	wp_enqueue_media();
	wp_enqueue_script(
		'media-upload',
		'',
		[],
		SCRIPT_VERSION,
		true
	);
}

/**
 * Display images using fancybox-jq
 */
function enqueue_javascript() {
	wp_enqueue_script(
		'fancybox-jq',
		'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js',
		[ 'jquery' ],
		SCRIPT_VERSION,
		true
	);
}

/**
 * Remove an image attachment from an object.
 *
 * @see javascript/jquery-wp-uploader.js::remove_image_attachment()
 */
function remove_image_attachment_aj() {
	if ( ! check_ajax_referer( 'kcDbrTMMfFqh6jy8&LrCGoH7p', 'nonce' ) ) {
		wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
	}
	if ( ! isset( $_POST['image_id'] ) ) {
		wp_die( esc_html__( 'Tried to remove image attachment, but no image_id found.', 'wp-museum' ) );
	}
	if ( ! isset( $_POST['post_id'] ) ) {
		wp_die( esc_html__( 'Tried to remove image attachment, but no post_id found.', 'wp-museum' ) );
	}
	$image_id          = intval( $_POST['image_id'] );
	$post_id           = intval( $_POST['post_id'] );
	$image_attachments = get_object_image_attachments( $post_id );
	unset( $image_attachments[ $image_id ] );
	set_object_image_box_attachments( $image_attachments, $post_id );
	object_image_box_contents( $post_id );
	wp_die();
}

/**
 * Moves an image attachment for object post types when the left or right arrows are clicked.
 *
 * @see javascript/jquery-wp-uploader.js::wpm_image_move_js()
 */
function swap_image_order_aj() {
	if ( ! check_ajax_referer( 'kcDbrTMMfFqh6jy8&LrCGoH7p', 'nonce' ) ) {
		wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
	}
	if (
		! isset( $_POST['post_id'] ) ||
		! isset( $_POST['first_image_id'] ) ||
		! isset( $_POST['second_image_id'] )
	) {
		wp_die( esc_html__( 'swap_image_order: Required parameter missing.', 'wp-museum' ) );
	}
	$attached_images                     = get_object_image_attachments( intval( $_POST['post_id'] ) );
	$first_image_id                      = intval( substr( sanitize_key( wp_unslash( $_POST['first_image_id'] ) ), strlen( 'image-div-' ) ) );
	$second_image_id                     = intval( $_POST['second_image_id'] );
	$first_image_order                   = $attached_images[ $first_image_id ];
	$attached_images[ $first_image_id ]  = $attached_images[ $second_image_id ];
	$attached_images[ $second_image_id ] = $first_image_order;
	set_object_image_box_attachments( $attached_images, intval( $_POST['post_id'] ) );
	wp_die();
}

/**
 * Ajax callback to add images to object image box after selection/upload through
 * WP Media popup.
 *
 * @see javascript/jquery-wp-uploader.js
 */
function add_gallery_images_aj() {
	if ( ! check_ajax_referer( 'kcDbrTMMfFqh6jy8&LrCGoH7p', 'nonce' ) ) {
		wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
	}
	if ( ! isset( $_POST['post_id'] ) ) {
		wp_die( esc_html__( 'add_gallery_images: post_id not found.', 'wp-museum' ) );
	}
	$image_attachments = get_object_image_attachments( intval( $_POST['post_id'] ) );
	if ( isset( $_POST['wpm_gallery_attachment_ids'] ) ) {
		$new_ids_arr = explode( ',', sanitize_key( wp_unslash( $_POST['wpm_gallery_attachment_ids'] ) ) );
	} else {
		$new_ids_arr = array();
	}
	$max_sort_order = max( $image_attachments );
	foreach ( $new_ids_arr as $new_id ) {
		$max_sort_order++;
		$image_attachments[ $new_id ] = $max_sort_order;
	}
	set_object_image_box_attachments( $image_attachments, intval( $_POST['post_id'] ) );
	object_image_box_contents( intval( $_POST['post_id'] ) );
	wp_die();
}
