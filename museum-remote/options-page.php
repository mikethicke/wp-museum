<?php
/**
 * Settings page for Remote Museum
 * See: https://codex.wordpress.org/Creating_Options_Pages
 */

class RMSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Remote Museum Options', 
            'Remote Museum', 
            'manage_options', 
            'rm-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'rm_options' );
        ?>
        <div class="wrap">
            <h1>Remote Museum Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'rm_option_group' );
                do_settings_sections( 'rm-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'rm_option_group', // Option group
            'rm_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'rm_setting_section_id', // ID
            'Remote Museum Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'rm-setting-admin' // Page
        );  

        add_settings_field(
            'base_wp_url', // ID
            'Base URL of Remote Site', // Title 
            array( $this, 'base_wp_url_callback' ), // Callback
            'rm-setting-admin', // Page
            'rm_setting_section_id' // Section           
        );      

        add_settings_field(
            'style', 
            'Style', 
            array( $this, 'style_callback' ), 
            'rm-setting-admin', 
            'rm_setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        
        
        if( isset( $input['base_wp_url'] ) )
            $new_input['base_wp_url'] = sanitize_text_field( $input['base_wp_url']  );
        
        if( isset( $input['style'] ) )
            $new_input['style'] = sanitize_textarea_field( $input['style'] );
            
        return $new_input;
    }

    public function print_section_info()
    {
        /*
        print 'Enter your settings below:';
        */
    }

    public function base_wp_url_callback()
    {
        printf(
            '<input type="text" id="base_wp_url" name="rm_options[base_wp_url]" value="%s" />',
            isset( $this->options['base_wp_url'] ) ? esc_attr( $this->options['base_wp_url']) : ''
        );
    }
    
    public function style_callback()
    {
        printf(
            '<textarea id="style" name="rm_options[style]" rows=20 cols=100>%s</textarea>',
            isset( $this->options['style'] ) ? esc_attr( $this->options['style']) : ''
        );
    }

    
}

if( is_admin() )
    $rm_settings_page = new RMSettingsPage();
 
 
 
?>