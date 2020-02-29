import {
	Component
} from '@wordpress/element';

// https://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
function hexToRgb(hex) {
	// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
	var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
	hex = hex.replace(shorthandRegex, function(m, r, g, b) {
	  return r + r + g + g + b + b;
	});
  
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? {
	  r: parseInt(result[1], 16),
	  g: parseInt(result[2], 16),
	  b: parseInt(result[3], 16)
	} : null;
  }

class InfoContent extends Component {

	render()  {
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
		} = this.props;
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
						className = { 'img_info_' + imageAlignment }
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
			borderStyle: 'solid',
			padding: '5px',
			borderColor: borderColor,
			backgroundColor: `rgba( ${bRGB.r}, ${bRGB.g}, ${bRGB.b}, ${backgroundOpacity} )`,
		}

		if ( objectID !== null ) {	
			return [
				<div style={ divStyle }>
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
}

export default InfoContent;