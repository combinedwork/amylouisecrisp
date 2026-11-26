<?php
/**
 * First-time setup when the theme is activated: tagline, tidy web addresses, the About page and menus.
 * Only creates things that don't exist yet, so it never overwrites Amy's work.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Add a photo from the theme folder to the Media Library (once). */
function alc_theme_import_image( $file, $title ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_alc_demo', 'meta_value' => $file, 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$path = get_template_directory() . '/assets/img/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$upload = wp_upload_bits( $file, null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$id = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => $title, 'post_status' => 'inherit' ), $upload['file'] );
	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_alc_demo', $file );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	return (int) $id;
}

/** Block markup helpers for the About page. */
function alc_b_img( $id, $class = '' ) {
	$url = wp_get_attachment_image_url( $id, 'large' );
	$alt = esc_attr( get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	$att = array( 'id' => $id, 'sizeSlug' => 'large', 'linkDestination' => 'none' );
	if ( $class ) {
		$att['className'] = $class;
	}
	return '<!-- wp:image ' . wp_json_encode( $att ) . ' --><figure class="wp-block-image size-large' . ( $class ? ' ' . $class : '' ) . '"><img src="' . esc_url( $url ) . '" alt="' . $alt . '" class="wp-image-' . $id . '"/></figure><!-- /wp:image -->';
}
function alc_b_p( $text, $class = '' ) {
	return $class
		? '<!-- wp:paragraph {"className":"' . $class . '"} --><p class="' . $class . '">' . $text . '</p><!-- /wp:paragraph -->'
		: '<!-- wp:paragraph --><p>' . $text . '</p><!-- /wp:paragraph -->';
}
function alc_b_h( $text, $level = 2, $anchor = '' ) {
	$att = array();
	if ( 2 !== $level ) {
		$att['level'] = $level;
	}
	if ( $anchor ) {
		$att['anchor'] = $anchor;
	}
	return '<!-- wp:heading' . ( $att ? ' ' . wp_json_encode( $att ) : '' ) . ' --><h' . $level . ' class="wp-block-heading"' . ( $anchor ? ' id="' . $anchor . '"' : '' ) . '>' . $text . '</h' . $level . '><!-- /wp:heading -->';
}
function alc_b_cols( $cols, $class = '' ) {
	$att = array( 'verticalAlignment' => 'center' );
	if ( $class ) {
		$att['className'] = $class;
	}
	$out = '<!-- wp:columns ' . wp_json_encode( $att ) . ' --><div class="wp-block-columns are-vertically-aligned-center' . ( $class ? ' ' . $class : '' ) . '">';
	foreach ( $cols as $c ) {
		$out .= '<!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center">' . $c . '</div><!-- /wp:column -->';
	}
	return $out . '</div><!-- /wp:columns -->';
}
function alc_b_button( $text, $url, $outline = false ) {
	$att = $outline ? ' {"className":"is-style-outline"}' : '';
	return '<!-- wp:button' . $att . ' --><div class="wp-block-button' . ( $outline ? ' is-style-outline' : '' ) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . $text . '</a></div><!-- /wp:button -->';
}
function alc_b_details( $q, $a ) {
	return '<!-- wp:details --><details class="wp-block-details"><summary>' . $q . '</summary>' . alc_b_p( $a ) . '</details><!-- /wp:details -->';
}

function alc_about_content() {
	$amy     = alc_theme_import_image( 'sq-21.jpg', 'Amy holding a freshly baked cookie' );
	$kitchen = alc_theme_import_image( 'sq-33-1.jpg', 'Amy tasting from the pan in her kitchen' );
	$pizza   = alc_theme_import_image( 'sq-7-1.jpg', 'Pepperoni pizza fresh from the oven' );
	$pasta   = alc_theme_import_image( 'pasta.jpg', 'Creamy chicken pesto pasta' );
	$recipes = alc_recipes_url();
	$email   = 'mailto:' . get_option( 'admin_email' );

	$gallery = '<!-- wp:gallery {"columns":4,"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-4 is-cropped">';
	foreach ( array( $pizza, $pasta, $amy, $kitchen ) as $g ) {
		$gallery .= '<!-- wp:image {"id":' . $g . ',"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . esc_url( wp_get_attachment_image_url( $g, 'large' ) ) . '" alt="' . esc_attr( get_post_meta( $g, '_wp_attachment_image_alt', true ) ) . '" class="wp-image-' . $g . '"/></figure><!-- /wp:image -->';
	}
	$gallery .= '</figure><!-- /wp:gallery -->';

	return implode( "\n\n", array(
		alc_b_cols( array(
			alc_b_p( 'About Me', 'eyebrow' ) . alc_b_h( 'Hi, I’m <em>Amy!</em>', 2 ) .
			alc_b_p( 'I’m a mum of two who loves sharing easy family dinners, delicious baking and lunchbox inspiration.' ) .
			alc_b_p( 'I believe cooking from scratch doesn’t need to be complicated, expensive or take hours in the kitchen. Here you’ll find simple, family-friendly recipes you’ll actually want to make again and again!' ) .
			'<!-- wp:buttons --><div class="wp-block-buttons">' . alc_b_button( 'Browse Recipes', $recipes ) . alc_b_button( 'Get in Touch', '#contact', true ) . '</div><!-- /wp:buttons -->',
			alc_b_img( $amy, 'is-style-rounded' ),
		), 'about-intro' ),
		'<!-- wp:quote --><blockquote class="wp-block-quote">' . alc_b_p( 'Cooking from scratch doesn’t need to be complicated, expensive or take hours in the kitchen.' ) . '</blockquote><!-- /wp:quote -->',
		alc_b_p( 'My kitchen rules', 'eyebrow' ) . alc_b_h( 'What every recipe has in common' ),
		alc_b_cols( array(
			alc_b_h( 'Simple', 3 ) . alc_b_p( 'Easy-to-follow steps with no fancy techniques or hard-to-find equipment. If I wouldn’t make it on a Tuesday night, it doesn’t go on here.' ),
			alc_b_h( 'Affordable', 3 ) . alc_b_p( 'Everyday ingredients you can pick up in the weekly shop, so cooking from scratch doesn’t have to cost more.' ),
			alc_b_h( 'Family-friendly', 3 ) . alc_b_p( 'Food the whole table will actually eat, from fussy little ones to hungry grown-ups. No hours in the kitchen required.' ),
		), 'values-cols' ),
		alc_b_cols( array(
			alc_b_img( $kitchen ),
			alc_b_p( 'My story', 'eyebrow' ) . alc_b_h( 'From busy weeknights to a recipe blog' ) .
			alc_b_p( 'With two little ones at home, I know how quickly the day disappears, and how tempting it is to reach for something easy. I wanted family meals that were quick, affordable and still made from scratch.' ) .
			alc_b_p( 'So I started writing down the recipes that worked for us: the dinners everyone finished, the bakes that never lasted long in the tin and the lunchbox ideas that came home empty. This little corner of the internet is where I share them with you.' ) .
			alc_b_p( 'Amy x', 'sig' ),
		), 'story-cols' ),
		alc_b_p( 'Behind the scenes', 'eyebrow' ) . alc_b_h( 'In my kitchen' ),
		$gallery,
		alc_b_p( 'Good to know', 'eyebrow' ) . alc_b_h( 'Frequently Asked Questions' ),
		alc_b_details( 'Are your recipes suitable for kids?', 'Yes! Everything here is made with families in mind, using simple ingredients and flavours the whole table can enjoy.' ),
		alc_b_details( 'Do I need any special equipment?', 'Not at all. Most recipes need nothing more than everyday kit like a saucepan, a frying pan and a baking tin.' ),
		alc_b_details( 'Can I make recipes ahead or freeze them?', 'Where a recipe can be made ahead or frozen, you’ll find tips in the “Tips &amp; Storage” tab on the recipe.' ),
		alc_b_details( 'How do I hear about new recipes?', 'Pop your email in the newsletter box below. Once a month I’ll send a round-up of new recipes, family favourites and anything else I think you’ll enjoy.' ),
		alc_b_details( 'Can we work together?', 'I’d love to hear from you! Drop me an email using the button below.' ),
		alc_b_p( 'Get in touch', 'eyebrow' ) . alc_b_h( 'Say hello!', 2, 'contact' ),
		alc_b_p( 'Made one of my recipes? Got a question, or an idea for something you’d love to see? I’d love to hear from you.' ),
		'<!-- wp:buttons --><div class="wp-block-buttons">' . alc_b_button( 'Email me', $email ) . '</div><!-- /wp:buttons -->',
	) );
}

// Setup waits until the recipes plugin is active, so menu links point to the right places.
add_action( 'after_switch_theme', function () {
	update_option( 'alc_theme_needs_setup', 1 );
} );
add_action( 'init', function () {
	if ( get_option( 'alc_theme_needs_setup' ) && post_type_exists( 'recipe' ) && is_admin() ) {
		delete_option( 'alc_theme_needs_setup' );
		alc_theme_setup_content();
	}
}, 99 );
function alc_theme_setup_content() {
	$tagline = get_option( 'blogdescription' );
	if ( ! $tagline || 'Just another WordPress site' === $tagline ) {
		update_option( 'blogdescription', 'Family recipes you’ll actually make' );
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	// About page
	$about = get_page_by_path( 'about' );
	if ( ! $about ) {
		$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'About Me', 'post_name' => 'about', 'post_content' => alc_about_content() ) );
		if ( $id && ! is_wp_error( $id ) ) {
			set_post_thumbnail( $id, alc_theme_import_image( 'sq-21.jpg', 'Amy holding a freshly baked cookie' ) );
			$about = get_post( $id );
		}
	}

	// Privacy & Cookie Policy (published, so the footer menu below can link to it)
	if ( function_exists( 'alc_setup_privacy_page' ) ) {
		alc_setup_privacy_page();
		update_option( 'alc_privacy_setup', 1 );
	}

	// Menus
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		$menu = wp_get_nav_menu_object( 'Main menu' );
		$mid  = $menu ? $menu->term_id : wp_create_nav_menu( 'Main menu' );
		if ( ! $menu && ! is_wp_error( $mid ) ) {
			$items = array(
				array( 'Home', home_url( '/' ) ),
				array( 'Recipes', alc_recipes_url() ),
				array( 'Festive Favourites', alc_recipes_url( 'festive-favourites' ) ),
				array( 'Fakeaways', alc_recipes_url( 'fakeaways' ) ),
				array( 'One Pan Wonders', alc_recipes_url( 'one-pan-wonders' ) ),
			);
			foreach ( $items as $it ) {
				wp_update_nav_menu_item( $mid, 0, array( 'menu-item-title' => $it[0], 'menu-item-url' => $it[1], 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ) );
			}
			if ( $about ) {
				wp_update_nav_menu_item( $mid, 0, array( 'menu-item-title' => 'About Me', 'menu-item-object' => 'page', 'menu-item-object-id' => $about->ID, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
		}
		if ( ! is_wp_error( $mid ) ) {
			$locations['primary'] = $mid;
		}
	}
	if ( empty( $locations['footer'] ) ) {
		$menu = wp_get_nav_menu_object( 'Footer menu' );
		$fid  = $menu ? $menu->term_id : wp_create_nav_menu( 'Footer menu' );
		if ( ! $menu && ! is_wp_error( $fid ) ) {
			if ( $about ) {
				wp_update_nav_menu_item( $fid, 0, array( 'menu-item-title' => 'About Me', 'menu-item-object' => 'page', 'menu-item-object-id' => $about->ID, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
				wp_update_nav_menu_item( $fid, 0, array( 'menu-item-title' => 'Contact', 'menu-item-url' => get_permalink( $about ) . '#contact', 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
			}
			wp_update_nav_menu_item( $fid, 0, array( 'menu-item-title' => 'Newsletter', 'menu-item-url' => home_url( '/#newsletter' ), 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
			$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
			if ( $privacy && 'publish' === get_post_status( $privacy ) ) {
				wp_update_nav_menu_item( $fid, 0, array( 'menu-item-title' => 'Privacy Policy', 'menu-item-object' => 'page', 'menu-item-object-id' => $privacy, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
		}
		if ( ! is_wp_error( $fid ) ) {
			$locations['footer'] = $fid;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	flush_rewrite_rules();
}
