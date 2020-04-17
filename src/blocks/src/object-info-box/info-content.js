import { hexToRgb } from '../util';

import { ObjectSearchButton } from '../components/object-search-box.js';

const InfoContent = ( props ) => {
	const { 
		objectID,
		title,
		excerpt,
		thumbnailURL,
		fields,
		fieldData,
		imgDimensions,
		imgAlignment,
		fontSize,
		appearance,
		titleTag
	} = props;
	const { width, height } = imgDimensions;
	const { borderWidth, borderColor, backgroundColor, backgroundOpacity } = appearance;

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
			<>
			{ width && height &&
				<img 
					className = { 'img-info-' + imgAlignment }
					src = { thumbnailURL }
					height = { height }
					width = { width }
				/>
			}
			{ title === null || 
			<TitleTag>{ title }</TitleTag>
			}
			{ excerpt === null ||
			<p style={ { fontSize: fontSize + 'em'  } } >{ excerpt } </p>
			}
			</>
	);

	const bRGB = hexToRgb( backgroundColor.toString(16) );

	const divStyle = {
		borderWidth: borderWidth,
		borderColor: borderColor,
		backgroundColor: `rgba( ${bRGB.r}, ${bRGB.g}, ${bRGB.b}, ${backgroundOpacity} )`,
	}

	if ( objectID !== null ) {	
		return (
			<div className = 'info-outer-div' style = { divStyle }>
				{ body }
				{ field_list.length === 0 ||
					<ul>
						{ field_list }
					</ul>
				}
			</div>
		);
	} else {
		return (
			<div>

			</div>
		);
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