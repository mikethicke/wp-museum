<?php

require_once 'dateclass.php';

add_action( 'plugins_loaded', 'create_object_types' );

function type_name ( $object_name ) {
    $type_name = WPM_PREFIX . $object_name;
    if ( strlen( $type_name ) > 20 ) $type_name = substr( $type_name, 0, 19 );
    //should do collision checking here.
    return $type_name;
}

function object_from_type( $type_name ) {
    $object_types = get_object_types();
    foreach ( $object_types as $object_type ) {
        if ( type_name( $object_type->name ) == $type_name ) {
            return $object_type;
        }
    }
}

function create_object_types() {
    global $wpdb;
    $object_type_table = $wpdb->prefix . WPM_PREFIX . "object_types";
    $object_rows = $wpdb->get_results( "SELECT * FROM $object_type_table" );
    foreach ( $object_rows as $object_row ) {
        $fields_table_name = $wpdb->prefix . WPM_PREFIX . "object_fields";
        $object_name = $object_row->name;
        $object_id = $object_row->object_id;
        
        $options = [
            'type'          => type_name( $object_name ),
            'label'         => $object_row->label,
            'label_plural'  => $object_row->label . 's',
            'description'   => $object_row->description,
            'menu_icon'     => 'dashicons-archive',
            'hierarchical'  => true
        ];
        $object_post_type = new CustomPostType( $options );
        $object_post_type->supports = ['title', 'thumbnail', 'author'];
        $object_post_type->add_taxonomy( 'category' );
        
        $object_post_type->add_custom_meta (
            //Metabox display callback
            function ( WP_POST $post ) use ($object_row, $fields_table_name, $object_id) {
                add_meta_box ( type_name( $object_row->name ), 'Fields', function() use ($post, $fields_table_name, $object_id) {
                    global $wpdb;
                    $custom = get_post_custom( $post->ID );                 
                    $fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $fields_table_name WHERE object_id=%d", $object_id ) );
                    //Check for legacy field names
                    if ( isset( $custom['unidentified'] ) && object_name_from_id( $object_id ) == 'instrument' ) {
                        foreach ( $fields as $field ) {
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
                    foreach ( $fields as $field ) {
                        $field_name = WPM_PREFIX . $field->field_id;
                        ?>
                        <tr class='wpm-object-help-text'><td colspan=2><?php echo stripslashes($field->help_text);?></td></tr>
                        <tr><td class="wpm-object-field-label"><label title="<?php echo $field->help_text; ?>"><?php echo $field->name;?> </label></td>
                        <?php 
                        switch ($field->type) {
                            case 'varchar' :
                                ?>
                                <td><input type="text"
                                        name="<?php echo $field->field_id; ?>"
                                        value="<?php if ( isset ( $custom[$field_name][0] ) ) echo $custom[$field_name][0]; ?>"
                                    />
                                </td>
                                <?php
                                break;
                            case 'text' :
                                ?>
                                <td><textarea name="<?php echo $field_name; ?>"><?php if ( isset ( $custom[$field_name][0] ) ) echo $custom[$field_name][0]; ?></textarea>
                                </td>
                                <?php
                                break;
                            case 'tinyint' :
                                ?>
                                <td><input type="checkbox"
                                        name = "<?php echo $field_name; ?>"
                                        value = "1"
                                        <?php if ( isset ( $custom[$field_name][0] ) && $custom[$field_name][0] != '0' ) echo 'checked="checked"'; ?>
                                    />
                                </td>
                                <?php
                                break;
                            case 'date' :
                                $theDate = new DateClass();
                                if ( isset ( $custom[$field_name] ) ) {
                                    $theDate->fromString ( $custom[$field_name][0] );
                                }
                                else $theDate->fromString ( date ( 'Y-m-d' ) );
                                ?>
                                <td><select name = "<?php echo $field_name; ?>~month">
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
                                    <select name = "<?php echo $field_name; ?>~day">
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
                                    <select name = "<?php echo $field_name; ?>~year">
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
                    } //foreach ( $fields as $field ) 
                    echo "</table>";
                });
            },
            //Metabox save callback
            function ( $post_id ) use ($fields_table_name, $object_id) {
                // check autosave
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                        return $post_id;
                }
                
                global $wpdb;
                $fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $fields_table_name WHERE object_id=%d", $object_id ) );
                
                foreach ( $fields as $field ) {
                    $field_name = WPM_PREFIX . $field->field_id;
                    $old = get_post_meta($post_id, $field_name, true);
                    if ( $field->type == 'date' ) {
                        $the_date = new DateClass();
                        if ( isset ( $_POST[$field_name . "~day"] ) && isset ( $_POST[$field_name . "~month"] ) && isset ( $_POST[$field_name . "~year"] ) ) {
                            $the_date->year     =   $_POST[$field_name . "~year"];
                            $the_date->month    =   $_POST[$field_name . "~month"];
                            $the_date->day      =   $_POST[$field_name . "~day"];
                            
                            update_post_meta( $post_id, $field_name, $the_date->toString());
                        }
                        elseif ( isset ( $_POST[$field_name] ) ) update_post_meta ( $post_id, $field_name, trim( $_POST[$field_name] ) );
                    }
                    else if ( $field->type == 'tinyint' ) {
                        if ( !isset( $_POST[$field_name] ) ) $_POST[$field_name] = '0';
                        update_post_meta( $post_id, $field_name, trim( $_POST[$field_name] ) );
                    }
                    else {
                        if ( isset ( $_POST[$field_name] ) && $_POST[$field_name] != '' && $old != $_POST[$field_name]) update_post_meta( $post_id, $field_name, trim($_POST[$field_name] ) );
                        elseif ( isset ( $_POST[$field_name] ) && $_POST[$field_name] == '' && $old ) delete_post_meta( $post_id, $field_name, $old );   
                    }   
                }
            
                //Check if "Uncategorized" is checked, and if so set post's category to Uncategorized
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
        );
        
        $object_post_type->register();
    }
}

add_action ( 'edit_form_top', 'create_child_link');
function create_child_link ( WP_POST $post ) {
    $objects = get_object_types();
    foreach ( $objects as $object ) {
        if ( $post->post_type == type_name($object->name) ) {
            echo "<a class='page-title-action' href=''>New Child</a>";
        }
    }
}



function object_meta() {
    global $post;
    $custom = get_post_custom( $post->ID );
    
}

?>