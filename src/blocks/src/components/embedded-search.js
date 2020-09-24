/**
 * A minimal embedded search box.
 */

import {
	Button,
	CheckboxControl
} from '@wordpress/components';

import {
	useState,
} from '@wordpress/element';

const EmbeddedSearch = props => {
	const {
		searchDefaults,
		runSearch,
		searchButtonText = 'Search',
		showTitleToggle  = false,
		showReset        = true,
		resetButtonText  = 'Reset',
		placeholderText  = '',
	} = props;

	const [ searchText, setSearchText ] = useState( '' );
	const [ onlyTitle, setOnlyTitle ] = useState( true );

	const doSearch = () => {
		const searchValues = {
			...searchDefaults,
			onlyTitle   : onlyTitle,
			searchText  : searchText,
		}
		runSearch( searchValues );
	}

	const resetSearch = () => {
		setSearchText( '' );
		const searchValues = {
			...searchDefaults,
			onlyTitle  : onlyTitle,
			searchText : ''
		}
		runSearch( searchValues );
	}

	const handleKeyPress = ( event ) => {
		if ( event.key == 'Enter' ) {
			event.stopPropagation();
			doSearch();
		}
	}

	return (
		<div className = 'wpm-embedded-search'>
			<div className = 'embedded-search-input'>
				<input
					type        = 'text'
					placeholder = { placeholderText }
					onKeyPress  = { handleKeyPress }
					value       = { searchText }
					onChange    = { event => setSearchText( event.target.value ) }
				/>
				<Button
					isPrimary
					className = 'wpm-embedded-search-button'
					onClick   = { doSearch }
				>
					{ searchButtonText }
				</Button>
				{ showReset &&
					<Button
						isSecondary
						className = 'wpm-embedded-search-button'
						onClick   = { resetSearch }
					>
						{ resetButtonText }
					</Button>
				}
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