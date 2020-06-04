<?php
/**
 * Class for registering museum object custom post types.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Class for registering museum object custom post types.
 */
class ObjectPostType {

	/**
	 * The museum object kind associated with this post type.
	 *
	 * @var ObjectKind $kind
	 */
	public $kind;

	/**
	 * Fields associated with this post type.
	 *
	 * @var [MObjectFields] $fields
	 */
	public $fields;

	/**
	 * Post type object associated with this post type.
	 *
	 * @var CustomPostType $object_post_type
	 */
	public $object_post_type;

	/**
	 * Init object.
	 *
	 * @param ObjectKind $kind Database row from objects table.
	 */
	public function __construct( $kind ) {
		global $wpdb;
		$this->kind  = $kind;

		if ( $this->kind->label_plural ) {
			$label_plural = $this->kind->label_plural;
		} else {
			$label_plural = $this->kind->label . 's';
		}

		$options = [
			'type'         => $this->kind->type_name,
			'label'        => $this->kind->label,
			'label_plural' => $label_plural,
			'description'  => $this->kind->description,
			'menu_icon'    => museum_icon(),
			'hierarchical' => false,
			'options'      => [
				'capabilities' => [
					'edit_posts'           => WPM_PREFIX . 'edit_objects',
					'edit_others_posts'    => WPM_PREFIX . 'edit_others_objects',
					'publish_posts'        => WPM_PREFIX . 'publish_objects',
					'read_private_posts'   => WPM_PREFIX . 'read_private_objects',
					'delete_posts'         => WPM_PREFIX . 'delete_objects',
					'edit_published_posts' => WPM_PREFIX . 'edit_published_objects',
				],
				'map_meta_cap' => true,
				'template'    => [
					[ 'core/paragraph', [ 'placeholder' => 'A general description of the object...' ] ],
					[ 'wp-museum/object-meta-block' ],
					[ 'wp-museum/object-image-attachments-block' ],
					[ 'wp-museum/child-objects-block' ],
				],
				'template_lock' => 'all',
			],
		];

		if ( ! ( is_null( $this->kind->parent_kind_id ) || ' ' === $this->kind->parent_kind_id ) ) {
			$options['options']['show_in_menu'] = false;
		}

		$this->object_post_type = new CustomPostType( $options );

		$this->object_post_type->supports = [ 'title', 'thumbnail', 'author', 'editor', 'custom-fields' ];
		$this->object_post_type->add_taxonomy( 'category' );

		$this->fields = get_mobject_fields( $this->kind->kind_id );

		$this->object_post_type->custom_fields = $this->fields;
	}

	/**
	 * Callback for displaying object post's children.
	 */
	public function display_object_children() {
		global $post;
		$children = get_children(
			[
				'numberposts' => -1,
				'post_status' => 'any',
				'post_type'   => $this->object_post_type->options['type'],
				'post_parent' => $post->ID,
			]
		);
		echo '<table>';
		foreach ( $children as $child ) {
			$permalink = get_permalink( $child->ID );
			echo "<tr><td><a href='post.php?post=" . esc_html( $child->ID ) . "&action=edit'>" . esc_html( $child->post_title ) . '</a></td></tr>';
		}
		echo '</table><br />';
		echo "<button type='button' class='button button-large' onclick='new_obj(" . esc_html( $post->ID ) . ")'>New Part</button>";
	}

	/**
	 * Add fields to default WordPress search.
	 *
	 * Note: All custom post types get added to search already.
	 *
	 * @see CustomPostType::add_to_search().
	 * @param WP_QUERY $query The query.
	 */
	public function add_fields_to_search( $query ) {
		if ( $query->is_search() && $query->is_main_query() && ! empty( $query->get( 's' ) ) ) {
			$meta_query = [ 'relation' => 'OR' ];
			foreach ( $this->fields as $field ) {
				if ( $field->public || current_user_can( 'read_private_posts' ) ) {
					$meta_query[] = [
						'key'     => $field->slug,
						'value'   => $query->get( 's' ),
						'compare' => 'LIKE',
					];
				}
			}
			if ( count( $meta_query ) > 1 ) {
				$combined_query = $query->get( 'combined_query' );
				if ( empty( $combined_query ) ) {
					$combined_query = [
						'args'  => [ $query->query ],
						'union' => 'UNION',
					];
				}
				$combined_query['args'][] = [
					'post_type'   => $this->kind->type_name,
					'post_status' => 'publish',
					'meta_query'  => $meta_query,
				];
				$query->set( 'combined_query', $combined_query );
			}
		}
	}

	/**
	 * Register fields as meta fields.
	 */
	public function register_fields_meta() {
		if ( current_user_can( 'edit_posts' ) ) {
			foreach ( $this->fields as $field ) {
				$show_in_rest = true;
				if ( 'flag' === $field->type ) {
					$type = 'boolean';
				} elseif ( 'multiple' === $field->type ) {
					$type = 'array';
					$show_in_rest = [
						'schema' => [
							'type' => 'array',
							'items' => [
								'type' => 'string',
							],
						],
					];
				} elseif ( 'measure' === $field->type ) {
					$type = 'array';
					$show_in_rest = [
						'schema' => [
							'type' => 'array',
							'items' => [
								'type' => 'number',
							],
						],
					];
				} else {
					$type = 'string';
				}

				register_post_meta(
					$this->object_post_type->options['type'],
					$field->slug,
					[
						'type'          => $type,
						'description'   => $field->name,
						'single'        => true,
						'show_in_rest'  => $show_in_rest,
						'auth_callback' => function () {
							return current_user_can( 'edit_posts' );
						},
					]
				);
			}
		}
	}

	/**
	 * Register parent and child meta fields.
	 */
	public function register_relationship_meta() {
		register_post_meta(
			$this->object_post_type->options['type'],
			WPM_PREFIX . 'child_objects',
			[
				'type'          => 'object',
				'description'   => 'Child objects',
				'single'        => true,
				'show_in_rest'  => [
					'schema' => [
						'type'  => 'object',
						'properties' => [],
						'additionalProperties' => [
							'type'  => 'array',
							'items' => [
								'type' => 'number',
							],
						],
					],
				],
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);

		register_post_meta(
			$this->object_post_type->options['type'],
			WPM_PREFIX . 'child_objects_str',
			[
				'type'          => 'string',
				'description'   => 'Child objects',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);

		register_post_meta(
			$this->object_post_type->options['type'],
			'wpm_parent_object',
			[
				'type'          => 'number',
				'description'   => 'Parent post',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	/**
	 * Register the object as custom post type.
	 */
	public function register() {
		// Creates a MetaBox displaying an object's child posts.
		$children_box          = new MetaBox(
			$this->kind->type_name . '-children',
			$this->kind->label . ' Parts',
			array( $this, 'display_object_children' )
		);
		$children_box->context = 'side';
		$this->object_post_type->add_custom_meta( $children_box );

		register_post_meta(
			$this->object_post_type->options['type'],
			'wpm_gallery_attach_ids',
			[
				'type' => 'array',
				'description' => 'Associated Images',
				'single' => true,
				'show_in_rest' => [
					'schema' => [
						'type' => 'array',
						'items' => [
							'type' => 'number',
						],
					],
				],
				'auth_callback'    => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);

		/**
		 * This is here just to make the original save. Can be removed
		 * once WP saves array meta correctly.
		 */
		register_post_meta(
			$this->object_post_type->options['type'],
			'wpm_gallery_attach_ids_string',
			[
				'type' => 'string',
				'description' => 'Associated Images String',
				'single' => true,
				'show_in_rest' => true,
				'auth_callback'    => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		add_action( 'pre_get_posts', array( $this, 'add_fields_to_search' ) );

		$this->object_post_type->register();
		$this->register_fields_meta();
		$this->register_relationship_meta();
	}
}


