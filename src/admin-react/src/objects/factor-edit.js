import {
	Modal,
	Button,
	SVG,
	Path,
	Icon
} from '@wordpress/components';

import {
	useState,
	useRef,
	useEffect
} from '@wordpress/element';

const closeIcon = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
		<Path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z" />
	</SVG>
);

/**
 * Delete Icon from Darius Dan
 * @link https://www.flaticon.com/authors/darius-dan
 */
const deleteIcon = (
	<SVG
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 512 512"
    >
      <Path d="M256 512C114.84 512 0 397.16 0 256S114.84 0 256 0s256 114.84 256 256-114.84 256-256 256zm0-475.43C135.008 36.57 36.57 135.008 36.57 256S135.008 475.43 256 475.43 475.43 376.992 475.43 256 376.992 36.57 256 36.57zm0 0"></Path>
      <Path d="M347.43 365.715c-4.68 0-9.36-1.785-12.93-5.36L151.645 177.5c-7.145-7.145-7.145-18.715 0-25.855 7.14-7.141 18.714-7.145 25.855 0L360.355 334.5c7.145 7.145 7.145 18.715 0 25.855a18.207 18.207 0 01-12.925 5.36zm0 0"></Path>
      <Path d="M164.57 365.715c-4.68 0-9.355-1.785-12.925-5.36-7.145-7.14-7.145-18.714 0-25.855L334.5 151.645c7.145-7.145 18.715-7.145 25.855 0 7.141 7.14 7.145 18.714 0 25.855L177.5 360.355a18.216 18.216 0 01-12.93 5.36zm0 0"></Path>
    </SVG>
);

const FactorEditModal = props => {
	const {
		factorData,
		updateFactorData,
		close
	} = props;

	const textInput = useRef( null );

	useEffect( () => textInput.current.focus(), [] );

	const [ currentInputText, updateCurrentInputText ] = useState( '' );

	const updateInputText = ( event ) => {
		updateCurrentInputText( event.target.value );
	}

	const addItem = () => {
		if ( currentInputText && ! factorData.includes( currentInputText ) ) {
			const newFactorData = factorData.concat( [ currentInputText ] );
			updateFactorData( newFactorData );
			updateCurrentInputText( '' );
		}
	}

	const removeItem = factorItem => {
		const itemIndex = factorData.indexOf( factorItem )
		if ( itemIndex != -1 ) {
			const newFactorData = [ ...factorData ];
			newFactorData.splice( itemIndex, 1 );
			updateFactorData( newFactorData );
		}
	}

	const clearItems = () => {
		if ( factorData.length > 0 ) {
			updateFactorData( [] );
		}
	}

	const handleKeyPress = ( event ) => {
		if ( event.key == 'Enter' ) {
			event.stopPropagation();
			addItem();
		}
	}

	const handleListDelete = ( event, factorItem ) => {
		if ( event.key == 'Delete' || event.key == 'Backspace' ) {
			event.stopPropagation();
			removeItem( factorItem );
		}
	}

	const factorListItems = factorData.map( ( factorItem, index ) => 
		<div
			key = { index }
			tabIndex = '0'
			className = 'factor-list-item'
			onKeyDown = { event => handleListDelete( event, factorItem ) }
		>
			<div
				className = 'remove-item-div'
				onClick = { () => removeItem( factorItem ) }
			>
				<Icon
					icon = { deleteIcon }
					size = '0.8em'
				/>
			</div>
			{ factorItem }
		</div>
	);

	return (
		<Modal
			className = 'factor-edit-modal'
			title = 'Edit Factors'
			onRequestClose = { close }
		>
			<div className = 'factor-edit-input'>
				<input
					type = 'text'
					onKeyPress = { handleKeyPress }
					placeholder = 'Type to add factors...'
					value = { currentInputText }
					onChange = { updateInputText }
					ref = { textInput }
				/>
				<Button
					className = 'factor-button add'
					tabIndex = '-1'
					title = 'Add'
					onClick = { addItem }
					isLarge
					isPrimary
				>
					Add
				</Button>
			</div>
			<div className = 'factor-list'>
				<div>
					{ factorListItems }
				</div>
			</div>
			<div className = 'bottom-buttons'>
				<Button
					className = 'factor-button clear'
					title = 'Clear factors'
					onClick = { clearItems }
					isLarge
					isSecondary
				>
					Clear
				</Button>
				<Button
					className = 'factor-button close'
					title = 'Save and close'
					onClick = { close }
					isLarge
					isSecondary
				>
					Close
				</Button>
			</div>
		</Modal>

	);
}

export default FactorEditModal;