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

import CollectionMainNavigation from '../components/collection-main-navigation';

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
		verticalSpacing,
		useDefaultFontSize,
		useDefaultFontColor,
		useDefaultBackgroundColor,
		useDefaultBorderColor,
		useDefaultBorderWidth,
		useDefaultVerticalSpacing,
		subCollectionIndent,
		tags,
	} = attributes;

	const [ collectionData, setCollectionData ] = useState( {} );
	const [ tagList, setTagList ] = useState( {} );

	useEffect( () => {
		updateCollectionData();
	}, [ tags ] );
	
	useEffect( () => {
		updateTagList();
	}, [] );

	const updateCollectionData = () => {
		let collectionPath = `${baseRestPath}/collections`;
		if ( tags.length > 0 ) {
			const tagsString = tags.join();
			collectionPath += `/?tags=${tagsString}`
		}
		apiFetch( { path: collectionPath } ).then( result => setCollectionData( result ) );
	}

	const updateTagList = () => {
		apiFetch( { path: `${wordPressRestBase}/collection_tag`} )
			.then( result => {
				if ( Array.isArray( result ) ) {
					const newTagList = {};
					result.forEach( taxItem => {
						newTagList[ taxItem.slug ] = taxItem.name;
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
					<CheckboxControl
						label    = { __( 'Default font size' ) }
						checked  = { useDefaultFontSize }
						onChange = { isChecked => setAttributes( { useDefaultFontSize: isChecked } ) }
					/>
					<TextControl
						type     = 'number'
						label    = { __( 'Font Size (em)' ) }
						value    = { fontSize || '' }
						min      = { 0.1 }
						max      = { 10 }
						disabled = { useDefaultFontSize }
						onChange = { val => setAttributes( { fontSize: parseFloat( val ) } ) }
					/>
					<p>{ __( 'Font Color' ) }</p>
					<CheckboxControl
						label    = { __( 'Default font color' ) }
						checked  = { useDefaultFontColor }
						onChange = { isChecked => setAttributes( { useDefaultFontColor: isChecked } ) }
					/>
					<ColorPicker
						disabled         = { useDefaultFontColor }
						color            = { fontColor }
						onChangeComplete = { val => setAttributes( { fontColor: val.hex } ) }
					/>
					<p>{ __( 'Background Color' ) }</p>
					<CheckboxControl
						label    = { __( 'Default background color' ) }
						checked  = { useDefaultBackgroundColor }
						onChange = { isChecked => setAttributes( { useDefaultBackgroundColor: isChecked } ) }
					/>
					<ColorPicker
						disabled = { useDefaultBackgroundColor }
						color = { backgroundColor }
						onChangeComplete = { val => setAttributes( { backgroundColor: val.hex } ) }
					/>
					<p>{ __( 'Border Color' ) }</p>
					<CheckboxControl
						label    = { __( 'Default border color' ) }
						checked  = { useDefaultBorderColor }
						onChange = { isChecked => setAttributes( { useDefaultBorderColor: isChecked } ) }
					/>
					<ColorPicker
						disabled         = { useDefaultBorderColor }
						color            = { borderColor }
						onChangeComplete = { val => setAttributes( { borderColor: val.hex } ) }
					/>
					<CheckboxControl
						label    = { __( 'Default border width' ) }
						checked  = { useDefaultBorderWidth }
						onChange = { isChecked => setAttributes( { useDefaultBorderWidth: isChecked } ) }
					/>
					<TextControl
						disabled = { useDefaultBorderWidth }
						type     = 'number'
						label    = { __( 'Border Width (px)' ) }
						value    = { borderWidth || '' }
						min      = { 0.1 }
						max      = { 10 }
						onChange = { val => setAttributes( { borderWidth: parseFloat( val ) } ) }
					/>
					<CheckboxControl
						label    = { __( 'Default vertical spacing' ) }
						checked  = { useDefaultVerticalSpacing }
						onChange = { isChecked => setAttributes( { useDefaultVerticalSpacing: isChecked } ) }
					/>
					<TextControl
						disabled = { useDefaultVerticalSpacing }
						type     = 'number'
						label    = { __( 'Vertical Spacing (em)' ) }
						value    = { verticalSpacing || '' }
						min      = { 0 }
						max      = { 3 }
						onChange = { val => setAttributes( { verticalSpacing: parseFloat( val ) } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div>
				<CollectionMainNavigation
					attributes     = { attributes }
					collectionData = { collectionData }
				/>
			</div>
		</>
	);
}

export default CollectionMainNavigationEdit;