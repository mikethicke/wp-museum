<?php
/**
 * Registers REST routes and endpoints.
 *
 * REST root: /wp-json/wp-museum/v1
 *
 * ## Objects ##
 * /wp-json/wp-museum/v1/<object type>/              Data for objects with post type <object type>.
 * /wp-json/wp-museum/v1/<object type>/<post id>     Data for specific object.
 * /wp-json/wp-museum/v1/all/                        Data for all museum objects, regardless of type.
 * /wp-json/wp-museum/v1/all/<post id>               Data for specific object.
 * /wp-json/wp-musuem/v1/<object type>/custom        Data for public fields for <object type>.
 *
 * ## Kinds ##
 * /wp-json/wp-musuem/v1/mobject_kinds               Data for object kinds
 * /wp-json/wp-museum/v1/mobject_kinds/<object type> Data for a specific kind with <object type>.
 *
 * ## Collections ##
 * /wp-json/wp-museum/v1/collections                 Data for all museum collections.
 * /wp-json/wp-museum/v1/collections/<post id>       Data for a specific collection.
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

					$posts = get_posts(
						[
							'post_status' => 'publish',
							'paged'       => $paged,
							'post_type'   => $kind->type_name,
						]
					);
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
		 * /wp-json/wp-musuem/v1/<object type>/custom - Data for public fields for <object type>.
		 */
		foreach ( $kinds as $kind ) {
			register_rest_route(
				REST_NAMESPACE,
				$kind->type_name . '/custom',
				[
					'methods'  => 'GET',
					'callback' => function() use ( $kind ) {
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

				if ( ! isset( $paged ) || empty( $paged ) ) {
					$paged = 1;
				}
				$posts = get_posts(
					[
						'post_status' => 'publish',
						'paged'       => $paged,
						'post_type'   => $kind_type_list,
					]
				);
				foreach ( $posts as $post ) {
					$custom      = get_post_custom( $post->ID );
					$post_data[] = array_merge( $post->to_array(), $custom );
				}
				return $post_data;
			},
		]
	);

	/**
	 * /wp-json/wp-museum/v1/all/<post id> - Data for specific object.
	 */
	register_rest_route(
		'wp-museum/v1',
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
	 * /wp-json/wp-musuem/v1/mobject_kinds - Data for object kinds
	 */
	register_rest_route(
		'wp-museum/v1',
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

				$posts = get_posts(
					[
						'post_status' => 'publish',
						'paged'       => $paged,
						'post_type'   => WPM_PREFIX . 'collection',
					]
				);
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

	if ( has_post_thumbnail( $post->ID ) ) {
		$attach_id = get_post_thumbnail_id( $post->ID );
	} else {
		$attachments = get_object_image_attachments( $post->ID );
		if ( count( $attachments ) > 0 ) {
			reset( $attachments );
			$attach_id = key( $attachments );
		}
	}

	if ( isset( $attach_id ) ) {
		$img_data = wp_get_attachment_image_src( $attach_id, 'thumb' );
	} else {
		$img_data = [];
	}

	$additional_fields = [
		'link'      => get_permalink( $post ),
		'excerpt'   => get_the_excerpt( $post ),
		'thumbnail' => $img_data,
	];

	$post_data = array_merge(
		$post->to_array(),
		$custom,
		$additional_fields
	);
	return $post_data;
}
