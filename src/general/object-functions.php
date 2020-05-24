<?php
/**
 * Functions for interfacing with object post types.
 *
 * @package MikeThicke\WPMuseum
 * @see object_post_types.php
 */

namespace MikeThicke\WPMuseum;

/**
 * List of WordPress type names for all objects in database.
 *
 * @return [string] List of WordPress custom post type names.
 */
function get_object_type_names() {
	$mobject_kinds = get_mobject_kinds();
	$type_names    = [];
	foreach ( $mobject_kinds as $kind ) {
		$type_names[] = $kind->type_name;
	}
	return $type_names;
}

/**
 * Finds custom post object for a WordPress custom post type name.
 *
 * @param string $type_name The WordPress custom post type name.
 *
 * @return ObjectKind An object representing the museum object kind.
 */
function kind_from_type( $type_name ) {
	$mobject_kinds = get_mobject_kinds();
	foreach ( $mobject_kinds as $object_kind ) {
		if ( $object_kind->type_name === $type_name ) {
			return $object_kind;
		}
	}
	return false;
}

/**
 * Finds museum object kind from a particular post.
 *
 * @param WP_Post $post_type A WordPress post with a custom post type.
 *
 * @return ObjectKind The kind of that object.
 */
function kind_from_post( $post_type ) {
	return kind_from_type( $post_type->post_type );
}

/**
 * Save object image gallery array.
 *
 * This function used to take an associative array of image_id => sort_order
 * but now just takes a simple ordered array.
 *
 * @param array $attached_image_array Ordered array of image ids.
 * @param int   $post_id The id of post containing the image gallery.
 *
 * @return bool  True if successful.
 */
function set_object_image_box_attachments( $attached_image_array, $post_id ) {
	if ( ! is_array( $attached_image_array ) ) {
		return false;
	}
	update_post_meta( $post_id, 'wpm_gallery_attach_ids', $attached_image_array );

	return true;
}

/**
 * Get image gallery array for an object.
 *
 * @param int $post_id The id of the post containing the image gallery.
 *
 * @return [int=>int] An array of image_id => sort_order
 */
function get_object_image_attachments( $post_id ) {
	$attached_image_array = [];
	$attach_ids = get_post_meta( $post_id, 'wpm_gallery_attach_ids', true );
	if ( ! is_array( $attach_ids ) ) {
		return [];
	}
	$attach_ids = array_map(
		function( $item ) {
			return intval( $item );
		},
		$attach_ids
	);
	return array_flip( $attach_ids );
}

/**
 * Displays fancybox thumbnails for all image attachments of a post.
 *
 * @param int $post_id The id of the post.
 */
function object_image_box_contents( $post_id = null ) {
	global $post;
	if ( is_null( $post_id ) ) {
		if ( is_null( $post ) ) {
			return false;
		}
		$post_id = $post->ID;
	}

	$image_box_contents = get_object_image_attachments( $post_id );
	if ( ! empty( $image_box_contents ) ) {
		asort( $image_box_contents );
		foreach ( $image_box_contents as $image_id => $sort_order ) {
			$image_thumbnail = wp_get_attachment_image_src( $image_id, 'thumbnail' );
			$image_full      = wp_get_attachment_image_src( $image_id, 'large' );
			echo "<div id='image-div-" . esc_html( $image_id ) . "' class='inline-image-box'>";
			echo "<a data-fancybox='fbgallery' href='" . esc_html( $image_full[0] ) . "'><img src='" . esc_html( $image_thumbnail[0] ) . "' width=' " . esc_html( $image_thumbnail[1] ) . "' height='" . esc_html( $image_thumbnail[2] ) . "'></a>";
			echo "<a id='delete-" . esc_html( $image_id ) . "' class='wpm-image-delete' onclick='remove_image_attachment(" . esc_html( $image_id ) . ',' . esc_html( $post_id ) . ")'>[x]</a>";
			echo "<a id='moveup-" . esc_html( $image_id ) . "' class='wpm-image-moveup' onclick='wpm_image_move(" . esc_html( $image_id ) . ", -1)'><span class='dashicons dashicons-arrow-left'></span></a>";
			echo "<a id='movedown-" . esc_html( $image_id ) . "' class='wpm-image-movedown' onclick='wpm_image_move(" . esc_html( $image_id ) . ", +1)'><span class='dashicons dashicons-arrow-right'></span></a>";
			echo '</div>';
		}
	}
}

/**
 * Gets all descendent posts (recursive children) of a post.
 *
 * @param int     $post_id        A post.
 * @param string  $post_status    The publication status of descendent posts to retrieve.
 *
 * @return [WP_Post] Array of descendent posts.
 */
function get_post_descendants( $post_id, $post_status = 'publish' ) {
	$parent_post = get_post( $post_id );
	$descendants = [];
	$children    = get_posts(
		[
			'numberposts' => -1,
			'post_status' => $post_status,
			'post_type'   => $parent_post->post_type,
			'post_parent' => $post_id,
		]
	);
	foreach ( $children as $child ) {
		$grand_children = get_post_descendants( $child, $post_status );
		$descendants    = array_merge( $descendants, $grand_children );
	}
	$descendants = array_merge( $descendants, $children );
	return $descendants;
}

/**
 * Returns the id of an object's thumbnail.
 *
 * @param integer $post_id ID of the current post.
 * @return integer the ID of the thumbnail or the first image.
 */
function object_thumbnail_id( $post_id ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$attach_id = get_post_thumbnail_id( $post_id );
	} else {
		$attachments = get_attached_media( 'image', $post_id );
		if ( $attachments ) {
			$attachment = reset( $attachments );
			$attach_id  = $attachment->ID;
		}
	}

	if ( isset( $attach_id ) ) {
		return $attach_id;
	} else {
		return false;
	}
}

/**
 * Returns an object post from cat_id.
 *
 * @param ObjectKind / int  $kind    Kind or kind id corresponding to the object.
 * @param string            $cat_id  The post's catalog id field.
 *
 * @return WP_POST A Wordpress post matching that id, or null.
 */
function get_object_post_from_id( $kind, $cat_id ) {
	if ( is_int( $kind ) ) {
		$kind = get_kind( $kind );
	}
	$id_field = get_mobject_field( $kind->kind_id, $kind->cat_field_id );

	$args  = [
		'post_type'   => $kind->type_name,
		'post_status' => 'any',
		'meta_key'    => $id_field->slug,
		'meta_value'  => $cat_id,
	];
	$posts = get_posts( $args );
	if ( 1 === count( $posts ) ) {
		return $posts[0];
	} else {
		return null;
	}
}

/**
 * Builds a meta query for a kind from a REST request.
 *
 * @param int    $kind_id Kind id corresponding to the object.
 * @param Object $request  A REST request object.
 *
 * @return Array Meta query that can be added to a WordPress query in the meta_query field.
 */
function build_meta( $kind_id, $request ) {
	$mobject_fields = get_mobject_fields( $kind_id );
	$meta_query     = [ 'relation' => 'AND' ];
	foreach ( $mobject_fields as $field ) {
		$field_query = $request->get_param( $field->slug );
		if ( ! empty( $field_query ) && $field->public ) {
			$meta_query[] = [
				'key'     => $field->slug,
				'value'   => $field_query,
				'compare' => 'LIKE',
			];
		}
	}
	if ( count( $meta_query ) > 1 ) {
		return $meta_query;
	} else {
		return [];
	}
}

/**
 * Builds a combined query for REST search requests.
 *
 * @param ObjectKind | [ ObjectKind ] $kinds   A kind or list of kinds to be searched.
 * @param Object                      $request A REST request object.
 *
 * @return Array A combined query that can be passed as combined_query argument in a query object.
 */
function build_rest_combined_query( $kinds, $request ) {
	if ( ! is_array( $kinds ) ) {
		$kinds = [ $kinds ];
	}

	$search_string = $request->get_param( 's' );
	if ( empty( $search_string ) ) {
		$args = [];
		foreach ( $kinds as $kind ) {
			$meta_query = build_meta( $kind->kind_id, $request );
			$new_arg = [
				'post_type'   => $kind->type_name,
				'post_status' => 'public',
				'meta_query'  => $meta_query,
			];
			$title_query = $request->get_param( 'post_title' );
			if ( ! empty( $title_query ) ) {
				$new_arg['post_title'] = $title_query;
			}
			$content_query = $request->get_param( 'post_content' );
			if ( ! empty( $content_query ) ) {
				$new_arg['post_content'] = $content_query;
			}
			if ( ! empty( $meta_query ) ) {
				$args[] = $new_arg;
			}
		}
	} else {
		$kind_type_list = array_map(
			function ( $x ) {
				return $x->type_name;
			},
			$kinds
		);

		$args = [
			[
				'post_type'   => $kind_type_list,
				'post_status' => 'public',
				's'           => $search_string,
			],
		];

		foreach ( $kinds as $kind ) {
			$meta_query = [ 'relation' => 'OR' ];
			$fields     = get_mobject_fields( $kind->kind_id );
			foreach ( $fields as $field ) {
				if ( $field->public ) {
					$meta_query[] = [
						'key'     => $field->slug,
						'value'   => $search_string,
						'compare' => 'LIKE',
					];
				}
			}
			if ( count( $meta_query ) > 1 ) {
				$args[] = [
					'post_type'   => $kind->type_name,
					'post_status' => 'public',
					'meta_query'  => $meta_query,
				];
			}
		}
	}

	if ( empty( $args ) ) {
		return [];
	} else {
		$combined_query = [
			'args'  => $args,
			'union' => 'UNION',
		];
		return $combined_query;
	}
}

/**
 * Gets thumbnail for object if there is one, or first image if not.
 *
 * @param int $post_id WordPress post_id of object.
 *
 * @return Array Array of image data [url, height, width] or [] if none.
 */
function get_object_thumbnail( $post_id ) {

	if ( has_post_thumbnail( $post_id ) ) {
		$attach_id = get_post_thumbnail_id( $post_id );
	} else {
		$attachments = get_object_image_attachments( $post_id );
		if ( count( $attachments ) > 0 ) {
			reset( $attachments );
			$attach_id = key( $attachments );
		}
	}

	if ( isset( $attach_id ) ) {
		$img_data = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
	} else {
		$img_data = [];
	}

	return $img_data;
}


