<?php

/**
 * Adds exhibit tree widget.
 */
class UTSIC_Exhibit_Tree_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_exhibit_tree_widget', // Base ID
			'UTSIC Exhibit Tree Widget', // Name
			array( 'description' => __( 'Widget for displaying hierarcy of themes and posts within an exhibit.', 'text_domain' ), ) // Args
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
		if ( isset( $instance[ 'html_follow' ] ) ) {
			$html_follow = $instance[ 'html_follow' ];
		}
		else {
			$html_follow = __( '', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'html_follow' ); ?>"><?php _e( 'Following HTML:' ); ?></label> 
		<textarea rows=15 cols=30 id="<?php echo $this->get_field_id( 'html_follow' ); ?>" name="<?php echo $this->get_field_name( 'html_follow' ); ?>"><?php echo esc_attr( $html_follow ); ?></textarea>
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
                $instance['html_follow'] = $new_instance['html_follow'];

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
	public function widget( $args, $instance ) {
		extract( $args );
                global $post;
	    
		echo $before_widget;
                echo '<div id="exhibit-tree-wrapper">';
                
                //figure out which exhibit we are in.
                $current_object = get_queried_object();
                if ( is_single( $current_object->ID ) ) {
                    $categories = get_the_category();
                    //Hopefully there is only one category. Assume we are in the first. To Do: have a more sophisticated detection system.
                    $current_category = $categories[0];
                }
                elseif ( is_category( $current_object->ID ) ) {
                    $current_category = $current_object;
                }
                else {
                    //this shouldn't ever happen.
                    echo '</div>';
                    return false;
                }
                $exhibits_category = get_category_by_slug( 'exhibits' );
                $exhibits_children = get_categories( array ( 'parent' => $exhibits_category->term_id, 'hide_empty' => 0 ) );
                $is_parent_exhibit = 0;
                foreach ( $exhibits_children as $exhibit ) {
                    if ( cat_is_ancestor_of( $exhibit, $current_category ) ) $the_exhibit = $exhibit;
                    else if ( $exhibit->term_id == $current_category->term_id ) {
                        $the_exhibit = $exhibit;
                        $is_parent_exhibit = 1;
                    }
                }
                if ( ! isset( $the_exhibit ) ) {
                    echo '</div>';
                    return false;
                }
                
                echo sprintf ("<h3><a href='%s'>%s</a></h3>", get_category_link( $the_exhibit->term_id ), $the_exhibit->name);
                
                //list each theme, and list the posts within the current theme
                $the_exhibit_themes = get_categories( array ( 'parent' => $the_exhibit->term_id, 'hide_empty' => 0 ) );
                echo '<ul class="sidebar-exhibit-theme-list">';
                foreach ( $the_exhibit_themes as $the_theme ) {
                    echo sprintf( "<li><a href='%s'>%s</a>", get_category_link( $the_theme->term_id ), $the_theme->name );
                    if ( $the_theme->term_id == $current_category->term_id ) {
                        query_posts ("category_name={$current_category->slug}" );
                        echo '<ul class="sidebar-theme-post-list">';
                        while ( have_posts() ) {
                            the_post();
                            echo '<li>';
                            if ( isset( $current_object->post_type ) && $current_object->post_type == 'post' && $current_object->ID == $post->ID ) {
                                the_title();
                            }
                            else {
                                echo sprintf( "<a href='%s'>%s</a>", get_permalink(), get_the_title() );
                            }
                            echo '</li>';
                        }
                        echo '</ul>';
                        wp_reset_query();
                    }
                    echo '</li>';
                }
                
                
                echo '</ul>';
                
                echo $instance['html_follow'];
                
                
                echo '</div>'; //#exhibit-tree-wrapper
		echo $after_widget;
	}

}