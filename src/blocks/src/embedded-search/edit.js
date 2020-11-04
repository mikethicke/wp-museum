/**
 * Embedded search block, which redirects to a supplied search page.
 */

/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	RichText,
} from '@wordpress/blockEditor';

import {
	PanelBody
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import EmbeddedSearch from '../components/embedded-search';

/**
 * Embedded search of the catalogue.
 * 
 * @param {Object} props The block's properties.
 */
const EmbeddedSearchEdit = props => {
	const {
		attributes,
		setAttributes
	} = props;

	const {
		searchPageURL = '',
		headerText    = '',
	} = attributes;

	return (
		<>
		<InspectorControls>
			<PanelBody title = 'Search Options'>
				<label>
					Search Page URL:
					<input
						className = 'wpm-embedded-search-search-page-input'
						type      = 'text'
						value     = { searchPageURL }
						onChange  = { event => setAttributes( { searchPageURL: event.target.value } ) }
					/>
				</label>
			</PanelBody>
		</InspectorControls>
		<RichText
			tagName  = 'h2'
			value    = { headerText }
			onChange = { val => setAttributes( { headerText: val } ) }
		/>
		<EmbeddedSearch
			showTitleToggle = { false }
			autoFocus       = { false }
			searchPageURL   = { searchPageURL }
			showReset       = { false }
		/>
		</>
	);
}

export default EmbeddedSearchEdit;