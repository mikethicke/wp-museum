<?php

require_once 'dateclass.php';
require_once 'MetaBox.php';

add_action( 'plugins_loaded', 'create_object_types' );

function type_name ( $object_name ) {
    $type_name = WPM_PREFIX . $object_name;
    if ( strlen( $type_name ) > 20 ) $type_name = substr( $type_name, 0, 19 );
    //should do collision checking here.
    return $type_name;
}

function get_object_type_names() {
    $object_types = get_object_types();
    $type_names = array();
    foreach ( $object_types as $object ) {
        $type_names[] = type_name ( $object->name );
    }
    return $type_names;
}

function object_from_type( $type_name ) {
    $object_types = get_object_types();
    foreach ( $object_types as $object_type ) {
        if ( type_name( $object_type->name ) == $type_name ) {
            return $object_type;
        }
    }
    return false;
}

function object_type_from_object ( $object ) {
    $type_name = $object->post_type;
    $object_type = object_from_type ( $type_name );
    return $object_type;
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
            'hierarchical'  => true,
            'capabilities'  => [
                'edit_posts' => 'edit_objects',
                'edit_others_posts' => 'edit_others_objects',
                'publish_posts' => 'publish_objects',
                'read_private_posts' => 'read_private_objects',
                'delete_posts' => 'delete_objects',
                'edit_published_posts' => 'edit_published_objects'
            ],
            'map_meta_cap'  => true
        ];
        $object_post_type = new CustomPostType( $options );
        $object_post_type->supports = ['title', 'thumbnail', 'author'];
        $object_post_type->add_taxonomy( 'category' );
        
        $fields = get_object_fields( $object_id );
        $object_post_type->custom_fields = array_map(
                function ( $field ) {
                    return $field->slug;
                },
                $fields );
        
        //MetaBox for editing object fields.
        $display_fields_table = function () use ($fields_table_name, $object_id, $fields) {
            global $wpdb;
            global $post;
            $custom = get_post_custom( $post->ID );                 
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
                        $theDate = new DateClass();
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
            } //foreach ( $fields as $field ) 
            echo "</table>";
        };
        $save_fields_table = function ( $post_id ) use ($fields_table_name, $object_id) {
            /* check autosave */
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                    return $post_id;
            }
            
            global $wpdb;
            $fields = get_object_fields( $object_id );
            
            foreach ( $fields as $field ) {
                $old = get_post_meta($post_id, $field->slug, true);
                if ( $field->type == 'date' ) {
                    $the_date = new DateClass();
                    if ( isset ( $_POST[$field->slug . "~day"] ) && isset ( $_POST[$field->slug . "~month"] ) && isset ( $_POST[$field->slug . "~year"] ) ) {
                        $the_date->year     =   $_POST[$field->slug . "~year"];
                        $the_date->month    =   $_POST[$field->slug . "~month"];
                        $the_date->day      =   $_POST[$field->slug . "~day"];
                        
                        update_post_meta( $post_id, $field->slug, $the_date->toString());
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
        };
        $fields_box = new MetaBox ( type_name ( $object_row->name ).'-fields', __('Fields'), $display_fields_table, $save_fields_table );
        $object_post_type->add_custom_meta ( $fields_box );
        
        $display_object_children = function() use ( $object_post_type ) {
            global $post;
            $children = get_children( ['numberposts'    => -1,
                                       'post_status'    => 'any',
                                       'post_type'      => $object_post_type->options['type'],
                                       'post_parent'    => $post->ID]
                                    );
            echo "<table>";
            foreach ( $children as $child ) {
                $permalink = get_permalink( $child->ID );
                echo "<tr><td><a href='post.php?post={$child->ID}&action=edit'>{$child->post_title}</a></td></tr>";
            }
            echo "</table><br />";
            echo "<button type='button' class='button button-large' onclick='new_obj({$post->ID})'>New Part</button>";
        };
        $children_box = new MetaBox ( type_name ( $object_row->name ).'-children', __( $object_row->label. ' Parts' ), $display_object_children );
        $children_box->context = 'side';
        $object_post_type->add_custom_meta ( $children_box );
        
        $display_gallery_box = function () {
            global $post;
            echo "<div>";
            echo "<div id='admin-object-gallery'>";
            object_image_box_contents( $post->ID );
            echo "</div>";
            echo '<button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="content"><span class="wp-media-buttons-icon"></span> Add Images</button></div>';
        };
        $gallery_box = new MetaBox ( type_name ( $object_row->name ).'-gallery', __( $object_row->label. ' Images' ), $display_gallery_box );
        $object_post_type->add_custom_meta ( $gallery_box );
        
        $object_post_type->register();
    }
}

add_action( 'admin_footer', 'new_object_js' );
function new_object_js() {
    ?>
    <script type="text/javascript">
        function new_obj(parent) {
            var data = {
                'action'    : 'create_new_obj',
                'parent'    : parent
            };
            
            jQuery.post(ajaxurl, data, function(response) {
                window.location.href = "post.php?post=" + response +"&action=edit";
            });
        }
    </script>     
    <?php
}

add_action( 'wp_ajax_create_new_obj', 'create_new_obj');
function create_new_obj() {
    $parent_ID = intval( $_POST['parent'] );
    $parent_post = get_post( $parent_ID );
    $categories = wp_get_post_categories( $parent_ID );
    $args = [
        'post_title'        => '',
        'post_content'      => '',
        'post_type'         => $parent_post->post_type,
        'post_parent'       => $parent_ID,
        'post_category'     => $categories
    ];
    $post_id = wp_insert_post( $args );
    echo $post_id;
    wp_die();
}

add_action ( 'edit_form_top', 'add_object_parent_link');
function add_object_parent_link ( WP_POST $post ) {
    if ( substr($post->post_type, 0, strlen(WPM_PREFIX)) !== WPM_PREFIX ) return;
    $parent_ID = wp_get_post_parent_ID( $post->ID );
    if ( !$parent_ID ) return;
    $parent  = get_post( $parent_ID );
    if ( isset( $parent ) ) {
        echo "<div class='postbox' style='font-size:1.2em; padding:10px; margin-bottom:10px;'>Parent Object: {$parent->post_title} (<a href='post.php?post={$parent->ID}&action=edit'>Edit</a>)</div>";
    }
}

add_action( 'admin_enqueue_scripts', 'wpm_media_box_enqueue' );
function wpm_media_box_enqueue()  {
    wp_enqueue_media();
    wp_enqueue_script('media-upload');
    wp_enqueue_script( 'fancybox-jq', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js', ['jquery'] );
}

add_action( 'admin_footer', 'remove_image_attachment_js' );
function remove_image_attachment_js() {
    ?>
    <script type="text/javascript">
        function remove_image_attachment( image_id, post_id ) {
            var data = {
                'action'    : 'remove_image_attachment_aj',
                'post_id'   : post_id,
                'image_id'  : image_id
            };
            
            jQuery.post( ajaxurl, data, function( response ) {
                oib = document.getElementById('object-image-box');
                oib.innerHTML = response;
            });
        }
    </script>
    <?php
}

add_action( 'admin_footer', 'refresh_image_box_js' );
function refresh_image_box_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready( function() {
            wp.Uploader.queue.on('reset', function() { 
                var data = {
                    'action'    : 'refresh_image_box_on_upload_aj',
                    'post_id'   : <?php global $post; echo $post->ID; ?>
                };
                jQuery.post( ajaxurl, data, function( response ) {
                    oib = document.getElementById('object-image-box');
                    oib.innerHTML = response;
                });
            });
        });
    </script>
    <?php
}

add_action( 'wp_ajax_refresh_image_box_on_upload_aj', 'refresh_image_box_on_upload_aj' );
function refresh_image_box_on_upload_aj() {
    $post_id = intval( $_POST['post_id'] );
    object_image_box_contents( $post_id );
    wp_die();
}

add_action( 'wp_ajax_remove_image_attachment_aj', 'remove_image_attachment_aj');
function remove_image_attachment_aj() {
    $image_id = intval( $_POST['image_id'] );
    $post_id = intval( $_POST['post_id'] );
    wp_delete_attachment( $image_id );
    object_image_box_contents( $post_id );
    wp_die();
}

function object_image_box_contents ( $post_id ) {
    global $post;
    if ( is_null( $post ) ) $post = get_post( $post_id );
    $images = get_attached_media( 'image', $post );
    $prev_menu_order = -1;
    foreach ( $images as $image ) {
        if ( $image->menu_order <= $prev_menu_order ) {
            $image->menu_order = $prev_menu_order + 1;
            wp_update_post ( $image );
        }
        $prev_menu_order = $image->menu_order;
        $image_thumbnail = wp_get_attachment_image_src( $image->ID, 'thumbnail' )[0];
        $image_full = wp_get_attachment_image_src( $image->ID, 'large' )[0];
        echo "<div id='image-div-{$image->ID}' style='display:inline'>";
        echo "<a data-fancybox='fbgallery' href='$image_full'><img src='$image_thumbnail'></a>";
        echo "<a id='delete-{$image->ID}' class='wpm-image-delete' onclick='remove_image_attachment({$image->ID}, $post_id)'>[x]</a>";
        echo "<a id='moveup-{$image->ID}' class='wpm-image-moveup' onclick='wpm_image_move({$image->ID}, -1)'><span class='dashicons dashicons-arrow-left'></span></a>";
        echo "<a id='movedown-{$image->ID}' class='wpm-image-movedown' onclick='wpm_image_move({$image->ID}, +1)'><span class='dashicons dashicons-arrow-right'></span></a>";
        echo "</div>";
    }
}

function get_post_descendants ( $post, $post_status='publish' ) {
    $descendants = [];
    $children = get_posts ( [
        'numberposts'   => -1,
        'post_status'   => $post_status,
        'post_type'     => $post->post_type,
        'post_parent'   => $post->ID
    ] );
    foreach ( $children as $child ) {
        $grand_children = get_post_descendants ( $child, $post_status );
        $descendants = array_merge( $descendants, $grand_children);
    }
    $descendants = array_merge( $descendants, $children );
    return $descendants;
}

add_action( 'admin_footer', 'wpm_image_move_js' );
function wpm_image_move_js() {
    ?>
    <script type='text/javascript'>
    function wpm_image_move(image_id, direction) {
        div = document.getElementById("image-div-" + image_id);
        gallery_div = document.getElementById("admin-object-gallery");
        gallery_children = gallery_div.children;
        swapped = false;
        
        for ( i = 0; i < gallery_children.length; i++ ) {
            if ( gallery_children[i].id == "image-div-" + image_id ) {
                if ( direction == 1 && i < gallery_children.length - 1 ) {
                    swap_div = gallery_children[i + 1];
                    gallery_children[i].parentNode.insertBefore( gallery_children[i].parentNode.removeChild(swap_div), gallery_children[i] );
                    swapped = true;
                    break;
                }
                else if ( direction == -1 && i > 0 ) {
                    swap_div = gallery_children[i - 1];
                    gallery_children[i].parentNode.insertBefore( gallery_children[i].parentNode.removeChild(gallery_children[i]), swap_div );
                    swapped = true;
                    break;
                }
            }
        }
        
        if ( swapped ) {
             var data = {
                'action'    : 'swap_image_order_aj',
                'first_image_id'   : swap_div.id,
                'second_image_id'  : image_id
            };
            
            jQuery.post( ajaxurl, data, function( response ) {
                //pass
            });
        }
        
        
    }
    </script>
    <?php
}

add_action( 'wp_ajax_swap_image_order_aj', 'swap_image_order_aj');
function swap_image_order_aj() {
    $first_image_id = $_POST['first_image_id'];
    $first_image_id = intval( substr( $first_image_id, strlen("image-div-") ) );
    $second_image_id = intval( $_POST['second_image_id'] );
    $first_image_post = get_post( $first_image_id );
    $second_image_post = get_post( $second_image_id );
    $first_image_menu_order = $first_image_post->menu_order;
    $first_image_post->menu_order = $second_image_post->menu_order;
    $second_image_post->menu_order = $first_image_menu_order;
    wp_update_post( $first_image_post );
    wp_update_post( $second_image_post );
    wp_die();
}