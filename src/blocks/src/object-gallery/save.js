/**
 * Returns static HTML for frontend display of block.
 */

/**
 * WordPress dependencies
 */
import { RichText } from '@wordpress/blockEditor';

export default function save ( { attributes } ) {
	const {
		columns,
		objectURL,
		imgData,
		captionText,
		title,
		catID,
		fontSize,
		titleTag,
		displayTitle,
		displayCaption,
		linkToObject,
		displayCatID
	} = attributes;

	const TitleTag = titleTag;

	const percentWidth = Math.round( 1 / columns * 100 ) + '%';
	const imgStyle = {
		flexBasis: percentWidth
	}

	const grid = imgData.map( ( imgItem, index ) =>
		<div
			className = 'gallery-image-wrapper'
			style     = { imgStyle }
			key       = { `image-${index}` }
		>
			<img
				src = { imgItem.imgURL }
			/>
		</div>
	);

	return (
		<div
			className = 'object-gallery-block'
		>
			{ linkToObject &&
				<a className = 'object-link' href = { objectURL }>Hidden Link Text</a>
			}
			{ displayTitle && title &&
				<TitleTag>
					{ title }
				</TitleTag>
			}
			<div
				className = 'gallery-grid'
			>
				{ grid }
			</div>
			<div
				className = 'bottom-text-wrapper'
				style     = { { fontSize: fontSize + 'em' } }
			>
				{ displayCatID && catID &&
					<div
						className = 'cat-id'
					>
						{ catID }
					</div>
				}
				{ displayCaption && captionText &&
					<RichText.Content
						tagName   = 'p'
						className = 'caption-text-field'
						value     = { captionText }
					/>
				}
			</div>
		</div>
	);


}