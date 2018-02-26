<?php
/**
 * Search results page.
 */

get_header( );

get_sidebar();

global $wp_query;
if ( empty( $paged ) ) $paged = 1;
$starting_result = ( ($paged - 1) * $posts_per_page ) + 1;
$ending_result = $starting_result + $wp_query->post_count - 1;

echo "<div id='content'>";

echo "<h2>Search Results</h2>";

echo '<div class="paging">';
echo 'Showing results ' . $starting_result . '-' . $ending_result . ' of ' . $wp_query->found_posts;
echo "<div class='search_paging'>";

echo paginate_links( array(
    'base' => '?%_%',
    'format' => 'page=%#%',
    'current' => max( 1, $paged ),
    'total' => $wp_query->max_num_pages
) );
    
echo "</div></div>";
    
while ( have_posts() ) {
    the_post();
    post_row();
}

echo '<div class="paging">';
echo "<div class='search_paging'>";

echo paginate_links( array(
    'base' => '?%_%',
    'format' => 'page=%#%',
    'current' => max( 1, $paged ),
    'total' => $wp_query->max_num_pages
) );
    
echo "</div></div>";

echo "</div>"; //#content

get_footer();