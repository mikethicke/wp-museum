<?php
/**
 * Displays a collection.
 *
 * A collection is associated with a Wordpress category, and functions something
 * like a category combined with a post.
 */

$display_mode = get_display_mode();

global $post;

$self_link = $_SERVER['REQUEST_URI'];

get_header( 'small' );

if ( have_posts() ) {
    the_post();
    $custom = get_post_custom ( $post->ID );
    
    get_sidebar( 'collections' );
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
        /* Grid <-> list toggle */
        if ( $display_mode == "grid" ) {
            $toggle_class = "toggle-grid";
        }
        elseif ( $display_mode == "rows" ) {
            $toggle_class = "toggle-list";
        }
        $grid_off_url = add_get_param ( $self_link, 'mode', 'rows' );
        $grid_on_url = add_get_param ( $self_link, 'mode', 'grid' );
        ?>
        <div id="list-grid-toggle" class="<?php echo $toggle_class; ?>" >
            <a id="toggle-left" href="<?php echo $grid_off_url; ?>"><img src="<?php bloginfo('template_directory');?>/images/grid-list-icon.png" /></a>
            <a id="toggle-right" href="<?php echo $grid_on_url; ?>"><img src="<?php bloginfo('template_directory');?>/images/grid-list-icon.png" /></a> 
        </div>
        
        <?php 
        
        $associated_object_query = query_associated_objects();
        
        echo "<div class='paging'>";
        if ( $associated_object_query->max_num_pages > 1 ) {
            if ( isset ( $_GET['show_all'] ) && $_GET['show_all'] == 1 ) {
                $self_link = add_get_param( $self_link, 'show_all', '-1' );
                echo "<a href='$self_link'>Show Paged</a></div>";
            }
            else {
                $self_link = add_get_param( $self_link, 'show_all', '1' );
                $self_link = add_get_param( $self_link, 'page', '1' );
                $paged = $associated_object_query->get( 'paged', 1 );
                echo paginate_links( array(
                    'base' => '?%_%',
                    'format' => 'page=%#%',
                    'current' => max( 1, $paged ),
                    'total' => $associated_object_query->max_num_pages
                ) );
               echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='$self_link'>Show All</a>";
            }
        }
        else {
            echo "&nbsp;";
        }
        echo "</div>";
        
        
        $row_counter = 1; //toggles between 1 and 0 to allow for alternating row styles
        $column_counter = 1; //cycles between 1 and 3 for three columns of grid view.
            
        $col_1 = '';
        $col_2 = '';
        $col_3 = '';
        
        $associated_objects = $associated_object_query->posts;
        
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