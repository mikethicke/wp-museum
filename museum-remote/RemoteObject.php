<?php

class RemoteObject
{
    public $remote_site;
    
    public $name;
    public $description;
    public $post_type;
    public $id;
    public $link;
    public $slug;
    public $thumbnail_src;
    public $custom_fields;
    
    private static $custom_field_list_cache;
    
    private $custom_field_list;
    
    public function __construct ( $remote_site=null, $post_type=null, $id=null ) {
        if ( !is_null( $remote_site ) )
            $this->remote_site = $remote_site;
        if ( !is_null( $id ) )
            $this->id = $id;
        if ( !is_null( $post_type ) )
            $this->post_type = $post_type;
    }
    
    public function from_route ( $route, $id ) {
        $obj_data = $this->remote_site->get_object( $route, $id);   
    }
    
    public function from_id ( $id=null, $post_type=null ) {
        if ( !is_null($post_type) ) $this->post_type = $post_type;
        if ( !isset($this->post_type) ) return new WP_Error('RemoteObject', 'from_id requires post_type to be set.');
        if ( !is_null($id) ) $this->id = $id;
        if ( !isset($this->id) ) return new WP_Error('RemoteObject', 'from_id requires id to be set.');
        
        
        $route = '/wp/v2/' . $this->post_type;
        $object_data = $this->remote_site->get_from_id( $route, $this->id );
        
        $this->id = $object_data->id;
        $this->name = $object_data->title->rendered;
        $this->description = $object_data->description;
        $this->link = $object_data->link;
        $this->slug = $object_data->slug;
        $this->thumbnail_src = $object_data->thumbnail_src;
        
        $this->get_custom_field_list();
        
        foreach ( $this->custom_field_list as $cf ) {
            $this->custom_fields[$cf] = $object_data->$cf;   
        }    
    }
    
    public function get_custom_field_list( $flush = false ) {
        if ( $flush ) RemoteObject::$custom_field_list_cache = null;
        if ( isset(RemoteObject::$custom_field_list_cache) && !is_null(RemoteObject::$custom_field_list_cache) ) {
            $this->custom_field_list = RemoteObject::$custom_field_list_cache[$this->post_type];
            return $this->custom_field_list;
        }
        $object_types = $this->remote_site->get_endpoint('wp-museum/v1/object_types');
        foreach ( $object_types as $ot ) {
            $custom_list[$ot] = $this->remote_site->get_endpoint('wp-museum/v1/object_custom/' . $ot );
        }
        $this->custom_field_list = $custom_list[$this->post_type];
        RemoteObject::$custom_field_list_cache = $custom_list;
        return $this->custom_field_list;
    }
    
    public function display_row() {
        ?>
        <div class="wpmr-row">
            <div class="wpmr-row-thumbnail">
                <a href="<?php echo $this->link; ?>" target="_blank"><img src="<?php echo $this->thumbnail_src[0]; ?>"/></a>
            </div>
            <div class="wpmr-row-text-wrapper">
                <div class="wpmr-row-title">
                    <a href="<?php echo $this->link; ?>" target="_blank"><?php echo $this->name; ?></a>
                </div>
                <div class="wpmr-row-description">
                    <a href="<?php echo $this->link; ?>" target="_blank"><?php echo mb_strimwidth($this->description, 0, 1000, '...'); ?></a>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function display_box() {
        
    }
}
?>