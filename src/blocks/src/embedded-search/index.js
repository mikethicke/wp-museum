/**
 * An embedded search block that redirects to a search page on submit.
 *
 * This block is dynamic, so attributes are defined server-side.
 * @see src/blocks/embedded-search-block.php
 *
 * Attributes:
 *  - searchPageURL {string} URL of search page.
 */

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { museum } from '../icons';

/**
 * Internal dependencies
 */
import edit from './edit';

registerBlockType( 'wp-museum/embedded-search', {
	title    : __( 'Embedded Search' ),
	icon     : museum,
	category : 'wp-museum',
	edit,
	save     : () => null,
} );