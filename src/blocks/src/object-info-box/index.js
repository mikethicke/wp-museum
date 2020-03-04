import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

import edit from './edit';
import save from './save';

registerBlockType('wp-museum/object-info-box', {
	title: __('Museum Object Infobox'),
	icon: 'archive',
	category: 'widgets',
	supports: {
		align: [ 'left', 'right', 'center' ]
	},
	attributes: {
		align: {
			type: 'string',
			default: 'left'
		},
		objectID: {
			type: 'string',
			default: null
		},
		title: {
			type: 'string',
			default: null
		},
		excerpt: {
			type: 'string',
			default: null
		},
		thumbnailURL: {
			type: 'string',
			default: null
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
		displayThumbnail: {
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
		toggle: {
			type: 'boolean',
			default: false
		},
		imageDimensions: {
			type: 'object',
			default: {
				width: null,
				height: null,
				size: 'large' //options: thumbnail, medium, large, full
			}
		},
		imageAlignment: {
			type: 'string',
			default: 'center' //options: left, center, right
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
				borderWidth: 1,
				borderColor: '#000',
				backgroundColor: '#fff',
				backgroundOpacity: 0
			}
		}
	},
	edit,
	save
});