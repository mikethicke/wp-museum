<?php
/**
 * Defines capabilities for exhibits, exhibits, and collections.
 */

 /**
  * Callback to add capabilities for custom post types.
  */
 function add_museum_capabilities() {
    $editor_role = get_role( 'editor' );
    $editor_role->add_cap( 'edit_objects' );
    $editor_role->add_cap( 'publish_objects' );
    $editor_role->add_cap( 'delete_objects' );
    $editor_role->add_cap( 'edit_others_objects' );
    $editor_role->add_cap( 'edit_published_objects' );
    $editor_role->add_cap( 'read_private_objects' );
    $editor_role->add_cap( 'edit_collections' );
    $editor_role->add_cap( 'publish_collections' );
    $editor_role->add_cap( 'delete_collections' );
    $editor_role->add_cap( 'edit_others_collections' );
    $editor_role->add_cap( 'edit_published_collections' );
    $editor_role->add_cap( 'read_private_collections' );
    $editor_role->add_cap( 'edit_exhibits' );
    $editor_role->add_cap( 'publish_exhibits' );
    $editor_role->add_cap( 'delete_exhibits' );
    $editor_role->add_cap( 'edit_others_exhibits' );
    $editor_role->add_cap( 'edit_published_exhibits' );
    $editor_role->add_cap( 'read_private_exhibits' );
    
    $author_role = get_role( 'author' );
    $author_role->add_cap( 'edit_objects' );
    $author_role->add_cap( 'edit_others_objects' );
    $author_role->add_cap( 'edit_published_objects' );
    $author_role->add_cap( 'edit_collections' );
    $author_role->add_cap( 'edit_others_collections' );
    $author_role->add_cap( 'edit_published_collections' );
    $author_role->add_cap( 'edit_exhibits' );
    $author_role->add_cap( 'edit_others_exhibits' );
    $author_role->add_cap( 'edit_published_exhibits' );
 }
 add_action( 'admin_init', 'add_museum_capabilities' );
 
//Should have function here to remove capabilities upon plugin deactivation
 
 

?>