<?php
/**
 * Creates object post types.
 */

require_once ( 'SimpleDate.php' );
require_once ( 'MetaBox.php' );
require_once ( 'object_functions.php' );
require_once ( 'ObjectPostType.php' );

/**
 * Creates the custom museum object post types.
 *
 * Iterates through the user-created museum objects and creates a custom post type
 * for each. Each object has a table of custom fields that are presented to users
 * on the edit post screen. The bulk of this function is devoted to creating and
 * saving that form. Object posts are hierarchical--users can create child objects
 * from the edit post page. Objects and their custom fields are accessible to the
 * Wordpress REST api if they are marked as 'public' in the object admin page.
 * Objects have image galleries using ajax to add and manipulate image attachments.
 *
 * @see object_admin.php
 */
function create_object_types() {
    global $wpdb;
    $object_type_table = $wpdb->prefix . WPM_PREFIX . "object_types";
    $object_rows = $wpdb->get_results( "SELECT * FROM $object_type_table" );

    $object_type_list = array();
    foreach ( $object_rows as $object_row ) {
        $new_object_post_type = new ObjectPostType( $object_row );
        $new_object_post_type->register();
        $object_type_list[] = $new_object_post_type->object_type;
    }

    // Adds a list of the museum objects to the REST api.
    // Typically accessed at /wp-json/wp-museum/v1/object_types/
    add_action ( 'rest_api_init', function() use( $object_type_list) {
        register_rest_route( 'wp-museum/v1', '/object_types/', array (
            'methods'   => 'GET',
            'callback'  => function() {
                return $this->object_type_list;
            }
        ) );
    } );
}
add_action( 'plugins_loaded', 'create_object_types' );

/**
 * Adds a link to the parent object post for child posts.
 */
function add_object_parent_link ( WP_POST $post ) {
    if ( substr($post->post_type, 0, strlen(WPM_PREFIX)) !== WPM_PREFIX ) return;
    $parent_ID = wp_get_post_parent_ID( $post->ID );
    if ( !$parent_ID ) return;
    $parent  = get_post( $parent_ID );
    if ( isset( $parent ) ) {
        echo "<div class='postbox' style='font-size:1.2em; padding:10px; margin-bottom:10px;'>Parent Object: {$parent->post_title} (<a href='post.php?post={$parent->ID}&action=edit'>Edit</a>)</div>";
    }
}
add_action ( 'edit_form_top', 'add_object_parent_link');

function add_object_check ( ) {
    global $post;
    if ( !empty($post) && in_array( $post->post_type, get_object_type_names() ) ) {
        ?>
        <script type="text/javascript">
            jQuery('#save-post').click(check_object_post_for_publication);
            jQuery('#publish').click(check_object_post_for_publication);
        </script>
        <?php
    }  
}
//add_action ( 'admin_footer', 'add_object_check');

function add_object_problem_div() {
    global $post;
    if ( !empty($post) && in_array( $post->post_type, get_object_type_names() ) ) {
        echo "<div id='wpm-post-check' class='error'";
    if ( !empty( $_SESSION[WPM_PREFIX . 'object_problems'] ) ) {
        echo ">";
        echo $_SESSION[WPM_PREFIX . 'object_problems']; 
        unset ( $_SESSION[WPM_PREFIX . 'object_problems'] );
    }
    else {
        echo "style='display:none'>";
    }
    echo "</div>";
    } 
}
add_action ( 'admin_notices', 'add_object_problem_div' );

function check_object_post_on_publish( $new_status, $old_status, $post) {
    if ( empty($post) || !in_array( $post->post_type, get_object_type_names() ) )
        return;
    $problems = check_object_post( $post->ID );
    $problems_text = '';
    if (count( $problems ) > 0 ) {
        if ($new_status != $old_status && $new_status == 'publish' ) {
            $post->post_status = $old_status;
            wp_update_post( $post );
        }
        $problems_text .= "<ul>";
        foreach ( $problems as $problem ) {
            $problems_text .= "<li>$problem</li>";
        }
        $problems_text .= "</ul>";
    }
    $_SESSION[WPM_PREFIX . 'object_problems'] = $problems_text;
}
add_action( 'transition_post_status', 'check_object_post_on_publish', 10, 3);

