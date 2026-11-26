<?php
/**
 * Amy Louise Crisp theme.
 * Words and photos are edited in Appearance → Customise; recipes come from the "Amy's Recipes" plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALC_THEME_VERSION', '1.0.0' );

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/ads.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 480, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/editor.css' ) );
	register_nav_menus( array(
		'primary' => 'Main menu (top of every page)',
		'footer'  => 'Footer “More” links',
	) );
} );

/** A theme setting with its default. */
function alc_mod( $key ) {
	$d = alc_defaults();
	return get_theme_mod( $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

/** "Family recipes you’ll *actually* make" → highlights the starred word. */
function alc_highlight( $text ) {
	return preg_replace( '/\*([^*]+)\*/', '<em>$1</em>', esc_html( $text ) );
}

function alc_theme_img( $file ) {
	return get_template_directory_uri() . '/assets/img/' . $file;
}

/** Link helpers that work whether or not the recipes plugin is active. */
function alc_recipes_url( $hash = '' ) {
	$url = post_type_exists( 'recipe' ) ? get_post_type_archive_link( 'recipe' ) : home_url( '/' );
	return $url . ( $hash ? '#' . $hash : '' );
}
function alc_about_url() {
	$p = get_page_by_path( 'about' );
	return $p ? get_permalink( $p ) : home_url( '/' );
}

/** Fonts, styles and scripts. */
add_action( 'wp_enqueue_scripts', function () {
	$v   = ALC_THEME_VERSION;
	$uri = get_template_directory_uri() . '/assets/';
	wp_enqueue_style( 'alc-fonts', 'https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600&family=Lato:wght@400;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap', array(), null );
	wp_enqueue_style( 'alc-styles', $uri . 'styles.css', array( 'alc-fonts' ), $v );
	wp_enqueue_style( 'alc-theme', $uri . 'theme.css', array( 'alc-styles' ), $v );

	if ( ! function_exists( 'alc_site_data' ) ) {
		return; // The recipes plugin isn't active yet.
	}
	$current = is_singular( 'recipe' ) ? get_queried_object()->post_name : null;
	wp_register_script( 'alc-site', $uri . 'site.js', array(), $v, true );
	wp_add_inline_script( 'alc-site', 'window.ALC_DATA = ' . wp_json_encode( alc_site_data( $current ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';', 'before' );
	wp_enqueue_script( 'alc-site' );
	if ( is_front_page() || is_singular( 'recipe' ) ) {
		wp_enqueue_script( 'alc-recipe-card', $uri . 'recipe-card.js', array( 'alc-site' ), $v, true );
	}
	if ( is_front_page() ) {
		wp_enqueue_script( 'alc-home', $uri . 'home.js', array( 'alc-recipe-card' ), $v, true );
	}
	if ( is_post_type_archive( 'recipe' ) ) {
		wp_enqueue_script( 'alc-recipes', $uri . 'recipes-page.js', array( 'alc-site' ), $v, true );
	}
	if ( is_singular( 'recipe' ) ) {
		wp_enqueue_script( 'alc-recipe-page', $uri . 'recipe-page.js', array( 'alc-recipe-card' ), $v, true );
	}
} );

/** The Seasons font, once Amy uploads the licensed file (Customise → Site Identity). */
add_action( 'wp_head', function () {
	$font = get_theme_mod( 'alc_font_file' );
	echo '<meta name="theme-color" content="#f8f5ef">' . "\n";
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" href="%s" sizes="32x32">' . "\n" . '<link rel="icon" href="%s" sizes="512x512">' . "\n" . '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( alc_theme_img( 'logo/favicon-32.png' ) ), esc_url( alc_theme_img( 'logo/favicon.png' ) ), esc_url( alc_theme_img( 'logo/apple-touch-icon.png' ) ) );
	}
	if ( $font ) {
		$ext    = strtolower( pathinfo( wp_parse_url( $font, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		$format = array( 'woff2' => 'woff2', 'woff' => 'woff', 'otf' => 'opentype', 'ttf' => 'truetype' );
		printf( "<style>@font-face{font-family:'The Seasons';src:url('%s') format('%s');font-display:swap}</style>\n", esc_url( $font ), esc_attr( $format[ $ext ] ?? 'woff2' ) );
	}
}, 5 );

/** Allow font files in the Media Library (for The Seasons). */
add_filter( 'upload_mimes', function ( $m ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		$m['woff2'] = 'font/woff2';
		$m['woff']  = 'font/woff';
		$m['otf']   = 'font/otf';
		$m['ttf']   = 'font/ttf';
	}
	return $m;
} );
add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename ) {
	$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	$map = array( 'woff2' => 'font/woff2', 'woff' => 'font/woff', 'otf' => 'font/otf', 'ttf' => 'font/ttf' );
	if ( isset( $map[ $ext ] ) && current_user_can( 'edit_theme_options' ) ) {
		return array( 'ext' => $ext, 'type' => $map[ $ext ], 'proper_filename' => $filename );
	}
	return $data;
}, 10, 3 );

/** Which page we're on (used by the menus and the mobile tab bar). */
function alc_page_key() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_post_type_archive( 'recipe' ) || is_singular( 'recipe' ) ) {
		return 'recipes';
	}
	if ( is_page( 'about' ) ) {
		return 'about';
	}
	return 'page';
}

/** Fallback menu until one is set up in Appearance → Menus. */
function alc_menu_fallback() {
	echo '<ul>';
	printf( '<li><a href="%s">Home</a></li>', esc_url( home_url( '/' ) ) );
	printf( '<li><a href="%s">Recipes</a></li>', esc_url( alc_recipes_url() ) );
	printf( '<li><a href="%s">About Me</a></li>', esc_url( alc_about_url() ) );
	echo '</ul>';
}
function alc_footer_fallback() {
	echo '<ul>';
	printf( '<li><a href="%s">About Me</a></li>', esc_url( alc_about_url() ) );
	printf( '<li><a href="%s">Newsletter</a></li>', esc_url( home_url( '/#newsletter' ) ) );
	echo '</ul>';
}

/** Gentle reminder if the recipes plugin isn't switched on. */
add_action( 'admin_notices', function () {
	if ( ! function_exists( 'alc_site_data' ) && current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-warning"><p><strong>One more step:</strong> activate the <strong>Amy’s Recipes</strong> plugin in <a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">Plugins</a> so your recipes, Cook Mode and collections appear.</p></div>';
	}
} );

