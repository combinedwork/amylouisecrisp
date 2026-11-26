<?php
/**
 * Privacy & cookies: cookie banner settings, visitor statistics code (held back until
 * the visitor agrees), and the Privacy & Cookie Policy page.
 * Appearance → Customise → 🍪 Privacy & cookies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/privacy-content.php';

function alc_consent_on() {
	return (bool) get_theme_mod( 'consent_enabled', true );
}

/** Ad network brings its own consent banner (Mediavine, Raptive etc. usually do). */
function alc_ads_own_cmp() {
	return (bool) get_theme_mod( 'ads_own_cmp', false );
}

/**
 * Wrap code so it only runs once the visitor allows that kind of cookie.
 * $category: analytics | ads | media
 */
function alc_consent_wrap( $code, $category ) {
	if ( ! alc_consent_on() || ( 'ads' === $category && alc_ads_own_cmp() ) ) {
		return $code;
	}
	return '<template data-consent="' . esc_attr( $category ) . '">' . $code . '</template>';
}

function alc_privacy_url() {
	$url = get_privacy_policy_url();
	if ( $url ) {
		return $url;
	}
	$p = get_page_by_path( 'privacy-policy' );
	return $p ? get_permalink( $p ) : home_url( '/' );
}

/** Banner script + settings, on every page. */
add_action( 'wp_enqueue_scripts', function () {
	$cats = array();
	if ( trim( (string) get_theme_mod( 'analytics_code', '' ) ) ) {
		$cats[] = 'analytics';
	}
	if ( function_exists( 'alc_ads_on' ) && alc_ads_on() && ! alc_ads_own_cmp() ) {
		$cats[] = 'ads';
	}
	$cats[] = 'media';
	$cfg = array(
		'enabled'    => alc_consent_on(),
		'privacyUrl' => alc_privacy_url(),
		'heading'    => get_theme_mod( 'consent_heading', 'Cookies & your privacy' ),
		'text'       => get_theme_mod( 'consent_text', 'I use a few cookies to understand how the site is used and, in future, to show adverts that keep my recipes free. Saved recipes stay on your device. You choose what’s allowed.' ),
		'categories' => $cats,
		'version'    => 1,
	);
	wp_register_script( 'alc-consent', get_template_directory_uri() . '/assets/consent.js', array(), ALC_THEME_VERSION, true );
	wp_add_inline_script( 'alc-consent', 'window.ALC_CONSENT = ' . wp_json_encode( $cfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . ';', 'before' );
	wp_enqueue_script( 'alc-consent' );
}, 5 );

/** Visitor statistics code (e.g. Google Analytics) – only runs with permission. */
add_action( 'wp_head', function () {
	$code = trim( (string) get_theme_mod( 'analytics_code', '' ) );
	if ( $code ) {
		echo "\n" . alc_consent_wrap( $code, 'analytics' ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- code pasted by the site owner
	}
}, 2 );

/** Customiser section. */
add_action( 'customize_register', function ( $wp ) {
	$raw = function ( $v ) {
		return current_user_can( 'unfiltered_html' ) ? $v : wp_kses_post( $v );
	};
	$wp->add_section( 'alc_privacy', array(
		'title'       => '🍪 Privacy & cookies',
		'priority'    => 26,
		'description' => 'The cookie banner asks visitors before any statistics, adverts or YouTube videos load. Your Privacy & Cookie Policy is a normal page: edit it in Pages.',
	) );
	$wp->add_setting( 'consent_enabled', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp->add_control( 'consent_enabled', array( 'section' => 'alc_privacy', 'type' => 'checkbox', 'label' => 'Show the cookie banner', 'description' => 'Leave this on. Only turn it off if another plugin or your ad network shows a cookie banner instead.' ) );
	$wp->add_setting( 'consent_heading', array( 'default' => 'Cookies & your privacy', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp->add_control( 'consent_heading', array( 'section' => 'alc_privacy', 'label' => 'Banner heading' ) );
	$wp->add_setting( 'consent_text', array( 'default' => 'I use a few cookies to understand how the site is used and, in future, to show adverts that keep my recipes free. Saved recipes stay on your device. You choose what’s allowed.', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp->add_control( 'consent_text', array( 'section' => 'alc_privacy', 'type' => 'textarea', 'label' => 'Banner wording' ) );
	$wp->add_setting( 'analytics_code', array( 'default' => '', 'sanitize_callback' => $raw ) );
	$wp->add_control( 'analytics_code', array( 'section' => 'alc_privacy', 'type' => 'textarea', 'label' => 'Visitor statistics code (e.g. Google Analytics)', 'description' => 'Paste the code from Google Analytics (“Install manually”). It only runs for visitors who allow statistics.' ) );
	$wp->add_setting( 'ads_own_cmp', array( 'default' => false, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp->add_control( 'ads_own_cmp', array( 'section' => 'alc_privacy', 'type' => 'checkbox', 'label' => 'My ad network shows its own consent banner', 'description' => 'Tick this only if your ad network (e.g. Mediavine or Raptive) asks for consent itself. Your ad code then runs straight away and the “Advertising” choice is removed from this banner.' ) );
} );

/** Create (or fill in the default draft of) the Privacy & Cookie Policy page – once. Never overwrites a published policy. */
function alc_setup_privacy_page() {
	$pid  = (int) get_option( 'wp_page_for_privacy_policy' );
	$page = $pid ? get_post( $pid ) : null;
	if ( $page && 'publish' === $page->post_status ) {
		return $pid; // Amy already has a published policy – leave it alone.
	}
	$args = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Privacy & Cookie Policy',
		'post_name'    => 'privacy-policy',
		'post_content' => alc_privacy_content(),
	);
	if ( $page && 'trash' !== $page->post_status ) {
		$args['ID'] = $pid; // WordPress's own unpublished draft
		$pid        = wp_update_post( $args );
	} else {
		$pid = wp_insert_post( $args );
	}
	if ( $pid && ! is_wp_error( $pid ) ) {
		update_option( 'wp_page_for_privacy_policy', (int) $pid );
		return (int) $pid;
	}
	return 0;
}

// Sites that already ran the theme setup get the policy page on their next dashboard visit.
add_action( 'admin_init', function () {
	if ( get_option( 'alc_privacy_setup' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	update_option( 'alc_privacy_setup', 1 );
	$pid = alc_setup_privacy_page();
	// Add it to the footer "More" menu if it isn't there yet
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( $pid && ! empty( $locations['footer'] ) ) {
		$items = wp_get_nav_menu_items( $locations['footer'] );
		$has   = $items && array_filter( $items, function ( $i ) use ( $pid ) {
			return (int) $i->object_id === $pid;
		} );
		if ( ! $has ) {
			wp_update_nav_menu_item( $locations['footer'], 0, array( 'menu-item-title' => 'Privacy Policy', 'menu-item-object' => 'page', 'menu-item-object-id' => $pid, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		}
	}
} );
