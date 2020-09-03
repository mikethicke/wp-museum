/**
 * A minimal embedded search box.
 */

import {
	Button,
	CheckboxControl
} from '@wordpress/components';

import {
	useState
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import {
	baseRestPath
} from '../util';

const EmbeddedSearch = props => {
	const {
		searchDefaults,
		resultsCallback,
		searchButtonText = 'Search',
		resultsPerPage   = 50,
		page             = 1,
		showTitleToggle  = false,
	} = props;

	const [ searchText, setSearchText ] = useState( '' );
	const [ onlyTitle, setOnlyTitle ] = useState( true );

	const doSearch = () => {
		const searchValues = {
			...searchDefaults,
			onlyTitle   : onlyTitle,
			searchText  : searchText,
			page        : page,
			numberposts : resultsPerPage
		}

		apiFetch( {
			path   : `${baseRestPath}/search`,
			method : 'POST',
			data   : searchValues
		} ).then( result => {
			resultsCallback( result );
		} );
	}

	return (
		<div className = 'wpm-embedded-search'>
			<div className = 'embedded-search-input'>
				<input
					type = 'text'
					value = { searchText }
					onChange = { event => setSearchText( event.target.value ) }
				/>
				<Button
					isPrimary
					className = 'wpm-embedded-search-button'
					onClick   = { doSearch }
				>
					{ searchButtonText }
				</Button>
			</div>
			<div className = 'wpm-embedded-search-title-toggle'>
				{ showTitleToggle &&
					<CheckboxControl
						label = 'Only search titles'
						checked = { onlyTitle }
						onChange = { val => setOnlyTitle( val ) }
					/>
				}
			</div>
		</div>
	);
}

export default EmbeddedSearch;