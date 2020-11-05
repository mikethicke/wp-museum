<?php
/**
 * Embedded search block that redirects to search page on submit.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers the block.
 */
function register_embedded_search_block() {
	register_block_type(
		'wp-museum/embedded-search',
		[
			'render_callback' => __NAMESPACE__ . '\render_embedded_search',
			'attributes'      => [
				'searchPageURL' => [
					'type'    => 'string',
					'default' => '',
				],
				'headerText'    => [
					'type'    => 'string',
					'default' => 'Search the Catalogue',
				],
				'align'         => [
					'type'    => 'string',
					'default' => 'center',
				],
				'maxWidth'      => [
					'type'    => 'number',
					'default' => 100,
				],
			],
		],
	);
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_embedded_search_block' );

/**
 * Render the block on the front end.
 *
 * @param Array $attributes The block's attributes.
 */
function render_embedded_search( $attributes ) {
	if ( is_admin() ) {
		return null;
	}

	$encoded_attributes = json_encode( $attributes );

	return (
		"<div 
			class='wpm-embedded-search-block-frontend' 
			data-attributes='$encoded_attributes'
		>
		</div>"
	);
}
