<?php
/**
 * Packages recipes, collections and settings for the website's JavaScript (window.ALC_DATA),
 * and prints the recipe details Google uses on each recipe page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alc_recipe_data( $post ) {
	$post    = get_post( $post );
	$meta    = function ( $k ) use ( $post ) {
		return get_post_meta( $post->ID, $k, true );
	};
	$method  = alc_parse_method( $meta( 'alc_method' ) );
	$terms   = get_the_terms( $post, 'recipe_collection' );
	$cats    = $terms && ! is_wp_error( $terms ) ? wp_list_pluck( $terms, 'slug' ) : array();
	// Keep the collection order from Recipes → Collections, so main collections come first
	static $order = null;
	if ( null === $order ) {
		$order = wp_list_pluck( alc_collections_data(), 'slug' );
	}
	usort( $cats, function ( $x, $y ) use ( $order ) {
		$px = array_search( $x, $order, true );
		$py = array_search( $y, $order, true );
		return ( false === $px ? 999 : $px ) - ( false === $py ? 999 : $py );
	} );
	$img     = get_the_post_thumbnail_url( $post, 'large' );
	if ( ! $img && $terms && ! is_wp_error( $terms ) ) {
		// No recipe photo yet? Borrow the photo from one of its collections.
		foreach ( $terms as $t ) {
			$tid = (int) get_term_meta( $t->term_id, 'alc_image', true );
			if ( $tid ) {
				$img = wp_get_attachment_image_url( $tid, 'large' );
				break;
			}
		}
	}
	$yt      = alc_youtube_id( $meta( 'alc_video' ) );
	$ing     = alc_parse_ingredients( $meta( 'alc_ingredients' ) );
	$summary = $meta( 'alc_summary' );
	return array(
		'slug'        => $post->post_name,
		'url'         => get_permalink( $post ),
		'title'       => get_the_title( $post ),
		'categories'  => array_values( $cats ),
		'isNew'       => '1' === $meta( 'alc_new' ),
		'image'       => $img ? $img : '',
		'imagePos'    => '50% 50%',
		'summary'     => $summary ? $summary : '',
		'prep'        => (int) $meta( 'alc_prep' ),
		'cook'        => (int) $meta( 'alc_cook' ),
		'serves'      => max( 1, (int) ( $meta( 'alc_serves' ) ? $meta( 'alc_serves' ) : 4 ) ),
		'ingredients' => $ing,
		'method'      => $method['steps'],
		'timers'      => $method['timers'],
		'tips'        => alc_parse_tips( $meta( 'alc_tips' ) ),
		'video'       => $yt ? array( 'youtube' => $yt, 'title' => $meta( 'alc_video_title' ) ? $meta( 'alc_video_title' ) : get_the_title( $post ) ) : null,
		'keywords'    => (string) $meta( 'alc_keywords' ),
	);
}

function alc_collections_data() {
	$terms = get_terms( array( 'taxonomy' => 'recipe_collection', 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	usort( $terms, function ( $a, $b ) {
		$oa = get_term_meta( $a->term_id, 'alc_order', true );
		$ob = get_term_meta( $b->term_id, 'alc_order', true );
		$oa = '' === $oa ? 999 : (int) $oa;
		$ob = '' === $ob ? 999 : (int) $ob;
		return $oa === $ob ? strcmp( $a->name, $b->name ) : $oa - $ob;
	} );
	$out = array();
	foreach ( $terms as $t ) {
		$group = get_term_meta( $t->term_id, 'alc_group', true );
		$group = $group ? $group : 'main';
		$img   = (int) get_term_meta( $t->term_id, 'alc_image', true );
		$out[] = array(
			'slug'     => $t->slug,
			'name'     => $t->name,
			'group'    => 'seasonal' === $group ? 'more' : $group,
			'seasonal' => 'seasonal' === $group,
			'image'    => $img ? wp_get_attachment_image_url( $img, 'large' ) : null,
			'blurb'    => $t->description,
		);
	}
	return $out;
}

/** Everything the front end needs, in the same shape as the mock-up's data.js. */
function alc_site_data( $current = null ) {
	$posts   = get_posts( array( 'post_type' => 'recipe', 'post_status' => 'publish', 'numberposts' => 300, 'orderby' => 'date', 'order' => 'DESC' ) );
	$about   = get_page_by_path( 'about' );
	$season  = alc_get_season();
	$data    = array(
		'season'         => $season,
		'youtubeChannel' => (string) ( function_exists( 'alc_mod' ) ? alc_mod( 'alc_youtube' ) : get_theme_mod( 'alc_youtube', '' ) ),
		'categories'     => alc_collections_data(),
		'moods'          => alc_get_moods(),
		'recipes'        => array_map( 'alc_recipe_data', $posts ),
		'links'          => array(
			'home'    => home_url( '/' ),
			'recipes' => get_post_type_archive_link( 'recipe' ),
			'about'   => $about ? get_permalink( $about ) : home_url( '/' ),
		),
		'current'        => $current,
		'aboutImage'     => $about && has_post_thumbnail( $about ) ? get_the_post_thumbnail_url( $about, 'medium' ) : '',
	);
	return apply_filters( 'alc_site_data', $data );
}

/** Recipe details for Google on each recipe page. */
add_action( 'wp_head', function () {
	if ( ! is_singular( 'recipe' ) ) {
		return;
	}
	$r  = alc_recipe_data( get_queried_object() );
	$ld = array(
		'@context'           => 'https://schema.org',
		'@type'              => 'Recipe',
		'name'               => $r['title'],
		'description'        => $r['summary'],
		'author'             => array( '@type' => 'Person', 'name' => get_bloginfo( 'name' ) ),
		'datePublished'      => get_the_date( 'c' ),
		'prepTime'           => 'PT' . $r['prep'] . 'M',
		'cookTime'           => 'PT' . $r['cook'] . 'M',
		'totalTime'          => 'PT' . ( $r['prep'] + $r['cook'] ) . 'M',
		'recipeYield'        => (string) $r['serves'],
		'recipeCategory'     => array_values( array_filter( array_map( function ( $s ) {
			$t = get_term_by( 'slug', $s, 'recipe_collection' );
			return $t ? $t->name : '';
		}, $r['categories'] ) ) ),
		'recipeIngredient'   => array(),
		'recipeInstructions' => array(),
	);
	if ( $r['image'] ) {
		$ld['image'] = array( $r['image'] );
	}
	foreach ( $r['ingredients'] as $g ) {
		foreach ( $g['items'] as $it ) {
			$ld['recipeIngredient'][] = trim( ( null === $it[0] ? '' : $it[0] . ( $it[1] ? ' ' . $it[1] : '' ) . ' ' ) . $it[2] );
		}
	}
	foreach ( $r['method'] as $s ) {
		$ld['recipeInstructions'][] = array( '@type' => 'HowToStep', 'text' => $s );
	}
	if ( $r['video'] ) {
		$ld['video'] = array(
			'@type'        => 'VideoObject',
			'name'         => $r['video']['title'],
			'description'  => $r['summary'] ? $r['summary'] : $r['title'],
			'embedUrl'     => 'https://www.youtube.com/embed/' . $r['video']['youtube'],
			'thumbnailUrl' => $r['image'] ? $r['image'] : 'https://i.ytimg.com/vi/' . $r['video']['youtube'] . '/hqdefault.jpg',
			'uploadDate'   => get_the_date( 'c' ),
		);
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
} );
