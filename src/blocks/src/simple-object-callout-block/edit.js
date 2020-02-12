import {
	RichText,
} from "@wordpress/editor";

import {
	InspectorControls
} from '@wordpress/blockEditor'

import { 
	PanelBody,
	PanelRow,
	TextControl,
	Button,
	CheckboxControl
} from '@wordpress/components';

import {
	Component
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import ObjectSearchButton from '../components/object-search-box.js';
import CalloutContent from './callout-content';
import CalloutPlaceholder from './callout-placeholder';

class FieldsPanel extends Component {

	updateField ( key, val ) {
		const { attributes, setAttributes } = this.props;
		const { fields } = attributes;
		let newFields = fields;

		newFields[key] = val;
		setAttributes ( { fields: newFields } );
		this.forceUpdate();
	}

	render () {
		const { fields, field_data } = this.props.attributes;
		if ( 
			Object.keys(fields).length > 0 &&
			Object.keys(field_data).length === Object.keys(fields).length 
		) {
			let items = [];
			for ( let key in fields ) {
				items.push( //Use map instead
					<CheckboxControl
						label = { field_data[key]['name'] }
						checked = { fields[key] }
						onChange = { ( val ) => { this.updateField( key, val ) } }
					/>
				);
			}
			return [
				<PanelBody
					title = "Custom Fields"
					initialOpen = { false }
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

function EditContent ( props ) {
	const { attributes, onChangeObjectID, onUpdateButton } = props;
	const { 
		object_fetched,
		object_id,
		object_title,
		excerpt,
		thumbnail,
		object_link,
		fields
	} = attributes;

	if ( object_fetched ) {
		return [
			<CalloutContent 
				object_id = { object_id }
				title = { object_title }
				excerpt = { excerpt }
				thumbnail = { thumbnail }
				object_link = { object_link }
				fields = { fields }
			/>
		];
	} else {
		return [
			<CalloutPlaceholder
				object_id = { object_id }
				onChangeObjectID = { onChangeObjectID }
				onUpdateButton = { onUpdateButton }
			/>
		]
	}
}

class ObjectCalloutEdit extends Component {
	
	fetchFieldData ( ) {
		const { attributes, setAttributes } = this.props;
		const { object_id } = attributes;
		const base_rest_path = '/wp-museum/v1/';

		if ( object_id != null ) {
			const object_path = base_rest_path + 'all/' + object_id;
			const oce = this;
			apiFetch( { path: object_path } ).then( result => {
				setAttributes( {
					object_title:	result.post_title,
					url:			result.link,
					post_type:		result.post_type,
					excerpt:		result.excerpt,
					junk:           result.junk,
					object_fetched: true,
				} );
				apiFetch(
					{ path: base_rest_path + 
							'object_custom/' + 
							result.post_type 
					}
				).then( result => {
					const { setAttributes } = oce.props;
					const { fields } = oce.props.attributes;
					let newFields = {};
					for ( let key in result ) {
						if ( typeof ( fields[key] ) === 'undefined') {
							newFields[key] = result[key]['callout_default'];
						} else {
							newFields[key] = fields[key];
						}
					}
					setAttributes( { 
						fields : newFields,
						field_data: result 
					} ); 
				} );
			} );
		}
	}

	componentDidMount() {
		this.fetchFieldData();
	}

	onChangeObjectID( content ) {
		const { setAttributes } = this.props;

		setAttributes( { object_id: content } );
	}

	onUpdateButton() {
		this.fetchFieldData();
	}
	
	render () {
		const { object_fetched } = this.props.attributes;

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
								onChange = { this.onChangeObjectID }
								value = { this.object_id }
							/>
						</PanelRow>
						<PanelRow>
							<Button isDefault isPrimary
								onClick = { this.onUpdateButton }
							>
								Update
							</Button>
							<ObjectSearchButton>
								Search
							</ObjectSearchButton>
						</PanelRow>
					</PanelBody>
					<OptionsPanel { ...this.props } />
					<FieldsPanel { ...this.props } />
				</InspectorControls>
				<EditContent { ...this.props } 
					onUpdateButton = { this.onUpdateButton }
					onChangeObjectID = { this.onChangeObjectID }
				/>
			</>	
		];
	}
}

export default ObjectCalloutEdit;