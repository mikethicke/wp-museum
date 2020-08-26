import {
	render
} from '@wordpress/element';

import AdvancedSearchFront from './advanced-search/front';
import { cleanAttributes } from './util';

import './style.scss';

const advancedSearchElements = document.getElementsByClassName('wpm-advanced-search-block-frontend');
if ( !! advancedSearchElements ) {
	for ( let i = 0; i < advancedSearchElements.length; i++ ) {
		const advancedSearchElement = advancedSearchElements[i];
		const idString = advancedSearchElement.id.substr( 'advanced-search-'.length );
		const attributes = window[ `advancedSearch${idString}` ];
		cleanAttributes( attributes );
		render(
			<AdvancedSearchFront
				attributes = { attributes }
			/>,
			advancedSearchElement
		);
	}
}

