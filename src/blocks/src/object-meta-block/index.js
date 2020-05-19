/**
 * A block for Museum Object fields.
 *
 * @see blocks/customposttype-block.php for attributes.
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

registerBlockType( 'wp-museum/object-meta-block', {
	title : __( 'Object Fields' ),
	icon : 'archive',
	category : 'wp-museum',
	edit,
	save : () => null
} );
