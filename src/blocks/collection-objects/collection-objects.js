import apiFetch from '@wordpress/api-fetch';

import {
	useState,
	useEffect
} from '@wordpress/element';

import { 
	useSelect
} from '@wordpress/data';

import {
	registerPlugin,
	getPlugin
} from '@wordpress/plugins';

import { ObjectEditorTable } from '../../components';
import CollectionSettingsPanel from './collection-options';

const CollectionObjects = () => {
	const postID = useSelect (
		select => select( 'core/editor' ).getCurrentPostId(),
		[]
	);

	const isSavingPost = useSelect( select => select( 'core/editor').isSavingPost() );

	const [ associatedObjects, setAssociatedObjects ] = useState( [] );

	const baseRestPath = '/wp-museum/v1';

	const getAssociatedObjects = () => {
		setAssociatedObjects( [] );
		apiFetch( { path: `${baseRestPath}/collections/${postID}/objects`}).then(
			results => {
				setAssociatedObjects( results );
			}
		);
	}

	useEffect( () => {
		if ( ! isSavingPost ) {
			getAssociatedObjects();
		}
	}, [ isSavingPost ] );

	if ( typeof getPlugin( 'wpm-collection-settings-panel' ) === 'undefined' ) {
		registerPlugin( 'wpm-collection-settings-panel', {
			render: () => <CollectionSettingsPanel />
		} );
	}

	return (
		<div>
			<h2>Associated Objects</h2>
			{ associatedObjects.length > 0 ?
				<ObjectEditorTable
					mObjects = { associatedObjects }
				/>
				:
				<em>No objects found.</em>
			}
		</div>
	);
}

export default CollectionObjects;