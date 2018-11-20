<?php

/**
 * Category listings are in alternating color rows with thumbnails for each post.
 */

get_header();

get_sidebar();

if ( is_category() ) $current_category = get_category( get_query_var('cat') );

echo '<div id="category">';
if ( isset( $current_category->name ) ) echo '<h2 class="category-heading">' . $current_category->name . '</h2>';
if ( isset( $current_category->category_description ) && $current_category->category_description != '' ) {
    echo '<div class="category-description">' . apply_filters( 'the_content', $current_category->category_description ) . '</div>';
}

posts_nav_link();

echo "<br /><br />";

echo '<div id="category-rows">';

while ( have_posts() ) {
    the_post();
    
    post_row( $post );
  
}

echo "</div>"; //#category-rows

posts_nav_link();

echo "</div>"; //#category  
    
get_footer();