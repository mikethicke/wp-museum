<?php get_header();

global $post; ?>

<div id="content">
    
<?php

$row_counter = 0; //toggles between 0 and 1 to allow for alternating row styles

while ( have_posts() ) {
    the_post();
    $custom = get_post_custom ( $post->ID );
    
    //retrieve instrument fields from database
    global $wpdb;
    $fields_table_name = $wpdb->prefix . "instrument_fields";
    $fields = $wpdb->get_results( "SELECT id, name, slug, help_text, type FROM $fields_table_name" );
    
    //toggle row styles
    if ( $row_counter == 0 ) { 
        $row_counter = 1;
        $row_style = "instrument-archive-row even";
    }
    else {
        $row_counter = 0;
        $row_style = "instrument-archive-row odd";
    }
    ?>
    <div class = "<?php echo $row_style; ?>">
        <div class="instrument-archive-title">
            <?php echo the_title(); ?> 
        </div>
        <div class="instrument-archive-info">
            <span class="instrument-archive-info-category"><?php echo the_category(); ?></span> &middot; 
            <span class="instrument-archive-info-an"><?php echo $custom['accession-number'][0]; ?></span>
        </div>
        <div class="instrument-archive-excerpt">
            <?php echo $custom['description']; ?>
        </div>
    
    </div> <!-- .$row_style -->     
    
    <?php
} //while ( have_posts() ) ?>

</div> <!-- #content --> 





<?php get_footer(); ?>