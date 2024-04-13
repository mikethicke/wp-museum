/**
 * WordPress dependencies.
 */
import {
	useState,
	useEffect,
} from '@wordpress/element';

import {
	InspectorControls
} from '@wordpress/block-editor'

import { 
	PanelBody,
	PanelRow,
	CheckboxControl,
	RangeControl,
	Button
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

import {
	isEmpty
} from '../javascript/util';

/**
 * WP Museum dependencies.
 */

import { SearchBox, FontSizePanel } from '../components';
import RemoteCollectionGrid from './remote-collection-grid';

const RemoteCollectionSearchBox = props => {
	const {
		close,
		returnCallback,
		remoteData,
		wpmRestBase
	} = props;

	const fetchSearchResults = ( searchText, onlyTitle, updateLastRefresh, updateResults ) => {
		updateLastRefresh( new Date() );
		let fetchURL = `${remoteData.url}${wpmRestBase}/collections?uuid=${remoteData.uuid}`;
		if ( onlyTitle ) {
			fetchURL += `&post_title=${searchText}`;
		} else {
			fetchURL += `&s=${searchText}`;
		}
		fetch( fetchURL ).then( response => {
			if ( ! response.ok ) {
				console.log( response.statusText );
				return;
			} 
			response.json().then( data => updateResults( data ) );
		} );
	}

	return (
		<SearchBox
			close              = { close }
			title              = 'Search for Collection'
			fetchSearchResults = { fetchSearchResults }
			returnCallback     = { returnCallback }
		/>
	);
}

const RemoteCollectionEdit = props => {
	const {
		attributes,
		setAttributes
	} = props;

	const {
		columns,
		collectionID,
		imgDimensions,
		fontSize,
		titleTag,
		displayTitle,
		linkToObjects,
		displayExcerpt,
		displayObjects,
		displayThumbnail,
		imgAlignment,
	} = attributes;
	
	const wpmRestBase = '/wp-json/wp-museum/v1';
	const [ remoteData, setRemoteData ] = useState( {} );
	const [ searchModalOpen, setSearchModalOpen ] = useState( false );

	const onSearchModalReturn = newCollectionID => {
		setAttributes( { collectionID: newCollectionID } );
	}

	return (
		<>
		<InspectorControls>
			<PanelBody
				title = 'Collection'
				initialOpen = { true }
			>
				<PanelRow>
					{ collectionID === null ?
						<div>
							Click 'Search' to embed Collection.
						</div>
						:
						<div>
							Click 'Replace' to embed new Collection.
						</div>
					}
				</PanelRow>
				<PanelRow>
					<Button
						onClick = { () => setSearchModalOpen( true ) }
						isLarge
						isPrimary
						title = 'Search for Collection'
					>
						{ collectionID ? 'Replace' : 'Search' }
					</Button>
					{ searchModalOpen &&
						<RemoteCollectionSearchBox
							close = { () => setSearchModalOpen( false ) }
							returnCallback = { onSearchModalReturn }
							remoteData = { remoteData }
							wpmRestBase = { wpmRestBase }
						/>
					}
				</PanelRow>
			</PanelBody>
			<PanelBody
				initialOpen = { true }
			>
				<RangeControl
					label    = 'Columns'
					value    = { columns }
					onChange = { newCols => setAttributes( { columns: newCols } ) }
					min      = { 1 }
					max      = { 8 }
					disabled = { ! displayObjects }
				/>
			</PanelBody>
			<PanelBody
				initialOpen = { true }
				title       = 'Options'
			>
				<CheckboxControl
					label = 'Display Collection Title'
					checked = { displayTitle }
					onChange = { val => setAttributes( { displayTitle: val } ) }
				/>
				<CheckboxControl
					label = 'Display Excerpt'
					checked = { displayExcerpt }
					onChange = { val => setAttributes( { displayExcerpt: val } ) }
				/>
				<CheckboxControl
					label = 'Display Collection Thumbnail'
					checked = { displayThumbnail }
					onChange = { val => setAttributes( { displayThumbnail: val } ) }
				/>
				<CheckboxControl
					label = 'Display Object Images'
					checked = { displayObjects }
					onChange = { val => setAttributes( { displayObjects: val } ) }
				/>
				<CheckboxControl
					label = 'Link to Objects'
					checked = { linkToObjects }
					onChange = { val => setAttributes( { linkToObjects: val } ) }
				/>
			</PanelBody>
			<FontSizePanel
				setAttributes = { setAttributes }
				titleTag      = { titleTag }
				fontSize      = { fontSize }
				initialOpen   = { false }
			/>
		</InspectorControls>
		<RemoteCollectionGrid
			attributes = { attributes }
			setSearchModalOpen = { setSearchModalOpen }
			remoteData = { remoteData }
			setRemoteData = { setRemoteData }
			wpmRestBase = { wpmRestBase }
		/>
		</>
	)	
}

export default RemoteCollectionEdit;
