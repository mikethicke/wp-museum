import {
	InspectorControls,
	RichText,
} from "@wordpress/editor";

import { 
	PanelBody,
	PanelRow,
	TextControl,
	Button,
	CheckboxControl
} from '@wordpress/components';

import {
	Component,
	useState
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import ObjectSearchButton from '../components/object-search-box.js';

class FieldsPanel extends Component {

	render () {
		const { attributes, setAttributes } = this.props;
		const { fields, field_data } = attributes;
		if ( 
			Object.keys(fields).length > 0 &&
			Object.keys(field_data).length === Object.keys(fields).length 
		) {
			let items = [];
			for ( let key in fields ) {
				items.push(
					<CheckboxControl
						label = { field_data[key]['name'] }
						checked = { fields[key] }
						onChange = { ( val ) => { 
							fields[key] = val;
							setAttributes( { fields: fields } );
						} }
					/>
				);
			}
			return [
				<PanelBody
					title = "Custom Fields"
					initialOpen = {true}
				>
					{ items }
				</PanelBody>
			];
		} else {
			return null;
		}
	}
}

class OptionsPanel extends Component {

	render () {
		const { attributes, setAttributes } = this.props;
		const { display_title, display_excerpt, display_thumbnail, link_to_object } = attributes;
		return [
			<PanelBody
				title = "Options"
				initialOpen = {true}
			>
				<CheckboxControl
					label = 'Display Title'
					checked = { display_title }
					onChange = { ( val ) => { setAttributes( { display_title: val } ) } }
				/>
				<CheckboxControl
					label = 'Display Excerpt'
					checked = { display_excerpt }
					onChange = { ( val ) => { setAttributes( { display_excerpt: val } ) } }
				/>
				<CheckboxControl
					label = 'Display Thumbnail'
					checked = { display_thumbnail }
					onChange = { ( val ) => { setAttributes( { display_thumbnail: val } ) } }
				/>
				<CheckboxControl
					label = 'Link to Object'
					checked = { link_to_object }
					onChange = { ( val ) => { setAttributes( { link_to_object: val } ) } }
				/>
			</PanelBody>
		]
	}
}

class ObjectEdit extends Component {
	
	updateFields ( ) {
		const { attributes, setAttributes } = this.props;
		const { object_id } = attributes;

		if ( object_id != null ) {
			const object_path = base_rest_path + 'all/' + object_id
			apiFetch( { path: object_path } ).then( result => {
				setAttributes( {
					object_title:	result.post_title,
					url:			result.link,
					post_type:		result.post_type,
					excerpt:		result.excerpt
				} );
				apiFetch(
					{ path: base_rest_path + 
							'object_custom/' + 
							attributes.post_type 
					}
				).then( result => {
					for ( let key in result ) {
						if ( typeof ( fields[key] ) === 'undefined') {
							fields[key] = ( result[key]['callout_default'] === '1' );
						}
					}
					setAttributes( { 
						fields : fields,
						field_data: result 
					} );
				} );
			} );
		}
	}

	componentDidMount() {
		this.updateFields();
	}
	
	render () {
		return [
			<>
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
					<OptionsPanel />
					<FieldsPanel />
				</InspectorControls>
				<CalloutContent />
			</>	
		];
	}
}