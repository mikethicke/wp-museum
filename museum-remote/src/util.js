
/**
 * Generates a UUID to uniquely identify remote site to central site.
 *
 * @see https://stackoverflow.com/questions/105034/how-to-create-guid-uuid/2117523#2117523
 */
function generateUUID() {
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
	  var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
	  return v.toString(16);
	});
}

export { generateUUID };