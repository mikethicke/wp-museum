import {
	useState, useEffect
} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

import ObjectImageGrid from '../components/object-image-grid';
import ThumbnailImage from '../components/thumbnail-image';
import RemoteObjectModal from './remote-object-modal';

import {
	isEmpty
} from '../util';

const RemoteCollectionGrid = props => {

	const {
		attributes,
		setSearchModalOpen,
		remoteData,
		setRemoteData,
		wpmRestBase,
	} = props;

	const {
		displayThumbnail,
		imgDimensions,
		displayTitle,
		titleTag,
		displayExcerpt,
		fontSize,
		columns,
		collectionID,
		imgAlignment,
		displayObjects
	} = attributes;

	const [ selectedObjectID, setSelectedObjectID ] = useState( null );
	const [ collectionObjects, setCollectionObjects ] = useState( [] );
	const [ title, setTitle ] = useState( '' );
	const [ excerpt, setExcerpt ] = useState( '' );
	const [ collectionURL, setCollectionURL ] = useState( '' );
	const [ thumbnailURL, setThumbnailURL ] = useState( '' );

	const mrRestBase = '/museum-remote/v1';

	useEffect( () => {
		refreshCollectionData();
	}, [ attributes, remoteData ] );

	useEffect( () => refreshRemoteData(), [] );

	const refreshRemoteData = () => {
		apiFetch( { path: `${mrRestBase}/remote_data` } )
			.then( result => {
					if ( result ) {
						setRemoteData( result );
					}
					return result;
				} );
	}

	const refreshCollectionData = () => {
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
				setTitle( result['post_title'] );
				setExcerpt( result['excerpt'] );
				setCollectionURL( result['link'] );
				setThumbnailURL( newThumbnailURL );
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
					setCollectionObjects( newCollectionObjects );
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

	const onClickCallback = objectID => {
		setSelectedObjectID( objectID );
	}

	const closeObjectModal = () => {
		setSelectedObjectID( null );
	}

	const TitleTag = titleTag;

	return (
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
	);
}

export default RemoteCollectionGrid;