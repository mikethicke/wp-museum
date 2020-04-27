/**
 * A grid of square images with a variable number of rows and columns.
 *
 * Attribues:
 *  - columns        {number}  Number of columns in the grid.
 *  - rows           {number}  Number of rows in the grid.
 *  - objectData     {array}   Array of data for each object in the grid.
 *  - title          {string}  A title for the block.
 *  - displayTitle   {boolean} Whether to display the title.
 *  - captionText    {string}  A caption for the block.
 *  - displayCaption {boolean} Whether to display the caption.
 *  - linkToObject   {boolean} Whether clicking on each image in the grid
 *                             should link to associated image.
 *  - imgDimensions  {object}  Dimensions for images in the grid. Because
 *                             images vary in size depending on page width,
 *                             this is just used for determining which image
 *                             file to use.
 *  - fontSize       {number}  Font size for caption text (em).
 *  - titleTag       {string}  Tag name for the title to use.
 *  - appearance     {object}  User-controllable style attributes for the
 *                             block.  
 */

/**
 * WordPress dependencies
 */
import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';

registerBlockType( 'wp-museum/object-grid', {
	title      : __( 'Object Grid'),
	icon       : 'archive',
	category   : 'wp-museum',
	attributes : {
		columns: {
			type    : 'number',
			default : 3
		},
		rows: {
			type    : 'number',
			default : 2,
		},
		objectData: {
			type    : 'array',
			default : [],
		},
		title: {
			type    : 'string',
			default : null,
		},
		displayTitle: {
			type    : 'boolean',
			default : false
		},
		captionText: {
			type    : 'string',
			default : null
		},
		displayCaption: {
			type    : 'boolean',
			default : false
		},
		linkToObject: {
			type    : 'boolean',
			default : true
		},
		imgDimensions: {
			type    : 'object',
			default : {
				width  : 300,
				height : 300,
				size   : 'medium' //options: thumbnail, medium, large, full
			}
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
		}
	},
	edit,
	save
} );