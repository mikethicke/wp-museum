import apiFetch from '@wordpress/api-fetch';
import {
	useState,
	useEffect,
} from '@wordpress/element';
import {
	Button
} from '@wordpress/components';

import Edit from './edit';

const PageSelector = () => {
	const [ selectedPage, setSelectedPage ] = useState( {
		page: 'main',
		props: {}
	} );

	switch ( selectedPage.page ) {
		case 'main':
			return ( 
				<Main { ...selectedPage.props } 
					setSelectedPage = { setSelectedPage }	
				/>
			);
		case 'edit':
			return ( <Edit { ...selectedPage.props } 
				setSelectedPage = { setSelectedPage }	
			/> );
	}
}

const Main = ( props ) => {
	const { setSelectedPage } = props;
	const baseRestPath = '/wp-museum/v1';
	const [ objectKinds, setObjectKinds ] = useState( null );

	useEffect( () => {
		if ( ! objectKinds ) {
			apiFetch( { path: `${baseRestPath}/mobject_kinds` } ).then( setObjectKinds );
		}
	} );

	const editKind = ( kindItem ) => setSelectedPage( {
		page: 'edit',
		props: { kind: kindItem }
	} );

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

export { PageSelector as ObjectPage };