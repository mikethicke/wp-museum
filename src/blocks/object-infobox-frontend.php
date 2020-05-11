<?php
/**
 * Render the Object Infobox block on the frontend.
 * 
 * @see blocks/src/object-infobox
 *
 *  Attribues:
 *  - align          {string}  The alignment of the block on the page { left | right | center }
 *  - objectID       {number}  The WordPress post_id of the object.
 *  - catID          {string}  The museum catalogue ID of the object.
 *  - title          {string}  The title (name) of the object.
 *  - excerpt        {string}  An excerpt of the description of the object.
 *  - imgURL         {string}  The URL of the image.
 *  - imgIndex       {number}  The array index of the image in the object's gallery.
 *  - totalImages    {number}  The total number of images in the object's gallery.
 *  - imgHeight      {number}  The actual height of the image file.
 *  - imgWidth       {number}  The actual width of the image file.
 *  - objectURL      {string}  The URL of the object page (ie. WordPress page).
 *  - displayTitle   {boolean} Whether to display the object's title.
 *  - displayExcerpt {boolean} Whether to display the object's description.
 *  - displayImage   {boolean} Whether to display the image.
 *  - linkToObject   {boolean} Whether to link to the object page by clicking on the infobox.
 *  - fields         {object}  List of fields and whether they are to be displayed.
 *  - fieldData      {object}  Data for each field to be displayed in the box.
 *  - imgDimensions  {object}  The *displayed* dimensions of the image.
 *  - imgAlignment   {string}  Alignment of the image within the infobox ( left | right | center ).
 *  - fontSize       {number}  Font size of description & field text (em).
 *  - titleTag       {string}  HTML tag for title (h1, h2, p, etc).
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

add_action(
	'plugins_loaded',
	function () {
		register_block_type(
			'wp-museum/object-infobox',
			[
				'render_callback' => __NAMESPACE__ . '\render_object_infobox_block',
				'attributes' => [
					'align' => [
						'type'    => 'string',
						'default' => 'center',
					],
					'objectID' => [
						'type'    => 'number',
						'default' => null,
					],
					'catID' => [
						'type'    => 'string',
						'default' => null,
					],
					'title' => [
						'type'    => 'string',
						'default' => 'No Object Selected',
					],
					'excerpt' => [
						'type'    => 'string',
						'default' => 'No Object Selected',
					],
					'imgURL' => [
						'type'    => 'string',
						'default' => null,
					],
					'imgIndex' => [
						'type'    => 'number',
						'default' => 0,
					],
					'totalImages' => [
						'type'    => 'number',
						'default' => 0,
					],
					'imgHeight' => [
						'type'    => 'number',
						'default' => null,
					],
					'imgWidth' => [
						'type'    => 'number',
						'default' => null,
					],
					'objectURL' => [
						'type'=> 'string',
						'default'=> null,
					],
					'displayTitle' => [
						'type'    => 'boolean',
						'default' => true,
					],
					'displayExcerpt' => [
						'type'    => 'boolean',
						'default' => true,
					],
					'displayImage' => [
						'type'    => 'boolean',
						'default' => true,
					],
					'linkToObject' => [
						'type'    => 'boolean',
						'default' => true,
					],
					'fields' => [
						'type'    => 'object',
						'default' => [],
					],
					'fieldData' => [
						'type'    => 'object',
						'default' => [],
					],
					'imgDimensions' => [
						'type'    => 'object',
						'default' => [
							'width'  => 150,
							'height' => 150,
							'size'   => 'thumbnail', // options => thumbnail, medium, large, full.
						],
					],
					'imgAlignment' => [
						'type'    => 'string',
						'default' => 'left', // options=> left, center, right.
					],
					'fontSize' => [
						'type'    => 'float',
						'default' => 0.7,
					],
					'titleTag' => [
						'type'    => 'string',
						'default' => 'h6', // options=> h2, h3, h, h5, h6, p.
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
 * @see blocks/src/object-infobox/
 *
 * @param Array $attributes The block attributes.
 */
function render_object_infobox_block( $attributes ) {
	//phpcs:disable

	$title          = $attributes['title'];
	$excerpt        = $attributes['excerpt'];
	$objectURL      = $attributes['objectURL'];
	$displayTitle   = $attributes['displayTitle'];
	$displayExcerpt = $attributes['displayExcerpt'];
	$imgURL         = $attributes['imgURL'];
	$displayImage   = $attributes['displayImage'];
	$linkToObject   = $attributes['linkToObject'];
	$fields         = $attributes['fields'];
	$fieldData      = $attributes['fieldData'];
	$imgDimensions  = $attributes['imgDimensions'];
	$imgAlignment   = $attributes['imgAlignment'];
	$fontSize       = $attributes['fontSize'];
	$titleTag       = $attributes['titleTag'];

	$width  = $imgDimensions['width'];
	$height = $imgDimensions['height'];

	$fieldList = [];
	if ( count( $fields ) === count( $fieldData ) ) {
		$fieldList = array_keys( $fields );
		$fieldList = array_filter(
			$fieldList,
			function ( $key ) use ( $fields ) {
				return ( $fields[ $key ] );
			}
		);
		$fieldList = array_map(
			function ( $key ) use ( $fontSize, $fieldData ) {
				ob_start();
				?>
				<li key = '<?= $key ?>' style = 'font-size: <?= $fontSize ?>em'>
					<span class = 'field-name'><?= $fieldData[$key]['name'] ?>: </span>
					<span class = 'field-data'><?= $fieldData[$key]['content'] ?? '' ?> </span>
				</li>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			},
			$fieldList
		);
	}
	$fieldListHTML = '';
	foreach ( $fieldList as $field ) {
		$fieldListHTML .= $field;
	}

	ob_start();
	?>
	<div class = 'info-outer-div'>
		<div class = 'infobox-body-wrapper img-<?= $imgAlignment ?>'>
			<?php if ( $linkToObject ): ?>
				<a class = 'object-link' href = '<?= $objectURL ?>'>Hidden Link Text</a>
			<?php endif; ?>
			<?php if ( ! is_null( $imgURL ) && $displayImage ): ?>
				<div class = 'infobox-img-wrapper'>
					<img
						src    = '<?= $imgURL ?>'
						height = '<?= $height ?>'
						width  = '<?= $width  ?>'
					/>
				</div>
			<?php endif; ?>
			<div class = 'infobox-content-wrapper'>
				<?php if ( null !== $title && $displayTitle ): ?>
					<<?= $titleTag ?>><?= $title ?></<?= $titleTag ?>>
				<?php endif; ?>
				<?php if ( null !== $excerpt && $displayExcerpt ): ?>
					<p style = 'font-size:<?= $fontSize ?>em'><?= $excerpt ?></p>
				<?php endif; ?>
				<?php if ( count( $fieldList ) > 0 ): ?>
					<ul>
						<?= $fieldListHTML ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
