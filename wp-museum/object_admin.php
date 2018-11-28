<?php
/**
 * Administration of object types.
 *
 * Adds pages to the dashboard (Settings|Museum Objects) for the creation and
 * administration of museum object post types. The first page allows administrators
 * to create new object types and shows existing object types. The second page
 * allows for the creation and editing of fields for a particular object.
 *
 * Each field has a name, type (short string, text, date, true/false), help text,
 * and optionally a schema. The schema is a regular expression with <a>, <b>, <c> etc.
 * placeholders that correspond to different subfields of the field. The schema
 * is currently used for sorting in the quick browse page---the field will be sorted
 * first by <a>, then <b>, etc. In the future, the schema could also be used for
 * validating input.
 *
 * The public, required, and quick checkboxes determine whether everyone (or just site
 * contributors) can see the field, whether the field is required when creating an object,
 * and whether the field appears in the quick browse table.
 *
 * @see object_post_types.php
 */

//prefix for new fields created by javascript before being saved. Just has to be something
//unique.
const WPM_FIELD = 'wpm-new-field#';

/**
 * Creates the admin page and adds it to the Settings menu. Only accessible to administrators.
 */
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
add_action( 'admin_menu', 'add_object_admin_page' );

/**
 * Generates a field slug from a field's name.
 *
 * Converts a field's name as set by user, that may have capitals, spaces, special
 * characters to a slug with only lowercase, spaces replaced by '-', and no special
 * characters. Does not do collision checking.
 *
 * @param string $name Field name as set by user.
 */
function field_slug_from_name ( $name ) {
    $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
    return substr( trim( strtolower( str_replace( ' ', '-', $name ) ) ), 0, 255 );
}

/**
 * Display object types.
 *
 * Table of user created objects for main administration page.
 */
function display_objects_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . WPM_PREFIX . "object_types";
    
    $rows = $wpdb->get_results( "SELECT * FROM $table_name");
    
    //process submitted form
    $submitted_form = array();
    if ( count( $_POST ) > 0 ) {
        foreach ( $_POST as $post_key => $post_val ) {
            $key_array = explode("~", $post_key);
            if ( count($key_array) == 2 ) {
                $key_row = $key_array[0];
                $key_col = $key_array[1];
                $submitted_form[$key_row][$key_col] = $post_val;
            }  
        }
    }
    
    if ( isset( $submitted_form['del'] ) ) {
        foreach ( $submitted_form['del'] as $key=>$value ) {
            $wpdb->query( $wpdb->prepare("DELETE FROM $table_name WHERE object_id=%s", $key )  );
        }
    }
    
    if ( isset( $submitted_form['cat_field'] ) ) {
        foreach ( $submitted_form['cat_field'] as $key=>$value ) {
            $wpdb->update (
                $table_name,
                ['cat_field_id' => $value],
                ['object_id' => $key]
            );
        }
    }
    
    if ( count( $_POST ) > 0 ) $rows = $wpdb->get_results( "SELECT * FROM $table_name");
    
    $form_action = $_SERVER['PHP_SELF'] . "?page=wpm-objects-admin";
    ?>
    <div class="wrap">
        <h1>Museum Objects Administration</h1>
        <form name='wpm-objects-admin' method='post' action='<?php echo $form_action;?>'>
        <table class='widefat striped wp-list-table wpm-object'>
        <tr><th class="check-column"><span class="dashicons dashicons-trash"></span><th>Object Type</th><th>ID Field</th><th></th></tr>
        <?php
        foreach ( $rows as $object_row ) {
            $fields = get_object_fields( $object_row->object_id );
            echo "<tr>";
            echo "<td><input name='del~{$object_row->object_id}' type='checkbox' /></td>";
            echo "<td>{$object_row->label}</td>";
            echo "<td><select name='cat_field~{$object_row->object_id}'>";
            echo "<option></option>";
            foreach ( $fields as $field ) {
                echo "<option value='$field->field_id'";
                if ( $object_row->cat_field_id == $field->field_id ) echo " selected='selected' ";
                echo ">$field->name</option>";
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
        echo "<input type='submit' value='Update' name='btn_update' class='button button-primary'> ";
        echo "<a class='button' href='{$_SERVER['PHP_SELF']}?page=wpm-objects-admin&wpm-objects-page=wpm-new-object'>Add New</a>";
        echo "</form>";
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

/**
 * Table of fields for editing individual objects.
 */
function object_fields_table($rows) {
    ?>
    <table id="wpm-object-fields-table" class="widefat striped wp-list-table wpm-object">
        <tr><th></th><th class="check-column"><span class="dashicons dashicons-trash"></span></th></th></th><th>Field</th><th>Type</th><th>Help Text</th><th>Schema</th><th class="check-column">Public</th><th class="check-column">Required</th><th class="check-column">Quick</th></tr>
        <?php
        $order_counter = 0;
        foreach ( $rows as $row ) {
            if ( !is_null( $row->display_order ) && $row->display_order > $order_counter ) $order_counter = $row->display_order + 1;
        }
        foreach ( $rows as $row ) {
            if ( is_null( $row->display_order ) ) {
                $row->display_order = $order_counter;
                $order_counter++;
            }
            $row_id = "wpm-row-" . $row->display_order;
            ?>
            <tr id="<?php echo $row_id; ?>">
                <td>
                    <input type="hidden" id="doi-<?php echo $row_id; ?>" name="<?php echo stripslashes( $row->field_id ); ?>~display_order" value="<?php echo stripslashes( $row->display_order ); ?>"/>
                    <a class='clickable' onclick="wpm_reorder_table('<?php echo $row_id; ?>', -1);"><span class="dashicons dashicons-arrow-up-alt2"></span></a><br />
                    <a class='clickable' onclick="wpm_reorder_table('<?php echo $row_id; ?>', 1);"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
                </td>
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
                <td><input type="text" name="<?php echo $row->field_id; ?>~field_schema" value="<?php echo stripslashes( $row->field_schema ); ?>" /></td>
                <td><input type="checkbox" name="<?php echo $row->field_id; ?>~public" <?php if ($row->public > 0) echo 'checked="checked"'; ?> value="1"/></td>
                <td><input type="checkbox" name="<?php echo $row->field_id; ?>~required" <?php if ($row->required > 0) echo 'checked="checked"'; ?> value="1"/></td>
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

/**
 * Page for editing museum objects.
 *
 * First processes submitted form, then displays page for editing a single object
 * type.
 *
 * @param int   $object_id  The id of the object to edit. If -1, a new object.
 */
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
        if ( isset($_POST['hierarchical']) && $_POST['hierarchical'] == 1 ) $object_data['hierarchical'] = 1;
        else $object_data['hierarchical'] = 0;
        if ( isset($_POST['categorized']) && $_POST['categorized'] == 1 ) $object_data['categorized'] = 1;
        else $object_data['categorized'] = 0;
        if ( isset($_POST['must_featured_image']) && $_POST['must_featured_image'] == 1 ) $object_data['must_featured_image'] = 1;
        else $object_data['must_featured_image'] = 0;
        if ( isset($_POST['must_gallery']) && $_POST['must_gallery'] == 1 ) $object_data['must_gallery'] = 1;
        else $object_data['must_gallery'] = 0;
        if ( isset($_POST['strict_checking']) && $_POST['strict_checking'] == 1 ) $object_data['strict_checking'] = 1;
        else $object_data['strict_checking'] = 0;
        if ( !isset($_POST['object_description']) ) $object_data['description'] = '';
        else $object_data['description'] = str_replace( '~', '-', $_POST['object_description'] );
        if ( isset($_POST['cat_field_id']) ) $object_data['cat_field_id'] = $_POST['cat_field_id'];
        if ( !isset($_POST['object_name']) || $_POST['object_name'] == '' ) {
            wp_die( __( 'All objects must have a name.' ) );
        }
        else {
            $object_data['label'] = str_replace( '~', '-', $_POST['object_name'] );
        }
        if ( $object_id == -1 ) {
            $object_id = new_object( $object_data );
            if ( $object_id == -1 )
                wp_die( __( 'Error creating object.' ) );
        }
        else {
            update_object( $object_id, $object_data );
        }
        
        $submitted_form = array();
        
        //Translate POST into form matching database. The submitted form is
        //encoded in the form row~column (see below).
        foreach ( $_POST as $post_key => $post_val ) {
            if ( strpos( $post_key, '~') && $post_key != 'save-object' ) {
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
                if ( !isset($form_row['required']) ) $form_row['required'] = 0;
                if ( !isset($form_row['quick_browse']) ) $form_row['quick_browse'] = 0;
            }
        }
        
        foreach ( $submitted_form as $key=>&$form_row ) {
            //New fields
            if ( substr( $key, 0, strlen(WPM_FIELD)) === WPM_FIELD ) {
                $form_row['object_id'] = $object_id;
                $form_row['slug'] = field_slug_from_name( $form_row['name'] );
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
                    $field_slugs = [];
                    foreach ( $db_rows as $db_row ) {
                        $db_fields[$db_row->field_id] = $db_row;
                        $field_slugs[$db_row->field_id] = $db_row->slug;
                    }
                    foreach ( $form_row as $key=>$val ) {
                        $change = false;
                        if ( $db_fields[(int)$form_row['field_id']]->$key != $val ) {
                            $change = true;
                            $change_log[] = "FIELD: {$form_row['name']} COL: $key FROM: {$db_fields[$form_row['field_id']]->$key} TO: $val";
                        }
                        if ( !isset( $field_slugs[$form_row['field_id']] ) ) $old_slug = '';
                        else $old_slug = $field_slugs[$form_row['field_id']];
                        $form_row['slug'] = field_slug_from_name( $form_row['name'] );
                        if ( $form_row['slug'] != $old_slug ) {
                            $meta_table = $wpdb->prefix . 'postmeta';
                            $wpdb->query ("UPDATE $meta_table SET meta_key='{$form_row['slug']}' WHERE meta_key=$old_slug");
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
    }
    
    if ( $object_id != -1 ) {
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE object_id = %d ORDER BY display_order", $object_id ) );
        $object_table = $wpdb->prefix . WPM_PREFIX . "object_types";
        $object_data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $object_table WHERE object_id = %d", $object_id ), ARRAY_A )[0];
    }
    else {
        $rows = [];    
    }
    if ( !isset( $object_data['label'] ) ) $object_data['label'] = '';
    if ( !isset( $object_data['description'] ) ) $object_data['description'] = '';
    if ( !isset( $object_data['hierarchical'] ) ) $object_data['hierarchical'] = 0;
    if ( !isset( $object_data['categorized'] ) ) $object_data['categorized'] = 0;
    if ( !isset( $object_data['must_featured_image'] ) ) $object_data['must_featured_image'] = 0;
    if ( !isset( $object_data['must_gallery'] ) ) $object_data['must_gallery'] = 0;
    if ( !isset( $object_data['strict_checking'] ) ) $object_data['strict_checking'] = 0;

    $id_select = "<select name='cat_field_id'>";
    $id_select .= "<option></option>";
    $fields = get_object_fields ( $object_id );
    if ( !is_null( $fields ) ) {
        foreach ( $fields as $field ) {
            $id_select .= "<option value='$field->field_id'";
            if ( $object_data['cat_field_id'] == $field->field_id ) $id_select .= " selected='selected' ";
            $id_select .= ">$field->name</option>";
        }
    }
    $id_select .= "</select>";

    ?>
    <div class="wrap">
        <form name="object_fields_form" method="post" action="<?php echo $form_action; ?>"> 
        <div id='wpm-object-top-buttons'>
            <input type="submit" class="button button-primary button-large" name="save-object" value="Save" />
        </div>
        <h1 class="wp-heading">Edit Museum Object Type</h1>
            <table id="wpm-edit-object" class="wpm-object">
                <tr><th>Object Name:</th><td><input type="text" style="width:100%;" name="object_name" value="<?php echo $object_data['label'];?>"/></td></tr>
                <tr><th>Object Description:</th><td><textarea style="width:100%;" name="object_description"><?php echo $object_data['description'];?></textarea></td></tr>
                <tr><th>ID Field:</th></td><td><?php echo $id_select;?></td></tr>
                <tr><th>Options:</th><td>
                    <table><tr>
                        <td><ul>
                        <li><input type="checkbox" <?php if ($object_data['strict_checking'] > 0) echo 'checked="checked"';?> name="strict_checking" value="1"/> Strictly enforce requirements</li>
                        <li><input type="checkbox" <?php if ($object_data['hierarchical'] > 0) echo 'checked="checked"';?> name="hierarchical" value="1"/> Hierarchical</li>
                        <li><input type="checkbox" <?php if ($object_data['categorized'] > 0) echo 'checked="checked"';?> name="categorized" value="1"/> Must be categorized</li>
                        </ul></td>
                        <td><ul>
                        <li><input type="checkbox" <?php if ($object_data['must_featured_image'] > 0) echo 'checked="checked"';?> name="must_featured_image" value="1"/> Must have featured image</li>
                        <li><input type="checkbox" <?php if ($object_data['must_gallery'] > 0) echo 'checked="checked"';?> name="must_gallery" value="1"/> Must have image gallery</li>
                        </ul></td>
                    </table></tr>
            </table>
        <h2 class="wp-heading">Object Fields</h2>
        <?php
        object_fields_table($rows);
        ?>
        <div id='wpm-object-bottom-buttons'>
            <button type='button' class='button button-large' onclick='add_field("<?php echo WPM_FIELD; ?>", <?php echo count($rows); ?>);'>Add Field</button>
            <input type="submit" class="button button-primary button-large" name="save-object" value="Save" />
        </div>
        </form>
    </div>
    <?php  
}

/**
 * Displays appropriate object administration page.
 *
 * Top-level function for object admin page that displays appropriate editing page depending on get parameter.
 */
function objects_admin_page() {
    //fix_field_slugs();
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

/**
 * Imports instruments from previous version of plugin into new custom object types.
 */
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
            $old_field['required'] = $old_field['in_description'];
            unset ( $old_field['in_description'] );
            $old_field['object_id'] = $object_id;
            $old_field['slug'] = field_slug_from_name( $old_field['name'] );
            $wpdb->insert( $object_fields_table, $old_field );
        }  
    }   
      
    $old_instrument_posts = $wpdb->get_results( "SELECT ID FROM {$wpdb->posts} WHERE post_type='instrument'" );
    foreach ( $old_instrument_posts as $old_instrument ) {
        $updated = $wpdb->update( $wpdb->posts, ['post_type'=>type_name($object_name)], ['ID' => $old_instrument->ID] ); 
    }
    
    //update meta_fields
    $args = [
        'numberposts'           => -1,
        'post_type'             => type_name( $object_name ),
        'post_status'           => 'any'
    ];
    $instrument_posts = get_posts ( $args );
    $object_type = get_object ( get_object_id ( $object_name ) );
    
}