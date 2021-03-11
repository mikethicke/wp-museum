<?php
/**
 * A widget that shows a hierarchical list of collections with the current
 * collection indicated.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Singleton class for configuring and displaying widget.
 */
class CollectionTreeWidget extends \WP_Widget {

	/**
	 * Basic setup of widget.
	 */
	public function __construct() {
		parent::__construct(
			WPM_PREFIX . 'collection_tree_widget',
			'Collection Tree Widget',
			[ 'description' => __( 'Widget for displaying hierarchical list of collections with current collection indicated') ]
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
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
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
	}
}

/**
 * Registers the widget.
 */
function register_collection_tree_widget() {
	register_widget( __NAMESPACE__ . '\Collection_Tree_Widget' );
}