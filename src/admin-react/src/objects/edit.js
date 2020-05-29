import {
	useState,
	useEffect
} from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import FieldEdit from './field-edit';

const Edit = props => {
	const {
		kind
	} = props;

	const {
		kind_id   : kindId,
		label     : kindLabel,
		type_name : kindPostType,
	} = kind;

	const baseRestPath = '/wp-museum/v1';

	const [ fieldData, setFieldData ] = useState( null );

	useEffect( () => {
		if ( ! fieldData ) {
			apiFetch( { path: `${baseRestPath}/${kindPostType}/fields_all`} ).then( setFieldData );
		}
	} );

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
		
		if ( fieldData[ fieldId ][ fieldItem ] != changeEvent.target.value ) {
			newFieldData[ fieldId ][ fieldItem ] = changeEvent.target.value;
			setFieldData( newFieldData );
		}
	}

	const deleteField = ( fieldId ) => {
		const newFieldData = Object.assign( {}, fieldData );
		delete newFieldData[ fieldId ];
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

	let fieldForms;
	if ( fieldData ) {
		fieldForms = Object.entries( fieldData )
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
			<h1>{ kindLabel }</h1>
			<div className = 'fields-edit-wrapper'>
				<div className = 'field-edit'>
					{ !! fieldForms && fieldForms }
				</div>
				<div className = 'field-instructions'></div>
			</div>
		</div>
	);
}

export default Edit;