import {
	render,
	useState,
	useEffect
} from '@wordpress/element';
import {
	Button
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const RemoteAdminPage = () => {
	const wpmRestBase = '/wp-json/wp-museum/v1';
	const wordPressRestBase = '/wp-json/wp/v2';

	const [ remoteURL, setRemoteURL ] = useState( null );
	const [ siteData, setSiteData ] = useState( null );

	const onUrlChange = event => {
		setRemoteURL( event.target.value );
	}

	const doConnect = () => {
		const trimmedUrl = remoteURL.endsWith('/') ? remoteURL.slice(0, -1 ) : remoteURL;
		fetch( `${trimmedUrl}${wordPressRestBase}/settings` ).then( result  =>
			{
				console.log( result );
			}
		);
	} 

	
	return (
		<div className = 'remote-admin-page'>
			<h2>Museum Remote Configuration</h2>
			<label>
				Remote Museum URL:
				<input
					type = 'url'
					placeholder = 'http://example.com/'
					pattern = "https?:\/\/.*"
					onChange = { onUrlChange }
					value = { remoteURL }
				/>
			</label>
			<Button
				isLarge
				isPrimary
				onClick = { doConnect }
			>
				Connect
			</Button>

		</div>
	);
}

if ( !! document.getElementById( 'museum-remote-admin-container' ) ) {
	render(
		<RemoteAdminPage />,
		document.getElementById( 'museum-remote-admin-container' )
	);
}