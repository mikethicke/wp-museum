/**
 * A block showing a list of collections in the main post area.
 * 
 * This block is dynamic, so attributes are defined server-side.
 * @see src/blocks/collection-main-navigation-block.php
 *
 * Attributes:
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
import { museum } from '../../icons';

registerBlockType( 'wp-museum/collection-main-navigation-block', {
	title    : __( 'Collection Main Navigation' ),
	icon     : museum,
	category : 'wp-museum',
	edit,
	save      : () => null
} );