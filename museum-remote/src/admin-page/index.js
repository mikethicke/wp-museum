import {
	render,
	useState,
	useEffect,
	useRef
} from '@wordpress/element';
import {
	Button,
	Spinner
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const SiteInfo = props => {
	const {
		currentlyConnecting,
		connectionError,
		siteData
	} = props;

	const {
		title,
		description,
		url,
		collections,
		object_count : objectCount
	} = siteData;

	const collectionCount = Array.isArray( collections ) ? collections.length : 0;

	if ( currentlyConnecting ) {
		return (
			<div className = 'site-info'>
				<div className = 'connection-status connecting'>
					<Spinner />Connecting...
				</div>
			</div>
		);
	}

	if ( connectionError ) {
		return (
			<div className = 'site-info'>
				<div className = 'connection-status error'>
					Connection error: { connectionError }
				</div>
			</div>
		);
	}

	return (
		<div className = 'site-info'>
			<div className = 'connection-status success'>
				Connected
			</div>
			<table>
				<tbody>
					<tr><td>Site:</td><td>{ title }</td></tr>
					<tr><td>URL:</td><td>{ url }</td></tr>
					<tr><td>Collection count:</td><td>{ collectionCount }</td></tr>
					<tr><td>Object count:</td><td>{ objectCount }</td></tr>
				</tbody>
			</table>
			
		</div>
	);
}

const RemoteAdminPage = () => {
	const wpmRestBase = '/wp-json/wp-museum/v1';
	const mrRestBase = '/museum-remote/v1'

	const [ remoteURL, setRemoteURL ] = useState( null );
	const [ siteData, setSiteData ] = useState( {} );
	const [ currentlyConnecting, setCurrentlyConnecting ] = useState( false );
	const [ connectionError, setConnectionError ] = useState( null );

	const textInput = useRef( null );
	useEffect( () => textInput.current.focus(), [] );

	useEffect(
		() => {
			apiFetch( { path: `${mrRestBase}/remote_url` } )
				.then( result => {
					if ( result ) {
						setRemoteURL( result );
						doConnect( result );
					}
				} );
		}, []
	);

	const updateRemoteUrlOption = ( newUrl = null ) => {
		const data = newUrl != null ? newUrl : remoteURL;
		apiFetch(
			{
				path   : `${mrRestBase}/remote_url`,
				method : 'POST',
				data   : data,
			}
		).then( result => console.log( result ) );
	}
	
	const onUrlChange = event => {
		setRemoteURL( event.target.value );
	}

	const onUrlBlur = () => {
		const newUrl = cleanUrl();
		updateRemoteUrlOption( newUrl );
	}

	const cleanUrl = ( newUrl = null ) => {
		let cleanedUrl = newUrl ? newUrl : remoteURL;
		cleanedUrl = cleanedUrl.trim();
		cleanedUrl = cleanedUrl.endsWith('/') ? cleanedUrl.slice(0, -1 ) : cleanedUrl;
		cleanedUrl = cleanedUrl
			.startsWith('http://') || cleanedUrl.startsWith('https://' ) ?
			cleanedUrl :
			'http://' + cleanedUrl;
		setRemoteURL( cleanedUrl );
		return cleanedUrl;
	}

	/**
	 * Connects to remote site, checks response, and if everything is ok update the site data.
	 *
	 * @see https://developers.google.com/web/ilt/pwa/working-with-the-fetch-api
	 */
	const doConnect = ( newUrl = null ) => {
		setCurrentlyConnecting( true );
		setConnectionError( null );
		const cleanedUrl = cleanUrl( newUrl );
		updateRemoteUrlOption( cleanedUrl );

		const validateResponse = response => {
			if ( ! response.ok ) {
				throw Error( response.statusText );
			}
			return response;
		}

		const readJSONResponse = response => {
			response.json().then( data => setSiteData( data ) );
		}

		const stopConnecting = () => {
			setCurrentlyConnecting( false );
		}

		const catchError = error => {
			stopConnecting();
			setConnectionError( error.message );
		}
		
		fetch( `${cleanedUrl}${wpmRestBase}/site_data` )
			.then( validateResponse )
			.then( readJSONResponse )
			.then( stopConnecting )
			.catch( catchError );
	}

	const maybeConnect = event => {
		if ( event.key === 'Enter' ) {
			event.preventDefault();
        	event.stopPropagation();
			doConnect();
		}
	}

	return (
		<div className = 'remote-admin-page'>
			<h2>Museum Remote Configuration</h2>
			<label>
				Remote Museum URL:
				<input
					type = 'url'
					ref = { textInput }
					placeholder = 'http://example.com'
					pattern = "https?:\/\/.*"
					onChange = { onUrlChange }
					onBlur   = { onUrlBlur }
					onKeyDown = { maybeConnect }
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
			{ ( currentlyConnecting || connectionError || Object.keys( siteData ).length > 0 ) &&
				<SiteInfo
					currentlyConnecting = { currentlyConnecting }
					connectionError     = { connectionError }
					siteData            = { siteData }
				/>
			}
		</div>
	);
}

if ( !! document.getElementById( 'museum-remote-admin-container' ) ) {
	render(
		<RemoteAdminPage />,
		document.getElementById( 'museum-remote-admin-container' )
	);
}