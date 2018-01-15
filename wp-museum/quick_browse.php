<?php

add_action( 'admin_menu', 'add_quick_browse' );

function add_quick_browse() {
    $object_types = get_object_types();
    
    foreach ( $object_types as $object_type ) {
        $type_name = type_name( $object_type->name );
        add_submenu_page(
            "edit.php?post_type=$type_name",
            "Quick Browse",
            "Quick Browse",
            'edit_others_posts',
            $object_type->name . '-quick-browse',
            'quick_browse'
        );
    }
    
}

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
    else $sort_col = WPM_PREFIX . $fields[0]->field_id;
    
    if ( isset ( $_GET['sort_dir'] ) ) $sort_dir = $_GET['sort_dir'];
    else $sort_dir = 'asc';
    
    foreach ( $fields as $field ) {
        if ( $field->quick_browse == 1 ) {
            echo "<th>";
            if ( $sort_col == WPM_PREFIX . $field->field_id ) {
                if ( $sort_dir == 'asc' ) {
                    echo "<a href='$self_url&sort_col={$sort_col}&sort_dir=desc'>{$field->name}<span class='dashicons dashicons-arrow-down'></span>";
                }
                else {
                    echo "<a href='$self_url&sort_col={$sort_col}&sort_dir=asc'>{$field->name}<span class='dashicons dashicons-arrow-up'></span>";
                }
            }
            else {
                $col = WPM_PREFIX . $field->field_id;
                echo "<a href='$self_url&sort_col=$col&sort_dir=asc'>$field->name";
            }
            echo "</a></th>";
        }
    }
    echo "<th></th><th></th>";
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
        foreach ( $fields as $field ) {
            if ( $field->quick_browse == 1 ) {
                $field_name = WPM_PREFIX . $field->field_id;
                if ( isset( $custom[$field_name] ) ) {
                    $field_value = $custom[$field_name][0];
                }
                else {
                    $field_value = '(none)';
                }
                
                echo "<td><a href='$edit_url'>$field_value</a></td>";
            }
        }
        echo "<td><a href='$view_url'>View</a><td>";
        echo "</tr>";
    }
    
    echo "</tbody></table></div>"; 
}

function wpm_sort_by_field(&$target_array, $sort_col, $sort_dir) {
    $fields = get_object_fields ( object_type_from_object( $target_array[0] )->object_id );
    foreach ( $fields as $field ) {
        if ( WPM_PREFIX . $field->field_id == $sort_col ) {
            $sort_field = $field;
            break;
        }
    }
    if ( $sort_dir == 'asc' ) {
        $rv = 1;
    }
    else $rv = -1;
    usort( $target_array, function($a, $b) use ($sort_field, $rv, $sort_col) {
        $a_custom = get_post_custom( $a->ID );
        $b_custom = get_post_custom( $b->ID );
        
        if ( isset( $a_custom[$sort_col] ) ) $a_field_val = $a_custom[$sort_col][0];
        else return -1*$rv;
        
        if ( isset( $b_custom[$sort_col] ) ) $b_field_val = $b_custom[$sort_col][0];
        else return $rv;
        
        if ( isset( $sort_field->field_schema ) ) {
            $a_matches = [];
            $b_matches = [];
            $pattern = '/' . stripslashes( $sort_field->field_schema ) . '/';
            if ( preg_match( $pattern, $a_field_val, $a_matches ) &&
                 preg_match( $pattern, $b_field_val, $b_matches ) ) {
                $limit = min( count($a_matches), count($b_matches) );
                for ( $i=1; $i<$limit; $i++ ) {
                    if ( is_numeric($a_matches[$i]) && is_numeric($b_matches[$i]) ) {
                        if ( $a_matches[$i] > $b_matches[$i] ) return $rv;
                        elseif ( $a_matches[$i] < $b_matches[$i] ) return -1*$rv;
                    }
                    elseif ( strcmp( $a_matches[$i], $b_matches[$i] ) > 0 ) return $rv;
                    elseif ( strcmp( $a_matches[$i], $b_matches[$i] ) < 0 ) return -1*$rv; 
                }
                return -1*$rv;
            }
        }
        
        if ( $a_field_val > $b_field_val ) return $rv;
        else return -1*$rv;
    });
}

?>