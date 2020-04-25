import ImageSelector from './image-selector';

import {
	useState
} from '@wordpress/element';

import {
	ObjectSearchButton, ObjectSearchBox,
} from './object-search-box';

import {
	Dashicon
} from '@wordpress/components';

const GridImage = ( props ) => {
	const {
		objectID,
		imgDimensions,
		imgURL,
		imgIndex,
		updateImgCallback,
		updateObjectIDCallback,
	} = props;

	const [ imgData, updateImgData ] = useState( {
		imgHeight   : null,
		imgWidth    : null,
		totalImages : 0
	} );

	const [ modalOpen, setModalOpen ] = useState( false );

	const setImgData = ( attrs ) => {
		const {
			imgURL,
			imgHeight,
			imgWidth,
			imgIndex,
			totalImages
		} = attrs;

		if ( imgData.imgHeight != imgHeight || imgData.imgWidth != imgWidth || imgData.totalImages != totalImages ) {
			updateImgData( {
				imgHeight: imgHeight,
				imgWidth: imgWidth,
				totalImages: totalImages
			} );
		}

		if ( ( imgURL && imgURL != props.imgURL ) || ( imgIndex && imgIndex != props.imgIndex ) ) {
			updateImgCallback( {
				imgURL   : imgURL,
				imgIndex : imgIndex
			} );
		}
	}

	return (
		<div
			className = 'grid-image-container'
		>
			{ objectID ? 
				<div
					className = 'grid-image-image'
				>
					<ImageSelector
						objectID      = { objectID }
						imgIndex      = { imgIndex }
						totalImages   = { imgData.totalImages }
						imgDimensions = { imgDimensions }
						setImageSize  = { false }
						imgURL        = { imgURL }
						setImgData    = { setImgData }
					/>
					<a
						className = 'removeImageLink'
						onClick = { () => updateObjectIDCallback( null ) }
					>
						[X]
					</a>
				</div>
				:
				<>
				<div
					className = 'grid-image-placeholder'
					onClick   = { () => setModalOpen( true ) } 
				>
					<div
						className = 'grid-image-placeholder-plus'
					>
						+
					</div>
				</div>
				{ modalOpen &&
					<ObjectSearchBox
						close = { () => setModalOpen( false ) }
						returnCallback = { newObjectID => updateObjectIDCallback( newObjectID ) }
					/>
				}
				</>
			}
		</div>
	);
}

export default GridImage;