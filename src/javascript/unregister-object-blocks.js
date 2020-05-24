/**
 * Unregisters blocks for non Museum Object post types.
 */

wp.domReady( function() {
	wp.blocks.unregisterBlockType( 'wp-museum/object-image-attachments-block' );
	wp.blocks.unregisterBlockType( 'wp-museum/object-meta-block' );
} );