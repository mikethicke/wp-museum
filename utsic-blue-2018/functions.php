<?php

add_theme_support( 'post-thumbnails' );
add_theme_support('category-thumbnails');
register_nav_menu ( 'primary', 'Primary' );

/**
 * Admin settings.
 */ 
function utsic_admin_init() {
    //Register admin settings.
    //See: http://ottopress.com/2009/wordpress-settings-api-tutorial/
    register_setting('utsic_options', 'utsic_options', 'utsic_options_validate');
    add_settings_section( 'utsic_general_options_section', 'General Options', 'utsic_general_text', 'utsic_theme_options' );
    add_settings_section('utsic_options_section', 'Homepage Options', 'utsic_options_text', 'utsic_theme_options');
    add_settings_field( 'utsic_posts_per_page', 'Posts Per Category Page', 'utsic_posts_per_page_setting', 'utsic_theme_options', 'utsic_general_options_section' );
    add_settings_field('utsic_intro_blurb', 'Homepage Intro Paragraph', 'utsic_intro_blurb_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_1', 'Homepage First Item', 'utsic_homepage_box_1_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_1_title', 'Box 1 Link Title', 'utsic_homepage_box_1_title_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_1_image_url', 'Box 1 Image URL', 'utsic_homepage_box_1_image_url_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_2', 'Homepage Second Item', 'utsic_homepage_box_2_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_2_title', 'Box 2 Link Title', 'utsic_homepage_box_2_title_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_2_image_url', 'Box 2 Image URL', 'utsic_homepage_box_2_image_url_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_3', 'Homepage Third Item', 'utsic_homepage_box_3_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_3_title', 'Box 3 Link Title', 'utsic_homepage_box_3_title_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_3_image_url', 'Box 3 Image URL', 'utsic_homepage_box_3_image_url_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_feed', 'Category for Homepage Feed', 'utsic_homepage_feed_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_donate_url', 'URL For Donate Button', 'utsic_donate_url_setting', 'utsic_theme_options', 'utsic_options_section');
    
    //Media Uploader Scripts
    //Allows image upload on theme options page.
    //See: http://www.justinwhall.com/multiple-upload-inputs-in-a-wordpress-theme-options-page/
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_register_script('my-upload', get_bloginfo( 'stylesheet_directory' ) . '/uploader.js', array('jquery','media-upload','thickbox'));
    
    //Media Uploader Style
    wp_enqueue_style('thickbox');
}
add_action('admin_init', 'utsic_admin_init');
add_action('widgets_init', 'utsic_register_widgets');

/**
 * Fix rewrite rules to make permalinks work with instrument post type.
 * Source: http://wordpress.org/support/topic/custom-post-types-permalinks (wpAdm)
 */ 
function my_rewrite() {
    global $wp_rewrite;
    $wp_rewrite->add_permastruct('typename', 'typename/%year%/%postname%/', true, 1);
    add_rewrite_rule('typename/([0-9]{4})/(.+)/?$', 'index.php?typename=$matches[2]', 'top');
    $wp_rewrite->flush_rules(); // !!!
}
add_action('init', 'my_rewrite');

set_post_thumbnail_size ( 150, 150, false );

//image size for thumbnails in instrument grid category and search pages
add_image_size ( "instrument_grid_thumb", 190, 1000 );

//image size for homepage content boxes
add_image_size ( "homepage_thumb", 235, 200 );

//keep track of whether to display grid or list view in search and category pages
session_start();
if ( isset ( $_GET['grid'] ) && $_GET['grid'] == 1 ) {
    $_SESSION['grid'] = 1;
}
elseif ( isset ( $_GET['grid'] ) && $_GET['grid'] == -1 ) {
    $_SESSION['grid'] = -1;
}
elseif ( !isset ( $_SESSION['grid'] ) ) {
    $_SESSION['grid'] = -1;
}

/**
 * Based on wp_trim_excerpt (formatting.php).
 * Trims $text to $length words, and appends "..." if initial length > $length.
 * Used to display excerpts of instrument descriptions.
 *
 * @param string $text text to be trimmed
 * @param integer $length length to be trimmed to (55)
 * @return string the trimmed text
 */
function custom_excerpt ( $text, $length = 55 ) {
    $raw_excerpt = $text;
    
    $text = strip_shortcodes ( $text );
    $text = apply_filters ( 'the_content', $text );
    $text = str_replace(']]>', ']]&gt;', $text);
    $excerpt_length = apply_filters('excerpt_length', $length);
    $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
    $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
    
    return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

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
            <a href="<?php the_permalink(); ?>"> 
            <?php
            if ( has_post_thumbnail() ) {
                 the_post_thumbnail();   
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
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </div>
            <div class="instrument-archive-info">
                <span class="instrument-archive-info-category"><?php the_category ( ' &middot; '); ?></span> &middot; 
                <span class="instrument-archive-info-an"><?php echo $custom['accession-number'][0]; ?></span>
            </div>
            <div class="instrument-archive-excerpt">
                <a href="<?php the_permalink(); ?> "><?php echo custom_excerpt( $custom['description'][0], 50 ); ?></a>
            </div>
        </div>
    
    </div> <!-- .$row_style -->
    <?php
}

/**
 * Displays rows of post excerpts
 *
 */
function post_row( $post, $row_counter, $indent=0 ) {
    if ( $row_counter == 1 ) {
        $row_style = "theme-row-even";
    }
    else {
        $row_style = "theme-row-odd";
    }
    
    if ( $indent > 0 ) {
        $row_style .= " indent-$indent";
    }
    
    $thumbnail_present = true;
    
    ?>
    <div class = "<?php echo $row_style; ?>">
        <?php if ( has_post_thumbnail() ) { ?>
            <div class="theme-row-thumbnail">
                <a href="<?php the_permalink(); ?>"> 
                <?php
                if ( has_post_thumbnail($post) ) {
                     echo get_the_post_thumbnail($post);   
                }
                
                ?>
                </a>
            </div>
        <?php }
        else $thumbnail_present = false;
        ?>
        <div <?php if ( $thumbnail_present ) echo 'class="theme-row-text-wrapper"'; else echo 'class="theme-row-no-thumbnail"'; ?>>
            <div class="theme-row-title">
                <a href="<?php the_permalink($post); ?>"><?php echo get_the_title($post); ?></a>
            </div>
            <div class="theme-excerpt">
                <a href="<?php the_permalink($post); ?> "><?php echo get_the_excerpt($post); ?></a>
            </div>
        </div>
    
    </div> <!-- .$row_style -->
    <?php
}

function post_rows ( $posts, $indent=0 ) {
    $row_counter = 1;
    
    foreach ( $posts as $post ) {
        post_row ( $post, $row_counter, $indent );
        $row_counter = 1 - $row_counter;
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
            <a href="<?php the_permalink(); ?>" title="<?php echo $post->post_title; ?>">
            <?php
                $thumb_id = instrument_thumbnail_id ( $post->ID );
                if ( $thumb_id ) echo wp_get_attachment_image ( $thumb_id, $image_size );
            ?>
            </a>
        </div>
        <?php if ( $image_size == 'instrument_grid_thumb' ) { ?>
            <div class="instrument-grid-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
            <div class="instrument-grid-info">
                <span class="instrument-grid-info-collection"><?php the_category ( ' &middot; ');  ?> &middot; </span>
                <span class="instrument-grid-info-an"><?php echo $custom['accession-number'][0]; ?></span>
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


/**
 * Adds theme options page to admin menu.
 * See: http://ottopress.com/2009/wordpress-settings-api-tutorial/
 */
function add_theme_options_page() {
    add_theme_page('UTSIC Theme Options', 'UTSIC Theme Options', 'manage_options', 'utsic_theme_options', 'utsic_options_page');
}
add_action('admin_menu', 'add_theme_options_page');


/**
 * Creates options page for UTSIC Theme.
 * See: http://ottopress.com/2009/wordpress-settings-api-tutorial/
 */
function utsic_options_page() {
    ?>
    <div>
    <h2>UTSIC Theme Options</h2>
    <form action="options.php" method="post">
    <?php wp_enqueue_script('my-upload'); ?>
    <?php settings_fields('utsic_options'); ?>
    <?php do_settings_sections('utsic_theme_options'); ?>
    
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form></div>
    
    <?php    
}

/**
 * Callback to display text for UTSIC Options section.
 */
function utsic_options_text() {
    ?>
    <div class="options_section_description">
        Homepage boxes are left-to-right. If the Image URL is left blank, the item's featured image will be used instead if it exists.
    </div>
    <?php
}


/**
 * Callback to display form section for intro blurb
 */
function utsic_intro_blurb_setting() {
    $options = get_option('utsic_options');
    ?>
    <textarea cols="70" rows="10" id="utsic_intro_blurb" name="utsic_options[utsic_intro_blurb]"><?php echo $options['utsic_intro_blurb']; ?></textarea>
    <?php
}

/**
 * Callback to display form section for posts per page
 */
function utsic_posts_per_page_setting() {
    $options = get_option('utsic_options');
    ?>
    <input id="utsic_posts_per_page" type="text" name="utsic_options[utsic_posts_per_page]" size="5" value="<?php echo $options['utsic_posts_per_page']; ?>" />
    <?php
}


/**
 * Callback to display form section for donate url
 */
function utsic_donate_url_setting() {
    $options = get_option('utsic_options');
    ?>
    <input id="utsic_donate_url" type="text" name="utsic_options[utsic_donate_url]" size="70" value="<?php echo $options['utsic_donate_url']; ?>" />
    <?php
}

/**
 * Common code for homepage box setting callback functions
 */
function utsic_homepage_box_settings($box_number) {
    $options = get_option('utsic_options');
    $box_string = 'utsic_homepage_box_' . $box_number;
    ?>
    <select id="<?php echo $box_string;?>" name="utsic_options[<?php echo $box_string;?>]">
        <optgroup label="Posts">
            <?php
            $posts = get_posts( array( 'numberposts' => '1000000' ) );
            foreach ($posts as $post) {
                ?>
                <option value="post-<?php echo $post->ID;?>" <?php if ($options[$box_string] == 'post-' . $post->ID) echo 'selected = "selected"';?>>
                    <?php echo $post->post_title;?>
                </option>
                <?php
            }
            ?>
        </optgroup>
        <optgroup label="Pages">
            <?php
            $pages = get_pages();
            foreach ($pages as $page) {
                ?>
                <option value="page-<?php echo $page->ID;?>" <?php if ($options[$box_string] == 'page-' . $page->ID) echo 'selected = "selected"';?>>
                    <?php echo $page->post_title;?>
                </option>
                <?php
            }
            ?>
        </optgroup>
        <optgroup label="Categories">
            <?php
            //$collections_category = get_category_by_slug ( 'collections' ); array('child_of'=>$collections_category->cat_ID)
            $categories = get_categories();
            foreach ($categories as $category) {
                ?>
                <option value="category-<?php echo $category->cat_ID;?>" <?php if ($options[$box_string] == 'category-' . $category->cat_ID) echo 'selected = "selected"';?>>
                    <?php echo $category->name;?>
                </option>
                <?php
            }
            ?>
        </optgroup>
    </select>
    <?php
}

/**
 * Common code for homepage box image upload
 */
function utsic_homepage_box_image_settings($box_number) {
    $options = get_option('utsic_options');
    $box_string = 'utsic_homepage_box_' . $box_number . '_image_url';
    
    ?>
    <input type="text" size="70" id="<?php echo $box_string;?>" class="upload_url" name="utsic_options[<?php echo $box_string;?>]" value="<?php echo $options[$box_string];?>" />
    <input type="button" id="button-<?php echo $box_number;?>" class="st_upload_button" name="button-<?php echo $box_number;?>" value="Upload" />
    <?php
}

/**
 * Callback to disply form section for first homepage box selection.
 */ 
function utsic_homepage_box_1_setting() {
    utsic_homepage_box_settings(1);
}

/**
 * Callback to disply form section for second homepage box selection.
 */ 
function utsic_homepage_box_2_setting() {
    utsic_homepage_box_settings(2);
}

/**
 * Callback to disply form section for third homepage box selection.
 */ 
function utsic_homepage_box_3_setting() {
    utsic_homepage_box_settings(3);
}

/**
 * Callback to display form section for uploading first hompage box image. 
 */
function utsic_homepage_box_1_image_url_setting() {
    utsic_homepage_box_image_settings(1);
}

/**
 * Callback to display form section for uploading second hompage box image. 
 */
function utsic_homepage_box_2_image_url_setting() {
    utsic_homepage_box_image_settings(2);
}

/**
 * Callback to display form section for uploading third hompage box image. 
 */
function utsic_homepage_box_3_image_url_setting() {
    utsic_homepage_box_image_settings(3);
}

/**
 * Callback to display category selection for homepage feed. 
 */
function utsic_homepage_feed_setting() {
    $options = get_option('utsic_options');
    ?>
    <select id="utsic_homepage_feed" name="utsic_options[utsic_homepage_feed]">
        <?php
        $categories = get_categories();
        foreach ($categories as $category) {
            ?>
            <option value="<?php echo $category->cat_ID;?>" <?php if ($options['utsic_homepage_feed'] == $category->cat_ID) echo 'selected = "selected"';?>>
                <?php echo $category->name;?>
            </option>
            <?php
        }
        ?>
    </select>
   <?php 
}

/**
 * Callback to display title input for first homepage box.
 */
function utsic_homepage_box_1_title_setting() {
    $options = get_option('utsic_options');
    ?>
    <input type="text" size="70" id="utsic_box_1_title" name="utsic_options[utsic_homepage_box_1_title]" value="<?php echo $options['utsic_homepage_box_1_title'];?>" />
    <?php
}

/**
 * Callback to display title input for second homepage box.
 */
function utsic_homepage_box_2_title_setting() {
    $options = get_option('utsic_options');
    ?>
    <input type="text" size="70" id="utsic_box_2_title" name="utsic_options[utsic_homepage_box_2_title]" value="<?php echo $options['utsic_homepage_box_2_title'];?>" />
    <?php
}

/**
 * Callback to display title input for third homepage box.
 */
function utsic_homepage_box_3_title_setting() {
    $options = get_option('utsic_options');
    ?>
    <input type="text" size="70" id="utsic_box_3_title" name="utsic_options[utsic_homepage_box_3_title]" value="<?php echo $options['utsic_homepage_box_3_title'];?>" />
    <?php
}

/**
 * Callback to validate theme options. 
 */
function utsic_options_validate($input) {
    $options = get_option('utsic_options');
    
    foreach ($input as $key => $value) {
            $options[$key] = trim($value);
    }
    
    return $options;
}

//Displays content buckets on front page. $box_number is used by style.css to position boxes.
function fill_feature_box($box_number) {
    $options = get_option('utsic_options');
    $gllr_options = get_option( 'gllr_options' );
    $box_string = 'utsic_homepage_box_' . $box_number;
    $title_string = $box_string . '_title';
    $image_url_string = $box_string .'_image_url';
    
    $object_array = explode('-',$options[$box_string]);
    $object_type = $object_array[0];
    $object_ID = $object_array[1];
    
    $attachments = get_posts(array(
            "showposts"	    => -1,
            "what_to_show"	    => "posts",
            "post_status"	    => "inherit",
            "post_type"         => "attachment",
            "orderby"	    => $gllr_options['order_by'],
            "order"		    => $gllr_options['order'],
            "post_mime_type"    => "image/jpeg,image/gif,image/jpg,image/png",
            "post_parent"	    => $object_ID
        ));
    
    if (count($attachments) > 0) $attachment_ID = $attachments[0]->ID;
    if (isset ($attachment_ID)) $object_image = wp_get_attachment_image($attachment_ID, "homepage_thumb");
    
    if ($object_type == 'post') {
        $the_post = get_post($object_ID);
        $object_title = $the_post->post_title;
        $object_url = get_permalink($the_post->ID);
    }
    elseif ($object_type == 'page') {
        $the_page = get_page($object_ID);
        $object_title = $the_page->post_title;
        $object_url = get_permalink($the_page->ID);
    }
    elseif ($object_type == 'category') {
        $the_category = get_category($object_ID);
        $object_title = $the_category->cat_name;
        $object_url = get_category_link($object_ID);
    }
    
    if (is_null($options[$title_string])) $box_title = $object_title;
    else $box_title = $options[$title_string];
    
    if (is_null($options[$image_url_string])) $image_src = $object_image;
    else $image_src = '<img src="' . $options[$image_url_string] . '" />';
    
    ?>
    <div class="homepage-box-content">
        <div class="homepage-box-image"><a href="<?php echo $object_url; ?>"><?php echo $image_src; ?></a></div>
        <div class="homepage-box-title"><a href="<?php echo $object_url; ?>"><?php echo $box_title; ?></a></div>
    </div>
    <?php
}

function new_excerpt_more($more) {
       global $post;
	return ' <a href="'. get_permalink($post->ID) . '">[Read More...]</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');

/**
 * Sidebars
 */

register_sidebar( array (
    'name'  => 'Collections Sidebar',
    'id'    => 'collections_sidebar',
) );

register_sidebar( array (
    'name'  => 'Exhibit Sidebar',
    'id'    => 'exhibit_sidebar'
) );

register_sidebar ( array (
    'name'  => 'Description Page Sidebar',
    'id'    => 'description_page_sidebar'
) );

register_sidebar ( array (
    'name'  => 'Instrument Sidebar',
    'id'    => 'instrument_sidebar'
) );


/**
 * Adds search widget.
 */
class UTSIC_Search_Collections_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_search_collections_widget', // Base ID
			'UTSIC Search Collections Widget', // Name
			array( 'description' => __( 'Widget for searching instrument collections', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
	    
		echo $before_widget;
                
                $current_category = get_queried_object();
                if (is_category ($current_category)) $catID = $current_category->cat_ID;
                else $catID = 0;
		
                ?>
                <form role="search" method="get" id="searchform" action="<?php echo site_url(); ?>" >
                    <div>
                        <label class="screen-reader-text" for="s">Search for:</label>
                        <input type="text" placeholder="search the collection..." name="s" id="s" /><br />
                        <input type="radio" name="within_collection" value="entire_collection" checked="checked"/>Search entire collection<br />
                        <input type="radio" name="within_collection" value="<?php echo $catID ?>" />Search within collection<br />
                        <input type="submit" id="searchsubmit" value="Search" />
                    </div>
                </form>
                <?php
                
                
		echo $after_widget;
	}

} // class UTSIC_Search_Widget

/**
 * Adds exhibit tree widget.
 */
class UTSIC_Exhibit_Tree_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_exhibit_tree_widget', // Base ID
			'UTSIC Exhibit Tree Widget', // Name
			array( 'description' => __( 'Widget for displaying hierarcy of themes and posts within an exhibit.', 'text_domain' ), ) // Args
		);
	}
        
        /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'html_follow' ] ) ) {
			$html_follow = $instance[ 'html_follow' ];
		}
		else {
			$html_follow = __( '', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'html_follow' ); ?>"><?php _e( 'Following HTML:' ); ?></label> 
		<textarea rows=15 cols=30 id="<?php echo $this->get_field_id( 'html_follow' ); ?>" name="<?php echo $this->get_field_name( 'html_follow' ); ?>"><?php echo esc_attr( $html_follow ); ?></textarea>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
                //currently just trust given input. Don't want to strip HTML.
                $instance['html_follow'] = $new_instance['html_follow'];

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
                global $post;
	    
		echo $before_widget;
                echo '<div id="exhibit-tree-wrapper">';
                
                //figure out which exhibit we are in.
                $current_object = get_queried_object();
                if ( is_single( $current_object->ID ) ) {
                    $categories = get_the_category();
                    //Hopefully there is only one category. Assume we are in the first. To Do: have a more sophisticated detection system.
                    $current_category = $categories[0];
                }
                elseif ( is_category( $current_object->ID ) ) {
                    $current_category = $current_object;
                }
                else {
                    //this shouldn't ever happen.
                    echo '</div>';
                    return false;
                }
                $exhibits_category = get_category_by_slug( 'exhibits' );
                $exhibits_children = get_categories( array ( 'parent' => $exhibits_category->term_id, 'hide_empty' => 0 ) );
                $is_parent_exhibit = 0;
                foreach ( $exhibits_children as $exhibit ) {
                    if ( cat_is_ancestor_of( $exhibit, $current_category ) ) $the_exhibit = $exhibit;
                    else if ( $exhibit->term_id == $current_category->term_id ) {
                        $the_exhibit = $exhibit;
                        $is_parent_exhibit = 1;
                    }
                }
                if ( ! isset( $the_exhibit ) ) {
                    echo '</div>';
                    return false;
                }
                
                echo sprintf ("<h3><a href='%s'>%s</a></h3>", get_category_link( $the_exhibit->term_id ), $the_exhibit->name);
                
                //list each theme, and list the posts within the current theme
                $the_exhibit_themes = get_categories( array ( 'parent' => $the_exhibit->term_id, 'hide_empty' => 0 ) );
                echo '<ul class="sidebar-exhibit-theme-list">';
                foreach ( $the_exhibit_themes as $the_theme ) {
                    echo sprintf( "<li><a href='%s'>%s</a>", get_category_link( $the_theme->term_id ), $the_theme->name );
                    if ( $the_theme->term_id == $current_category->term_id ) {
                        query_posts ("category_name={$current_category->slug}" );
                        echo '<ul class="sidebar-theme-post-list">';
                        while ( have_posts() ) {
                            the_post();
                            echo '<li>';
                            if ( isset( $current_object->post_type ) && $current_object->post_type == 'post' && $current_object->ID == $post->ID ) {
                                the_title();
                            }
                            else {
                                echo sprintf( "<a href='%s'>%s</a>", get_permalink(), get_the_title() );
                            }
                            echo '</li>';
                        }
                        echo '</ul>';
                        wp_reset_query();
                    }
                    echo '</li>';
                }
                
                
                echo '</ul>';
                
                echo $instance['html_follow'];
                
                if ( $the_exhibit->name == 'The Colour of Science' ) {
                    ?>
                    
                    <h3><a href='https://utsic.escalator.utoronto.ca/home/blog/2014/01/21/the-making-of-the-colour-of-science-exhibition/'>The Making of The Colour of Science</a></h3>
                    <h3><a href='https://utsic.escalator.utoronto.ca/home/blog/2014/02/19/colour-of-science-partners/'>Our Partners</a></h3>
                    
                    <?php    
                }
                
                
                echo '</div>'; //#exhibit-tree-wrapper
		echo $after_widget;
	}

} // class UTSIC_Search_Widget

/**
 * Adds exhibit tree widget.
 */
class UTSIC_Collections_Tree_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_collections_tree_widget', // Base ID
			'UTSIC Collections Tree Widget', // Name
			array( 'description' => __( 'Widget for displaying hierarcy of instrument collections.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
                global $post;
	    
		echo $before_widget;
                echo '<div id="collections-tree-wrapper">';
                
                $collections_category = get_category_by_slug( 'collections' );
                
                $current_object = get_queried_object();
                
                //Determine which category we are in.
                if ( is_single( $current_object->ID ) ) {
                    $categories = get_the_category();
                    //Assume we are in the last category.
                    $current_category = $categories[count( $categories ) - 1];
                }
                elseif ( is_category( $current_object->ID ) ) {
                    $current_category = $current_object;
                }
                else {
                    //this shouldn't ever happen.
                    echo '</div>';
                    return false;
                }
                
                //Collections category
                if ( $current_object->ID == $collections_category->term_id ) {
                    echo sprintf( "<h3>%s</h3>", $collections_category->name );
                }
                else {
                    echo sprintf( "<h3><a href='%s'>%s</a></h3>", get_category_link( $collections_category->term_id ), $collections_category->name );
                }
                
                //Collections
                
                
                
                
                echo '</div>'; //#collections-tree-wrapper
		echo $after_widget;
	}
        
        /**
        * Recursive helper for printing collections and sub-collections
        * Aug 26 2016: I don't think I use this anywhere and it doesn't do anything? 
        */
        private function recursive_collections( $parent_collection ) {
            
            $children = get_categories( array ( 'parent' => $parent_collection->term_id ) );
            
            foreach ( $children as $child ) {
                    
                
            }
        }

} // class UTSIC_Search_Widget

/**
 * Adds collections description page link widget that links from collections and instruments to
 * the appropriate description page.
 */
class UTSIC_Collections_Description_Page_Link_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_collections_description_page_link_widget', // Base ID
			'UTSIC Collections Description Page Link Widget', // Name
			array( 'description' => __( 'Widget for linking to description page for instrument or collection.', 'text_domain' ), ) // Args
		);
	}
        
         /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'link_text' ] ) ) {
			$link_text = $instance[ 'link_text' ];
		}
		else {
			$link_text = __( '', 'text_domain' );
		}
		?>
		<p>
                    <label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link text. Can use %collection%. (eg. "Read about the %collection% collection."):' ); ?></label><br />
                    <input id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $link_text ); ?>" />
                    </p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
                //currently just trust given input. Don't want to strip HTML.
                $instance['link_text'] = sanitize_text_field($new_instance['link_text']);

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
                global $post;
	    
		echo $before_widget;
                
                //get collection
                $collections_category = get_category_by_slug( 'collections' );
                $current_object = get_queried_object();
                $obj_ID = $current_object->term_id;
                
                
                //Determine which category we are in.
                if ( is_single( $obj_ID ) ) {
                    $categories = get_the_category();
                }
                elseif ( is_category( $obj_ID ) ) {
                    $categories = array($current_object);
                }
                else {
                    //this shouldn't ever happen.
                    return false;
                }
                
                foreach ( $categories as $current_category ) {
                    $cat_ID = $current_category->term_id;
                    $cat_meta = get_option ("category_$cat_ID");
                    //link to the description page
                    $link_text = get_post_meta($cat_meta['description_page'], 'link_text', true);
                    if ($link_text != '') {
                        $link_text = preg_replace ("/%collection%/", $current_category->name, $link_text);
                    }
                    else {
                        $link_text = preg_replace ("/%collection%/", $current_category->name, $instance['link_text']);
                    }
                    if ($link_text == '') {
                        $link_text = "Read about the {$current_category->name} collection";   
                    }
                    
                    echo sprintf ("<p><a class=\"collections_description_page_link\" href=\"%s\">%s</a></p>", get_page_link ($cat_meta['description_page']), $link_text);
                }
                
                     
                
		echo $after_widget;
	}
    
} // class UTSIC_Collections_Description_Page_Link_Widget

/**
 * Adds collections description page link widget that links from collections and instruments to
 * the appropriate description page.
 */
class UTSIC_Link_To_Collection_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_link_to_collection_widget', // Base ID
			'UTSIC Link To Collection Widget', // Name
			array( 'description' => __( 'Widget for linking to collection from a description page.', 'text_domain' ), ) // Args
		);
	}
        
        /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'link_text' ] ) ) {
			$link_text = $instance[ 'link_text' ];
		}
		else {
			$link_text = __( '', 'text_domain' );
		}
		?>
		<p>
                    <label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Default link text. Can use %collection%. (eg. "View the %collection% collection."):' ); ?></label><br />
                    <input id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $link_text ); ?>" />
                    </p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
                //currently just trust given input. Don't want to strip HTML.
                $instance['link_text'] = sanitize_text_field($new_instance['link_text']);

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget ( $args, $instance ) {
		extract ( $args );
                global $post;
                $current_object = get_queried_object();
	    
		echo $before_widget;
                
                //get categories with this page as description page
                $categories = get_categories();
                
                foreach ($categories as $category) {
                    $cat_meta = get_option ("category_{$category->term_id}");
                    if ($cat_meta != '' && $cat_meta['description_page'] == $current_object->ID) {
                            //link to the description page
                            if ($cat_meta['link_text'] == '') {
                                $link_text = preg_replace ("/%collection%/", $category->name, $instance['link_text']);
                            }
                            else {
                                $link_text = preg_replace ("/%collection%/", $category->name, $cat_meta['link_text']);
                            }
                            if ($link_text == '') {
                                $link_text = "Browse the {$category->name} collection";   
                            }
                            $link_url = esc_url(get_category_link($category->term_id));
                            echo sprintf ("<p><a class=\"collections_description_page_link\" href=\"%s\">%s</a></p>", $link_url, $link_text);
                        }
                }
                     
                
		echo $after_widget;
	}
    
} // class UTSIC_Link_To_Collection_Widget


function utsic_register_widgets() {
    register_widget ( 'utsic_search_collections_widget' );
    register_widget ( 'utsic_exhibit_tree_widget' );
    register_widget ( 'utsic_collections_tree_widget' );
    register_widget ( 'utsic_collections_description_page_link_widget' );
    register_widget ( 'utsic_link_to_collection_widget' );
}

/**
 * Checks if current category is a child (direct descendent) of a category.
 *
 * @param object $parent_category (Potential) parent category.
 * @param object $current_category Current category (potential child).
 * @return bool True if $current_category is child of $parent_category, false otherwise.
 */
function cat_is_child_of( $parent_category, $current_category) {
    
    $child_categories = get_categories( array ( 'parent' => $parent_category->term_id, 'hide_empty' => 0 ) );
    
    foreach ( $child_categories as $child ) {
        if ( $current_category->term_id == $child->term_id ) return true;
    }
    
    return false;
}

/**
 * Display box with a category's thumbnail and title, linking to that category
 *
 * @param object $category The category to be displayed.
 * @param string $css_style='' CSS style for box.
 * @return string HTML of box.
 */
function category_box( $category, $css_class='' ) {
    
    ?>
    <div class="category-box" style="<?php echo $css_class; ?>">
        <div class="category-box-image">
            <a href="<?php echo get_category_link( $category->term_id ); ?>">
                <?php echo get_the_term_thumbnail( $category->term_id, 'category', 'thumbnail' ); ?>
            </a>
        </div>
        <div class="category-box-title">
            <a href="<?php echo get_category_link( $category->term_id ); ?>">
                <?php echo $category->name; ?>
            </a>
        </div>
    </div>
    
    <?php
    
}

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

/*
 * function add_category_page_option
 *
 * Adds an option box to the edit category page to associate a page with the category.
 * When instruments in the category are displayed there is a link to that page.
 *
 * Based on: http://wordpress.stackexchange.com/questions/8736/add-custom-field-to-category
 *
 * @param $tag The category being edited
 *   
 */
function add_category_page_option( $tag  ) {    
    $term_id = $tag->term_id;
    $cat_meta = get_option( "category_$term_id" );
    
    //get pages with template "Collection Description Template"
    $collection_pages = get_pages ( /* array(  'meta_key' => '_wp_page_template', 'meta_value' => 'collection-description-template' ) */ );
    
    
    
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="cat_page">Category description page</label></th>
        <td>
            <select id="cat_meta[descrition_page]" name="cat_meta[description_page]">
                <option value="none" <?php if ( $cat_meta['description_page'] == 'none' ) echo 'selected = "selected"';?>>
                            None
                </option>
                <?php
                foreach ( $collection_pages as $c_page ) {
                    $template_name = get_post_meta( $c_page->ID, '_wp_page_template', true );
                    if ( $template_name == "collection-description-template.php" ||
                            $c_page->post_name = "collections") {
                        ?>
                        <option value="<?php echo $c_page->ID;?>" <?php if ( $cat_meta['description_page'] == $c_page->ID ) echo 'selected = "selected"';?>>
                            <?php echo $c_page->post_title;?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row" valign="top"><label for="cat_page">Link text</label></th>
        <td>
            <input type="text" id="cat_meta[link_text]" name="cat_meta[link_text]" value="<?php echo $cat_meta['link_text']; ?>">
            <p class="description">Text to use when linking to this category in sidebar. Overrides widget setting. Can use %collection% for category name. Eg. "View the %collection% collection."</p>   
        </td>
    </tr>
    
    <?php
}
add_action ( 'edit_category_form_fields', 'add_category_page_option');

/*
 * function save_category_page_option
 *
 * Save the page selected for a category (add_category_page_option).
 *
 * Based on: http://wordpress.stackexchange.com/questions/8736/add-custom-field-to-category
 *
 * @param $term_id ID of the edited category
 *   
 */
function save_category_page_option( $term_id  ) {
    if ( isset( $_POST['cat_meta'] ) ) {
        $cat_meta = get_option( "category_$term_id");
        $cat_keys = array_keys($_POST['cat_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['cat_meta'][$key])){
                $cat_meta[$key] = $_POST['cat_meta'][$key];
            }
        }
        //save the option array
        update_option( "category_$term_id", $cat_meta );
    }
}
add_action ( 'edited_category', 'save_category_page_option');

/**
 * Tests if any of a post's assigned categories are descendants of target categories
 *
 * @param int|array $cats The target categories. Integer ID or array of integer IDs
 * @param int|object $_post The post. Omit to test the current post in the Loop or main query
 * @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
 * @see get_term_by() You can get a category by name or slug, then pass ID to this function
 * @uses get_term_children() Passes $cats
 * @uses in_category() Passes $_post (can be empty)
 * @version 2.7
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
 */
if ( ! function_exists( 'post_is_in_descendant_category' ) ) {
	function post_is_in_descendant_category( $cats, $_post = null ) {
		foreach ( (array) $cats as $cat ) {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $cat, 'category' );
			if ( $descendants && in_category( $descendants, $_post ) )
				return true;
		}
		return false;
	}
}

function addUploadMimes($mimes) {
    $mimes = array_merge($mimes, array(
        'csv' => 'text/csv'
    ));
 
    return $mimes;
}

add_filter('upload_mimes', 'addUploadMimes');


?>
