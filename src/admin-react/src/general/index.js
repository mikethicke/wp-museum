import apiFetch from '@wordpress/api-fetch';

import {
	useState,
	useEffect,
} from '@wordpress/element';

import {
	CheckboxControl,
	Button
} from '@wordpress/components';

import { __ } from '@wordpress/i18n';

import { baseRestPath } from "../util";

const GeneralOptions = () => {
    const [ siteOptions, updateSiteOptions ] = useState( {} );

    const {
        clear_data_on_uninstall
    } = siteOptions;

	useEffect( () => {
		refreshSiteOptions();
	}, [] );

    const refreshSiteOptions = () => {
		apiFetch( { path: `${baseRestPath}/admin_options`} )
			.then( response => {
				updateSiteOptions( response );
				return response;
			} );
	}

	const doSave = () => {
		apiFetch( {
			path   : `${baseRestPath}/admin_options`,
			method : 'POST',
			data   : siteOptions
		} );
	}

	const updateOption = ( option, newValue ) => {
		const updatedOptions = Object.assign( {}, siteOptions );
		updatedOptions[ option ] = newValue;
		updateSiteOptions( updatedOptions );
	}

    return (
		<div className = 'museum-admin-options'>
			<h2>Options</h2>
			{ siteOptions &&
				<CheckboxControl
                    label    = { __( 'Delete all museum data on uninstall.' ) }
                    checked  = { !! clear_data_on_uninstall }
                    onChange = { isChecked => updateOption( 'clear_data_on_uninstall', isChecked ) }
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

export default GeneralOptions;