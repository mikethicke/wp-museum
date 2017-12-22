<?php
/**
 * Search results page.
 * http://codex.wordpress.org/Creating_a_Search_Page
 */

get_header();

get_sidebar( 'collections' );

?>

<div id="content">
    
<?php

global $post;
global $query_string;
global $wp_query;
$query_args = explode ( '&', $query_string );

foreach ( $query_args as $arg ) {
    $arg_array = explode ( '=', $arg );
    if ( isset ($arg_array[0] ) ) $query_array[$arg_array[0]] = urldecode($arg_array[1]);
}

if ( isset ( $_GET['within_collection'] ) ) {
//If this is set, we're searching instruments.      
    if ( $_GET['within_collection'] == 'entire_collection' ) {
        $parent_collection = get_category_by_slug( 'collections' );
        $parent_collection_ID = $parent_collection->cat_ID;
    }
    else {
        $parent_collection_ID = $_GET['within_collection'];
        $parent_collection = get_category ( $parent_collection_ID );
    }
    
    $search_collections = get_categories ( array ( 'child_of' => $parent_collection_ID ) );
    $search_collections[] = $parent_collection;
    
    foreach ( $search_collections as $coll ) {
        $coll_IDs[] = $coll->cat_ID;
    }
    
    $args['category__in'] = $coll_IDs;
    $args['post_type'] = 'any';
    $args['nopaging'] = true;
    $args['posts_per_page'] = -1;
    
    $no_meta_args = $args;
    $args['meta_query'] = array (
        array ('value' => $query_array['s'],
               'compare' => 'LIKE')
    );
    
    query_posts ( $args );
    $meta_results = $wp_query->posts;
    
    $no_meta_args = array_merge ( $no_meta_args, $query_array );
    query_posts ( $no_meta_args );
    $wpq_posts = $wp_query->posts;
    $merged_results_ununique = array_merge( $meta_results, $wpq_posts );
    
    $merged_results = array();
    
    foreach ( $merged_results_ununique as $result ) {
        $duplicate_found = false;
        foreach ($merged_results as $m_result ) {
            if ( $m_result->ID == $result->ID ) $duplicate_found == true;
        }
        if ( ! $duplicate_found ) $merged_results[] = $result;
    }
    
    foreach( $merged_results as $item ) {
        $post_ids[]=$item->ID; //create a new query only of the post ids
    }
    $unique_posts = array_unique($post_ids); //remove duplicate post ids

    if ( $unique_posts ) {
        query_posts(array(
                    'post__in' => $unique_posts, //new query of only the unique post ids on the merged queries from above
                    'post_type' => 'any',
                    'nopaging' => true,
                    'posts_per_page' => -1
        ));
    
        $row_counter = 1; //toggles between 1 and 0 to allow for alternating row styles
        $column_counter = 1; //cycles between 1 and 3 for three columns of grid view.
        
        $toggle_class = "";
        
        while ( have_posts () ) {
            the_post();    
            if ( isset ( $_GET['within_collection'] ) ) {
                if ( $toggle_class == "toggle-grid" ) { //if displaying as a grid, buffer output for columns
                    if ( $column_counter == 1 ) {
                        $col_1 .= instrument_grid_box ( $post, $row_counter );
                        $column_counter = 2;
                    }
                    elseif ( $column_counter == 2) {
                        $col_2 .= instrument_grid_box ( $post, $row_counter );
                        $column_counter = 3;
                    }
                    elseif ( $column_counter == 3) {
                        $col_3 .= instrument_grid_box ( $post, $row_counter );
                        $column_counter = 1;
                    }
                }
                else { //otherwise display in rows.
                    instrument_row ( $post, $row_counter );   
                }
                //toggle row styles
                $row_counter = 1 - $row_counter;
            }
            else {
            
            }
        }
        //output the grid contents, which have been buffered.
        if ( $toggle_class == "toggle-grid" ) {
            ?>
            <div id="grid-col-1">
                <?php echo $col_1; ?> 
            </div>
            <div id="grid-col-2">
                <?php echo $col_2; ?>
            </div>
            <div id="grid-col-3">
                <?php echo $col_3; ?>
            </div>
            <div class="clear"></div>
            <?php
        }
    }
}
else {
    query_posts ( $query_array );
    
    while ( have_posts() ) {
        the_post();
        
        ?>
        <div class="search_result">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="search_excerpt"><?php the_excerpt(); ?></div>
        </div>
        <?php
    }
}
//Reset Query
wp_reset_query();

?>

</div> <!-- #content --> 

<?php get_footer(); ?>