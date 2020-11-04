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
		headerText
	} = attributes;

	return (
		<div className = 'wpm-embedded-search-block'>
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