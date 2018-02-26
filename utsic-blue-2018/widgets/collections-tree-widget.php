<?php

/**
 * Adds collections tree widget.
 */
class UTSIC_Collections_Tree_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'utsic_collections_tree_widget', // Base ID
			'UTSIC Collections Tree Widget', // Name
			array( 'description' => __( 'Widget for displaying hierarchy of instrument collections.', 'text_domain' ), ) // Args
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
        global $post;
	    
		echo $before_widget;
        echo '<div id="collections-tree-wrapper">';
        
        $collections_category = get_category_by_slug( 'collections' );
        
        $current_object = get_queried_object();
        
        //Determine which category we are in.
        if ( is_single( $current_object->ID ) ) {
            $categories = get_the_category();
            //Assume we are in the last category.
            $current_category = $categories[count( $categories ) - 1];
        }
        elseif ( is_category( $current_object->ID ) ) {
            $current_category = $current_object;
        }
        else {
            //this shouldn't ever happen.
            echo '</div>';
            return false;
        }
        
        //Collections category
        if ( $current_object->ID == $collections_category->term_id ) {
            echo sprintf( "<h3>%s</h3>", $collections_category->name );
        }
        else {
            echo sprintf( "<h3><a href='%s'>%s</a></h3>", get_category_link( $collections_category->term_id ), $collections_category->name );
        }
        
        //Collections
        
        
        
        
        echo '</div>'; //#collections-tree-wrapper
		echo $after_widget;
	}
        
        /**
        * Recursive helper for printing collections and sub-collections
        * Aug 26 2016: I don't think I use this anywhere and it doesn't do anything? 
        */
        private function recursive_collections( $parent_collection ) {
            
            $children = get_categories( array ( 'parent' => $parent_collection->term_id ) );
            
            foreach ( $children as $child ) {
                    
                
            }
        }

} // class UTSIC_Search_Widget