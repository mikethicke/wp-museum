import { hexToRgb } from '../util';


import {
    useState,
} from '@wordpress/element';

import { ObjectSearchBox } from '../components/object-search-box';
import ImageSelector from '../components/image-selector';

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
				<li key={ 'field_list_' + key } style={ { fontSize: fontSize + 'em'  } } >
					<span className = 'field-name'>{ fieldData[key]['name']}: </span>
					<span className = 'field-data'>{ fieldData[key]['content'] }</span>
				</li>
		);
	}

	const TitleTag = titleTag;

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
								setImgData    = { setAttributes }
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
		borderWidth: borderWidth,
		borderColor: borderColor,
		backgroundColor: `rgba( ${bRGB.r}, ${bRGB.g}, ${bRGB.b}, ${backgroundOpacity} )`,
	}
	
	return (
		<div className = 'info-outer-div' style = { divStyle }>
			{ body }
		</div>
	);
}

export default InfoContent;