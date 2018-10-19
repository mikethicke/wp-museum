<?php

require_once ( 'RemoteCollection.php' );
require_once ( 'WPRestSite.php' );

function collection_display_shortcode ( $atts ) {
    $args =  shortcode_atts ( array (
        'format'            =>  'rows',
        'show_title'        =>  'false',
        'show_description'  =>  'true',
        'show_utsic'        =>  'true',
        'collection'        =>  ''
    ), $atts);
    
    foreach ( $args as &$arg ) {
        if ( $arg == 'false' ) $arg = false;
        if ( $arg == 'true' ) $arg = true;
    }
    
    if ( $args['collection'] == '' )
        return new WP_Error('shortcodes', 'Must call collection_display_shortcode with a collection slug.');
    
    $options = get_option ( 'rm_options' );
    
    $remote_site = new WPRestSite ( $options['base_wp_url'] );
    $remote_collection = new RemoteCollection ( $remote_site );
    $remote_collection->from_slug( $args['collection'] );
    
    if ( $args['format'] == 'rows' )
        $remote_collection->display_rows( $args['show_title'],
                                          $args['show_description'],
                                          $args['show_utsic'] );       
}
add_shortcode ( 'remote_collection', 'collection_display_shortcode');

?>