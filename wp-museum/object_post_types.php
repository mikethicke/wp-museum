<?php

const VARCHAR_LENGTH = 255;

add_action( 'plugins_loaded', 'update_object_tables' );

function update_object_table ( $object_id ) {
    global $wpdb;
    $object_type_table = $wpdb->prefix . WPM_PREFIX . "object_types";
    $object_fields_table = $wpdb->prefix . WPM_PREFIX . "object_fields";
    
    $object_type_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $object_type_table WHERE object_id=%s", $object_id ) )[0];
    $object_fields_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $object_fields_table WHERE object_id=%s", $object_id ) );
    $object_name = $object_type_data->name;
    $object_table_name = $wpdb->prefix . WPM_PREFIX . $object_name;
    
    $count_result = $wpdb->query(
        "
        SELECT COUNT(1) AS 'result' 
        FROM information_schema.tables 
        WHERE table_schema = '{$wpdb->dbname}' 
        AND table_name = '{$object_table_name}';
        "
    );
    $table_count = (int)$wpdb->last_result[0]->result;
    
    if ( $table_count == 0 ) {
        $sql = "CREATE TABLE $object_table_name (
            {$object_name}_id mediumint(9) NOT NULL AUTO_INCREMENT";
        foreach ( $objects_fields_data as $field ) {
            $sql .= $field->name . ' ';
            switch ( $field->type ) {
                case 'varchar':
                    $sql .= ", varchar(" . VARCHAR_LENGTH .")";
                    break;
                case 'text':
                    $sql .= ", text";
                    break;
                case 'tinyint':
                    $sql .= ", tinyint(1)";
                    break;
                case 'date':
                    
            }
        }
    }
}

function update_object_tables() {
    global $wpdb;
    $object_type_table = $wpdb->prefix . WPM_PREFIX . "object_types";
    $object_id_rows = $wpdb->get_results( "SELECT object_id FROM $object_type_table" );
    foreach ( $object_id_rows as $object_id_row ) {
        update_object_table( $object_id_row->object_id );
    }
}

?>