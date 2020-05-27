import { render } from '@wordpress/element';

import Dashboard from './dashboard';

if ( !! document.getElementById( 'wpm-react-admin-app-container-dashboard') ) {
	render( 
		<Dashboard />,
		document.getElementById( 'wpm-react-admin-app-container-dashboard')
	);
}