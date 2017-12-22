<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 */

get_header(); ?>


<div id="content">
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post(); ?>
            <div class="post-wrap">
                <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="entry">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php
        } //while
    }   
    ?> 
</div><!-- #content --> 
		

<?php get_footer(); ?>