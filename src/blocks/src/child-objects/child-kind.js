import ChildObject from './child-object';
import { Button } from '@wordpress/components';

const ChildKind = props => {
	const {
		kind,
		kindObjects,
		newChildObject,
		deleteChildObject,
		updateChildObject
	} = props;

	const {
		label_plural,
		label,
	} = kind;

	const updateTitle = ( child, newTitle ) => {
		updateChildObject( child, kind, { title: newTitle } );
	}

	const childElements = kindObjects ? kindObjects.map( ( childObject, index ) => (
		<ChildObject
			key               = { index }
			objectData        = { childObject }
			deleteChildObject = { ( child ) => deleteChildObject( child, kind ) }
			updateTitle       = { updateTitle }
		/>
	) ) : [];

	return (
		<div className = 'child-kind'>
			<h2>{ label_plural }</h2>
			<Button
				className = 'new-child-object'
				onClick   = { () => newChildObject( kind ) }
				isLarge
				isPrimary
			>
				New { label }
			</Button>
			{ !! childElements && childElements }
		</div>
	);
}

export default ChildKind;