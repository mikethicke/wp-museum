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
} from '../icons';
import { getBestImage } from '../util';

const ImageScroll = props => {
	const {
		images
	} = props;

	const [ imgIndex, setImgIndex ] = useState( 0 );

	const imgDimensions = {
		height: 1024,
		width: 1024
	}

	const updateImgIndex = ( increment ) => {
		let targetIndex = imgIndex + increment;
		if ( images.length === 0 ) {
			return;
		}
		if ( targetIndex < 0 ) {
			targetIndex = images.length - 1;
		} else if ( targetIndex >= images.length ) {
			targetIndex = 0;
		}
		setImgIndex( targetIndex );
	}

	const bestImage = getBestImage( images[ imgIndex ], imgDimensions );

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
					title = { images[imgIndex].title || '' }
					alt   = { images[imgIndex].alt || '' }
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
			<ImageScroll
				images = { images }
			/>
			<div className = 'object-modal-info'>
				<div className = 'modal-content'>{ content }</div>
				<div className = 'read-more-link'>
					<a href = { url }>{ linkText }</a>
				</div>
			</div>
		</Modal>
	)
}

export default ObjectModal;