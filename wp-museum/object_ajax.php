<?php

/*
 * Ajax functions that add functionality to edit screens for object post types.
 */


/**
 * Adds javascript to upload image attachments to object posts.
 */
function wpm_media_box_enqueue()  {
    wp_enqueue_media();
    wp_enqueue_script('media-upload');
    wp_enqueue_script( 'fancybox-jq', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js', ['jquery'] );
}
add_action( 'admin_enqueue_scripts', 'wpm_media_box_enqueue' );

/**
 * Creates a new child post of current object and loads edit screen for that object.
 */
function new_object_js() {
    ?>
    <script type="text/javascript">
        function new_obj(parent) {
            var data = {
                'action'    : 'create_new_obj',
                'parent'    : parent
            };
            
            jQuery.post(ajaxurl, data, function(response) {
                window.location.href = "post.php?post=" + response +"&action=edit";
            });
        }
    </script>     
    <?php
}
add_action( 'admin_footer', 'new_object_js' );

/**
 * Creates a new post with same type as current post and sets current post as its parent.
 * Called via ajax.
 */
function create_new_obj() {
    $parent_ID = intval( $_POST['parent'] );
    $parent_post = get_post( $parent_ID );
    $categories = wp_get_post_categories( $parent_ID );
    $args = [
        'post_title'        => '',
        'post_content'      => '',
        'post_type'         => $parent_post->post_type,
        'post_parent'       => $parent_ID,
        'post_category'     => $categories
    ];
    $post_id = wp_insert_post( $args );
    echo $post_id;
    wp_die();
}
add_action( 'wp_ajax_create_new_obj', 'create_new_obj');

/**
 * Removes an image attachment from an object.
 * Triggered by user clicking "x" on an image.
 *
 * @see remove_image_attachment_aj()
 */
function remove_image_attachment_js() {
    ?>
    <script type="text/javascript">
        function remove_image_attachment( image_id, post_id ) {
            var data = {
                'action'    : 'remove_image_attachment_aj',
                'post_id'   : post_id,
                'image_id'  : image_id
            };
            
            jQuery.post( ajaxurl, data, function( response ) {
                oib = document.getElementById('object-image-box');
                oib.innerHTML = response;
            });
        }
    </script>
    <?php
}
add_action( 'admin_footer', 'remove_image_attachment_js' );

/**
 * Remove an image attachment from an object.
 *
 * @see remove_image_attachment_js()
 */
function remove_image_attachment_aj() {
    $image_id = intval( $_POST['image_id'] );
    $post_id = intval( $_POST['post_id'] );
    wp_delete_attachment( $image_id );
    object_image_box_contents( $post_id );
    wp_die();
}
add_action( 'wp_ajax_remove_image_attachment_aj', 'remove_image_attachment_aj');

/**
 * Refreshes the image gallery for object posts when new images are uploaded.
 */
function refresh_image_box_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready( function() {
            wp.Uploader.queue.on('reset', function() { 
                var data = {
                    'action'    : 'refresh_image_box_on_upload_aj',
                    'post_id'   : <?php global $post; echo $post->ID; ?>
                };
                jQuery.post( ajaxurl, data, function( response ) {
                    oib = document.getElementById('object-image-box');
                    oib.innerHTML = response;
                });
            });
        });
    </script>
    <?php
}
add_action( 'admin_footer', 'refresh_image_box_js' );

/**
 * Refreshes the image gallery for object posts when new images are uploaded.
 *
 * @see refresh_image_box_js()
 */
function refresh_image_box_on_upload_aj() {
    $post_id = intval( $_POST['post_id'] );
    object_image_box_contents( $post_id );
    wp_die();
}
add_action( 'wp_ajax_refresh_image_box_on_upload_aj', 'refresh_image_box_on_upload_aj' );

/**
 * Moves an image attachment for object post types when the left or right arrows are clicked.
 *
 * @see swap_image_order_aj()
 */
function wpm_image_move_js() {
    ?>
    <script type='text/javascript'>
    function wpm_image_move(image_id, direction) {
        div = document.getElementById("image-div-" + image_id);
        gallery_div = document.getElementById("admin-object-gallery");
        gallery_children = gallery_div.children;
        swapped = false;
        
        for ( i = 0; i < gallery_children.length; i++ ) {
            if ( gallery_children[i].id == "image-div-" + image_id ) {
                if ( direction == 1 && i < gallery_children.length - 1 ) {
                    swap_div = gallery_children[i + 1];
                    gallery_children[i].parentNode.insertBefore( gallery_children[i].parentNode.removeChild(swap_div), gallery_children[i] );
                    swapped = true;
                    break;
                }
                else if ( direction == -1 && i > 0 ) {
                    swap_div = gallery_children[i - 1];
                    gallery_children[i].parentNode.insertBefore( gallery_children[i].parentNode.removeChild(gallery_children[i]), swap_div );
                    swapped = true;
                    break;
                }
            }
        }
        
        if ( swapped ) {
             var data = {
                'action'    : 'swap_image_order_aj',
                'first_image_id'   : swap_div.id,
                'second_image_id'  : image_id
            };
            
            jQuery.post( ajaxurl, data, function( response ) {
                //pass
            });
        }
        
        
    }
    </script>
    <?php
}
add_action( 'admin_footer', 'wpm_image_move_js' );

/**
 * Moves an image attachment for object post types when the left or right arrows are clicked.
 *
 * @see wpm_image_move_js()
 */
function swap_image_order_aj() {
    $first_image_id = $_POST['first_image_id'];
    $first_image_id = intval( substr( $first_image_id, strlen("image-div-") ) );
    $second_image_id = intval( $_POST['second_image_id'] );
    $first_image_post = get_post( $first_image_id );
    $second_image_post = get_post( $second_image_id );
    $first_image_menu_order = $first_image_post->menu_order;
    $first_image_post->menu_order = $second_image_post->menu_order;
    $second_image_post->menu_order = $first_image_menu_order;
    wp_update_post( $first_image_post );
    wp_update_post( $second_image_post );
    wp_die();
}
add_action( 'wp_ajax_swap_image_order_aj', 'swap_image_order_aj');

?>