/**
 * Embedded search block, which redirects to a supplied search page.
 */

/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

import {
	CheckboxControl,
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
import { EmbeddedSearch } from '../../components';

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
		searchPageURL     = '',
		headerText        = '',
		advancedSearchURL = '',
		showTitleToggle   = true,
		maxWidth,
	} = attributes;

	const blockProps = useBlockProps({
		className: 'wpm-embedded-search-block',
		style: { maxWidth: `${maxWidth}%` }
	});

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
					<label>
						Advanced Search URL:
						<input
							className = 'wpm-embedded-search-advanced-search-page-input'
							type      = 'text'
							value     = { advancedSearchURL }
							onChange  = { event => setAttributes( { advancedSearchURL: event.target.value } ) }
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
				<PanelRow>
					<CheckboxControl
						label = { __( 'Show title toggle' ) }
						checked = { showTitleToggle }
						onChange = { val => setAttributes( { showTitleToggle: val } ) }
					/>
				</PanelRow>
			</PanelBody>
		</InspectorControls>
		<div { ...blockProps }>
			<RichText
				tagName  = 'h2'
				value    = { headerText }
				onChange = { val => setAttributes( { headerText: val } ) }
			/>
			<EmbeddedSearch
				showTitleToggle   = { showTitleToggle }
				searchPageURL     = { searchPageURL }
				showReset         = { false }
				advancedSearchURL = { advancedSearchURL }
			/>
		</div>
		</>
	);
}

export default EmbeddedSearchEdit;