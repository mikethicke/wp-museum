/**
 * WordPress dependencies
 */
import {
	useState,
	useEffect
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import {
	baseRestPath,
} from '../util';

import CollectionMainNavigation from '../components/collection-main-navigation';

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

export default CollectionMainNavigationFront;