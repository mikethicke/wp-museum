/**
 * WordPress dependencies.
 */
import {
	useState,
	useEffect,
} from '@wordpress/element';

import {
	InspectorControls
} from '@wordpress/blockEditor'

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
} from '../util';

/**
 * WP Museum dependencies.
 */

import { SearchBox } from '../components/search-box';
import FontSizePanel from '../components/font-size-panel';
import ObjectImageGrid from '../components/object-image-grid';
import ThumbnailImage from '../components/thumbnail-image';
import RemoteObjectModal from './remote-object-modal';

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
		numObjects,
		columns,
		collectionID,
		collectionURL,
		collectionObjects,
		thumbnailURL,
		imgDimensions,
		title,
		excerpt,
		fontSize,
		titleTag,
		displayTitle,
		linkToObjects,
		displayExcerpt,
		displayObjects,
		displayThumbnail,
		imgAlignment,
	} = attributes;
	
	const mrRestBase = '/museum-remote/v1';
	const wpmRestBase = '/wp-json/wp-museum/v1';
	const [ remoteData, setRemoteData ] = useState( {} );
	const [ searchModalOpen, setSearchModalOpen ] = useState( false );
	const [ selectedObjectID, setSelectedObjectID ] = useState( null );
	
	useEffect( () => refreshRemoteData(), [] );
	useEffect( () => {
		if ( ! collectionID ) {
			return;
		}
		refreshCollectionData( collectionID );
	}, [])

	const refreshRemoteData = () => {
		apiFetch( { path: `${mrRestBase}/remote_data` } )
			.then( result => {
					if ( result ) {
						setRemoteData( result );
					}
					return result;
				} );
	}

	const onSearchModalReturn = newCollectionID => {
		setAttributes( { collectionID: newCollectionID } );
		if ( ! newCollectionID ) {
			return;
		}
		refreshCollectionData( newCollectionID );
		
	}

	const onClickCallback = objectID => {
		setSelectedObjectID( objectID );
	}

	const closeObjectModal = () => {
		setSelectedObjectID( null );
	}

	const refreshCollectionData = collectionID => {
		if ( ! remoteData || isEmpty( remoteData ) ) {
			return;
		}
		
		const collectionRestURL = `${remoteData.url}${wpmRestBase}/collections/${collectionID}/?uuid=${remoteData.uuid}`;
		fetch( collectionRestURL ).then( response => {
			if ( ! response.ok ) {
				console.log( response.statusText );
				return;
			}
			response.json().then( result => {
				const newThumbnailURL = result['thumbnail'].length > 0 ? result['thumbnail'][0] : null;

				setAttributes( {
					title          : result['post_title'],
					collectionURL  : result['link'],
					thumbnailURL   : newThumbnailURL,
					excerpt        : result['excerpt'],
				} );
			} );
		} );

		const objectsRestURL = `${remoteData.url}${wpmRestBase}/collections/${collectionID}/objects/?uuid=${remoteData.uuid}`;
		fetch( objectsRestURL ).then( response => {
			if ( ! response.ok ) {
				console.log( response.statusText );
				return;
			}
			response.json().then( result => {
				if ( Array.isArray( result ) && result.length > 0 ) {
					const newCollectionObjects = result.map( result => {
						const objImgURL = result['thumbnail'].length > 0 ? result['thumbnail'][0] : null;
						return ( {
							imgURL : objImgURL,
							title  : result['post_title'],
							URL    : result['link'],
							ID     : result['ID'],
						} );
					} );
					setAttributes( {
						collectionObjects: newCollectionObjects
					} );
				}
			} );
		} );
	}

	const fetchObjectImages = objectID => {
		if ( ! remoteData || isEmpty( remoteData ) ) {
			return Promise.resolve( false );
		}
		return fetch( `${remoteData.url}${wpmRestBase}/all/${objectID}/images`)
			.then( response => {
				if ( ! response.ok ) {
					console.log( response.statusText );
					return false;
				}
				return response.json().then( result => {
					return result;
				} );
			} );
	}

	const TitleTag = titleTag;

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
                			<div>{ title }</div>
                			<div><a href = { URL } target='_blank'>View Collection</a></div>
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
		<div className = 'museum-collection-block' >
			<div className = { `collection-block-upper-content img-${imgAlignment}` } >
				{ displayThumbnail &&
					<div className = 'thumbnail-wrapper'>
							<ThumbnailImage
								thumbnailURL       = { thumbnailURL }
								imgDimensions      = { imgDimensions }
								setSearchModalOpen = { setSearchModalOpen }
							/>
					</div>
				}
				<div className = 'collection-info'>
					{ displayTitle && title &&
						<TitleTag className = 'remote-collection-title'>
							<a href = { collectionURL }> { title }</a>
						</TitleTag>
					}
					{ displayExcerpt && excerpt &&
						<div 
							className = 'collection-excerpt'
							style     = { { fontSize: fontSize + 'em' } }
						>
							{ excerpt }
							{ collectionURL && 
								<span> (<a href = { collectionURL }>Read More</a>)</span>
							}
						</div>
					}
				</div>
			</div>
			<div className = 'collection-block-lower-content'>
					{ displayObjects &&
						<>
						<ObjectImageGrid
							objects           = { collectionObjects }
							numObjects        = { collectionObjects.length }
							columns           = { columns }
							linkToObjects     = { false }
							fetchObjectImages = { fetchObjectImages }
							onClickCallback   = { onClickCallback }
						/>
						<RemoteObjectModal
							remoteData  = { remoteData }
							wpmRestBase = { wpmRestBase }
							objectID    = { selectedObjectID }
							modalOpen   = { selectedObjectID != null }
							closeModal  = { closeObjectModal }
						/>
						</>
					}
			</div>
		</div>
		</>
	)	
}

export default RemoteCollectionEdit;
