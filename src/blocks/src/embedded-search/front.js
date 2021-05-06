/**
 * Frontend display of embedded search block.
 */

/**
 * Internal dependencies
 */
import EmbeddedSearch from '../components/embedded-search';

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

export default EmbeddedSearchFront;