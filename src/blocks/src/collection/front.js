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
		selectedCollections : [ postID ],
		per_page            : resultsPerPage,
		status              : 'publish',
	}
	
	const [ mObjects, setMObjects ] = useState( [] );
	const [ currentPage, setCurrentPage ] = useState( 1 );
	const [ totalPages, setTotalPages ] = useState( 0 );
	const [ currentSearchParams, setCurrentSearchParams ] = useState( searchDefaults );

	useEffect( () => {
		const initialSearchValues = {
			...searchDefaults,
			page   : 1,
		}
		runSearch( initialSearchValues );
	}, [] );

	const runSearch = ( searchValues ) => {
		setCurrentSearchParams( searchValues );
		apiFetch( {
			path   : `${baseRestPath}/search`,
			method : 'POST',
			data   : searchValues,
			parse  : false,
		} )
			.then( response => {
				setCurrentPage( response.headers.get( 'X-WP-Page' ) || 1 );
				setTotalPages( response.headers.get( 'X-WP-TotalPages' || 0 ) );
				return response.json();
			} )
			.then ( result => {
				setMObjects( result );
			});
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
					searchParams   = { currentSearchParams }
					mObjects       = { mObjects }
				/>
			}
		</div>
	);
}

export default CollectionObjectsFront;