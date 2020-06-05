import { render } from '@wordpress/element';

import Dashboard from './dashboard';
import { ObjectPage } from './objects';

if ( !! document.getElementById( 'wpm-react-admin-app-container-dashboard') ) {
	render( 
		<Dashboard />,
		document.getElementById( 'wpm-react-admin-app-container-dashboard')
	);
} else if ( !! document.getElementById( 'wpm-react-admin-app-container-objects') ) {
	render( 
		<ObjectPage />,
		document.getElementById( 'wpm-react-admin-app-container-objects')
	);
}