import {
	useState,
	useEffect,
} from '@wordpress/element';

import ObjectModal from '../components/object-modal';
import { isEmpty } from '../util';

const RemoteObjectModal = props => {
	const {
		remoteData,
		wpmRestBase,
		objectID,
		modalOpen,
		closeModal
	} = props;

	const [ objectData, setObjectData ] = useState( {} );
	const [ imageData, setImageData ] = useState( {} );

	useEffect( () => {
		if ( objectID ) {
			fetchObjectData();
			fetchImageData();
		}
	}, [ objectID ] );

	const doClose = () => {
		setImageData( {} );
		closeModal();
	}

	const fetchObjectData = () => {
		fetch( `${remoteData.url}${wpmRestBase}/all/${objectID}?uuid=${remoteData.uuid}` )
			.then( response => {
				if ( ! response.ok ) {
					console.log( response.statusText );
					return;
				}
				response.json().then( result => setObjectData( result ) );
			} );
	}

	const fetchImageData = () => {
		fetch( `${remoteData.url}${wpmRestBase}/all/${objectID}/images?uuid=${remoteData.uuid}` )
			.then( response => {
				if ( ! response.ok ) {
					console.log( response.statusText );
					return;
				}
				response.json().then( result => setImageData( result ) );
			} );
	}

	if ( isEmpty( objectData ) || isEmpty( imageData ) ) {
		return null;
	}

	const {
		post_title,
		excerpt,
		link
	} = objectData;

	return (
		<>
		{ modalOpen &&
			<ObjectModal
				title    = { post_title }
				content  = { excerpt }
				url      = { link }
				linkText = { `View the full entry at ${remoteData.host_title}` }
				images   = { Object.values( imageData ) }
				close    = { doClose }
			/>
		}
		</>
	);
}

export default RemoteObjectModal;