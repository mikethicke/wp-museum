import { hexToRgb } from '../util';

export default function save ( props ) {
	const { attributes } = props;
	const {
		title,
		excerpt,
		objectURL,
		displayTitle,
		displayExcerpt,
		imgURL,
		displayImage,
		linkToObject,
		fields,
		fieldData,
		imgDimensions,
		imgAlignment,
		fontSize,
		titleTag,
		appearance
	} = attributes;
	const { width, height } = imgDimensions;
	const { borderWidth, borderColor, backgroundColor, backgroundOpacity } = appearance;

	const TitleTag = titleTag;

	const bRGB = hexToRgb( backgroundColor.toString(16) );

	const outerDivStyle = {
		borderWidth: borderWidth,
		borderColor: borderColor,
		backgroundColor: `rgba( ${bRGB.r}, ${bRGB.g}, ${bRGB.b}, ${backgroundOpacity} )`,
	}

	let field_list = [];
	if ( Object.keys(fieldData).length === Object.keys(fields).length ) {
		field_list = Object.keys(fields).filter( key => fields[key] ).map( key => 
			<li key={ key } style={ { fontSize: fontSize + 'em'  } } >
				<span className = 'field-name'>{ fieldData[key]['name'] }: </span>
				<span className = 'field-data'>{ fieldData[key]['content'] }</span>
			</li>
		);
	}

	const body = (
		<div className = { `infobox-body-wrapper img-${imgAlignment}` }>
			{ linkToObject &&
				<a className='object-link' href={ objectURL }>Hidden Link Text</a>
			}
			{ imgURL != null && displayImage &&
				<div
					className = {'infobox-img-wrapper'}
				>
					<img
						src = { imgURL }
						height = { height }
						width = { width }
					/>
				</div>
			}
			<div className = 'infobox-content-wrapper'>
				{ title != null && displayTitle &&
					<TitleTag>{ title }</TitleTag>
				}
				{ excerpt != null && displayExcerpt && 
					<p style={ { fontSize: fontSize + 'em'  } } >{ excerpt } </p>
				}
				{ field_list.length > 0 &&
					<ul>
						{ field_list }
					</ul>
				}
			</div>
		</div>
	);
	
	return (
		<div
			className = 'info-outer-div'
			style = { outerDivStyle }
		>
			{ body }	
		</div>	
	);
	

}