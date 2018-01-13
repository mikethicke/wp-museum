<?php
/**
 * Base custom post type that creates specialized custom post types.
 */

const DB_VERSION = 0.05;
const DB_SHOW_ERRORS = true;
const WPM_FIELD = 'wpm-new-field#';

add_action( 'admin_menu', 'add_object_admin_page' );
add_action( 'plugins_loaded', 'db_version_check' );

function db_version_check() {
    $version = get_site_option("wpm_db_version");
    if ( $version != DB_VERSION ) {
        create_objects_table();
        create_object_fields_table();
        update_option("wpm_db_version", DB_VERSION);
    }
        
}

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
        length int(5),
        PRIMARY KEY  (field_id)
    );";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);   
}

function get_object_types( $object_id=-1, $object_name='') {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    if ( $object_id != -1 ) {
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE object_id=%s", $object_id ) );
    }
    elseif ( $object_name != '' ) {
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE name=%s", $object_name ) );
    }
    else {
        $results = $wpdb->get_results( "SELECT * FROM $table_name");  
    }
    return $results;
}

function get_object_fields( $object_name ) {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_fields";
    $object = get_object_types(-1, $object_name)[0];
    $results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE object_id=%s", $object->object_id ) );
    return $results;
}

function display_objects_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    $rows = $wpdb->get_results( "SELECT * FROM $table_name");
    
    ?>
    <div class="wrap">
        <h1>Museum Objects Administration</h1>
        <table class='widefat striped wp-list-table wpm-object'>
        <tr><th class="check-column"><span class="dashicons dashicons-trash"></span><th>Object Type</th><th>ID Field</th><th></th></tr>
        <?php
        foreach ( $rows as $object_row ) {
            $fields = get_object_fields( $object_row->name );
            echo "<tr>";
            echo "<td></td>";
            echo "<td>{$object_row->label}</td>";
            echo "<td><select>";
            foreach ( $fields as $field ) {
                echo "<option>$field->name</option>";
            }
            echo "</select></td>";
            echo "<td><a href='{$_SERVER['PHP_SELF']}?page=wpm-objects-admin&wpm-objects-page=wpm-edit-object&oid={$object_row->object_id}'>Edit</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        if ( count($rows) < 1 ) {
            echo "<div class='empty-table-notification'>There are currently no object types.</div>";
        }
        echo "<div id='wpm-object-bottom-buttons'>";
        echo "<a class='button' href='{$_SERVER['PHP_SELF']}?page=wpm-objects-admin&wpm-objects-page=wpm-new-object'>Add New</a>";
    echo "</div></div>";
    
    //Test for old instrument_fields table
    $instrument_fields_table = $wpdb->prefix . 'instrument_fields';
    $count_result = $wpdb->query(
        "
        SELECT COUNT(1) AS 'result' 
        FROM information_schema.tables 
        WHERE table_schema = '{$wpdb->dbname}' 
        AND table_name = '{$instrument_fields_table}';
        "
    );
    $table_count = (int)$wpdb->last_result[0]->result;
    if ( $table_count > 0) {
        echo "<a href='{$_SERVER['PHP_SELF']}?page=wpm-objects-admin&import-legacy=1'>Import legacy instruments</a>";
    }
}

function object_fields_table($rows) {
    ?>
    <table id="wpm-object-fields-table" class="widefat striped wp-list-table wpm-object">
        <tr><th class="check-column"><span class="dashicons dashicons-trash"></span></th></th></th><th>Field</th><th>Type</th><th>Help Text</th><th class="check-column">Public</th><th class="check-column">Visible</th><th class="check-column">Quick</th></tr>
        <?php
        foreach ( $rows as $row ) {
        ?>
            <tr>
                <td>
                    <input type="hidden" name="<?php echo stripslashes( $row->field_id ); ?>~field_id" value="<?php echo stripslashes( $row->field_id ); ?>"/>
                    <input type="checkbox" name="<?php echo stripslashes( $row->field_id ); ?>~delete" value="1"/>
                </td>
                <td><input type="text" name="<?php echo $row->field_id; ?>~name" value="<?php echo stripslashes( $row->name ); ?>" /></td>
                <td>
                    <select name="<?php echo $row->field_id; ?>~type">
                        <option value="varchar" <?php if ($row->type == 'varchar') print 'selected="selected"'; ?>">Short String</option>
                        <option value="text" <?php if ($row->type == 'text') print 'selected="selected"'; ?>">Text</option>
                        <option value="date" <?php if ($row->type == 'date') print 'selected="selected"'; ?>">Date</option>
                        <option value="tinyint" <?php if ($row->type == 'tinyint') print 'selected="selected"'; ?>">True/False</option>
                    </select>
                </td>
                <td><textarea name="<?php echo $row->field_id; ?>~help_text" rows=3 cols=25><?php echo stripslashes ( $row->help_text );?></textarea></td>
                <td><input type="checkbox" name="<?php echo $row->field_id; ?>~public" <?php if ($row->public > 0) echo 'checked="checked"'; ?> value="1"/></td>
                <td><input type="checkbox" name="<?php echo $row->field_id; ?>~visible" <?php if ($row->visible > 0) echo 'checked="checked"'; ?> value="1"/></td>
                <td><input type="checkbox" name="<?php echo $row->field_id; ?>~quick_browse" <?php if ($row->quick_browse > 0) echo 'checked="checked"'; ?> value="1"/></td>
            </tr>
        <?php
        } //foreach ( $rows as $row )
        ?>
    </table>
    <?php
    if ( count($rows) < 1 ) {
        echo "<div class='empty-table-notification' id='wpm-object-fields-empty'>Object contains no fields.</div>";
    }
}

add_action( 'admin_footer', 'add_field_js' );
function add_field_js() {
    ?>
    <script type="text/javascript">
        var new_field_counter = 0;
        function add_field(wpm_field) {
            var field_prefix = wpm_field + new_field_counter;
            
            var empty_div = document.getElementById("wpm-object-fields-empty");
            if ( empty_div != null ) empty_div.style.display = "none";
            
            var fields_table = document.getElementById("wpm-object-fields-table");
            var row = fields_table.insertRow(-1);
            var delete_cell = row.insertCell(0);
            var name_cell = row.insertCell(1);
            var type_cell = row.insertCell(2);
            var help_cell = row.insertCell(3);
            var public_cell = row.insertCell(4);
            var visible_cell = row.insertCell(5);
            var quick_cell = row.insertCell(5);
            
            var delete_checkbox = document.createElement("input");
            delete_checkbox.setAttribute("type", "checkbox");
            delete_checkbox.name = field_prefix + "~delete";
            delete_checkbox.value = 1;
            delete_cell.appendChild(delete_checkbox);
            
            var field_id_input = document.createElement("input");
            field_id_input.setAttribute("type", "hidden");
            field_id_input.name = field_prefix + "~field_id";
            name_cell.appendChild(field_id_input);
            
            var name_input = document.createElement("input");
            name_input.setAttribute("type", "text")
            name_input.name = field_prefix + "~name";
            name_cell.appendChild(name_input);
            
            var type_select = document.createElement("select");
            type_select.setAttribute("name", field_prefix + "~type");
            var option_varchar = document.createElement("option");
            option_varchar.value = "varchar";
            option_varchar.text = "Short String";
            var option_text = document.createElement("option");
            option_text.value = "text";
            option_text.text = "Text";
            var option_date = document.createElement("option");
            option_date.value = "date";
            option_date.text = "Date";
            var option_tinyint = document.createElement("option");
            option_tinyint.value = "tinyint";
            option_tinyint.text = "True/False";
            type_select.appendChild(option_varchar);
            type_select.appendChild(option_text);
            type_select.appendChild(option_date);
            type_select.appendChild(option_tinyint);
            type_cell.appendChild(type_select);
            
            var help_text = document.createElement("textarea");
            help_text.name = field_prefix + "~help_text";
            help_cell.appendChild(help_text);
            
            var public_checkbox = document.createElement("input");
            public_checkbox.setAttribute("type", "checkbox");
            public_checkbox.name = field_prefix + "~public";
            public_checkbox.value = 1;
            public_cell.appendChild(public_checkbox);
            
            var visible_checkbox = document.createElement("input");
            visible_checkbox.setAttribute("type", "checkbox");
            visible_checkbox.name = field_prefix + "~visible";
            visible_checkbox.value = 1;
            visible_cell.appendChild(visible_checkbox);
            
            var quick_checkbox = document.createElement("input");
            quick_checkbox.setAttribute("type", "checkbox");
            quick_checkbox.name = field_prefix + "~quick_browse";
            quick_checkbox.value = 1;
            quick_cell.appendChild(quick_checkbox);
                      
            new_field_counter += 1;
        }
    </script>
    <?php
}

function object_name ( $object_label ) {
    return strtolower( str_replace( " ", "-", $object_label ) );
}

function update_object ( $object_id, $object_data ) {
    if ( $object_id == -1 ) return -1;
    
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    
    if ( isset( $object_data['label'] ) ) $object_data['name'] = object_name( $object_data['label'] );
    
    return $wpdb->update( $table_name, $object_data, ['object_id'=>$object_id] ); 
}

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

function edit_object($object_id=-1) {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    
    $form_action = $_SERVER['PHP_SELF'] . "?page=wpm-objects-admin&wpm-objects-page=wpm-edit-object";
    if ( $object_id != -1 ) $form_action .= "&oid=" . $object_id;
    
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_fields";
    
    //Process submitted form
    if ( isset($_POST) and count($_POST) > 0 ) {
        if ( $object_id == -1 ) {
            if ( !isset($_POST['object_name']) || $_POST['object_name'] == '' ) {
                wp_die( __( 'All objects must have a name.' ) );
            }
            else $object_name = $_POST['object_name'];
            if ( !isset($_POST['object_description']) ) $object_description = '';
            else $object_description = $_POST['object_description'];
            $object_id = new_object($object_name, $object_description);
            if ( $object_id == -1 )
                wp_die( __( 'Error creating object.' ) );
        }
        else {
            update_object( $object_id, [
                'label'         => $_POST['object_name'],
                'description'   => $_POST['object_description']
            ]);
        }
        
        $submitted_form = array();
        
        //Translate POST into form matching database. The submitted form is
        //encoded in the form row~column (see below).
        foreach ( $_POST as $post_key => $post_val ) {
            if ( $post_key != 'object_name' && $post_key != 'object_description' && $post_key != 'save-object' ) {
                //print "KEY: $post_key VAL: $post_val <br>";
                $key_array = explode("~", $post_key);
                $key_row = $key_array[0];
                $key_col = $key_array[1];
                $submitted_form[$key_row][$key_col] = $post_val;   
            }
        }
        
        //for checkboxes that weren't submitted, we need to explicitly set the value to 0
        if ( isset($submitted_form) ) {
            foreach ( $submitted_form as &$form_row ) {
                if ( !isset($form_row['delete']) ) $form_row['delete'] = 0;
                if ( !isset($form_row['public']) ) $form_row['public'] = 0;
                if ( !isset($form_row['visible']) ) $form_row['visible'] = 0;
                if ( !isset($form_row['quick_browse']) ) $form_row['visible'] = 0;
            }
        }
        
        foreach ( $submitted_form as $key=>&$form_row ) {
            //New fields
            if ( substr( $key, 0, strlen(WPM_FIELD)) === WPM_FIELD ) {
                $form_row['object_id'] = $object_id;
                if ( $form_row['delete'] != 1 ) {
                    unset($form_row['delete']);
                    $wpdb->insert( $table_name, $form_row );
                }
            }
            else {
                //Delete fields
                if ( $form_row['delete'] == 1 ) {
                    if ( $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE object_id=%d AND field_id=%d", $object_id, $form_row['field_id'] ) ) ) {
                        $change_log[] = "Deleted FIELD: {$form_row['name']}";
                    }
                    else $change_log[] = "Error deleting FIELD: {$form_row['label']}";
                }
                //Update fields
                else {
                    unset( $form_row['delete'] );
                    $db_rows = $wpdb->get_results( "SELECT * FROM $table_name WHERE object_id=$object_id" );
                    foreach ( $db_rows as $db_row ) {
                        $db_fields[$db_row->field_id] = $db_row;
                    }
                    foreach ( $form_row as $key=>$val ) {
                        $change = false;
                        if ( $db_fields[(int)$form_row['field_id']]->$key != $val ) {
                            $change = true;
                            $change_log[] = "FIELD: {$form_row['name']} COL: $key FROM: {$db_fields[$form_row['field_id']]->$key} TO: $val";
                        }
                        if ( $change ) {
                            $wpdb->update (
                                $table_name,
                                $form_row,
                                ['field_id' => $form_row['field_id']]
                            );
                        }
                    }
                }
            }
        }
        
        if ( $object_id != -1 ) {
            $rows = $wpdb->get_results( "SELECT * FROM $table_name WHERE object_id=$object_id" );
        }
        else $rows = [];
    }
    
    if ( $object_id != -1 ) {
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE object_id = %d", $object_id ) );
        $object_table = $wpdb->prefix . WPM_PREFIX . "object_types";
        $object_data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $object_table WHERE object_id = %d", $object_id ), ARRAY_A )[0];
    }
    else {
        $rows = [];    
    }
    if ( !isset( $object_data['label'] ) ) $object_data['label'] = '';
    if ( !isset( $object_data['description'] ) ) $object_data['description'] = '';
    ?>
    <div class="wrap">
        <h1 class="wp-heading">Edit Museum Object Type</h1>
        <form name="object_fields_form" method="post" action="<?php echo $form_action; ?>">
            <table id="wpm-edit-object" class="wpm-object">
                <tr><th>Object Name:</th><td><input type="text" style="width:100%;" name="object_name" value="<?php echo $object_data['label'];?>"></input></td></tr>
                <tr><th>Object Description:</th><td><textarea style="width:100%;" name="object_description"><?php echo $object_data['description'];?></textarea></td></tr> 
            </table>
        <?php
        object_fields_table($rows);
        ?>
        <div id='wpm-object-bottom-buttons'>
            <button type='button' class='button button-large' onclick='add_field("<?php echo WPM_FIELD; ?>");'>Add Field</button>
            <input type="submit" class="button button-primary button-large" name="save-object" value="Save" />
        </div>
        </form>
    </div>
    <?php  
}

function objects_admin_page() {
    if ( isset($_GET['wpm-objects-page']) ) $wpm_page = $_GET['wpm-objects-page'];
    else $wpm_page = 'wpm-objects-table';
    switch($wpm_page) {
        case 'wpm-objects-table' :
             if ( isset($_GET['import-legacy']) ) {
                import_legacy_instruments();
             }
             display_objects_table();
             break;
        case 'wpm-new-object' :
            edit_object();
            break;
        case 'wpm-edit-object' :
            if ( isset($_GET['oid']) ) {
                edit_object($_GET['oid']);
            }
            else {
                edit_object();
            }
            break;
    }
   
}

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

function import_legacy_instruments() {
    global $wpdb;
    $old_fields_table = $wpdb->prefix . 'instrument_fields';
    $old_fields = $wpdb->get_results( "SELECT * FROM $old_fields_table", ARRAY_A );
    
    $object_types_table = $wpdb-> prefix . WPM_PREFIX . 'object_types';
    $object_fields_table = $wpdb->prefix . WPM_PREFIX . 'object_fields';
    
    $existing_fields_table = $wpdb->get_results( "SELECT object_id FROM $object_types_table WHERE label='Instrument'" );
    if ( count($existing_fields_table) > 0 ) {
        $object_id = $existing_fields_table[0]->object_id;
    }
    else {
        $object_id = new_object( 'Instrument', 'A scientific instrument' );
    }
    $object_name = object_name_from_id($object_id);
    
    foreach ( $old_fields as $old_field ) {
        $existing_field = $wpdb->get_results( "SELECT field_id FROM $object_fields_table WHERE object_id='$object_id' AND name='{$old_field['name']}'" );
        if ( count( $existing_field ) == 0 ) {
            unset ( $old_field['id'] );
            unset ( $old_field['slug'] );
            unset ( $old_field['description_order'] );
            $old_field['visible'] = $old_field['in_description'];
            unset ( $old_field['in_description'] );
            $old_field['object_id'] = $object_id;
            $wpdb->insert ( $object_fields_table, $old_field );
        }  
    }   
    
    
    $old_instrument_posts = $wpdb->get_results( "SELECT ID FROM {$wpdb->posts} WHERE post_type='instrument'" );
    foreach ( $old_instrument_posts as $old_instrument ) {
        $updated = $wpdb->update( $wpdb->posts, ['post_type'=>type_name($object_name)], ['ID' => $old_instrument->ID] ); 
    }
}

function add_object_admin_page() {
    add_submenu_page(
        'options-general.php',
        'Museum Objects',
        'Museum Objects',
        'manage_options',
        'wpm-objects-admin',
        'objects_admin_page'
    );
}



?>