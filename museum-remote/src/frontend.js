import {
	render
} from '@wordpress/element';

import CollectionBlockFront from './collection-block/front'

const collectionElements = document.getElementsByClassName( 'wpm-remote-collection-block-front');
if ( !! collectionElements ) {
	for ( let i = 0; i < collectionElements.length; i++ ) {
		const collectionElement = collectionElements[i];
		const collectionId = collectionElement.id.substr( 'collection'.length );
		const attributes = window[`attributesCollection${collectionId}`];
		render(
			<CollectionBlockFront
				attributes = { attributes }
			/>,
			collectionElement
		);
	}
}