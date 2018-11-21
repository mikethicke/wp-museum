<?php
/**
 * General functions for UTSIC Blue Theme
 */


require_once( 'wpm-functions.php' );
require_once( 'widgets/collection-toggle-widget.php' );
require_once( 'widgets/collections-tree-widget.php' );
require_once( 'widgets/exhibit-tree-widget.php' );
require_once( 'widgets/search-collections-widget.php' );
require_once( 'instrument-display-functions.php' );
require_once( 'frontpage-functions.php' );
require_once( 'options-page.php' );

add_theme_support( 'post-thumbnails' );
add_theme_support('category-thumbnails');
register_nav_menu ( 'primary', 'Primary' );

if ( session_status() != PHP_SESSION_ACTIVE ) session_start();

/**
 * Admin settings.
 */ 
function utsic_admin_init() {
    //Register admin settings.
    //See: http://ottopress.com/2009/wordpress-settings-api-tutorial/
    register_setting('utsic_options', 'utsic_options', 'utsic_options_validate');
    //add_settings_section( 'utsic_general_options_section', 'General Options', 'utsic_general_text', 'utsic_theme_options' );
    add_settings_section('utsic_options_section', 'Homepage Options', 'utsic_options_text', 'utsic_theme_options');
    add_settings_field( 'utsic_posts_per_page', 'Posts Per Category Page', 'utsic_posts_per_page_setting', 'utsic_theme_options', 'utsic_options_section' );
    add_settings_field('utsic_intro_blurb', 'Homepage Intro Paragraph', 'utsic_intro_blurb_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_1', 'Homepage Box 1 URL', 'utsic_homepage_box_1_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_1_title', 'Box 1 Link Title', 'utsic_homepage_box_1_title_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_1_image_url', 'Box 1 Image URL', 'utsic_homepage_box_1_image_url_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_2', 'Homepage Box 2 URL', 'utsic_homepage_box_2_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_2_title', 'Box 2 Link Title', 'utsic_homepage_box_2_title_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_2_image_url', 'Box 2 Image URL', 'utsic_homepage_box_2_image_url_setting', 'utsic_theme_options', 'utsic_options_section');
    add_settings_field('utsic_homepage_box_3', 'Homepage Box 3 URL', 'utsic_homepage_box_3_setting', 'utsic_theme_options', 'utsic_options_section');
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

flush_rewrite_rules( false );

set_post_thumbnail_size ( 150, 150, false );

//image size for thumbnails in instrument grid category and search pages
add_image_size ( "instrument_grid_thumb", 190, 1000 );

//image size for homepage content boxes
add_image_size ( "homepage_thumb", 235, 200 );

//image size for instrument gallery (allows non-square aspect ratios)
add_image_size ( "object_gallery_thumb", 160, 1000 );

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

register_sidebar ( array (
    'name'  => 'Default Sidebar',
    'id'    => 'default_sidebar'
) );


/**
 * Widgets
 */
function utsic_register_widgets() {
    register_widget ( 'utsic_search_collections_widget' );
    register_widget ( 'utsic_exhibit_tree_widget' );
    register_widget ( 'utsic_collections_tree_widget' );
    register_widget ( 'utsic_collection_toggle_widget' );
}
add_action('widgets_init', 'utsic_register_widgets');

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

/**
 * Allows CSV files to be uploaded. Necessary for import of CSV files to database.
 * (Why is this in theme not plugin?)
 */
function addUploadMimes($mimes) {
    $mimes = array_merge($mimes, array(
        'csv' => 'text/csv'
    ));
 
    return $mimes;
}
add_filter('upload_mimes', 'addUploadMimes');

/**
 * Returns the first thumbnail of a post.
 *
 * @param int $post_id The post's id.
 */
function first_thumbnail ( $post_id ) {
    $attachments = get_attached_media( 'image', $post_id );
    
    if( count( $attachments ) > 0 ) {
        $attachment = reset( $attachments );
        $image_attributes = wp_get_attachment_image_src( $attachment->ID, 'thumb' );
        return $image_attributes;      
    }
    return '';
}

/**
 * Return src of first instrument gallery attachment in thumbnail size.
 */
function instrument_first_thumbnail ( $post_id ) {
    return first_thumbnail( $post_id );
}

/**
 * Returns the id of an instrument's thumbnail.
 *
 * @param integer $post_id ID of the current post
 * @return integer the ID of the thumbnail or the first image.
 */
function instrument_thumbnail_id ( $post_id ) {
    
    if ( has_post_thumbnail( $post_id ) ) {
        $attach_id = get_post_thumbnail_id( $post_id );
    }
    else {
        $attachments = get_attached_media( 'image', $post_id );
        
        if( count( $attachments ) > 0 ) {
            $attachment = reset( $attachments );
            $attach_id = $attachment->ID;
        }
    }
    
    if ( isset ( $attach_id ) ) return $attach_id;
    else return false;
}

/**
 * Checks whether a collection should display as a grid or as rows.
 */
function get_display_mode() {

    if ( isset($_GET['mode'] ) ) {
        $display_mode = $_GET['mode'];
        if ( $display_mode == 'def' ) {
            if ( isset( $_SESSION['mode'] ) ) {
                $display_mode = $_SESSION['mode'];
            }
            else {
                $display_mode = 'grid';
            }
        }
        if ( $display_mode != 'about' ) $_SESSION['mode'] = $display_mode;
    }
    elseif ( isset( $_SESSION['mode'] ) ) {
        $display_mode = $_SESSION['mode'];
    }
    else {
        $display_mode = 'about';
    }

    return $display_mode;
}

/**
 * Adds a get parameter to a url in the form ?key=value
 * 
 * @see https://stackoverflow.com/questions/5809774/manipulate-a-url-string-by-adding-get-parameters/16987010
 */
function add_get_param ( $url, $key, $value ) {
    $url_parts = parse_url($url);
    if ( isset( $url_parts['query'] ) ) parse_str($url_parts['query'], $params);
    else $params = [];

    $params[$key] = $value;     // Overwrite if exists
    
    // Note that this will url_encode all values
    $url_parts['query'] = http_build_query($params);
    
    $final_url = '';
    
    if ( isset( $url_parts['scheme'] ) ) $final_url .= $url_parts['scheme'] . '://';
    if ( isset( $url_parts['host'] ) ) $final_url .= $url_parts['host'];
    if ( isset( $url_parts['path'] ) ) $final_url .= $url_parts['path'];
    if ( isset( $url_parts['query'] ) ) $final_url .= '?' . $url_parts['query'] ;
    
    return $final_url;
}

/**
 * Replaces the standard Wordpress excerpt.
 */
function new_excerpt_more($more) {
       global $post;
	return ' <a href="'. get_permalink($post->ID) . '">[Read More...]</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');

/**
 * Fixes pagination for searches.
 */
function fix_search_pagination ( $query ) {
    if ( is_search() && $query->is_main_query() ) {
        if ( isset( $_GET['page'] ) ) $pagenum = $_GET['page'];
        else $pagenum = get_query_var('page');
        $query->set( 'paged', $pagenum );
        return $query;
    }
}
add_action( 'pre_get_posts', 'fix_search_pagination' );