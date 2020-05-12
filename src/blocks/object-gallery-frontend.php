<?php
/**
 * Render Object Gallery block on the frontend.
 *
 * @see blocks/src/object-gallery
 *
 * Attributes
 *  - columns        {number}  Number of columns in the grid.
 *  - objectID       {number}  WordPress post_id of the object.
 *  - objectURL      {string}  The URL of the object (ie. a WordPress frontend page).
 *  - imgData        {array}   Array of URLs of images in gallery.
 *  - imgDimensions  {object}  Dimensions for images in the grid. Because
 *                             images vary in size depending on page width,
 *                             this is just used for determining which image
 *                             file to use.
 *  - captionText    {string}  A caption for the block.
 *  - title          {string}  The object's title (name).
 *  - catID          {string}  The museum catalogue id for the object.
 *  - fontSize       {number}  Font size for caption text (em).
 *  - titleTag       {string}  Tag name for the title to use.
 *  - displayTitle   {boolean} Whether to display the title.
 *  - displayCaption {boolean} Whether to display the caption.
 *  - displayCatID   {boolean} Whether to display the object's catalogue ID.
 *  - linkToObject   {boolean} Whether clicking on each image in the grid
 *                             should link to associated image.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

add_action(
	'plugins_loaded',
	function () {
		register_block_type(
			'wp-museum/object-gallery',
			[
				'render_callback' => __NAMESPACE__ . '\render_object_gallery_block',
				'attributes' => [
					'columns' => [
						'type' => 'number',
						'default' => 3,
					],
					'objectID' => [
						'type' => 'number',
						'default' => null,
					],
					'objectURL' => [
						'type'    => 'string',
						'default' => null,
					],
					'imgData' => [
						'type' => 'array',
						'default' => [],
						'items' => [
							'type' => 'object',
						],
					],
					'imgDimensions' => [
						'type'    => 'object',
						'default' => [
							'width'  => 300,
							'height' => 300,
							'size'   => 'medium', // options: thumbnail, medium, large, full.
						],
					],
					'captionText' => [
						'type'    => 'string',
						'default' => null,
					],
					'title' => [
						'type'    => 'string',
						'default' => 'No Object Selected',
					],
					'catID' => [
						'type'    => 'string',
						'default' => 'No Object Selected',
					],
					'fontSize' => [
						'type'    => 'float',
						'default' => 0.7,
					],
					'titleTag' => [
						'type'    => 'string',
						'default' => 'h4', // options: h2, h3, h, h5, h6, p.
					],
					'displayTitle' => [
						'type'    => 'boolean',
						'default' => true,
					],
					'displayCaption' => [
						'type'    => 'boolean',
						'default' => true,
					],
					'linkToObject' => [
						'type'    => 'boolean',
						'default' => true,
					],
					'displayCatID' => [
						'type'    => 'boolean',
						'default' => false,
					],
				],
			]
		);
	}
);

/**
 * Renders the block frontend.
 *
 * This function mimics the React component as closely as possible, including
 * coding style.
 *
 * @see blocks/src/object-gallery/
 *
 * @param Array $attributes The block attributes.
 */
function render_object_gallery_block( $attributes ) {
	//phpcs:disable
	$columns        = $attributes['columns'];
	$objectURL      = $attributes['objectURL'] ?? null;
	$imgData        = $attributes['imgData'];
	$captionText    = $attributes['captionText'] ?? null;
	$title          = $attributes['title'];
	$catID          = $attributes['catID'];
	$fontSize       = $attributes['fontSize'];
	$titleTag       = $attributes['titleTag'];
	$displayTitle   = $attributes['displayTitle'];
	$displayCaption = $attributes['displayCaption'];
	$linkToObject   = $attributes['linkToObject'];
	$displayCatID   = $attributes['displayCatID'];

	$percentWidth = round( 1 / $columns * 100 ) . '%';

	$grid = '';
	foreach ( $imgData as $imgItem ) {
		ob_start();
		?>
		<div
			class = 'gallery-image-wrapper'
			style = 'flex-basis: <?= $percentWidth ?>'
		>
			<img src = '<?= $imgItem['imgURL'] ?>' />
		</div>
		<?php
		$grid .= ob_get_contents();
		ob_end_clean();
	}

	ob_start();
	?>
	<div class = 'object-gallery-block'>
		<?php if ( $linkToObject && null !== $objectURL ): ?>
			<a class = 'object-link' href = '<?= $objectURL ?>'>Hidden Link Text</a>
		<?php endif; ?>
		<?php if ( $displayTitle && null !== $title ): ?>
			<<?= $titleTag ?>>
				<?= $title ?>
			</<?= $titleTag ?>>
		<?php endif; ?>
		<div class = 'gallery-grid'>
			<?= $grid ?>
		</div>
		<div
			class = 'bottom-text-wrapper'
			style = 'font-size: <?= $fontSize ?>em'
		>
			<?php if ( $displayCatID && null !== $catID ) : ?>
				<div class = 'cat-id'>
					<?= $catID ?>
				</div>
			<?php endif; ?>
			<?php if ( $displayCaption && null !== $captionText ): ?>
				<p><?= $captionText ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
