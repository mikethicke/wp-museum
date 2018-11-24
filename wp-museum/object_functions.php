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
 * Save object image gallery array.
 * 
 * @param [Int=>Int] $attached_image_array Associative array of image_id => sort_order.
 * @param Int $post_id The id of post containing the image gallery.
 * 
 * @return Bool True if successful.
 */
function set_object_image_box_attachments ( $attached_image_array, $post_id )  {
    if ( !is_array($attached_image_array) ) return false;
    $attached_images_str = '';
    $existing_sort_orders = array();
    foreach ( $attached_image_array as $image_id => $sort_order  ) {
        if ( $image_id != '' && $image_id != 0 ) {
            if ( $attached_images_str != '' ) $attached_images_str .= ',';
            if ( $sort_order == '' || in_array( $sort_order, $existing_sort_orders ) ) {
                $sort_order = max ( $existing_sort_orders ) + 1;
            }
            $existing_sort_orders[] = $sort_order;
            $attached_images_str .= $image_id . ':' . $sort_order;
        }       
    }
    update_post_meta( $post_id, 'wpm_gallery_attach_ids', $attached_images_str );
    return true;
}

/**
 * Get image gallery array for an object.
 * 
 * @param Int $post_id The id of the object post containing the image gallery.
 * 
 * @return [Int=>Int] An array of image_id => sort_order 
 */
function get_object_image_box_attachments ( $post_id ) {
    $attached_image_array = array();
    $custom = get_post_custom( $post_id );
    if ( !isset($custom['wpm_gallery_attach_ids']) || 
          $custom['wpm_gallery_attach_ids'][0] == '' ) 
      return array();
    $image_pairs_array = explode( ',', $custom['wpm_gallery_attach_ids'][0] );
    $max_order = 0;
    foreach ( $image_pairs_array as $image_pair_str ) {
        $image_pair_arr = explode( ':', $image_pair_str );
        if ( count($image_pair_arr) == 2 ) {
            $attached_image_array[$image_pair_arr[0]] = $image_pair_arr[1];
            if ( $image_pair_arr[1] >= $max_order ) $max_order = $image_pair_arr[1];
        }
        elseif ( count($image_pair_arr) == 1 ) {
            $max_order += 1;
            $attached_image_array[$image_pair_arr[0]] = $max_order;
        }
    }
    return $attached_image_array;
}

/**
 * Resets object image galleries from post attachments.
 * 
 * Resets image gallery arrays for all objects, and then reconstructs them from
 * Wordpress image attachment system and menu order (the old system of object 
 * image galleries). This has the potential to lose data. Only callable by
 * administrator. Called by setting Get parameter wpm_foia.
 * 
 */
function fix_object_image_attachments() {
    if ( !current_user_can( 'manage_options' ) || !isset( $_GET['wpm_foia'] ) ) return;
    $object_types = get_object_type_names();
    foreach ( $object_types as $object_type ) {
        $object_posts = get_posts(array(
            'post_type'     => $object_type,
            'numberposts'   => -1,
            'post_status'   => 'any'
        ));
        foreach ( $object_posts as $object_post ) {
            update_post_meta ( $object_post->ID, 'wpm_gallery_attach_ids', '' );
            $object_image_box_contents = array();
            $existing_post_ids = array();
            $existing_sort_orders = array();
            $attachments = get_posts( array(
                'post_type'     => 'attachment',
                'numberposts'   => -1,
                'post_status'   => 'any',
                'post_parent'   => $object_post->ID
            ));
            foreach ( $attachments as $attachment ) {
                if ( !in_array( $attachment->ID, $existing_post_ids ) ) {
                    if ( !in_array( $attachment->menu_order, $existing_sort_orders ) ) {
                        $sort_order = $attachment->menu_order;
                    }  
                    else {
                        $sort_order = max($existing_sort_orders) + 1;
                    }
                    $existing_sort_orders[] = $sort_order;
                    $existing_post_ids[] = $attachment->ID;
                }
                $object_image_box_contents[$attachment->ID] = $sort_order;
            }
            set_object_image_box_attachments( $object_image_box_contents, $object_post->ID );
        } 
    }
}

/**
 * Displays fancybox thumbnails for all image attachments of a post.
 *
 * @param int $post_id The id of the post.
 */
function object_image_box_contents ( $post_id=null ) {
    global $post;
    if ( is_null( $post_id ) ) {
        if ( is_null ( $post ) ) return false;
        $post_id = $post->ID;
    }
    
    $image_box_contents = get_object_image_box_attachments( $post_id );
    if ( !empty( $image_box_contents ) ) {
        asort ( $image_box_contents );
        foreach ( $image_box_contents as $image_id => $sort_order ) {
            $image_thumbnail = wp_get_attachment_image_src( $image_id, 'thumbnail' );
            $image_full = wp_get_attachment_image_src( $image_id, 'large' );
            echo "<div id='image-div-{$image_id}' class='inline-image-box'>";
            echo "<a data-fancybox='fbgallery' href='$image_full[0]'><img src='$image_thumbnail[0]' width='$image_thumbnail[1]' height='$image_thumbnail[2]'></a>";
            echo "<a id='delete-{$image_id}' class='wpm-image-delete' onclick='remove_image_attachment({$image_id}, $post_id)'>[x]</a>";
            echo "<a id='moveup-{$image_id}' class='wpm-image-moveup' onclick='wpm_image_move({$image_id}, -1)'><span class='dashicons dashicons-arrow-left'></span></a>";
            echo "<a id='movedown-{$image_id}' class='wpm-image-movedown' onclick='wpm_image_move({$image_id}, +1)'><span class='dashicons dashicons-arrow-right'></span></a>";
            echo "</div>";
        }
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

function check_object_post ( $post_id=null ) {
    global $post;
    if ( $post_id == null ) {
        if ( $post == null ) return false;
        else $post_id = $post->ID;
    }
    else {
        $post = get_post ( $post_id );
    }

    $problems = array();

    $custom = get_post_custom( $post_id );
    $object = object_from_type( $post->post_type );
    $fields = get_object_fields ( $object->object_id );

    foreach ( $fields as $field ) {
        if ( $field->required == 1 && empty( $custom[$field->slug][0] ) ) {
            $problems[] = "{$field->name} is required but empty.";
        }
        if ( !empty( $field->field_schema ) && !empty( $custom[$field->slug][0] ) ) {
            $pattern = '/^' . stripslashes( $field->field_schema ) . '$/';
            if ( !preg_match( $pattern, $custom[$field->slug][0], $matches ) ) {
                $problems[] = esc_html( "{$field->name} does not conform to required schema: {$pattern}." );
            }
        }
        if ( !empty( $custom[$field->slug] ) && $field->field_id == $object->cat_field_id ) {
            $args = [
                'post_type'     => $post->post_type,
                'numberposts'   => -1,
                'post_status'   => 'any',
                'meta_key'   => $field->slug,
                'meta_value' => $custom[$field->slug][0]
            ];
            $matching_posts = get_posts( $args );
            foreach ( $matching_posts as $match ) {
                if ( $match->ID != $post->ID ) {
                    $problems[] = "{$field->name} must be unique, but is already possessed by <a href='post.php?post={$match->ID}&action=edit'>{$match->post_title}</a>.";
                }
            }
        }
    }
    if ( $object->categorized ) {
        $post_category = get_the_category( $post->ID );
        if ( count ($post_category) == 0 || 
                (   count ( $post_category ) == 1  && 
                    $post_category[0]->name == "Uncategorized" 
                ) 
            ) {
                $problems[] = "Post must be categorized.";
            }    
    }
    if ( $object->must_featured_image ) {
        $thumb = get_the_post_thumbnail( $post );
        if ( empty( $thumb ) ) $problems[] = "Post must have featured image.";
    }
    if ( $object->must_gallery ) {
        if ( !isset( $custom[WPM_PREFIX . 'gallery_attach_ids'][0] ) ||
         empty ($custom[WPM_PREFIX . 'gallery_attach_ids'][0] ) ) {
             $problems[] = "Post must have image gallery.";
         }
    }
    return $problems;
}