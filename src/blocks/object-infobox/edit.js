/**
 * Gutenberg editor view for Object Infobox block. Creates <ObjectInfoEdit> component.
 */

/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor'

import { 
	PanelBody,
	CheckboxControl,
} from '@wordpress/components';

import {
	useCallback,
	useEffect,
	useState
} from '@wordpress/element';

import { __ } from "@wordpress/i18n";

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import {
	ObjectEmbedPanel,
	ImageSizePanel,
	FontSizePanel
} from '../../components';

import InfoContent from './info-content';


/**
 * Inspector panel for selecting which fields to display in the infobox.
 *
 * @param {object}   props               The component's properties.
 * @param {function} props.setAttributes Callback function to set block
 *                                       attributes.
 * @param {object}   props.fields        List of object fields and whether they
 *                                       are selected for display.
 * @param {object}   props.fieldData     Data for each field.
 */
const FieldsPanel = ( props ) => {
	const {
		setAttributes,
		fields,
		fieldData
	} = props;

	/**
	 * Callback to update whether a field is selected.
	 *
	 * @param {number}  key Array index of the field.
	 * @param {boolean} val Whether the field is selected. 
	 */
	const updateField = ( key, val ) => {
		const newFields = Object.assign( {}, fields );
		newFields[key]  = val;
		
		setAttributes ( { 
			fields: newFields,
		} );
	}

	if ( Object.keys(fields).length > 0 &&
		 Object.keys(fieldData).length === Object.keys(fields).length 
	) {
		const items = Object.keys(fields).map( key => 
			<CheckboxControl
				key      = { key.toString() }
				label    = { fieldData[key]['name'] }
				checked  = { fields[key] }
				onChange = { ( val ) => { updateField( key, val ) } }
			/>
		);
		return (
			<PanelBody
				title       = "Custom Fields"
				initialOpen = { false }
			>
				{ items }
			</PanelBody>
		);
	} else {
		return null;
	}
	
}

/**
 * Inspector panel controlling whether to display title, caption for the block
 * and whether clicking on images will link to the associated object.
 * 
 * @param {object}   props                           The component's properties.
 * @param {object}   props.attributes                The block's attributes.
 * @param {function} props.setAttributes             Callback function to update block attributes.
 * @param {boolean}  props.attributes.displayTitle   Whether to display a title for the block.
 * @param {boolean}  props.attributes.displayExcerpt Whether to display object description.
 * @param {boolean}  props.attributes.displayImage   Whether to display image for the block.
 * @param {boolean}  props.attributes.linkToObject   Whether block should link to objects.
 */
const OptionsPanel = ( props ) => {
	const { attributes, setAttributes } = props;
	const { displayTitle, displayExcerpt, displayImage, linkToObject } = attributes;
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
				label = 'Display Image'
				checked = { displayImage }
				onChange = { ( val ) => { setAttributes( { displayImage: val } ) } }
			/>
			<CheckboxControl
				label = 'Link to Object'
				checked = { linkToObject }
				onChange = { ( val ) => { setAttributes( { linkToObject: val } ) } }
			/>
		</PanelBody>
	);
}

/**
 * Main editor component for Object Infobox block.
 *
 * All of the content of this block is fetched from the REST api. The user
 * controls which information is displayed through the InspectorControl.
 */
const ObjectInfoEdit = ( props ) => {
	const [ objectData, setObjectData ] = useState( {} );
	const { setAttributes, attributes } = props;

	/**
	 * Fetches object data from WordPress REST api.
	 *
	 * If objectFetchID is set, then fetch that object. Otherwise use the
	 * objectID set in the block attributes. This function takes an
	 * objectFetchID so that you don't need to wait for setAttributes to fire
	 * before fetching data from the API, which would introduce additional
	 * update lag.
	 *
	 * @param {number} objectFetchID WordPress post_id of object.
	 */
	const fetchFieldData = useCallback(( objectFetchID = null ) => {
		const base_rest_path = '/wp-museum/v1/';
		const objectID = objectFetchID ? objectFetchID : attributes.objectID;
		
		if ( objectID != null ) {
			const object_path = base_rest_path + 'all/' + objectID;
			apiFetch( { path: object_path } ).then( result => {
				setObjectData( result );
				setAttributes( {
					title     : result['post_title'],
					excerpt   : result['excerpt'],
					objectURL : result['link']
				} );
				apiFetch(
					{ path: base_rest_path + 
							result.post_type +
							'/fields'
					}
				).then( result => {
					const { fields } = attributes;
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
							if ( objectData[ result[key]['slug'] ] === 1 ) {
								content = 'Yes';
							} else {
								content = 'No';
							}
						} else {
							content = objectData[ result[key]['slug'] ];
						}
						fieldData[key] = {
							name    : result[key]['name'],
							content : content
						}
					}
					setAttributes( {
						catID     : objectData[ objectData[ 'cat_field' ] ],
						fields    : newFields,
						fieldData : fieldData
					} );
				} );
			} );
		}
	}, [attributes.objectID, setAttributes]);

	/**
	 * When component mounts, fetch object data from the REST API.
	 */
	useEffect(() => {
		fetchFieldData();
	}, [fetchFieldData]);

	/**
	 * Callback function for search modal. When an object is found, set the
	 * objectID attribute and fetch the data for that object from the REST API.
	 *
	 * @param {number} returnValue WordPress post_of found object.
	 */
	const onSearchModalReturn = useCallback(( returnValue ) => {
		if ( returnValue != null ) {
			setAttributes( { objectID: returnValue } );
			fetchFieldData( returnValue );
		}
	}, [setAttributes, fetchFieldData]);

	const { 
		fontSize,
		titleTag,
		title,
		catID,
		objectID,
		fields,
		fieldData,
		objectURL,
		imgDimensions,
		imgAlignment,
		displayTitle,
		displayExcerpt,
		excerpt,
		imgURL,
		displayImage,
		linkToObject,
		totalImages,
		imgHeight,
		imgWidth,
		imgIndex,
	} = attributes;
	
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<ObjectEmbedPanel 
					onSearchModalReturn = { onSearchModalReturn }
					title               = { title }
					catID               = { catID }
					objectID            = { objectID }
					objectURL           = { objectURL }
				/>
				<OptionsPanel { ...props } />
				<ImageSizePanel
					setAttributes = { setAttributes }
					imgDimensions = { imgDimensions }
					imgAlignment  = { imgAlignment }
					initialOpen   = { true }
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
			<InfoContent 
				objectID            = { objectID }
				title               = { displayTitle ? title : null }
				excerpt             = { displayExcerpt ? excerpt : null }
				imgIndex            = { imgIndex }
				imgURL              = { imgURL }
				imgHeight           = { imgHeight }
				imgWidth            = { imgWidth }
				displayImage        = { displayImage }
				objectURL           = { linkToObject ? objectURL : null }
				fields              = { fields }
				fieldData           = { fieldData }
				imgDimensions       = { imgDimensions }
				imgAlignment        = { imgAlignment }
				fontSize            = { fontSize }
				titleTag            = { titleTag }
				onSearchModalReturn = { onSearchModalReturn }
				setAttributes       = { setAttributes }
				totalImages         = { totalImages }
			/>
		</div>
	);
}

export default ObjectInfoEdit;