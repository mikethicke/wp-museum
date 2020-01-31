import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

import {
	InspectorControls,
	RichText,
} from "@wordpress/editor";

import { 
	PanelBody,
	PanelRow,
	TextControl,
	Button
} from '@wordpress/components';

import {
	Fragment
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import ObjectSearchButton from '../components/object-search-box.js';

registerBlockType('wp-museum/simple-object-callout-block', {
	title: __('Simple Object Callout'),
	icon: 'archive',
	category: 'widgets',
	attributes: {
		object_id: {
			type: 'string',
			default: ''
		},
		object_title: {
			type: 'string',
			default: ''
		},
		url: {
			type: 'string',
			default: ''
		}
	},
	edit: ( props ) => {
		let object_id = props.attributes.object_id;
		let confirmed_id = props.attributes.confirmed_id;

		function onChangeObjectID ( content ) {
			props.setAttributes( { object_id: content } );
		}

		function onUpdateButton ( ) {
			let post = new wp.api.collections.Posts( { id: object_id } );
			post.fetch().then( ( posts ) => {
				let a = 1;
			});


			const object_path = 'wp/v2/wpm_instrument/' + object_id
			apiFetch( { path: object_path } ).then( result => {
				props.setAttributes( {
					object_title:	result.title.rendered,
					url:			result.link,
					post_type:		result.type		
				} );
				apiFetch
			} );
		}

		let content = '';
		if ( props.attributes.object_title != '' ) {
			content = [
				<div>
					<table>
						<tr><td>Title:</td><td>{props.attributes.object_title}</td></tr>
						<tr><td>URL:</td><td>{props.attributes.url}</td></tr>
						<tr><td>Post Type:</td><td>{props.attributes.post_type}</td></tr>
					</table>
				</div>
			]
		}


		return [
			<Fragment>
				<InspectorControls>
					<PanelBody
						title = "Embed Object"
						initialOpen = {true}
					>
						<PanelRow>
							<TextControl
								label = 'Object ID'
								onChange = { onChangeObjectID }
								value = { object_id }
							>
							</TextControl>
						</PanelRow>
						<PanelRow>
							<Button isDefault isPrimary
								onClick = { onUpdateButton }
							>
								Update
							</Button>
							<ObjectSearchButton>
								Search
							</ObjectSearchButton>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
				{ content }
			</Fragment>
			
		];
	},
	save: ( props ) => {

	}
});