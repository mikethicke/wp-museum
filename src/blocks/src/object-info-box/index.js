import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

import edit from './edit';
import save from './save';

registerBlockType('wp-museum/object-info-box', {
	title: __('Object Infobox'),
	icon: 'archive',
	category: 'wp-museum',
	supports: {
		align: [ 'left', 'right', 'center' ]
	},
	attributes: {
		align: {
			type: 'string',
			default: 'center'
		},
		objectID: {
			type: 'number',
			default: null
		},
		catID: {
			type: 'string',
			default: null
		},
		title: {
			type: 'string',
			default: 'No Object Selected'
		},
		excerpt: {
			type: 'string',
			default: 'No Object Selected'
		},
		imgURL: {
			type: 'string',
			default: null
		},
		imgIndex: {
			type: 'number',
			default: 0
		},
		totalImages: {
			type    : 'number',
			default : 0
		},
		imgHeight: {
			type    : 'number',
			default : null
		},
		imgWidth: {
			type    : 'number',
			default : null
		},
		objectURL: {
			type: 'string',
			default: null
		},
		displayTitle: {
			type: 'boolean',
			default: true
		},
		displayExcerpt: {
			type: 'boolean',
			default: true
		},
		displayImage: {
			type: 'boolean',
			default: true
		},
		linkToObject: {
			type: 'boolean',
			default: true
		},
		fields: {
			type: 'object',
			default: {}
		},
		fieldData: {
			type: 'object',
			default: {}
		},
		imgDimensions: {
			type: 'object',
			default: {
				width: 150,
				height: 150,
				size: 'thumbnail' //options: thumbnail, medium, large, full
			}
		},
		imgAlignment: {
			type: 'string',
			default: 'left' //options: left, center, right
		},
		fontSize: {
			type: 'float',
			default: 0.7
		},
		titleTag: {
			type: 'string',
			default: 'h6' //options: h2, h3, h, h5, h6, p
		},
		appearance: {
			type: 'object',
			default: {
				borderWidth: 0,
				borderColor: '#000',
				backgroundColor: '#fff',
				backgroundOpacity: 0
			}
		}
	},
	edit,
	save
});