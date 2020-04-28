/**
 * Component for rendering infobox in Gutenberg editor.
 */

/**
 * WordPress dependencies.
 */
import {
    useState,
} from '@wordpress/element';

/**
 * Internal dependencies.
 */
import { hexToRgb } from '../util';
import { ObjectSearchBox } from '../components/object-search-box';
import ImageSelector from '../components/image-selector';

/**
 * Renders the editor content for the infobox.
 * 
 * @param {object} props The component properties.
 * @see ./index.js for attribute descriptions.
 */
const InfoContent = ( props ) => {
	const { 
		title,
		excerpt,
		imgURL,
		imgIndex,
		imgHeight,
		imgWidth,
		displayImage,
		fields,
		fieldData,
		imgDimensions,
		imgAlignment,
		fontSize,
		appearance,
		titleTag,
		onSearchModalReturn,
		objectID,
		setAttributes,
		totalImages
	} = props;
	const { borderWidth, borderColor, backgroundColor, backgroundOpacity } = appearance;
	const [ modalOpen, setModalOpen ] = useState( false );

	let field_list = [];
	if ( Object.keys(fieldData).length === Object.keys(fields).length ) {
		field_list = Object.keys(fields).filter( key => fields[key] ).map( key => 
				<li key={ 'field_list_' + key } style ={ { fontSize: fontSize + 'em'  } } >
					<span className = 'field-name'>{ fieldData[key]['name']}: </span>
					<span className = 'field-data'>{ fieldData[key]['content'] }</span>
				</li>
		);
	}

	const TitleTag = titleTag;

	/**
	 * Update image data attributes from ImageSelector.
	 * 
	 * @param {object} newImageData Image data returned from ImageSelector component
	 */
	const setImgData = ( newImageData ) => {
		setAttributes( newImageData );

		if ( ! newImageData.imgURL && newImageData.imgIndex != imgIndex ) {
			setAttributes( { imgURL: null } );
		}
	}

	const body = (
			<div className = { `infobox-body-wrapper img-${imgAlignment}` }>
				{ displayImage &&
					<div
						className = { `infobox-img-wrapper` }
					>
						{ objectID ? 
							<ImageSelector 
								imgHeight     = { imgHeight }
								imgWidth      = { imgWidth }
								objectID      = { objectID }
								imgIndex      = { imgIndex }
								imgURL        = { imgURL }
								imgDimensions = { imgDimensions }
								setImgData    = { setImgData }
								totalImages   = { totalImages }
							/>
							:
							<>
								<div
									className = 'image-selector-placeholder'
									style     = { { height: imgDimensions.height, width: imgDimensions.width } }
									onClick   = { ( event ) => {
										event.stopPropagation();
										setModalOpen( true ) 
									} } 
								>
									<div
										className = 'image-selector-placeholder-plus'
									>
										+
									</div>
								</div>
								{ modalOpen &&
									<ObjectSearchBox
										close = { () => setModalOpen( false ) }
										returnCallback = { onSearchModalReturn }
									/>
								}
							</>
						}
					</div>
				}
				<div className = 'infobox-content-wrapper'>
					{ title === null || 
					<TitleTag>{ title }</TitleTag>
					}
					{ excerpt === null ||
					<p style={ { fontSize: fontSize + 'em'  } } >{ excerpt } </p>
					}
					{ field_list.length === 0 ||
						<ul>
							{ field_list }
						</ul>
					}
				</div>
			</div>
	);

	const bRGB = hexToRgb( backgroundColor.toString(16) );

	const divStyle = {
		borderWidth     : borderWidth,
		borderColor     : borderColor,
		backgroundColor : `rgba( ${bRGB.r}, ${bRGB.g}, ${bRGB.b}, ${backgroundOpacity} )`,
	}
	
	return (
		<div className = 'info-outer-div' style = { divStyle }>
			{ body }
		</div>
	);
}

export default InfoContent;