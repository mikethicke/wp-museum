import apiFetch from '@wordpress/api-fetch';
import {
	useState,
	useEffect,
} from '@wordpress/element';


import withPagination from '../components/with-pagination';
import EmbeddedSearch from '../components/embedded-search';
import ObjectGrid from '../components/object-grid';

import {
	baseRestPath
} from '../util';

const PaginatedObjectGrid = withPagination( ObjectGrid );

const CollectionObjectsFront = props => {
	const {
		postID,
		resultsPerPage = 20,
	} = props;

	const searchDefaults = {
		selectedCollections: [ postID ],
		numberposts: resultsPerPage,
	}
	
	const [ mObjects, setMObjects ] = useState( [] );

	const updateMObjects = ( newMObjects ) => {
		
	}

	useEffect( () => {
		const initialSearchValues = {
			...searchDefaults,
			page : 1
		}
		runSearch( initialSearchValues );
	}, [] );

	const runSearch = ( searchValues ) => {
		apiFetch( {
			path   : `${baseRestPath}/search`,
			method : 'POST',
			data   : searchValues
		} ).then( result => {
			setMObjects( result );
		} );
	}

	let currentPage = 1;
	let totalPages = 0;
	if ( mObjects.length > 0 && typeof mObjects[0].query_data != 'undefined' ) {
		currentPage = mObjects[0].query_data.current_page;
		totalPages = mObjects[0].query_data.num_pages;
	}

	return (
		<div>
			<EmbeddedSearch
				searchDefaults = { searchDefaults }
				runSearch      = { runSearch }
			/>
			{ mObjects.length > 0 &&
				<PaginatedObjectGrid
					currentPage    = { currentPage }
					totalPages     = { totalPages }
					searchCallback = { runSearch }
					searchParams   = { searchDefaults }
					mObjects       = { mObjects }
				/>
			}
		</div>
	);
}

export default CollectionObjectsFront;