/**
 * A block for Museum Object image attachments.
 */

 /**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import edit from './edit';
import { museum } from '../icons';

registerBlockType( 'wp-museum/object-image-attachments-block', {
	title : __( 'Object Image Attachments' ),
	icon : museum,
	category : 'wp-museum',
	edit,
	save : () => null
} );

