<?php

namespace MikeThicke\WPMuseum;
	
$numObjects       = $attributes['numObjects'];
$columns          = $attributes['columns'];
$collectionID     = $attributes['collectionID'] ?? null;
$displayTitle     = $attributes['displayTitle'];
$titleTag         = $attributes['titleTag'];
$imgAlignment     = $attributes['imgAlignment'];
$displayThumbnail = $attributes['displayThumbnail'];
$thumbnailURL     = $attributes['thumbnailURL'] ?? null;
$displayExcerpt   = $attributes['displayExcerpt'];
$fontSize         = $attributes['fontSize'];
$displayObjects   = $attributes['displayObjects'];
$linkToObjects    = $attributes['linkToObjects'];

$collection_post = get_post( $collectionID );
$title           = $collection_post->post_title;

add_filter( 'excerpt_more', __NAMESPACE__ . '\rest_excerpt_filter', 10, 0 );
$excerpt =
	html_entity_decode(
		wp_strip_all_tags(
			get_the_excerpt( $collectionID )
		)
	);
remove_filter( 'excerpt_more', __NAMESPACE__ . '\rest_excerpt_filter', 10, 0 );

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
