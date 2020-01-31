<?php
/**
 * Creates museum object post types.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Creates the custom museum object post types.
 *
 * Iterates through the user-created museum objects and creates a custom post type
 * for each. Each object has a table of custom fields that are presented to users
 * on the edit post screen. The bulk of this function is devoted to creating and
 * saving that form. Object posts are hierarchical--users can create child objects
 * from the edit post page. Objects and their custom fields are accessible to the
 * WordPress REST api if they are marked as 'public' in the object admin page.
 * Objects have image galleries using ajax to add and manipulate image attachments.
 *
 * @see object_admin.php
 */
function create_mobject_post_types() {
	global $wpdb;
	$kind_kinds_table = $wpdb->prefix . WPM_PREFIX . 'mobject_kinds';
	$kind_rows       = $wpdb->get_results( "SELECT * FROM $kind_kinds_table" ); //phpcs:ignore

	$kind_type_list = array();

	foreach ( $kind_rows as $kind_row ) {
		$new_object_post_type = new ObjectPostType( new ObjectKind( $kind_row ) );
		$new_object_post_type->register();
		$kind_type_list[] = $new_object_post_type->kind->type_name;
	}

	// Adds a list of the museum objects to the REST api.
	// Typically accessed at /wp-json/wp-museum/v1/mobject_kinds/ .
	add_action(
		'rest_api_init',
		function() use ( $kind_type_list ) {
			register_rest_route(
				'wp-museum/v1',
				'/mobject_kinds/',
				array(
					'methods'  => 'GET',
					'callback' => function() {
						return $kind_type_list;
					},
				)
			);
		}
	);

	// Creates a REST endpoint that includes posts for all museum objects.
	add_action(
		'rest_api_init',
		function () use ( $kind_type_list ) {
			register_rest_route(
				'wp-museum/v1',
				'/all',
				[
					'methods'  => 'GET',
					'callback' => function ( $request ) use ( $kind_type_list ) {
						$post_data = [];
						$paged     = $request->get_param( 'page' );

						if ( isset( $paged ) && ! empty( $paged ) ) {
							$paged = 1;
						}
						$posts = get_posts(
							[
								'paged'     => $paged,
								'post_type' => $kind_type_list,
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
						$post      = get_post( $request['id'] );
						$custom    = array_map(
							function ( $i ) {
								return $i[0];
							},
							get_post_custom( $post->ID )
						);
						$post_data = array_merge( $post->to_array(), $custom );
						return $post_data;
					},
				]
			);
		}
	);
}

/**
 * Adds a link to the parent post for child posts.
 *
 * @param \WP_POST $post A post of some museum object post type.
 */
function add_object_parent_link( \WP_POST $post ) {
	if ( substr( $post->post_type, 0, strlen( WPM_PREFIX ) ) !== WPM_PREFIX ) {
		return;
	}
	$parent_id = wp_get_post_parent_id( $post->ID );
	if ( ! $parent_id ) {
		return;
	}
	$parent = get_post( $parent_id );
	if ( isset( $parent ) ) {
		echo "<div class='postbox' style='font-size:1.2em; padding:10px; margin-bottom:10px;'>Parent Object: " . esc_html( $parent->post_title ) . "(<a href='post.php?post=" . esc_html( $parent->ID ) . "&action=edit'>Edit</a>)</div>";
	}
}

/**
 * Adds a div to top of object edit pages for the reporting of problems, and
 * report problems based on SESSION variable.
 *
 * Called at admin_notices.
 */
function add_object_problem_div() {
	global $post;
	if ( ! empty( $post ) && in_array( $post->post_type, get_object_type_names(), true ) ) {
		echo "<div id='wpm-post-check' class='error'";
		if ( ! empty( $_SESSION[ WPM_PREFIX . 'object_problems' ] ) ) {
			echo '>';
			echo esc_html( $_SESSION[ WPM_PREFIX . 'object_problems' ] );
			unset( $_SESSION[ WPM_PREFIX . 'object_problems' ] );
		} else {
			echo "style='display:none'>";
		}
		echo '</div>';
	}
}

/**
 * Checks that objects meet requirements set in Object Admin on saving or
 * publishing an object post. Sets Session variable for display of problems
 * by add_objects_problem_div(). If there are problems and post is to be
 * published, prevent post from publishing.
 *
 * Called at transition_post_status.
 *
 * @param string  $new_status The new status of the post. Eg. 'publish'.
 * @param string  $old_status The old status of the post. Eg. 'draft'.
 * @param WP_POST $post The post.
 */
function check_object_post_on_publish( $new_status, $old_status, $post ) {
	if ( empty( $post ) || ! in_array( $post->post_type, get_object_type_names(), true ) ) {
		return;
	}
	$problems      = check_object_post( $post->ID );
	$problems_text = '';
	if ( count( $problems ) > 0 ) {
		$kind = kind_from_type( $post->post_type );
		if ( $new_status !== $old_status && 'publish' === $new_status && $kind->strict_checking ) {
			$post->post_status = $old_status;
			wp_update_post( $post );
		}
		$problems_text .= '<ul>';
		foreach ( $problems as $problem ) {
			$problems_text .= "<li>$problem</li>";
		}
		$problems_text .= '</ul>';
	}
	$_SESSION[ WPM_PREFIX . 'object_problems' ] = $problems_text;
}

/**
 * Filter to add link text to content containing text patterns matching
 * schema of object's id field.
 *
 * @param string $content The content to filter.
 * @return string The content with links added.
 *
 * @see class-objectkind::ObjectKind::cat_field_id
 */
function link_objects_by_id( $content ) {
	global $post;
	$kinds = get_mobject_kinds();
	foreach ( $kinds as $kind ) {
		if ( empty( $kind->cat_field_id ) ) {
			break;
		}
		$id_field = get_mobject_field( $kind->kind_id, $kind->cat_field_id );
		if ( empty( $id_field->field_schema ) ) {
			break;
		}
		$pattern       = '/' . stripslashes( $id_field->field_schema ) . '/';
		$pattern       = preg_replace( '/<.*?>/', ':', $pattern );
		$matches       = array();
		$plain_content = wp_strip_all_tags( $content );
		preg_match( $pattern, $plain_content, $matches );
		foreach ( $matches as $match ) {
			$args  = [
				'post_type'   => $kind->type_name,
				'post_status' => 'publish',
				'meta_key'    => $id_field->slug,
				'meta_value'  => $match,
			];
			$posts = get_posts( $args );
			if ( ! empty( $posts ) && $posts[0]->ID !== $post->ID ) {
				$post_url = get_permalink( $posts[0] );
				$link     = "<a href='$post_url'>{$match}</a>";
				$content  = str_replace( $match, $link, $content );
			}
		}
	}

	return $content;
}
