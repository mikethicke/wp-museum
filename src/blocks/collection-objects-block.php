<?php
/**
 * Register collection objects block.
 *
 * @see blocks/src/collection
 *
 * Attributes:
 *  - postID         {number} WordPress post_id of the colleciton.
 *  - resultsPerPage {number} How many results to show per page (default 20).
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

add_action(
	'plugins_loaded',
	function() {
		register_block_type(
			'wp-museum/collection-objects',
			[
				'render_callback' => __NAMESPACE__ . '\render_collection_objects_block',
				'attributes' => [
					'postID' => [
						'type' => 'number',
						'default' => -1,
					],
					'resultsPerPage' => [
						'type' => 'number',
						'default' => 20
					],
				],
			],
		);
	}
);

/**
 * Renders the collection on the frontend.
 *
 * @param Array $attributes The block attributes.
 */
function render_collection_objects_block( $attributes ) {
	$post_id = get_the_ID();
	$output = '';
	if ( $post_id ) {
		return ( "<div class='wpm-collection-objects-block' data-post-ID='$post_id'></div>" );
	}
	return ( '' );
}
