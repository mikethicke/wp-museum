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
				width  <  bestFitImage.width ) {
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
		] = selectedImageData['full'];
		bestFitImage.URL    = URL;
		bestFitImage.height = height;
		bestFitImage.width  = width
	}

	return bestFitImage;
}