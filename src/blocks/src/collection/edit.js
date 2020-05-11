/**
 * Gutenberg editor view for Collection block. Creates <Collection> component.
 */

/**
 * WordPress dependencies.
 */
import {
    useState,
} from '@wordpress/element';

import {
	InspectorControls
} from '@wordpress/blockEditor'

import { 
	PanelBody,
	CheckboxControl,
	RangeControl
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies.
 */

import { CollectionEmbedPanel, SearchBox } from "../components/search-box";
import FontSizePanel from "../components/font-size-panel";
import ImageSizePanel from '../components/image-size-panel';


const Collection = ( props ) => {
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

	const TitleTag = titleTag;

	const maxDisplayObjects = Math.max( collectionObjects.length, 4 );

	const [ modalOpen, setModalOpen ] = useState( false );

	const percentWidth = Math.round( 1 / columns * 100 ) + '%';
	const imgStyle = {
		flexBasis: percentWidth
	}

	const collectionImages = collectionObjects
		.filter( object => object.imgURL )
		.map( ( object, index ) => {
			return (
				<div 
					className = 'collection-object-image-wrapper'
					style     = { imgStyle }
					key       = { 'collection-object-image-' + index }
				>
					{ linkToObjects ?
						<a href= { object.URL }>
							<img
								src    = { object.imgURL }
								title = { object.title }
							/>
						</a>
						:
						<img
							src   = { object.imgURL }
							title = { object.title }
						/>
					}			
				</div>
			);
		} )
		.slice( 0, numObjects );

	const onSearchModalReturn = ( newCollectionID ) => {
		setAttributes( { collectionID: newCollectionID } );

		const baseRestPath = '/wp-museum/v1/';

		if ( newCollectionID ) {
			const collectionRestPath = `${baseRestPath}collections/${newCollectionID}`;
			apiFetch( { path: collectionRestPath } ).then( result => {
				const newThumbnailURL = result['thumbnail'].length > 0 ? result['thumbnail'][0] : null;

				setAttributes( {
					title          : result['post_title'],
					collectuionURL : result['link'],
					thumbnailURL   : newThumbnailURL,
					excerpt        : result['excerpt'],
				} );

				const objectsRestPath = `${baseRestPath}collections/${newCollectionID}/objects`;
				apiFetch( { path: objectsRestPath } ).then( result => {
					if ( Array.isArray( result ) && result.length > 0 ) {
						const newCollectionObjects = result.map( result => {
							const objImgURL = result['thumbnail'].length > 0 ? result['thumbnail'][0] : null;
							return ( {
								imgURL : objImgURL,
								title  : result['post_title'],
								URL    : result['link']
							} );
						} );
						setAttributes( {
							collectionObjects: newCollectionObjects
						} );
					}
				} );
			} ); 
		}
	}

	return (
		<>
		<InspectorControls>
			<CollectionEmbedPanel
				collectionID        = { collectionID }
				title               = { title }
				URL                 = { collectionURL }
				onSearchModalReturn = { onSearchModalReturn }
				initialOpen         = { true }
			/>
			<PanelBody
				initialOpen = { true }
			>
				<RangeControl
					label    = 'Collection Items to Display'
					value    = { numObjects }
					onChange = { newNum => setAttributes( { numObjects: newNum } ) }
					min      = { 1 }
					max      = { maxDisplayObjects }
					disabled = { ! displayObjects }
				/>
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
			<ImageSizePanel
				setAttributes = { setAttributes }
				imgDimensions = { imgDimensions }
				imgAlignment  = { imgAlignment }
				initialOpen   = { true }
			/>
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
						{ thumbnailURL ?
							<img src = { thumbnailURL } />
							:
							<div
								className = 'thumbnail-placeholder'
								style     = { { height: imgDimensions.height, width: imgDimensions.width } }
								onClick   = { event => {
									event.stopPropagation();
									setModalOpen( true )
								} }
							>
								<div className = 'thumbnail-placeholder-plus'>+</div>
								{ modalOpen && 
									<SearchBox
										close = { () => setModalOpen( false ) }
										returnCallback = { onSearchModalReturn }
									/>
								}
							</div>
						}
					</div>
				}
				<div className = 'collection-info'>
					{ displayTitle && title &&
						<TitleTag>
							{ title }
						</TitleTag>
					}
					{ displayExcerpt && excerpt &&
						<div 
							className = 'collection-excerpt'
							style     = { { fontSize: fontSize + 'em' } }
						>
							{ excerpt }
						</div>
					}
				</div>
			</div>
			<div className = 'collection-block-lower-content'>
					{ displayObjects && collectionObjects.length > 0 &&
						collectionImages
					}
			</div>
		</div>
		</>
	)
}

export default Collection;