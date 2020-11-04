/**
 * WordPress dependencies
 */
import {
	useState,
	useEffect,
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import {
	baseRestPath
} from '../util';

import EmbeddedSearch from '../components/embedded-search';
import { PaginatedObjectList } from '../components/object-list';

const BasicSearchFront = props => {
	const {
		attributes 
	} = props;

	const {
		searchText         = '',
		resultsPerPage     = 20,
		advancedSearchLink = '',
		acceptGETRequest   = true,
	} = attributes;

	const [ currentSearchParams, setCurrentSearchParams ] = useState( [] );
	const [ searchResults, setSearchResults ] = useState( [] );

	const onSearch = searchParams => {
		searchParams['numberposts'] = resultsPerPage;
		if ( searchParams['searchText'] ) {
			apiFetch( {
				path:   `${baseRestPath}/search`,
				method: 'POST',
				data:   searchParams
			} ).then( result => {
				setSearchResults( result );
			} );
		} else {
			setSearchResults( [] );
		}
	}

	useEffect( () => {
		if ( acceptGETRequest && searchText ) {
			setCurrentSearchParams( { searchText: searchText } );
			onSearch( { searchText: searchText } );
		} else {
			setCurrentSearchParams( { searchText: '' } );
		}
	}, [] );

	let currentPage = 1;
	let totalPages = 0;
	if ( searchResults.length > 0 && typeof searchResults[0].query_data != 'undefined' ) {
		currentPage = searchResults[0].query_data.current_page;
		totalPages = searchResults[0].query_data.num_pages;
	}

	return (
		<div className = 'wpm-basic-search-block'>
			<EmbeddedSearch
				searchDefaults  = { currentSearchParams }
				runSearch       = { onSearch }
				showTitleToggle = { true }
			/>
			{ !! advancedSearchLink &&
				<a
					href = { advancedSearchLink }
				>
					Advanced Search
				</a>
			}
			{ searchResults &&
				<PaginatedObjectList
					currentPage    = { currentPage }
					totalPages     = { totalPages }
					searchCallback = { onSearch }
					searchParams   = { currentSearchParams }
					mObjects       = { searchResults }
					displayImages  = { true }
				/>
			}
		</div>
	);
}

export default BasicSearchFront;