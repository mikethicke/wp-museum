<?php

/*
 * Ajax functions that add functionality to edit screens for object post types.
 */

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
 * Adds javascript to upload image attachments to object posts.
 */
function wpm_media_box_enqueue()  {
    wp_enqueue_media();
    wp_enqueue_script('media-upload');
    wp_enqueue_script( 'fancybox-jq', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js', ['jquery'] );
}
add_action( 'admin_enqueue_scripts', 'wpm_media_box_enqueue' );

/**
 * Remove an image attachment from an object.
 *
 * @see remove_image_attachment_js()
 */
function remove_image_attachment_aj() {
    $image_id = intval( $_POST['image_id'] );
    $post_id = intval( $_POST['post_id'] );
    $image_attachments = get_object_image_box_attachments( $post_id );
    unset( $image_attachments[$image_id] );
    set_object_image_box_attachments( $image_attachments, $post_id );
    object_image_box_contents( $post_id );
    wp_die();
}
add_action( 'wp_ajax_remove_image_attachment_aj', 'remove_image_attachment_aj');

/**
 * Moves an image attachment for object post types when the left or right arrows are clicked.
 *
 * @see wpm_image_move_js()
 */
function swap_image_order_aj() {
    $attached_images = get_object_image_box_attachments( $_POST['post_id'] );
    $first_image_id = intval( substr( $_POST['first_image_id'], strlen("image-div-") ) );
    $second_image_id = intval( $_POST['second_image_id'] );
    $first_image_order = $attached_images[$first_image_id];
    $attached_images[$first_image_id] = $attached_images[$second_image_id];
    $attached_images[$second_image_id] = $first_image_order;
    set_object_image_box_attachments( $attached_images, $_POST['post_id'] );
    wp_die();
}
add_action( 'wp_ajax_swap_image_order_aj', 'swap_image_order_aj');

/**
 * Ajax callback to add images to object image box after selection/upload through
 * WP Media popup.
 */
function add_gallery_images_aj() {
    $image_attachments = get_object_image_box_attachments( $_POST['post_id'] );
    if ( isset($_POST['wpm_gallery_attachment_ids']) ) $new_ids_arr = explode( ',', $_POST['wpm_gallery_attachment_ids'] );
    else $new_ids_arr = array();
    $max_sort_order = max ( $image_attachments );
    foreach ( $new_ids_arr as $new_id ) {
        $max_sort_order += 1;
        $image_attachments[$new_id] = $max_sort_order;
    }
    set_object_image_box_attachments( $image_attachments, $_POST['post_id'] );
    object_image_box_contents( $_POST['post_id'] );
    wp_die();
}
add_action( 'wp_ajax_add_gallery_images_aj', 'add_gallery_images_aj' );

function check_object_post_on_publish_aj() {
    $post_id = $_POST['post_id'];
    $problems = check_object_post( $post_id );
    $problems_text = '';
    if (count( $problems ) > 0 ) {
        $problems_text .= "<ul>";
        foreach ( $problems as $problem ) {
            $problem = esc_html($problem);
            $problems_text .= "<li>$problem</li>";
        }
        $problems_text .= "</ul>";
    }
    echo $problems_text;
    $_SESSION[WPM_PREFIX . 'object_problems'] = $problems_text;
    wp_die();
}
//add_action( 'wp_ajax_check_object_post_on_publish_aj', 'check_object_post_on_publish_aj' );

?>