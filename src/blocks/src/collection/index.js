/**
 * WordPress dependencies
 */
import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";

/**
 * Internal dependencies
 */
import CollectionObjects from './collection-objects';
import { museum } from '../icons';

registerBlockType( 'wp-museum/collection-objects', {
	title    : __( 'Collection Objects' ),
	icon     : museum,
	category : 'wp-museum',
	edit     : CollectionObjects,
	save     : () => null
} );