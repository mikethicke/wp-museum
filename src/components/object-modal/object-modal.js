import {
	useState
} from '@wordpress/element'; 

import {
	Modal,
	Button
} from '@wordpress/components';

import {
	chevronLeft,
	chevronRight
} from '../../icons';
import { getBestImage } from '../../javascript/util';



const ImageScroll = props => {
	const {
		images
	} = props;

	const imgArray = Object.values( images );
	imgArray.sort( (a, b) => a['sort_order'] - b['sort_order'] );
	
	const [ imgIndex, setImgIndex ] = useState( 0 );

	const imgDimensions = {
		height: 1024,
		width: 1024
	}

	const updateImgIndex = ( increment ) => {
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

	const bestImage = getBestImage( imgArray[ imgIndex ], imgDimensions );

	return (
		<div className = 'object-modal-image-scroll'>
			<Button
				className = 'image-scroll-button dec'
				icon      = { chevronLeft }
				onClick   = { () => updateImgIndex( -1 ) }
			/>
			<Button
				className = 'image-scroll-button inc'
				icon      = { chevronRight }
				onClick   = { () => updateImgIndex( 1 ) }
			/>
			<div className = 'img-wrapper'>
				<img
					src   = { bestImage.URL }
					title = { imgArray[imgIndex].title || '' }
					alt   = { imgArray[imgIndex].alt || '' }
				/>
			</div>
		</div>
	);
}

const ObjectModal = props => {
	const {
		title,
		content,
		url,
		linkText,
		images,
		close
	} = props;

	return (
		<Modal
			className = 'wpm-object-modal'
			title = { title }
			onRequestClose = { close }
		>
			<div className = 'object-modal-content-wrapper'>
				<div className = 'object-modal-content'>
					<div className = 'object-modal-image'>
						<ImageScroll
							images = { images }
						/>
					</div>
					<div className = 'object-modal-info'>
						<div className = 'read-more-link'>
								<a href = { url }>{ linkText }</a>
						</div>
						<div className = 'modal-content'>{ content }</div>	
					</div>
				</div>
			</div>
		</Modal>
	);
}

export default ObjectModal;