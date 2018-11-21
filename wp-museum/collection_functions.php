<?php
/*
 * Functions for interfacing with the Collection post type.
 */

/**
 * Gets all posts of the Collection post type.
 *
 * @param string $post_status The post status of collections to retrieve.
 *
 * @return [Post] Array of collection posts.
 */
function get_collections( $post_status='any') {
    $collections = get_posts ( [
        'numberposts'    => -1,
        'post_status'    => $post_status,
        'post_type'      => 'collection'
    ]);
    return $collections;
}

/*
 * Default number of posts per page to retrieve in query_associated_objects.
 */
const DEF_POSTS_PER_PAGE = 20;

/**
 * Creates query containing all posts associated with the current collection. 
 *
 * @param string    $post_status    The the publication status of posts to retrieve.
 * @param int       $post_id        If set, retrieve posts associated with $post_id rather than the current $post.
 * @param int       $show_all       If set, retrieve all posts rather than paged results. Can also be set with
 *                                  $_GET['show_all'].
 * @param int       $page_num       If set, retrieve specific page of results. Can also be set with $_GET['page'].
 *                                  If not set, defaults to global query value.
 *
 * @return A Wordpress query object containing the retrieved posts.
 */
function query_associated_objects( $post_status='publish', $post_id=null, $show_all=null, $page_num=null ) {
    global $post;
    if ( is_null( $post_id ) ) $post_id = $post->ID;
    $post_custom = get_post_custom( $post_id );
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
    
    if ( is_null($show_all) && isset( $_GET['show_all'] ) ) $show_all = $_GET['show_all'];
    
    if ( !is_null($show_all) && $show_all == 1 ) {
        $collection_query = new WP_Query ( [
            $cat_call           => $cat_val,
            'numberposts'       => -1,
            'post_status'       => $post_status,
            'posts_per_page'    => -1,
            'post_type'         => $object_types
        ] );    
    }
    else {
        if ( is_null($page_num) && isset( $_GET['[page]'] ) ) $page_num = $_GET['page'];
        else $page_num = get_query_var('page');
        if ( isset( $options['utsic_posts_per_page'] ) ) $posts_per_page = $options['utsic_posts_per_page'];
        else $posts_per_page = DEF_POSTS_PER_PAGE;
        $collection_query = new WP_Query ( [
            $cat_call           => $cat_val,
            'posts_per_page'    => $posts_per_page,
            'paged'             => $page_num,
            'post_status'       => $post_status,
            'post_type'         => $object_types
        ] ); 
    } 
    return $collection_query;
}

/**
 * Retrieves all posts associated with the current collection.
 *
 * @param string    $post_status    The publication status of the posts to retrieve.
 *
 * @return [Post]   Array of posts associated with the current collection.
 */
function get_associated_objects ( $post_status='publish' ) {
    $query = query_associated_objects( $post_status );
    if ( !is_null($query) ) return $query->posts;
    return array();
}

/**
 * Retrieves post ids of all posts associated with a collection.
 *
 * @param int       $post_id        The post id of the collection.
 * @param string    $post_status    The publication status of the posts to retrieve.
 *
 * @return [int]    Array of post ids associated with the collection.
 */
function get_associated_object_ids ( $post_id, $post_status='publish' ) {
    $query = query_associated_objects( $post_status, $post_id, 1 );
    $post_ids = array_map ( function( $element ) {
            return $element->ID;
        }, $query->posts );
    return $post_ids;
}

/**
 * Retrieves all collections that a post is associated with.
 *
 * @param int   $post_id    The id of the post.
 *
 * @return [Post]   Array of collection posts that the current post is associated with.
 */
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

/**
 * Retrieves links to all collections that a post is associated with.
 *
 * @param string    $separator      String separating each link.
 * @param int       $post_id        The id of the post.
 *
 * @return string   Html string containing links to each collection.
 */
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


?>