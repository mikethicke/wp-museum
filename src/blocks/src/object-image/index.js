import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

import edit from './edit';
import save from './save';

registerBlockType( 'wp-museum/object-image', {
	title      : __( 'Object Image'),
	icon       : 'archive',
	category   : 'wp-museum',
	supports    : {
		align: [ 'left', 'right', 'center' ]
	},
	attributes : {
		align: {
			type    : 'string',
			default : 'left'
		},
		objectID: {
			type    : 'number',
			default : null
		},
		catID: {
			type    : 'string',
			default : 'No Object Selected'
		},
		title: {
			type    : 'string',
			default : 'No Object Selected'
		},
		captionText: {
			type    : 'string',
			default : null
		},
		imgHeight: {
			type    : 'number',
			default : null
		},
		imgWidth: {
			type    : 'number',
			default : null
		},
		imgURL: {
			type    : 'string',
			default : null
		},
		imgIndex: {
			type    : 'number',
			default : 0
		},
		totalImages: {
			type    : 'number',
			default : 0
		},
		objectURL: {
			type    : 'string',
			default : null
		},
		displayTitle: {
			type    : 'boolean',
			default : true
		},
		displayCatID: {
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
			default : 'h6' //options: h2, h3, h, h5, h6, p
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