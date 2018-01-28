<?php

if ( isset($_GET['mode'] ) ) $display_mode = $_GET['mode'];
else $display_mode = 'about';

global $post;
get_header();

if ( have_posts() ) {
    the_post();
    $custom = get_post_custom ( $post->ID );
    
    //get_sidebar ( );
    
    if ( ( $display_mode == 'grid' || $display_mode == 'rows') && isset( $custom['associated_category'] ) ) {
        $post_types = get_wpm_post_types();
        $associated_objects = get_posts( [
            'category__in'      => $custom['associated_category'],
            'numberposts'       => -1,
            'post_status'       => 'publish',
            'post_type'         => $post_types
        ]);
    }
    echo '<div id="content">';
    
    if ( $display_mode == 'about' ) {
        ?>
        <div class="post-wrap">
            <h2 class="post-title"><?php the_title(); ?></h2>
            <div class="entry">
                <?php the_content(); ?>
            </div>
        </div>
        <?php
    }
    elseif ( ( $display_mode == 'grid' || $display_mode == 'rows') && isset( $custom['associated_category'] ) ) {
        $post_types = get_wpm_post_types();
        $associated_objects = get_posts( [
            'category__in'      => $custom['associated_category'],
            'numberposts'       => -1,
            'post_status'       => 'publish',
            'post_type'         => $post_types
        ]);
        //echo '<div class="breadcrumbs">' . get_category_parents( $current_category->cat_ID , TRUE, " &raquo; " ) . '</div>';
        
        $row_counter = 1; //toggles between 1 and 0 to allow for alternating row styles
        $column_counter = 1; //cycles between 1 and 3 for three columns of grid view.
            
        $col_1 = '';
        $col_2 = '';
        $col_3 = '';
        
        foreach ( $associated_objects as $object ) { //if displaying as a grid, buffer output for columns
            if ( $display_mode == 'grid' ) {
                if ( $column_counter == 1 ) $col_1 .= instrument_grid_box( $object, $row_counter );
                elseif ( $column_counter == 2) $col_2 .= instrument_grid_box( $object, $row_counter );
                else $col_3 .= instrument_grid_box( $object, $row_counter );
                $column_counter = ( $column_counter + 1 ) % 3;
            }
            else {
                instrument_row ( $object, $row_counter );
                $row_counter = 1 - $row_counter;
            }
        }
        
        //output the grid contents, which have been buffered.
        if ( $display_mode == 'grid' ) {
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
    
    
         
        
    echo '<div class="clear"></div>';    
    echo'</div>'; // #content
}

get_footer();

?>