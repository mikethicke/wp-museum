/**
 * A grid of square images with a variable number of rows and columns showing
 * images for a particular museum object.
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
 *  - appearance     {object}  User-controllable style attributes for the
 *                             block.
 *  - displayTitle   {boolean} Whether to display the title.
 *  - displayCaption {boolean} Whether to display the caption.
 *  - displayCatID   {boolean} Whether to display the object's catalogue ID.
 *  - linkToObject   {boolean} Whether clicking on each image in the grid
 *                             should link to associated image.
 */

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';

registerBlockType( 'wp-musuem/object-gallery', {
	title      : __( 'Object Gallery' ),
	icon       : 'archive',
	category   : 'wp-museum',
	attributes : {
		columns: {
			type: 'number',
			default: 3
		},
		objectID: {
			type: 'number',
			defult: null
		},
		objectURL: {
			type    : 'string',
			default : null
		},
		imgData: {
			type: 'array',
			default: []
		},
		imgDimensions: {
			type    : 'object',
			default : {
				width  : 300,
				height : 300,
				size   : 'medium' //options: thumbnail, medium, large, full
			}
		},
		captionText: {
			type    : 'string',
			default : null
		},
		title: {
			type    : 'string',
			default : 'No Object Selected'
		},
		catID: {
			type    : 'string',
			default : 'No Object Selected'
		},
		fontSize: {
			type    : 'float',
			default : 0.7
		},
		titleTag: {
			type    : 'string',
			default : 'h4' //options: h2, h3, h, h5, h6, p
		},
		appearance: {
			type    : 'object',
			default : {
				borderWidth       : 0,
				borderColor       : '#000',
				backgroundColor   : '#fff',
				backgroundOpacity : 0
			}
		},
		displayTitle: {
			type    : 'boolean',
			default : true
		},
		displayCaption: {
			type    : 'boolean',
			default : true
		},
		linkToObject: {
			type    : 'boolean',
			default : true
		},
		displayCatID: {
			type    : 'boolean',
			default : false
		},
	},
	edit,
	save
} );