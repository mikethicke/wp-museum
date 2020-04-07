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
