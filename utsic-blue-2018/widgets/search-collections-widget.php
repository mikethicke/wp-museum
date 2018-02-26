<?php
/**
 * Adds search widget.
 */
class UTSIC_Search_Collections_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_search_collections_widget', // Base ID
			'UTSIC Search Collections Widget', // Name
			array( 'description' => __( 'Widget for searching instrument collections', 'text_domain' ), ) // Args
		);
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
	    
		echo $before_widget;
                
                $current_category = get_queried_object();
                if (is_category ($current_category)) $catID = $current_category->cat_ID;
                else $catID = 0;
		
                ?>
                <form role="search" method="get" id="searchform" action="<?php echo site_url(); ?>" >
                    <div>
                        <label class="screen-reader-text" for="s">Search for:</label>
                        <input type="text" placeholder="search the collection..." name="s" id="s" /><br />
                        <input type="radio" name="within_collection" value="entire_collection" checked="checked"/>Search entire collection<br />
                        <input type="radio" name="within_collection" value="<?php echo $catID ?>" />Search within collection<br />
                        <input type="submit" id="searchsubmit" value="Search" />
                    </div>
                </form>
                <?php
                
                
		echo $after_widget;
	}

} // class UTSIC_Search_Widget