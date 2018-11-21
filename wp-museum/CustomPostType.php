<?php
/**
 * Class for creating Wordpress custom post types. 
 */

require_once ( 'MetaBox.php' );
 
class CustomPostType
{

    /* Basic options for post type
     * See: https://developer.wordpress.org/reference/functions/register_post_type/
     */
    public $options = [
        'type'          =>  'post',
        'label'         =>  'Post',
        'label_plural'  =>  'Posts',
        'description'   =>  '',
        'public'        =>  true,
        'hierarchical'   =>  true,
        'menu_icon'     =>  'dashicons-format-aside', // See: https://developer.wordpress.org/resource/dashicons/
        'options'       =>  [],
        'rewrite'       => true
    ];
    
    /* Meta Box options */
    public $meta_name = 'post_meta';
    public $meta_label = 'Post Meta';
    public $meta_box_fields = [];
    
    /* Possible options:
     *'title'
     *'editor' (content)
     *'author'
     *'thumbnail' (featured image) (current theme must also support Post Thumbnails)
     *'excerpt'
     *'trackbacks'
     *'custom-fields' (see Custom_Fields, aka meta-data)
     *'comments' (also will see comment count balloon on edit screen)
     *'revisions' (will store revisions)
     *'page-attributes' (template and menu order) (hierarchical must be true)
     *'post-formats' (see Post_Formats)
     * See: https:*codex.wordpress.org/Function_Reference/post_type_support'
     */
    public $supports = ['title', 'editor', 'author'];
    
    public $taxonomies = [];
    
    public $custom_metas = [];
    
    public $custom_fields = [];
    
    public $include_in_loop = true;
    
    /**
     * Constructor.
     *
     * @param array $options Array of key-value pairs of options.
     * @see https://developer.wordpress.org/reference/functions/register_post_type/
     */
    function __construct($options) {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
        
        $this->meta_name = $this->options['type'] . '_meta';
        $this->meta_label = $this->options['label'] . ' Options';
    }
    
    /**
     * Add support to post type.
     *
     * @param array|string $supports Array of strings to add multiple support options, or string to add one support option.
     * @see https://codex.wordpress.org/Function_Reference/post_type_supports
     */
    public function add_support ( $supports ) {
        if ( gettype($supports) == 'string' ) {
            $this->supports[] = $supports;
        }
        elseif ( gettype($supports) == 'array' ) {
            $this->supports = array_merge($this->supports, $supports);
        }
    }
    
    /**
     * Add taxonomy to post type.
     *
     * @param array|string $taxonomy Array of strings to add multiple taxonomies, or string to add one taxonomy.
     * @see https://developer.wordpress.org/reference/functions/register_post_type/
     */
    public function add_taxonomy ( $taxonomy ) {
        if ( gettype($taxonomy) == 'string' ) {
            $this->taxonomies[] = $taxonomy;
        }
        elseif ( gettype($taxonomy) == 'array' ) {
            $this->taxonomies = array_merge($this->taxonomies, $taxonomy);
        }
    }
    
    /**
     * Add custom metabox to post type that will appear on edit screen.
     *
     * @param function $display_callback Callback function to display metabox.
     * @param function $save_callback Callback function to process metabox form and save data.
     */
    public function add_custom_meta ( MetaBox $new_meta ) {
        $this->custom_metas[] = $new_meta;
    }
    
    /**
     * Add a field to main metabox.
     *
     * @param string $field_name Name of the field (will be name of option in templates, etc.).
     * @param string $field_label Label of field in metabox form.
     * @param string $field_type Type of field (text|textarea|select|radio|checkbox)
     */
    public function add_meta_field ( $field_name, $field_label, $field_type='text', $options=[] ) {
        $this->meta_box_fields[$field_name] = [
            'label'     =>  $field_label,
            'type'      =>  $field_type,
            'options'   =>  $options
        ];
    
        register_post_meta ( $this->options['type'],
                            $field_name,
                            [
                                'type' => 'string',
                                'description' => $field_name,
                                'single' => true,
                                'show_in_rest' => true
                             ] ); 
    }
    
    /**
     * Display all metaboxes in edit page of post.
     *
     * @param WP_POST $post The post.
     */
    public function display_meta_boxes ( WP_POST $post ) {
        if ( count($this->meta_box_fields)  > 0 ) $this->main_meta_box( $post );
        foreach ( $this->custom_metas as $cm ) {
            $cm->add();
        }
    }
    
    /**
     * Display main metabox in edit page of post.
     *
     * @param WP_POST $post The post.
     */
    public function main_meta_box ( WP_POST $post )
    {
        add_meta_box($this->meta_name, $this->meta_label, function() use ($post) {
            
            wp_nonce_field($this->meta_name . '_nonce', $this->meta_name . '_nonce');
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
                                if ( isset( $field_options['options'] ) ) {
                                    foreach ( $field_options['options'] as $option_value => $option_label ) {
                                        echo "<input type='checkbox' name='{$field_name}' value='{$option_value}' ";
                                        if ( $option_value == $field_value) echo ' checked ';
                                        echo ">{$option_label}<br />";
                                    }
                                }
                                else {
                                    echo "<input type='checkbox' name='$field_name' value='1' ";
                                    if ( $field_value == '1' ) echo ' checked ';
                                    echo ">";
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
     * Callback to save custom post fields.
     */
    public function save_main_metabox ( $post_id ) {
        $post = get_post($post_id);
        $is_revision = wp_is_post_revision($post_id);
        
        if ( $post->post_type != $this->options['type'] || $is_revision )
            return;
        /*if ( ! (check_admin_referer($this->meta_name + '_nonce', $this->meta_name + '_nonce') ) )
            return;*/
        
        foreach ( $this->meta_box_fields as $field_name => $field_data ) {
            if ( isset($_POST[$field_name]) ) {
                $field_value = trim($_POST[$field_name]);
                if ( isset($field_value) && $field_value != '' ) update_post_meta($post_id, $field_name, $field_value);
                else delete_post_meta($post_id, $field_name);
            }
            elseif ( $field_data['type'] == 'checkbox' ) {
                update_post_meta( $post_id, $field_name, '0' );
            }   
        }   
    }
    
    /**
     * Adds post type to query. Called by add_to_search and add_to_loop.
     */
    private function add_to_query( $query ) {
        if ( !is_admin() ) {
            $post_types = [];
            if ( !is_null( $query->get( 'post_type' ) ) ) $post_types = $query->get( 'post_type' );
            if ( !is_array($post_types) ) $post_types = array( $post_types );
            if ( empty( $post_types ) ) $post_types = ['post', 'page'];
            if ( !in_array($this->options['type'], $post_types ) ) $post_types[] = $this->options['type'];
            $query->set( 'post_type', $post_types );
            return $query;
        }
    }
    
    /**
     * Adds this post type to searches.
     *
     * @see https://webdevstudios.com/2015/09/01/search-everything-within-custom-post-types/
     */
    public function add_to_search( $query ) {
        if ( is_search() && $query->is_main_query() && !empty( $search_string ) ) {
            $this->add_to_query( $query );
        }
    }
    
    /**
     * Add this post type to list of post types retrieved by the Wordpress loop.
     * A hook to call this during pre_get_posts is added on registration if
     * include_in_loop is true.
     *
     * @see https://stackoverflow.com/questions/29669534/include-custom-post-type-in-wordpress-loop
     */
    public function add_to_loop ( $query ) {
        if ( $query->is_main_query() ) {
            $this->add_to_query( $query );
        }
    }
    
    /**
     * Creates array of labels based on this->label and this->label_plural
     * 
     * @see https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
     *
     * @return [string] The labels.
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
     * 
     * @see https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
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
                'rewrite'   => $this->options['rewrite'],
                'show_in_rest' => true,
                'register_meta_box_cb' => array($this, 'display_meta_boxes')
            ];
        if ( count($this->meta_box_fields)  > 0 ) add_action('save_post', array( $this, 'save_main_metabox' ) );     
            
        
        add_action( 'init', function() use ($arguments) {
            $arguments = $arguments + $this->options['options'];
            register_post_type( $this->options['type'], $arguments);
            if ( $arguments['hierarchical'] ) add_post_type_support( $this->options['type'], 'page-attributes' );
        });
        
        add_action( 'pre_get_posts', array( $this, 'add_to_search' )  );
        if ( $this->include_in_loop ) add_action( 'pre_get_posts', array( $this, 'add_to_loop' ) );
    }    
}


/**
 * Callback to search post meta fields when searching posts.
 */
$custom_search = function ( $query ) {
      global $wpdb;
      if ( $query->is_main_query() && is_search() ) {
        $search_string = get_search_query();
        $search_string = '%' . $wpdb->esc_like( $search_string ) . '%';
        $post_ids_meta = $wpdb->get_col( $wpdb->prepare( "
            SELECT DISTINCT post_id FROM {$wpdb->postmeta}
            WHERE meta_value LIKE '%s'
            ", $search_string ) );
        $post_ids_post = $wpdb->get_col( $wpdb->prepare( "
            SELECT DISTINCT ID FROM {$wpdb->posts}
            WHERE post_title LIKE '%s'
            OR post_content LIKE '%s'
            ", $search_string, $search_string ) );
        $post_ids = array_merge( $post_ids_meta, $post_ids_post );
        $query->set( 'post__in', $post_ids );
        return $query;
      }
};
add_action( 'pre_get_posts', $custom_search );