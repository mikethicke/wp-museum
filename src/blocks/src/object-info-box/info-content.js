import { hexToRgb } from './util';

import { ObjectSearchButton } from '../components/object-search-box.js';

const InfoContent = ( props ) => {
	const { 
		objectID,
		title,
		excerpt,
		thumbnailURL,
		fields,
		fieldData,
		imageDimensions,
		imageSizes,
		state,
		imageAlignment,
		fontSize,
		appearance,
		titleTag
	} = props;
	const { width, height, size } = imageDimensions;
	const { imgHeight, imgWidth, imgReady } = state;
	const { borderWidth, borderColor, backgroundColor, backgroundOpacity } = appearance;

	let imgRenderHeight, imgRenderWidth;
	if ( imgReady ) {
		if ( width != null && height != null ) {
			imgRenderWidth = width;
			imgRenderHeight = height;
		} else {
			const targetSize = imageSizes[ size ].width; //width == height
			const scaleFactor = targetSize / Math.max( imgHeight, imgWidth );
			imgRenderWidth = Math.round( imgWidth * scaleFactor );
			imgRenderHeight = Math.round( imgWidth * scaleFactor );
		}
	}

	let field_list = [];
	if ( Object.keys(fieldData).length === Object.keys(fields).length ) {
		for ( let key in fields ) {
			if ( fields[key] ) {
				field_list.push(
					<li key={ key } style={ { fontSize: fontSize + 'em'  } } >
						<span className = 'field-name'>{ fieldData[key]['name']}: </span>
						<span className = 'field-data'>{ fieldData[key]['content'] }</span>
					</li>
				)
			}
		}
	}

	const TitleTag = titleTag;

	const body = [
			<>
			{ imgReady &&
				<img 
					className = { 'img-info-' + imageAlignment }
					src = { thumbnailURL }
					height = { imgRenderHeight }
					width = { imgRenderWidth }
				/>
			}
			{ title === null || 
			<TitleTag>{ title }</TitleTag>
			}
			{ excerpt === null ||
			<p style={ { fontSize: fontSize + 'em'  } } >{ excerpt } </p>
			}
			</>
	];

	const bRGB = hexToRgb( backgroundColor.toString(16) );

	const divStyle = {
		borderWidth: borderWidth,
		borderColor: borderColor,
		backgroundColor: `rgba( ${bRGB.r}, ${bRGB.g}, ${bRGB.b}, ${backgroundOpacity} )`,
	}

	if ( objectID !== null ) {	
		return [
			<div className = 'info-outer-div' style = { divStyle }>
				{ body }
				{ field_list.length === 0 ||
					<ul>
						{ field_list }
					</ul>
				}
			</div>
		]
	} else {
		return [
			<div>

			</div>
		];
	}
}

const InfoPlaceholder = ( props ) => {
	const { onSearchModalReturn } = props;
	return (
		<div>
			<div>Click 'Search' to embed object.</div>
			<ObjectSearchButton
				returnCallback = { onSearchModalReturn }
			>
				Search
			</ObjectSearchButton>
		</div>
	);
}

export { InfoContent, InfoPlaceholder };