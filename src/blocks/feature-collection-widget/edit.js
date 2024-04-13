/**
 * WordPress dependencies
 */
import {
	useState,
	useEffect
} from '@wordpress/element';

import {
	useSelect
} from '@wordpress/data';

import apiFetch from '@wordpress/api-fetch';

import {
	InspectorControls
} from '@wordpress/block-editor';

import {
	CheckboxControl
} from '@wordpress/components';

import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FeaturedCollection } from '../../components';

import {
	baseRestPath, isEmpty
} from '../../javascript/util';

const FeaturedCollectionEdit = props => {
	const {
		attributes,
		setAttributes
	} = props;

	const {
		showFeatureImage,
		showDescription
	} = attributes;

	const [ objectData, setObjectData ] = useState( {} );

	const postID = useSelect (
		select => select( 'core/editor' ).getCurrentPostId(),
		[]
	);

	const getObjectData = () => {
		apiFetch( { path: `${baseRestPath}/all/${postID}` } )
			.then( result => {
				setObjectData( result );
			} );
	}

	useEffect( () => {
		getObjectData();
	}, [] );

	let collectionBoxes = [];
	if ( ! isEmpty( objectData ) && Array.isArray( objectData.collections ) ) {
		collectionBoxes = objectData.collections.map( collectionID => 
			<FeaturedCollection
				{ ...attributes }
				key          = { collectionID }
				collectionID = { collectionID }
			/>
		);
	}

	return (
		<>
		<InspectorControls>
			<CheckboxControl
				label    = { __( 'Show collection featured image.' ) }
				checked  = { showFeatureImage }
				onChange = { checked => setAttributes( { showFeatureImage: checked } ) }
			/>
			<CheckboxControl
				label    = { __( 'Show collection description') }
				checked  = { showDescription }
				onChange = { checked => setAttributes( { showDescription: checked } ) }
			/>
		</InspectorControls>
		<div className = 'wpm-feature-collection-widget'>
			{ collectionBoxes.length > 0 && collectionBoxes }
		</div>
		</>
	);
}

export default FeaturedCollectionEdit;