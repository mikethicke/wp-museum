<?php
/*
 * Functions for interfacing with object post types.
 *
 * @see object_post_types.php
 */

 /**
  * Creates a wordpress type name from a museum object name.
  *
  * @param string $object_name The object's name as stored in the object database table.
  *
  * @return Wordpress post type name.
  */
 function type_name ( $object_name ) {
    $type_name = WPM_PREFIX . $object_name;
    if ( strlen( $type_name ) > 20 ) $type_name = substr( $type_name, 0, 19 );
    //should do collision checking here.
    return $type_name;
}

/**
 * List of Wordpress type names for all objects in database.
 *
 * @return [string] List of Wordpress custom post type names.
 */
function get_object_type_names() {
    $object_types = get_object_types();
    $type_names = array();
    foreach ( $object_types as $object ) {
        $type_names[] = type_name ( $object->name );
    }
    return $type_names;
}

/**
 * Finds custom post object for a Wordpress custom post type name.
 *
 * @param string $type_name The Wordpress custom post type name.
 *
 * @return CustomPostType The associated custom post type.
 */
function object_from_type( $type_name ) {
    $object_types = get_object_types();
    foreach ( $object_types as $object_type ) {
        if ( type_name( $object_type->name ) == $type_name ) {
            return $object_type;
        }
    }
    return false;
}

/**
 * Finds custom post type object from a particular object post.
 *
 * @param WP_Post $object A Wordpress post with a custom post type.
 *
 * @return CustomPostType The custom post type of that object.
 */
function object_type_from_object ( $object ) {
    $type_name = $object->post_type;
    $object_type = object_from_type ( $type_name );
    return $object_type;
}

/**
 * Displays fancybox thumbnails for all image attachments of a post.
 *
 * @param int $post_it The id of the post.
 */
function object_image_box_contents ( $post_id ) {
    global $post;
    if ( is_null( $post ) ) $post = get_post( $post_id );
    $images = get_attached_media( 'image', $post );
    $prev_menu_order = -1;
    foreach ( $images as $image ) {
        if ( $image->menu_order <= $prev_menu_order ) {
            $image->menu_order = $prev_menu_order + 1;
            wp_update_post ( $image );
        }
        $prev_menu_order = $image->menu_order;
        $image_thumbnail = wp_get_attachment_image_src( $image->ID, 'thumbnail' )[0];
        $image_full = wp_get_attachment_image_src( $image->ID, 'large' )[0];
        echo "<div id='image-div-{$image->ID}' style='display:inline'>";
        echo "<a data-fancybox='fbgallery' href='$image_full'><img src='$image_thumbnail'></a>";
        echo "<a id='delete-{$image->ID}' class='wpm-image-delete' onclick='remove_image_attachment({$image->ID}, $post_id)'>[x]</a>";
        echo "<a id='moveup-{$image->ID}' class='wpm-image-moveup' onclick='wpm_image_move({$image->ID}, -1)'><span class='dashicons dashicons-arrow-left'></span></a>";
        echo "<a id='movedown-{$image->ID}' class='wpm-image-movedown' onclick='wpm_image_move({$image->ID}, +1)'><span class='dashicons dashicons-arrow-right'></span></a>";
        echo "</div>";
    }
}

/**
 * Gets all descendent posts (recursive children) of a post.
 *
 * @param WP_Post   $post           A post
 * @param string    $post_status    The publication status of descendent posts to retrieve
 *
 * @return [WP_Post] Array of descendent posts.
 */
function get_post_descendants ( $post, $post_status='publish' ) {
    $descendants = [];
    $children = get_posts ( [
        'numberposts'   => -1,
        'post_status'   => $post_status,
        'post_type'     => $post->post_type,
        'post_parent'   => $post->ID
    ] );
    foreach ( $children as $child ) {
        $grand_children = get_post_descendants ( $child, $post_status );
        $descendants = array_merge( $descendants, $grand_children);
    }
    $descendants = array_merge( $descendants, $children );
    return $descendants;
}
?>