import './admin.scss';

import { createRoot } from '@wordpress/element';

import Dashboard from './dashboard';
import GeneralOptions from './general';
import { ObjectPage } from './objects';
import RemoteAdmin from './remote';

if ( !! document.getElementById( 'wpm-react-admin-app-container-general') ) {
	const root = createRoot( document.getElementById( 'wpm-react-admin-app-container-general') );
	root.render( 
		<GeneralOptions />
	);
} else if ( !! document.getElementById( 'wpm-react-admin-app-container-dashboard') ) {
	const root = createRoot( document.getElementById( 'wpm-react-admin-app-container-dashboard' ) );
	root.render( 
		<Dashboard />
	);
} else if ( !! document.getElementById( 'wpm-react-admin-app-container-objects') ) {
	const root = createRoot( document.getElementById( 'wpm-react-admin-app-container-objects') );
	root.render( 
		<ObjectPage />
	);
} else if ( !! document.getElementById( 'wpm-react-admin-app-container-remote') ) {
	const root = createRoot( document.getElementById( 'wpm-react-admin-app-container-remote' ) ); 
	root.render( 
		<RemoteAdmin />
	);
}