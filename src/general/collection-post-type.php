<?php
/**
 * Creates the collection post type, an instance of CustomPostType.
 *
 * Collections are associated with WordPress categories. They "contain"
 * museum objects in associated categories. They may optionally contain
 * objects of sub-categories and/or child collections.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

$collection_options   = [
	'type'         => WPM_PREFIX . 'collection',
	'label'        => 'Collection',
	'label_plural' => 'Collections',
	'description'  => 'Contains related museum objects.',
	'menu_icon'    => museum_icon(),
	'hierarchical' => true,
	'options'      => [
		'capabilities' => [
			'edit_posts'           => WPM_PREFIX . 'edit_collections',
			'edit_others_posts'    => WPM_PREFIX . 'edit_others_collections',
			'publish_posts'        => WPM_PREFIX . 'publish_collections',
			'read_private_posts'   => WPM_PREFIX . 'read_private_collections',
			'delete_posts'         => WPM_PREFIX . 'delete_collections',
			'edit_published_posts' => WPM_PREFIX . 'edit_published_collections',
		],
		'map_meta_cap' => true,
		'template'     => [
			[ 'core/paragraph', [ 'placeholder' => 'A general description of the collection...' ] ],
			[ 'wp-museum/collection-objects'],
		],
	],
];
$collection_post_type = new CustomPostType( $collection_options );
$collection_post_type->add_support( [ 'thumbnail', 'custom-fields' ] );
$collection_post_type->add_taxonomy( 'category' );
$collection_post_type->add_taxonomy( 'collection_tag' );

/*
 * Custom Fields
 */
$categories       = get_categories( array( 'hide_empty' => false ) );
$category_options = [ -1 => '' ];
foreach ( $categories as $category ) {
	$category_options[ $category->cat_ID ] = $category->name;
}
$collection_post_type->register_post_meta( 'associated_category', 'string', 'Associated Category' );
$collection_post_type->register_post_meta( 'include_sub_collections', 'boolean', 'Include Sub Collections' );
$collection_post_type->register_post_meta( 'include_child_categories', 'boolean', 'Include Child Categories' );
$collection_post_type->register_post_meta( 'single_page', 'boolean', 'Single Page View', [ 'default' => true ] );

/*
 * Register the post type (CustomPostType)
 */
$collection_post_type->register();

/*
 * Collection Tag taxonomy ( separate from post tag taxonomy ).
 */
add_action(
	'init',
	function() {
		register_taxonomy(
			'collection_tag',
			WPM_PREFIX . 'collection',
			[
				'hierarchical' => false,
				'show_in_rest' => true,
			]
		);
	},
	0
);
