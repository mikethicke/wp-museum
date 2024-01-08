<?php
/**
 * Registers a Gutenberg block for adding and editing images associated with an object.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers the block.
 */
function register_object_image_attachments_block() {
	/**
	 * This block should only be available to object posts, and should never be
	 * available in the picker.
	 */
	$post_type     = admin_post_type();
	$mobject_types = get_object_type_names();

	if ( in_array( $post_type, $mobject_types, true ) ) {
		register_block_type(
			'wp-museum/object-image-attachments-block',
			[
				'attributes' => [
					'imgAttach'    => [
						'type'   => 'array',
						'source' => 'meta',
						'meta'   => 'wpm_gallery_attach_ids',
						'items'  => [
							'type' => 'number',
						],
					],
					'imgAttachStr' => [
						'type'   => 'string',
						'source' => 'meta',
						'meta'   => 'wpm_gallery_attach_ids_string',
					],
				],
			]
		);
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_object_image_attachments_block' );
