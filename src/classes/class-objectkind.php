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
	 * Machine-readable name of kind. Derrived from label.
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
	 * Human-readable name of kind. User generated.
	 *
	 * @var string $label
	 */
	public $label = null;

	/**
	 * Human-readable plural name of kind. User generated.
	 *
	 * @var string $label_plural
	 */
	public $label_plural = null;

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
	 * Whether objects of this kind should be excluded from searches. This is
	 * mostly applicable for child kinds.
	 *
	 * @var bool $exclude_from_search
	 */
	public $exclude_from_search = null;

	/**
	 * Kind_id of parent kind. Setting this makes this a child kind.
	 *
	 * @var int $parent_kind_id
	 */
	public $parent_kind_id = null;

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
	 * Generates type name from name.
	 */
	private function type_name_from_name() {
		global $wpdb;

		if ( is_null( $this->name ) ) {
			return;
		}

		$table_name = $wpdb->prefix . WPM_PREFIX . 'mobject_kinds';
		$type_name  = WPM_PREFIX . $this->name;
		if ( strlen( $type_name ) > 20 ) {
			$type_name = substr( $type_name, 0, 19 );
		}

		$duplicates        = true;
		$duplicate_counter = 0;
		$unique_type_name  = $type_name;
		while ( $duplicates ) {
			if ( is_null( $this->kind_id ) || is_null( get_kind( $this->kind_id ) ) ) {
				$results = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT type_name FROM %i WHERE type_name = %s',
						$table_name,
						$unique_type_name
					)
				);
			} else {
				$results = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT type_name FROM %i WHERE type_name = %s AND kind_id != %s',
						$table_name,
						$unique_type_name,
						$this->kind_id
					)
				);
			}
			if ( 0 < count( $results ) ) {
				$unique_type_name = substr( $type_name, 0, 18 ) . '_' . $duplicate_counter;
				++$duplicate_counter;
			} else {
				$duplicates = false;
			}
		}

		return $unique_type_name;
	}

	/**
	 * Updates type name when name changes. This updates the post type of all
	 * associated posts.
	 */
	private function update_type_name() {
		$old_type_name = $this->type_name;
		$new_type_name = $this->type_name_from_name();
		if ( ! is_null( $old_type_name ) && $old_type_name !== $new_type_name ) {
			$posts = $this->get_all_posts();
			foreach ( $posts as $post ) {
				wp_update_post(
					[
						'ID'        => $post->ID,
						'post_type' => $new_type_name,
					]
				);
			}
		}
		$this->type_name = $new_type_name;
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
		if ( isset( $kind_row->label_plural ) ) {
			$this->label_plural = $kind_row->label_plural;
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
			$this->strict_checking = (bool) intval( $kind_row->strict_checking );
		}
		if ( isset( $kind_row->exclude_from_search ) ) {
			$this->exclude_from_search = (bool) intval( $kind_row->exclude_from_search );
		}
		if ( isset( $kind_row->parent_kind_id ) ) {
			$this->parent_kind_id = intval( $kind_row->parent_kind_id );
		}
	}

	/**
	 * Gets all posts associated with this kind.
	 */
	public function get_all_posts() {
		$posts = get_posts(
			[
				'numberposts' => -1,
				'status'      => 'any',
				'post_type'   => $this->type_name,
			]
		);
		return $posts;
	}

	/**
	 * Returns array of fields associated with this kind.
	 */
	public function get_fields() {
		return get_mobject_fields( $this->kind_id );
	}

	/**
	 * Returns array of kinds that are children of this kind.
	 */
	public function get_children() {
		$children  = [];
		$all_kinds = get_mobject_kinds();
		foreach ( $all_kinds as $kind ) {
			if ( $kind->parent_kind_id === $this->kind_id ) {
				$children[] = $kind;
			}
		}
		return $children;
	}

	/**
	 * Returns array of kind arrays of children of this kind.
	 */
	public function get_children_array() {
		$children       = $this->get_children();
		$children_array = array_map(
			function ( $child ) {
				return $child->to_array();
			},
			$children
		);
		return $children_array;
	}

	/**
	 * Returns array of kind types that are children of this kind.
	 */
	public function get_child_types() {
		$children    = $this->get_children();
		$child_types = array_map(
			function ( $kind ) {
				return $kind->type_name;
			},
			$children
		);
		return $child_types;
	}

	/**
	 * Returns block template for this kind.
	 */
	public function block_template() {
		$post_type_object = get_post_type_object( $this->type_name );
		return $post_type_object->template;
	}

	/**
	 * Return properties as associative array.
	 */
	public function to_array() {
		$arr                        = [];
		$arr['kind_id']             = $this->kind_id;
		$arr['cat_field_id']        = $this->cat_field_id;
		$arr['name']                = $this->name;
		$arr['type_name']           = $this->type_name;
		$arr['label']               = $this->label;
		$arr['label_plural']        = $this->label_plural;
		$arr['description']         = $this->description;
		$arr['categorized']         = $this->categorized;
		$arr['hierarchical']        = $this->hierarchical;
		$arr['must_featured_image'] = $this->must_featured_image;
		$arr['must_gallery']        = $this->must_gallery;
		$arr['strict_checking']     = $this->strict_checking;
		$arr['exclude_from_search'] = $this->exclude_from_search;
		$arr['parent_kind_id']      = $this->parent_kind_id;
		return $arr;
	}

	/**
	 * Return properties as associative array.
	 */
	public function to_rest_array() {
		$arr                        = [];
		$arr['kind_id']             = $this->kind_id;
		$arr['cat_field_id']        = $this->cat_field_id;
		$arr['name']                = $this->name;
		$arr['type_name']           = $this->type_name;
		$arr['label']               = $this->label;
		$arr['label_plural']        = $this->label_plural;
		$arr['description']         = $this->description;
		$arr['categorized']         = $this->categorized;
		$arr['hierarchical']        = $this->hierarchical;
		$arr['must_featured_image'] = $this->must_featured_image;
		$arr['must_gallery']        = $this->must_gallery;
		$arr['strict_checking']     = $this->strict_checking;
		$arr['exclude_from_search'] = $this->exclude_from_search;
		$arr['parent_kind_id']      = $this->parent_kind_id;
		$arr['block_template']      = $this->block_template();
		$arr['children']            = $this->get_children_array();
		return $arr;
	}

	/**
	 * Return public properties as associative array.
	 */
	public function to_public_rest_array() {
		$arr                   = [];
		$arr['kind_id']        = $this->kind_id;
		$arr['cat_field_id']   = $this->cat_field_id;
		$arr['name']           = $this->name;
		$arr['type_name']      = $this->type_name;
		$arr['label']          = $this->label;
		$arr['label_plural']   = $this->label_plural;
		$arr['description']    = $this->description;
		$arr['categorized']    = $this->categorized;
		$arr['hierarchical']   = $this->hierarchical;
		$arr['parent_kind_id'] = $this->parent_kind_id;
		$arr['children']       = $this->get_children_array();
		return $arr;
	}

	/**
	 * Save kind to database.
	 */
	public function save_to_db() {
		global $wpdb;
		$table_name = $wpdb->prefix . WPM_PREFIX . 'mobject_kinds';

		if ( $this->label ) {
			$this->name = self::name_from_label( $this->label );
			if ( ! $this->label_plural ) {
				$this->label_plural = $this->label . 's';
			}
		}

		$saved_kind = get_kind( $this->kind_id );

		if ( $this->name ) {
			$this->update_type_name();
		}

		if ( is_null( $saved_kind ) ) {
			$insert_array = $this->to_array();
			unset( $insert_array['kind_id'] );
			return $wpdb->insert( $table_name, $insert_array );
		} else {
			return $wpdb->update( $table_name, $this->to_array(), [ 'kind_id' => $this->kind_id ] );
		}
	}

	/**
	 * Deletes kind from database.
	 */
	public function delete_from_db() {
		global $wpdb;
		$table_name = $wpdb->prefix . WPM_PREFIX . 'mobject_kinds';

		if ( is_null( $this->kind_id ) || 0 > $this->kind_id ) {
			return false;
		}

		$fields = $this->get_fields();
		foreach ( $fields as $field ) {
			$field->delete_from_db();
		}
		return $wpdb->delete( $table_name, [ 'kind_id' => $this->kind_id ] );
	}
}
