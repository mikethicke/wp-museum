import {
	useState,
	useEffect
} from '@wordpress/element';

import { 
	useSelect,
	useDispatch
} from '@wordpress/data';

import apiFetch from '@wordpress/api-fetch';
import ChildKind from './child-kind';

const ChildObjectsEdit = props => {
	const { attributes, setAttributes } = props;
	const { childObjects } = attributes;

	const [ kindData, setKindData ] = useState( null );
	const [ childObjectData, setChildObjectData] = useState( null );
	const [ wasSaving, setWasSaving ] = useState( false );

	const baseRestPath = '/wp-museum/v1';
	const wordpressRestPath = '/wp/v2';

	const { postType, postId, isSavingPost, currentPostStatus } = useSelect( 
		( select ) => {
			const {
				getCurrentPostType,
				getCurrentPostId,
				isSavingPost,
				getEditedPostAttribute,
			} = select( 'core/editor' );
			return {
				postType          : getCurrentPostType(),
				postId            : getCurrentPostId(),
				isSavingPost      : isSavingPost(),
				currentPostStatus : getEditedPostAttribute( 'status' )
			}
		},
		[]
	);

	const { savePost } = useDispatch( 'core/editor' );

	useEffect( () => {
		if ( kindData === null ) {
			refreshKindData();
		}
	} );

	useEffect( () => {
		if ( childObjectData === null ) {
			refreshChildObjectData();
		}
	} );

	useEffect( () => {
		if ( childObjects ) {
			savePost();
		}
	}, [ childObjects ] );

	useEffect( () => {
		if ( isSavingPost && ! wasSaving ) {
			setWasSaving( true );
		} else if ( ! isSavingPost && wasSaving ) {
			setWasSaving( false );
			refreshChildObjectData();
		}
	} );

	const refreshKindData = () => {
		apiFetch( { path: `${baseRestPath}/mobject_kinds/${postType}`} ).then( setKindData );
	}

	const refreshChildObjectData = () => {
		apiFetch( { path: `${baseRestPath}/all/${postId}/children` } ).then( setChildObjectData );
	}

	const addChildObject = ( child, kind ) => {
		const {
			kind_id
		} = kind;

		const updatedChildObjectData = childObjectData ? Object.assign( {}, childObjectData ) : {};
		if ( typeof updatedChildObjectData[ kind_id ] === 'undefined' ) {
			updatedChildObjectData[ kind_id ] = [];
		}
		updatedChildObjectData[ kind_id ].push( child );
		setChildObjectData( updatedChildObjectData );

		const updatedChildObjects = childObjects ? Object.assign( {}, childObjects ) : {};
		if ( typeof updatedChildObjects[ kind_id ] === 'undefined' ) {
			updatedChildObjects[ kind_id ] = [];
		}
		updatedChildObjects[ kind_id ].push( child.id );
		setAttributes( {
			childObjects : updatedChildObjects,
			childObjectsStr : JSON.stringify( updatedChildObjectData )
		} );
	}

	const deleteChildObject = ( child, kind ) => {
		if ( ! childObjects ) return;
		const updatedChildObjects = Object.assign( {}, childObjects );
		if ( typeof updatedChildObjects[ kind.kind_id ] === 'undefined' ) {
			return;
		}
		const index = updatedChildObjects[ kind.kind_id ].findIndex( object => object.id === child.id );
		if ( index === -1 ) return;
		updatedChildObjects[ kind.kind_id ].splice( index, 1 );
		setAttributes( {
			childObjects : updatedChildObjects,
			childObjectsStr : JSON.stringify( updatedChildObjects )
		} );
		apiFetch( {
			path    :  `${wordpressRestPath}/${kind.type_name}/${child.ID}`,
			method  : 'DELETE'
		} ).then( result => console.log(result) );
	}

	const updateChildObject = ( child, kind, data ) => {
		apiFetch( {
			path   : `${wordpressRestPath}/${kind.type_name}/${child.ID}`,
			method : 'POST',
			data   : data
		} ).then( result => console.log( result ) );
	}

	const newChildObject = ( kind ) => {
		const {
			type_name,
			label,
			block_template
		} = kind;

		let postContent = '';
		if ( block_template ) {
			block_template.forEach( templateItem => {
				postContent += '<!-- wp:' + templateItem[0];
				if ( templateItem.length > 1 ) {
					postContent += ' {'
					Object.entries( templateItem[1] ).forEach( ( [ key, value ] ) => {
						postContent += `"${key}": "${value}", `
					} );
					postContent = postContent.slice(0, -2 );
					postContent += '}';
				}
				postContent += " /-->\n\n"
			} );
		}

		apiFetch( {
			path: `${wordpressRestPath}/${type_name}/`,
			method: 'POST',
			data: {
				'title'   : label,
				'status'  : currentPostStatus,
				'meta'    : { 'wpm_parent_object': postId },
				'content' : postContent
			}
		} ).then( result => {
			addChildObject( result, kind );
		} );
	}

	const kindSections = kindData ? kindData.children.map( kind => (
		<ChildKind
			key               = { kind.kind_id }
			kind              = { kind }
			kindObjects       = { childObjectData && childObjectData[ kind.kind_id ] ?
				childObjectData[ kind.kind_id] : 
				[]
			}
			newChildObject    = { newChildObject }
			deleteChildObject = { deleteChildObject }
			updateChildObject = { updateChildObject }
		/>
	) ) : [];
	
	return (
		<div className = 'child-objects-block'>
			{ kindSections }
		</div>
	)

}

export default ChildObjectsEdit;