<?php
/**
 * Functions for interfacing with the Collection post type.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Gets all posts of the Collection post type.
 *
 * @param string $post_status The post status of collections to retrieve.
 *
 * @return Array Array WP_Post.
 */
function get_collections( $post_status = 'any' ) {
	$collections = get_posts(
		[
			'numberposts' => -1,
			'post_status' => $post_status,
			'post_type'   => WPM_PREFIX . 'collection',
		]
	);
	return $collections;
}

/**
 * Generates a category argument for querying objects associated with a collection.
 *
 * @param int $post_id Post ID of the collection.
 *
 * @return Array [ key: cat | category__in, val: list of category IDs ]
 */
function generate_associated_objects_category_argument( $post_id, $post_status = 'publish' ) {
	$post_custom = get_post_custom( $post_id );

	if ( ! isset( $post_custom['associated_category'] ) ) {
		return false;
	}

	$included_categories = $post_custom['associated_category'];
	if ( isset( $post_custom['include_sub_collections'] ) && '1' === $post_custom['include_sub_collections'][0] ) {
		$descendants = get_post_descendants( $post_id, $post_status );
		foreach ( $descendants as $descendant ) {
			$d_custom            = get_post_custom( $descendant->ID );
			$included_categories = array_merge( $included_categories, $d_custom['associated_category'] );
		}
	}

	if ( isset( $post_custom['include_child_categories'] ) && '1' === $post_custom['include_child_categories'][0] ) {
		$cat_call = 'cat';
		$cat_val  = implode( ',', $included_categories );
	} else {
		$cat_call = 'category__in';
		$cat_val  = $included_categories;
	}

	return [
		'key' => $cat_call,
		'val' => $cat_val,
	];
}

/**
 * Creates query containing all posts associated with the current collection.
 *
 * @param string $post_status    The the publication status of posts to retrieve.
 * @param int    $post_id        If set, retrieve posts associated with $post_id rather than the current $post.
 * @param bool   $show_all       If true, retrieve all posts rather than paged results.
 * @param int    $page_num       If set, retrieve specific page of results. Can also be set with $_GET['page'].
 *                               If not set, defaults to global query value.
 *
 * @return WP_Query              A WordPress query object containing the retrieved posts.
 *
 * @link https://developer.wordpress.org/reference/classes/wp_query/
 */
function query_associated_objects( $post_status = 'publish', $post_id = null, $show_all = false, $page_num = null ) {
	global $post;
	if ( is_null( $post_id ) ) {
		$post_id = $post->ID;
	}

	$mobject_kinds   = get_object_type_names();
	$display_options = get_customizer_settings()[ WPM_PREFIX . 'collection_style' ];

	$cat_args = generate_associated_objects_category_argument( $post_id, $post_status );
	if ( ! $cat_args ) {
		return null;
	}

	if ( $show_all ) {
		$collection_query = new \WP_Query(
			[
				$cat_args['key'] => $cat_args['val'],
				'numberposts'    => -1,
				'post_status'    => $post_status,
				'posts_per_page' => -1,
				'post_type'      => $mobject_kinds,
			]
		);
	} else {
		if ( is_null( $page_num ) && isset( $_GET['page'] ) ) {
			$page_num = intval( $_GET['page'] );
		} else {
			$page_num = get_query_var( 'page' );
		}
		if ( isset( $display_options['posts_per_page'] ) ) {
			$posts_per_page = $display_options['posts_per_page'];
		} else {
			$posts_per_page = DEF_POSTS_PER_PAGE;
		}
		$collection_query = new \WP_Query(
			[
				$cat_args['key'] => $cat_args['val'],
				'posts_per_page' => $posts_per_page,
				'paged'          => $page_num,
				'post_status'    => $post_status,
				'post_type'      => $mobject_kinds,
			]
		);
	}
	return $collection_query;
}

/**
 * Retrieves all posts associated with the current collection.
 *
 * @param string   $post_status The publication status of the posts to retrieve.
 * @param int|null $post_id     Collection's post_id.
 *
 * @return [Post]   Array of posts associated with the current collection.
 */
function get_associated_objects( $post_status = 'publish', $post_id = null ) {
	$query = query_associated_objects( $post_status, $post_id );
	if ( ! is_null( $query ) ) {
		return $query->posts;
	}
	return array();
}

/**
 * Retrieves post ids of all posts associated with a collection.
 *
 * @param int    $post_id        The post id of the collection.
 * @param string $post_status    The publication status of the posts to retrieve.
 *
 * @return [int]    Array of post ids associated with the collection.
 */
function get_associated_object_ids( $post_id, $post_status = 'publish' ) {
	$query = query_associated_objects( $post_status, $post_id, 1 );
	if ( ! is_null( $query ) ) {
		$post_ids = array_map(
			function ( $element ) {
					return $element->ID;
			},
			$query->posts
		);
		return $post_ids;
	}
	return [];
}

/**
 * Retrieves all collections that a post is associated with.
 *
 * @param int $post_id    The id of the post.
 *
 * @return Array  Array of collection posts that the current post is associated with.
 */
function get_object_collections( $post_id ) {
	$object             = get_post( $post_id );
	$collections        = get_collections();
	$object_collections = [];
	$object_categories  = array_map(
		function ( $cat ) {
			return $cat->term_id;
		},
		get_the_category( $object->ID )
	);
	foreach ( $collections as $collection ) {
		$collection_custom = get_post_custom( $collection->ID );
		if (
			isset( $collection_custom['associated_category'] ) &&
			is_array( $collection_custom['associated_category'] )
		) {
			$cat_intersect = array_intersect( $object_categories, $collection_custom['associated_category'] );
			if ( count( $cat_intersect ) > 0 ) {
				$object_collections[] = $collection;
			}
		}
	}
	return $object_collections;
}

/**
 * Retrieves links to all collections that a post is associated with.
 *
 * @param string $separator      String separating each link.
 *
 * @param int    $post_id        The id of the post.
 * @return string   Html string containing links to each collection.
 */
function object_collections_string( $post_id, $separator = '' ) {
	$collections   = get_object_collections( $post_id );
	$return_string = '';
	foreach ( $collections as $collection ) {
		if ( '' !== $return_string ) {
			$return_string .= $separator;
		}
		$permalink      = get_permalink( $collection->ID );
		$return_string .= "<a href='$permalink'>" . esc_html( $collection->post_title ) . '</a>';
	}
	return $return_string;
}

/**
 * Callback to redirect category listings for collection.
 *
 * If collection_override_cattegory option is set, redirect category listing for
 * associated collections to the collection post instead.
 */
function collection_redirect() {
	global $wp_query;
	if ( is_null( $wp_query->queried_object ) || 'category' !== $wp_query->queried_object->taxonomy ) {
		return;
	}
	if ( ! get_option( WPM_PREFIX . 'collection_override_category' ) ) {
		return;
	}
	$collections = get_collections( 'publish' );
	foreach ( $collections as $collection ) {
		$custom = get_post_custom( $collection->ID );
		if ( intval( $custom['associated_category'][0] ) === $wp_query->queried_object->cat_ID ) {
			wp_safe_redirect( get_post_permalink( $collection->ID ), 308 );
			exit;
		}
	}
}

/**
 * Get url of featured image for a collection.
 *
 * If the collection post has a featured image, return that. Otherwise, get an
 * image from one of the collection objects.
 *
 * @param int $post_id        The id of the post.
 *
 * @return Array Image data of featured image or image from collection object.
 */
function get_collection_featured_image( $post_id ) {
	$image_id = get_post_thumbnail_id( $post_id );
	if ( $image_id ) {
		return wp_get_attachment_image_src( $image_id, 'medium' );
	}

	$associated_objects = get_associated_objects( 'publish', $post_id );
	foreach ( $associated_objects as $object ) {
		$image_attachments = get_object_image_attachments( $object->ID );
		if ( count( $image_attachments ) > 0 ) {
			foreach ( $image_attachments as $image_attach_id => $sort_order ) {
				return wp_get_attachment_image_src( $image_attach_id, 'medium' );
			}
		}
	}

	return false;
}
