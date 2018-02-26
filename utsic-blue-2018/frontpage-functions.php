<?php
//Displays content buckets on front page. $box_number is used by style.css to position boxes.
function fill_feature_box($box_number) {
    $options = get_option('utsic_options');
    $box_string = 'utsic_homepage_box_' . $box_number;
    $title_string = $box_string . '_title';
    $image_url_string = $box_string .'_image_url';
    
    $box_title = $options[$title_string];
    
    $image_src = '<img src="' . $options[$image_url_string] . '" />';
    
    $object_url = $options[$box_string];
    
    ?>
    <div class="homepage-box-content">
        <div class="homepage-box-image"><a href="<?php echo $object_url; ?>"><?php echo $image_src; ?></a></div>
        <div class="homepage-box-title"><a href="<?php echo $object_url; ?>"><?php echo $box_title; ?></a></div>
    </div>
    <?php
}