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
			[ 'wp-museum/collection-objects' ],
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
$collection_post_type->register_post_meta( 'collection_term_id', 'number', 'Associated Collection Term ID' );

/*
 * Register the post type (CustomPostType)
 */
$collection_post_type->register();

/*
 * Collection Tag taxonomy ( separate from post tag taxonomy ).
 */
add_action(
	'init',
	function () {
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

/**
 * Handle automatic creation and association of taxonomy terms when a collection is saved.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @param bool    $update  Whether this is an existing post being updated.
 */
function handle_collection_term_association( $post_id, $post, $update ) {
	// Skip auto-saves and revisions
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Only process our collection post type
	if ( WPM_PREFIX . 'collection' !== $post->post_type ) {
		return;
	}

	// Check if this collection already has an associated term
	$existing_term_id = get_post_meta( $post_id, WPM_PREFIX . 'collection_term_id', true );
	
	if ( $existing_term_id ) {
		// Update the existing term to match the collection
		$term = get_term( $existing_term_id, WPM_PREFIX . 'collection_tax' );
		
		if ( $term && !is_wp_error( $term ) ) {
			// Update the term name and slug if needed
			if ( $term->name !== $post->post_title || $term->slug !== $post->post_name ) {
				wp_update_term(
					$existing_term_id,
					WPM_PREFIX . 'collection_tax',
					[
						'name' => $post->post_title,
						'slug' => $post->post_name,
					]
				);
			}
			
			// Update parent if the post has a parent
			if ( $post->post_parent ) {
				// Get the parent collection's term ID
				$parent_term_id = get_post_meta( $post->post_parent, WPM_PREFIX . 'collection_term_id', true );
				
				if ( $parent_term_id && $term->parent != $parent_term_id ) {
					wp_update_term(
						$existing_term_id,
						WPM_PREFIX . 'collection_tax',
						[
							'parent' => $parent_term_id,
						]
					);
				}
			}
			
			return;
		}
	}
	
	// Create a new term for this collection
	$parent_term_id = 0;
	
	// If the collection has a parent, get the parent's term ID
	if ( $post->post_parent ) {
		$parent_term_id = get_post_meta( $post->post_parent, WPM_PREFIX . 'collection_term_id', true );
	}
	
	$result = wp_insert_term(
		$post->post_title,
		WPM_PREFIX . 'collection_tax',
		[
			'slug' => $post->post_name,
			'parent' => $parent_term_id ? $parent_term_id : 0,
		]
	);
	
	if ( !is_wp_error( $result ) ) {
		$term_id = $result['term_id'];
		
		// Store the term ID in the collection post meta
		update_post_meta( $post_id, WPM_PREFIX . 'collection_term_id', $term_id );
	}
}
add_action( 'save_post', __NAMESPACE__ . '\handle_collection_term_association', 10, 3 );
