import {
	render
} from '@wordpress/element';

import CollectionBlockFront from './collection-block/front'

const cleanAttributes = attributes => {
	for ( const [ key, value ] of Object.entries( attributes) ) {
		if ( ! isNaN( value ) ) {
			let newValue = value;
			if ( newValue === '' ) {
				newValue = null;
			} else {
				newValue = parseInt( value );
				if ( newValue === 0 ) {
					newValue = false;
				}
			}
			attributes[key] = newValue;
		}
	}
	return null;
}

const collectionElements = document.getElementsByClassName( 'wpm-remote-collection-block-front');
if ( !! collectionElements ) {
	for ( let i = 0; i < collectionElements.length; i++ ) {
		const collectionElement = collectionElements[i];
		const collectionId = collectionElement.id.substr( 'collection'.length );
		const attributes = window[`attributesCollection${collectionId}`];
		cleanAttributes( attributes );
		render(
			<CollectionBlockFront
				attributes = { attributes }
			/>,
			collectionElement
		);
	}
}