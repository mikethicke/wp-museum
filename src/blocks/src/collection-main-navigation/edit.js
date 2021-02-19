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
	PanelBody,
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */

import {
	baseRestPath
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
	} = props;

	const [ collectionData, setCollectionData ] = useState( {} );

	useEffect( () => {
		updateCollectionData();
	}, [] );

	const updateCollectionData = () => {
		apiFetch( { path: `${baseRestPath}/collections` } ).then( result => setCollectionData( result ) );
	}

	return (
		<div>
			This is the collection navigation block edit view.
		</div>
	);
}

export default CollectionMainNavigationEdit;