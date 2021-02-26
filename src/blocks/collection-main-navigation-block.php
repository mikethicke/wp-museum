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
			'render_callback' => __NAMESPACE__ . '\render_collection_main_navigation',
			'attributes' => [
				'fontSize'                  => [
					'type'    => 'number',
					'default' => 1,
				],
				'fontColor'                 => [
					'type'    => 'string',
					'default' => 'initial',
				],
				'backgroundColor'           => [
					'type'    => 'string',
					'default' => 'initial',
				],
				'borderColor'               => [
					'type'    => 'string',
					'default' => 'inherit',
				],
				'borderWidth'               => [
					'type'    => 'number',
					'default' => 0,
				],
				'verticalSpacing'           => [
					'type'    => 'number',
					'default' => 0,
				],
				'useDefaultFontSize'        => [
					'type'    => 'boolean',
					'default' => true,
				],
				'useDefaultFontColor'       => [
					'type'    => 'boolean',
					'default' => true,
				],
				'useDefaultBackgroundColor' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'useDefaultBorderColor'     => [
					'type'    => 'boolean',
					'default' => true,
				],
				'useDefaultBorderWidth'     => [
					'type'    => 'boolean',
					'default' => true,
				],
				'useDefaultVerticalSpacing' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'subCollectionIndent'       => [
					'type'    => 'number',
					'default' => 3,
				],
				'sortBy'                    => [
					'type'    => 'string',
					'enum'    => [ 'Alphabetical', 'Date Created', 'Date Updated' ],
					'default' => 'Alphabetical',
				],
				'sortOrder'                 => [
					'type'    => 'string',
					'enum'    => [ 'Ascending', 'Descending' ],
					'default' => 'Descending',
				],
				'tags'                      => [
					'type'    => 'array',
					'items'   => [
						'type' => 'string',
					],
					'default' => [ '_all' ],
				],
			],
		],
	);
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_collection_main_navigation_block' );

function render_collection_main_navigation( $attributes ) {
	if ( is_admin() ) {
		return null;
	}

	$encoded_attributes = json_encode( $attributes );

	return (
		"<div 
			class='wpm-collection-main-navigation-front' 
			data-attributes='$encoded_attributes'
		>
		</div>"
	);
}
