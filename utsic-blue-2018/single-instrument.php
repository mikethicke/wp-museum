<?php
/**
 * Single instrument display.
 */

global $post;
get_header();

if ( have_posts() ) {
    the_post();
    $custom = get_post_custom ( $post->ID );
    
    //retrieve instrument fields from database
    global $wpdb;
    $fields_table_name = $wpdb->prefix . "instrument_fields";
    $fields = $wpdb->get_results( "SELECT id, name, slug, help_text, type, public FROM $fields_table_name" );
    get_sidebar ( 'instrument' );
    ?>
    <div id="content">
        
        <h2><?php echo the_title(); ?></h2>
        <!-- breadcrumb list of category hierarchy -->
        <div class="instrument-post-categories"><?php the_category ( ' &middot; '); ?></div>
        <?php //display image gallery
        print instrument_gallery ( array ( 'id' => $post->ID ) ); ?> 
        <?php
        foreach ( $fields as $field ) {
            //Public can only view fields marked as "public"
            if ( isset ( $custom[$field->slug][0] ) && ( $field->public == 1 || current_user_can ( 'read_private_posts' ) ) ) {
                ?>
                <div class="field-text">
                    <?php
                    if ( strlen ( $custom[$field->slug][0] ) > 39) {
                          $field_text = '<div class="field-label-div">' . $field->name . ':</div>' . $custom[$field->slug][0];  
                    }
                    elseif ( $field->type == 'tinyint' ) {
                        $field_text = '<span class="field-label">' . $field->name . ':</span> ';
                        if ( $custom[$field->slug][0] == '0' ) $field_text .= " No";
                        elseif ( $custom[$field->slug][0] == '1' ) $field_text .= " Yes";
                    }
                    else {
                        $field_text = '<span class="field-label">' . $field->name . ':</span> ' . $custom[$field->slug][0];
                    }
                    echo apply_filters('the_content', $field_text);
                    ?>
                </div>
                <?php
            }
        }      
        ?>
    <div class="clear"></div>    
    </div> <!-- #content --> 
<?php    
}
    get_footer();
?>