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
	/**
	 * We're going to get the post type of the current page. If it is one of
	 * the Museum object kinds, then we will register a block with the
	 * attributes of that object type. So the block will automatically only be
	 * available for objects, though we will still need to keep it out of the
	 * picker, because we only want it added automatically.
	 */
	$post_type     = admin_post_type();
	$mobject_types = get_object_type_names();

	if ( in_array( $post_type, $mobject_types, true ) ) {
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
				'attributes' => $attributes,
			]
		);
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_object_meta_block' );
