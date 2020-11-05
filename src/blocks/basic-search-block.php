<?php
/**
 * Block for creating a basic search page.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers the block.
 */
function register_basic_search_block() {
	register_block_type(
		'wp-museum/basic-search',
		[
			'render_callback' => __NAMESPACE__ . '\render_basic_search',
			'attributes'      => [
				'searchText'         => [
					'type'    => 'string',
					'default' => '',
				],
				'resultsPerPage'     => [
					'type'    => 'number',
					'default' => 20,
				],
				'advancedSearchLink' => [
					'type'    => 'string',
					'default' => '',
				],
			],
		]
	);
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_basic_search_block' );

/**
 * Render the block on the front end.
 *
 * @param Array $attributes The block's attributes.
 */
function render_basic_search( $attributes ) {
	if ( is_admin() ) {
		return null;
	}

	if ( isset( $_GET['searchText'] ) ) {
		$attributes['searchText'] = sanitize_text_field( $_GET['searchText'] );
	}
	if ( isset( $_GET['onlyTitle'] ) ) {
		$attributes['onlyTitle'] = sanitize_text_field( $_GET['onlyTitle'] );
	}

	$encoded_attributes = json_encode( $attributes );

	return (
		"<div 
			class='wpm-basic-search-block-frontend' 
			data-attributes='$encoded_attributes'
		>
		</div>"
	);
}
