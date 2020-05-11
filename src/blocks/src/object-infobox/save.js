/**
 * Returns static HTML for frontend display of block.
 */

/**
 * Internal dependencies
 */
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
	} = attributes;
	const { width, height } = imgDimensions;

	const TitleTag = titleTag;

	let fieldList = [];
	if ( Object.keys(fieldData).length === Object.keys(fields).length ) {
		fieldList = Object.keys(fields).filter( key => fields[key] ).map( key => 
			<li key={ key } style={ { fontSize: fontSize + 'em'  } } >
				<span className = 'field-name'>{ fieldData[key]['name'] }: </span>
				<span className = 'field-data'>{ fieldData[key]['content'] }</span>
			</li>
		);
	}

	const body = (
		<div className = { `infobox-body-wrapper img-${imgAlignment}` }>
			{ linkToObject &&
				<a className = 'object-link' href = { objectURL }>Hidden Link Text</a>
			}
			{ imgURL != null && displayImage &&
				<div
					className = {'infobox-img-wrapper'}
				>
					<img
						src    = { imgURL }
						height = { height }
						width  = { width }
					/>
				</div>
			}
			<div className = 'infobox-content-wrapper'>
				{ title != null && displayTitle &&
					<TitleTag>{ title }</TitleTag>
				}
				{ excerpt != null && displayExcerpt && 
					<p style = { { fontSize: fontSize + 'em'  } } >{ excerpt } </p>
				}
				{ fieldList.length > 0 &&
					<ul>
						{ fieldList }
					</ul>
				}
			</div>
		</div>
	);
	
	return (
		<div
			className = 'info-outer-div'
		>
			{ body }	
		</div>	
	);
	

}