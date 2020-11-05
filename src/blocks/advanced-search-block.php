<?php
/**
 * Block for creating an advanced search page.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers the block.
 */
function register_advanced_search_block() {
	register_block_type(
		'wp-museum/advanced-search',
		[
			'render_callback' => __NAMESPACE__ . '\render_advanced_search',
			'attributes'      => [
				'defaultSearch'   => [
					'type'    => 'string',
					'default' => '',
				],
				'fixSearch'       => [
					'type'    => 'boolean',
					'default' => false,
				],
				'runOnLoad'       => [
					'type'    => 'boolean',
					'default' => false,
				],
				'showObjectType' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'showTitleToggle' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'showFlags'       => [
					'type'    => 'boolean',
					'default' => true,
				],
				'showCollections' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'showFields'      => [
					'type'    => 'boolean',
					'default' => true,
				],
				'resultsPerPage'  => [
					'type'    => 'number',
					'default' => 20,
				],
			],
		]
	);
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_advanced_search_block' );

/**
 * Render the block on the front end.
 *
 * @param Array $attributes The block's attributes.
 */
function render_advanced_search( $attributes ) {
	if ( is_admin() ) {
		return null;
	}

	if ( isset( $_GET['searchText'] ) ) {
		$attributes['defaultSearch'] = [
			'searchText' => sanitize_text_field( $_GET['searchText'] ),
		];
	}

	$encoded_attributes = json_encode( $attributes );

	return (
		"<div 
			class='wpm-advanced-search-block-frontend' 
			data-attributes='$encoded_attributes'
		>
		</div>"
	);
}
