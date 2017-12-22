<?php
/**
 * Top page for catalogue, listing all catalogue description pages.
 */

global $post;
get_header();

if ( have_posts() ) {
    the_post();
    the_content();
    
    $catalogue_page_id = $id;
    
    $categories = get_categories();
    $description_pages = array();
    foreach ( $categories as $category ) {
        $cat_meta = get_option ("category_{$category->term_id}");
        if ($cat_meta != '' && $cat_meta['description_page'] != '') {
                if (!in_array ($cat_meta['description_page'], $description_pages) ) $description_pages[] = $cat_meta['description_page'];
            }
    }
    
    $row_counter = 1;
    
    $parent_pages = array();
    $child_pages = array();
    foreach ( $description_pages as $description_page ) {
        $the_page = get_page ( $description_page );
        if ( $the_page->post_parent == 0 ) $parent_pages[] = $the_page;
        else $child_pages[] = $the_page;
    }
    
    $description_pages = array();
    foreach ( $parent_pages as $parent_page ) {
        $description_pages[] = $parent_page;
        foreach ( $child_pages as $child_page ) {
            if ( $child_page->post_parent == $parent_page->ID ) $description_pages[] = $child_page;
        }
    }
    
    foreach ( $description_pages as $description_page ) {
        $the_page = get_page ( $description_page );    
        $post = $the_page;
        setup_postdata ( $post );
        if ( $the_page->post_parent != 0 ) $indent = 1;
        else $indent = 0;
        if ( $the_page->post_status == "publish" && $the_page->ID != $catalogue_page_id) post_row ( $post, $row_counter, $indent );
        $row_counter = 1 - $row_counter;
    }
    
    
}
    get_footer();
?>