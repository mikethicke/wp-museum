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

		$options = [
			'type'         => $this->kind->type_name,
			'label'        => $this->kind->label,
			'label_plural' => $this->kind->label . 's',
			'description'  => $this->kind->description,
			'menu_icon'    => 'dashicons-archive',
			'hierarchical' => true,
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
			],
		];

		$this->object_post_type           = new CustomPostType( $options );
		$this->object_post_type->supports = [ 'title', 'thumbnail', 'author', 'editor' ];
		$this->object_post_type->add_taxonomy( 'category' );

		$this->fields                          = get_mobject_fields( $this->kind->kind_id );
		$this->object_post_type->custom_fields = $this->fields;
	}

	/**
	 * Display the fields table for object post types (callback).
	 */
	public function display_fields_table() {
		global $wpdb;
		global $post;
		$custom = get_post_custom( $post->ID );
		wp_nonce_field( 'hvcVHYT8rAb6ADEwp9WjCumA[', 'wpm-fields-table-nonce' );
		echo '<input type="hidden" id="display_fields_table_input" name="display_fields_table" value="1" />';
		echo "<table class='wp-list-table widefat striped wpm-object' id='wpm-field-edit'>";
		foreach ( $this->fields as $field ) {
			?>
			<tr class='wpm-object-help-text'><td colspan=2><?php echo esc_html( wp_unslash( $field->help_text ) ); ?></td></tr>
			<tr><td class="wpm-object-field-label"><label title="<?php echo esc_html( wp_unslash( $field->help_text ) ); ?>"><?php echo esc_html( wp_unslash( $field->name ) ); ?> </label></td>
			<?php
			switch ( $field->type ) {
				case 'varchar':
					echo (
						'<td><input type="text" ' .
						'name="' . esc_html( $field->slug ) . '" ' .
						'value="'
					);
					if ( isset( $custom[ $field->slug ][0] ) ) {
						echo esc_html( $custom[ $field->slug ][0] );
					}
					echo '" /></td>';
					break;
				case 'text':
					echo '<td><textarea name="' . esc_html( $field->slug ) . '">';
					if ( isset( $custom[ $field->slug ][0] ) ) {
						echo esc_html( $custom[ $field->slug ][0] );
					}
					echo '</textarea></td>';
					break;
				case 'tinyint':
					echo (
						'<td><input type="checkbox" ' .
						'name="' . esc_html( $field->slug ) . '" ' .
						'value="1"'
					);
					if ( isset( $custom[ $field->slug ][0] ) && $custom[ $field->slug ][0] != '0' ) {
						echo ' checked="checked"';}
					echo '/></td>';
					break;
				case 'date':
					$months = [];
					for ( $m = 1; $m <= 12; $m++ ) {
						// See: https://stackoverflow.com/questions/10829424/displaying-the-list-of-months-using-mktime-for-the-year-2012 .
						$month    = date( 'F', mktime( 0, 0, 0, $m, 1, date( 'Y' ) ) );
						$months[] = $month;
					}
					$days = [];
					for ( $d = 1; $d <= 31; $d++ ) {
						$days[] = $d;
					}
					?>
					<td><select name = "<?php echo esc_html( $field->slug ); ?>~month">
							<?php
							$month_num = 0;
							foreach ( $months as $month ) {
								$month_num++;
								?>
								<option value="<?php echo esc_html( $month_num ); ?>" 
								<?php
								if ( intval( date( 'm' ) ) === $month_num ) {
									echo 'selected = "selected"';}
								?>
								>
									<?php echo esc_html( $month ); ?>
								</option>
								<?php
							}
							?>
						</select>
						<select name = "<?php echo esc_html( $field->slug ); ?>~day">
							<?php
							foreach ( $days as $day ) {
								?>
								<option value="<?php echo esc_html( $day ); ?>" 
								<?php
								if ( intval( date( 'd' ) ) === $day ) {
									echo 'selected = "selected"';}
								?>
								>
									<?php echo esc_html( $day ); ?>
								</option>
								<?php
							}
							?>
						</select>
						<select name = "<?php echo esc_html( $field->slug ); ?>~year">
							<?php
							$current_year = intval( date( 'Y' ) );
							for ( $year = $current_year - 5; $year <= $current_year + 5; $year++ ) {
								?>
								<option value="<?php echo esc_html( $year ); ?>" 
								<?php
								if ( $current_year === $year ) {
									echo 'selected = "selected"';}
								?>
								>
									<?php echo esc_html( $year ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
					<?php
					break;
			} //switch
			?>
			</tr>
			<?php
		} //foreach ( $this->fields as $field )
		echo '</table>';
	}

	/**
	 * Saves fields table for object post types (callback).
	 *
	 * @param int $post_id Id of post being saved.
	 */
	public function save_fields_table( $post_id ) {
		if (
				! in_array( get_post_type( $post_id ), get_object_type_names(), true ) ||
				! isset( $_POST['display_fields_table'] )
			) {
			return;
		}
		$result = 1;
		$result = check_admin_referer( 'hvcVHYT8rAb6ADEwp9WjCumA[', 'wpm-fields-table-nonce' );
		if ( ! $result ) {
			wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
		}
		foreach ( $this->fields as $field ) {
			$old = get_post_meta( $post_id, $field->slug, true );
			if ( 'date' === $field->type ) {
				if (
					isset( $_POST[ $field->slug . '~day' ] ) &&
					isset( $_POST[ $field->slug . '~month' ] ) &&
					isset( $_POST[ $field->slug . '~year' ] )
				) {
					$year  = intval( $_POST[ $field->slug . '~year' ] );
					$month = intval( $_POST[ $field->slug . '~month' ] );
					$day   = intval( $_POST[ $field->slug . '~day' ] );

					update_post_meta( $post_id, $field->slug, strftime( '%Y-%m-%d', mktime( 0, 0, 0, $month, $day, $year ) ) );
				} elseif ( isset( $_POST[ $field->slug ] ) ) {
					update_post_meta( $post_id, $field->slug, trim( sanitize_key( wp_unslash( $_POST[ $field->slug ] ) ) ) );
				}
			} elseif ( 'tinyint' === $field->type ) {
				if ( ! isset( $_POST[ $field->slug ] ) ) {
					$_POST[ $field->slug ] = '0';
				}
				update_post_meta( $post_id, $field->slug, trim( sanitize_key( wp_unslash( $_POST[ $field->slug ] ) ) ) );
			} else {
				if ( isset( $_POST[ $field->slug ] ) && '' !== $_POST[ $field->slug ] && $old !== $_POST[ $field->slug ] ) {
					update_post_meta( $post_id, $field->slug, trim( sanitize_text_field( wp_unslash( $_POST[ $field->slug ] ) ) ) );
				} elseif ( isset( $_POST[ $field->slug ] ) && '' === $_POST[ $field->slug ] && $old ) {
					delete_post_meta( $post_id, $field->slug, $old );
				}
			}
		}

		/* Check if "Uncategorized" is checked, and if so set post's category to Uncategorized */
		$current_categories = wp_get_post_categories( $post_id );
		$new_categories     = [];
		$unident_cat        = get_category_by_slug( 'unidentified' );
		if ( get_post_meta( $post_id, 'unidentified', true ) == '1' ) {
			if ( ! in_array( $unident_cat->cat_ID, $current_categories, true ) ) {
				$current_categories[] = $unident_cat->cat_ID;
			}
			wp_set_post_categories( $post_id, $current_categories );
		} else {
			foreach ( $current_categories as $ccat ) {
				if ( $ccat !== $unident_cat->cat_ID ) {
					$new_categories[] = $ccat;
				}
			}
			wp_set_post_categories( $post_id, $new_categories );
		}
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
	 * Callback for displaying object post's image attachments.
	 */
	public function display_gallery_box() {
		global $post;
		$custom = get_post_custom( $post->ID );
		echo '<div>';
		echo "<div id='object-image-box'>";
		object_image_box_contents( $post->ID );
		echo '</div>';
		echo '<button type="button" id="insert-wpm-image-button" class="button"><span class="wp-media-buttons-icon"></span> Add Images</button></div>';
		wp_nonce_field( 'k2GgFprmdAAG2VgQDycpUg2V)', 'wpm-display-gallery-box-nonce' );
		echo '<input type="hidden" id="gallery_attach_ids">';
		if ( isset( $custom['gallery_attach_ids'] ) ) {
			echo esc_html( $custom['gallery_attach_ids'] );
		}
		echo '</input>';
	}

	/**
	 * Save the image gallery box (callback).
	 */
	public function save_gallery_box() {
		global $post;
		if (
			! in_array( get_post_type( $post ), get_object_type_names(), true ) ||
			empty( $_POST )
			) {
			return;
		}
		if ( ! check_admin_referer( 'k2GgFprmdAAG2VgQDycpUg2V)', 'wpm-display-gallery-box-nonce' ) ) {
			wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
		}
		if ( isset( $_POST['gallery_attach_ids'] ) && ! is_null( $post ) ) {
			/* check autosave */
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post->ID;
			}
			$custom = get_post_custom( $post->ID );
			update_post_meta( $post->ID, 'gallery_attach_ids', sanitize_text_field( wp_unslash( $_POST['gallery_attach_ids'] ) ) );
		}
	}

	/**
	 * Adds each public custom field to the REST api.
	 * Typically accessed at /wp-json/wp/v2/<object_slug>/<field_slug>
	 * add_action( 'rest_api_init', function() use( $fields, $object_type, $object_type_list )
	 */
	public function wpm_rest_custom_fields() {
		foreach ( $this->fields as $field ) {
			if ( $field->public ) {
				register_rest_field(
					$this->kind->type_name,
					$field->slug,
					array(
						'get_callback'    => function ( $object ) use ( $field ) {
							$custom_fields = get_post_custom( $object['id'] );
							if ( isset( $custom_fields[ $field->slug ] ) ) {
								return ( $custom_fields[ $field->slug ][0] );
							} else {
								return ( null );
							}

						},
						'update_callback' => null,
						'schema'          => null,
					)
				);
			}
		}

		// Adds thumbnail url and img attributes to the REST api.
		// Typically accessed at /wp-json/wp/v2/<object_id> [thumbnail_src].
		// Eg. /wp-json/wp/v2/wpm_instrument/7719/
		register_rest_field(
			$this->kind->type_name,
			'thumbnail_src',
			array(
				'get_callback' => function ( $object ) {
					if ( has_post_thumbnail( $object['id'] ) ) {
						$attach_id = get_post_thumbnail_id( $object['id'] );
					} else {
						$attachments = get_attached_media( 'image', $object['id'] );

						if ( count( $attachments ) > 0 ) {
							$attachment = reset( $attachments );
							$attach_id = $attachment->ID;
						}
					}
					if ( isset( $attach_id ) ) {
						return wp_get_attachment_image_src( $attach_id, 'thumb' );
					}
				},
			)
		);

		// Adds a list of the object post type's public custom fields to the REST api.
		// Typically accessed at /wp-json/wp-museum/v1/object_custom/<kind_slug>/ .
		// Eg: /wp-json/wp-museum/v1/object_custom/wpm_instrument/
		register_rest_route(
			'wp-museum/v1',
			'/object_custom/' . $this->kind->type_name . '/',
			array(
				'methods'  => 'GET',
				'callback' => function() {
					foreach ( $this->fields as $field ) {
						if ( $field->public ) {
							$filtered_fields[ $field->field_id ] = $field;
						}
					}
					return $filtered_fields;
				},
			)
		);
	}

	/**
	 * Register the object as custom post type.
	 */
	public function register() {

		// Create a MetaBox with the two above functions as callbacks.
		$fields_box = new MetaBox(
			$this->kind->type_name . '-fields',
			__( 'Fields', 'wp-museum' ),
			array( $this, 'display_fields_table' ),
			array( $this, 'save_fields_table' )
		);
		$this->object_post_type->add_custom_meta( $fields_box );

		// Creates a MetaBox displaying an object's child posts.
		$children_box          = new MetaBox(
			$this->kind->type_name . '-children',
			$this->kind->label . ' Parts',
			array( $this, 'display_object_children' )
		);
		$children_box->context = 'side';
		$this->object_post_type->add_custom_meta( $children_box );

		// Creates a MetaBox for displaying and manipulating object post's image gallery.
		$gallery_box = new MetaBox(
			$this->kind->type_name . '-gallery',
			$this->kind->label . ' Images',
			array( $this, 'display_gallery_box' ),
			array( $this, 'save_gallery_box' )
		);
		$this->object_post_type->add_custom_meta( $gallery_box );

		$this->object_post_type->register();

		add_action( 'rest_api_init', [ $this, 'wpm_rest_custom_fields' ] );

	}
}


