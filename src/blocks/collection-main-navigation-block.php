<?php
/**
 * Registers a Gutenbberg block for collection navigation within post content
 * area.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers the block.
 */
function register_collection_main_navigation_block() {
	register_block_type(
		'wp-museum/collection-main-navigation-block',
		[
			'attributes' => [

			]
		]
	);
}
