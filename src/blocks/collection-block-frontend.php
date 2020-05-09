<?php
/**
 * Render the Collection block on the frontend.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

add_action(
	'plugins_loaded',
	function () {
		register_block_type(
			'wp-museum/collection',
			[
				'render_callback' => __NAMESPACE__ . '\render_museum_block',
				'attributes'      => [
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
					'appearance'        => [
						'type'    => 'object',
						'default' => [
							'borderWidth'       => 0,
							'borderColor'       => '#000',
							'backgroundColor'   => '#fff',
							'backgroundOpacity' => 0,
						],
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
);

/**
 * Renders the block frontend.
 *
 * This function mimics the React edit component as closely as possible.
 *
 * @see blocks/src/collection/edit.js
 *
 * @param Array $attributes The block attributes.
 */
function render_museum_block( $attributes ) {
	// phpcs:disable
	
	$numObjects       = $attributes['numObjects'];
	$columns          = $attributes['columns'];
	$collectionID     = $attributes['collectionID'];
	$displayTitle     = $attributes['displayTitle'];
	$titleTag         = $attributes['titleTag'];
	$imgAlignment     = $attributes['imgAlignment'];
	$displayThumbnail = $attributes['displayThumbnail'];
	$thumbnailURL     = $attributes['thumbnailURL'];
	$displayExcerpt   = $attributes['displayExcerpt'];
	$fontSize         = $attributes['fontSize'];
	$displayObjects   = $attributes['displayObjects'];
	$linkToObjects    = $attributes['linkToObjects'];
	
	$collection_post = get_post( $collectionID );
	$title           = $collection_post->post_title;
	
	add_filter( 'excerpt_more', __NAMESPACE__ . '\rest_excerpt_filter', 10, 2 );
	$excerpt =
		html_entity_decode(
			wp_strip_all_tags(
				get_the_excerpt( $collectionID )
			)
		);
	remove_filter( 'excerpt_more', __NAMESPACE__ . '\rest_excerpt_filter', 10, 2 );

	$collection_objects = get_associated_objects( 'publish', $collectionID );
	$collection_object_data  = array_map(
		function( $object ) {
			$object_data = [];
			$object_data['title'] = $object->post_title;
			$object_data['URL'] = get_permalink( $object );
			$img_data = get_object_thumbnail( $object->ID );
			if ( count( $img_data ) > 0 ) {
				$object_data['imgURL'] = $img_data[0];
			} else {
				$object_data['imgURL'] =  null;
			}
			return $object_data;
		},
		$collection_objects
	);
	$collection_object_data = array_filter(
		$collection_object_data,
		function( $object ) {
			return ( ! is_null( $object['imgURL'] ) );
		}
	);
	$collection_object_data = array_slice( $collection_object_data, 0, $numObjects );

	$percent_width = round( 1 / $columns * 100 );
	ob_start();
	
	?>
	<div class = 'museum-collection-block'>
		<div class = 'collection-block-upper-content img-<?= $imgAlignment ?>'>
			<?php if ( $displayThumbnail && ! is_null( $thumbnailURL ) ): ?>
				<div class = 'thumbnail-wrapper'>
					<img src = '<?= $thumbnailURL ?>'/>
				</div>
			<?php endif; ?>
			<div class = 'collection-info'>
				<?php if ( $displayTitle && ! is_null( $title ) ) : ?>
					<<?= $titleTag; ?>>
						<?= $title; ?>
					</<?= $titleTag; ?>>
				<?php endif; ?>
				<?php if ( $displayExcerpt && ! is_null( $excerpt ) ): ?>
					<div
						class = 'collection-excerpt'
						style = 'font-size: <?= $fontSize ?>em'
					>
						<?= $excerpt ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<div class = 'collection-block-lower-content'>
			<?php if ( $displayObjects && count( $collection_object_data ) > 0 ): ?>
				<?php foreach( $collection_object_data as $object_data ): ?>
					<div
						class = 'collection-object-image-wrapper'
						style = 'flex-basis: <?= $percent_width ?>%'
					>
						<?php if ( $linkToObjects ): ?>
							<a href = '<?= $object_data['URL'] ?>'>
						<?php endif; ?>
						<img
							src   = '<?= $object_data['imgURL'] ?>'
							title = '<?= $object_data['title'] ?>'
						/>
						<?php if ( $linkToObjects ): ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
	// phpcs:enable
}



