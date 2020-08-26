import {
	useState,
	useEffect
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import AdvancedSearchUI from '../components/advanced-search-ui';
import ObjectList from '../components/object-list';

const AdvancedSearchFront = props => {
	const { attributes } = props;

	const {
		defaultSearch,
		fixSearch,
		runOnLoad,
		showObjectType,
		showTitleToggle,
		showFlags,
		showCollections,
		showFields,
		resultsPerPage
	} = attributes;

	const [ collectionData , setCollectionData ] = useState( {} );
	const [ kindsData, setKindsData ] = useState( [] );
	const [ searchResults, setSearchResults ] = useState( [] );

	const baseRestPath = '/wp-museum/v1';

	useEffect( () => {
		updateCollectionData();
		updateKindsData();

		if ( runOnLoad && defaultSearch ) {
			onSearch( JSON.parse( defaultSearch ) );
		}
	}, [] );

	const updateCollectionData = () => {
		apiFetch( { path: `${baseRestPath}/collections` } ).then( result => setCollectionData( result ) );
	}

	const updateKindsData = () => {
		apiFetch( { path: `${baseRestPath}/mobject_kinds` } ).then( result => setKindsData( result ) );
	}

	const getFieldData = postType => {
		return apiFetch( { path: `${baseRestPath}/${postType}/fields_all` } );
	}

	const onSearch = searchParams => {
		apiFetch( {
			path:   `${baseRestPath}/search`,
			method: 'POST',
			data:   searchParams
		} ).then( result => {
			setSearchResults( result );
		} );
	}

	return (
		<>
			{ ! fixSearch &&
				<AdvancedSearchUI
					defaultSearch   = { defaultSearch }
					showFlags       = { showFlags }
					showCollections = { showCollections }
					showFields      = { showFields }
					showObjectType  = { showObjectType }
					showTitleToggle = { showTitleToggle }
					collectionData  = { collectionData }
					kindsData       = { kindsData }
					getFieldData    = { getFieldData }
					inEditor        = { false }
					onSearch        = { onSearch }
				/>
			}
			{ searchResults &&
				<ObjectList
					objects = { searchResults }
					displayImages = { true }
				/>
			}
		</>
	);
}

export default AdvancedSearchFront;