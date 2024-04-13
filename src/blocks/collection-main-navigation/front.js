/**
 * WordPress dependencies
 */
import {
	useState,
	useEffect,
	render
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import {
	baseRestPath,
	attributesFromJSON
} from '../../javascript/util';

import { CollectionMainNavigation } from '../../components';

const CollectionMainNavigationFront = props => {
	const {
		attributes
	} = props;

	const {
		tags,
	} = attributes;

	const [ collectionData, setCollectionData ] = useState( {} );

	const updateCollectionData = () => {
		let collectionPath = `${baseRestPath}/collections`;
		if ( tags.length > 0 ) {
			const tagsString = tags.join();
			collectionPath += `/?tags=${tagsString}`
		}
		apiFetch( { path: collectionPath } ).then( result => setCollectionData( result ) );
	}

	useEffect( () => {
		updateCollectionData();
	}, [] );

	return (
		<div>
			<CollectionMainNavigation
				attributes     = { attributes }
				collectionData = { collectionData }
			/>
		</div>
	);

}

const collectionMainNavigationElements = document.getElementsByClassName('wpm-collection-main-navigation-front');
if ( collectionMainNavigationElements ) {
	for ( let i = 0; i < collectionMainNavigationElements.length; i++ ) {
		const collectionMainNavigationElement = collectionMainNavigationElements[i];
		const attributes = attributesFromJSON( collectionMainNavigationElement.dataset.attributes );
		render (
			<CollectionMainNavigationFront
				attributes = { attributes }
			/>,
			collectionMainNavigationElement
		);
	}
}