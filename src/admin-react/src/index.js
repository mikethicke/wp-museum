import './admin.scss';

import { render } from '@wordpress/element';

import Dashboard from './dashboard';
import GeneralOptions from './general';
import { ObjectPage } from './objects';
import RemoteAdmin from './remote';

if ( !! document.getElementById( 'wpm-react-admin-app-container-general') ) {
	render( 
		<GeneralOptions />,
		document.getElementById( 'wpm-react-admin-app-container-general')
	);
} else if ( !! document.getElementById( 'wpm-react-admin-app-container-dashboard') ) {
	render( 
		<Dashboard />,
		document.getElementById( 'wpm-react-admin-app-container-dashboard')
	);
} else if ( !! document.getElementById( 'wpm-react-admin-app-container-objects') ) {
	render( 
		<ObjectPage />,
		document.getElementById( 'wpm-react-admin-app-container-objects')
	);
} else if ( !! document.getElementById( 'wpm-react-admin-app-container-remote') ) {
	render( 
		<RemoteAdmin />,
		document.getElementById( 'wpm-react-admin-app-container-remote')
	);
}