/**
 * A minimal embedded search box.
 */

import {
	Button,
	CheckboxControl
} from '@wordpress/components';

import {
	useState,
	useEffect,
} from '@wordpress/element';
import { isEmpty } from '../util';

const EmbeddedSearch = props => {
	const {
		searchDefaults   = {},
		runSearch        = null,
		updateSearchText = null,
		searchButtonText = 'Search',
		showTitleToggle  = false,
		onlyTitleDefault = true,
		showReset        = true,
		autoFocus        = true,
		resetButtonText  = 'Reset',
		placeholderText  = '',
		searchPageURL    = '',
	} = props;

	const [ searchText, _setSearchText ] = useState( '' );
	const [ onlyTitle, setOnlyTitle ] = useState( onlyTitleDefault );

	useEffect( () => {
		if ( !! searchDefaults['searchText'] ) {
			setSearchText( searchDefaults['searchText'] );
		}
	}, [ searchDefaults ] );

	const setSearchText = newSearchText => {
		_setSearchText( newSearchText );
		if ( !!  updateSearchText ) {
			updateSearchText( newSearchText );
		}
	}

	const doSearch = ( newSearchValues = {} ) => {
		const searchValues = 
			! isEmpty( newSearchValues ) ? newSearchValues : 
			{
				...searchDefaults,
				onlyTitle   : onlyTitle,
				searchText  : searchText,
			};
		if ( runSearch ) {
			runSearch( searchValues );
		} else if ( searchPageURL ) {
			const queryString = new URLSearchParams( searchValues ).toString();
			window.open( `${searchPageURL}?${queryString}`, '_self' );
		}
	}

	const resetSearch = () => {
		setSearchText( '' );
		const searchValues = {
			...searchDefaults,
			onlyTitle  : onlyTitle,
			searchText : ''
		}
		doSearch( searchValues );
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
					autoFocus   = { autoFocus }
				/>
				<Button
					isPrimary
					className = 'wpm-embedded-search-button'
					onClick   = { () => doSearch() }
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
						onChange = { setOnlyTitle }
					/>
				}
			</div>
		</div>
	);
}

export default EmbeddedSearch;