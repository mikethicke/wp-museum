<?php

require_once ( 'CustomPostType.php' );
require_once ( 'MetaBox.php' );

$collection_options = [
    'type'          => 'collection',
    'label'         => 'Collection',
    'label_plural'  => 'Collections',
    'description'   => 'Contains related museum objects.',
    'menu_icon'     => 'dashicons-archive',
    'hierarchical'  => true,
    'capabilities'  => [
        'edit_posts' => 'edit_collections',
        'edit_others_posts' => 'edit_others_collections',
        'publish_posts' => 'publish_collections',
        'read_private_posts' => 'read_private_collections',
        'delete_posts' => 'delete_collections',
        'edit_published_posts' => 'edit_published_collections'
    ],
    'map_meta_cap'  => true
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
    
    $collection_objects = get_associated_objects( 'any' );
    
    echo "<table class='wp-list-table widefat striped'>";
    if ( isset( $collection_objects ) && !empty( $collection_objects ) ) {
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
    }
    else {
        echo "<tr><td>No collection objects found.</td></tr>";
    }
    echo "</table>";
};
$associated_objects_box = new MetaBox ( 'collection_objects', __('Objects'), $display_associated_objects );
$collection_post_type->add_custom_meta ( $associated_objects_box );

$collection_post_type->register();

function get_collections( $post_status='any') {
    $collections = get_posts ( [
        'numberposts'    => -1,
        'post_status'    => $post_status,
        'post_type'      => 'collection'
    ]);
    return $collections;

}

const DEF_POSTS_PER_PAGE = 20;
function query_associated_objects( $post_status='publish' ) {
    global $post;
    $post_custom = get_post_custom( $post->ID );
    $options = get_option( 'utsic_options' );
    $object_types = get_object_type_names();
    if ( !isset( $post_custom['associated_category'] ) ) return;
    
    $included_categories = $post_custom['associated_category'];
    if ( isset( $post_custom['include_sub_collections'] ) && $post_custom['include_sub_collections'][0] == '1' ) {
       $descendants = get_post_descendants( $post, $post_status );
       foreach ( $descendants as $descendant ) {
           $d_custom = get_post_custom( $descendant->ID );
           $included_categories = array_merge( $included_categories, $d_custom['associated_category']);
       }
    }
    
    if ( isset( $post_custom['include_child_categories'] ) && $post_custom['include_child_categories'][0] == '1' ) {
        $cat_call = 'cat';
        $cat_val = implode( ',', $included_categories);
    }
    else {
        $cat_call = 'category__in';
        $cat_val = $included_categories;
    }
    
    if ( isset ( $_GET['show_all'] ) && $_GET['show_all'] == 1 ) {
        $collection_query = new WP_Query ( [
            $cat_call           => $cat_val,
            'numberposts'       => -1,
            'post_status'       => $post_status,
            'posts_per_page'    => -1,
            'post_type'         => $object_types
        ] );    
    }
    else {
        if ( isset( $_GET['page'] ) ) $pagenum = $_GET['page'];
        else $pagenum = get_query_var('page');
        if ( isset( $options['utsic_posts_per_page'] ) ) $posts_per_page = $options['utsic_posts_per_page'];
        else $posts_per_page = DEF_POSTS_PER_PAGE;
        $collection_query = new WP_Query ( [
            $cat_call           => $cat_val,
            'posts_per_page'    => $posts_per_page,
            'paged'             => $pagenum,
            'post_status'       => $post_status,
            'post_type'         => $object_types
        ] ); 
    } 
    return $collection_query;
}

function get_associated_objects ( $post_status='publish' ) {
    $query = query_associated_objects( $post_status );
    return $query->posts;
}

function get_object_collections ( $post_id ) {
    $object = get_post ( $post_id );
    $collections = get_collections();
    $object_collections = [];
    $object_categories = array_map (function ($cat) { return $cat->term_id; }, get_the_category( $object->ID ) );
    foreach ( $collections as $collection ) {
        $collection_custom = get_post_custom ( $collection->ID );
        $cat_intersect = array_intersect( $object_categories , $collection_custom['associated_category'] );
        if ( count( $cat_intersect ) > 0 ) $object_collections[] = $collection;
    }
    return $object_collections;
}

function the_object_collections ( $separator='',  $post_id ) {
    $collections = get_object_collections( $post_id );
    $return_string = '';
    foreach ( $collections as $collection ) {
        if ( $return_string != '' ) $return_string .= $separator;
        $permalink = get_permalink ( $collection->ID );
        $return_string .= "<a href='$permalink'>{$collection->post_title}</a>";
    }
    echo $return_string;
}

