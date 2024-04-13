<?php
/**
 * Functions for dealing with CustomPostType objects.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * A filter for WP_QUERY to allow for searching just the post title.
 *
 * @link https://wordpress.stackexchange.com/questions/18703/wp-query-with-post-title-like-something
 *
 * @param string   $where    The existing WHERE clause.
 * @param WP_QUERY $wp_query The query object.
 */
function post_search_filter( $where, $wp_query ) {
	global $wpdb;

	// Run only once.
	remove_filter( 'posts_where', __NAMESPACE__ . 'post_search_filter', 10, 2 );

	$search_term = $wp_query->get( 'post_title' );
	if ( $search_term ) {
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
	}
	$search_term = $wp_query->get( 'post_content' );
	if ( $search_term ) {
		$where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
	}
	return $where;
}

/**
 * A filter to add post_title and post_content to the WP_QUERY query vars.
 *
 * @link https://www.smashingmagazine.com/2016/03/advanced-wordpress-search-with-wp_query/
 *
 * @param [string] $vars Array of accepted query vars.
 * @return [string] Updated array of query vars.
 */
function add_title_content_query_vars( $vars ) {
	$vars[] = 'post_title';
	$vars[] = 'post_content';
	return $vars;
}

/**
 * Get custom post type on admin pages.
 *
 * @see https://stackoverflow.com/a/59147234
 */
function admin_post_type() {
	global $post, $parent_file, $typenow, $current_screen, $pagenow;

	$post_type = null;

	if ( $post && ( property_exists( $post, 'post_type' ) || method_exists( $post, 'post_type' ) ) ) {
		$post_type = $post->post_type;
	}

	if ( empty( $post_type ) && ! empty( $current_screen ) && ( property_exists( $current_screen, 'post_type' ) || method_exists( $current_screen, 'post_type' ) ) && ! empty( $current_screen->post_type ) ) {
		$post_type = $current_screen->post_type;
	}

	if ( empty( $post_type ) && ! empty( $typenow ) ) {
		$post_type = $typenow;
	}

	if ( empty( $post_type ) && function_exists( 'get_current_screen' ) ) {
		$post_type = get_current_screen();
	}

	//phpcs:ignore
	if ( empty( $post_type ) && isset( $_REQUEST['post'] ) && ! empty( $_REQUEST['post'] ) && function_exists( 'get_post_type' ) && $get_post_type = get_post_type( (int) $_REQUEST['post'] ) ) {
		$post_type = $get_post_type;
	}

	if ( empty( $post_type ) && isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) {
		$post_type = sanitize_key( $_REQUEST['post_type'] );
	}

	if ( empty( $post_type ) && 'edit.php' == $pagenow ) {
		$post_type = 'post';
	}

	return $post_type;
}
