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
 * @param [int=>int] $attached_image_array Associative array of image_id => sort_order.
 * @param int        $post_id The id of post containing the image gallery.
 *
 * @return bool      True if successful.
 */
function set_object_image_box_attachments( $attached_image_array, $post_id ) {
	if ( ! is_array( $attached_image_array ) ) {
		return false;
	}
	$attached_images_str  = '';
	$existing_sort_orders = [];
	foreach ( $attached_image_array as $image_id => $sort_order ) {
		if ( $image_id ) {
			if ( $attached_images_str ) {
				$attached_images_str .= ',';
			}
			if ( ! $sort_order || in_array( $sort_order, $existing_sort_orders, true ) ) {
				$sort_order = max( $existing_sort_orders ) + 1;
			}
			$existing_sort_orders[] = $sort_order;
			$attached_images_str   .= $image_id . ':' . $sort_order;
		}
	}
	update_post_meta( $post_id, 'wpm_gallery_attach_ids', $attached_images_str );
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
	$custom               = get_post_custom( $post_id );
	if ( ! isset( $custom['wpm_gallery_attach_ids'] ) ||
		! $custom['wpm_gallery_attach_ids'][0] ) {
		return [];
	}
	$image_pairs_array = explode( ',', $custom['wpm_gallery_attach_ids'][0] );
	$max_order         = 0;
	foreach ( $image_pairs_array as $image_pair_str ) {
		$image_pair_arr = explode( ':', $image_pair_str );
		if ( 2 === count( $image_pair_arr ) ) {
			$attached_image_array[ $image_pair_arr[0] ] = $image_pair_arr[1];
			if ( $image_pair_arr[1] >= $max_order ) {
				$max_order = $image_pair_arr[1];
			}
		} elseif ( 1 === count( $image_pair_arr ) ) {
			$max_order++;
			$attached_image_array[ $image_pair_arr[0] ] = $max_order;
		}
	}
	asort( $attached_image_array );
	return $attached_image_array;
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
 * Checks a post upon save against requirementss set in object admin.
 *
 * @param int $post_id Id of post to check.
 *
 * @return [string] Array of error messages for failed requirements.
 */
function check_object_post( $post_id = null ) {
	global $post;
	if ( ! $post_id ) {
		if ( ! $post ) {
			return false;
		} else {
			$post_id = $post->ID;
		}
	}

	$the_post = get_post( $post_id );

	$problems = array();

	$custom = get_post_custom( $post_id );
	$kind = kind_from_type( $the_post->post_type );
	$fields = get_mobject_fields( $kind->kind_id );

	foreach ( $fields as $field ) {
		if ( 1 === $field->required && empty( $custom[ $field->slug ][0] ) ) {
			$problems[] = "{$field->name} is required but empty.";
		}
		if ( ! empty( $field->field_schema ) && ! empty( $custom[ $field->slug ][0] ) ) {
			$pattern = '/^' . stripslashes( $field->field_schema ) . '$/';
			if ( ! preg_match( $pattern, $custom[ $field->slug ][0], $matches ) ) {
				$problems[] = esc_html( "{$field->name} does not conform to required schema: {$pattern}." );
			}
		}
		if ( ! empty( $custom[ $field->slug ] ) && $field->field_id === $kind->cat_field_id ) {
			$args           = [
				'post_type'   => $the_post->post_type,
				'numberposts' => -1,
				'post_status' => 'any',
				'meta_key'    => $field->slug,
				'meta_value'  => $custom[ $field->slug ][0],
			];
			$matching_posts = get_posts( $args );
			foreach ( $matching_posts as $match ) {
				if ( $match->ID !== $the_post->ID ) {
					$problems[] = "{$field->name} must be unique, but is already possessed by <a href='post.php?post={$match->ID}&action=edit'>{$match->post_title}</a>.";
				}
			}
		}
	}
	if ( $kind->categorized ) {
		$post_category = get_the_category( $the_post->ID );
		if ( 0 === count( $post_category ) ||
				( 1 === count( $post_category ) &&
					'Uncategorized' === $post_category[0]->name
				)
			) {
				$problems[] = 'Post must be categorized.';
		}
	}
	if ( $kind->must_featured_image ) {
		$thumb = get_the_post_thumbnail( $the_post );
		if ( empty( $thumb ) ) {
			$problems[] = 'Post must have featured image.';
		}
	}
	if ( $kind->must_gallery ) {
		if ( ! isset( $custom[ WPM_PREFIX . 'gallery_attach_ids' ][0] ) ||
			empty( $custom[ WPM_PREFIX . 'gallery_attach_ids' ][0] ) ) {
				$problems[] = 'Post must have image gallery.';
		}
	}
	return $problems;
}

/**
 * Returns the id of an object's thumbnail.
 *
 * @param integer $post_id ID of the current post.
 * @return integer the ID of the thumbnail or the first image.
 */
function object_thumbnail_id ( $post_id ) {
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


