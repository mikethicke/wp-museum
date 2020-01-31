<?php
/**
 * Functions for dealing with CustomPostType objects.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Callback to search post meta fields when searching posts.
 *
 * @param WP_QUERY $query WordPress query object.
 */
function custom_search( $query ) {
	global $wpdb;
	if ( $query->is_main_query() && is_search() ) {
		$search_string = get_search_query();
		$search_string = '%' . $wpdb->esc_like( $search_string ) . '%';
		$post_ids      = wp_cache_get( 'custom_search_post_ids_' . $search_string, CACHE_GROUP );
		if ( ! $post_ids ) {
			$post_ids_meta = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT post_id FROM {$wpdb->postmeta}
					WHERE meta_value LIKE %s",
					$search_string
				)
			);
			$post_ids_post = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT ID FROM {$wpdb->posts}
					WHERE post_title LIKE %s
					OR post_content LIKE %s",
					$search_string,
					$search_string
				)
			);
			$post_ids      = array_merge( $post_ids_meta, $post_ids_post );
			wp_cache_add( 'custom_search_post_ids_' . $search_string, $post_ids, CACHE_GROUP );
			$query->set( 'post__in', $post_ids );
		}
		return $query;
	} else {
		return $query;
	}
}