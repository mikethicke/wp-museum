<?php
/**
 * Sidebar
 */
?>

<div id="left-sidebar">
    <?php if ( dynamic_sidebar('collections_sidebar') );
    
    $collections_category = get_category_by_slug( 'collections' );
    $current_category = get_queried_object();
    
    
    ?>
    
</div>