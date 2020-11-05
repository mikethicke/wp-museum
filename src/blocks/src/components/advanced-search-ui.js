
import {
	useState,
	useEffect
} from '@wordpress/element';

import {
	Button,
	CheckboxControl,
	SelectControl
} from '@wordpress/components';

import {
	isEmpty
} from '../util';

const FieldSearchElement = props => {
	const {
		fieldData,
		searchFieldData,
		updateSearch,
	} = props;

	const {
		field,
		search
	} = searchFieldData;

	const fieldDataArray = typeof fieldData === 'undefined' ?
		[] :
		Object.values( fieldData )

	let selectedFieldData = fieldDataArray.length > 0 && typeof field !== 'undefined' ?
		fieldDataArray.find( fieldItem => fieldItem.slug === field ) || {} :
		{};
	
	if ( isEmpty( selectedFieldData ) && fieldDataArray.length > 0 ) {
		selectedFieldData = fieldDataArray[0];
	}

	const {
		type = 'plain',
		slug = '',
	} = selectedFieldData;

	const fieldOptions = fieldDataArray.length > 0 ?
		fieldDataArray
			.filter( fieldItem => fieldItem.type !== 'flag' )
			.map( fieldItem => { return ( { label: fieldItem.name, value: fieldItem.slug } ) } ) :
		[];

	let inputElement;
	if ( type === 'date' ) {
		let searchVals;

		try {
			searchVals = JSON.parse( search );
		} catch {
			searchVals = {};
		}

		const {
			fromDate,
			toDate
		} = searchVals;

		const updateDateSearch = updateObj => {
			const newDateSearch = {
				...searchVals,
				...updateObj
			}

			updateSearch( field, JSON.stringify( newDateSearch ) );
		}

		inputElement = (
			<>
			<label>
				From:
				<input
					type = 'date'
					value = { fromDate || '' }
					onChange = { event => updateDateSearch( { from: event.target.value } ) }
				/>
			</label>
			<label>
				To:
				<input
					type = 'date'
					value = { toDate || '' }
					onChange = { event => updateDateSearch( { to: event.target.value } ) }
				/>
			</label>
			</>
		)
	} else {
		inputElement = (
			<input
				type = 'text'
				className = 'field-search-input'
				value = { search || '' }
				onChange = { event => updateSearch( field, event.target.value ) }
			/>
		);
	}

	return (
		<div className='field-search-element'>
			<SelectControl
				value = { slug }
				onChange = { val => updateSearch( val, search ) }
				options = { fieldOptions }
			/>
			{ inputElement }
		</div>
	)
}

const AdvancedSearchUI = props => {
	const {
		defaultSearch,
		showFlags,
		showCollections,
		showFields,
		showObjectType,
		showTitleToggle,
		getFieldData,
		kindsData,
		collectionData,
		onSearch,
		inEditor,
		setAttributes,
		fixSearch,
	} = props;

	const [ searchValues, setSearchValues ] = useState( {} );
	const [ fieldData, setFieldData ] = useState( {} );
	const [ numberFieldSearches, setNumberFieldSearches ] = useState( 3 );

	const {
		searchText,
		onlyTitle,
		selectedFlags,
		selectedCollections,
		selectedKind,
		searchFields,
	} = searchValues;

	useEffect( () => {
		setSearchValues( JSON.parse( defaultSearch ) );
	}, [] );

	useEffect( () => {
		if ( typeof selectedKind !== 'undefined' && !! kindsData && kindsData.length > 0 ) {
			const selectedKindData = kindsData.find( kindItem => kindItem.kind_id === selectedKind );
			getFieldData( selectedKindData.type_name ).then( result => setFieldData( result ) );
		}
	}, [ selectedKind, kindsData ] );

	useEffect( () => {
		if ( ! selectedKind && !! kindsData && kindsData.length > 0 ) {
			setSearchValues( {
				...searchValues,
				selectedKind: kindsData[0].kind_id
			} );
		}
	}, [ kindsData ] );

	const updateSearchValues = ( updatedValues ) => {
		setSearchValues( {
			...searchValues,
			...updatedValues
		} );
	}

	const updateFieldSearch = ( index, field = null, search = null ) => {
		const newSearchFields = !! searchFields ? [ ...searchFields ] : [];
		let fieldValue = field;
		let searchValue = search;
		if ( typeof newSearchFields[ index ] !== 'undefined' ) {
			if ( field === null ) {
				if ( newSearchFields[index].field === null ) {
					fieldValue = Object.values(fieldData)[0].slug;
				} else {
					fieldValue = newSearchFields[index].field;
				}
			}
			if ( search === null ) {
				searchValue = newSearchFields[index].search;
			}
		}
		newSearchFields[ index ] = { field: fieldValue, search: searchValue }
		updateSearchValues( { searchFields: newSearchFields } );
	}

	const flagOptions = () => {
		const opts = [];
		let optionIndex = 0;
		Object.entries( fieldData ).forEach( ( [ index, field ] ) => {
			if ( field.type === 'flag' ) {
				opts[optionIndex] = { value: field.slug, label: field.name };
				optionIndex++;
			}
		} );
		return opts;
	}

	const collectionOptions = () => {
		const opts = [];
		Object.entries( collectionData ).forEach( ( [ index, collection ] ) => {
			opts[index] = { value: collection.ID, label: collection.post_title };
		} );
		return opts;
	}

	const kindOptions = () => {
		const opts = [];
		kindsData.forEach( ( kindItem, index ) => {
			opts[index] = { value: kindItem.kind_id, label: kindItem.label };
		} );
		return opts;
	}

	let searchFieldElements = [];
	for ( let index = 0; index < numberFieldSearches; index++ ) {
		searchFieldElements[ index ] = (
			<FieldSearchElement
				key = { index }
				fieldData = { fieldData }
				searchFieldData = { !! searchFields && !! searchFields[ index ] ? 
					searchFields[ index ] :
					{}
				}
				updateSearch = { ( field, search ) => updateFieldSearch( index, field, search ) }
			/>
		);
	}
	
	return (
		<>
		{ inEditor &&
			<div className = 'advanced-search-editor-buttons'>
				<Button
					isSecondary
					onClick = { () => setSearchValues( {} ) }
				>
					Reset Search
				</Button>
				<Button
					isSecondary
					onClick = { () => setAttributes( { defaultSearch: JSON.stringify( searchValues ) } ) }
				>
					Set Defaults
				</Button>
			</div>
		}
		<div className = { 
				'advanced-search-form-wrapper'
				+ ( fixSearch ? ' search-hidden' : ' search-visible' )
		}>
			{ ( inEditor || showObjectType ) &&
				<div className = { 'advanced-search-object-type' 
					+ ( showObjectType ? ' search-visible' : ' search-hidden' )
				}>
					<SelectControl
						className = 'advanced-search-object-type-select'
						label     = 'Object Type'
						value     = { selectedKind }
						onChange  = { val => updateSearchValues( { selectedKind: val } ) }
						options   = { kindOptions() }
					/>
				</div>
			}
			<div className = 'advanced-search-main-input'>
				<input
					type     = 'text'
					value    = { searchText || '' }
					onChange = { event => updateSearchValues( { searchText: event.target.value } ) }
				/>
				<Button
					isPrimary
					className = 'advanced-search-search-button'
					onClick   = { () => onSearch( searchValues ) }
				>
					Search
				</Button>
			</div>
			<div className = 'advanced-search-toggles'>
				{ ( inEditor || showTitleToggle ) &&
					<CheckboxControl
						className = { 'advanced-search-title-toggle-checkbox'
							+ ( showTitleToggle ? ' search-visible' : ' search-hidden' )
						}
						label = 'Only search titles'
						checked = { !! onlyTitle }
						onChange = { val => updateSearchValues( { onlyTitle: val } ) }
					/>
				}
			</div>
			<div className = 'advanced-search-select-controls'>
				{ ( inEditor || showFlags ) &&
					<SelectControl
						className = { 'advanced-search-flags-select'
							+ ( showFlags ? ' search-visible' : ' search-hidden' )
						}
						multiple
						label = 'Flags'
						value = { selectedFlags }
						onChange = { val => updateSearchValues( { selectedFlags: val } ) }
						options = { flagOptions() }
					/>
				}
				{ ( inEditor || showCollections ) &&
					<SelectControl
						className = { 'advanced-search-collections-select'
							+ ( showCollections ? ' search-visible' : ' search-hidden' )
						}
						multiple
						label = 'Within Collections'
						value = { selectedCollections }
						onChange = { val => updateSearchValues( { selectedCollections: val } ) }
						options = { collectionOptions() }
					/>
				}
			</div>
			{ ( inEditor || showFields ) &&
				<div className = { 'advanced-search-fields'
					+ ( showFields ? ' search-visible' : ' search-hidden' )
				} >
					<div className='components-base-control'>
						<label className='components-base-control__label'>Search in fields</label>
					</div>
					{ searchFieldElements }
				</div>
			}
		</div>
		</>
	);
}

export default AdvancedSearchUI;