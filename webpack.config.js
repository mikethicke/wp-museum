const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry : {
		'blocks-edit' : {
			import : './src/blocks/src/index.js',
			filename : 'blocks-edit.js'
		},
		'blocks-front' : {
			import : './src/blocks/src/frontend.js',
			filename : 'blocks-frontend.js'
		},
		'admin' : {
			import : './src/admin-react/src/index.js',
			filename : 'admin-react.js',
		},
		'remote' : {
			import : './museum-remote/src/index.js',
			filename : 'museum-remote-admin.js'
		},
		'remote-front' : {
			import : './museum-remote/src/frontend.js',
			filename : 'museum-remote-front.js'
		}
	}
};

