<?php

/**
 * Exhibit Display custom post type.
 *
 * Exhibit Displays are components of Exhibit. Each exhibit display is associated with a category, and posts in that category make
 * up the display.
 */

require_once ( 'CustomPostType.php' );

$exhibit_options = [
  'type'          => 'exhibit-display',
  'label'         => 'Exhibit Display',
  'label_plural'  => 'Exhibit Displays',
  'description'   => 'A collection of objects within an exhibit.',
  'menu_icon'     => 'dashicons-location-alt'
];
$exhibit_display_post_type = new CustomPostType($exhibit_options);
$exhibit_display_post_type->add_support( ['thumbnail', 'revisions'] );
$exhibit_display_post_type->add_taxonomy( 'category' );



$exhibit_display_post_type->add_meta_field ( 'description', 'Description', 'textarea' );


$categories = get_categories( array('hide_empty' => false));
$category_options = [-1 => ''];
foreach ( $categories as $category ) {
    $category_options[$category->cat_ID] = $category->name;
}
$exhibit_display_post_type->add_meta_field ( 'associated_category', 'Associated Category', 'select', $options=['options'=>$category_options]);

$layout_options = [
    'manual'        => 'Manual',
    'icons'         => 'Icon grid',
    'list'          => 'List'
];
$exhibit_display_post_type->add_meta_field ( 'manual_layout', 'Exhibit Layout', 'radio', $options=['options'=>$layout_options] );

$exhibit_display_post_type->add_custom_meta (
    function ( WP_POST $post ) {
        add_meta_box ( 'exhibit_objects', 'Display Objects', function() use ($post) {
            $post_custom = get_post_custom( $post->ID );
            if ( !isset( $post_custom['associated_category'] ) ) return;
            
            $exhibit_objects = get_posts ( ['category__in'     => $post_custom['associated_category'],
                                             'numberposts'  => -1,
                                             'post_status'  => 'any'] );
            echo "<table class='wp-list-table widefat striped'>";
            foreach ( $exhibit_objects as $ed ) {
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

$exhibit_display_post_type->register();

add_action ( 'edit_form_top', 'add_exhibit_link');
function add_exhibit_link ( WP_POST $post ) {
    if ( $post->post_type != 'exhibit-display' ) return;
    $exhibits  = get_posts ( ['numberposts'     => -1,
                              'post_status'     => 'any',
                              'post_type'       => 'exhibit' ]);
    foreach ( $exhibits as $exhibit ) {
        $post_custom = get_post_custom( $exhibit->ID );
        if ( !isset( $post_custom['associated_category'] ) ) continue;
        if ( $post_custom['associated_category'][0] == wp_get_post_categories( $post->ID )[0] ) {
            $parent_exhibit = $exhibit;
            break;
        }
    }
    if ( isset( $parent_exhibit ) ) {
        echo "<div class='postbox' style='font-size:1.2em; padding:10px; margin-bottom:10px;'>Exhibit: {$parent_exhibit->post_title} (<a href='post.php?post={$parent_exhibit->ID}&action=edit'>Edit</a>)</div>";
    }
}