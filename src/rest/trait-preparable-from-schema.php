<?php
/**
 * Trait for controllers that prepare and sanitze responses from schema.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Trait for controllers that prepare and sanitze responses from schema.
 */
trait Preparable_From_Schema {
	/**
	 * Sanitize data based on expected type, based on schema.
	 *
	 * @param string|array|null $data         The data to be sanitized.
	 * @param array             $data_schema  The schema for that data.
	 *   string|array   $data_schema['type']  The expected type or types of the data.
	 *   array          $data_schema['items'] If type is array, then items should contain array of
	 *                                        schema for each item.
	 *
	 * @return string|array|null The sanitized data.
	 */
	private static function sanitize_from_type( $data, $data_schema ) {
		$new_data = [];

		if ( is_array( $data_schema['type'] ) ) {
			$type = $data_schema['type'];
		} else {
			$type = [ $data_schema['type'] ];
		}

		if ( in_array( 'null', $type, true ) && ! $data ) {
			$new_data = null;
		} elseif ( in_array( 'boolean', $type, true ) ) {
			if ( is_numeric( $data ) ) {
				$new_data = (bool) intval( $data );
			} else {
				$new_data = (bool) $data;
			}
		} elseif ( in_array( 'integer', $type, true ) && is_numeric( $data ) ) {
			$new_data = intval( $data );
		} elseif ( in_array( 'array', $type, true ) && is_array( $data ) ) {
			if (
				! is_array( $data_schema['items'] ) ||
				0 === count( $data ) ||
				0 === count( $data_schema['items'] )
			) {
				$new_data = [];
			} else {
				$data_count = count( $data );
				for ( $data_index = 0; $data_index < $data_count; $data_index++ ) {
					if ( count( $data_schema['items'] ) <= $data_index ) {
						break;
					}
					$new_data[ $data_index ] = self::sanitize_from_type(
						$data[ $data_index ],
						$data_schema['items'][ $data_index ]
					);
				}
			}
		} elseif ( in_array( 'string', $type, true ) ) {
			if ( 'url' === $data_schema['format'] ) {
				$new_data = esc_url( $data );
			} else {
				$new_data = sanitize_text_field( $data );
			}
		} else {
			$new_data = null;
		}

		return $new_data;
	}

	/**
	 * Prepares item for response, by checking against schema and sanitizing
	 * appropriately.
	 *
	 * @param Array           $item    Unsanitized item for response.
	 * @param WP_REST_Request $request Request object.
	 * @param Array|null      $schema  Schema against which to sanitize, or null for default schema.
	 *
	 * @return WP_REST_Response Sanitized response object.
	 */
	public function prepare_item_for_response( $item, $request, $schema = null ) {
		$data = [];
		if ( ! $schema ) {
			$schema = $this->get_item_schema();
		}

		foreach ( $schema['properties'] as $property => $prop_data ) {
			if ( isset( $item[ $property ] ) ) {
				$data[ $property ] = sanitize_from_type( $item[ $property ], $prop_data );
			}
		}
		return rest_ensure_response( $data );
	}
}
