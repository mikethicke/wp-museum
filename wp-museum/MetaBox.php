<?php

/**
 * Class for Wordpress metaboxes.
 *
 * @see https://developer.wordpress.org/reference/functions/add_meta_box/
 */
class MetaBox
{
    public $display_callback = null;
    private $save_callback = null;
    public $name;
    public $label;
    public $screen = null;
    public $context = 'advanced';
    public $priority = 'default';
    public $args = null;
    
    /**
     * New MetaBox.
     *
     * @param string $name Name/slug for the metabox (lowercase, no spaces).
     * @param string $label Label for metabox.
     * @param function $display_callback Function that displays the metabox.
     * @param function $save_callback Function that is called when post is saved (can be null)
     */
    function __construct ( $name, $label, $display_callback = null, $save_callback = null ) {
        $this->name = $name;
        $this->label = $label;
        $this->display_callback = $display_callback;
        if ( !is_null ( $save_callback ) ) {
            $this->save_callback = $save_callback;
            add_action( 'save_post', $this->save_callback );
        }
        
    }
    
    function set_save_callback ( $save_callback ) {
        if ( !is_null ( $save_callback ) ) {
            $this->save_callback = $save_callback;
            add_action( 'save_post', $this->save_callback );
        }
    }
    
    /**
     * Add the metabox.
     */
    public function add() {
        add_meta_box( $this->name,
                     $this->label,
                     $this->display_callback,
                     $this->screen,
                     $this->context,
                     $this->priority,
                     $this->args);
    }
}

?>