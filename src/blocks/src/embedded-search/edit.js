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
	PanelBody,
	PanelRow,
	RangeControl,
} from '@wordpress/components';

import {
	__
} from '@wordpress/i18n';

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
		maxWidth,
	} = attributes;

	return (
		<>
		<InspectorControls>
			<PanelBody title = 'Search Options'>
				<PanelRow>
					<label>
						Search Page URL:
						<input
							className = 'wpm-embedded-search-search-page-input'
							type      = 'text'
							value     = { searchPageURL }
							onChange  = { event => setAttributes( { searchPageURL: event.target.value } ) }
						/>
					</label>
				</PanelRow>
				<PanelRow>
					<RangeControl
						label = { __( 'Max Width (%)' ) }
						value = { maxWidth }
						min   = '0'
						max   = '100'
						step  = '1'
						initialPosition = '100'
						onChange = { val => setAttributes( { maxWidth: val } ) }
						withInputField
						allowReset
					/>
				</PanelRow>
			</PanelBody>
		</InspectorControls>
		<div 
			className = 'wpm-embedded-search-block'
			style = { { maxWidth: `${maxWidth}%` } }
		>
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
		</div>
		</>
	);
}

export default EmbeddedSearchEdit;