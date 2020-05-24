// https://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
export function hexToRgb(hex) {
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

export function getBestImage( imgData, imgDimensions ) {
	const bestFitImage = {
		'URL'    : null,
		'height' : 99999999,
		'width'  : 99999999
	};
	
	for ( let [ sizeSlug, dataArray ] of Object.entries( imgData ) ) {
		let [
			URL,
			height,
			width,
			isIntermediate
		] = dataArray;

		if ( height >= imgDimensions.height && 
			 height <  bestFitImage.height && 
			 width  >= imgDimensions.width && 
			 width  <  bestFitImage.width
		   ) {
				bestFitImage.URL    = URL;
			 	bestFitImage.height = height;
			 	bestFitImage.width  = width;
		}
	}

	if ( bestFitImage.URL === null ) {
		const [
			URL,
			height,
			width,
			isIntermediate
		] = imgData['full'];
		bestFitImage.URL    = URL;
		bestFitImage.height = height;
		bestFitImage.width  = width
	}

	return bestFitImage;
}

/**
 * Javascript implementation of php's stripslashes.
 *
 * @link https://github.com/kvz/locutus/blob/master/src/php/strings/stripslashes.js
 * @param {string} str String to be unslashed.
 */
export function stripslashes (str) {
	//       discuss at: https://locutus.io/php/stripslashes/
	//      original by: Kevin van Zonneveld (https://kvz.io)
	//      improved by: Ates Goral (https://magnetiq.com)
	//      improved by: marrtins
	//      improved by: rezna
	//         fixed by: Mick@el
	//      bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
	//      bugfixed by: Brett Zamir (https://brett-zamir.me)
	//         input by: Rick Waldron
	//         input by: Brant Messenger (https://www.brantmessenger.com/)
	// reimplemented by: Brett Zamir (https://brett-zamir.me)
	//        example 1: stripslashes('Kevin\'s code')
	//        returns 1: "Kevin's code"
	//        example 2: stripslashes('Kevin\\\'s code')
	//        returns 2: "Kevin\'s code"
	return (str + '')
	  .replace(/\\(.?)/g, function (s, n1) {
		switch (n1) {
		  case '\\':
			return '\\'
		  case '0':
			return '\u0000'
		  case '':
			return ''
		  default:
			return n1
		}
	  })
  }