import apiFetch from '@wordpress/api-fetch';
import {
	useState,
	useEffect,
} from '@wordpress/element';
import {
	Button
} from '@wordpress/components';

import Edit from './edit';

const ObjectAdminControl = () => {
	const [ selectedPage, setSelectedPage ] = useState( 'main' );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ kindItem, setKindItem ] = useState( null );
	const [ newKindCount, updateNewKindCount ] = useState( 1 );

	const baseRestPath = '/wp-museum/v1';
	const [ objectKinds, updateObjectKinds ] = useState( null );
	const [ kindIds, setKindIds ] = useState( null );

	useEffect( () => {
		if ( ! objectKinds ) {
			refreshKindData();
		}
	} );

	useEffect( () => maybeSaveKindData(), [ objectKinds ] );

	const refreshKindData = () => {
		apiFetch( { path: `${baseRestPath}/mobject_kinds` } )
			.then( ( result ) => {
				if ( ! objectKinds || JSON.stringify( result ) != JSON.stringify( objectKinds ) ) {
					setObjectKinds( result );
				}
			} );
	}

	const setObjectKinds = ( newKindArray ) => {
		updateObjectKinds( newKindArray );
		if ( ! kindItem || ! newKindArray ) return;
		const kindItemIndex = newKindArray.findIndex( item => item.kind_id == kindItem.kind_id );
		if ( kindItemIndex === -1 ) {
			setKindItem( null );
		} else {
			setKindItem( newKindArray[ kindItemIndex ] );
		}
	}

	const updateKind = ( kindId, field, event ) => {
		const kindIndex = objectKinds.findIndex( kindItem => kindItem.kind_id == kindId );
		if ( kindIndex === -1 ) return;
		
		const newKindArray = objectKinds.concat([]);
		if ( event.target.type === 'checkbox' ) {
			if ( objectKinds[ kindIndex ][ field ] != event.target.checked ) {
				newKindArray[ kindIndex ][ field ] = event.target.checked;
				setObjectKinds( newKindArray );
			}
			return;
		}

		if ( objectKinds[ kindIndex ][ field ] != event.target.value ) {
			newKindArray[ kindIndex ][ field ] = event.target.value;
			setObjectKinds( newKindArray );
		}
	}

	const defaultKind = {
		kind_id             : 0 - newKindCount,
		cat_field_id        : null,
		name                : null,
		type_name           : null,
		label               : 'New Object Type',
		label_plural        : null,
		description         : null,
		categorized         : false,
		hierarchical        : false,
		must_featured_image : false,
		must_gallery        : false,
		strict_checking     : false,
		exclude_from_search : false,
		parent_kind_id      : null
	}

	const newKind = () => {
		const newKind = Object.assign( {}, defaultKind );
		const newObjectKinds = objectKinds.concat( [ newKind ] );
		setObjectKinds( newObjectKinds );
		saveKindData();
	}

	const saveKindData = () => {
		setIsSaving( true );
		apiFetch( {
			path   : `${baseRestPath}/mobject_kinds`,
			method : 'POST',
			data   : objectKinds
		} ).then( () => {
			refreshKindData();
			setIsSaving( false );
		} );
	}

	const maybeSaveKindData = () => {
		const currentIds = objectKinds ? JSON.stringify( objectKinds.map( kindItem => kindItem.kind_id ) ) : null;
		if ( ! kindIds || kindIds !=  currentIds ) {
			setKindIds( currentIds );
			saveKindData();
		}
	}

	const deleteKind = ( kindItem ) => {
		let confirmDelete = confirm( 'Really delete kind? Objects associated with this kind will remain in database but will be inaccessible.' );
		if ( confirmDelete ) {
			kindItem.delete = true;
			saveKindData();
		}
	}

	const editKind = ( newKindItem ) => {
		setKindItem ( newKindItem )
		setSelectedPage( 'edit' );
	}

	switch ( selectedPage ) {
		case 'main':
			return ( 
				<Main
					objectKinds = { objectKinds }
					editKind    = { editKind }
					newKind     = { newKind }
					deleteKind  = { deleteKind }
				/>
			);
		case 'edit':
			if ( kindItem ) {
				return ( <Edit
					kinds           = { objectKinds }
					kindItem        = { kindItem }
					updateKind      = { updateKind }
					saveKindData    = { saveKindData }
					isSaving        = { isSaving }
					setIsSaving     = { setIsSaving }
					setSelectedPage = { setSelectedPage }	
				/> );
			} else {
				return null;
			}
	}
}

const Main = ( props ) => {
	const {
		objectKinds,
		editKind,
		newKind,
		deleteKind,
	} = props;

	if ( objectKinds ) {
		const kindRows = objectKinds
			.filter( kindItem => typeof kindItem.delete === 'undefined' || ! kindItem.delete )
			.map( ( kindItem, index ) => (
				<div
					key = { index }
				>
					<div>{ kindItem.label }</div>
					<div
						className = 'object-action-buttons'
					>
						<Button
							onClick = { () => editKind( kindItem ) }
							isLarge
							isSecondary
						>
							Edit
						</Button>
						<Button
							isLarge
							isSecondary
							onClick = { () => deleteKind( kindItem ) }
						>
							Delete
						</Button>
						<Button
							isLarge
							isSecondary
						>
							Export CSV
						</Button>
						<Button
							isLarge
							isSecondary
						>
							Import CSV
						</Button>
					</div>
				</div>
			) );
		
		
		return( 
			<div className = 'museum-admin-main'>
				<h1>Museum Administration</h1>
				<div>{ kindRows }</div>
				<div>
					<Button
						onClick = { newKind }
						isLarge
						isSecondary
					>
						Add New
					</Button>
				</div>
			</div>
		);
	} else {
		return ( <div></div> );
	}
}

export { ObjectAdminControl as ObjectPage };