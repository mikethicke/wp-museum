<?php

/**
 * Adds theme options page to admin menu.
 * See: http://ottopress.com/2009/wordpress-settings-api-tutorial/
 */
function add_theme_options_page() {
    add_theme_page('UTSIC Theme Options', 'UTSIC Theme Options', 'manage_options', 'utsic_theme_options', 'utsic_options_page');
}
add_action('admin_menu', 'add_theme_options_page');

/**
 * Creates options page for UTSIC Theme.
 * See: http://ottopress.com/2009/wordpress-settings-api-tutorial/
 */
function utsic_options_page() {
    ?>
    <div>
    <h2>UTSIC Theme Options</h2>
    <form action="options.php" method="post">
    <?php wp_enqueue_script('my-upload'); ?>
    <?php settings_fields('utsic_options'); ?>
    <?php do_settings_sections('utsic_theme_options'); ?>
    
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form></div>
    
    <?php    
}

/**
 * Callback to display text for UTSIC Options section.
 */
function utsic_options_text() {
    ?>
    <div class="options_section_description">
        Homepage boxes are left-to-right. If the Image URL is left blank, the item's featured image will be used instead if it exists.
    </div>
    <?php
}

/**
 * Callback to display form section for intro blurb
 */
function utsic_intro_blurb_setting() {
    $options = get_option('utsic_options');
    ?>
    <textarea cols="70" rows="10" id="utsic_intro_blurb" name="utsic_options[utsic_intro_blurb]"><?php echo $options['utsic_intro_blurb']; ?></textarea>
    <?php
}

/**
 * Callback to display form section for posts per page
 */
function utsic_posts_per_page_setting() {
    $options = get_option('utsic_options');
    ?>
    <input id="utsic_posts_per_page" type="text" name="utsic_options[utsic_posts_per_page]" size="5" value="<?php echo $options['utsic_posts_per_page']; ?>" />
    <?php
}

/**
 * Callback to display form section for donate url
 */
function utsic_donate_url_setting() {
    $options = get_option('utsic_options');
    ?>
    <input id="utsic_donate_url" type="text" name="utsic_options[utsic_donate_url]" size="70" value="<?php echo $options['utsic_donate_url']; ?>" />
    <?php
}

/**
 * Common code for homepage box setting callback functions
 */
function utsic_homepage_box_settings($box_number) {
    $options = get_option('utsic_options');
    $box_string = 'utsic_homepage_box_' . $box_number;
    echo "<input type='text' id='$box_string' name='utsic_options[$box_string]' value='";
    if ( isset( $options[$box_string] ) ) echo $options[$box_string];
    echo "'>";
}

/**
 * Common code for homepage box image upload
 */
function utsic_homepage_box_image_settings($box_number) {
    $options = get_option('utsic_options');
    $box_string = 'utsic_homepage_box_' . $box_number . '_image_url';
    
    ?>
    <input type="text" size="70" id="<?php echo $box_string;?>" class="upload_url" name="utsic_options[<?php echo $box_string;?>]" value="<?php echo $options[$box_string];?>" />
    <input type="button" id="button-<?php echo $box_number;?>" class="st_upload_button" name="button-<?php echo $box_number;?>" value="Upload" />
    <?php
}

/**
 * Callback to disply form section for first homepage box selection.
 */ 
function utsic_homepage_box_1_setting() {
    utsic_homepage_box_settings(1);
}

/**
 * Callback to disply form section for second homepage box selection.
 */ 
function utsic_homepage_box_2_setting() {
    utsic_homepage_box_settings(2);
}

/**
 * Callback to disply form section for third homepage box selection.
 */ 
function utsic_homepage_box_3_setting() {
    utsic_homepage_box_settings(3);
}

/**
 * Callback to display form section for uploading first hompage box image. 
 */
function utsic_homepage_box_1_image_url_setting() {
    utsic_homepage_box_image_settings(1);
}

/**
 * Callback to display form section for uploading second hompage box image. 
 */
function utsic_homepage_box_2_image_url_setting() {
    utsic_homepage_box_image_settings(2);
}

/**
 * Callback to display form section for uploading third hompage box image. 
 */
function utsic_homepage_box_3_image_url_setting() {
    utsic_homepage_box_image_settings(3);
}

/**
 * Callback to display category selection for homepage feed. 
 */
function utsic_homepage_feed_setting() {
    $options = get_option('utsic_options');
    ?>
    <select id="utsic_homepage_feed" name="utsic_options[utsic_homepage_feed]">
        <?php
        $categories = get_categories();
        foreach ($categories as $category) {
            ?>
            <option value="<?php echo $category->cat_ID;?>" <?php if ($options['utsic_homepage_feed'] == $category->cat_ID) echo 'selected = "selected"';?>>
                <?php echo $category->name;?>
            </option>
            <?php
        }
        ?>
    </select>
   <?php 
}

/**
 * Callback to display title input for first homepage box.
 */
function utsic_homepage_box_1_title_setting() {
    $options = get_option('utsic_options');
    ?>
    <input type="text" size="70" id="utsic_box_1_title" name="utsic_options[utsic_homepage_box_1_title]" value="<?php echo $options['utsic_homepage_box_1_title'];?>" />
    <?php
}

/**
 * Callback to display title input for second homepage box.
 */
function utsic_homepage_box_2_title_setting() {
    $options = get_option('utsic_options');
    ?>
    <input type="text" size="70" id="utsic_box_2_title" name="utsic_options[utsic_homepage_box_2_title]" value="<?php echo $options['utsic_homepage_box_2_title'];?>" />
    <?php
}

/**
 * Callback to display title input for third homepage box.
 */
function utsic_homepage_box_3_title_setting() {
    $options = get_option('utsic_options');
    ?>
    <input type="text" size="70" id="utsic_box_3_title" name="utsic_options[utsic_homepage_box_3_title]" value="<?php echo $options['utsic_homepage_box_3_title'];?>" />
    <?php
}

/**
 * Callback to validate theme options. 
 */
function utsic_options_validate($input) {
    $options = get_option('utsic_options');
    
    foreach ($input as $key => $value) {
            $options[$key] = trim($value);
    }
    
    return $options;
}