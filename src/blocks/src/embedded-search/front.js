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
				showTitleTogge = { false }
				searchPageURL  = { searchPageURL }
				showReset      = { false }
			/>
		</div>
	)
}

export default EmbeddedSearchFront;