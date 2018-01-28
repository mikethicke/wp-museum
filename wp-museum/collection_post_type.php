<?php

require_once ( 'CustomPostType.php' );
require_once ( 'MetaBox.php' );

$collection_options = [
    'type'          => 'collection',
    'label'         => 'Collection',
    'label_plural'  => 'Collections',
    'description'   => 'Contains related museum objects.',
    'menu_icon'     => 'dashicons-archive',
    'hierarchical'  => true
];
$collection_post_type = new CustomPostType( $collection_options );
$collection_post_type->add_support( ['thumbnail'] );

/*
 * Custom Fields
 */

$categories = get_categories( array('hide_empty' => false));
$category_options = [-1 => '' ];
foreach ( $categories as $category ) {
    $category_options[$category->cat_ID] = $category->name;
}
$collection_post_type->add_meta_field( 'include_sub_collections', 'Include Sub Collections', 'checkbox' );
$collection_post_type->add_meta_field( 'associated_category', 'Associated Category', 'select', $options=['options'=>$category_options]);
$collection_post_type->add_meta_field( 'include_child_categories', 'Include Child Categories', 'checkbox' );

/*
 * Associated objects
 */

 $display_associated_objects = function() {
    global $post;
    $post_custom = get_post_custom( $post->ID );
    if ( !isset( $post_custom['associated_category'] ) ) return;
    
    if ( isset( $post_custom['include_child_categories'] ) && $post_custom['include_child_categories'][0] == '1' ) {
        $cat_call = 'category';
        $cat_val = implode( ',', $post_custom['associated_category']);
    }
    else {
        $cat_call = 'category__in';
        $cat_val = $post_custom['associated_category'];
    }
    
    $collection_objects = get_posts ( [$cat_call    => $cat_val,
                                     'numberposts'  => -1,
                                     'post_status'  => 'any',
                                     'post_type' => 'any' ] );
    echo "<table class='wp-list-table widefat striped'>";
    foreach ( $collection_objects as $co ) {
        if ( $co->post_type == 'collection' ) continue;
        $permalink = get_permalink( $co->ID );
        $ps = get_post_status_object( $co->post_status )->label;
        echo "<tr>
                <td>{$co->post_title}</td>
                <td><a href='post.php?post={$co->ID}&action=edit'>Edit</a></td>
                <td><a href='{$permalink}'>View</a></td>
                <td>{$ps}</td>
            </tr>";
    }
    echo "</table>";
};
$associated_objects_box = new MetaBox ( 'collection_objects', __('Objects'), $display_associated_objects );
$collection_post_type->add_custom_meta ( $associated_objects_box );


$collection_post_type->register();

