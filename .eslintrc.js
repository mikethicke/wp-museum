const defaultConfig = require( './node_modules/@wordpress/scripts/config/.eslintrc.js' );
const wpmConfig = Object.assign( {}, defaultConfig );
wpmConfig.rules = {
		"prettier/prettier": "off", 
		"jsx-a11y/label-has-for": "off"
}
module.exports = wpmConfig;
