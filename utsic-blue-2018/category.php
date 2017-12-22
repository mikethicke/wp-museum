<?php get_header();

global $post; 
$collections_category = get_category_by_slug( 'collections' );
$catalogue_category = get_category_by_slug( 'catalogue' );
$current_category = get_queried_object();
$exhibits_category = get_category_by_slug( 'exhibits' );
$exhibits_children = get_categories( array ( 'parent' => $exhibits_category->term_id, 'hide_empty' => 0, 'exclude' => '61,62,63,64,65' ) );
//hack to hide color of science exhibit
$options = get_option( 'utsic_options' );

//check to see whether instrument only or posts only overrides are set. If so, will just display those.
$instruments_only = 0;
$posts_only = 0;
$inferno_only = 0;
if ( isset ( $_GET['instruments_only'] ) && $_GET['instruments_only'] == 1 ) {
    $instruments_only = 1;
}

else if ( isset ( $_GET['posts_only'] ) && $_GET['posts_only'] == 1 ) {
    $posts_only = 1;
}
else if ( isset ( $_GET['inferno_only'] ) && $_GET['inferno_only'] == 1 ) {
    $inferno_only = 1;
}


/**
 * Template for displaying instrument collections. The 'collections' category needs to exist, and
 * instruments need to be in it. Instrument rows are displayed if the instrument is in the current
 * category or in a category that is a descendent of that category. 
 */ 
if ( cat_is_ancestor_of( $collections_category, $current_category ) || is_category( 'collections' ) || $instruments_only == 1 ) {
    
    if ( cat_is_ancestor_of ( $exhibits_category, $current_category ) ) {
        get_sidebar( 'exhibit' );
    }
    else get_sidebar( 'collections' );
    
    echo '<div id="content">';
    
    //link to toggle between list and grid views
    if ( isset( $_SESSION['grid'] ) && $_SESSION['grid'] == 1 ) {
        $toggle_class = "toggle-grid";    
    }
    else {
        $toggle_class = "toggle-list";
        $_SESSION['grid'] = -1;
    }
    
    $grid_off_string = '?grid=-1';
    $grid_on_string = '?grid=1';
    if ( $instruments_only == 1 ) {
        $grid_off_string .= '&instruments_only=1';
        $grid_on_string .= '&instruments_only=1';
    }
    
    ?>
    <div id="list-grid-toggle" class="<?php echo $toggle_class; ?>" >
        <a id="toggle-left" href="<?php echo $_SERVER['REDIRECT_URL']; echo $grid_off_string; ?>"><img src="<?php bloginfo('template_directory');?>/images/grid-list-icon.png" /></a>
        <a id="toggle-right" href="<?php echo $_SERVER['REDIRECT_URL']; echo $grid_on_string; ?>"><img src="<?php bloginfo('template_directory');?>/images/grid-list-icon.png" /></a> 
    </div>
    <?php
    
    //breadcrumb list of category hierarchy
    echo '<div class="breadcrumbs">' . get_category_parents( $current_category->cat_ID , TRUE, " &raquo; " ) . '</div>';
    
    //pagination
    if ( is_category( 'collections' ) ) {
        if ( isset ( $_GET['show_all'] ) && $_GET['show_all'] == 1 ) {
            $query_paging = "posts_per_page=-1";
            query_posts( "post_type=instrument&$query_paging" ); //Display only instruments for Collections
            echo '<div class="paging"><a href="'. $_SERVER['REDIRECT_URL'] . '?grid='. $_SESSION['grid'] . '&show_all=-1">Show Paged</a></div>';
        }
        else {
            $paged = get_query_var( 'page' );
            if ( ! $paged  ) $paged = 1;
            $query_paging = "posts_per_page=". $options['utsic_posts_per_page'] ."&paged=" . $paged;
            query_posts( "post_type=instrument&$query_paging" ); //Display only instruments for Collections
            $wp_query->max_num_pages = (int) ceil( $wp_query->found_posts / get_query_var( 'posts_per_page' ) );
            echo '<div class="paging">';
            
            echo paginate_links( array(
                'base' => '?%_%',
                'format' => 'page=%#%',
                'current' => max( 1, $paged ),
                'total' => $wp_query->max_num_pages
            ) );
            if (isset($_SERVER['REDIRECT_URL'])) {
                $link_url = $_SERVER['REDIRECT_URL'];
            }
            else {
                $link_url = $_SERVER['REQUEST_URI'];
            }
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $link_url . '?show_all=1">Show All</a></div>';
        }
    }
    else {
        //for now only allow pagination for collections category, as it is broken otherwise.
        $query_paging = "posts_per_page=-1";
        query_posts( "post_type=instrument&$query_paging" ); //Display only instruments for Collections
    }
    
    
    $row_counter = 1; //toggles between 1 and 0 to allow for alternating row styles
    $column_counter = 1; //cycles between 1 and 3 for three columns of grid view.
        
    $col_1 = '';
    $col_2 = '';
    $col_3 = '';
    
    while ( have_posts() ) {
        the_post();
        
        //see if one of post's categories is a descendent of current category.
        $post_categories = wp_get_post_categories( $post->ID );
        $found_ancestor = false;  
        foreach ( $post_categories as $cat ) {
            if ( cat_is_ancestor_of( $current_category, $cat ) ) $found_ancestor = true;
            if ( $current_category->cat_ID == $cat ) $found_ancestor = true;
        } 
        
        //display instrument row only if instrument is in current category or is a descendent of current category.
        if ( $found_ancestor )
        {   
            if ( $toggle_class == "toggle-grid" ) { //if displaying as a grid, buffer output for columns
                if ( $column_counter == 1 ) {
                    $col_1 .= instrument_grid_box( $post, $row_counter );
                    $column_counter = 2;
                }
                elseif ( $column_counter == 2) {
                    $col_2 .= instrument_grid_box( $post, $row_counter );
                    $column_counter = 3;
                }
                elseif ( $column_counter == 3) {
                    $col_3 .= instrument_grid_box( $post, $row_counter );
                    $column_counter = 1;
                }
            }
            else { //otherwise display in rows.
                instrument_row( $post, $row_counter );   
            }
            //toggle row styles
            $row_counter = 1 - $row_counter;
        } //if found ancestor
    } //while ( have_posts() )
    
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
    
    echo '</div>'; //#content 
    
    //Reset Query
    wp_reset_query();
} //if ( is_child_of_category ( 'collections', '', false ) )





if ( cat_is_ancestor_of( $catalogue_category, $current_category ) || is_category( 'catalogue' ) || $inferno_only == 1 ) {
    
    if ( cat_is_ancestor_of ( $catalogue_category, $current_category ) ) {
        get_sidebar( 'catalogue' );
    }
  
    
    echo '<div id="content">';
    
    //link to toggle between list and grid views
    if ( isset( $_SESSION['grid'] ) && $_SESSION['grid'] == 1 ) {
        $toggle_class = "toggle-grid";    
    }
    else {
        $toggle_class = "toggle-list";
        $_SESSION['grid'] = -1;
    }
    
    $grid_off_string = '?grid=-1';
    $grid_on_string = '?grid=1';
    if ( $inferno_only == 1 ) {
        $grid_off_string .= '&inferno_only=1';
        $grid_on_string .= '&inferno_only=1';
    }
    
    ?>
    <div id="list-grid-toggle" class="<?php echo $toggle_class; ?>" >
        <a id="toggle-left" href="<?php echo $_SERVER['REDIRECT_URL']; echo $grid_off_string; ?>"><img src="<?php bloginfo('template_directory');?>/images/grid-list-icon.png" /></a>
        <a id="toggle-right" href="<?php echo $_SERVER['REDIRECT_URL']; echo $grid_on_string; ?>"><img src="<?php bloginfo('template_directory');?>/images/grid-list-icon.png" /></a> 
    </div>
    <?php
    
    //breadcrumb list of category hierarchy
    echo '<div class="breadcrumbs">' . get_category_parents( $current_category->cat_ID , TRUE, " &raquo; " ) . '</div>';
    
    //pagination
    if ( is_category( 'catalogue' ) ) {
        if ( isset ( $_GET['show_all'] ) && $_GET['show_all'] == 1 ) {
            $query_paging = "posts_per_page=-1";
            query_posts( "post_type=inferno&$query_paging" ); //Display only inferno for catalogue
            echo '<div class="paging"><a href="'. $_SERVER['REDIRECT_URL'] . '?grid='. $_SESSION['grid'] . '&show_all=-1">Show Paged</a></div>';
        }
        else {
            $paged = get_query_var( 'page' );
            if ( ! $paged  ) $paged = 1;
            $query_paging = "posts_per_page=". $options['utsics_posts_per_page'] ."&paged=" . $paged;
            query_posts( "post_type=inferno&$query_paging" ); //Display only instruments for Collections
            $wp_query->max_num_pages = (int) ceil( $wp_query->found_posts / get_query_var( 'posts_per_page' ) );
            echo '<div class="paging">';
            
            echo paginate_links( array(
                'base' => '?%_%',
                'format' => 'page=%#%',
                'current' => max( 1, $paged ),
                'total' => $wp_query->max_num_pages
            ) );
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $_SERVER['REDIRECT_URL'] . '?show_all=1">Show All</a></div>';
        }
    }
    else {
        //for now only allow pagination for collections category, as it is broken otherwise.
        $query_paging = "posts_per_page=-1";
        query_posts( "post_type=inferno&$query_paging" ); //Display only instruments for Collections
    }
    
    
    $row_counter = 1; //toggles between 1 and 0 to allow for alternating row styles
    $column_counter = 1; //cycles between 1 and 3 for three columns of grid view.
        
    $col_1 = '';
    $col_2 = '';
    $col_3 = '';
    
    while ( have_posts() ) {
        the_post();
        
        //see if one of post's categories is a descendent of current category.
        $post_categories = wp_get_post_categories( $post->ID );
        $found_ancestor = false;  
        foreach ( $post_categories as $cat ) {
            if ( cat_is_ancestor_of( $current_category, $cat ) ) $found_ancestor = true;
            if ( $current_category->cat_ID == $cat ) $found_ancestor = true;
        } 
        
        //display inferno row only if inferno is in current category or is a descendent of current category.
        if ( $found_ancestor )
        {   
            if ( $toggle_class == "toggle-grid" ) { //if displaying as a grid, buffer output for columns
                if ( $column_counter == 1 ) {
                    $col_1 .= inferno_grid_box( $post, $row_counter );
                    $column_counter = 2;
                }
                elseif ( $column_counter == 2) {
                    $col_2 .= inferno_grid_box( $post, $row_counter );
                    $column_counter = 3;
                }
                elseif ( $column_counter == 3) {
                    $col_3 .= inferno_grid_box( $post, $row_counter );
                    $column_counter = 1;
                }
            }
            else { //otherwise display in rows.
                instrument_row( $post, $row_counter );   
            }
            //toggle row styles
            $row_counter = 1 - $row_counter;
        } //if found ancestor
    } //while ( have_posts() )
    
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
    
    echo '</div>'; //#content 
    
    //Reset Query
    wp_reset_query();
} //if ( is_child_of_category ( 'catalogue', '', false ) )




















/**
 * Template for displaying Exhibits category.
 */
else if ( is_category( 'exhibits' ) && $posts_only == 0 ) {
    echo '<div id="exhibit-listing">';
    
    foreach ( $exhibits_children as $exhibit ) {
        ?>
        <div class="exhibit-row">
            <h2><a href="<?php echo get_category_link( $exhibit->term_id ); ?>"><?php echo $exhibit->name; ?></a></h2>
            <div class="exhibit-row-thumbnail">
                <a href="<?php echo get_category_link( $exhibit->term_id ); ?>">
                    <?php echo get_the_term_thumbnail( $exhibit->term_id, 'category', 'thumbnail' ); ?>
                </a>
            </div>
            <div class="exhibit-text-wrapper">
                
                <div class="exhibit-row-description">
                    <?php echo custom_excerpt( $exhibit->category_description, 100 ); ?>
                </div>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
        <?php
    }
    
    ?>
    </div>
    
    <?php

}

/**
 * Template for displaying individual exhibits
 */
else if ( cat_is_child_of ( $exhibits_category, $current_category ) && $posts_only == 0  && $current_category->term_id != 61) {
//hack to hide color of science exhibit    
    $themes_categories = get_categories( array ( 'parent' => $current_category->term_id, 'hide_empty' => 0 ) );
    
    $themes_posts = get_posts( array ( 'numberposts' => 10, 'category__in' => $current_category->term_id ) );
    
    $box_width = get_option( 'thumbnail_size_w' ) + 40;
    $num_boxes_per_row = (int) floor( 830 / $box_width ); //830 is the text width, from style.css;
    $total_width = $box_width * $num_boxes_per_row;
    $total_boxes = count( $themes_categories );
    
    $boxes_in_last_row = $total_boxes % $num_boxes_per_row;
    if ( $boxes_in_last_row == 0 ) $boxes_in_last_row = $num_boxes_per_row;
    
    $rows = (int) ceil( $total_boxes / $num_boxes_per_row );
    $row_counter = 0;
    $row_num = 0;
    
    $themes_instruments = get_posts( array ( 'numberposts' => 1000, 'category' => $current_category->term_id, 'post_type' => 'instrument' ) );

    ?>
    
    <div id="exhibit">
        <h2 class="exhibit-heading"><?php echo $current_category->name; ?></h2>
        <div class="exhibit-image"><?php echo get_the_term_thumbnail( $current_category->term_id, 'category', 'medium' ); ?></div>
        <div class="exhibit-description"><?php echo apply_filters( 'the_content', $current_category->category_description ); ?></div>
        <div class="clear"></div>
        
        <div id="themes">
            <?php
            
            //first print categories as boxes
            foreach ( $themes_categories as $theme_category ) {
                if ( $row_counter == 0 ) {
                    $row_num++;
                    if ( $row_num < $rows ) $the_style = "width: $total_width";
                    else {
                        $hanging_width = $box_width * ( $boxes_in_last_row );
                        $the_style = "width: $hanging_width";
                    }
                    echo "<div class='theme-row' style='$the_style'>";
                }
                category_box ( $theme_category, 'margin: 20px' );   
                $row_counter++;
                if ( $row_counter == $num_boxes_per_row || ( $row_num == $rows && $row_counter == $boxes_in_last_row ) ) {
                    echo '</div>';
                    $row_counter = 0;
                }
            }
            
            /*
            //then print posts in the same way
            foreach ( $themes_posts as $theme_post ) {
                if ( $row_counter == 0 ) {
                    $row_num++;
                    if ( $row_num < $rows ) $the_style = "width: $total_width";
                    else {
                       $hanging_width = $box_width * ( $boxes_in_last_row );
                        $the_style = "width: $hanging_width";
                    }
                    echo "<div class='theme-row' style='$the_style'>";
                }
                post_box ( $theme_post, 'margin: 20px' );
                $row_counter++;
                if ( $row_counter == $num_boxes_per_row || ( $row_num == $rows && $row_counter == $boxes_in_last_row ) ) {
                    echo '</div>';
                    $row_counter = 0;
                }
            }
            */
            
            ?>
        </div>
        
        <div class="clear">&nbsp;</div>
        
        <div id="override-links">
            <?php
            if ( count ( $themes_posts ) > 0 ) echo "<span id='override-posts'><a href='{$_SERVER['REDIRECT_URL']}?posts_only=1'>All Blog Posts</a></span>";
            if ( count ( $themes_instruments ) > 0 ) echo "<span id='override-instruments'><a href='{$_SERVER['REDIRECT_URL']}?instruments_only=1'>All Instruments</a></span>";
            ?>
        </div>
    </div>

<?php    
}

/**
 * Template for displaying an exhibit theme -- descendents of exhibits category that are not children.
 */
else if ( cat_is_ancestor_of ( $exhibits_category, $current_category ) && $posts_only == 0 ) {

    if ($current_category->term_id < 61 || $current_category->term_id > 65) { //hack to hide colour of science exhibit
    
        get_sidebar( 'exhibit' );
        
        ?>
        <div id="exhibit-theme">
            <h2 class="exhibit-theme-heading"><?php echo $current_category->name; ?></h2>
            <div class="exhibit-theme-image"><?php echo get_the_term_thumbnail( $current_category->term_id, 'category', 'medium' ); ?></div>
            <div class="exhibit-theme-description"><?php echo apply_filters( 'the_content', $current_category->category_description ); ?></div>
            <!--<div class="clear"></div>-->
    
        <?php
        
        echo '<div class="clear">&nbsp</div>';
        
        $row_counter = 1; //cycles between 1 and 0 to alternate row colours.
        
        ?>
        
        <?php
        //toggle between display posts and display instruments
        $themes_instruments = get_posts( array ( 'numberposts' => 2, 'category' => $current_category->term_id, 'post_type' => 'instrument' ) );
        $themes_posts = get_posts( array ( 'numberposts' => 2, 'category__in' => $current_category->term_id ) );
        if ( count ( $themes_posts ) > 0 && count ( $themes_instruments ) > 0 ) {
        
            if ( isset ( $_GET['toggle_instruments'] ) && $_GET['toggle_instruments'] == 1 ) {
                $toggle_instruments = 1;
                $toggle_post_class = 'toggle-unselected';
                $toggle_instrument_class = 'toggle-selected';
            }
            else {
                $toggle_instruments = 0;
                $toggle_post_class = 'toggle-selected';
                $toggle_instrument_class = 'toggle-unselected';
            }
            
            ?>
            
            <div id="exhibit-theme-toggle">
                <span id="exhibit-theme-toggle-post" class="<?php echo $toggle_post_class; ?>"><a href="<?php echo $_SERVER['REDIRECT_URL']; ?>">Posts</a></span>
                <span id="exhibit-theme-toggle-instrument" class="<?php echo $toggle_instrument_class; ?>"><a href="<?php echo $_SERVER['REDIRECT_URL']; ?>?toggle_instruments=1">Instruments</a></span>
            </div>
            
            <?php   
        
        }
        else if ( count ( $themes_instruments ) > 0 ) {
            $toggle_instruments = 1;
        }
        
            
        echo '<div id="exhibit-rows">';
        
        $q_string = "category_name={$current_category->slug}";
        
        if ( $toggle_instruments == 1) $q_string .= "&post_type=instrument";
        
        query_posts ( $q_string );
        while ( have_posts() ) {
            the_post();
            
            if ( $toggle_instruments == 1) instrument_row( $post, $row_counter ); 
            else post_row( $post, $row_counter );
            
            $row_counter = 1 - $row_counter;
        }
        wp_reset_query();
        
        echo "</div>"; //#exhibit-rows
        
        echo "</div>"; //#exhibit-theme
    
    }
}

/**
 * Default category page
 */
else {
    
    if ( $posts_only == 1 && cat_is_ancestor_of ( $exhibits_category, $current_category ) ) {
        get_sidebar( 'exhibit' );
    }
    
    
    echo '<div id="category">';
    if ( isset( $current_category->name ) ) echo '<h2 class="category-heading">' . $current_category->name . '</h2>';
    //$term_thumbnail = get_the_term_thumbnail( $current_category->term_id, 'category', 'medium' );
    //if ( $term_thumbnail ) echo '<div class="category-thumbnail">' . $term_thumbnail . '</div>';
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
    
}


?>



<?php get_footer(); ?>
