/**
 * WordPress dependencies
 */
import {
	useState,
	useEffect
} from '@wordpress/element';

import {
	InspectorControls
} from '@wordpress/blockEditor';

import { 
	CheckboxControl,
	ColorPicker,
	PanelBody,
	TextControl,
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

import {
	__
} from  '@wordpress/i18n';

/**
 * Internal dependencies
 */

import {
	baseRestPath,
	wordPressRestBase,
	isEmpty
} from '../util';

const CollectionMainNavigationEdit = props => {
	const {
		attributes,
		setAttributes
	} = props;

	const {
		fontSize,
		fontColor,
		backgroundColor,
		borderColor,
		borderWidth,
		tags
	} = attributes;

	const [ collectionData, setCollectionData ] = useState( {} );
	const [ tagList, setTagList ] = useState( {} );

	useEffect( () => {
		updateCollectionData();
		updateTagList();
	}, [] );

	const updateCollectionData = () => {
		apiFetch( { path: `${baseRestPath}/collections` } ).then( result => setCollectionData( result ) );
	}

	const updateTagList = () => {
		apiFetch( { path: `${wordPressRestBase}/collection_tag`} )
			.then( result => {
				if ( Array.isArray( result ) ) {
					const newTagList = {};
					result.forEach( taxItem => {
						newTagList[ taxItem['slug'] ] = taxItem['name'];
					} );
					setTagList( newTagList );
				}
			} );
	}

	const tagChecked = ( tagSlug ) => tags.includes( tagSlug );
	const allTagChecked = () => tags.includes( '_all' );

	const checkTag = ( tagSlug, isChecked ) => {
		const newTags = tags.filter( tag => tag !== tagSlug );
		if ( isChecked ) {
			newTags.push( tagSlug );
		}
		setAttributes( {
			tags: newTags
		} );
	}

	const tagCheckboxes = [
		<CheckboxControl
			key      = 'tag-checkboxes-_all'
			label    = { __('All') }
			checked  = { tagChecked( '_all') }
			onChange = { isChecked => checkTag( '_all', isChecked ) }
		/>,
		<CheckboxControl
			key      = 'tag-checkboxes-_untagged'
			label    = { __('Untagged') }
			checked  = { tagChecked( '_untagged' ) }
			onChange = { isChecked => checkTag( '_untagged', isChecked ) }
			disabled = { allTagChecked() }
		/>
	];

	if ( ! isEmpty( tagList ) ) {
		Object.entries( tagList ).forEach( ( [ tagSlug, tagLabel ] ) => {
			tagCheckboxes.push(
				<CheckboxControl
					key      = { `tag-checkboxes-${tagSlug}` }
					label    = { tagLabel }
					checked  = { tagChecked( tagSlug ) }
					onChange = { isChecked => checkTag( tagSlug, isChecked ) }
					disabled = { allTagChecked() }
				/>
			);
		} );
	}

	return (
		<>
			<InspectorControls>
				<PanelBody
					title = { __( 'Tags' ) }
				>
					{ tagCheckboxes }
				</PanelBody>
				<PanelBody
					title = { __( 'Appearance' ) }
				>
					<TextControl
						type = 'number'
						label = { __( 'Font Size (em)' ) }
						value = { fontSize || '' }
						min = { 0.1 }
						max = { 10 }
						onChange = { val => setAttributes( { fontSize: parseFloat( val ) } ) }
					/>
					<p>{ __( 'Font Color' ) }</p>
					<ColorPicker
						color = { fontColor }
						onChangeComplete = { val => setAttributes( { fontColor: val.hex } ) }
					/>
					<p>{ __( 'Background Color' ) }</p>
					<ColorPicker
						color = { backgroundColor }
						onChangeComplete = { val => setAttributes( { backgroundColor: val.hex } ) }
					/>
					<p>{ __( 'Border Color' ) }</p>
					<ColorPicker
						color = { borderColor }
						onChangeComplete = { val => setAttributes( { borderColor: val.hex } ) }
					/>
					<TextControl
						type = 'number'
						label = { __( 'Border Width (px)' ) }
						value = { borderWidth || '' }
						min = { 0.1 }
						max = { 10 }
						onChange = { val => setAttributes( { borderWidth: parseFloat( val ) } ) }
					/>


				</PanelBody>
			</InspectorControls>
			<div>
				This is the collection navigation block edit view.
			</div>
		</>
	);
}

export default CollectionMainNavigationEdit;