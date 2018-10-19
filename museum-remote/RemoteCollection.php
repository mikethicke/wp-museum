<?php

require_once ( 'RemoteObject.php' );

class RemoteCollection
{
    public $remote_site;
    
    public $name;
    public $description;
    public $link;
    public $endpoint_url;
    public $associated_objects;
    
    public function __construct ( $remote_site=null ) {
        if ( !is_null( $remote_site ) ) {
            $this->remote_site = $remote_site;
        }
    }
    
    public function from_json_data ( $collection_data ) {
        $this->name = $collection_data->title->rendered;
        $this->description = $collection_data->content->rendered;
        $associated_object_arr = $collection_data->associated_objects;
        foreach ( $associated_object_arr as $object_pair ) {
            $new_object = new RemoteObject( $this->remote_site, $object_pair[1], $object_pair[0] );
            $this->associated_objects[] = $new_object;
        }
    }
    
    public function from_endpoint ( $endpoint_url=null ) {
        if ( !is_null( $endpoint_url ) ) $this->endpoint_url = $endpoint_url;
        if ( !isset( $this->endpoint_url ) || !isset( $this->remote_site ) )
            return new WP_Error( 'RemoteCollection', 'from_endpoint requires endpoint_url & remote_site to be set.' );
        
        $collection_data = $this->remote_site->get_endpoint ( $endpoint_url );
        if ( !is_wp_error($collection_data) ) {
            $this->from_json_data( $collection_data );
        }
        else return $collection_data;
    }
    
    public function from_slug ( $slug ) {
        $collections_data = $this->remote_site->get_type( '/wp/v2/collection' );
        foreach ( $collections_data as $ce ) {
            if ( $ce->slug == $slug ) {
                $this->from_endpoint ( '/wp/v2/collection/' . $ce->id );
                return true;
            }
        }
        return false;
    }
    
    public function display_rows( $show_title=false, $show_description=true, $show_utsic=true ) {
        ?>
        <div class="wpmr-collection-rows">
            <?php if ( $show_title ) echo ( "<h2>{$this->name}</h2>" ); ?>
            <?php if ( $show_description ) echo ( "<div class='wpmr-collection-description'>{$this->description}</div>" ); ?>
            <div class="clear">&nbsp;</div>
            <?php
                foreach ( $this->associated_objects as $ao ) {
                    $ao->from_id();
                    $ao->display_row();
                    echo '<div class="clear">&nbsp;</div>';
                }
            ?>
        </div>
        <?php
    }
    
    public static function get_remote_collections ( $remote_site ) {
        $collections_data = $remote_site->get_type( '/wp/v2/collection' );
        $remote_collections = array();
        foreach ( $collections_data as $datum ) {
            $new_collection = new RemoteCollection( $remote_site );
            $new_collection->from_json_data( $datum );
            $remote_collections[] = $new_collection;
        }
        return $remote_collections;
    }
    
}
?>