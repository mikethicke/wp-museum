<?php
/**
 * Registers a Gutenberg block for entering data into Objects.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers Gutenberg block to edit museusm objects.
 */
function register_object_meta_block() {
	$kind   = get_kind_from_typename( $post_type );
	$fields = get_mobject_fields( $kind->kind_id );

	$attributes = [];
	foreach ( $fields as $field ) {
		$field_name = $field->slug;
		if ( 'flag' === $field->type ) {
			$type = 'boolean';
		} elseif ( 'multiple' === $field->type ) {
			$type = 'array';
		} elseif ( 'measure' === $field->type ) {
			$type = 'array';
		} else {
			$type = 'string';
		}
		$attributes[ $field_name ] = [
			'type'   => $type,
			'source' => 'meta',
			'meta'   => $field->slug,
		];
		if ( 'measure' === $field->type ) {
			$attributes[ $field_name ]['items'] = 'number';
		}
	}
	$attributes['fieldErrors'] = [
		'type'    => 'object',
		'default' => null,
	];

	register_block_type(
		'wp-museum/object-meta-block',
		[
			'render_callback' => __NAMESPACE__ . '\render_object_post_type_block',
			'attributes'      => $attributes,
		]
	);
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\register_object_meta_block' );

/**
 * Renders the object custom fields, image gallery, and collection breadcrumbs
 * on the frontend.
 *
 * @param Array $attributes The block attributes.
 */
function render_object_post_type_block( $attributes ) {
	global $post;

	if ( ! in_the_loop() || ! is_singular( get_object_type_names() ) ) {
		return '';
	}

	$display_options = get_customizer_settings()[ WPM_PREFIX . 'mobject_style' ];
	$object_kind     = kind_from_type( $post->post_type );
	$fields          = get_mobject_fields( $object_kind->kind_id );

	// Breadcrumb links to collections containing this object.
	$breadcrumb_html = '';
	if ( $display_options['collections_breadcrumbs'] ) {
		$breadcrumb_html = 
			"<div class='wpm-obj-categories'>" .
			object_collections_string( $post->ID, $display_options['collections_separator'] ) .
			'</div>';
	}

	// Custom fields.
	$custom_fields_html = '';
	foreach ( $fields as $field ) {
		$meta_value = get_post_meta( $post->ID, $field->slug, true );
		// Public can only view fields marked as "public".
		if ( ! $field->public && ! current_user_can( 'read_private_posts' ) ) {
			continue;
		}
		if ( ! $field->public ) {
			$priv = ' ' . WPM_PREFIX . 'obj-private-field';
		} else {
			$priv = '';
		}
		$custom_fields_html .= "<div class='" . WPM_PREFIX . "field-text $priv'>";
		if (
				'flag' === $field->type &&
				'list' === $display_options['yes_no_display']
			) {
			if ( '1' === $meta_value ) {
				$bool_yes_fields[] = $field->name;
			}
		} else {
			if ( ! is_string( $meta_value ) || strlen( $meta_value ) > 39 ) {
				$field_text = '<div class="' . WPM_PREFIX . 'field-label-div">' . $field->name . ':</div>';
			} else {
				$field_text = '<span class="' . WPM_PREFIX . 'field-label">' . $field->name . ':</span> ';
			}
			if ( 'flag' === $field->type ) {
				if ( '1' === $meta_value ) {
					$field_text .= 'Yes';
				} else {
					$field_text .= 'No';
				}
			} elseif ( 'multiple' === $field->type ) {
				$field_text .= implode( '; ', $meta_value );
			} elseif ( 'measure' === $field->type ) {
				if ( count( $meta_value ) > 0 ) {
					if ( 1 === $field->dimensions['n'] ) {
						$field_text .= $meta_value[0];
					} else {
						for ( $i = 0; $i < $field->dimensions['n']; $i++ ) {
							$field_text .= $field->dimensions['labels'][ $i ] . ': ' . $meta_value[ $i ] . ' ' . $field->units . ' ';
						}
					}
				}
			} else {
				$field_text .= $meta_value;
			}
			$field_text = \html_entity_decode( $field_text );
			$custom_fields_html .= apply_filters( 'the_content', $field_text );
		}
		$custom_fields_html .= '</div>';
	}
	if ( count( $bool_yes_fields ) > 0 ) {
		$custom_fields_html .= '<ul>';
		foreach ( $bool_yes_fields as $field ) {
			$custom_fields_html .= "<li class='$priv'>$field</li>";
		}
		$custom_fields_html .= '</ul>';
	}

	// Object children.
	$object_children_html = '';
	$child_kinds = $object_kind->get_children();
	$child_ids = get_post_meta( $post->ID, WPM_PREFIX . 'child_objects', true );
	if ( $child_kinds && $child_ids ) {
		foreach ( $child_kinds as $child_kind ) {
			if (
				isset( $child_ids[ $child_kind->kind_id ] ) &&
				count( $child_ids[ $child_kind->kind_id ] ) > 0
			) {
				$object_children_html .= "<div class='child-kind'>";
				$object_children_html .= "<h2>$child_kind->label_plural</h2>";
				foreach ( $child_ids[ $child_kind->kind_id ] as $child_id ) {
					$child_post = get_post( $child_id );
					$thumbnail  = get_object_thumbnail( $child_post->ID );
					$link       = get_permalink( $child_post );
					$title      = get_the_title( $child_post );

					// phpcs:disable
					ob_start();
					?>
					<div class='child-object'>
						<img class='child-object-thumb' src='<?= $thumbnail[0] ?>' />
						<div class='child-object-content'>
							<h3><?= $title ?></h3>
							<?= get_the_excerpt( $child_post ) ?>
							<div class='child-view-link'>
								<a href='<?= $link ?>'>View</a>
							</div>
						</div>
					</div>
					<?php
					// phpcs:enable
					$object_children_html = ob_get_contents();
					ob_end_clean();
				}
				$object_children_html .= '</div>';
			}
		}
	}

	// phpcs:disable
	ob_start();
	?>
	<div class = 'wpm-objectposttype-block'>
		<?= $breadcrumb_html ?>
		<div class = 'wpm-objectposttype-content'>
			<div
				class = 'wpm-objectposttype-image-gallery <?= $display_options['imgage_gallery_position'] ?>'
				data-post-ID = '<?= $post->ID ?>'
			>
			</div>
			<?= $custom_fields_html ?>
			<?= $object_children_html ?>
		</div>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
	// phpcs:enable
}
