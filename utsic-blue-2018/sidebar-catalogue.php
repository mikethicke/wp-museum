<?php
/**
 * Sidebar2
 */
?>

<div id="left-sidebar">
    <?php if ( dynamic_sidebar('catalogue_sidebar') );
    
    $catalogue_category = get_category_by_slug( 'catalogue' );
    $current_category = get_queried_object();
    
    
    ?>
    
</div>