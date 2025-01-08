/**
 * Frontend display of embedded search block.
 */

import { createRoot } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { EmbeddedSearch } from '../../components';
import { attributesFromJSON } from '../../javascript/util';

const EmbeddedSearchFront = props => {
	const {
		attributes
	} = props;

	const {
		searchPageURL,
		headerText,
		align,
		maxWidth,
		showTitleToggle,
		advancedSearchURL
	} = attributes;

	return (
		<div 
			className = { `wpm-embedded-search-block align${align}`}
			style = { { maxWidth: `${maxWidth}%` } }
		>
			{ !! headerText &&
				<h2>
					{ headerText }
				</h2>
			}
			<EmbeddedSearch
				showTitleToggle   = { showTitleToggle }
				searchPageURL     = { searchPageURL }
				showReset         = { false }
				advancedSearchURL = { advancedSearchURL }
			/>
		</div>
	)
}

const embeddedSearchElements = document.getElementsByClassName('wpm-embedded-search-block-frontend');
if ( !! embeddedSearchElements ) {
	for ( let i = 0; i < embeddedSearchElements.length; i++ ) {
		const embeddedElement = embeddedSearchElements[i];
		const attributes = attributesFromJSON( embeddedElement.dataset.attributes );
		const root = createRoot( embeddedElement );
		root.render (
			<EmbeddedSearchFront
				attributes = { attributes }
			/>
		);
	}
}