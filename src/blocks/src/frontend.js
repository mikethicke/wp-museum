import {
	render
} from '@wordpress/element';

import AdvancedSearchFront from './advanced-search/front';
import BasicSearchFront from './basic-search/front';
import EmbeddedSearchFront from './embedded-search/front';
import CollectionObjectsFront from './collection/front';
import ObjectPostImageGallery from './components/object-post-image-gallery';
import CollectionMainNavigationFront from './collection-main-navigation/front';
import { attributesFromJSON } from './util';

import './style.scss';

const advancedSearchElements = document.getElementsByClassName('wpm-advanced-search-block-frontend');
if ( !! advancedSearchElements ) {
	for ( let i = 0; i < advancedSearchElements.length; i++ ) {
		const advancedSearchElement = advancedSearchElements[i];
		const attributes = attributesFromJSON( advancedSearchElement.dataset.attributes );
		if ( typeof attributes['defaultSearch'] != 'string' ) {
			attributes['defaultSearch'] = JSON.stringify( attributes['defaultSearch'] );
		}
		render (
			<AdvancedSearchFront
				attributes = { attributes }
			/>,
			advancedSearchElement
		);
	}
}

const basicSearchElements = document.getElementsByClassName('wpm-basic-search-block-frontend');
if ( !! basicSearchElements ) {
	for ( let i = 0; i < basicSearchElements.length; i++ ) {
		const basicSearchElement = basicSearchElements[i];
		const attributes = attributesFromJSON( basicSearchElement.dataset.attributes );
		render (
			<BasicSearchFront
				attributes = { attributes }
			/>,
			basicSearchElement
		);
	}
}

const embeddedSearchElements = document.getElementsByClassName('wpm-embedded-search-block-frontend');
if ( !! embeddedSearchElements ) {
	for ( let i = 0; i < embeddedSearchElements.length; i++ ) {
		const embeddedElement = embeddedSearchElements[i];
		const attributes = attributesFromJSON( embeddedElement.dataset.attributes );
		render (
			<EmbeddedSearchFront
				attributes = { attributes }
			/>,
			embeddedElement
		);
	}
}

const collectionObjectsBlockElements = document.getElementsByClassName('wpm-collection-objects-block');
if ( !! collectionObjectsBlockElements ) {
	for ( let i = 0; i < collectionObjectsBlockElements.length; i++ ) {
		const collectionObjectsBlockElement = collectionObjectsBlockElements[i];
		const postID = parseInt( collectionObjectsBlockElement.dataset.postId );
		render (
			<CollectionObjectsFront
				postID = { postID }
			/>,
			collectionObjectsBlockElement
		);
	}
}

const objectImageGalleryElements = document.getElementsByClassName('wpm-objectposttype-image-gallery');
if ( !! objectImageGalleryElements ) {
	for ( let i = 0; i < objectImageGalleryElements.length; i++ ) {
		const objectImageGalleryElement = objectImageGalleryElements[i];
		const postId = parseInt( objectImageGalleryElement.dataset.postId );
		render (
			<ObjectPostImageGallery
				postId = { postId }
			/>,
			objectImageGalleryElement
		);
	}
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
