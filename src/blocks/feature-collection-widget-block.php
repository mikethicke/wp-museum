<?php
/**
 * Register feature collection block.
 *
 * When an object post is being viewed, this widget will show the collections
 * the object is a part of in the sidebar.
 *
 * @see blocks/src/feature-collection-widget
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Registers the block.
 */
function register_feature_collection_widget_block() {
	register_block_type(
		'wp-museum/feature-collection-widget',
		[
			'render_callback' => __NAMESPACE__ . '\render_feature_collection_widget',
			'attributes' => [
				'showFeatureImage' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'showDescription'  => [
					'type'    => 'boolean',
					'default' => true,
				],
			],
		],
	);
}

add_action( 'plugins_loaded',  __NAMESPACE__ . '\register_feature_collection_widget_block' );

/**
 * Render the block on the front end.
 *
 * @param Array $attributes The block's attributes.
 */
function render_feature_collection_widget_block( $attributes ) {
	return  'This is the feature collection widget block';
}