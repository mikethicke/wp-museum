import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

import edit from './edit';

registerBlockType('wp-museum/simple-object-callout-block', {
	title: __('Simple Object Callout'),
	icon: 'archive',
	category: 'widgets',
	attributes: {
		object_id: {
			type: 'string',
			default: null
		},
		display_title: {
			type: 'boolean',
			default: true
		},
		display_excerpt: {
			type: 'boolean',
			default: true
		},
		display_thumbnail: {
			type: 'boolean',
			default: true
		},
		link_to_object: {
			type: 'boolean',
			default: true
		},
		fields: {
			type: 'object',
			default: {}
		},
		field_data: {
			type: 'object',
			default: {}
		},
		object_fetched: {
			type: 'boolean',
			default: false
		}
	},
	edit,
	save: ( props ) => {
		return null;
	}
});