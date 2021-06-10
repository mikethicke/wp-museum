<?php
/**
 * Adds quick browse tables to all museum object types. Quick browse tables allow administrators
 * to quickly see all objects of a particular type along with publication status and summary
 * data. The quick browse page is accessed through the <Object Type>|Quick Browse menu.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Adds quick browse page to all object types.
 */
function add_quick_browse() {
	$mobject_kinds = get_mobject_kinds();

	foreach ( $mobject_kinds as $object_type ) {
		$type_name = $object_type->type_name;
		add_submenu_page(
			"edit.php?post_type=$type_name",
			'Quick Browse',
			'Quick Browse',
			WPM_PREFIX . 'edit_others_objects',
			$object_type->name . '-quick-browse',
			__NAMESPACE__ . '\quick_browse'
		);
	}
}

/**
 * Display the quick browse table.
 */
function quick_browse() {
	global $wpdb;
	if ( ! isset( $_GET['post_type'] ) ) {
		wp_die( esc_html__( 'quick_browse: post_type needs to be set to display Quick Browse table.', 'wp-museum' ) );
	}
	$type_name   = sanitize_key( $_GET['post_type'] );
	$object_type = kind_from_type( $type_name );
	$self_url    = esc_url(
		wp_unslash( $_SERVER['PHP_SELF'] ) . "?post_type=$type_name&page={$object_type->name}-quick-browse"
	);
	$csv_url     = add_query_arg(
		[
			'post_type' => $type_name,
			'page'      => $object_type->name . '-quick-browse',
			'action'    => 'csv_upload',
			'qb-nonce'  => wp_create_nonce( 'VQsJrvZ6V2rPjLM^4m>m' ),
		],
		wp_unslash( $_SERVER['PHP_SELF'] )
	);

	display_csv_upload_form( $csv_url );

	if ( isset( $_GET['action'] ) ) {
		if ( ! check_admin_referer( 'VQsJrvZ6V2rPjLM^4m>m', 'qb-nonce' ) ) {
			wp_die( esc_html__( 'Failed nonce check.', 'wp-museum' ) );
		}
		echo process_uploaded_csv();
	}

	$fields = get_mobject_fields( $object_type->kind_id );
	if ( ! count( $fields ) ) {
		esc_html_e( 'No fields selected for quick browse in object admin.', 'wp-museum' );
		return;
	}

	echo "<div class='wrap'><table class='widefat'>
            <thead><tr>";

	if ( isset( $_GET['sort_col'] ) ) {
		$sort_col = sanitize_key( $_GET['sort_col'] );
	} else {
		$sort_col = reset( $fields )->slug;
	}

	if ( isset( $_GET['sort_dir'] ) ) {
		$sort_dir = sanitize_key( $_GET['sort_dir'] );
	} else {
		$sort_dir = 'asc';
	}

	echo '<th>';
	if ( 'post_title' === $sort_col ) {
		if ( 'asc' === $sort_dir ) {
			echo "<a href='" . esc_html( $self_url ) . "&sort_col=post_title&sort_dir=desc'>Name<span class='dashicons dashicons-arrow-down'></span>";
		} else {
			echo "<a href='" . esc_html( $self_url ) . "&sort_col=post_title&sort_dir=asc'>Name<span class='dashicons dashicons-arrow-up'></span>";
		}
	} else {
		echo "<a href='" . esc_html( $self_url ) . "&sort_col=post_title&sort_dir=asc'>Name";
	}
	echo '</a></th>';

	foreach ( $fields as $field ) {
		if ( $field->quick_browse ) {
			echo '<th>';
			if ( $sort_col === $field->slug ) {
				if ( 'asc' === $sort_dir ) {
					echo "<a href='" . esc_html( $self_url ) . '&sort_col=' . esc_html( $sort_col ) . "&sort_dir=desc'>" . esc_html( $field->name ) . "<span class='dashicons dashicons-arrow-down'></span>";
				} else {
					echo "<a href='" . esc_html( $self_url ) . '&sort_col=' . esc_html( $sort_col ) . "&sort_dir=asc'>" . esc_html( $field->name ) . "<span class='dashicons dashicons-arrow-up'></span>";
				}
			} else {
				$col = $field->slug;
				echo "<a href='" . esc_html( $self_url ) . '&sort_col=' . esc_html( $col ) . "&sort_dir=asc'>" . esc_html( $field->name );
			}
			echo '</a></th>';
		}
	}
	// export_csv_button() and import_csv_buton() escape output.
	echo(
		'<th>' .
		export_csv_button( $object_type->kind_id ) .
		' ' .
		import_csv_button( $object_type->kind_id ) .
		'</th><th></th><th></th>'
	);
	echo '</tr></thead><tbody>';

	$args    = [
		'numberposts' => -1,
		'post_type'   => $type_name,
		'post_status' => 'any',
	];
	$objects = get_posts( $args );

	wpm_sort_by_field( $objects, $sort_col, $sort_dir );

	foreach ( $objects as $object ) {
		$custom = get_post_custom( $object->ID );

		$edit_url  = admin_url( "post.php?post={$object->ID}&action=edit" );
		$view_url  = get_permalink( $object->ID );
		$row_class = 'wpm-quick-row-' . $object->post_status;
		echo "<tr class='" . esc_html( $row_class ) . "'>";
		echo "<td><a href='" . esc_html( $edit_url ) . "'>" . esc_html( $object->post_title ) . '</a></td>';
		foreach ( $fields as $field ) {
			if ( $field->quick_browse ) {
				if ( isset( $custom[ $field->slug ] ) ) {
					$field_value = $custom[ $field->slug ][0];
				} else {
					$field_value = '(none)';
				}

				echo "<td><a href='" . esc_html( $edit_url ) . "'>" . esc_html( $field_value ) . '</a></td>';
			}
		}
		echo "<td><a href='" . esc_html( $view_url ) . "'>View</a><td>";
		echo '<td>';
		if ( count( get_object_image_attachments( $object->ID ) ) > 0 ) {
			echo '<span class="dashicons dashicons-format-gallery"></span>';
		}
		echo '</td>';
		echo '</tr>';
	}

	echo '</tbody></table></div>';
}

/**
 * Callback function for sorting quick browse table by a column.
 *
 * @param Array     $target_array   The posts to be sorted.
 * @param string    $sort_col       Slug of field to sort by.
 * @param string    $sort_dir       The direction to sort by (asc or desc).
 */
function wpm_sort_by_field( &$target_array, $sort_col, $sort_dir ) {
	if ( 'post_title' === $sort_col ) {
		$sort_field = null;
	} else {
		$fields = get_mobject_fields( kind_from_post( $target_array[0] )->kind_id );
		foreach ( $fields as $field ) {
			if ( $field->slug === $sort_col ) {
				$sort_field = $field;
				break;
			}
		}
	}
	if ( 'asc' === $sort_dir ) {
		$rv = 1;
	} else {
		$rv = -1;
	}
	usort(
		$target_array,
		function( $a, $b ) use ( $sort_field, $rv, $sort_col ) {
			if ( 'post_title' === $sort_col ) {
				$a_field_val = $a->post_title;
				$b_field_val = $b->post_title;
			} else {
				$a_custom = get_post_custom( $a->ID );
				$b_custom = get_post_custom( $b->ID );

				if ( isset( $a_custom[ $sort_col ] ) ) {
					$a_field_val = $a_custom[ $sort_col ][0];
				} else {
					return -1 * $rv;
				}

				if ( isset( $b_custom[ $sort_col ] ) ) {
					$b_field_val = $b_custom[ $sort_col ][0];
				} else {
					return $rv;
				}

				if ( isset( $sort_field->field_schema ) && ! empty( $sort_field->field_schema ) ) {
					$a_matches = [];
					$b_matches = [];
					$pattern   = '/' . $sort_field->field_schema . '/';
					if ( preg_match( $pattern, $a_field_val, $a_matches ) && preg_match( $pattern, $b_field_val, $b_matches ) ) {
						$a_named_capture_keys = array_filter( array_keys( $a_matches ), 'is_string' );
						$b_named_capture_keys = array_filter( array_keys( $b_matches ), 'is_string' );
						$named_capture_keys   = array_intersect( $a_named_capture_keys, $b_named_capture_keys );
						if ( count( $named_capture_keys ) > 0 ) {
							// Named capture groups.
							sort( $named_capture_keys );
							foreach ( $named_capture_keys as $key ) {
								if ( is_numeric( $a_matches[ $key ] ) && is_numeric( $b_matches[ $key ] ) ) {
									if ( intval( $a_matches[ $key ] ) > intval( $b_matches[ $key ] ) ) {
										return $rv;
									} elseif ( intval( $a_matches[ $key ] ) < intval( $b_matches[ $key ] ) ) {
										return -1 * $rv;
									}
								} elseif ( strcasecmp( $a_matches[ $key ], $b_matches[ $key ] ) > 0 ) {
									return $rv;
								} elseif ( strcasecmp( $a_matches[ $key ], $b_matches[ $key ] ) < 0 ) {
									return -1 * $rv;
								}
							}
						} else {
							// Sequential capture groups.
							$limit = min( count( $a_matches ), count( $b_matches ) );
							for ( $i = 1; $i < $limit; $i++ ) {
								if ( is_numeric( $a_matches[ $i ] ) && is_numeric( $b_matches[ $i ] ) ) {
									if ( intval( $a_matches[ $i ] ) > intval( $b_matches[ $i ] ) ) {
										return $rv;
									} elseif ( intval( $a_matches[ $i ] ) < intval( $b_matches[ $i ] ) ) {
										return -1 * $rv;
									}
								} elseif ( strcasecmp( $a_matches[ $i ], $b_matches[ $i ] ) > 0 ) {
									return $rv;
								} elseif ( strcasecmp( $a_matches[ $i ], $b_matches[ $i ] ) < 0 ) {
									return -1 * $rv;
								}
							}
						}
						if ( count( $a_matches ) > count( $b_matches ) ) {
							return 1 * $rv;
						} elseif ( count( $a_matches ) < count( $b_matches ) ) {
							return -1 * $rv;
						} else {
							return 0;
						}
					}
				}
			}

			if ( is_numeric( $a_field_val ) && is_numeric( $b_field_val ) ) {
				if ( intval( $a_field_val ) > intval( $b_field_val ) ) {
					return $rv;
				} elseif ( $a_field_val > $b_field_val ) {
					return -1 * $rv;
				} else {
					return 0;
				}
			} else {
				if ( strcasecmp( $a_field_val, $b_field_val ) > 0 ) {
					return $rv;
				} elseif ( strcasecmp( $a_field_val, $b_field_val ) < 0 ) {
					return -1 * $rv;
				} else {
					return 0;
				}
			}

		}
	);
}
