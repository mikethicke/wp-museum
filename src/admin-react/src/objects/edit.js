import {
	useState,
	useEffect
} from '@wordpress/element';

import {
	Button,
	Spinner
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
		isSaving,
		setIsSaving,
	} = props;

	const {
		kind_id   : kindId,
		label     : kindLabel,
		type_name : kindPostType,
	} = kindItem;

	const baseRestPath = '/wp-museum/v1';

	const dimensionsDefault = {
		n : 1,
		labels : [ '', '', '' ]
	}

	const [ fieldData, setFieldData ] = useState( null );
	const [ nextFieldId, setNextFieldId ] = useState( -1 );

	useEffect( () => {
		if ( ! fieldData ) {
			refreshFieldData();
		}
	} );

	const refreshFieldData = () => {
		apiFetch( { path: `${baseRestPath}/${kindPostType}/fields`} ).then( setFieldData );
	}

	const updateKindData = ( field, event ) => {
		updateKind( kindId, field, event );
	}

	const doSave = () => {
		saveFieldData();
		saveKindData();
	}
	
	const saveFieldData = ( refreshNeeded = false, updatedFieldData = null ) => {
		setIsSaving( true );
		const saveData = updatedFieldData ? updatedFieldData : fieldData;
		apiFetch( {
			path   : `${baseRestPath}/${kindPostType}/fields`,
			method : 'POST',
			data   : saveData
		} ).then( () => { 
			if ( refreshNeeded ) {
				refreshFieldData();
			}
			setIsSaving( false );
		} );
	}
	
	const updateField = ( fieldId, fieldItem, changeEvent ) => {
		const newFieldData = Object.assign( {}, fieldData );

		if ( fieldItem.startsWith('dimension') ) {
			const [ dimension, key, index ] = fieldItem.split( '.' );
			const dimensionsField = fieldData[ fieldId ]['dimensions'];
			const newDimensionData = dimensionsField ? dimensionsField : dimensionsDefault;
			if ( key == 'n' ) {
				newDimensionData.n = changeEvent.target.value;
			} else {
				newDimensionData[key][index] = changeEvent.target.value;
			}
			newFieldData[ fieldId ]['dimensions'] = newDimensionData;
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
		saveFieldData( true, newFieldData );
	}

	const updateFactors = ( fieldId, newFactors ) => {
		if ( JSON.stringify( fieldData[ fieldId ]['factors'] ) != JSON.stringify( newFactors ) ) {
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
		field_id              : 0,
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
		dimensions            : dimensionsDefault,
		factors               : [],
		units                 : ''
	};

	const addField = () => {
		const updatedFieldData = fieldData ? Object.assign( {}, fieldData ) : {};
		updatedFieldData[ nextFieldId ] = defaultFieldData;
		updatedFieldData[ nextFieldId ]['field_id'] = nextFieldId;
		if ( fieldData && Object.values( fieldData ).length > 0 ) {
			const sortedFields = Object.values( fieldData )
				.sort( (a, b) => a['display_order'] < b['display_order'] ? 1 : -1 );
			updatedFieldData[ nextFieldId ]['display_order'] = sortedFields[0]['display_order'] + 1;
		}
		setNextFieldId( nextFieldId - 1 );
		setFieldData( updatedFieldData );
		saveFieldData( true, updatedFieldData );
	}

	let fieldForms;
	if ( fieldData ) {
		fieldForms = Object.values( fieldData )
			.filter( ( dataItem ) => ( typeof dataItem.delete == 'undefined' || ! dataItem.delete ) )
			.sort( (a, b) => a['display_order'] > b['display_order'] ? 1 : -1 )
			.map( ( dataItem ) => (
					<FieldEdit
						key               = { dataItem['field_id'] }
						fieldData         = { dataItem }
						updateField       = { updateField }
						updateFactors     = { updateFactors }
						deleteField       = { deleteField }
						moveItem          = { moveItem }
						saveFieldData     = { saveFieldData }
						dimensionsDefault = { dimensionsDefault }
					/>
			) );
	}

	return (
		<div>
			<div className = 'edit-header'>
				<h1>{ kindLabel }</h1>
				<Button
					className = 'do-save-button'
					onClick = { doSave }
					isPrimary
					isLarge
				>
					Save
				</Button>
				{ isSaving &&
					<div className='is-saving'>
						<Spinner />
						Saving...
					</div>
				}
			</div>	
			<div className = 'kind-edit-wrapper'>
				<div className = 'kind-edit'>
					<div 
						className = 'kind-settings'
						onBlur    = { saveKindData }
					>
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