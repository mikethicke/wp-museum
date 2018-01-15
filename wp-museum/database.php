<?php
/**
 * Functions for interfacing with custom database tables (object_types & object_fields).
 */

add_action( 'plugins_loaded', 'db_version_check' );

const DB_VERSION = 0.07;
const DB_SHOW_ERRORS = true;

/**
 * Checks whether site database schema is up-to-date and updates if not.
 */
function db_version_check() {
    $version = get_site_option("wpm_db_version");
    if ( $version != DB_VERSION ) {
        create_objects_table();
        create_object_fields_table();
        update_option("wpm_db_version", DB_VERSION);
    }      
}

/**
 * Create table for object types, or sync site table.
 */
function create_objects_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    $wpdb->show_errors = DB_SHOW_ERRORS;
    $sql = "CREATE TABLE $table_name (
        object_id mediumint(9) NOT NULL AUTO_INCREMENT,
        cat_field mediumint(9),
        name varchar(255),
        label varchar(255),
        description text,
        activated tinyint(1),
        PRIMARY KEY  (object_id)
    );";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
}

/**
 * Create / sync table for object fields.
 */
function create_object_fields_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_fields";
    $wpdb->show_errors = DB_SHOW_ERRORS;
    $sql = "CREATE TABLE $table_name (
        field_id mediumint(9) NOT NULL AUTO_INCREMENT,
        object_id mediumint(9),
        name varchar(255),
        label varchar(255),
        type varchar(255),
        display_order int(5),
        public tinyint(1),
        visible tinyint(1),
        quick_browse tinyint(1),
        help_text varchar(255),
        field_schema varchar(255),
        length int(5),
        PRIMARY KEY  (field_id)
    );";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);   
}

/**
 * Get object type given object id.
 *
 * @param int/string $object_id ID of object in database.
 *
 * @return object Database entry for object as object.
 */
function get_object ( $object_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE object_id=%s", $object_id ) );
    return $results[0];
}

/**
 * Get object type's id with given name.
 *
 * @param string $object_name The object's name.
 *
 * @return string The object's ID.
 */
function get_object_id ( $object_name ) {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT object_id FROM $table_name WHERE name=%s", $object_name ) );
    return $results[0]->object_id;
}

/**
 * Get object type's name from ID.
 *
 * @param string/int $object_id The ID of the object type.
 *
 * @return string The object type's name.
 */
function object_name_from_id( $object_id ) {
    global $wpdb;
    $object_types_table = $wpdb-> prefix . WPM_PREFIX . 'object_types';
    $result = $wpdb->get_results( "SELECT name FROM $object_types_table WHERE object_id = $object_id" );
    if ( count( $result ) > 0 ) {
        return $result[0]->name;
    }
    else {
        return '';
    }
}

/**
 * Get all object types from database.
 *
 * @return [object] Array of objects corresponding to database rows.
 */
function get_object_types() {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    $results = $wpdb->get_results( "SELECT * FROM $table_name");  
    return $results;
}

/**
 * Get fields associated with a given object type.
 *
 * @param string/int $object_id The ID of the object type.
 *
 * @return [object] Array of objects corresponding to rows of object field table.
 */
function get_object_fields( $object_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_fields";
    $results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE object_id=%s", $object_id ) );
    return $results;
}

/**
 * Converts object type's label to name: all lowercase, spaces replaced by dashes.
 *
 * @param string $object_label The object type's label.
 *
 * @return string The object type's name.
 */
function object_name ( $object_label ) {
    return strtolower( str_replace( " ", "-", $object_label ) );
}

/**
 * Update object type in database.
 *
 * @param string/int $object_id The object type to be updated. Must exist in db.
 * @param ['field'=>'value'] $object_data Array of field/value pairs.
 */
function update_object ( $object_id, $object_data ) {
    if ( $object_id == -1 ) return -1;
    
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    
    if ( isset( $object_data['label'] ) ) $object_data['name'] = object_name( $object_data['label'] );
    
    return $wpdb->update( $table_name, $object_data, ['object_id'=>$object_id] ); 
}

/**
 * Create new object type.
 *
 * @param string $object_label Object type's label.
 * @param string $object_description Short description of object type.
 * @param int $activated 1 if object type is active.
 */
function new_object($object_label, $object_description='', $activated=1) {
    if ( $object_label == '' ) return -1;
    
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    
    $object_name = object_name( $object_label );
    $data = [
        'name'          => $object_name,
        'label'         => $object_label,
        'description'   => $object_description,
        'activated'     => $activated
    ];
    $wpdb->insert( $table_name, $data );
    return $wpdb->insert_id;
}


?>