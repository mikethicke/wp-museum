import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

import edit from './edit';
import save from './save';

registerBlockType( 'wp-museum/object-grid', {
	title      : __( 'Object Grid'),
	icon       : 'archive',
	category   : 'wp-museum',
	attributes : {
		columns: {
			type: 'number',
			default: 3
		},
		rows: {
			type: 'number',
			default: 2,
		},
		objectData: {
			type: 'array',
			default: [],
		},
		title: {
			type: 'string',
			default: null,
		},
		displayTitle: {
			type: 'boolean',
			default: false
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