<?php

class ObjectPostType {
    
    public $object_row;
    public $object_id;
    public $fields;
    public $object_post_type;
    public $object_type;
    
    public function __construct ( $object_row ) {
        global $wpdb;
        $this->object_row = $object_row;
        $this->object_type = type_name( $this->object_row->name );
        $this->object_id = $this->object_row->object_id;
        
        $options = [
            'type'          => $this->object_type,
            'label'         => $this->object_row->label,
            'label_plural'  => $this->object_row->label . 's',
            'description'   => $this->object_row->description,
            'menu_icon'     => 'dashicons-archive',
            'hierarchical'  => true,
            'options'   => [
                'capabilities'  => [
                    'edit_posts'            => WPM_PREFIX . 'edit_objects',
                    'edit_others_posts'     => WPM_PREFIX . 'edit_others_objects',
                    'publish_posts'         => WPM_PREFIX . 'publish_objects',
                    'read_private_posts'    => WPM_PREFIX . 'read_private_objects',
                    'delete_posts'          => WPM_PREFIX . 'delete_objects',
                    'edit_published_posts'  => WPM_PREFIX . 'edit_published_objects'
                ],
                'map_meta_cap'  => true
            ]    
        ];
        $this->object_post_type = new CustomPostType( $options );
        $this->object_post_type->supports = ['title', 'thumbnail', 'author'];
        $this->object_post_type->add_taxonomy( 'category' );
        
        $this->fields = get_object_fields( $this->object_id );
        $this->object_post_type->custom_fields = $this->fields;
    }

    function display_fields_table () {
        global $wpdb;
        global $post;
        $custom = get_post_custom( $post->ID );                 
        //Check for legacy field names
        if ( isset( $custom['unidentified'] ) && object_name_from_id( $this->object_id ) == 'instrument' ) {
            foreach ( $this->fields as $field ) {
                $field_slug = strtolower(str_replace(" ", "-", $field->name));
                $old_custom = $custom;
                foreach ( $old_custom as $key=>$value ) {
                    if ( $key == $field_slug ) {
                        $custom[WPM_PREFIX . $field->field_id] = $value;
                    }
                }
            }
        }
        echo "<table class='wp-list-table widefat striped wpm-object' id='wpm-field-edit'>";
        foreach ( $this->fields as $field ) {
            ?>
            <tr class='wpm-object-help-text'><td colspan=2><?php echo stripslashes($field->help_text);?></td></tr>
            <tr><td class="wpm-object-field-label"><label title="<?php echo $field->help_text; ?>"><?php echo $field->name;?> </label></td>
            <?php 
            switch ($field->type) {
                case 'varchar' :
                    ?>
                    <td><input type="text"
                            name="<?php echo $field->slug; ?>"
                            value="<?php if ( isset ( $custom[$field->slug][0] ) ) echo $custom[$field->slug][0]; ?>"
                        />
                    </td>
                    <?php
                    break;
                case 'text' :
                    ?>
                    <td><textarea name="<?php echo $field->slug; ?>"><?php if ( isset ( $custom[$field->slug][0] ) ) echo $custom[$field->slug][0]; ?></textarea>
                    </td>
                    <?php
                    break;
                case 'tinyint' :
                    ?>
                    <td><input type="checkbox"
                            name = "<?php echo $field->slug; ?>"
                            value = "1"
                            <?php if ( isset ( $custom[$field->slug][0] ) && $custom[$field->slug][0] != '0' ) echo 'checked="checked"'; ?>
                        />
                    </td>
                    <?php
                    break;
                case 'date' :
                    $theDate = new SimpleDate();
                    if ( isset ( $custom[$field->slug] ) ) {
                        $theDate->fromString ( $custom[$field->slug][0] );
                    }
                    else $theDate->fromString ( date ( 'Y-m-d' ) );
                    ?>
                    <td><select name = "<?php echo $field->slug; ?>~month">
                            <?php
                            $month_num = 0;
                            foreach ($theDate->months as $month) {
                                $month_num++;
                                ?>
                                <option value="<?php echo $month_num; ?>" <?php if ( $theDate->month == $month_num ) echo 'selected = "selected"';?>>
                                    <?php echo $month; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                        <select name = "<?php echo $field->slug; ?>~day">
                            <?php
                            foreach ($theDate->days as $day) {
                                ?>
                                <option value="<?php echo $day; ?>" <?php if ( $theDate->day == $day ) echo 'selected = "selected"';?>>
                                    <?php echo $day; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                        <select name = "<?php echo $field->slug; ?>~year">
                            <?php
                            foreach ($theDate->years as $year) {
                                ?>
                                <option value="<?php echo $year; ?>" <?php if ( $theDate->year == $year ) echo 'selected = "selected"';?>>
                                    <?php echo $year; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <?php
                    break;
            } //switch
            ?>
            </tr>
            <?php   
        } //foreach ( $this->fields as $field ) 
        echo "</table>";
    }

    function save_fields_table ( $post_id ) {
        /* check autosave */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
        }
        
        foreach ( $this->fields as $field ) {
            $old = get_post_meta($post_id, $field->slug, true);
            if ( $field->type == 'date' ) {
                $the_date = new SimpleDate();
                if ( isset ( $_POST[$field->slug . "~day"] ) && isset ( $_POST[$field->slug . "~month"] ) && isset ( $_POST[$field->slug . "~year"] ) ) {
                    $the_date->year     =   $_POST[$field->slug . "~year"];
                    $the_date->month    =   $_POST[$field->slug . "~month"];
                    $the_date->day      =   $_POST[$field->slug . "~day"];
                    
                    update_post_meta( $post_id, $field->slug, $the_date->toString() );
                }
                elseif ( isset ( $_POST[$field->slug] ) ) update_post_meta ( $post_id, $field->slug, trim( $_POST[$field->slug] ) );
            }
            else if ( $field->type == 'tinyint' ) {
                if ( !isset( $_POST[$field->slug] ) ) $_POST[$field->slug] = '0';
                update_post_meta( $post_id, $field->slug, trim( $_POST[$field->slug] ) );
            }
            else {
                if ( isset ( $_POST[$field->slug] ) && $_POST[$field->slug] != '' && $old != $_POST[$field->slug]) update_post_meta( $post_id, $field->slug, trim($_POST[$field->slug] ) );
                elseif ( isset ( $_POST[$field->slug] ) && $_POST[$field->slug] == '' && $old ) delete_post_meta( $post_id, $field->slug, $old );   
            }   
        }
    
        /* Check if "Uncategorized" is checked, and if so set post's category to Uncategorized */
        $current_categories = wp_get_post_categories( $post_id );
        $new_categories = [];
        $unident_cat = get_category_by_slug( 'unidentified' );
        if ( get_post_meta($post_id, 'unidentified', true) == "1") {
            if ( !in_array( $unident_cat->cat_ID, $current_categories ) ) $current_categories[] = $unident_cat->cat_ID;
            wp_set_post_categories( $post_id, $current_categories );
        }
        else {
            foreach ( $current_categories as $ccat ) {
                if ( $ccat != $unident_cat->cat_ID ) $new_categories[] = $ccat;
            }
            wp_set_post_categories( $post_id, $new_categories );
        }   
    }

    //Callback for displaying object post's children.
    function display_object_children ( ) {
        global $post;
        $children = get_children( ['numberposts'    => -1,
                                   'post_status'    => 'any',
                                   'post_type'      => $this->object_post_type->options['type'],
                                   'post_parent'    => $post->ID]
                                );
        echo "<table>";
        foreach ( $children as $child ) {
            $permalink = get_permalink( $child->ID );
            echo "<tr><td><a href='post.php?post={$child->ID}&action=edit'>{$child->post_title}</a></td></tr>";
        }
        echo "</table><br />";
        echo "<button type='button' class='button button-large' onclick='new_obj({$post->ID})'>New Part</button>";
    }

    //Callback for displaying object post's image attachments.
    function display_gallery_box () {
        global $post;
        $custom = get_post_custom( $post->ID );
        echo "<div>";
        echo "<div id='object-image-box'>";
        object_image_box_contents( $post->ID );
        echo "</div>";
        echo '<button type="button" id="insert-wpm-image-button" class="button"><span class="wp-media-buttons-icon"></span> Add Images</button></div>';
        echo '<input type="hidden" id="gallery_attach_ids">';
        if ( isset($custom['gallery_attach_ids']) ) echo $custom['gallery_attach_ids'];
        echo '</input>';
    }

    function save_gallery_box () {
        global $post;
        if ( isset( $_POST['gallery_attach_ids'] ) && !is_null( $post ) ) {
            /* check autosave */
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
                return $post->ID;
            }
            $custom = get_post_custom( $post->ID );
            update_post_meta( $post->ID, 'gallery_attach_ids', $_POST['gallery_attach_ids'] );  
        }  
    }

    // Adds each public custom field to the api.
    // Typically accessed at /wp-json/wp/v2/<object_slug>/<field_slug> 
    // add_action( 'rest_api_init', function() use( $fields, $object_type, $object_type_list ) 
    function wpm_rest_custom_fields () {
        foreach ( $this->fields as $field ) { 
            if ( $field->public == 1 ) {
                register_rest_field( $this->object_type, $field->slug, array(
                    'get_callback'      => function ( $object ) use( $field ) {
                        $custom_fields = get_post_custom( $object['id'] );
                        if ( isset($custom_fields[$field->slug]) ) {
                            return ( $custom_fields[$field->slug][0] ); 
                        }
                        else return ( null );
                       
                    },
                    'update_callback'   => null,
                    'schema'            => null        
                )
                );   
            }
        }

        // Adds thumbnail url and img attributes to the api.
        // Typically accessed at /wp-json/wp/v2/<object_slug>/thumbnail_src
        register_rest_field( $this->object_type, 'thumbnail_src', array(
            'get_callback'      => function ( $object ) {
                if ( has_post_thumbnail( $object['id'] ) ) {
                    $attach_id = get_post_thumbnail_id( $object['id'] );
                }
                else {
                    $attachments = get_attached_media( 'image', $object['id'] );
    
                    if( count( $attachments ) > 0 ) {
                        $attachment = reset( $attachments );
                        $attach_id = $attachment->ID;
                    }
                }
                if ( isset($attach_id) ) {
                    return wp_get_attachment_image_src ( $attach_id, 'thumb' );
                }
            }
        ) );

        // Adds a list of the object post type's public custom fields to the api.
        // Typically accessed at /wp-json/wp-museum/v1/object_custom/<object_slug>/
        register_rest_route( 'wp-museum/v1', '/object_custom/' . $this->object_type . '/', array (
            'methods'   => 'GET',
            'callback'  => function() {
                foreach ( $this->fields as $field ) {
                    if ( $field->public == 1) {
                        $filtered_fields[] = $field->slug;
                    }
                }
                return $filtered_fields;
            }
        ) );
    }

    function register() {
        //Create a MetaBox with the two above functions as callbacks.
        $fields_box = new MetaBox ( type_name ( $this->object_row->name ).'-fields', 
            __('Fields'), 
            array( $this, 'display_fields_table' ), 
            array( $this, 'save_fields_table' ) );
        $this->object_post_type->add_custom_meta ( $fields_box );

        //Creates a MetaBox displaying an object's child posts.
        $children_box = new MetaBox ( type_name ( $this->object_row->name ).'-children', 
            __( $this->object_row->label. ' Parts' ), 
            array( $this, 'display_object_children' ) );
        $children_box->context = 'side';
        $this->object_post_type->add_custom_meta ( $children_box );
        
        //Creates a MetaBox for displaying and manipulating object post's image gallery.
        $gallery_box = new MetaBox ( type_name ( $this->object_row->name ).'-gallery', __( $this->object_row->label. ' Images' ), 
                array( $this, 'display_gallery_box' ), 
                array( $this, 'save_gallery_box' ) );
        $this->object_post_type->add_custom_meta ( $gallery_box );
        
        $this->object_post_type->register();
    }
}


