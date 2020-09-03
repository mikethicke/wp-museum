import apiFetch from "@wordpress/api-fetch";


export const baseRestPath = '/wp-museum/v1/';

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
		if ( ! Array.isArray( dataArray ) || dataArray.length < 4 ) {
			continue;
		}

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

export function getFirstObjectImage( imgData ) {
	if ( isEmpty( imgData ) ) {
		return null;
	}
	return Object.values( imgData )[0];
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

/**
 * Generates a UUID to uniquely identify remote site to central site.
 *
 * @see https://stackoverflow.com/questions/105034/how-to-create-guid-uuid/2117523#2117523
 */
export function generateUUID() {
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
	  var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
	  return v.toString(16);
	});
}

/**
 * Efficient test if an object is empty (ie. {} ).
 * 
 * @see https://stackoverflow.com/questions/679915/how-do-i-test-for-an-empty-javascript-object
 * @param {Object} obj Object to test for being empty
 */
export function isEmpty(obj) {
	if ( obj === null ) {
		return true;
	}

	for( let prop in obj ) {
        if( obj.hasOwnProperty( prop ) )
            return false;
    }
    return true;
}

/**
 * Takes attributes passed from wp_localize_script to frontend scripts and
 * parses and recasts them to match the format of attributes in the editor.
 *
 * @param {Object} attributes Attributes of a block, passed from
 *                            wp_localize_script.
 */
export function cleanAttributes( attributes ) {
	for ( const [ key, value ] of Object.entries( attributes) ) {
		if ( ! isNaN( value ) ) {
			let newValue = value;
			if ( newValue === '' ) {
				newValue = null;
			} else {
				newValue = parseInt( value );
				if ( newValue === 0 ) {
					newValue = false;
				}
			}
			attributes[key] = newValue;
		}
	}
	return null;
}

/**
 * Optionally links to or calls onClick callback when clicked on.
 * 
 * @param {*} props The component's properties
 */
export const MaybeLink = props => {
	const {
		href,
		onClickCallback,
		children,
		doLink
	} = props
	
	if ( doLink ) {
		return (
			<a href = { href }>{ children }</a>
		)
	}
	if ( !! onClickCallback ) {
		return (
			<a onClick = { onClickCallback }>{ children }</a>
		)
	}
	return ( <>{ children }</> );
}

/**
 * Returns a promise that returns image data for a museum object.
 */
export const fetchObjectImages = objectID => {
	const baseRestPath = '/wp-museum/v1/';

	return apiFetch( { path: `${baseRestPath}/all/${objectID}/images` } );
}