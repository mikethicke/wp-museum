var new_field_counter = 0;

/**
 * Adds a new field to the edit object form.
 * 
 * @param {string} wpm_field 
 * @param {int} num_fields 
 */
function add_field(wpm_field, num_fields) {
    var field_prefix = wpm_field + new_field_counter;

    var empty_div = document.getElementById("wpm-object-fields-empty");
    if (empty_div != null) empty_div.style.display = "none";

    var fields_table = document.getElementById("wpm-object-fields-table");
    var row = fields_table.insertRow(-1);
    var doi_cell = row.insertCell(-1);
    var delete_cell = row.insertCell(-1);
    var name_cell = row.insertCell(-1);
    var type_cell = row.insertCell(-1);
    var help_cell = row.insertCell(-1);
    var schema_cell = row.insertCell(-1);
    var public_cell = row.insertCell(-1);
    var required_cell = row.insertCell(-1);
    var quick_cell = row.insertCell(-1);

    row.id = "wpm-row-" + num_fields;

    var doi_input = document.createElement("input");
    doi_input.setAttribute("type", "hidden");
    doi_input.name = field_prefix + '~display_order';
    doi_input.value = num_fields;
    doi_input.id = "doi-wpm-row-" + doi_input.value;
    doi_cell.appendChild(doi_input);
    var doi_text = "<a class='clickable' onclick='wpm_reorder_table(\"wpm-row-" + doi_input.value + "\", -1);'><span class='dashicons dashicons-arrow-up-alt2'></span></a><br />";
    doi_text = doi_text + "<a class='clickable' onclick='wpm_reorder_table(\"wpm-row-" + doi_input.value + "\", 1);'><span class='dashicons dashicons-arrow-down-alt2'></span></a><br />";
    doi_cell.innerHTML = doi_cell.innerHTML + doi_text;

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

    var schema_input = document.createElement("input");
    schema_input.setAttribute("type", "text")
    schema_input.name = field_prefix + "~field_schema";
    schema_cell.appendChild(schema_input);

    var public_checkbox = document.createElement("input");
    public_checkbox.setAttribute("type", "checkbox");
    public_checkbox.name = field_prefix + "~public";
    public_checkbox.value = 1;
    public_cell.appendChild(public_checkbox);

    var required_checkbox = document.createElement("input");
    required_checkbox.setAttribute("type", "checkbox");
    required_checkbox.name = field_prefix + "~required";
    required_checkbox.value = 1;
    required_cell.appendChild(required_checkbox);

    var quick_checkbox = document.createElement("input");
    quick_checkbox.setAttribute("type", "checkbox");
    quick_checkbox.name = field_prefix + "~quick_browse";
    quick_checkbox.value = 1;
    quick_cell.appendChild(quick_checkbox);

    new_field_counter += 1;
}

/**
 * Javascript for reordering fields when editing object fields.
 */
function wpm_reorder_table(row_id, direction) {
    row = document.getElementById(row_id);
    row_input = document.getElementById("doi-" + row_id);
    table = document.getElementById("wpm-object-fields-table");
    swapped = false;

    if (direction == 1 && row.rowIndex < row.parentNode.rows.length - 1) {
        swap_row = row.parentNode.rows[row.rowIndex + 1];
        row.parentNode.insertBefore(row.parentNode.removeChild(swap_row), row);
        swapped = true;
    } else if (direction == -1 && row.rowIndex > 1) {
        swap_row = row.parentNode.rows[row.rowIndex - 1];
        row.parentNode.insertBefore(row.parentNode.removeChild(row), swap_row);
        swapped = true;
    }

    if (swapped) {
        swap_row_input = document.getElementById("doi-" + swap_row.id);
        save_value = swap_row_input.value;
        swap_row_input.value = row_input.value;
        row_input.value = save_value;
    }
}

/**
 * Creates a new child post of current object and loads edit screen for that object.
 *
 * @param string parent post_id of parent post.
 * @see object-ajax.php::create_new_obj_aj()
 */
function new_obj(parent) {
    var data = {
        'action': 'create_new_obj_aj',
        'parent': parent,
        'nonce': admin_ajax_data.nonce
    };

    jQuery.post(ajaxurl, data, function(response) {
        window.location.href = "post.php?post=" + response + "&action=edit";
    });
}

/**
 * Creates backup of all object images and makes available for download.
 */
jQuery(document).ready(function($) {
    var backup_in_progress = false;
    $("#image-new-backup").click(function() {
        if (backup_in_progress) return;
        backup_in_progress = true;
        $("#image-backup-status").show();
        $("#image-backup-status").html("Image backup in progress. Do not refresh or close page. This can take several minutes.");
        var data = {
            'action': 'export_images_aj',
            'nonce': admin_ajax_data.nonce
        };
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            success: function(response) {
                $("#image-backup-status").hide();
                $("#image-backups-table").html(response);
            },
            error: function(response) {
                $("#image-backup-status").html("Image backup failed.");
            },
            complete: function() {
                backup_in_progress = false;
            }
        });
    });
});

/**
 * Deletes an object kind after confirmation.
 */
jQuery(document).ready(function($) {
    $("#kinds-admin-table").on('click', '.delete-kind-button', function() {
        if (confirm("Are you sure you want to delete this kind? This cannot be undone. Post data will not be affected.")) {
            var kind_id = $(this).data('kind-id');
            var data = {
                'action': 'delete_kind_aj',
                'nonce': admin_ajax_data.nonce,
                'kind_id': kind_id
            };
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                success: function(response) {
                    $("#kind-row-" + kind_id).remove();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("Error, status = " + textStatus + ", " + "error thrown: " + errorThrown);
                }
            });
        }
    });
});

/**
 * Deletes an image backup after confirmation.
 */
jQuery(document).ready(function($) {
    $("#image-backup-table").on('click', '.delete-zip-button', function() {
        if (confirm("Are you sure you want to delete this backup? This cannot be undone.")) {
            var zip_item = $(this).data('zip-item');
            var data = {
                'action': 'delete_image_zip_aj',
                'nonce': admin_ajax_data.nonce,
                'zip_item': zip_item
            };
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                success: function(response) {
                    var zip_id = "#backup-row-" + zip_item.replace('.', '_');
                    $(zip_id).remove();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("Error, status = " + textStatus + ", " + "error thrown: " + errorThrown);
                }
            });
        }

    });
});

/**
 * Shows CSV upload form when clicking "Import CSV" for a kind.
 */
jQuery(document).ready(function($) {
    $(".import-csv-button").click(function() {
        $(".upload-plugin-wrap").show();
        $(".upload-plugin").show();
        $("#import-kind-id").val($(this).data('kind-id'));
    });
});

/**
 * Make CSV upload Import button enabled when a file is selected.
 */
jQuery(document).ready(function($) {
    $("#csv-upload-file").change(function() {
        var filename = $(this).val();
        if ('' != filename) {
            if (filename.toLowerCase().endsWith('.csv')) {
                $("#csv-upload-help").css("color", "inherit");
                $("#csv-upload-help").text("Click Import to import CSV. This will overwrite existing postdata and cannot be undone.");
                $("#csv-upload-submit").prop("disabled", false);
            } else {
                $("#csv-upload-help").css("color", "red");
                $("#csv-upload-help").text("Selected file must be a .CSV (comma-separated-value) file.");
                $("#csv-upload-submit").prop("disabled", true);
            }
        } else {
            $("#csv-upload-help").css("color", "inherit");
            $("#csv-upload-help").text("Select a CSV file to import.");
            $("#csv-upload-submit").prop("disabled", true);
        }
    });
})