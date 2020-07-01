import {
	useEffect,
	useState
} from '@wordpress/element';

import {
	getBestImage,
	getFirstObjectImage,
	isEmpty
} from '../util';

const PlaceholderGrid = props => {
	const {
		columns,
		numImages
	} = props;

	const percentWidth = Math.round( 1 / columns * 100 ) + '%';
	const boxStyle = {
		flexBasis: percentWidth,
	}

	const grid = [];
	for ( let index = 0; index < numImages; index++ ) {
		grid[ index ] = (
			<div
				key       = { index }
				className = 'grid-image-wrapper'
				style     = { boxStyle }
			>
				<div
					className = 'placeholder-box'
				>

				</div>
			</div>
		);
	}

	return (
		<>
			{ grid }
		</>
	);
}

const ObjectImageGrid = props => {
	const {
		objects,
		numObjects,
		columns,
		linkToObjects,
		fetchObjectImages,
		onClickCallback
	} = props;

	const imgDimensions = {
		height: 300,
		width : 300
	}

	const [ imgData, setImgData ] = useState( {} );
	const [ bufferedImageGrid, setBufferedImageGrid ] = useState( null );

	useEffect( () => {
		if ( ! objects || objects.length === 0 || ! fetchObjectImages ) {
			return;
		}
		let updateArray = [];
		objects.forEach( item => {
			updateArray.push(
				fetchObjectImages( item.ID ).then( result => {
					if ( ! result ) {
						return;
					}
					imgData[ item.ID ] = result;
				} )
			);
		} );
		Promise.all( updateArray ).then( () => {
			const newImgData = Object.assign( {}, imgData );
			setImgData( newImgData );
			setBufferedImageGrid( null );
		} );
	}, [ objects ] );

	const percentWidth = Math.round( 1 / columns * 100 ) + '%';
	const imgStyle = {
		flexBasis: percentWidth
	}

	if ( isEmpty( imgData ) ) {
		return (
			<div className = 'museum-blocks-image-grid'>
				<PlaceholderGrid
					columns = { columns }
					numImages = { numObjects }
				/>
			</div>
		)
	}

	const MaybeLink = props => {
		const {
			href,
			children
		} = props
		
		if ( linkToObjects ) {
			return (
				<a href = { href }>{ children }</a>
			)
		}
		return ( <>{ children }</> );
	}

	let imageGrid = bufferedImageGrid;

	if ( ! imageGrid ) { 
		imageGrid = objects
			.filter( object => object.imgURL )
			.map( ( object, index ) => {
				let imgAttrs;
				if ( ! isEmpty( imgData ) && typeof imgData[ object.ID ] !== 'undefined' ) {
					const bestImage = getBestImage(
						getFirstObjectImage( imgData[ object.ID ] ),
						imgDimensions
					);
					imgAttrs = {
						src   : bestImage.URL,
						title : imgData[ object.ID ].title || '',
						alt   : imgData[ object.ID ].alt || ''
					}
				} else {
					imgAttrs = {
						src   : object.imgURL,
						title : object.title,
						alt   : object.title
					}
				}
				return (
					<div 
						className = 'grid-image-wrapper'
						style     = { imgStyle }
						key       = { 'grid-image-' + index }
					>
						<MaybeLink href = { object.URL }>
							<img { ...imgAttrs }
								onClick = { () => onClickCallback( object.ID ) || null }
							/>
						</MaybeLink>
					</div>
				);
			} )
			.slice( 0, numObjects );
	}
	
	if ( imageGrid != bufferedImageGrid ) {
		setBufferedImageGrid( imageGrid );
	}

	return (
		<div className = 'museum-blocks-image-grid'>
			{ imageGrid }
		</div>
	)
}

export default ObjectImageGrid;