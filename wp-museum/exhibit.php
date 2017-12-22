<?php

/**
 * Exhibit custom post type.
 *
 * Each exhibit is associated with a category, and links to the pages of that category.
 */

require_once ( 'CustomPostType.php' );

$exhibit_options = [
  'type'          => 'exhibit',
  'label'         => 'Exhibit',
  'label_plural'  => 'Exhibits',
  'description'   => 'A museum exhibit that guides readers through a set of related posts and objects.',
  'menu_icon'     => 'dashicons-location-alt',
  'hierarchical'  => true
];
$exhibit_post_type = new CustomPostType($exhibit_options);
$exhibit_post_type->add_support( ['thumbnail', 'revisions'] );
$exhibit_post_type->add_taxonomy( 'category' );

$exhibit_post_type->add_meta_field ( 'description', 'Description', 'textarea' );


$categories = get_categories( array('hide_empty' => false));
$category_options = [-1 => '' ];
foreach ( $categories as $category ) {
    $category_options[$category->cat_ID] = $category->name;
}
$exhibit_post_type->add_meta_field ( 'associated_category', 'Associated Category', 'select', $options=['options'=>$category_options]);

$layout_options = [
    'manual'        => 'Manual',
    'icons'         => 'Icon grid',
    'list'          => 'List'
];
$exhibit_post_type->add_meta_field ( 'layout', 'Exhibit Layout', 'radio', $options=['options'=>$layout_options] );

$exhibit_post_type->add_custom_meta (
    function ( WP_POST $post ) {
        add_meta_box ( 'sub_exhibits', 'Sub Exhibits', function() use ($post) {          
            $sub_exhibits = get_children (  ['numberposts'  => -1,
                                             'post_status'  => 'any',
                                             'post_type'    => 'exhibit',
                                             'post_parent'  => $post->ID]
                                             );
            echo "<table class='wp-list-table widefat striped'>";
            foreach ( $sub_exhibits as $se ) {
                $permalink = get_permalink( $se->ID );
                $ps = get_post_status_object( $se->post_status )->label;
                echo "<tr>
                        <td>{$se->post_title}</td>
                        <td><a href='post.php?post={$se->ID}&action=edit'>Edit</a></td>
                        <td><a href='{$permalink}'>View</a></td>
                        <td>{$ps}</td>
                    </tr>";
            }
            echo "</table><br />";
            echo "<button type='button' class='button button-large' onclick='new_SE({$post->ID})'>New Sub Exhibit</button>";
        });       
    },
    function ( $post_id ) {
        return;
    }
);

$exhibit_post_type->add_custom_meta (
    function ( WP_POST $post ) {
        add_meta_box ( 'exhibit_objects', 'Objects', function() use ($post) {
            $post_custom = get_post_custom( $post->ID );
            if ( !isset( $post_custom['associated_category'] ) ) return;
            
            $exhibit_objects = get_posts ( ['category__in'     => $post_custom['associated_category'],
                                             'numberposts'  => -1,
                                             'post_status'  => 'any'] );
            echo "<table class='wp-list-table widefat striped'>";
            foreach ( $exhibit_objects as $ed ) {
                if ( $ed->post_type == 'exhibit' ) continue;
                $permalink = get_permalink( $ed->ID );
                $ps = get_post_status_object( $ed->post_status )->label;
                echo "<tr>
                        <td>{$ed->post_title}</td>
                        <td><a href='post.php?post={$ed->ID}&action=edit'>Edit</a></td>
                        <td><a href='{$permalink}'>View</a></td>
                        <td>{$ps}</td>
                    </tr>";
            }
            echo "</table>";
        });       
    },
    function ( $post_id ) {
        return;
    }
);

add_action( 'admin_footer', 'new_sub_exhibit_js' );
function new_sub_exhibit_js() {
    ?>
    <script type="text/javascript">
        function new_SE(parent_exhibit) {
            var data = {
                'action'    : 'create_new_se',
                'parent'    : parent_exhibit
            };
            
            jQuery.post(ajaxurl, data, function(response) {
                window.location.href = "post.php?post=" + response +"&action=edit";
            });
        }
    </script> 
    <?php
}

add_action( 'wp_ajax_create_new_se', 'create_new_se');
function create_new_se() {
    $parent_ID = intval( $_POST['parent'] );
    $category = get_post_custom( $parent_ID )['associated_category'];
    $args = [
        'post_title'        => '',
        'post_content'      => '',
        'post_type'         => 'exhibit',
        'post_parent'       => $parent_ID,
        'post_category'     => $category
    ];
    $post_id = wp_insert_post( $args );
    echo $post_id;
    wp_die();
}


$exhibit_post_type->register();

add_action ( 'edit_form_top', 'add_parent_link');
function add_parent_link ( WP_POST $post ) {
    if ( $post->post_type != 'exhibit' ) return;
    $parent_ID = wp_get_post_parent_ID( $post->ID );
    if ( !$parent_ID ) return;
    $parent  = get_post( $parent_ID );
    if ( isset( $parent ) ) {
        echo "<div class='postbox' style='font-size:1.2em; padding:10px; margin-bottom:10px;'>Parent Exhibit: {$parent->post_title} (<a href='post.php?post={$parent->ID}&action=edit'>Edit</a>)</div>";
    }
}

