<?php
/**
 * Exhibit template
 * Requires wp-museum plugin
 */

global $post;
get_header();

if ( have_posts() ) {
    the_post();
    $post_custom = get_post_custom();
    $sub_exhibits = get_posts ( [   'post_parent'  => $post->ID,
                                    'numberposts'  => -1,
                                    'post_status'  => 'published',
                                    'post_type'    => 'exhibit'] );
    
    $objects = get_posts ( [        'numberposts'   => -1,
                                    'post_status'   => 'published',
                                    'category__in'  => $post_custom['associated_category']
                            ] );
    
    
    ?>
    <div id="exhibit">
        <h2 class="exhibit-heading"><?php echo the_title(); ?></h2>
        <div class="exhibit-image"><?php if ( has_post_thumbnail() ) the_post_thumbnail('medium'); ?></div>
        <div class="exhibit-description"><?php echo the_content(); ?></div>
        <div class="clear"></div>
        
        <?php
        if ( $post_custom['layout'][0] != 'manual' ) {
            if ( count($sub_exhibits) > 0 ) {
                ?>
                <div id="displays">
                <?php
                if ( $post_custom['layout'][0] == 'icons' ) {
                    post_boxes ( $sub_exhibits );
                }
                elseif ( $post_custom['layout'][0] == 'list' ) {
                    post_rows ( $sub_exhibits );
                }
                ?>
                </div>
                <?php
            }
            if ( count($objects) > 0 ) {
                ?>
                <div>
                <?php
                if ( $post_custom['layout'][0] == 'icons' ) {
                    post_boxes ( $objects );
                }
                elseif ( $post_custom['layout'][0] == 'list' ) {
                    post_rows ( $objects );
                }
                ?>
                </div>
                <?php
            }
        }
    ?>
    </div> <!--exhibit-->
    <?php
}
    get_footer();
?>