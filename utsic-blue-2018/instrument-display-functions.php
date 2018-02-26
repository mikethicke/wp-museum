<?php
/**
 * Displays instruments for category and search pages in row format.
 *
 * @param object $post Wordpress post object
 * @param integer $row_counter either 1 or -1, for alternating row styles
 */
function instrument_row ( $post, $row_counter ) {
    
    if ( $row_counter == 1 ) {
        $row_style = "instrument-archive-row-even";
    }
    else {
        $row_style = "instrument-archive-row-odd";
    }
    
    $custom = get_post_custom ( $post->ID );
    
    ?>
    <div class = "<?php echo $row_style; ?>">
        <div class="instrument-archive-thumbnail">
            <a href="<?php the_permalink( $post->ID ); ?>"> 
            <?php
            if ( has_post_thumbnail( $post->ID ) ) {
                 echo get_the_post_thumbnail( $post-> ID );   
            }
            else {
                ?> 
                <img src="<?php echo instrument_first_thumbnail( $post->ID ); ?>" /> 
            <?php
            }
            ?>
            </a>
        </div>
        <div class="instrument-text-wrapper">
            <div class="instrument-archive-title">
                <a href="<?php the_permalink( $post->ID ); ?>"><?php echo get_the_title( $post->ID ); ?></a>
            </div>
            <div class="instrument-archive-info">
                <span class="instrument-archive-info-category"><?php the_category ( ' &middot; ', '', $post->ID ); ?></span> &middot; 
                <span class="instrument-archive-info-an"><?php if ( isset( $custom['accession-number'] ) ) echo $custom['accession-number'][0]; ?></span>
            </div>
            <div class="instrument-archive-excerpt">
                <a href="<?php the_permalink( $post->ID ); ?> "><?php if ( isset( $custom['description'] ) ) echo custom_excerpt( $custom['description'][0], 50 ); ?></a>
            </div>
        </div>
    
    </div> <!-- .$row_style -->
    <?php
}

function post_row ( $post=null, $indent=0 ) {
    if ( is_null( $post ) ) global $post;
    
    global $post_row_toggle;
    if ( !isset( $post_row_toggle ) ) $post_row_toggle = 0;
    else $post_row_toggle = 1 - $post_row_toggle;
    
    if ( $post_row_toggle == 1 ) $row_style  = "theme-row-even";
    else $row_style = "theme-row-odd";
    
    if ( $indent > 0 ) {
        $row_style .= " indent-$indent";
    }
    
    $custom = get_post_custom( $post->ID );
    
    if ( isset( $custom['description'] ) && !empty( $custom['description'] ) ) $excerpt_text = custom_excerpt( $custom['description'][0], 50 );
    else $excerpt_text = get_the_excerpt( $post->ID );
    
    if ( has_post_thumbnail( $post->ID ) ) $thumbnail_img = get_the_post_thumbnail( $post->ID );
    else $thumbnail_img = '<img src="' . first_thumbnail( $post->ID ) . '">';
    
    ?>
    <div class = "<?php echo $row_style; ?>">
        <div class="theme-row-thumbnail">
            <a href="<?php the_permalink( $post->ID ); ?>"> <?php echo $thumbnail_img; ?></a>
        </div>
        <div class="theme-row-text-wrapper">
            <div class="theme-row-title">
                <a href="<?php the_permalink( $post->ID ); ?>"><?php echo get_the_title( $post->ID ); ?></a>
            </div>
            <div class="theme-excerpt">
                <a href="<?php the_permalink( $post->ID ); ?> "><?php echo $excerpt_text; ?></a>
            </div>
        </div>
    
    </div> <!-- .$row_style -->
    
    <?php
    
}

function post_rows ( $posts, $indent=0 ) {
    foreach ( $posts as $post ) {
        post_row ( $post, $indent );
    }
}


/**
 * Returns HTML for a single grid box, for displaying grid of instruments for
 * category and search pages.
 *
 * @param object $post Wordpress post object.
 * @param integer $row_counter either 1 or -1, for alternating styles.
 * @param integer $image_size Wordpress image size. Default: 'instrument_grid_thumb'
 * @param string $text 'show': show text 'hide': hide text
 * @return string HTML for a single grid box.
 */
function instrument_grid_box ( $post, $row_counter, $image_size='instrument_grid_thumb' ) {
    
    if ( $row_counter == 1 ) {
        $row_style = "instrument-grid-even";
    }
    else {
        $row_style = "instrument-grid-odd";
    }
  
    $custom = get_post_custom ( $post->ID );
    
    ob_start();
    ?>
    <div class="<?php echo $row_style; ?>">
        <div class="instrument-grid-image">
            <a href="<?php echo get_permalink( $post ); ?>" title="<?php echo $post->post_title; ?>">
            <?php
                $thumb_id = instrument_thumbnail_id ( $post->ID );
                if ( $thumb_id ) echo wp_get_attachment_image ( $thumb_id, $image_size );
            ?>
            </a>
        </div>
        <?php if ( $image_size == 'instrument_grid_thumb' ) { ?>
            <div class="instrument-grid-name"><a href="<?php echo get_permalink( $post ); ?>"><?php echo $post->post_title; ?></a></div>
            <div class="instrument-grid-info">
                <span class="instrument-grid-info-collection"><?php the_object_collections ( ' &middot; ', $post->ID );  ?> </span>
                <!-- <span class="instrument-grid-info-an"><?php echo $custom['accession-number'][0]; ?></span> -->
            </div>
        <?php } //if ?>
    </div><!-- .$row_style -->
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
}

/**
 * Shortcode to display instrument grid in a page or post.
 *
 * @param string[] $atts Associative array of attributes: cat="category"; num="number_of_instruments"; cols="columns"; rows="rows"; grow="false"
 * @return string HTML to be inserted in post.
 */
function instrument_grid_shortcode ( $atts ) {
    extract ( shortcode_atts ( array (
        'cat' => 'collections',
        'full' => 'false',
        'num' => 0
     ), $atts ) );
    
    //rows, cols, num should all be integers.
    $cols = 3;
    $num = (int) ceil ( $num );
    $rows = (int) ceil ( $num / $cols );
     
    //keep track of rows and cols
    $row_counter = 1;
    $col_counter = 0;
    $post_counter = 0;
    
    //get posts in cat, loop through
    $cat_obj = get_category_by_slug ( $cat );
    $cat_id = $cat_obj->term_id;
    global $post;
    query_posts ( "post_type=instrument&cat=$cat_id&posts_per_page=$num&paged=1" );
    while ( have_posts() ) {
        the_post();
        if ( $full == 'false' ) $image_size = 'photo-thumb';
        else $image_size = 'instrument_grid_thumb';
        $col_output[$col_counter] .= instrument_grid_box ( $post, $row_counter, $image_size );
        $row_counter = 1 - $row_counter;
        $col_counter = (1 + $col_counter ) % $cols;
        
        $post_counter++;
        if ( $post_counter >= $num ) {
            break;
        }
    }
    
    ob_start();
    
    print '<div class="clear">';
    print '<div class = "instrument-grid">';
    foreach ( $col_output as $output_column ) {
        print '<div class = "instrument-grid-col">';
        print $output_column;
        print '</div>';
    }
    print '</div></div>';
    
    $output = ob_get_clean();
    
    wp_reset_query();
    
    return $output;
}
add_shortcode ( 'instrument_grid', 'instrument_grid_shortcode' );

/**
 * Shortcode to display a single instrument box.
 */
function instrument_box_shortcode( $atts ){
    extract ( shortcode_atts ( array (
        'acc_num' => '',
        'full' => 'true',
        'float' => 'none'
    ), $atts ) );
    
    if ( $float == 'left' ) {
        $style = "float: left";
    }
    elseif ( $float == 'right' ) {
        $style = "float: right";
    }
    else {
        $style = "clear: both; margin-right: auto; margin-left: auto; width: 190px";
    }
    
    if ( $acc_num != '' ) {
        global $post;
        query_posts ( "post_type=instrument&meta_value=$acc_num" );
        if ( have_posts() ) {
            the_post();
            if ( $full == 'false' ) $image_size = 'photo-thumb';
            else $image_size = 'instrument_grid_thumb';
            $output = '<div class="instrument_grid_wrapper" style="' . $style . '">';
            $output .= instrument_grid_box ( $post, $row_counter, $image_size );
            $output .= '</div>';
        }
        
        return $output;
    }
    else return '';
}
add_shortcode( 'instrument_box', 'instrument_box_shortcode' );

/*
 * function post_box
 *
 * Display box with a post's thumbnail and title, linking to that post.
 *
 * @param $post
 * @param $css_class=''
 *   
 */
function post_box( $post, $css_class=''  ) {
    
    ?>
    <div class="category-box" style="<?php echo $css_class; ?>">
        <div class="category-box-image">
            <a href="<?php echo get_permalink( $post->ID ); ?>">
                <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
            </a>
        </div>
        <div class="category-box-title">
            <a href="<?php echo get_permalink( $post->ID ); ?>">
                <?php echo $post->post_title; ?>
            </a>
        </div>
    </div>
    
    <?php
     
}

function post_boxes ( $posts, $css_class='' ) {
    $box_width = get_option( 'thumbnail_size_w' ) + 40;
    $num_boxes_per_row = (int) floor( 830 / $box_width ); //830 is the text width, from style.css;
    $total_width = $box_width * $num_boxes_per_row;
    $total_boxes = count( $posts );
    
    $boxes_in_last_row = $total_boxes % $num_boxes_per_row;
    if ( $boxes_in_last_row == 0 ) $boxes_in_last_row = $num_boxes_per_row;
    
    $rows = (int) ceil( $total_boxes / $num_boxes_per_row );
    $row_counter = 0;
    $row_num = 0;
    
    foreach ( $posts as $post ) {
        if ( $row_counter == 0 ) {
            $row_num++;
            if ( $row_num < $rows ) $the_style = "width: $total_width";
            else {
                $hanging_width = $box_width * ( $boxes_in_last_row );
                $the_style = "width: $hanging_width";
            }
            echo "<div class='theme-row' style='$the_style'>";
        }
        post_box ( $post, 'margin: 20px' );   
        $row_counter++;
        if ( $row_counter == $num_boxes_per_row || ( $row_num == $rows && $row_counter == $boxes_in_last_row ) ) {
            echo '</div>';
            $row_counter = 0;
        }
    }
}