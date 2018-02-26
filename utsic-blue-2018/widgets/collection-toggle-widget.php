<?php
/**
 * Adds widget that toggles between description page and collection view page for collections.
 */
class UTSIC_Collection_Toggle_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_collection_toggle_widget', // Base ID
			'UTSIC Collection View Toggle Widget', // Name
			array( 'description' => __( 'Widget for toggling between collection description and view pages.', 'text_domain' ), ) // Args
		);
	}
        
    /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'link_view' ] ) ) {
			$link_view = $instance[ 'link_view' ];
		}
		else {
			$link_view = __( '', 'text_domain' );
		}
		if ( isset( $instance[ 'link_description' ] ) ) {
			$link_description = $instance[ 'link_description' ];
		}
		else {
			$link_description = __( '', 'text_domain' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_view' ); ?>"><?php _e( 'Link text to view collection. Can use %collection%. (eg. "View the %collection% collection."):' ); ?></label><br />
			<input id="<?php echo $this->get_field_id( 'link_view' ); ?>" name="<?php echo $this->get_field_name( 'link_view' ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $link_view ); ?>" />
			<label for="<?php echo $this->get_field_id( 'link_description' ); ?>"><?php _e( 'Link text to view collection description. Can use %collection%. (eg. "Read about the %collection% collection."):' ); ?></label><br />
			<input id="<?php echo $this->get_field_id( 'link_description' ); ?>" name="<?php echo $this->get_field_name( 'link_description' ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $link_description ); ?>" />
        </p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		//currently just trust given input. Don't want to strip HTML.
		$instance['link_view'] = sanitize_text_field($new_instance['link_view']);
		$instance['link_description'] = sanitize_text_field($new_instance['link_description']);

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget ( $args, $instance ) {
		extract ( $args );
		global $post;
		$current_object = get_queried_object();
		$display_mode = get_display_mode();
		
		echo $before_widget;
		
		if ( $post->post_type != 'collection' ) {
			$collections = get_collections();
			$post_categories = wp_get_post_categories ( $post->ID );
			foreach ( $collections as $collection ) {
				$custom = get_post_custom( $collection->ID );
				if ( in_array ($custom['associated_category'][0], $post_categories ) ) {
					$link_text = preg_replace( "/%collection%/", get_the_title( $collection->ID ), $instance['link_description'] );
					$link_url = get_the_permalink( $collection->ID );
					echo sprintf ("<p><a class=\"collections_description_page_link\" href=\"%s\">%s</a></p>", $link_url, $link_text);
				}
			}
		}
		else {
			if ( $display_mode == 'about' ) {
				$link_text = preg_replace( "/%collection%/", get_the_title( $post->ID ), $instance['link_view'] );
				$link_url = get_the_permalink( $post->ID ) . '?mode=def';
			}
			else {
				$link_text = preg_replace( "/%collection%/", get_the_title( $post->ID ), $instance['link_description'] );
				$link_url = get_the_permalink( $post->ID ) . '?mode=about';
			}
			echo sprintf ("<p><a class=\"collections_description_page_link\" href=\"%s\">%s</a></p>", $link_url, $link_text);
		}
		        
		echo $after_widget;
	}
    
} // class UTSIC_Link_To_Collection_Widget