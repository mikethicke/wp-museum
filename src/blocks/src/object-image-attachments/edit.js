import { 
	useState,
	useEffect,
} from '@wordpress/element';
import { 
	useSelect,
} from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

import ImgItem from './img-item';

const ObjectImageAttachmentEdit = ( props ) => {
	const { attributes, setAttributes, clientId } = props;
	const { imgAttach } = attributes;

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
			apiFetch( { path: `${baseRestPath}/all/${postId}/images` } )
				.then( result => setImgData( result ) );
		}
	} );

	useEffect( () => {
		if ( isSavingPost ) {
			Object.entries( imgData ).map( ( [ itemId, itemData ] ) => {
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
		imgData[imgId]['title']       = title;
		imgData[imgId]['caption']     = caption;
		imgData[imgId]['description'] = description;
		imgData[imgId]['alt']         = alt;
	}

	const moveItem = ( imgId, move ) => {
		const imgIndex = imgAttach.findIndex( id => id === imgId );
		const newIndex = imgIndex + move;
		if ( newIndex < 0 || newIndex >= imgAttach.length ) {
			return;
		}
		imgAttach[ imgIndex ] = imgAttach[ newIndex ];
		imgAttach[ newIndex ] = imgId;
		
		const updatedImgAttach = [ ...imgAttach ];
		setAttributes( { 
			imgAttach: updatedImgAttach,
		} );
	}

	let imgItems = null;
	if ( imgData ) {
		imgItems = Object.entries( imgAttach ).map( ( [index, imgId ] ) =>
			<ImgItem
				key      = { index }
				itemData = { imgData[ imgId ] }
				imgId    = { imgId }
				onUpdate = { updateImgData }
				clientId = { clientId }
				moveItem = { moveItem }
			/>
		);
	}

	return (
		<>
		<h3>Images</h3>
		<div className = 'img-attach-img-wrapper'>
			{ imgItems }
		</div>
		</>
	);
}

export default ObjectImageAttachmentEdit;