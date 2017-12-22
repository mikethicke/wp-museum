<?php
/**
 * Class for creating Wordpress custom post types.
 */
class CustomPostType
{

    //Basic options for post type
    public $options = [
        'type'          =>  'post',
        'label'         =>  'Post',
        'label_plural'  =>  'Posts',
        'description'   =>  '',
        'public'        =>  true,
        'hierarchical'   =>  true,
        'menu_icon'     =>  'dashicons-format-aside', // See: https://developer.wordpress.org/resource/dashicons/
        'options'       =>  []
    ];
    
    // Meta Box options
    public $meta_name = 'post_meta';
    public $meta_label = 'Post Meta';
    public $meta_box_fields = [];
    
    // Possible options:
    //'title'
    //'editor' (content)
    //'author'
    //'thumbnail' (featured image) (current theme must also support Post Thumbnails)
    //'excerpt'
    //'trackbacks'
    //'custom-fields' (see Custom_Fields, aka meta-data)
    //'comments' (also will see comment count balloon on edit screen)
    //'revisions' (will store revisions)
    //'page-attributes' (template and menu order) (hierarchical must be true)
    //'post-formats' (see Post_Formats)
    // See: https://codex.wordpress.org/Function_Reference/post_type_supports
    public $supports = ['title', 'editor', 'author'];
    
    public $taxonomies = [];
    
    public $custom_metas = [];
    
    function __construct($options) {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
        
        $this->meta_name = $this->options['type'] . '_meta';
        $this->meta_label = $this->options['label'] . ' Options';
    }
    
    public function add_support ( $supports ) {
        if ( gettype($supports) == 'string' ) {
            $this->supports[] = $supports;
        }
        elseif ( gettype($supports) == 'array' ) {
            $this->supports = array_merge($this->supports, $supports);
        }
    }
    
    public function add_taxonomy ( $taxonomy ) {
        if ( gettype($taxonomy) == 'string' ) {
            $this->taxonomies[] = $taxonomy;
        }
        elseif ( gettype($taxonomy) == 'array' ) {
            $this->taxonomies = array_merge($this->taxonomies, $taxonomy);
        }
    }
    
    public function add_custom_meta ( $display_callback, $save_callback ) {
        $this->custom_metas[] = ['display' => $display_callback, 'save' => $save_callback];
    }
    
    public function add_meta_field ( $field_name, $field_label, $field_type='text', $options=[] ) {
        $this->meta_box_fields[$field_name] = [
            'label'     =>  $field_label,
            'type'      =>  $field_type,
            'options'   =>  $options
        ];
    }
    
    public function display_meta_boxes ( WP_POST $post ) {
        if ( count($this->meta_box_fields)  > 0 ) $this->main_meta_box( $post );
        foreach ( $this->custom_metas as $cm ) {
            $cm['display']( $post );
        }
    }
    
    public function main_meta_box (WP_POST $post)
    {
        add_meta_box($this->meta_name, $this->meta_label, function() use ($post) {
            
            wp_nonce_field($this->meta_name + '_nonce', $this->meta_name + '_nonce');
            ?>
            <table class='form-table'>
            <?php
                foreach($this->meta_box_fields as $field_name => $field_array) {
                    $field_value = trim(get_post_meta($post->ID, $field_name, true));
                    $field_label = $field_array['label'];
                    $field_type = $field_array['type'];
                    $field_options = $field_array['options'];
                    
                    if ( isset($field_options['style']) ) {
                        $style = $field_options['style'];
                    }
                    elseif ( isset($field_options['width'])) {
                        $style = "width: {$field_options['width']};";
                    }
                    else {
                        $style = 'width: 100%;';
                        if ( $field_type == 'textarea' ) $style .= " height: 5em;";
                    }
                    
                    ?>
                    <tr>
                        <th> <label for="<?php echo $field_name; ?>"><?php echo $field_label; ?></label></th>
                        <td>
                        <?php
                        switch ($field_type) {
                            case 'text':
                                ?>
                                <input id="<?php echo $field_name; ?>"
                                    name="<?php echo $field_name; ?>"
                                    type="text"
                                    value="<?php echo esc_attr($field_value); ?>"
                                    style="<?php echo $style;?>"
                                />
                                <?php
                                break;
                            case 'textarea':
                                ?>
                                <textarea id="<?php echo $field_name; ?>"
                                          name="<?php echo $field_name; ?>"
                                          style="<?php echo $style;?>"
                                ><?php echo esc_attr($field_value); ?></textarea>
                                <?php
                                break;
                            case 'select':
                                ?>
                                <select id="<?php echo $field_name; ?>"
                                        name="<?php echo $field_name; ?>"
                                        style="<?php echo $style;?>"
                                        <?php if ( isset($field_options->multiple) && $field_options->multiple == true ) echo " multiple "; ?>
                                        <?php if ( isset($field_options->size) ) echo " size='{$field_options->size}' "; ?>
                                >
                                    <?php
                                        foreach ( $field_options['options'] as $option_value => $option_label ) {
                                            echo "<option value='{$option_value}' ";
                                            if ( $option_value == $field_value) echo ' selected ';
                                            echo ">{$option_label}</option>";
                                        }
                                    ?>
                                </select>
                                <?php
                                break;
                            case 'radio':
                                foreach ( $field_options['options'] as $option_value => $option_label ) {
                                    echo "<input type='radio' name='{$field_name}' value='{$option_value}' ";
                                    if ( $option_value == $field_value) echo ' checked ';
                                    echo ">{$option_label}<br />";
                                }
                                break;
                            case 'checkbox':
                                foreach ( $field_options['options'] as $option_value => $option_label ) {
                                    echo "<input type='checkbox' name='{$field_name}' value='{$option_value}' ";
                                    if ( $option_value == $field_value) echo ' checked ';
                                    echo ">{$option_label}<br />";
                                }
                                break;
                            
                        }
                        ?>
                        </td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            <?php
        } );          
    }
    
    
    /**
     * Creates array of labels based on this->label and this->label_plural
     * See: https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
     *
     * @return [string]
     */
    private function labels()
    {
        $p_lower = strtolower($this->options['label_plural']);
        $s_lower = strtolower($this->options['label']);

        return [
            'name' => $this->options['label_plural'],
            'singular_name' => $this->options['label'],
            'add_new_item' => "New {$this->options['label']}",
            'edit_item' => "Edit {$this->options['label']}",
            'view_item' => "View {$this->options['label']}",
            'view_items' => "View {$this->options['label_plural']}",
            'search_items' => "Search {$this->options['label_plural']}",
            'not_found' => "No $p_lower found",
            'not_found_in_trash' => "No $p_lower found in trash",
            'parent_item_colon' => "Parent {$this->options['label']}",
            'all_items' => "All {$this->options['label_plural']}",
            'archives' => "{$this->options['label']} Archives",
            'attributes' => "{$this->options['label']} Attributes",
            'insert_into_item' => "Insert into $s_lower",
            'uploaded_to_this_item' => "Uploaded to this $s_lower"
        ];
    }
    
    /**
     * Register the post type.
     * See: https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
     */
    public function register()
    {
        $arguments = [
                'public' => $this->options['public'],
                'description' => $this->options['description'],
                'labels'  => $this->labels(),
                'menu_icon' => $this->options['menu_icon'],
                'supports' => $this->supports,
                'taxonomies' => $this->taxonomies,
                'hierarchical' => $this->options['hierarchical'],
                'show_in_rest' => true,
                'register_meta_box_cb' => array($this, 'display_meta_boxes')
            ];
        if ( count($this->meta_box_fields)  > 0 ) {
            add_action('save_post', function($post_id) {
            $post = get_post($post_id);
            $is_revision = wp_is_post_revision($post_id);
            
            if ( $post->post_type != $this->options['type'] || $is_revision )
                return;
            /*if ( ! (check_admin_referer($this->meta_name + '_nonce', $this->meta_name + '_nonce') ) )
                return;*/
            
            foreach ($this->meta_box_fields as $field_name => $field_label) {
                if ( isset($_POST[$field_name]) ) {
                    $field_value = trim($_POST[$field_name]);
                    if ( isset($field_value) && $field_value != '' )
                        update_post_meta($post_id, $field_name, $field_value);
                    else
                        delete_post_meta($post_id, $field_name);
                    }
                }
                
            } );     
            
        }
        foreach ( $this->custom_metas as $cm ) {
            add_action('save_post', $cm['save']);
        }
        add_action( 'init', function() use ($arguments) {
            $arguments = $arguments + $this->options['options'];
            register_post_type( $this->options['type'], $arguments);
        });
    }
    
    
    
}