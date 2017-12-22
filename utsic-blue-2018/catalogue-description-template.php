<?php
/**
 * Template Name: Catalogue Description Template
 */

global $post;
get_header();

if ( have_posts() ) {
    the_post();
    

    get_sidebar ('catalogue-page');
    
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