
/**
 * WordPress dependencies
 */

import {
	useState,
	useEffect
} from '@wordpress/element';

import {
	InspectorControls
} from '@wordpress/blockEditor';

import { 
	PanelBody,
	CheckboxControl,
	SelectControl
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */

import AdvancedSearchUI from '../components/advanced-search-ui';
import {
	PaginatedObjectList
} from '../components/object-list';

const AdvancedSearchEdit = props => {
	const {
		attributes,
		setAttributes
	} = props;

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
		for ( const [ key, value ] of Object.entries( searchParams ) ) {
			if ( key != 'page' && value != currentSearchParams[key] ) {
				searchParams['page'] = 1;
				break;
			}
		}
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
		<InspectorControls>
			<PanelBody
				title = 'Search Options'
			>
				<CheckboxControl
					label = 'Fix Search'
					checked = { fixSearch }
					onChange = { val => setAttributes( { fixSearch: val } ) }
				/>
				<CheckboxControl
					label = 'Run Search on Load'
					checked = { runOnLoad }
					onChange = { val => setAttributes( { runOnLoad: val } ) }
				/>
			</PanelBody>
			<PanelBody
				title = 'Search Sections'
			>
				<CheckboxControl
					label = 'Show Object Type'
					checked = { showObjectType }
					onChange = { val => setAttributes( { showObjectType: val } ) }
				/>
				<CheckboxControl
					label = 'Show Title Toggle'
					checked = { showTitleToggle }
					onChange = { val => setAttributes( { showTitleToggle: val } ) }
				/>
				<CheckboxControl
					label = 'Show Flags'
					checked = { showFlags }
					onChange = { val => setAttributes( { showFlags: val } ) }
				/>
				<CheckboxControl
					label = 'Show Collections'
					checked = { showCollections }
					onChange = { val => setAttributes( { showCollections: val } ) }
				/>
				<CheckboxControl
					label = 'Show Fields'
					checked = { showFields }
					onChange = { val => setAttributes( { showFields: val } ) }
				/>
			</PanelBody>
			<PanelBody
				title = 'Results'
			>
				<SelectControl
					label = 'Results per Page'
					value = { resultsPerPage }
					onChange = { val => setAttributes( { resultsPerPage: parseInt( val ) } ) }
					options = { [
						{ value: 20,  label: '20' },
						{ value: 40,  label: '40' },
						{ value: 60,  label: '60' },
						{ value: 80,  label: '80' },
						{ value: 100, label: '100' },
						{ value: -1,  label: 'Unlimited' }
					] }
				/>
			</PanelBody>
		</InspectorControls>
		<AdvancedSearchUI
			defaultSearch   = { defaultSearch }
			showFlags       = { showFlags }
			showCollections = { showCollections }
			showFields      = { showFields }
			showObjectType  = { showObjectType }
			fixSearch       = { fixSearch }
			showTitleToggle = { showTitleToggle }
			collectionData  = { collectionData }
			kindsData       = { kindsData }
			getFieldData    = { getFieldData }
			inEditor        = { true }
			setAttributes   = { setAttributes }
			onSearch        = { onSearch }
		/>
		{ searchResults &&
			<PaginatedObjectList
				objects = { searchResults }
				displayImages = { true }
			/>
		}
		</>
	);
}

export default AdvancedSearchEdit;