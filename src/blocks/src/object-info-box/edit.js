import {
	InspectorControls
} from '@wordpress/blockEditor'

import { 
	PanelBody,
	CheckboxControl,
} from '@wordpress/components';

import {
	Component
} from '@wordpress/element';

import { __ } from "@wordpress/i18n";

import apiFetch from '@wordpress/api-fetch';

import { ObjectEmbedPanel } from '../components/object-search-box';
import AppearancePanel from '../components/appearance-panel';
import ImageSizePanel from '../components/image-size-panel';
import { InfoContent, InfoPlaceholder } from './info-content';
import FontSizePanel from '../components/font-size-panel';

const FieldsPanel = ( props ) => {
	const {
		setAttributes,
		fields,
		fieldData
	} = props;

	const updateField = ( key, val ) => {
		const newFields = Object.assign( {}, fields );
		newFields[key] = val;
		setAttributes ( { 
			fields: newFields,
		} );
	}

	if ( 
		Object.keys(fields).length > 0 &&
		Object.keys(fieldData).length === Object.keys(fields).length 
	) {
		const items = Object.keys(fields).map( key => 
			<CheckboxControl
				key = { key.toString() }
				label = { fieldData[key]['name'] }
				checked = { fields[key] }
				onChange = { ( val ) => { updateField( key, val ) } }
			/>
		);
		return (
			<PanelBody
				title = "Custom Fields"
				initialOpen = { false }
			>
				{ items }
			</PanelBody>
		);
	} else {
		return null;
	}
	
}

const OptionsPanel = ( props ) => {
	const { attributes, setAttributes } = props;
	const { displayTitle, displayExcerpt, displayThumbnail, linkToObject } = attributes;
	return (
		<PanelBody
			title = "Options"
			initialOpen = {true}
		>
			<CheckboxControl
				label = 'Display Title'
				checked = { displayTitle }
				onChange = { ( val ) => { setAttributes( { displayTitle: val } ) } }
			/>
			<CheckboxControl
				label = 'Display Excerpt'
				checked = { displayExcerpt }
				onChange = { ( val ) => { setAttributes( { displayExcerpt: val } ) } }
			/>
			<CheckboxControl
				label = 'Display Thumbnail'
				checked = { displayThumbnail }
				onChange = { ( val ) => { setAttributes( { displayThumbnail: val } ) } }
			/>
			<CheckboxControl
				label = 'Link to Object'
				checked = { linkToObject }
				onChange = { ( val ) => { setAttributes( { linkToObject: val } ) } }
			/>
		</PanelBody>
	);
}

const EditContent = ( props ) => {
	const { attributes, state, onChangeObjectID, onUpdateButton, onSearchModalReturn } = props;
	const { 
		objectID,
		title,
		excerpt,
		thumbnailURL,
		objectURL,
		fields,
		fieldData,
		imgDimensions,
		imgAlignment,
		fontSize,
		displayTitle,
		displayThumbnail,
		displayExcerpt,
		linkToObject,
		appearance,
		titleTag
	} = attributes;
	const {
		object_fetched
	} = state;

	if ( object_fetched ) {
		return (
			<InfoContent 
				objectID = { objectID }
				title = { displayTitle ? title : null }
				excerpt = { displayExcerpt ? excerpt : null }
				thumbnailURL = { displayThumbnail ? thumbnailURL : null }
				objectURL = { linkToObject ? objectURL : null }
				fields = { fields }
				fieldData = { fieldData }
				imgDimensions = { imgDimensions }
				state = { state }
				imgAlignment = { imgAlignment }
				fontSize = { fontSize }
				appearance = { appearance }
				titleTag = { titleTag }
			/>
		);
	} else {
		return (
			<InfoPlaceholder
				objectID = { objectID }
				onChangeObjectID = { onChangeObjectID }
				onUpdateButton = { onUpdateButton }
				onSearchModalReturn = { onSearchModalReturn }
			/>
		);
	}
}

class ObjectInfoEdit extends Component {
	constructor ( props ) {
		super ( props );

		this.onUpdateButton      = this.onUpdateButton.bind( this );
		this.onChangeObjectID    = this.onChangeObjectID.bind( this );
		this.fetchFieldData      = this.fetchFieldData.bind ( this );
		this.onSearchModalReturn = this.onSearchModalReturn.bind( this );

		this.state = {
			object_fetched : false,
			object_data    : {},
			imgHeight      : null,
			imgWidth       : null
		}
	}
	
	fetchFieldData ( objectFetchID = null ) {
		const { setAttributes } = this.props;
		const base_rest_path = '/wp-museum/v1/';
		const objectID = objectFetchID ? objectFetchID : this.props.attributes.objectID;
		
		if ( objectID != null ) {
			const object_path = base_rest_path + 'all/' + objectID;
			const that = this;
			apiFetch( { path: object_path } ).then( result => {
				that.setState( { object_data: result } );
				setAttributes( {
					title: result['post_title'],
					excerpt: result['excerpt'],
					thumbnailURL: result['thumbnail'][0],
					objectURL: result['link']
				} );
				that.setState( {
					imgWidth: result['thumbnail'][1],
					imgHeight: result['thumbnail'][2]
				} );
				apiFetch(
					{ path: base_rest_path + 
							result.post_type +
							'/custom'
					}
				).then( result => {
					const { fields } = that.props.attributes;
					const { object_data } = that.state;
					let newFields = {};
					let fieldData = {};
					for ( let key in result ) {
						if ( typeof ( fields[key] ) === 'undefined') {
							newFields[key] = false;
						} else {
							newFields[key] = fields[key];
						}
						let content = '';
						if ( result[key]['type'] === 'tinyint' ) {
							if ( object_data[ result[key]['slug'] ] === 1 ) {
								content = 'Yes';
							} else {
								content = 'No';
							}
						} else {
							content = object_data[ result[key]['slug'] ];
						}
						fieldData[key] = {
							name: result[key]['name'],
							content: content
						}
					}
					setAttributes( {
						catID     : object_data[ object_data[ 'cat_field' ] ],
						fields    : newFields,
						fieldData : fieldData
					} );
					that.setState( { 
						object_fetched: true
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

		setAttributes( { objectID: content } );
	}

	onUpdateButton() {
		this.fetchFieldData();
	}

	onSearchModalReturn( returnValue ) {
		const { setAttributes } = this.props;

		if ( returnValue != null ) {
			setAttributes( { objectID: returnValue } );
			this.fetchFieldData( returnValue );
		}
	}
	
	render () {
		const { setAttributes, attributes } = this.props;
		const { 
			fontSize,
			appearance,
			titleTag,
			title,
			catID,
			objectID,
			fields,
			fieldData,
			objectURL,
			imgDimensions,
			imgAlignment
		} = attributes;

		const {
			imgHeight,
			imgWidth,
		} = this.state;
		
		return (
			<>
				<InspectorControls>
					<ObjectEmbedPanel 
						onSearchModalReturn = { this.onSearchModalReturn }
						title               = { title }
						catID               = { catID }
						objectID            = { objectID }
						objectURL           = { objectURL }
					/>
					<OptionsPanel { ...this.props } />
					<ImageSizePanel
						setAttributes = { setAttributes }
						imgHeight     = { imgHeight }
						imgWidth      = { imgWidth }
						imgDimensions = { imgDimensions }
						imgAlignment  = { imgAlignment }
					/>
					<AppearancePanel
						setAttributes = { setAttributes }
						appearance    = { appearance }
					/>
					<FontSizePanel
						setAttributes = { setAttributes }
						titleTag      = { titleTag }
						fontSize      = { fontSize }
						initialOpen   = { false }
					/>
					<FieldsPanel
						setAttributes = { setAttributes }
						fields        = { fields }
						fieldData     = { fieldData }
					/>
				</InspectorControls>
				<EditContent { ...this.props } 
					onSearchModalReturn = { this.onSearchModalReturn }
					onChangeObjectID    = { this.onChangeObjectID }
					state               = { this.state }
				/>
			</>	
		);
	}
}

export default ObjectInfoEdit;