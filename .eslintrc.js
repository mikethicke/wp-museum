const defaultConfig = require( './node_modules/@wordpress/scripts/config/.eslintrc.js' );
const wpmConfig = Object.assign( {}, defaultConfig );
wpmConfig.rules = {
		"prettier/prettier": "off"
}
module.exports = wpmConfig;
