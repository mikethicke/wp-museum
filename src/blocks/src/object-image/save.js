
import { hexToRgb } from '../util';
import { RichText } from '@wordpress/blockEditor';

export default function save ( { attributes } ) {
	const {
		title,
		catID,
		captionText,
		imgURL,
		objectURL,
		displayTitle,
		displayCatID,
		displayCaption,
		linkToObject,
		imgDimensions,
		imgAlignment,
		fontSize,
		titleTag,
		appearance
	} = attributes;
	const { borderWidth, borderColor, backgroundColor, backgroundOpacity } = appearance;

	const TitleTag = titleTag;

	const bRGB = hexToRgb( backgroundColor.toString(16) );

	const outerDivStyle = {
		borderWidth: borderWidth,
		borderColor: borderColor,
		backgroundColor: `rgba( ${bRGB.r}, ${bRGB.g}, ${bRGB.b}, ${backgroundOpacity} )`,
	}

	const body = (
		<>
			{ imgURL && imgDimensions.height && imgDimensions.width &&
				<img
					className = { 'image-selector-image-' + imgAlignment }
					src       = { imgURL }
					height    = { imgDimensions.height }
					width     = { imgDimensions.width }
				/>
			}
			{ displayTitle && title &&
				<TitleTag>
					{ title }
				</TitleTag>
			}
			{ displayCatID && catID &&
				<div
					style     = { { fontSize: fontSize + 'em' } }
				>
					{ catID }
				</div>
			}
			{ displayCaption && captionText &&
				<RichText.Content
					className = 'caption-text-field'
					style     = { { fontSize: fontSize + 'em' } }
					tagName   = 'p'
					value     = { captionText }
				/>
			}
		</>
	);

	const linkedBody = ( linkToObject && objectURL ) ? <a className='object-link' href={ objectURL }>{ body }</a> : body;

	return (
		<div className = 'image-selector'>
			{ linkedBody }
		</div>
	);
}