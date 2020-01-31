/**
 * Modal dialog box allowing user to search for a museum object post.
 */

import { Button, Modal } from '@wordpress/components';
import { useState } from '@wordpress/element';

const ObjectSearchButton = (props) => {
	const {children } = props;
	
	const [ isOpen, setOpen ] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );
 
    return [
        <>
            <Button isSecondary onClick={ openModal }>{ children }</Button>
            { isOpen && (
                <Modal
                    title="This is my modal"
                    onRequestClose={ closeModal }>
                    <Button isSecondary onClick={ closeModal }>
                        My custom close button
                    </Button>
                </Modal>
            ) }
        </>
	]
}

export default ObjectSearchButton;
