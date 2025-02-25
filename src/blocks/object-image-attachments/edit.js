import { 
	useState,
	useEffect,
} from '@wordpress/element';
import { 
	useSelect,
} from '@wordpress/data';
import {
	Button,
	PanelBody
} from '@wordpress/components'
import apiFetch from '@wordpress/api-fetch';
import {
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

import ImgItem from './img-item';

const MediaUploadLauncher = ( props ) => {
	const {
		renderCallback,
		addNewMedia
	} = props;

	return (
		<MediaUploadCheck>
			<MediaUpload
				onSelect     = { addNewMedia }
				multiple     = { true }
				allowedTypes = { [ 'image' ] }
				render       = { renderCallback }
				gallery      = { true }
			/>
		</MediaUploadCheck>
	)
}

const ObjectImageAttachmentEdit = ( props ) => {
	const { attributes, setAttributes, clientId } = props;
	const { imgAttach, imgAttachStr } = attributes;
	const blockProps = useBlockProps();

	const [ imgData, setImgData ] = useState( null );

	const { postType, postId, isSavingPost } = useSelect( 
		( select ) => {
			const {
				getCurrentPostType,
				getCurrentPostId,
				isSavingPost,
			} = select( 'core/editor' );
			return {
				postType          : getCurrentPostType(),
				postId            : getCurrentPostId(),
				isSavingPost      : isSavingPost(),
			}
		},
		[]
	);

	const baseRestPath = '/wp-museum/v1';

	useEffect( () => {
		if ( ! imgData ) {
			fetchImageData();
		}
	} );

	useEffect( () => {
		if ( isSavingPost && imgData ) {
			Object.entries( imgData ).forEach( ( [ itemId, itemData ] ) => {
				apiFetch( {
					path: `/wp/v2/media/${itemId}`,
					method: 'POST',
					data: {
						'title'       : itemData.title,
						'caption'     : itemData.caption,
						'alt_text'    : itemData.alt,
						'description' : itemData.description,
					}
				} );
			} );
		}	
	} );
	
	const updateImgData = ( imgId, title, caption, description, alt ) => {
		imgData[imgId].title       = title;
		imgData[imgId].caption     = caption;
		imgData[imgId].description = description;
		imgData[imgId].alt         = alt;
	}

	const updateImgAttach = ( updatedImgAttach ) => {
		setAttributes( { 
			imgAttach: updatedImgAttach,
			imgAttachStr: JSON.stringify( updatedImgAttach ),
		} );
		apiFetch( {
			path   : `${baseRestPath}/all/${postId}/images/`,
			method : 'POST',
			data   : {
				'images' : updatedImgAttach,
			}
		} ).then( fetchImageData );
	}

	const fetchImageData = () => {
		apiFetch( { path: `${baseRestPath}/all/${postId}/images` } )
				.then( result => setImgData( result ) );
	}

	const addNewImages = ( media ) => {
		if ( Array.isArray( media ) && media.length > 0 ) {
			const updatedImgAttach = ( Array.isArray( imgAttach ) ? [ ...imgAttach ] : [] );
			let changes = false;
			media.forEach( mediaItem => {
				if ( updatedImgAttach.findIndex( item => item === mediaItem.id ) === -1 ) {
					updatedImgAttach.push( mediaItem.id );
					changes = true;
				}
			} );
			if ( changes ) {
				updateImgAttach( updatedImgAttach );
			}
		}
	}

	const removeItem = ( imgId ) => {
		const updatedImgAttach = ( Array.isArray( imgAttach ) ? [ ...imgAttach ] : [] );
		const removeIndex = updatedImgAttach.findIndex( item => item === imgId );
		if ( removeIndex > -1 ) {
			updatedImgAttach.splice( removeIndex, 1 );
			updateImgAttach( updatedImgAttach );
		}
	}

	const moveItem = ( imgId, move ) => {
		const imgIndex = imgAttach.findIndex( id => id === imgId );
		const newIndex = imgIndex + move;
		if ( newIndex < 0 || newIndex >= imgAttach.length ) {
			return;
		}
		imgAttach[ imgIndex ] = imgAttach[ newIndex ];
		imgAttach[ newIndex ] = imgId;
		
		const updatedImgAttach = ( Array.isArray(imgAttach) ? [ ...imgAttach ] : [] );
		setAttributes( { 
			imgAttach: updatedImgAttach,
			imgAttachStr: JSON.stringify( updatedImgAttach )
		} );

	}

	let imgItems = null;
	if ( imgData && imgAttach) {
		imgItems = Object.entries( imgAttach ).map( ( [index, imgId ] ) => {
			if ( ! ( ( typeof imgData[ imgId ] ) === 'undefined' ) ) {
				return (
					<ImgItem
						key      = { index }
						itemData = { imgData[ imgId ] }
						imgId    = { imgId }
						onUpdate = { updateImgData }
						clientId = { clientId }
						moveItem = { moveItem }
						removeItem = { removeItem }
						imgIndex = { index }
					/>
				);
			} else {
				return null;
			} 
		} );
	}

	const mediaOpenButton = ( { open } ) => (
			<Button
				onClick = { open }
				isPrimary
			>
				Add Image(s)
			</Button>
	);

	const addImageDiv = ( { open } ) => (
		<div 
			className = 'add-image-div'
			onClick   = { open }
		>
			<div className = 'image-div-message'>Add Image(s)</div>
		</div>
	);

	return (
		<>
		<InspectorControls>
			<PanelBody>
				<MediaUploadLauncher
					renderCallback = { mediaOpenButton }
					addNewMedia = { addNewImages }
				/>
			</PanelBody>
		</InspectorControls>
		<div {...blockProps}>
			<h3>Images</h3>
			<div className = 'img-attach-img-wrapper'>
				{ imgItems }
				<MediaUploadLauncher
					renderCallback = { addImageDiv }
					addNewMedia = { addNewImages }
				/>
			</div>
		</div>
		</>
	);
}

export default ObjectImageAttachmentEdit;