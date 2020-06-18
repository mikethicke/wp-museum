<?php
/**
 * Block for displaying a remote collection.
 *
 * @package MikeThicke\MuseumRemote
 */

namespace MikeThicke\MuseumRemote;

/**
 * Register collection block.
 */
function register_collecton_block() {
	register_block_type(
		'museum-remote/collection-block',
		[
			'render_callback' => __NAMESPACE__ . '\render_collection_block',
			'attributes' => [
				'numObjects'        => [
					'type'    => 'number',
					'default' => 4,
				],
				'columns'          => [
					'type'    => 'number',
					'default' => 4,
				],
				'collectionID'      => [
					'type'    => 'number',
					'default' => null,
				],
				'collectionURL'     => [
					'type'    => 'string',
					'default' => null,
				],
				'collectionObjects' => [
					'type'    => 'array',
					'default' => [],
					'items'   => [
						'type' => 'array',
					],
				],
				'thumbnailURL'      => [
					'type'    => 'string',
					'default' => null,
				],
				'imgDimensions'     => [
					'type'    => 'object',
					'default' => [
						'width'  => 150,
						'height' => 150,
						'size'   => 'thumbnail', // options => thumbnail, medium, large, full.
					],
				],
				'title'             => [
					'type'    => 'string',
					'default' => 'No Object Selected',
				],
				'excerpt'           => [
					'type'    => 'string',
					'default' => null,
				],
				'fontSize'          => [
					'type'    => 'float',
					'default' => 0.7,
				],
				'titleTag'          => [
					'type'    => 'string',
					'default' => 'h4', // options => h2, h3, h, h5, h6, p.
				],
				'imgAlignment'      => [
					'type'    => 'string',
					'default' => 'left', // options => left, center, right.
				],
				'displayTitle'      => [
					'type'    => 'boolean',
					'default' => true,
				],
				'linkToObjects'     => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayExcerpt'    => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayObjects'    => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayThumbnail'  => [
					'type'    => 'boolean',
					'default' => true,
				],
			],
		]
	);
}

/**
 * Renders collection block on frontend.
 *
 * @param Array $attributes The block attributes.
 */
function render_collection_block( $attributes ) {
	return '';
}

