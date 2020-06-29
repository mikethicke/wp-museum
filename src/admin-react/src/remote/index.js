import apiFetch from '@wordpress/api-fetch';
import {
	useState,
	useEffect,
} from '@wordpress/element';
import {
	Button,
	CheckboxControl,
	SVG,
	Path,
} from '@wordpress/components';

const ClientsTable = props => {
	const {
		clientData,
		updateBlocked,
		deleteItem,
	} = props;

	const trash = (
		<SVG xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24">
			<Path d="M12 4h3c.6 0 1 .4 1 1v1H3V5c0-.6.5-1 1-1h3c.2-1.1 1.3-2 2.5-2s2.3.9 2.5 2zM8 4h3c-.2-.6-.9-1-1.5-1S8.2 3.4 8 4zM4 7h11l-.9 10.1c0 .5-.5.9-1 .9H5.9c-.5 0-.9-.4-1-.9L4 7z" />
		</SVG>
	);

	const clientRows = clientData
		.filter( clientItem => ! clientItem.delete )
		.map( ( clientItem, index ) => (
			<tr key = { index } >
				<td>
					<Button
						icon = { trash }
						onClick = { () => deleteItem( index ) }
					/>
				</td>
				<td>
					<CheckboxControl
						checked = { !! clientItem.blocked }
						onChange = { isChecked => updateBlocked( index, isChecked ) }
					/>
				</td>
				<td>{ clientItem.title }</td>
				<td><a href={ clientItem.url }>{ clientItem.url }</a></td>
				<td>{ clientItem.registration_time }</td>
			</tr>
		) );

	return (
		<table
			className = 'widefat'
		>
			<thead>
				<tr>
					<th></th>
					<th>Block</th>
					<th>Title</th>
					<th>URL</th>
					<th>First Registered</th>
				</tr>
			</thead>
			<tbody>
				{ !! clientRows && clientRows.length > 0 ?
					clientRows :
					<tr>
						<td colSpan = { 5 }>No registered clients</td>
					</tr>
				}
			</tbody>
		</table>
	)
}

const RemoteOptions = props => {
	const {
		siteOptions,
		updateSiteOption,
	} = props;

	const {
		allow_remote_requests,
		allow_unregistered_requests,
		rest_authorized_domains
	} = siteOptions;

	return (
		<div className = 'remote-options'>
			<CheckboxControl
				label = 'Allow remote requests'
				checked = { !! allow_remote_requests }
				onChange = { isChecked => updateSiteOption( 'allow_remote_requests', isChecked ) }
			/>
			<CheckboxControl
				label = 'Allow unregistered requests'
				checked = { !! allow_unregistered_requests }
				onChange = { isChecked => updateSiteOption( 'allow_unregistered_requests', isChecked ) }
			/>
			<label>
				Allow requests from these domains (leave blank for all):
				<input
					type = 'text'
					value = { rest_authorized_domains || '' }
					onChange = { event => updateSiteOption( 'rest_authorized_domains', event.target.value ) }
				/>
			</label>
		</div>
	)
}

const RemoteAdmin = props => {

	const [ clientData, updateClientData ] = useState( [] );
	const [ siteOptions, updateSiteOptions ] = useState( {} );

	const baseRestPath = '/wp-museum/v1';

	useEffect( () => {
		refreshClientData();
		refreshSiteOptions();
	}, [] );

	const refreshClientData = () => {
		apiFetch( { path: `${baseRestPath}/remote_clients`} )
			.then( response => {
				updateClientData( response );
				return response;
			} );
	}

	const refreshSiteOptions = () => {
		apiFetch( { path: `${baseRestPath}/admin_options`} )
			.then( response => {
				updateSiteOptions( response );
				return response;
			} );
	}

	const doSave = () => {
		apiFetch( {
			path   : `${baseRestPath}/remote_clients`,
			method : 'POST',
			data   : clientData
		} );
		apiFetch( {
			path   : `${baseRestPath}/admin_options`,
			method : 'POST',
			data   : siteOptions
		} );
	}


	const updateSiteOption = ( option, newValue ) => {
		const newOptions = Object.assign( {}, siteOptions );
		newOptions[ option ] = newValue;
		updateSiteOptions( newOptions );
	}

	const updateBlocked = ( index, isBlocked ) => {
		const newClientData = [ ...clientData ];
		newClientData[index].blocked = isBlocked;
		updateClientData( newClientData );
	}

	const deleteItem = ( index ) => {
		const newClientData = [ ...clientData ];
		newClientData[index].delete = true;
		updateClientData( newClientData );
	}
	
	return (
		<div className = 'museum-admin-remote'>
			<h2>Registered Clients</h2>
			{ clientData &&
				<ClientsTable
					clientData    = { clientData }
					updateBlocked = { updateBlocked }
					deleteItem    = { deleteItem }
				/>
			}
			<h2>Options</h2>
			{ siteOptions &&
				<RemoteOptions
					siteOptions      = { siteOptions }
					updateSiteOption = { updateSiteOption }
				/>
			}
			<div className = 'save-button-bottom'>
				<Button
					isPrimary
					isLarge
					onClick = { doSave }
				>
					Save
				</Button>
			</div>
		</div>
	);
}

export default RemoteAdmin;