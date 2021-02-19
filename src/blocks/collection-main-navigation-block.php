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
				'fontSize'         => [
					'type'    => 'number',
					'default' => 1,
				],
				'fontColor'        => [
					'type'    => 'string',
					'default' => 'initial',
				],
				'backgroundColor' => [
					'type'    => 'string',
					'default' => 'initial',
				],
				'borderColor'     => [
					'type'    => 'string',
					'default' => 'initial',
				],
				'borderWidth'     => [
					'type'    => 'number',
					'default' => 0,
				],
				'tags'            => [
					'type'    => 'array',
					'items'   => [
						'type' => 'string',
					],
					'default' => [],
				],
			],
		],
	);
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_collection_main_navigation_block' );
