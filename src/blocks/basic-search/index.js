/**
 * A block for running basic searches of museum objects.
 *
 * This block is dynamic, so attributes are defined server-side.
 * @see src/blocks/basic-search-block.php
 *
 * Attributes:
 * 	- searchText         {string} Initial search text.
 *  - resultsPerPage     {number} Number of results per page to show.
 *  - advancedSearchLink {string} URL of advanced search page.
 */

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { museum } from '../../icons';

/**
 * Internal dependencies
 */
import edit from './edit';

registerBlockType( 'wp-museum/basic-search', {
	title    : __( 'Basic Search' ),
	icon     : museum,
	category : 'wp-museum',
	edit,
	save     : () => null,
} );