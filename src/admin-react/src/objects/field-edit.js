import {
	useState
} from '@wordpress/element';

import {
	Button,
	SVG,
	Path,
	Toolbar,
	ToolbarButton
} from '@wordpress/components';

import FactorEditModal from './factor-edit';

const trash = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24">
		<Path d="M12 4h3c.6 0 1 .4 1 1v1H3V5c0-.6.5-1 1-1h3c.2-1.1 1.3-2 2.5-2s2.3.9 2.5 2zM8 4h3c-.2-.6-.9-1-1.5-1S8.2 3.4 8 4zM4 7h11l-.9 10.1c0 .5-.5.9-1 .9H5.9c-.5 0-.9-.4-1-.9L4 7z" />
	</SVG>
);

const chevronDown = (
	<SVG viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
		<Path d="M17 9.4L12 14 7 9.4l-1 1.2 6 5.4 6-5.4z" />
	</SVG>
);

const chevronUp = (
	<SVG viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
		<Path d="M12 8l-6 5.4 1 1.2 5-4.6 5 4.6 1-1.2z" />
	</SVG>
);

const MoveToolbar = ( props ) => {
	const {
		moveUp,
		moveDown,
	} = props;

	return (
		<Toolbar>
			<ToolbarButton
				icon    = { chevronUp }
				onClick = { moveUp }
			/>
			<ToolbarButton
				icon    = { chevronDown }
				onClick = { moveDown }
			/>
		</Toolbar>
	);
}

const FieldEdit = props => {
	const {
		fieldData,
		updateField,
		updateFactors,
		deleteField,
		moveItem,
	} = props;

	const {
		field_id              : fieldId,
								name,
								type,
		display_order         : displayOrder,
		public                : isPublic,
								required,
		quick_browse          : quickBrowse,
		help_text             : helpText,
		detailed_instructions : detailedInstructions,
		public_description    : publicDescription,
		field_schema          : fieldSchema,
		max_length            : maxLength,
		dimensions            : dimensions,
								factors,
								units
	} = fieldData;

	const [ factorModalOpen, setFactorModalOpen ] = useState( false );

	const dimensionData = dimensions ?
		JSON.parse( dimensions ) :
		{
			n : 1,
			labels: [ '', '', '' ]
		};
	
	const factorData = factors ?
		JSON.parse( factors ) :
		[];
	
	const updateFactorData = newFactorData => {
		updateFactors( fieldId, JSON.stringify( newFactorData ) );
	}

	const deleteThisField = () => {
		let confirmDelete = confirm( 'Really delete field? This cannot be undone. Deleting field will not remove data from database, but it will be inaccessible unless a new field with the same name is created.');
		if ( confirmDelete ) {
			deleteField( fieldId );
		}
	}

	const moveUp = () => {
		moveItem( fieldId, -1 );
	}

	const moveDown = () => {
		moveItem( fieldId, 1 );
	}
	
	const selectOptions = [
		{ value: 'plain', label: 'Plain Text' },
		{ value: 'rich', label: 'Rich Text' },
		{ value: 'date', label: 'Date' },
		{ value: 'measure', label: 'Measure' },
		{ value: 'factor', label: 'Factor' },
		{ value: 'multiple', label: 'Multiple Factor' },
		{ value: 'flag', label: 'Flag' }
	]

	const selectOptionsElements = selectOptions.map( ( option, index ) => (
		<option
		key = { index }	
		value = { option.value }
		>
			{ option.label }
		</option>
	) );

	let dimensionElements = [];
	if ( dimensionData.n > 1 ) {
		for ( let i = 0; i < dimensionData.n; i++ ) {
			dimensionElements[i] = (
				<div
					className = 'dimension-field'
					key = { i }
				>
					<label>
						Dimension { i + 1}
						<input
							type = 'text'
							value = { dimensionData.labels[i] }
							onChange = { event => updateField( fieldId, `dimension.labels.${i}`, event ) }
						/>
					</label>	
				</div>
			);
		}
	}
	
	const newField = ! fieldData;

	return (
		<div className = 'field-form'>
			<MoveToolbar
				moveUp   = { moveUp }
				moveDown = { moveDown }
			/>
			<div className = 'delete-field'>
				<Button
					className = 'delete-field-button'
					icon = { trash }
					onClick = { deleteThisField }
				/>
			</div>
			<div className = 'field-section'>
				<label>
					Label
					<input
						type = 'text'
						value = { name }
						onChange = { event => updateField( fieldId, 'name', event ) }
					/>
				</label>
			</div>
			<div className = 'field-type'>
				<div className = 'field-type-group'>
					<div className = 'field-section'>
						<label>
							Type
							<select
								value = { type }
								onChange = { event => updateField( fieldId, 'type', event ) }
							>
								{ selectOptionsElements }
							</select>
						</label>
					</div>
					{ ! newField && ( type == 'plain' || type == 'rich' ) && 
						<div className = 'field-section'>
							<label>
								Max Length
								<input
									type = 'number'
									value = { maxLength }
									onChange = { event => updateField( fieldId, 'max_length', event ) }
								/>
							</label>
						</div>
					}
					{ ! newField && ( type == 'factor' || type == 'multiple' ) &&
						<div className = 'field-section factor-button'>
							<Button
								className = 'field-edit-button button'
								onClick = { () => setFactorModalOpen( true ) }
								title = 'Open factors modal'
							>
								Edit Factors
							</Button>
							{ factorModalOpen && 
								<FactorEditModal
									factorData = { factorData }
									updateFactorData = { updateFactorData }
									close = { () => setFactorModalOpen( false ) }
								/>
							}
						</div>
					}
					{ ! newField && type == 'measure' &&
						<>
						<div className = 'field-section'>
							<label>
								Dimensions
								<select
									value = { dimensionData.n }
									onChange = { event => updateField( fieldId, 'dimension.n', event ) }
								>
									<option value = '1'>1</option>
									<option value = '2'>2</option>
									<option value = '3'>3</option>
								</select>
							</label>
						</div>
						<div className = 'field-section units'>
							<label>
								Units
								<input
									type = 'text'
									value = { units }
									onChange = { event => updateField( fieldId, 'units', event ) }
								/>
							</label>
						</div>
						</>
					}
				</div>
				{ ! newField && type == 'measure' && dimensionData.n > 1 &&
					<div className = 'field-type-group' >
						<div className = 'dimension-labels'>
							{ dimensionElements }
						</div>
					</div>
				}
			</div>
			<div className = 'field-middle'>
				<div className = 'field-section'>
					<label>
						Field Schema
						<input
							type = 'text'
							value = { fieldSchema }
							onChange = { event => updateField( fieldId, 'field_schema', event ) }
							/>
					</label>
				</div>
				<div className = 'field-section'>
					<label>
						Help Text
						<input
							type = 'text'
							value = { helpText }
							onChange = { event => updateField( fieldId, 'help_text', event ) }
						/>
					</label>
				</div>
				<div className = 'field-section'>
					<label>
						Detailed Instructions
						<textarea
							value = { detailedInstructions }
							onChange = { event => updateField( fieldId, 'detailed_instructions', event ) }
						/>
					</label>
				</div>
				<div className = 'field-section'>
					<label>
						Public Description
						<textarea 
							value = { publicDescription }
							onChange = { event => updateField( fieldId, 'public_description', event ) }
						/>
					</label>
				</div>
			</div>
			<div className = 'field-checkboxes'>
				<label>
					Public
					<input
						type = 'checkbox'
						checked = { isPublic }
						onChange = { event => updateField( fieldId, 'public', event ) }
					/>
				</label>
				<label>
					Required
					<input
						type = 'checkbox'
						checked = { required }
						onChange = { event => updateField( fieldId, 'required', event ) }
					/>
				</label>
				<label>
					Quick Browse
					<input
						type = 'checkbox'
						checked = { quickBrowse }
						onChange = { event => updateField( fieldId, 'quick_browse', event ) }
					/>
				</label>
			</div>
		</div>
	);
}

export default FieldEdit;