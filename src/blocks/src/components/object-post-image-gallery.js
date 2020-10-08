import {
	useState,
	useEffect
} from '@wordpress/element';

import {
	Button,
	Modal
} from '@wordpress/components';

import {
	fetchObjectImages,
	getBestImage
} from '../util';

import { 
	chevronLeft,
	chevronRight
} from '../icons';

const ObjectPostImageSideBox = props => {
	const {
		imgData       = null,
		imgIndex      = 0,
		openModal,
		imgDimensions = { width: 200, height: 200 }
	} = props

	const bestImage = !! imgData ? getBestImage( imgData, imgDimensions ) : null;
	const altText = !! imgData ? imgData.alt : '';
	const titleText = !! imgData ? imgData.title : '';

	return (
		<div className = 'wpm_obj-image'>
			{ !! imgData &&
				<img
					src     = { bestImage.URL }
					alt     = { altText }
					title   = { titleText }
					onClick = { () => openModal( imgIndex ) }
				/>
			}
		</div>
	);
}

const ObjectPostImageModal = props => {
	const {
		imgData            = {},
		imgIndex           = 0,
		displayCaption     = true,
		displayDescription = false,
		updateImgIndex,
		close
	} = props;

	const imgArray = Object.values( imgData )
		.sort( (a, b ) => a['sort_order'] - b['sort_order'] );

	const {
		title       = null,
		caption     = null,
		description = null,
		alt         = null,
	} = imgArray[ imgIndex ];

	const imgDimensions = {
		height: 1024,
		width: 1024
	}

	const bestImage = !! imgArray.length > 0 ? 
		getBestImage( imgArray[ imgIndex ], imgDimensions ) : 
		null;

	return (
		<Modal
			className      = 'wpm-object-post-image-modal'
			title          = { title || '' }
			onRequestClose = { close }
		>
			<div className = 'image-modal-content-wrapper'>
				<div className = 'image-modal-content'>
					<Button
						className = 'image-modal-button dec'
						icon      = { chevronLeft }
						onClick   = { () => updateImgIndex( -1 ) }
					/>
					<Button
						className = 'image-modal-button inc'
						icon      = { chevronRight }
						onClick   = { () => updateImgIndex( 1 ) }
					/>
					<div className = 'image-modal-image'>
						{ !! bestImage &&
							<img
								src   = { bestImage.URL }
								title = { title || '' }
								alt   = { alt || '' }
							/>
						}
					</div>
					<div className = 'image-modal-image-link'>
						<a 
							href = { imgArray[ imgIndex ]['full'][0] }
							target = '_blank'
						>
							View Full Image
						</a>
					</div>
					{ displayCaption &&
						<div className = 'image-modal-caption'>
							{ caption }
						</div>
					}
					{ displayDescription &&
						<div className = 'image-modal-description'>
							{ description }
						</div>
					}
				</div>
			</div>
		</Modal>
	);
}

const ObjectPostImageGallery = props => {
	const {
		postId
	} = props;

	const [ imgData, setImgData ] = useState( {} );
	const [ modalOpen, setModalOpen ] = useState( false );
	const [ imgIndex, setImgIndex ] = useState( 0 );

	const updateImgData = () => {
		fetchObjectImages( postId ).then( images => setImgData( images ) );
	}

	const openModal = newImgIndex => {
		setImgIndex( newImgIndex );
		setModalOpen( true );
	}

	const updateImgIndex = increment => {
		const imgArray = Object.values( imgData );
		let targetIndex = imgIndex + increment;
		if ( imgArray.length === 0 ) {
			return;
		}
		if ( targetIndex < 0 ) {
			targetIndex = imgArray.length - 1;
		} else if ( targetIndex >= imgArray.length ) {
			targetIndex = 0;
		}
		setImgIndex( targetIndex );
	}

	useEffect( () => {
		if ( !! postId ) {
			updateImgData( postId );
		}
	}, [ postId ] );

	const imageSideboxes = Object.values( imgData )
		.sort( (a, b ) => a['sort_order'] - b['sort_order'] )
		.map( ( singleImgData, index ) => (
			<ObjectPostImageSideBox
				imgData   = { singleImgData }
				imgIndex  = { index }
				openModal = { openModal }
			/>
	) );

	return (
		<>
		{ modalOpen &&
			<ObjectPostImageModal
				imgData        = { imgData }
				imgIndex       = { imgIndex }
				updateImgIndex = { updateImgIndex }
				close          = { () => setModalOpen( false ) }
			/>
		}
		<div id = 'wpm_obj-gallery'>
			{ imageSideboxes }
		</div>
		</>
	)


}

export default ObjectPostImageGallery;