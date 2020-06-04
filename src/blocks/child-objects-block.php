<?php
/**
 * Registers a Gutenberg block for managing child object blocks from parent objects.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers the block.
 */
function register_child_objects_block () {
	/**
	 * This block should only be registered for museum object post types.
	 */
	$post_type     = admin_post_type();
	$mobject_types = get_object_type_names();
	if ( ! in_array( $post_type, $mobject_types, true ) ) {
		return;
	}

	register_block_type(
		'wp-museum/child-objects-block',
		[
			'attributes' => [
				'childObjects' => [
					'type'   => 'object',
					'source' => 'meta',
					'meta'   => WPM_PREFIX . 'child_objects',
				],
				'childObjectsStr' => [
					'type'    => 'string',
					'source'  => 'meta',
					'meta'    => WPM_PREFIX . 'child_objects_str',
				],
			],
		]
	);
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_child_objects_block' );