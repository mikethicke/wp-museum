/**
 * Displays a grid of objects with captions.
 */

 import {
	 useState,
 } from '@wordpress/element';

import {
	MaybeLink,
	fetchObjectImages,
	getBestImage,
	getFirstObjectImage
} from '../util';

import ObjectModal from '../components/object-modal';

const ObjectGridBox = props => {
	const {
		mObject,
		imgStyle           = '',
		displayTitle       = true,
		displayDate        = false,
		displayExcerpt     = false,
		linkToObject       = true,
		onClickCallback    = null,
		imgURL             = null,
	} = props;

	const {
		post_title : postTitle,
		post_date  : postDate,
		excerpt,
		link,
		thumbnail,
	} = mObject;

	let useImgURL;
	if ( imgURL ) {
		useImgURL = imgURL;
	} else if ( thumbnail ) {
		useImgURL = thumbnail[0];
	} else {
		useImgURL = null;
	}

	return (
		<MaybeLink
			href            = { link }
			doLink          = { linkToObject }
			onClickCallback = { onClickCallback }
		>
			<div 
				className = 'object-grid-box'
				imgStyle  = { imgStyle }
			>
				<div className = 'object-grid-thumbnail-div'>
					{ !! thumbnailURL &&
						<img src = { useImgURL } />
					}
				</div>
				<div className = 'object-grid-caption-div'>
					{ displayDate && !! postDate &&
						<div className = 'ogc-date'>{ postDate }</div>
					}
					{ displayTitle && !! postTitle &&
						<h3>{ postTitle }</h3>
					}
					{ displayExcerpt && !! excerpt &&
						<div className = 'ogc-excerpt'>{ excerpt }</div>
					}
				</div>
			</div>
		</MaybeLink>
	);
}

const ObjectGridBoxDynamicImage = props => {
	const {
		mObject,
		displayTitle,
		displayDate,
		displayExcerpt,
		linkToObject,
		imgStyle,
		targetWidthHeight = 300,
		doObjectModal     = false,
	} = props;

	const {
		post_title : postTitle,
		post_date  : postDate,
		excerpt,
		link,
		thumbnail,
	} = mObject;

	const [ imgData, setImgData ] = useState( null );
	const [ modalOpen, setModalOpen ] = useState( false );

	useEffect( () => {
		fetchObjectImages( mObject.ID ).then( result => {
			if ( result ) {
				setImgData( result );
			}
		} );
	}, [ mObject ] );

	const bestImage = getBestImage(
		getFirstObjectImage( imgData ),
		{ width: targetWidthHeight, height: targetWidthHeight }
	);

	return (
		<>
			<ObjectGridBox
				mObject         = { mObject }
				imgStyle        = { imgStyle }
				displayTitle    = { displayTitle }
				displayDate     = { displayDate }
				displayExcerpt  = { displayExcerpt }
				linkToObject    = { linkToObject }
				onClickCallback = { () => setModalOpen( true ) }
				imgURL          = { bestImage.URL }
			/>
			{ doObjectModal && modalOpen && 
				<ObjectModal
					title    = { postTitle }
					content  = { excerpt }
					url      = { link }
					linkText = 'View full entry'
					images   = { imgData }
					close    = { () => setModalOpen( false ) }
				/>
			}
		</>
	);
}

const ObjectGrid = props => {
	const {
		mObjects,
		columns        = 3,
		displayTitle   = true,
		displayDate    = false,
		displayExcerpt = false,
		linkToObjects  = false,
		doObjectModal  = true,
	} = props;

	if ( ! mObjects || mObjects.length == 0 ) {
		return null;
	}

	const percentWidth = Math.round( 1 / columns * 100 ) + '%';
	const imgStyle = {
		flexBasis: percentWidth
	}

	gridObjects = mObjects.map( mObject => (
		<ObjectGridBoxDynamicImage
			mObject        = { mObject }
			imgStyle       = { imgStyle }
			displayTitle   = { displayTitle } 
			displayDate    = { displayDate }
			displayExcerpt = { displayExcerpt }
			linkToObject   = { linkToObjects }
			doObjectModal  = { doObjectModal }
		/>
	) );

	return (
		<div className = 'wpm-object-grid'>
			{ gridObjects }
		</div>
	);
}

export default ObjectGrid;