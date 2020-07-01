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
				'columns'          => [
					'type'    => 'number',
					'default' => 4,
				],
				'collectionID'      => [
					'type'    => 'number',
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
add_action( 'plugins_loaded', __NAMESPACE__ . '\register_collecton_block' );

/**
 * Renders collection block on frontend.
 *
 * @param Array $attributes The block attributes.
 */
function render_collection_block( $attributes ) {
	if ( ! is_admin() ) {
		wp_enqueue_script(
			'museum-remote-react-front',
			plugins_url( MR_REACT_PATH . 'index.js', __FILE__ ),
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-api' ],
			filemtime( plugin_dir_path( __FILE__ ) . MR_REACT_PATH . 'index.js' ),
			true
		);
		$object_name = 'attributesCollection' . $attributes['collectionID'];
		$element_id  = 'collection' . $attributes['collectionID'];

		wp_localize_script( 'museum-remote-react-front', $object_name, $attributes );

		return "<div class='wpm-remote-collection-block-front' id='$element_id'>Remote Collection Block</div>";
	}
}

