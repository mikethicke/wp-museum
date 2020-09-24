<?php
/**
 * Functions for displaying museum objects, collections, etc on the front end.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

$object_content_filter_in_progress = false; // Stops infinite recursion of filters.
/**
 * Filter content for single WPM object to display custom fields.
 *
 * @param string $content Post content to be filtered.
 *
 * @link https://wordpress.stackexchange.com/questions/96660/custom-post-type-plugin-where-do-i-put-the-template
 */
function object_content_filter( $content ) {
	global $wpm_object_content_filter_in_progress;
	global $wpdb;
	global $post;

	if (
			in_the_loop() &&
			is_singular( get_object_type_names() ) &&
			! $wpm_object_content_filter_in_progress
		) {
		$wpm_object_content_filter_in_progress = true;

		$object_kind     = kind_from_type( $post->post_type );
		$fields          = get_mobject_fields( $object_kind->kind_id );
		$bool_yes_fields = [];

		$images = get_object_image_attachments( $post->ID );

		$display_options = get_customizer_settings()[ WPM_PREFIX . 'mobject_style' ];

		if ( $display_options['collections_breadcrumbs'] ) {
			$content .=
				"<div class='wpm-obj-categories'>" .
				object_collections_string( $post->ID, $display_options['collections_separator'] ) .
				'</div>';
		}

		// Image gallery.
		$image_gallery_content = '';
		if ( $display_options['display_image_gallery'] ) {
			$image_gallery_content .= "<div id='" . WPM_PREFIX . "obj-gallery'>";
			foreach ( $images as $image_id => $sort_order ) {
				$image_thumbnail = wp_get_attachment_image_src( $image_id, 'object_gallery_thumb' )[0];
				$image_full      = wp_get_attachment_image_src( $image_id, 'large' )[0];
				if (
					'left' === $display_options['image_gallery_position'] ||
					'right' === $display_options['image_gallery_position']
				) {
					$element = 'div';
				} else {
					$element = 'span';
				}
				$image_gallery_content .= '<' . $element . " class='" . WPM_PREFIX . "obj-image'>";
				$image_gallery_content .= "<a data-fancybox='gallery' href='$image_full'><img src='$image_thumbnail'></a>";
				$image_gallery_content .= '</' . $element . '>';
			}
			$image_gallery_content .= '</div>';
		}

		if ( 'bottom' !== $display_options['image_gallery_position'] ) {
			$content .= $image_gallery_content;
		}

		// Custom fields.
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
			$content .= "<div class='" . WPM_PREFIX . "field-text $priv'>";
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
				$content .= apply_filters( 'the_content', $field_text );
			}
			$content .= '</div>';
		}
		if ( count( $bool_yes_fields ) > 0 ) {
			$content .= '<ul>';
			foreach ( $bool_yes_fields as $field ) {
				$content .= "<li class='$priv'>$field</li>";
			}
			$content .= '</ul>';
		}

		// Children.
		$child_kinds = $object_kind->get_children();
		$child_ids = get_post_meta( $post->ID, WPM_PREFIX . 'child_objects', true );
		if ( $child_kinds && $child_ids ) {
			foreach ( $child_kinds as $child_kind ) {
				if (
					isset( $child_ids[ $child_kind->kind_id ] ) &&
					count( $child_ids[ $child_kind->kind_id ] ) > 0
				) {
					$content .= "<div class='child-kind'>";
					$content .= "<h2>$child_kind->label_plural</h2>";
					foreach ( $child_ids[ $child_kind->kind_id ] as $child_id ) {
						$child_post = get_post( $child_id );
						$thumbnail  = get_object_thumbnail( $child_post->ID );
						$link       = get_permalink( $child_post );
						$title      = get_the_title( $child_post );

						$content .= "<div class='child-object'>";
						$content .= "<img class='child-object-thumb' src='{$thumbnail[0]}' />";
						$content .= "<div class='child-object-content'>";
						$content .= "<h3>$title</h3>";
						$content .= get_the_excerpt( $child_post );
						$content .= "<div class='child-view-link'><a href='$link'>View</a></div>";
						$content .= '</div>';
						$content .= '</div>';
					}
					$content .= '</div>';
				}
			}
		}
		if ( 'bottom' === $display_options['image_gallery_position'] ) {
			$content .= $image_gallery_content;
		}
	}

	$object_content_filter_in_progress = false;
	return $content;
}

$collection_content_filter_in_progress = false;
/**
 * Filter content for single collection to display custom fields.
 *
 * @param string $content Post content to be filtered.
 */
function collection_content_filter( $content ) {
	global $post;
	global $collection_content_filter_in_progress;

	if ( $collection_content_filter_in_progress ) {
		return $content;
	}
	$collection_content_filter_in_progress = true;

	if (
			isset( $_GET['show_description'] ) ||
			isset( $_GET['show_all'] ) ||
			isset( $_GET['page'] )
	) {
		$success = false;
		if ( isset( $_GET['collection_content_filter'] ) ) {
			$nonce = sanitize_key( $_GET['collection_content_filter'] );
			$success = wp_verify_nonce( $nonce, 'r6vmJjB7E]Ds]NZ8pqYu' );
		}
		if ( ! $success ) {
			wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
		}
	}

	if ( in_the_loop() && is_singular( WPM_PREFIX . 'collection' ) ) {
		$custom          = get_post_custom( $post->ID );
		$display_options = get_customizer_settings()[ WPM_PREFIX . 'collection_style' ];

		$self_link = home_url( add_query_arg( null, null ) );
		$self_link = add_query_arg( 'collection_content_filter', wp_create_nonce( 'r6vmJjB7E]Ds]NZ8pqYu' ), $self_link );

		$associated_object_query = query_associated_objects();

		/*
		 * Paging for collections with large number of objects.
		 */
		if ( $associated_object_query->max_num_pages > 1 ) {
			$content .= "<div class='paging'>";
			if ( isset( $_GET['show_all'] ) && 1 === intval( $_GET['show_all'] ) ) {
				$self_link = add_query_arg( 'show_all', '-1', $self_link );
				$content .= "<a href='" . esc_url( $self_link ) . "'>Show Paged</a>";
				$associated_object_query = query_associated_objects(
					'publish',
					null,
					true,
					null
				);
			} else {
				$self_link = add_query_arg( 'show_all', '1', $self_link );
				$self_link = add_query_arg( 'page', '1', $self_link );
				$paged     = $associated_object_query->get( 'paged', 1 );

				$content .= paginate_links(
					[
						'base'    => '?%_%',
						'format'  => 'page=%#%',
						'current' => max( 1, $paged ),
						'total'   => $associated_object_query->max_num_pages,
					]
				);
				$content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$content .= "<a href='" . esc_url( $self_link ) . "'>Show All</a>";
			}
			$content .= '</div>'; // paging.
		}

		/*
		 * Museum object listing.
		 */
		$content .= "<div class='" . WPM_PREFIX . "object_listing'>";

		$associated_objects = $associated_object_query->posts;
		$column_counter     = 1;
		$grid_columns       = [];
		foreach ( $associated_objects as $object ) {
			$content .= object_row( $object );
		}
		$content .= '</div>';
	}
	$collection_content_filter_in_progress = false;
	return $content;
}

/**
 * Generate HTML for a single museum object row for display in archive
 * pages and collections.
 *
 * @param WP_POST $post The source post.
 * @return string HTML for the row.
 */
function object_row( $post ) {
	$custom          = get_post_custom( $post->ID );
	$display_options = get_customizer_settings()[ WPM_PREFIX . 'collection_style' ];
	$content         = '';

	$content .= "<div class='" . WPM_PREFIX . "object-row'>";
	if ( $display_options['list_show_thumbnail'] ) {
		$content .= "<div class='" . WPM_PREFIX . "object-row-thumbnail'>";
		$content .= "<a href='" . get_the_permalink( $post->ID ) . "'>";
		if ( has_post_thumbnail( $post->ID ) ) {
			$content .= get_the_post_thumbnail( $post->ID, WPM_PREFIX . 'list_thumb' );
		} else {
			$first_thumbnail_attach = first_thumbnail( $post->ID );
			if ( is_array( $first_thumbnail_attach ) && ! empty( $first_thumbnail_attach ) ) {
				$content .= (
					'<img src="' . $first_thumbnail_attach[0] . '" ' .
					'width="' . $first_thumbnail_attach[1] . '" ' .
					'height="' . $first_thumbnail_attach[2] . '" />'
				);
			}
		}
		$content .= '</a>';
		$content .= '</div>'; // object-row-thumbnail.
	}
	$content .= "<div class='" . WPM_PREFIX . "object-row-textwrapper'>";
	$content .= '<div class="' . WPM_PREFIX . 'object-row-title">';
	$content .= '<h4><a href="' . get_the_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></h4>';
	$content .= '</div>'; // object-row-title.
	$content .= '<div class="' . WPM_PREFIX . 'object-row-excerpt">';
	if ( $display_options['excerpt_max_length'] ) {
		add_filter( 'excerpt_length', __NAMESPACE__ . '\mobject_excerpt_length' );
	}
	$content .= '<a href="' . get_the_permalink( $post->ID ) . '">' . get_the_excerpt( $post->ID ) . '</a>';
	if ( $display_options['excerpt_max_length'] ) {
		remove_filter( 'excerpt_length', __NAMESPACE__ . '\mobject_excerpt_length' );
	}
	$content .= '</div>'; // object-row-excerpt.
	$content .= '</div>'; // object-row-textwrapper.
	$content .= '</div>'; // object-row.
	return $content;
}

/**	
 * Filter to make excerpts for museum objects if none exists.
 *
 * @param string   $excerpt_text Text of the excerpt as generated by get_the_excerpt.
 * @param \WP_POST $post The post.
 */
function mobject_excerpt_filter( $excerpt_text, $post ) {
	if ( $excerpt_text ) {
		return $excerpt_text;
	}
	return wp_trim_excerpt( '', $post );
}

/**
 * Filter to Override default excerpt length if excerpt length for object rows
 * is set in customizer.
 *
 * @param int $length The default lenght.
 */
function mobject_excerpt_length( $length ) {
	if ( is_admin() ) {
		return $length;
	}
	$display_options = get_customizer_settings()[ WPM_PREFIX . 'collection_style' ];
	if ( $display_options['excerpt_max_length'] ) {
		return $display_options['excerpt_max_length'];
	}
	return $length;
}

/**
 * Generate image sizes for post row thumbnails.
 */
function generate_image_sizes() {
	$display_options = get_customizer_settings()[ WPM_PREFIX . 'collection_style' ];

	add_image_size(
		WPM_PREFIX . 'list_thumb',
		$display_options['list_image_max_width'],
		$display_options['list_image_max_height']
	);
}

/**
 * Returns the first thumbnail of a post.
 *
 * @param int $post_id The post's id.
 */
function first_thumbnail( $post_id ) {
	$attachments = get_attached_media( 'image', $post_id );

	if ( $attachments ) {
		$attachment       = reset( $attachments );
		$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'thumb' );
		return $image_attributes;
	}
	return '';
}

/**
 * Generate CSS text from an array of selectors, attributes, and values.
 *
 * @param [string => [string => string] ] $css_array CSS array - [selector => [attribute => value ] ].
 * @return string CSS text.
 */
function css_from_array( $css_array ) {
	$css_text = '';
	foreach ( $css_array as $selector => $style ) {
		$css_text .= ' ' . $selector . ' { ';
		foreach ( $style as $attribute => $value ) {
			$css_text .= $attribute . ': ' . $value . '; ';
		}
		$css_text .= ' }';
	}
	return $css_text;
}

/**
 * Insert CSS for museum objects into header.
 */
function object_css() {
	if ( is_singular( get_object_type_names() ) ) {
		$display_options = get_customizer_settings()[ WPM_PREFIX . 'mobject_style' ];
		$styles          = [];

		if ( 'bold' === $display_options['field_label_font_weight'] ) {
			$styles[ '.' . WPM_PREFIX . 'field-label-div' ]['font-weight'] = 'bold';
			$styles[ '.' . WPM_PREFIX . 'field-label' ]['font-weight']     = 'bold';
		}
		if ( $display_options['field_label_color'] ) {
			$styles[ '.' . WPM_PREFIX . 'field-label-div' ]['color'] = $display_options['field_label_color'];
			$styles[ '.' . WPM_PREFIX . 'field-label' ]['color']     = $display_options['field_label_color'];
		}
		if ( $display_options['field_text_color'] ) {
			$styles[ '.' . WPM_PREFIX . 'field-text' ]['color'] = $display_options['field_text_color'];
		}
		if ( 0 < $display_options['image_max_width'] ) {
			$styles[ '.' . WPM_PREFIX . 'obj-image img' ]['max-width'] = $display_options['image_max_width'] . 'px';
		}
		if ( 0 < $display_options['image_max_height'] ) {
			$styles[ '.' . WPM_PREFIX . 'obj-image img' ]['max-height'] = $display_options['image_max_height'] . 'px';
		}
		if ( 0 < $display_options['image_border_width'] ) {
			$styles[ '.' . WPM_PREFIX . 'obj-image img' ]['border-width'] = $display_options['image_border_width'] . 'px';
			$styles[ '.' . WPM_PREFIX . 'obj-image img' ]['border-style'] = 'solid';
		}
		if ( $display_options['image_border_color'] ) {
			$styles[ '.' . WPM_PREFIX . 'obj-image img' ]['border-color'] = $display_options['image_border_color'];
		}
		if ( 'right' === $display_options['image_gallery_position'] || 'left' === $display_options['image_gallery_position'] ) {
			$styles[ '#' . WPM_PREFIX . 'obj-gallery' ]['width']       = $display_options['image_max_width'];
			$styles[ '.' . WPM_PREFIX . 'obj-image' ]['margin-top']    = $display_options['image_margin'] . 'px';
			$styles[ '.' . WPM_PREFIX . 'obj-image' ]['margin-bottom'] = $display_options['image_margin'] . 'px';
		}
		switch ( $display_options['image_gallery_position'] ) {
			case 'right':
				$styles[ '#' . WPM_PREFIX . 'obj-gallery' ]['float']       = 'right';
				$styles[ '#' . WPM_PREFIX . 'obj-gallery' ]['margin-left'] = $display_options['image_gallery_margin'] . 'px';
				$styles[ '.' . WPM_PREFIX . 'field-text' ]['margin-right'] =
					(
						$display_options['image_gallery_margin'] +
						$display_options['image_max_width'] +
						$display_options['image_border_width']
					) . 'px !important';
				break;
			case 'left':
				$styles[ '#' . WPM_PREFIX . 'obj-gallery' ]['float']        = 'left';
				$styles[ '#' . WPM_PREFIX . 'obj-gallery' ]['margin-right'] = $display_options['image_gallery_margin'] . 'px';
				$styles[ '.' . WPM_PREFIX . 'field-text' ]['margin-left']   =
					(
						$display_options['image_gallery_margin'] +
						$display_options['image_max_width'] +
						$display_options['image_border_width']
					) . 'px !important';
				break;
			case 'top':
			case 'bottom':
				$styles[ '.' . WPM_PREFIX . 'obj-image' ]['margin-left']  = $display_options['image_margin'] . 'px';
				$styles[ '.' . WPM_PREFIX . 'obj-image' ]['margin-right'] = $display_options['image_margin'] . 'px';
				break;
		}

		/**
		 * Child object div
		 */
		$styles['.child-object']['display']            = 'flex';
		$styles['.child-object']['flex-direction']     = 'row';
		$styles['.child-object']['flex-wrap']          = 'nowrap';
		$styles['.child-object-thumb']['width']        = '100px';
		$styles['.child-object-thumb']['height']       = '100px';
		$styles['.child-object-thumb']['min-width']    = '100px';
		$styles['.child-object-thumb']['object-fit']   = 'cover';
		$styles['.child-object-thumb']['margin-right'] = '1em';

		echo '<style type="text/css">';
		echo esc_html( css_from_array( $styles ) );
		echo '</style>';
	}
}

function collection_css() {
	if ( is_singular( WPM_PREFIX . 'collection' ) ) {
		$display_options = get_customizer_settings()[ WPM_PREFIX . 'collection_style' ];
		$styles          = [];

		if ( ! $display_options['list_color_1'] ) {
			$display_options['list_color_1'] = 'transparent';
		}
		if ( ! $display_options['list_color_2'] ) {
			$display_options['list_color_2'] = 'transparent';
		}
		if ( $display_options['alternate_list_color'] ) {
			$styles[ '.' . WPM_PREFIX . 'object-row:nth-child(odd)' ]['background-color']  = $display_options['list_color_1'];
			$styles[ '.' . WPM_PREFIX . 'object-row:nth-child(even)' ]['background-color'] = $display_options['list_color_2'];
		} else {
			$styles[ '.' . WPM_PREFIX . 'object-row' ]['background-color'] = $display_options['list_color_1'];
		}
		$styles[ '.' . WPM_PREFIX . 'object-row a' ]['text-decoration']       = 'none !important';
		$styles[ '.' . WPM_PREFIX . 'object-row a' ]['color']                 = 'inherit !important';
		$styles[ '.' . WPM_PREFIX . 'object-row-thumbnail' ]['float']         = $display_options['list_thumbnail_position'];
		$styles[ '.' . WPM_PREFIX . 'object-row-thumbnail' ]['max-width']     = $display_options['list_image_max_width'] . 'px';
		$styles[ '.' . WPM_PREFIX . 'object-row-thumbnail' ]['max-height']    = $display_options['list_image_max_height'] . 'px';
		$styles[ '.' . WPM_PREFIX . 'object-row-thumbnail' ]['margin']        = '20px';
		$styles[ '.' . WPM_PREFIX . 'object-row-textwrapper' ]
				[ 'margin-' . $display_options['list_thumbnail_position'] ] =
					( $display_options['list_image_max_width'] + 20 ) . 'px';
		$styles[ '.' . WPM_PREFIX . 'object-row h4' ]['clear']              = 'none';
		if ( $display_options['excerpt_line_height'] ) {
			$styles[ '.' . WPM_PREFIX . 'object-row-excerpt' ]['line-height'] = $display_options['excerpt_line_height'] . 'em';
		}
		if ( $display_options['excerpt_font_size'] ) {
			$styles[ '.' . WPM_PREFIX . 'object-row-excerpt' ]['font-size'] = $display_options['excerpt_font_size'] . 'em';
		}

		$styles[ '.' . WPM_PREFIX . 'object-row' ]['padding']    = '10px';
		$styles[ '.' . WPM_PREFIX . 'object-row' ]['min-height'] = ( $display_options['list_image_max_height'] + 40 ) . 'px';

		echo '<style type="text/css">';
		echo esc_html( css_from_array( $styles ) );
		echo '</style>';
	}
}
