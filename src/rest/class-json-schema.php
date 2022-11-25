<?php
/**
 * JSON_Schema class.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

/**
 * Represents a JSON schema.
 *
 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/
 *
 * @since 0.3.0
 */
class JSON_Schema implements \JsonSerializable {
	/**
	 * An object representing the JSON schema.
	 *
	 * @since 0.3.0
	 *
	 * @var object $schema The schema.
	 */
	private ?object $schema = null;

	/**
	 * Constructor
	 *
	 * @since 0.3.0
	 *
	 * @param array|object|string $schema_source The source from which to construct schema.
	 */
	public function __construct( $schema_source ) {
		$this->set_schema( $schema_source );
	}

	/**
	 * Sets the schema given from an array.
	 *
	 * @since 0.3.0
	 *
	 * @param array|object|string $schema_source The source from which to construct schema.
	 */
	public function set_schema( $schema_source ) {
		if ( is_string( $schema_source ) ) {
			$this->schema = json_decode( $schema_source, false );
		} elseif ( is_array( $schema_source ) ) {
			$this->schema = json_decode( wp_json_encode( $schema_source ), false );
		} else {
			// Assume $schema_source is an object.
			$this->schema = $schema_source;
		}
	}

	/**
	 * Returns the schema as an array.
	 *
	 * @since 0.3.0
	 *
	 * @return object The schema.
	 */
	public function get_schema() {
		return $this->schema;
	}

	/**
	 * Returns list of properties in the schema.
	 *
	 * @since 0.3.0
	 *
	 * @return array List of properties in the schema.
	 */
	public function get_schema_property_list() : array {
		if ( is_null( $this->schema ) || ! property_exists( $this->schema, 'properties' ) ) {
			return [];
		}
		return array_keys( get_object_vars( $this->schema->properties ) );
	}

	/**
	 * Implements JsonSerializable interface.
	 *
	 * @since 0.3.0
	 *
	 * @return mixed The schema.
	 */
	public function jsonSerialize() {
		return $this->get_schema();
	}

	/**
	 * Checks whether data conforms to schema.
	 *
	 * @since 0.3.0
	 *
	 * @param mixed $data JSON-encoded data to be validated.
	 *
	 * @return bool Whether the data conforms to the schema.
	 */
	public function validate_data( $data ) : bool {
		$validator = new Validator();

		if ( is_string( $data ) ) {
			$data_object = json_decode( $data );
		} else {
			$data_object = $data;
		}

		if ( ! is_array( $data_object ) ) {
			$data_array = [ $data_object ];
		} else {
			$data_array = $data_object;
		}

		foreach ( $data_array as $data_item ) {
			$validator->validate( $data_item, $this->schema, Constraint::CHECK_MODE_COERCE_TYPES );
			if ( ! $validator->isValid() ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Validates and sanitizes data from schema.
	 *
	 * @since 0.3.0
	 *
	 * @param mixed $data JSON-encoded data to be validated and sanitized.
	 *
	 * @return array|null The validated and sanitized data, or null if data does not conform to schema.
	 */
	public function validate_and_sanitize_data( $data ) : ?array {
		$validator = new Validator();

		if ( is_string( $data ) ) {
			$data_object = json_decode( $data );
		} else {
			$data_object = $data;
		}

		if ( ! is_array( $data_object ) ) {
			$data_array = [ $data_object ];
		} else {
			$data_array = $data_object;
		}

		foreach ( $data_array as $data_item ) {
			$validator->validate( $data_item, $this->schema, Constraint::CHECK_MODE_COERCE_TYPES );
			if ( ! $validator->isValid() ) {
				return null;
			}
		}

		return $data_array;
	}
}
