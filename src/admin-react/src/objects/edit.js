import {
	useState,
	useEffect
} from '@wordpress/element';

import {
	Button,
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

import FieldEdit from './field-edit';
import KindSettings from './kind-settings';

const Edit = props => {
	const {
		kindItem,
		kinds,
		updateKind,
		saveKindData,
	} = props;

	const {
		kind_id   : kindId,
		label     : kindLabel,
		type_name : kindPostType,
	} = kindItem;

	const baseRestPath = '/wp-museum/v1';

	const [ fieldData, setFieldData ] = useState( null );
	const [ newFieldCount, setNewFieldCount ] = useState( 0 );

	useEffect( () => {
		if ( ! fieldData ) {
			refreshFieldData();
		}
	} );

	const refreshFieldData = () => {
		apiFetch( { path: `${baseRestPath}/${kindPostType}/fields_all`} ).then( setFieldData );
	}

	const updateKindData = ( field, event ) => {
		updateKind( kindId, field, event );
	}

	const doSave = () => {
		saveFieldData();
		saveKindData();
	}
	
	const saveFieldData = () => {
		apiFetch( {
			path   : `${baseRestPath}/${kindPostType}/fields_all`,
			method : 'POST',
			data   : fieldData
		} ).then( refreshFieldData );
	}
	
	const updateField = ( fieldId, fieldItem, changeEvent ) => {
		const newFieldData = Object.assign( {}, fieldData );

		if ( fieldItem.startsWith('dimension') ) {
			const [ dimension, key, index ] = fieldItem.split( '.' );
			const dimensionsField = fieldData[ fieldId ]['dimensions'];
			const newDimensionData = dimensionsField ?
				JSON.parse( dimensionsField ) :
				{
					n : 1,
					labels : [ '', '', '' ]
				};
			if ( key == 'n' ) {
				newDimensionData.n = changeEvent.target.value;
			} else {
				newDimensionData[key][index] = changeEvent.target.value;
			}
			newFieldData[ fieldId ]['dimensions'] = JSON.stringify( newDimensionData );
			setFieldData( newFieldData );
			return;
		}

		if ( changeEvent.target.type === 'checkbox' ) {
			if ( fieldData[ fieldId ][ fieldItem ] != changeEvent.target.checked ) {
				newFieldData[ fieldId ][ fieldItem ] = changeEvent.target.checked;
				setFieldData( newFieldData );
			}
			return;
		}
		
		if ( fieldData[ fieldId ][ fieldItem ] != changeEvent.target.value ) {
			newFieldData[ fieldId ][ fieldItem ] = changeEvent.target.value;
			setFieldData( newFieldData );
		}
	}

	const deleteField = ( fieldId ) => {
		const newFieldData = Object.assign( {}, fieldData );
		newFieldData[ fieldId ]['delete'] = true;
		setFieldData( newFieldData );
	}

	const updateFactors = ( fieldId, newFactors ) => {
		if ( fieldData[ fieldId ]['factors'] != newFactors ) {
			const newFieldData = Object.assign( {}, fieldData );
			newFieldData[ fieldId ]['factors'] = newFactors;
			setFieldData( newFieldData );
		}
	}

	const moveItem = ( fieldId, move ) => {
		const oldOrder = fieldData[ fieldId ]['display_order'];
		const targetOrder = oldOrder + move;
		if ( targetOrder < 0 ) return;
		
		const fieldValues = Object.values( fieldData );
		if ( targetOrder >= fieldValues.length ) return;

		const targetIndex = fieldValues.findIndex( fieldItem => fieldItem['display_order'] == targetOrder );
		const targetKey = fieldValues[ targetIndex ]['field_id'];

		const newFieldData = Object.assign( {}, fieldData );
		newFieldData[ fieldId ]['display_order'] = targetOrder;
		newFieldData[ targetKey ]['display_order'] = oldOrder;
		setFieldData( newFieldData );
	}

	const defaultFieldData = {
		field_id              : 0 - ( newFieldCount + 1 ),
		slug                  : '',
		kind_id               : kindId,
		name                  : '',
		type                  : 'plain',
		display_order         : 0,
		public                : true,
		required              : false,
		quick_browse          : false,
		help_text             : '',
		detailed_instructions : '',
		public_description    : '',
		field_schema          : '',
		max_length            : 0,
		dimensions            : '',
		factors               : '',
		units                 : ''
	};
	if ( fieldData ) {
		const sortedFields = Object.values( fieldData )
			.sort( (a, b) => a['display_order'] < b['display_order'] ? 1 : -1 );
		defaultFieldData.display_order = sortedFields[0].display_order + 1;
	}

	const addField = () => {
		const updatedFieldData = fieldData ? Object.assign( {}, fieldData ) : {};
		updatedFieldData[ defaultFieldData.field_id ] = defaultFieldData;
		setNewFieldCount( newFieldCount + 1 );
		setFieldData( updatedFieldData );
	}

	let fieldForms;
	if ( fieldData ) {
		fieldForms = Object.entries( fieldData )
			.filter( ( [ fieldId, dataItem ] ) => ( typeof dataItem.delete == 'undefined' || ! dataItem.delete ) )
			.sort( (a, b) => a[1]['display_order'] > b[1]['display_order'] ? 1 : -1 )
			.map( ( [ fieldId, dataItem ] ) => {
				return (
					<FieldEdit
						key           = { fieldId }
						fieldData     = { dataItem }
						updateField   = { updateField }
						updateFactors = { updateFactors }
						deleteField   = { deleteField }
						moveItem      = { moveItem }
					/>
				);
			} );
	}

	return (
		<div>
			<div className = 'do-save'>
				<Button
					onClick = { doSave }
					isPrimary
					isLarge
				>
					Save
				</Button>
			</div>
			<h1>{ kindLabel }</h1>
			<div className = 'kind-edit-wrapper'>
				<div className = 'kind-edit'>
					<div className = 'kind-settings'>
						<KindSettings
							kindData = { kindItem }
							fieldData = { fieldData }
							kinds = { kinds }
							updateKindData = { updateKindData }
						/>
					</div>
					<h2>Fields</h2>
					{ !! fieldForms && fieldForms }
					<Button
						className = 'new-field-button'
						onClick   = { addField }
						isLarge
						isSecondary
					>
						Add New Field
					</Button>
				</div>
				<div className = 'field-instructions'></div>
			</div>
		</div>
	);
}

export default Edit;