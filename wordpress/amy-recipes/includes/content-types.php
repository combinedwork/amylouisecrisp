<?php
/**
 * The "Recipes" section and "Collections" (recipe categories) in the dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'alc_register_content_types' );
function alc_register_content_types() {
	register_post_type(
		'recipe',
		array(
			'labels'        => array(
				'name'                  => 'Recipes',
				'singular_name'         => 'Recipe',
				'menu_name'             => 'Recipes',
				'add_new'               => 'Add New Recipe',
				'add_new_item'          => 'Add New Recipe',
				'edit_item'             => 'Edit Recipe',
				'new_item'              => 'New Recipe',
				'view_item'             => 'View Recipe',
				'view_items'            => 'View Recipes',
				'search_items'          => 'Search Recipes',
				'not_found'             => 'No recipes yet – click “Add New Recipe” to add your first one.',
				'all_items'             => 'All Recipes',
				'featured_image'        => 'Recipe photo',
				'set_featured_image'    => 'Choose recipe photo',
				'remove_featured_image' => 'Remove recipe photo',
				'use_featured_image'    => 'Use as recipe photo',
			),
			'public'        => true,
			'has_archive'   => 'recipes',
			'rewrite'       => array( 'slug' => 'recipe', 'with_front' => false ),
			'menu_icon'     => 'dashicons-carrot',
			'menu_position' => 4,
			'supports'      => array( 'title', 'thumbnail', 'revisions' ),
			'show_in_rest'  => true,
		)
	);

	register_taxonomy(
		'recipe_collection',
		'recipe',
		array(
			'labels'            => array(
				'name'          => 'Collections',
				'singular_name' => 'Collection',
				'menu_name'     => 'Collections',
				'all_items'     => 'All Collections',
				'edit_item'     => 'Edit Collection',
				'add_new_item'  => 'Add New Collection',
				'search_items'  => 'Search Collections',
				'not_found'     => 'No collections yet.',
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'meta_box_cb'       => 'post_categories_meta_box',
			'rewrite'           => array( 'slug' => 'collection', 'with_front' => false ),
		)
	);
}

/** Collection pages open the Recipes page already filtered to that collection. */
add_action( 'template_redirect', 'alc_collection_redirect' );
function alc_collection_redirect() {
	if ( is_tax( 'recipe_collection' ) ) {
		$term = get_queried_object();
		wp_safe_redirect( get_post_type_archive_link( 'recipe' ) . '#' . $term->slug, 302 );
		exit;
	}
}

/** Show every recipe on the Recipes page (the page filters them itself). */
add_action( 'pre_get_posts', 'alc_recipes_archive_query' );
function alc_recipes_archive_query( $q ) {
	if ( ! is_admin() && $q->is_main_query() && $q->is_post_type_archive( 'recipe' ) ) {
		$q->set( 'posts_per_page', -1 );
	}
}

/** Friendlier "Enter title here". */
add_filter( 'enter_title_here', function ( $text, $post ) {
	return 'recipe' === $post->post_type ? 'Recipe name, e.g. Creamy Chicken Pesto Pasta' : $text;
}, 10, 2 );

/** Recipe list columns: photo, collections, time. */
add_filter( 'manage_recipe_posts_columns', function ( $cols ) {
	$new = array();
	foreach ( $cols as $k => $v ) {
		if ( 'title' === $k ) {
			$new['alc_photo'] = 'Photo';
		}
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['alc_time'] = 'Time';
		}
	}
	return $new;
} );
add_action( 'manage_recipe_posts_custom_column', function ( $col, $id ) {
	if ( 'alc_photo' === $col ) {
		echo has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, array( 60, 60 ), array( 'style' => 'border-radius:6px;object-fit:cover;width:60px;height:60px' ) ) : '<span style="color:#b32d2e">No photo yet</span>';
	}
	if ( 'alc_time' === $col ) {
		$t = (int) get_post_meta( $id, 'alc_prep', true ) + (int) get_post_meta( $id, 'alc_cook', true );
		echo $t ? esc_html( $t . ' mins' ) : '–';
	}
}, 10, 2 );
