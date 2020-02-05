<?php
/**
 * Class representing a single museum object field.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Class representing a single museum object field.
 */
class MObjectField {
	/**
	 * Database primary key of field.
	 *
	 * @var int $field_id
	 */
	public $field_id;

	/**
	 * WordPress identifier for the field. Used as name for saving and retrieving
	 * field's value from the database.
	 *
	 * @var string $slug
	 */
	public $slug;

	/**
	 * Primary key of the ObjectKind associated with this field.
	 *
	 * @var int $kind_id
	 */
	public $kind_id;

	/**
	 * Name of HTML elements for forms containing this field.
	 *
	 * @var string $name
	 */
	public $name;

	/**
	 * Label of this field for user display.
	 *
	 * @var string $label
	 */
	public $label;

	/**
	 * Datatype of the field: varchar|text|date|tinyint.
	 *
	 * @var string $type
	 */
	public $type;

	/**
	 * Order in which to display this field in forms and the front end
	 * (ascending order).
	 *
	 * @var int $display_order
	 */
	public $display_order;

	/**
	 * Whether this field is publicly-viewable on the front end.
	 *
	 * @var bool $public
	 */
	public $public;

	/**
	 * Whether this field is required to be filled in when posts are published.
	 *
	 * @var bool $required
	 */
	public $required;

	/**
	 * Whether this field is displayed in the Quick Browse table.
	 *
	 * @var bool $quick_browse
	 */
	public $quick_browse;

	/**
	 * Help text for users filling in this field.
	 *
	 * @var string $help_text
	 */
	public $help_text;

	/**
	 * Regular expression that this field must conform to. Also used for sorting.
	 *
	 * @var string $field_schema
	 */
	public $field_schema;

	/**
	 * Whether this field is displayed in callout boxes by default.
	 *
	 * @var bool $callout_default
	 */
	public $callout_default;

	public function __construct( $database_field ) {
		$this->field_id      = intval( $database_field->field_id );
		$this->slug          = $database_field->slug;
		$this->kind_id       = intval( $database_field->kind_id );
		$this->name          = $database_field->name;
		$this->label         = $database_field->label;
		$this->type          = $database_field->type;
		$this->display_order = intval( $database_field->display_order );
		$this->public        = (bool) intval( $database_field->public );
		$this->required      = (bool) intval( $database_field->required );
		$this->quick_browse  = (bool) intval( $database_field->quick_browse );
		$this->help_text     = $database_field->help_text;
		$this->field_schema  = $database_field->field_schema;
		$this->callout_default = (bool) intval( $database_field->callout_default );
	}
}
