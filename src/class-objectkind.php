<?php
/**
 * Class representing a single museum object kind.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Class representing a single museum object kind.
 */
class ObjectKind {
	/**
	 * Database primary key.
	 *
	 * @var int $kind_id
	 */
	public $kind_id = null;

	/**
	 * Primary key of field that is used as unique identifier by users for
	 * musuem objects of this kind.
	 *
	 * @var int $cat_fieild_id
	 */
	public $cat_field_id = null;

	/**
	 * Machine-readible name of kind. Derrived from label.
	 *
	 * @var string $name
	 */
	public $name = null;

	/**
	 * WordPress custom post type name for this kind. Derrived from name. Must
	 * be unique for WordPress intallation.
	 *
	 * @var string $type_name
	 */
	public $type_name = null;

	/**
	 * Human-readible name of kind. User generated.
	 *
	 * @var string $label
	 */
	public $label = null;

	/**
	 * Short, human-readible description of the kind.
	 *
	 * @var string $description
	 */
	public $description = null;

	/**
	 * Whether museum objects of this kind must be assigned a category before
	 * publication.
	 *
	 * @var bool $categorized
	 */
	public $categorized = null;

	/**
	 * Whether posts of this kind can be hierarchical (have a parent-child
	 * relationship with other posts of this kind).
	 *
	 * @var bool $hierarchical
	 */
	public $hierarchical = null;

	/**
	 * Whether posts of this kind must have a featured image before publication.
	 *
	 * @var bool $must_featured_image
	 */
	public $must_featured_image = null;

	/**
	 * Whether posts of this kind must have an attached image gallery before
	 * publication.
	 *
	 * @var bool $must_gallery
	 */
	public $must_gallery = null;

	/**
	 * Whether posts of this kind are prevented from publication if they don't
	 * meet its requirements (see above) or if the user is just issued a warning.
	 *
	 * @var bool $strict_checking
	 */
	public $strict_checking = null;

	/**
	 * Converts museum object kind's label to name: all lowercase, spaces replaced by dashes.
	 *
	 * @param   string $kind_label The object kind's label.
	 *
	 * @return  string  The object kind's name.
	 */
	public static function name_from_label( $kind_label ) {
		return strtolower( str_replace( ' ', '-', $kind_label ) );
	}

	/**
	 * Constructor.
	 *
	 * @param StdObj $kind_row A row of the mobject_kinds_table where keys are
	 *                         column names and values are column values.
	 */
	public function __construct( $kind_row ) {
		if ( isset( $kind_row->kind_id ) ) {
			$this->kind_id = intval( $kind_row->kind_id );
		}
		if ( isset( $kind_row->cat_field_id ) ) {
			$this->cat_field_id = intval( $kind_row->cat_field_id );
		}
		if ( isset( $kind_row->name ) ) {
			$this->name = $kind_row->name;
		}
		if ( isset( $kind_row->type_name ) ) {
			$this->type_name = $kind_row->type_name;
		}
		if ( isset( $kind_row->label ) ) {
			$this->label = $kind_row->label;
		}
		if ( isset( $kind_row->description ) ) {
			$this->description = $kind_row->description;
		}
		if ( isset( $kind_row->hierarchical ) ) {
			$this->hierarchical = (bool) intval( $kind_row->hierarchical );
		}
		if ( isset( $kind_row->must_featured_image ) ) {
			$this->must_featured_image = (bool) intval( $kind_row->must_featured_image );
		}
		if ( isset( $kind_row->must_gallery ) ) {
			$this->must_gallery = (bool) intval( $kind_row->must_gallery );
		}
		if ( isset( $kind_row->strict_checking ) ) {
			$this->strict_checking     = (bool) intval( $kind_row->strict_checking );
		}

		if ( $this->label && ! $this->name ) {
			$this->name = self::name_from_label( $this->label );
		}
		if ( $this->name && ! $this->type_name ) {
			$this->type_name = WPM_PREFIX . $this->name;
		}
		if ( strlen( $this->type_name ) > 20 ) {
			$this->type_name = substr( $type_name, 0, 19 );
		}
	}

	/**
	 * Return properties as associative array.
	 */
	public function to_array() {
		$arr                        = [];
		$arr['cat_field_id']        = $this->cat_field_id;
		$arr['name']                = $this->name;
		$arr['type_name']           = $this->type_name;
		$arr['label']               = $this->label;
		$arr['description']         = $this->description;
		$arr['hierarchical']        = $this->hierarchical;
		$arr['must_featured_image'] = $this->must_featured_image;
		$arr['must_gallery']        = $this->must_gallery;
		$arr['strict_checking']     = $this->strict_checking;
		return $arr;
	}

	/**
	 * Save kind to database.
	 */
	public function save_to_db() {
		if ( ! $this->label || ! $this->name || ! $this->type_name ) {
			return false;
		}
		global $wpdb;
		$table_name   = $wpdb->prefix . WPM_PREFIX . 'mobject_kinds';

		$saved_kind = get_kind( $this->kind_id );
		if ( is_null( $saved_kind ) ) {
			// Collision checking.
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT type_name FROM $table_name WHERE type_name LIKE %s",
					'%' . $wpdb->esc_like( $type_name ) . '%'
				)
			);
			if ( 0 < count( $results ) ) {
				$this->type_name = substr( $this->type_name, 0, 18 ) . '_' . count( $results );
			}
			return $wpdb->insert( $table_name, $this->to_array() );
		} else {
			return $wpdb->update( $table_name, $this->to_array(), [ 'kind_id' => $this->kind_id ] );
		}
	}
}
