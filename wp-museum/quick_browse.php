<?php
/**
 * Adds quick browse tables to all museum object types. Quick browse tables allow administrators
 * to quickly see all objects of a particular type along with publication status and summary
 * data. The quick browse page is accessed through the <Object Type>|Quick Browse menu.
 */

/**
 * Adds quick browse page to all object types.
 */
function add_quick_browse() {
    $object_types = get_object_types();
    
    foreach ( $object_types as $object_type ) {
        $type_name = type_name( $object_type->name );
        add_submenu_page(
            "edit.php?post_type=$type_name",
            "Quick Browse",
            "Quick Browse",
            WPM_PREFIX . 'edit_others_objects',
            $object_type->name . '-quick-browse',
            'quick_browse'
        );
    }
}
add_action( 'admin_menu', 'add_quick_browse' );

/**
 * Display the quick browse table.
 */
function quick_browse() {
    global $wpdb;
    $type_name = $_GET['post_type'];
    $object_type = object_from_type( $type_name );
    
    $fields = get_object_fields( $object_type->object_id );
    if ( count($fields) == 0 ) {
        echo "No fields selected for quick browse.";
        return;
    }
    
    echo "<div class='wrap'><table class='widefat'>
            <thead><tr>";
                
    
    $self_url = $_SERVER['PHP_SELF'] . "?post_type=$type_name&page={$object_type->name}-quick-browse";
    
    if ( isset( $_GET['sort_col'] ) ) $sort_col = $_GET['sort_col'];
    else $sort_col = $fields[0]->slug;
    
    if ( isset ( $_GET['sort_dir'] ) ) $sort_dir = $_GET['sort_dir'];
    else $sort_dir = 'asc';
    
    echo "<th>";
    if ( $sort_col == 'post_title' ) {
        if ( $sort_dir == 'asc' ) {
            echo "<a href='$self_url&sort_col=post_title&sort_dir=desc'>Name<span class='dashicons dashicons-arrow-down'></span>";
        }
        else {
            echo "<a href='$self_url&sort_col=post_title&sort_dir=asc'>Name<span class='dashicons dashicons-arrow-up'></span>";
        }
    }
    else {
        echo "<a href='$self_url&sort_col=post_title&sort_dir=asc'>Name";
    }
    echo "</a></th>";
    
    foreach ( $fields as $field ) {
        if ( $field->quick_browse == 1 ) {
            echo "<th>";
            if ( $sort_col == $field->slug ) {
                if ( $sort_dir == 'asc' ) {
                    echo "<a href='$self_url&sort_col={$sort_col}&sort_dir=desc'>{$field->name}<span class='dashicons dashicons-arrow-down'></span>";
                }
                else {
                    echo "<a href='$self_url&sort_col={$sort_col}&sort_dir=asc'>{$field->name}<span class='dashicons dashicons-arrow-up'></span>";
                }
            }
            else {
                $col = $field->slug;
                echo "<a href='$self_url&sort_col=$col&sort_dir=asc'>$field->name";
            }
            echo "</a></th>";
        }
    }
    $csv_button = export_csv_button ( $object_type->object_id );
    echo "<th>$csv_button</th><th></th><th></th>";
    echo "</tr></thead><tbody>";
    
    $args = [
        'numberposts'       => -1,
        'post_type'         => $type_name,
        'post_status'       => 'any'
    ];
    $objects = get_posts( $args );
    
    wpm_sort_by_field ( $objects, $sort_col, $sort_dir );
    
    foreach ( $objects as $object ) {
        $custom = get_post_custom ( $object->ID );
        
        $edit_url = admin_url( "post.php?post={$object->ID}&action=edit");
        $view_url = get_permalink( $object->ID );
        $row_class = "wpm-quick-row-" . $object->post_status;
        echo "<tr class='$row_class'>";
        echo "<td><a href='$edit_url'>{$object->post_title}</a></td>";
        foreach ( $fields as $field ) {
            if ( $field->quick_browse == 1 ) {
                if ( isset( $custom[$field->slug] ) ) {
                    $field_value = $custom[$field->slug][0];
                }
                else {
                    $field_value = '(none)';
                }
                
                echo "<td><a href='$edit_url'>$field_value</a></td>";
            }
        }
        echo "<td><a href='$view_url'>View</a><td>";
        echo "<td>";
        if ( count(get_attached_media( 'image', $object ) ) > 0 ) {
            echo '<span class="dashicons dashicons-format-gallery"></span>';
        }
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody></table></div>"; 
}

/**
 * Callback function for sorting quick browse table by a column.
 *
 * @param [WP_Post]     $target_array   The posts to be sorted.
 * @param string        $sort_col       Slug of field to sort by.
 * @param string        $sort_dir       The direction to sort by (asc or desc)
 */
function wpm_sort_by_field(&$target_array, $sort_col, $sort_dir) {
    if ( $sort_col == 'post_title' ) {
        $sort_field = null;
    }
    else {
        $fields = get_object_fields ( object_type_from_object( $target_array[0] )->object_id );
        foreach ( $fields as $field ) {
            if ( $field->slug == $sort_col ) {
                $sort_field = $field;
                break;
            }
        }
    }
    if ( $sort_dir == 'asc' ) {
        $rv = 1;
    }
    else $rv = -1;
    usort( $target_array, function($a, $b) use ($sort_field, $rv, $sort_col) {
        if ( $sort_col == 'post_title' ) {
            $a_field_val = $a->post_title;
            $b_field_val = $b->post_title;
        }
        else {
            $a_custom = get_post_custom( $a->ID );
            $b_custom = get_post_custom( $b->ID );
            
            if ( isset( $a_custom[$sort_col] ) ) $a_field_val = $a_custom[$sort_col][0];
            else return -1*$rv;
            
            if ( isset( $b_custom[$sort_col] ) ) $b_field_val = $b_custom[$sort_col][0];
            else return $rv;
            
            if ( isset( $sort_field->field_schema ) && !empty( $sort_field->field_schema ) ) {
                $a_matches = [];
                $b_matches = [];
                $pattern = '/' . stripslashes( $sort_field->field_schema ) . '/';
                if ( preg_match( $pattern, $a_field_val, $a_matches ) &&
                     preg_match( $pattern, $b_field_val, $b_matches ) ) {
                    if ( count(array_filter(array_keys($a_matches), 'is_string')) > 0 ) {
                        //named capture groups
                        ksort ( $a_matches );
                        foreach ( $a_matches as $a_key => $a_val ) {
                            if ( is_numeric( $a_val ) && is_numeric( $b_matches[$a_key] ) ) {
                                if ( $a_val > $b_matches[$a_key] ) return $rv;
                                elseif ( $a_val < $b_matches[$a_key] ) return -1*$rv;
                            }
                            elseif ( strcasecmp( $a_val, $b_matches[$a_key] ) > 0 ) return $rv;
                            elseif ( strcasecmp( $a_val, $b_matches[$a_key] ) < 0 ) return -1*$rv;
                        }
                    }
                    else {
                        //sequential capture groups
                        $limit = min( count($a_matches), count($b_matches) );
                        for ( $i=1; $i<$limit; $i++ ) {
                            if ( is_numeric($a_matches[$i]) && is_numeric($b_matches[$i]) ) {
                                if ( $a_matches[$i] > $b_matches[$i] ) return $rv;
                                elseif ( $a_matches[$i] < $b_matches[$i] ) return -1*$rv;
                            }
                            elseif ( strcasecmp( $a_matches[$i], $b_matches[$i] ) > 0 ) return $rv;
                            elseif ( strcasecmp( $a_matches[$i], $b_matches[$i] ) < 0 ) return -1*$rv;
                        }
                    }
                    if ( count( $a_matches ) > count ( $b_matches) ) return 1*$rv;
                    elseif ( count( $a_matches ) < count ( $b_matches) ) return -1*$rv;
                    else return 0; 
                }
            }    
        }
        
        if ( is_numeric( $a_field_val ) && is_numeric( $b_field_val ) ) {
            if ( $a_field_val > $b_field_val ) return $rv;
            elseif ( $a_field_val > $b_field_val ) return -1*$rv;
            else return 0;
        }
        else {
            if ( strcasecmp( $a_field_val, $b_field_val ) > 0) return $rv;
            elseif ( strcasecmp( $a_field_val, $b_field_val ) < 0) return -1*$rv;
            else return 0;
        }
        
        
    });
}