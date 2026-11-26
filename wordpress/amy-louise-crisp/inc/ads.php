<?php
/**
 * Advertising, ready for later. Nothing shows until Amy turns it on in
 * Appearance → Customise → 💷 Advertising and pastes the code from her ad network.
 *
 * - Code for the <head>: the script most ad networks (Mediavine, Raptive, AdSense…) give you.
 * - ads.txt: the lines your ad network asks for, served at yoursite.co.uk/ads.txt.
 * - Ad spaces: optional code for fixed spaces between sections. Networks that place ads
 *   automatically don't need these.
 * - Preview: add ?ads=preview to any page address (while logged in) to see where the spaces are.
 * Cook Mode never shows adverts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alc_ad_slots() {
	return array(
		'home_1'        => 'Homepage – after “Something a little different”',
		'home_2'        => 'Homepage – after “Latest Recipes”',
		'recipe_after'  => 'Recipe page – after the recipe',
		'recipe_more'   => 'Recipe page – after “You might also like”',
		'recipes_list'  => 'Recipes page – below the recipe list',
		'above_footer'  => 'Every page – above the newsletter sign-up',
	);
}

function alc_ads_on() {
	return (bool) get_theme_mod( 'ads_enabled', false );
}

function alc_ads_preview() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return ( isset( $_GET['ads'] ) && 'preview' === $_GET['ads'] && current_user_can( 'edit_theme_options' ) ) || ( is_customize_preview() && alc_ads_on() );
}

/** Prints an ad space. Empty (and invisible) unless adverts are on and code is pasted in. */
function alc_ad( $slot ) {
	$slots = alc_ad_slots();
	if ( ! isset( $slots[ $slot ] ) ) {
		return;
	}
	$code = trim( (string) get_theme_mod( 'ad_' . $slot, '' ) );
	if ( alc_ads_on() && $code ) {
		$code = function_exists( 'alc_consent_wrap' ) ? alc_consent_wrap( $code, 'ads' ) : $code;
		echo '<div class="ad-slot ad-slot--' . esc_attr( $slot ) . '" aria-label="Advertisement">' . $code . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- code pasted by the site owner
	} elseif ( alc_ads_preview() ) {
		echo '<div class="ad-slot ad-slot--preview"><span>Advert space</span><small>' . esc_html( $slots[ $slot ] ) . '</small></div>';
	}
}

/** Ad network script in the <head>. */
add_action( 'wp_head', function () {
	$code = trim( (string) get_theme_mod( 'ads_head', '' ) );
	if ( alc_ads_on() && $code ) {
		echo "\n" . ( function_exists( 'alc_consent_wrap' ) ? alc_consent_wrap( $code, 'ads' ) : $code ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- code pasted by the site owner
	}
}, 1 );

/** yoursite.co.uk/ads.txt */
add_action( 'init', function () {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	$home = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( rtrim( (string) $home, '/' ) . '/ads.txt' !== $path ) {
		return;
	}
	$txt = trim( (string) get_theme_mod( 'ads_txt', '' ) );
	if ( '' === $txt ) {
		return; // Let WordPress show its normal "not found" page.
	}
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo $txt . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- plain text file
	exit;
}, 0 );

/** Customiser section. */
add_action( 'customize_register', function ( $wp ) {
	$raw = function ( $v ) {
		return current_user_can( 'unfiltered_html' ) ? $v : wp_kses_post( $v );
	};
	$wp->add_section( 'alc_ads', array(
		'title'       => '💷 Advertising (for later)',
		'priority'    => 27,
		'description' => 'Nothing shows until you tick “Turn on adverts”. When you join an ad network (e.g. Mediavine, Raptive or Google AdSense), paste the code they give you below. Most networks place ads automatically using the head code, so the ad spaces are optional. Tip: add ?ads=preview to the end of any page address while logged in to see where the ad spaces are.',
	) );
	$wp->add_setting( 'ads_enabled', array( 'default' => false, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp->add_control( 'ads_enabled', array( 'section' => 'alc_ads', 'type' => 'checkbox', 'label' => 'Turn on adverts' ) );
	$wp->add_setting( 'ads_head', array( 'default' => '', 'sanitize_callback' => $raw ) );
	$wp->add_control( 'ads_head', array( 'section' => 'alc_ads', 'type' => 'textarea', 'label' => 'Code for the <head>', 'description' => 'The script your ad network asks you to add to every page.' ) );
	$wp->add_setting( 'ads_txt', array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp->add_control( 'ads_txt', array( 'section' => 'alc_ads', 'type' => 'textarea', 'label' => 'ads.txt', 'description' => 'Paste the ads.txt lines your ad network gives you. They’ll appear at yoursite/ads.txt (this works even before adverts are turned on).' ) );
	foreach ( alc_ad_slots() as $slot => $label ) {
		$wp->add_setting( 'ad_' . $slot, array( 'default' => '', 'sanitize_callback' => $raw ) );
		$wp->add_control( 'ad_' . $slot, array( 'section' => 'alc_ads', 'type' => 'textarea', 'label' => 'Ad space: ' . $label ) );
	}
} );
