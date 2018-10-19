<?php

/**
 * Category listings are in alternating color rows with thumbnails for each post.
 */

get_header();

if ( $posts_only == 1 && cat_is_ancestor_of ( $exhibits_category, $current_category ) ) {
    get_sidebar( 'exhibit' );
}


echo '<div id="category">';
if ( isset( $current_category->name ) ) echo '<h2 class="category-heading">' . $current_category->name . '</h2>';
if ( isset( $current_category->category_description ) && $current_category->category_description != '' ) {
    echo '<div class="category-description">' . apply_filters( 'the_content', $current_category->category_description ) . '</div>';
}

echo '<div class="clear">&nbsp</div>';

$row_counter = 1; //cycles between 1 and 0 to alternate row colours.

posts_nav_link();

echo "<br /><br />";

echo '<div id="category-rows">';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
query_posts ( "category_name={$current_category->slug}&posts_per_page=10&paged=" . $paged );
while ( have_posts() ) {
    the_post();
    
    post_row( $post, $row_counter );
    
    $row_counter = 1 - $row_counter;
}
wp_reset_query();

echo "</div>"; //#category-rows

posts_nav_link();

echo "</div>"; //#category  
    
get_footer();