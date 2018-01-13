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
            'quick-browse',
            'quick_browse'
        );
    }
    
}

function quick_browse() {
    global $wpdb;
    $type_name = $_GET['post_type'];
    $object_type = object_from_type( $type_name );
    
    $fields = get_object_fields( $object_type->name );
    
    echo "<table class='widefat striped'>
            <tr>";
    foreach ( $fields as $field ) {
        if ( $field->quick_browse == 1 ) {
            echo "<th>{$field->name}</th>";
        }
    }
    echo "<th></th>";
    echo "</tr>";
    
    $args = [
        'numberposts'       => -1,
        'post_type'         => $type_name,
        'post_status'       => 'publish, inherit, pending, private, future, draft'
    ];
    $objects = get_posts( $args );
    
    foreach ( $objects as $object ) {
        $custom = get_post_custom ( $object->ID );
        
        //Check for legacy field names
        if ( isset( $custom['unidentified'] ) && $object_type->name == 'instrument' ) {
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
        
        $edit_url = admin_url( "post.php?post={$object->ID}&action=edit");
        $view_url = get_permalink( $object->ID );
        $row_class = "wpm-quick-row-" . $object->post_status;
        echo "<tr class='$row_class'>";
        foreach ( $fields as $field ) {
            if ( $field->quick_browse == 1 ) {
                $field_name = WPM_PREFIX . $field->field_id;
                $field_value = $custom[$field_name][0];
                echo "<td><a href='$edit_url'>$field_value</a></td>";
            }
        }
        echo "<td><a href='$view_url'>View</a><td>";
        echo "</tr>";
    }
    
    echo "</table>"; 
}

?>