<?php
/**
 * Registers REST routes and endpoints.
 *
 * REST root: /wp-json/wp-museum/v1
 *
 * ## Objects ##
 * /wp-json/wp-museum/v1/<object type>/[?s=|<field>=]   Objects with post type <object type>.
 * /wp-json/wp-museum/v1/<object type>/<post id>        Specific object.
 * /wp-json/wp-museum/v1/<object type>/<post id>/images Images associated with object.
 * /wp-json/wp-museum/v1/all/[?s=|<field>=]             All museum objects, regardless of type.
 * /wp-json/wp-museum/v1/all/<post id>                  Specific object.
 * /wp-json/wp-museum/v1/all/<post id>/images           Images associated with object. 
 * /wp-json/wp-musuem/v1/<object type>/custom           Public fields for <object type>.
 * /wp-json/wp-musuem/v1/<object type>/custom_all       All fields for <object type>.
 *
 * ## Kinds ##
 * /wp-json/wp-musuem/v1/mobject_kinds                  Object kinds
 * /wp-json/wp-museum/v1/mobject_kinds/<object type>    A specific kind with <object type>.
 *
 * ## Collections ##
 * /wp-json/wp-museum/v1/collections/[?s=]              All museum collections.
 * /wp-json/wp-museum/v1/collections/<post id>          A specific collection.
 * /wp-json/wp-museum/v1/collections/<post id>/objects  Objects associated with a collection.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Register REST endpoints.
 */
function rest_routes() {
	$kinds = get_mobject_kinds();
	foreach ( $kinds as $kind ) {
		/**
		 * /wp-json/wp-museum/v1/<object type>/ - Data for objects with post type <object type>.
		 */
		register_rest_route(
			REST_NAMESPACE,
			'/' . $kind->type_name,
			[
				'methods'  => 'GET',
				'callback' => function( $request ) use ( $kind ) {
					$paged = $request->get_param( 'page' );
					if ( ! isset( $paged ) || empty( $paged ) ) {
						$paged = 1;
					}

					$combined_query = build_rest_combined_query( [ $kind ], $request );

					$args          = [
						'post_status'      => 'publish',
						'paged'            => $paged,
						'post_type'        => $kind->type_name,
						'combined_query'   => $combined_query,
						'suppress_filters' => false,
					];
					$title_query   = $request->get_param( 'post_title' );
					$content_query = $request->get_param( 'post_content' );
					if ( $title_query ) {
						$args['post_title'] = $title_query;
					}
					if ( $content_query ) {
						$args['post_content'] = $content_query;
					}
					$posts = get_posts( $args );

					$post_data = [];
					foreach ( $posts as $post ) {
						$post_data[] = combine_post_data( $post->ID );
					}
					return $post_data;
				},
			]
		);

		/**
		 * /wp-json/wp-museum/v1/<object type>/<post id> - Data for specific object.
		 */
		register_rest_route(
			REST_NAMESPACE,
			'/' . $kind->type_name . '/(?P<id>[\d]+)',
			[
				'methods'  => 'GET',
				'args'     =>
					[
						'id' =>
							[
								'validate_callback' => function( $param, $request, $key ) {
									return is_numeric( $param );
								},
							],
					],
				'callback' => function ( $request ) {
					return combine_post_data( $request['id'] );
				},
			]
		);

		/**
		 * /wp-json/wp-museum/v1/<object type>/<post id>/images Images associated with object.
		 */
		register_rest_route(
			REST_NAMESPACE,
			'/' . $kind->type_name . '/(?P<id>[\d]+)/images',
			images_routes_args()
		);

		/**
		 * /wp-json/wp-musuem/v1/<object type>/custom - Data for public fields for <object type>.
		 */
		foreach ( $kinds as $kind ) {
			register_rest_route(
				REST_NAMESPACE,
				$kind->type_name . '/custom',
				[
					'methods'  => 'GET',
					'callback' => function( $request ) use ( $kind ) {
						$fields = get_mobject_fields( $kind->kind_id );
						$filtered_fields = [];
						foreach ( $fields as $field ) {
							if ( $field->public ) {
								$filtered_fields[ $field->field_id ] = $field;
							}
						}
						return $filtered_fields;
					},
				]
			);
		}

		/**
		 * /wp-json/wp-musuem/v1/<object type>/custom_all - Data for all fields for <object type>.
		 */
		foreach ( $kinds as $kind ) {
			register_rest_route(
				REST_NAMESPACE,
				$kind->type_name . '/custom_all',
				[
					'methods'  => 'GET',
					'callback' => function( $request ) use ( $kind ) {
						$fields = get_mobject_fields( $kind->kind_id );
						$filtered_fields = [];
						foreach ( $fields as $field ) {
							$filtered_fields[ $field->field_id ] = $field;
						}
						return $filtered_fields;
					},
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
				]
			);
		}

		/**
		 * /wp-json/wp-museum/v1/mobject_kinds/<object type> - Data for a specific kind with <object type>.
		 */
		register_rest_route(
			REST_NAMESPACE,
			'/mobject_kinds/' . $kind->type_name,
			[
				'methods' => 'GET',
				'callback' => function() use ( $kind ) {
					return $kind->to_array();
				},
			]
		);
	}

	/**
	 * /wp-json/wp-museum/v1/all/ - Data for all museum objects, regardless of type.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/all',
		[
			'methods'  => 'GET',
			'callback' => function ( $request ) use ( $kinds ) {
				$post_data = [];
				$paged     = $request->get_param( 'page' );

				$kind_type_list = array_map(
					function ( $x ) {
						return $x->type_name;
					},
					$kinds
				);

				$combined_query = build_rest_combined_query( $kinds, $request );

				if ( ! isset( $paged ) || empty( $paged ) ) {
					$paged = 1;
				}

				$args          = [
					'post_status'      => 'publish',
					'paged'            => $paged,
					'post_type'        => $kind_type_list,
					'combined_query'   => $combined_query,
					'suppress_filters' => false,
				];
				$title_query   = $request->get_param( 'post_title' );
				$content_query = $request->get_param( 'post_content' );
				if ( $title_query ) {
					$args['post_title'] = $title_query;
				}
				if ( $content_query ) {
					$args['post_content'] = $content_query;
				}
				$posts = get_posts( $args );
				foreach ( $posts as $post ) {
					$post_data[] = combine_post_data( $post );
				}
				return $post_data;
			},
		]
	);

	/**
	 * /wp-json/wp-museum/v1/all/<post id> - Data for specific object.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/all/(?P<id>[\d]+)',
		[
			'methods'  => 'GET',
			'args'     =>
				[
					'id' =>
						[
							'validate_callback' => function( $param, $request, $key ) {
								return is_numeric( $param );
							},
						],
				],
			'callback' => function ( $request ) {
				return combine_post_data( $request['id'] );
			},
		]
	);

	/**
	 * /wp-json/wp-museum/v1/all/<post id>/images Images associated with object.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/all/(?P<id>[\d]+)/images/',
		images_routes_args()
	);

	/**
	 * /wp-json/wp-musuem/v1/mobject_kinds - Data for object kinds
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/mobject_kinds/',
		array(
			'methods'  => 'GET',
			'callback' => function() {
				return get_mobject_kinds();
			},
		)
	);

	/**
	 * /wp-json/wp-museum/v1/collections - Data for all museum collections.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/collections/',
		[
			'methods' => 'GET',
			'callback' => function( $request ) {
				$paged = $request->get_param( 'page' );
				if ( ! isset( $paged ) || empty( $paged ) ) {
					$paged = 1;
				}

				$args  = [
					'post_status' => 'publish',
					'paged'       => $paged,
					'post_type'   => WPM_PREFIX . 'collection',
				];
				$search_string = $request->get_param( 's' );
				if ( ! empty( $search_string ) ) {
					$args['s'] = $search_string;
				}
				$title_search = $request->get_param( 'post_title' );
				if ( ! empty( $title_search ) ) {
					$args['post_title'] = $title_search;
				}
				$posts = get_posts( $args );

				$post_array = [];
				foreach ( $posts as $post ) {
					$post_data = combine_post_data( $post->ID );
					$associated_objects = get_associated_object_ids( $post->ID );
					$post_data['associated_objects'] = $associated_objects;
					$post_array[] = $post_data;
				}
				return $post_array;
			},
		]
	);

	/**
	 * /wp-json/wp-museum/v1/collections/<post id> - Data for a specific collection.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/collections/(?P<id>[\d]+)',
		[
			'methods' => 'GET',
			'args'    =>
				[
					'id' =>
						[
							'validate_callback' => function( $param, $request, $key ) {
								return is_numeric( $param );
							},
						],
				],
			'callback' => function ( $request ) {
				$post_data = combine_post_data( $request['id'] );
				$associated_objects = get_associated_object_ids( $request['id'] );
				$post_data['associated_objects'] = $associated_objects;
				return $post_data;
			},
		]
	);

	/**
	 * /wp-json/wp-museum/v1/collections/<post id>/objects  Objects associated with a collection.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/collections/(?P<id>[\d]+)/objects',
		[
			'methods' => 'GET',
			'args'    =>
				[
					'id' =>
						[
							'validate_callback' => function( $param, $request, $key ) {
								return is_numeric( $param );
							},
						],
				],
			'callback' => function ( $request ) {
				$associated_objects = get_associated_objects( 'publish', $request['id'] );

				$object_data = [];
				foreach ( $associated_objects as $object ) {
					$object_data[] = combine_post_data( $object );
				}
				return $object_data;
			},
		]
	);
}

/**
 * Filter to change the "Read More..." text into "..." for REST requests.
 *
 * @param string $more The original Read More text.
 */
function rest_excerpt_filter( $more ) {
	return '...';
}

/**
 * Combine custom post data with standard post data and return as array.
 *
 * @param WP_POST | int $post Post object or post id.
 */
function combine_post_data( $post ) {
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	$custom = array_map(
		function ( $i ) {
			return $i[0];
		},
		get_post_custom( $post->ID )
	);

	$kind = get_kind_from_typename( $post->post_type );
	if ( ! empty( $kind ) ) {
		$filtered_custom = [];
		$fields          = get_mobject_fields( $kind->kind_id );
		foreach ( $custom as $field_slug => $field_data ) {
			if ( isset( $fields[ $field_slug ] ) && $fields[ $field_slug ]->public ) {
				$filtered_custom[ $field_slug ] = $field_data;
			}
		}
		$custom = $filtered_custom;

		$cat_field = get_mobject_field( $kind->kind_id, $kind->cat_field_id );
	}

	if ( ! empty( $cat_field ) ) {
		$cat_field_slug = $cat_field->slug;
	} else {
		$cat_field_slug = null;
	}

	$img_data = get_object_thumbnail( $post->ID );

	add_filter( 'excerpt_more', __NAMESPACE__ . '\rest_excerpt_filter', 10, 2 );
	$filtered_excerpt =
		html_entity_decode(
			wp_strip_all_tags(
				get_the_excerpt( $post )
			)
		);
	remove_filter( 'excerpt_more', __NAMESPACE__ . '\rest_excerpt_filter', 10, 2 );

	$additional_fields = [
		'link'      => get_permalink( $post ),
		'edit_link' => get_edit_post_link( $post ),
		'excerpt'   => $filtered_excerpt,
		'thumbnail' => $img_data,
		'cat_field' => $cat_field_slug,
	];

	$post_data = array_merge(
		$post->to_array(),
		$custom,
		$additional_fields
	);
	return $post_data;
}

/**
 * Get data for images assoicated with a post and return as an array.
 *
 * @param WP_POST | int $post The post.
 */
function object_image_data( $post ) {
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	$images      = get_object_image_attachments( $post->ID );
	$image_sizes = get_intermediate_image_sizes();

	$associated_image_data = [];
	foreach ( $images as $image_id => $sort_order ) {
		$image_post = get_post( $image_id );
		$image_data = [];

		$image_data['title']       = $image_post->post_title;
		$image_data['caption']     = $image_post->post_excerpt;
		$image_data['description'] = $image_post->post_content;
		$image_data['alt']         = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

		foreach ( $image_sizes as $size_slug ) {
			$image_data[ $size_slug ] = wp_get_attachment_image_src( $image_id, $size_slug );
		}
		$image_data['full'] = wp_get_attachment_image_src( $image_id, 'full' );
		$associated_image_data[ $image_id ] = $image_data;
	}

	return $associated_image_data;
}

/**
 * Options for /images routes.
 */
function images_routes_args() {
	return (
		[
			[
				'methods'  => 'GET',
				'args'     =>
					[
						'id' =>
							[
								'validate_callback' => function( $param, $request, $key ) {
									return is_numeric( $param );
								},
							],
					],
				'callback' => function ( $request ) {
					return object_image_data( $request['id'] );
				},
			],
			[
				'methods'  => 'POST',
				'args'     =>
					[
						'id' =>
							[
								'validate_callback' => function( $param, $request, $key ) {
									return ( is_numeric( $param ) && get_post_status( $param ) !== false );
								},
							],
					],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'callback' => function ( $request ) {
					return set_object_image_box_attachments( $request['images'], $request['id'] );
				},
			],
		]
	);
}

