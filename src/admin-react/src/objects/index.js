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
	const [ selectedPage, setSelectedPage ] = useState( {
		page: 'main',
		props: {}
	} );

	const baseRestPath = '/wp-museum/v1';
	const [ objectKinds, setObjectKinds ] = useState( null );

	useEffect( () => {
		if ( ! objectKinds ) {
			refreshKindData();
		}
	} );

	const refreshKindData = () => {
		apiFetch( { path: `${baseRestPath}/mobject_kinds` } ).then( setObjectKinds );
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

	const saveKindData = () => {
		apiFetch( {
			path   : `${baseRestPath}/mobject_kinds`,
			method : 'POST',
			data   : objectKinds
		} ).then( refreshKindData );
	}

	const editKind = ( kindItem ) => setSelectedPage( {
		page: 'edit',
		props: {
			kinds        : objectKinds,
			kindItem     : kindItem,
			updateKind   : updateKind,
			saveKindData : saveKindData
		}
	} );

	switch ( selectedPage.page ) {
		case 'main':
			return ( 
				<Main { ...selectedPage.props } 
					objectKinds = { objectKinds }
					editKind    = { editKind }	
				/>
			);
		case 'edit':
			return ( <Edit { ...selectedPage.props } 
				setSelectedPage = { setSelectedPage }	
			/> );
	}
}

const Main = ( props ) => {
	const {
		objectKinds,
		editKind
	 } = props;
	

	if ( objectKinds ) {
		const kindRows = objectKinds.map( ( kindItem, index ) => (
			<div
				key = { index }
			>
				<div>{ kindItem.label }</div>
				<div>
					<Button
						onClick = { () => editKind( kindItem ) }
					>
						Edit
					</Button>
					<Button>
						Delete
					</Button>
					<Button>
						Export CSV
					</Button>
					<Button>
						Import CSV
					</Button>
				</div>
			</div>
		) );
		
		
		return( 
			<>
				<div>{ kindRows }</div>
				<div>
					<Button>
						Add New
					</Button>
				</div>
			</>
		);
	} else {
		return ( <div></div> );
	}
}

export { ObjectAdminControl as ObjectPage };