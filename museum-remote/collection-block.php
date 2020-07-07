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
				'columns'         => [
					'type'    => 'number',
					'default' => 4,
				],
				'collectionID'     => [
					'type'    => 'number',
					'default' => null,
				],
				'collectionSlug'   => [
					'type'    => 'string',
					'default' => null,
				],
				'imgDimensions'    => [
					'type'    => 'object',
					'default' => [
						'width'  => 150,
						'height' => 150,
						'size'   => 'thumbnail', // options => thumbnail, medium, large, full.
					],
				],
				'fontSize'         => [
					'type'    => 'float',
					'default' => 0.7,
				],
				'titleTag'         => [
					'type'    => 'string',
					'default' => 'h4', // options => h2, h3, h, h5, h6, p.
				],
				'imgAlignment'     => [
					'type'    => 'string',
					'default' => 'left', // options => left, center, right.
				],
				'displayTitle'     => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayExcerpt'   => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayObjects'   => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayThumbnail' => [
					'type'    => 'boolean',
					'default' => false,
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
		if ( ! empty( $attributes['collectionID'] ) ) {
			$object_name = 'attributesCollection' . $attributes['collectionID'];
			$element_id  = 'collection' . $attributes['collectionID'];
		} else {
			$object_name = 'attributesCollection' . $attributes['collectionSlug'];
			$element_id  = 'collection' . $attributes['collectionSlug'];
		}

		wp_localize_script( 'museum-remote-react-front', $object_name, $attributes );

		return "<div class='wpm-remote-collection-block-front' id='$element_id'>Remote Collection Block</div>";
	}
}

/**
 * Renders a collection block from a shortcode.
 *
 * @param Array $atts The shortcode attributes, which will be translated into
 *                    block attributes.
 */
function collection_block_shortcode( $atts ) {
	$shortcode_attributes = shortcode_atts(
		[
			'columns'           => 4,
			'id'                => '',
			'slug'              => '',
			'font_size'         => 0.7,
			'title_tag'         => 'h4',
			'img_alignment'     => 'left',
			'display_title'     => 1,
			'display_excerpt'   => 1,
			'display_objects'   => 1,
			'display_thumbnail' => 0,
		],
		$atts
	);

	$attributes =
		[
			'columns'          => $shortcode_attributes['columns'],
			'collectionID'     => $shortcode_attributes['id'],
			'collectionSlug'   => $shortcode_attributes['slug'],
			'imgDimensions'    => [
				'width'  => 150,
				'height' => 150,
				'size'   => 'thumbnail', // options => thumbnail, medium, large, full.
			],
			'fontSize'         => $shortcode_attributes['font_size'],
			'titleTag'         => $shortcode_attributes['title_tag'],
			'imgAlignment'     => $shortcode_attributes['img_alignment'],
			'displayTitle'     => $shortcode_attributes['display_title'],
			'displayExcerpt'   => $shortcode_attributes['display_excerpt'],
			'displayObjects'   => $shortcode_attributes['display_objects'],
			'displayThumbnail' => $shortcode_attributes['display_thumbnail'],
		];

	return render_collection_block( $attributes );
}
add_shortcode( 'museum_collection', __NAMESPACE__ . '\collection_block_shortcode' );
