<?php
/**
 * Single WPM object (instrument)
 */

global $post;
get_header();

if ( have_posts() ) {
    the_post();
    $custom = get_post_custom ( $post->ID );
    
    //retrieve instrument fields from database
    global $wpdb;
    $object_type = object_from_type( $post->post_type );
    $fields = get_object_fields( $object_type->object_id );
    $bool_yes_fields = [];
    
    
    
    
    get_sidebar ( 'instrument' );
    ?>
    <div id="content">
        
        <h2><?php echo the_title(); ?></h2>
        <!-- breadcrumb list of category hierarchy -->
        <div class="instrument-post-categories"><?php the_category ( ' &middot; '); ?></div>
        <?php
        $images = get_attached_media( 'image' );
        echo "<div id='wpm-object-gallery'>";
        foreach ( $images as $image ) {
            $image_thumbnail  = wp_get_attachment_image_src( $image->ID, 'object_gallery_thumb' )[0];
            $image_full = wp_get_attachment_image_src( $image->ID, 'large' )[0];
            ?>
            <div class="wpm-object-image">
                <a data-fancybox="gallery" href="<?php echo $image_full; ?>"><img src="<?php echo $image_thumbnail; ?>"></a>
            </div>
            <?php
        }
        echo "</div>";
        
        
        ?>
        
        <?php
        foreach ( $fields as $field ) {
            //Public can only view fields marked as "public"
            if ( isset ( $custom[$field->slug][0] ) && ( $field->public == 1 || current_user_can ( 'read_private_posts' ) ) ) {
                $priv = '';
                if ( $field->public != 1 ) {
                    $priv = " object-private-field";
                }
                ?>
                <div class="field-text <?php echo $priv; ?>">
                    <?php
                    if ( $field->type == 'tinyint' ) {
                        if ( $custom[$field->slug][0] == '1' ) $bool_yes_fields[] = $field->name;
                    }
                    else {
                        if ( strlen ( $custom[$field->slug][0] ) > 39) {
                          $field_text = '<div class="field-label-div">' . $field->name . ':</div>' . $custom[$field->slug][0];  
                        }
                        else {
                            $field_text = '<span class="field-label">' . $field->name . ':</span> ' . $custom[$field->slug][0];
                        }
                        echo apply_filters('the_content', $field_text);   
                    }
                    ?>
                </div>
                <?php
            }
        }      
        ?>
        <div>
            <?php
            if ( count( $bool_yes_fields ) > 0 ) {
                echo "<ul>";
                foreach ( $bool_yes_fields as $field ) {
                    echo "<li class='$priv'>$field</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>
    <div class="clear"></div>    
    </div> <!-- #content --> 
<?php    
}
    get_footer();
?>