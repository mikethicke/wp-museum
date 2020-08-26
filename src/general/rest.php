<?php
/**
 * Registers REST routes and endpoints.
 *
 * REST root: /wp-json/wp-museum/v1
 *
 * ## General ##
 * /site_data                        Overview data for the site.
 * /admin_options                    Site-wide options.
 * /search                           For advanced search of objects.
 *
 * ## Objects ##
 * /<object type>/[?s=|<field>=]     Objects with post type <object type>.
 * /<object type>/<post id>          Specific object.
 * /<object type>/<post id>/images   Images associated with object.
 * /<object type>/<post id>/children Child objects of object.
 * /all/[?s=|<field>=]               All museum objects, regardless of type.
 * /all/<post id>                    Specific object.
 * /all/<post id>/images             Images associated with object.
 * /all/<post id>/children           Child objects of object.
 * /<object type>/fields             Public fields for <object type>.
 * /<object type>/fields_all         All fields for <object type>.
 *
 * ## Kinds ##
 * /mobject_kinds                  Object kinds
 * /mobject_kinds/<object type>    A specific kind with <object type>.
 *
 * ## Collections ##
 * /collections/[?s=]              All museum collections.
 * /collections/<post id>          A specific collection.
 * /collections/<post id>/objects  Objects associated with a collection.
 *
 * ## Remote Clients ##
 * /remote_clients                All remote clients
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

const DEFAULT_NUMBERPOSTS = -1;

/**
 * Register REST endpoints.
 */
function rest_routes() {
	/**
	 * /site_data                        Overview data for the site.
	 *
	 * @return Array Array of site data.
	 *
	 * [
	 *    'title'        => Title of site.
	 *    'description'  => Site tagline.
	 *    'url'          => Site URL.
	 *    'collections'  => List of available collections.
	 *    'object_count' => Total number of public objects.
	 * ]
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/site_data',
		[
			'methods' => 'GET',
			'permission_callback' => function() {
				return true;
			},
			'callback' => function() {
				return get_site_data();
			},
		]
	);

	/**
	 * /admin_options                    Site-wide options.
	 *
	 * Get and set site-wide options for the museum plugin. Can be read by
	 * authors+ and changed by administrators.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/admin_options',
		[
			[
				'methods' => 'GET',
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'callback' => __NAMESPACE__ . '\get_admin_options',
			],
			[
				'methods' => 'POST',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback' => __NAMESPACE__ . '\set_admin_options',
			],
		]
	);

	/**
	 * /search                           For advanced search of objects.
	 *
	 * Run an advanced search and return the result. Search parameters passed
	 * through POST request.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/search',
		[
			'methods' => 'POST',
			'permission_callback' => function() {
				return true;
			},
			'callback' => __NAMESPACE__ . '\do_advanced_search',
		]
	);

	/**
	 * /register_remote
	 *
	 * @return Array | WP_ERROR Array of site data or error if registration unsuccessful.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/register_remote',
		[
			'methods' => 'POST',
			'permission_callback' => function() {
				return true;
			},
			'callback' => __NAMESPACE__ . '\register_remote_client',
		]
	);

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
				'permission_callback' => function() {
					return true;
				},
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
						'numberposts'      => DEFAULT_NUMBERPOSTS,
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
				'permission_callback' => function() {
					return true;
				},
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
		 * /wp-json/wp-museum/v1/<object type>/<post id>/children Child objects of object.
		 */
		register_rest_route(
			REST_NAMESPACE,
			'/' . $kind->type_name . '/(?P<id>[\d]+)/children',
			child_objects_routes_args()
		);

		/**
		 * /wp-json/wp-musuem/v1/<object type>/fields - Data for public fields for <object type>.
		 */
		register_rest_route(
			REST_NAMESPACE,
			$kind->type_name . '/fields',
			[
				'methods'  => 'GET',
				'permission_callback' => function() {
					return true;
				},
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

		/**
		 * /wp-json/wp-musuem/v1/<object type>/fields_all - Data for all fields for <object type>.
		 */
		register_rest_route(
			REST_NAMESPACE,
			$kind->type_name . '/fields_all',
			[
				[
					'methods'  => 'GET',
					'callback' => function( $request ) use ( $kind ) {
						$fields = get_mobject_fields( $kind->kind_id );
						$filtered_fields = [];
						foreach ( $fields as $field ) {
							if ( $field->public || current_user_can( 'edit_posts' ) ) {
								$filtered_fields[ $field->field_id ] = $field;
							}
						}
						return $filtered_fields;
					},
					'permission_callback' => function () {
						return true;
					},
				],
				[
					'methods' => 'POST',
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
					'callback' => function( $request ) {
						/**
						 * We're going to get an array of fields to update.
						 * We instantiate each item as an MObjectField, and
						 * then update those fields in the database.
						 */
						global $wpdb;
						$field_data = json_decode( $request->get_body(), true );
						$success = true;
						$failed_queries = [];
						foreach ( $field_data as $field_id => $field_object ) {
							$mobject_field = MObjectField::from_rest( $field_object );
							if ( isset( $field_object['delete'] ) && true === $field_object['delete'] ) {
								$mobject_field->delete_from_db();
							} else if ( false === $mobject_field->save_to_db() ) {
								$success = false;
								$failed_queries[] = $wpdb->last_query;
							};
						}
						return $success;
					},
				],
			]
		);

		/**
		 * /wp-json/wp-museum/v1/mobject_kinds/<object type> - Data for a specific kind with <object type>.
		 */
		register_rest_route(
			REST_NAMESPACE,
			'/mobject_kinds/' . $kind->type_name,
			[
				'methods' => 'GET',
				'callback' => function() use ( $kind ) {
					return $kind->to_rest_array();
				},
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	} // foreach( $kinds as $kind ).

	/**
	 * /wp-json/wp-museum/v1/all/ - Data for all museum objects, regardless of type.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/all',
		[
			'methods'  => 'GET',
			'permission_callback' => function() {
				return true;
			},
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
					'numberposts'      => DEFAULT_NUMBERPOSTS,
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
			'permission_callback' => function() {
				return true;
			},
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
	 * /wp-json/wp-museum/v1/all/<post id>/children Child objects of object.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/all/(?P<id>[\d]+)/children/',
		child_objects_routes_args()
	);

	/**
	 * /wp-json/wp-musuem/v1/mobject_kinds - Data for object kinds
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/mobject_kinds/',
		[
			[
				'methods'  => 'GET',
				'callback' => function() {
					$kinds_array = [];
					$kinds = get_mobject_kinds();
					foreach ( $kinds as $kind ) {
						if ( current_user_can( 'edit_posts' ) ) {
							$kinds_array[] = $kind->to_rest_array();
						} else {
							$kinds_array[] = $kind->to_public_rest_array();
						}
					}
					return $kinds_array;
				},
				'permission_callback' => function() {
					return true;
				},
			],
			[
				'methods' => 'POST',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback' => function ( $request ) {
					global $wpdb;
					$updated_kinds = json_decode( $request->get_body(), false );
					$success = true;
					if ( $updated_kinds ) {
						foreach ( $updated_kinds as $kind_data ) {
							$kind = new ObjectKind( $kind_data );
							if ( isset( $kind_data->delete ) && true === $kind_data->delete ) {
								$kind->delete_from_db();
							} else if ( ! $kind->save_to_db() ) {
								$success = false;
							};
						}
					}
					return $success;
				},
			],
		]
	);

	/**
	 * /wp-json/wp-museum/v1/collections - Data for all museum collections.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/collections/',
		[
			'methods' => 'GET',
			'permission_callback' => function() {
				return true;
			},
			'callback' => function( $request ) {
				if ( ! empty( $request->get_param( 'slug' ) ) ) {
					return get_collection_data( $request );
				}

				$paged = $request->get_param( 'page' );
				if ( ! isset( $paged ) || empty( $paged ) ) {
					$paged = 1;
				}

				$args  = [
					'post_status'      => 'publish',
					'paged'            => $paged,
					'post_type'        => WPM_PREFIX . 'collection',
					'suppress_filters' => false,
					'numberposts'      => DEFAULT_NUMBERPOSTS,
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
			'permission_callback' => function() {
				return true;
			},
			'args'    =>
				[
					'id' =>
						[
							'validate_callback' => function( $param, $request, $key ) {
								return is_numeric( $param );
							},
						],
				],
			'callback' => __NAMESPACE__ . '\get_collection_data',
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
			'permission_callback' => function() {
				return true;
			},
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

	/**
	 * /remote_clients                All remote clients
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/remote_clients',
		[
			[
				'methods' => 'GET',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback' => function () {
					return RemoteClient::get_all_clients_assoc_array();
				},
			],
			[
				'methods' => 'POST',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback' => __NAMESPACE__ . '\update_clients_from_rest',
			],
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
			if (
				isset( $fields[ $field_slug ] ) &&
				( $fields[ $field_slug ]->public || current_user_can( 'edit_posts' ) )
			) {
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

	$default_post_data = $post->to_array();
	$default_post_data['post_content'] = apply_filters( 'the_content', get_the_content( null, false, $post ) );
	$post_data = array_merge(
		$default_post_data,
		$custom,
		$additional_fields
	);
	return $post_data;
}

/**
 * Combine post data for array of posts.
 *
 * @param [WP_Post] $posts Array of WP_Post objects.
 */
function combine_post_data_array( $posts ) {
	return array_map( __NAMESPACE__ . '\combine_post_data', $posts );
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
 * Get data for a specific collection.
 *
 * @param WP_REST_Request $request REST request.
 */
function get_collection_data( $request ) {
	if ( ! isset( $request['id'] ) ) {
		$slug = $request->get_param( 'slug' );
		if ( $slug ) {
			$posts = get_posts(
				[
					'numberposts' => 1,
					'post_type'   => WPM_PREFIX . 'collection',
					'post_status' => 'publish',
					'name'        => sanitize_text_field( $slug ),
				]
			);
			if ( 1 === count( $posts ) ) {
				$post_id = $posts[0]->ID;
			} else {
				return null;
			}
		} else {
			return null;
		}
	} else {
		$post_id = $request['id'];
	}

	$post_data = combine_post_data( $post_id );
	$associated_objects = get_associated_object_ids( $post_id );
	$post_data['associated_objects'] = $associated_objects;
	return $post_data;
}

/**
 * Options for /images routes.
 */
function images_routes_args() {
	return (
		[
			[
				'methods'  => 'GET',
				'permission_callback' => function() {
					return true;
				},
				'args'     =>
					[
						'id' =>
							[
								'validate_callback' => function( $param, $request, $key ) {
									return is_numeric( $param );
								},
							],
					],
				'callback' => function( $request ) {
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
				'permission_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
				'callback' => function( $request ) {
					return set_object_image_box_attachments( $request['images'], $request['id'] );
				},
			],
		]
	);
}

/**
 * Args for object/child endpoints.
 */
function child_objects_routes_args() {
	return (
		[
			'methods' => 'GET',
			'permission_callback' => function() {
				return true;
			},
			'args'     =>
				[
					'id' =>
						[
							'validate_callback' => function( $param, $request, $key ) {
								return is_numeric( $param );
							},
						],
				],
			'callback' => function( $request ) {
				$meta = get_post_meta( $request['id'], WPM_PREFIX . 'child_objects', true );
				$data_array = [];
				if ( $meta ) {
					foreach ( $meta as $kind_id => $object_ids ) {
						$data_array[ $kind_id ] = [];
						if ( $object_ids ) {
							foreach ( $object_ids as $object_id ) {
								$data_array[ $kind_id ][] = combine_post_data( $object_id );
							}
						}
					}
				}
				return $data_array;
			},
		]
	);
}

/**
 * Basic data about the site.
 */
function get_site_data() {
	$collections = get_collections();

	$collection_names = array_map(
		function( $collection ) {
			return $collection->post_title;
		},
		$collections
	);

	$object_posts = get_object_posts( null, 'publish' );

	return (
		[
			'title'        =>
				sanitize_text_field(
					html_entity_decode(
						get_bloginfo( 'name' ),
						ENT_QUOTES | ENT_XML1,
						'UTF-8'
					)
				),
			'description'  =>
				sanitize_text_field(
					html_entity_decode(
						get_bloginfo( 'description' ),
						ENT_QUOTES | ENT_XML1,
						'UTF-8'
					)
				),
			'url'          => esc_url( get_bloginfo( 'url' ) ),
			'collections'  => $collection_names,
			'object_count' => count( $object_posts ),
		]
	);
}

$admin_options = [
	'allow_remote_requests'       => 'bool',
	'allow_unregistered_requests' => 'bool',
	'rest_authorized_domains'     => 'string',
];

/**
 * Returns museum site wite options as associative array.
 */
function get_admin_options() {
	global $admin_options;

	$admin_data = [];
	foreach ( $admin_options as $admin_option => $option_type ) {
		$option_value = get_option( $admin_option );
		if ( false === $option_value ) {
			$option_value = null;
		} elseif ( 'bool' === $option_type ) {
			$option_value = (bool) intval( $option_value );
		} elseif ( 'int' === $option_type ) {
			$option_value = intval( $option_value );
		}
		$admin_data[ $admin_option ] = $option_value;
	}
	return $admin_data;
}

/**
 * Sets museum site-wide options from REST request.
 *
 * @param WP_REST_Request $request A REST POST request json encoded.
 */
function set_admin_options( $request ) {
	global $admin_options;

	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error(
			'permission-denied',
			'You do not have permission to access this resource',
			[ 'status' => 403 ]
		);
	}

	$option_values = $request->get_json_params();
	foreach ( $admin_options as $admin_option => $option_type ) {
		if (
			isset( $option_values[ $admin_option ] ) &&
			! is_null( $option_values[ $admin_option ] )
		) {
			update_option( $admin_option, $option_values[ $admin_option ] );
		}
	}
	return true;
}


