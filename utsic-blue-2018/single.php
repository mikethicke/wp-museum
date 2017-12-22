<?php
/**
 * Single post or page.
 */

global $post;
get_header();

if ( have_posts() ) {
    the_post();
    
    //retrieve instrument fields from database
    global $wpdb;
    
    //if exhibits page, display exhibit sidebar
    $exhibits_category = get_category_by_slug( 'exhibits' );
    if ( post_is_in_descendant_category( $exhibits_category->term_id ) ) {
        get_sidebar( 'exhibit' );
    }
    
    ?>
    <div id="content">
        <div class="post-wrap">
            <h2 class="post-title"><?php the_title(); ?></h2>
            <div class="entry">
                <?php the_content(); ?>
            </div>
        </div>
        <div class="clear"></div>    
    </div> <!-- #content --> 
<?php    
}
    get_footer();
?>