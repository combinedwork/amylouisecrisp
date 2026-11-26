<?php
/**
 * Appearance → Customise: every word and photo on the homepage, the newsletter and social links.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alc_defaults() {
	return array(
		// Top of the homepage
		'hero_eyebrow'    => 'Welcome to Amy Louise Crisp',
		'hero_heading'    => 'Family recipes you’ll *actually* make',
		'hero_text1'      => 'Hi, I’m Amy! A mum of two who loves sharing easy family dinners, delicious baking and lunchbox inspiration.',
		'hero_text2'      => 'Cooking from scratch doesn’t need to be complicated, expensive or take hours in the kitchen. So grab a cuppa and find something delicious to make!',
		'hero_btn1'       => 'Browse Recipes',
		'hero_btn2'       => 'About Me',
		'mood_heading'    => 'What are you in the mood for?',
		'cats_eyebrow'    => 'Browse by category',
		'cats_heading'    => 'Find Something Delicious',
		'coll_eyebrow'    => 'More to explore',
		'coll_heading'    => 'Something a little different',
		'latest_eyebrow'  => 'Fresh from my kitchen',
		'latest_heading'  => 'Latest Recipes',
		'about_image'     => alc_theme_img( 'sq-21.jpg' ),
		'about_eyebrow'   => 'About Me',
		'about_heading'   => 'Hi, I’m Amy!',
		'about_text'      => "I’m a mum of two who loves sharing easy family dinners, delicious baking and lunchbox inspiration.\n\nI believe cooking from scratch doesn’t need to be complicated, expensive or take hours in the kitchen. Here you’ll find simple, family-friendly recipes you’ll actually want to make again and again!",
		'about_sign'      => 'Amy x',
		'about_btn'       => 'Read my story',
		// Slideshow
		'slides_seconds'  => 3,
		'slide1_image'    => alc_theme_img( 'pasta.jpg' ),
		'slide1_title'    => '',
		'slide1_sub'      => '',
		'slide1_badge'    => '',
		'slide1_link'     => '',
		'slide2_image'    => alc_theme_img( 'sq-7-1.jpg' ),
		'slide2_title'    => 'Fakeaways',
		'slide2_sub'      => 'Your takeaway favourites, made at home',
		'slide2_badge'    => '',
		'slide2_link'     => '',
		'slide3_image'    => alc_theme_img( 'sq-21.jpg' ),
		'slide3_title'    => 'Baking',
		'slide3_sub'      => 'Bakes and treats for the tin',
		'slide3_badge'    => '',
		'slide3_link'     => '',
		'slide4_image'    => alc_theme_img( 'hero.jpg' ),
		'slide4_title'    => 'Hi, I’m Amy!',
		'slide4_sub'      => 'Meet the cook behind the recipes',
		'slide4_badge'    => '',
		'slide4_link'     => '',
		// Watch & cook along
		'video_show'      => true,
		'video_url'       => 'https://youtu.be/FvktW_5l0nY',
		'video_title'     => 'Can I Make Creamy Chicken Pesto Pasta in 30 Minutes?',
		'video_eyebrow'   => 'Watch & cook along',
		'video_heading'   => 'Can I make Creamy Chicken Pesto Pasta in 30 minutes?',
		'video_text'      => 'Grab a cuppa and join me in the kitchen! I’ll show you step by step how this creamy, cheesy family favourite comes together, from a silky homemade pesto sauce to that bubbling golden topping.',
		'video_points'    => "20 | Minutes from start to finish\n4 | Hungry people fed\n2 | Pans: just a frying pan and a saucepan",
		'video_image'     => alc_theme_img( 'pasta.jpg' ),
		'video_recipe'    => 0,
		// Newsletter
		'news_eyebrow'    => 'The monthly round-up',
		'news_heading'    => 'New recipes, straight to your inbox',
		'news_text'       => 'Once a month I’ll send you a little round-up of new recipes, family favourites and anything else I think you’ll enjoy. No spam, just good food.',
		'news_small'      => 'One email a month. Unsubscribe anytime.',
		'news_embed'      => '',
		// Social
		'alc_instagram'   => 'https://www.instagram.com/amylouisecrisp.x/',
		'alc_facebook'    => 'https://www.facebook.com/profile.php?id=61556348852629',
		'alc_pinterest'   => '',
		'alc_tiktok'      => 'https://www.tiktok.com/@amylouisecrisp.x',
		'alc_youtube'     => 'https://www.youtube.com/channel/UCjtX0o8ZOkc6L4hd2mzneVA',
		'footer_logo'     => alc_theme_img( 'logo/logo-full.png' ),
	);
}

/** Lets Amy paste a newsletter form (MailerLite, Kit, Mailchimp...) if she's allowed to post code. */
function alc_sanitize_embed( $v ) {
	return current_user_can( 'unfiltered_html' ) ? $v : wp_kses_post( $v );
}

add_action( 'customize_register', function ( $wp ) {
	$d = alc_defaults();

	$wp->add_panel( 'alc_home', array( 'title' => '🏠 Homepage', 'priority' => 20, 'description' => 'Change the words and photos on your homepage. Changes show in the preview straight away; click Publish to make them live.' ) );

	$add = function ( $section, $key, $label, $type = 'text', $desc = '', $extra = array() ) use ( $wp, $d ) {
		$sanitize = array(
			'text'     => 'sanitize_text_field',
			'textarea' => 'sanitize_textarea_field',
			'url'      => 'esc_url_raw',
			'image'    => 'esc_url_raw',
			'number'   => 'absint',
			'checkbox' => 'wp_validate_boolean',
			'select'   => 'absint',
			'embed'    => 'alc_sanitize_embed',
			'upload'   => 'esc_url_raw',
		);
		$wp->add_setting( $key, array( 'default' => $d[ $key ] ?? '', 'sanitize_callback' => $sanitize[ $type ], 'transport' => 'refresh' ) );
		$args = array_merge( array( 'section' => $section, 'label' => $label, 'description' => $desc ), $extra );
		if ( 'image' === $type ) {
			$wp->add_control( new WP_Customize_Image_Control( $wp, $key, $args ) );
		} elseif ( 'upload' === $type ) {
			$wp->add_control( new WP_Customize_Upload_Control( $wp, $key, $args ) );
		} else {
			$args['type'] = 'embed' === $type ? 'textarea' : $type;
			$wp->add_control( $key, $args );
		}
	};

	// Top section + About
	$wp->add_section( 'alc_home_hero', array( 'title' => 'Top of the page & About Me', 'panel' => 'alc_home', 'priority' => 10 ) );
	$add( 'alc_home_hero', 'hero_eyebrow', 'Small heading' );
	$add( 'alc_home_hero', 'hero_heading', 'Big heading', 'text', 'Put a word between *stars* to highlight it, e.g. Family recipes you’ll *actually* make' );
	$add( 'alc_home_hero', 'hero_text1', 'First paragraph', 'textarea' );
	$add( 'alc_home_hero', 'hero_text2', 'Second paragraph', 'textarea' );
	$add( 'alc_home_hero', 'hero_btn1', 'Green button wording (goes to Recipes)' );
	$add( 'alc_home_hero', 'hero_btn2', 'Outline button wording (goes to About Me)' );
	$add( 'alc_home_hero', 'mood_heading', '“Mood picker” question', 'text', 'The buttons themselves are in Recipes → Festive Season & Moods.' );
	$add( 'alc_home_hero', 'cats_eyebrow', 'Categories: small heading' );
	$add( 'alc_home_hero', 'cats_heading', 'Categories: heading', 'text', 'Which collections appear here is set in Recipes → Collections (“Where it appears”).' );
	$add( 'alc_home_hero', 'coll_eyebrow', 'Extra collections: small heading' );
	$add( 'alc_home_hero', 'coll_heading', 'Extra collections: heading' );
	$add( 'alc_home_hero', 'latest_eyebrow', 'Latest recipes: small heading' );
	$add( 'alc_home_hero', 'latest_heading', 'Latest recipes: heading' );
	$add( 'alc_home_hero', 'about_image', 'About Me photo', 'image', 'A square photo works best.' );
	$add( 'alc_home_hero', 'about_eyebrow', 'About Me: small heading' );
	$add( 'alc_home_hero', 'about_heading', 'About Me: heading' );
	$add( 'alc_home_hero', 'about_text', 'About Me: text', 'textarea', 'Leave an empty line between paragraphs.' );
	$add( 'alc_home_hero', 'about_sign', 'Sign-off', 'text', 'e.g. Amy x' );
	$add( 'alc_home_hero', 'about_btn', 'About Me: button wording' );

	// Slideshow
	$wp->add_section( 'alc_home_slides', array( 'title' => 'Slideshow (top right)', 'panel' => 'alc_home', 'priority' => 20, 'description' => 'Four photos that change automatically. Each has a little label with a link. Leave the first slide’s wording blank to show your newest recipe automatically.' ) );
	$add( 'alc_home_slides', 'slides_seconds', 'Seconds per slide', 'number', '', array( 'input_attrs' => array( 'min' => 2, 'max' => 15 ) ) );
	for ( $i = 1; $i <= 4; $i++ ) {
		$add( 'alc_home_slides', "slide{$i}_image", "Slide {$i}: photo", 'image', 1 === $i ? 'Landscape photos look best.' : '' );
		$add( 'alc_home_slides', "slide{$i}_title", "Slide {$i}: label title" );
		$add( 'alc_home_slides', "slide{$i}_sub", "Slide {$i}: label line" );
		$add( 'alc_home_slides', "slide{$i}_link", "Slide {$i}: link (web address)", 'url', 'Copy the address of any page, recipe or collection.' );
	}

	// Video
	$recipes = array( 0 => '— Your newest recipe —' );
	if ( post_type_exists( 'recipe' ) ) {
		foreach ( get_posts( array( 'post_type' => 'recipe', 'numberposts' => 200 ) ) as $p ) {
			$recipes[ $p->ID ] = $p->post_title;
		}
	}
	$wp->add_section( 'alc_home_video', array( 'title' => 'Watch & Cook Along video', 'panel' => 'alc_home', 'priority' => 30 ) );
	$add( 'alc_home_video', 'video_show', 'Show this section', 'checkbox' );
	$add( 'alc_home_video', 'video_url', 'YouTube link', 'url', 'Paste the link from YouTube’s Share button.' );
	$add( 'alc_home_video', 'video_title', 'Video title', 'text', 'Read out by screen readers.' );
	$add( 'alc_home_video', 'video_image', 'Thumbnail photo', 'image' );
	$add( 'alc_home_video', 'video_eyebrow', 'Small heading' );
	$add( 'alc_home_video', 'video_heading', 'Heading' );
	$add( 'alc_home_video', 'video_text', 'Text', 'textarea' );
	$add( 'alc_home_video', 'video_points', 'Quick facts', 'textarea', 'One per line: number | words, e.g. 20 | Minutes from start to finish' );
	$add( 'alc_home_video', 'video_recipe', '“Get the Recipe” button goes to', 'select', '', array( 'choices' => $recipes ) );

	// Newsletter
	$wp->add_section( 'alc_newsletter', array( 'title' => '💌 Newsletter sign-up', 'priority' => 25, 'description' => 'Shown at the bottom of every page.' ) );
	$add( 'alc_newsletter', 'news_eyebrow', 'Small heading' );
	$add( 'alc_newsletter', 'news_heading', 'Heading' );
	$add( 'alc_newsletter', 'news_text', 'Text', 'textarea' );
	$add( 'alc_newsletter', 'news_small', 'Small print' );
	$add( 'alc_newsletter', 'news_embed', 'Sign-up form code', 'embed', 'Paste the form code or shortcode from MailerLite, Kit or Mailchimp here. Until you do, a sample form is shown that doesn’t save emails.' );

	// Social
	$wp->add_section( 'alc_social', array( 'title' => '📱 Social media links', 'priority' => 26, 'description' => 'Paste the full web address of each profile. Leave blank to hide an icon.' ) );
	$add( 'alc_social', 'alc_instagram', 'Instagram', 'url' );
	$add( 'alc_social', 'alc_facebook', 'Facebook', 'url' );
	$add( 'alc_social', 'alc_pinterest', 'Pinterest', 'url' );
	$add( 'alc_social', 'alc_tiktok', 'TikTok', 'url' );
	$add( 'alc_social', 'alc_youtube', 'YouTube channel', 'url', 'Also used for the “Visit my YouTube channel” button.' );

	// Font (Site Identity)
	$wp->add_setting( 'alc_font_file', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp->add_control( new WP_Customize_Upload_Control( $wp, 'alc_font_file', array(
		'section'     => 'title_tagline',
		'label'       => 'The Seasons font file (optional)',
		'description' => 'Upload the licensed web font file (.woff2) for “The Seasons” and it will be used for headings. Until then a similar font is used.',
		'priority'    => 60,
	) ) );
	$wp->add_setting( 'footer_logo', array( 'default' => alc_theme_img( 'logo/logo-full.png' ), 'sanitize_callback' => 'esc_url_raw' ) );
	$wp->add_control( new WP_Customize_Image_Control( $wp, 'footer_logo', array(
		'section'     => 'title_tagline',
		'label'       => 'Footer logo',
		'description' => 'The larger logo at the bottom of every page.',
		'priority'    => 9,
	) ) );
	$wp->get_control( 'custom_logo' )->description = 'Your logo is already built into the theme. To use a different one, click “Select logo”. A wide version (name and tagline side by side) works best at the top of the page.';
	$wp->get_section( 'title_tagline' )->title = '✨ Logo, site name & font';
} );
