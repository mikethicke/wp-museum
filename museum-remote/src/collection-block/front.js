import {
	useState
} from '@wordpress/element';

import RemoteCollectionGrid from './remote-collection-grid';

const CollectionBlockFront = props => {
	const { attributes } = props;

	const wpmRestBase = '/wp-json/wp-museum/v1';
	const [ remoteData, setRemoteData ] = useState( {} );

	return (
		<RemoteCollectionGrid
			attributes    = { attributes }
			remoteData    = { remoteData }
			setRemoteData = { setRemoteData }
			wpmRestBase   = { wpmRestBase }
		/>
	);
}

export default CollectionBlockFront;