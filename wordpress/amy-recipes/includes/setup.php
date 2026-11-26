<?php
/**
 * First-time setup: creates the collections (with photos) and the first recipe,
 * so the site looks complete straight away. Runs once; never overwrites Amy's changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alc_activate() {
	alc_register_content_types();
	alc_seed_content();
	flush_rewrite_rules();
	set_transient( 'alc_just_activated', 1, 60 );
}

/** Add an image from the plugin's demo folder to the Media Library (once). */
function alc_import_demo_image( $file, $title ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_alc_demo', 'meta_value' => $file, 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$path = ALC_RECIPES_DIR . 'demo/' . $file;
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

function alc_seed_content() {
	if ( get_option( 'alc_seeded' ) ) {
		return;
	}
	$collections = array(
		array( 'Easy Family Dinners', 'easy-family-dinners', 'main', 1, 'pasta.jpg', 'Weeknight wins the whole family will love.' ),
		array( 'Slow Cooker Stash', 'slow-cooker-stash', 'main', 2, 'sq-33-1.jpg', 'Pop it on in the morning and come home to dinner that’s ready and waiting.' ),
		array( 'Lunchbox Line-Up', 'lunchbox-line-up', 'main', 3, 'hero.jpg', 'Packed lunch ideas the kids will actually eat.' ),
		array( 'Baking', 'baking', 'main', 4, 'sq-21.jpg', 'Bakes, treats and something for the tin.' ),
		array( 'Festive Favourites', 'festive-favourites', 'seasonal', 5, '', 'Seasonal recipes for every celebration of the year.' ),
		array( 'Fakeaways', 'fakeaways', 'more', 6, 'sq-7-1.jpg', 'Homemade versions of your favourite takeaways and high-street treats.' ),
		array( 'One Pan Wonders', 'one-pan-wonders', 'more', 7, 'hero.jpg', 'Easy family dinners with minimal washing up.' ),
		array( '30 Minute Meals', '30-minute-meals', 'tag', 8, 'pasta.jpg', 'On the table in half an hour or less.' ),
	);
	$ids = array();
	foreach ( $collections as $c ) {
		$term = term_exists( $c[1], 'recipe_collection' );
		if ( ! $term ) {
			$term = wp_insert_term( $c[0], 'recipe_collection', array( 'slug' => $c[1], 'description' => $c[5] ) );
		}
		if ( is_wp_error( $term ) ) {
			continue;
		}
		$tid           = (int) $term['term_id'];
		$ids[ $c[1] ] = $tid;
		if ( '' === get_term_meta( $tid, 'alc_group', true ) ) {
			update_term_meta( $tid, 'alc_group', $c[2] );
			update_term_meta( $tid, 'alc_order', $c[3] );
			if ( $c[4] ) {
				update_term_meta( $tid, 'alc_image', alc_import_demo_image( $c[4], $c[0] ) );
			}
		}
	}

	if ( ! get_posts( array( 'post_type' => 'recipe', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
		$id = wp_insert_post( array( 'post_type' => 'recipe', 'post_status' => 'publish', 'post_title' => 'Creamy Chicken Pesto Pasta', 'post_name' => 'creamy-chicken-pesto-pasta' ) );
		if ( $id && ! is_wp_error( $id ) ) {
			$meta = array(
				'alc_summary'     => 'One of those easy family dinners you’ll find yourself making again and again! Packed with flavour, wonderfully creamy and perfect for busy weeknights.',
				'alc_prep'        => 5,
				'alc_cook'        => 15,
				'alc_serves'      => 4,
				'alc_ingredients' => "Pasta & Chicken:\n300g dried pasta\n1 tbsp olive oil\n500g chicken breasts, diced\n1 white onion, chopped\n150g mushrooms, sliced\n\nCreamy Pesto Sauce:\n50g butter\n50g plain flour\n500ml milk\n½ jar green pesto\n50g mozzarella\n30g Parmesan\nExtra mozzarella and Parmesan, to top (optional)",
				'alc_method'      => "Bring a large saucepan of salted water to the boil. Cook the pasta according to the packet instructions, then drain, reserving a little pasta water.\nHeat the olive oil in a large frying pan. Add the diced chicken and cook until lightly golden.\nAdd the chopped onion and sliced mushrooms to the pan. Continue cooking until the vegetables have softened and the chicken is cooked all the way through.\nAdd the butter and allow it to melt, then stir in the flour. Cook for around 1 minute before gradually pouring in the milk, stirring continuously until you have a smooth, thickened sauce. [1 min]\nStir through the green pesto, mozzarella and Parmesan over a low heat until the cheese has melted and everything is combined.\nAdd the drained pasta and stir everything together until well coated in the creamy pesto sauce. Add a splash of reserved pasta water if needed to loosen the sauce.\nOptional: top with extra mozzarella and Parmesan, then pop under the grill until golden and bubbling.",
				'alc_tips'        => "Recipe tips: For a thinner sauce, add a splash of reserved pasta water or extra milk. You can also swap the mushrooms for spinach or leave them out entirely.\nMake ahead: Chop the chicken, onion and mushrooms in advance and keep them covered in the fridge. Keep raw chicken separate from the vegetables.\nLeftovers: Cool quickly, then refrigerate in an airtight container within 2 hours. Eat within 48 hours. Reheat until piping hot with a splash of milk. Only reheat once.\nFreezing: Best enjoyed fresh, as creamy sauces can change texture. If freezing, portion, freeze promptly and defrost fully in the fridge before reheating thoroughly.",
				'alc_video'       => 'https://youtu.be/FvktW_5l0nY',
				'alc_video_title' => 'Can I Make Creamy Chicken Pesto Pasta in 30 Minutes?',
				'alc_keywords'    => 'pesto pasta bake',
				'alc_new'         => '1',
			);
			foreach ( $meta as $k => $v ) {
				update_post_meta( $id, $k, $v );
			}
			wp_set_object_terms( $id, array_values( array_filter( array( $ids['easy-family-dinners'] ?? 0, $ids['30-minute-meals'] ?? 0 ) ) ), 'recipe_collection' );
			$img = alc_import_demo_image( 'pasta.jpg', 'Creamy chicken pesto pasta' );
			if ( $img ) {
				set_post_thumbnail( $id, $img );
			}
		}
	}
	if ( false === get_option( 'alc_moods', false ) ) {
		update_option( 'alc_moods', alc_moods_default() );
	}
	update_option( 'alc_seeded', ALC_RECIPES_VERSION );
}

add_action( 'admin_notices', function () {
	if ( get_transient( 'alc_just_activated' ) ) {
		delete_transient( 'alc_just_activated' );
		echo '<div class="notice notice-success is-dismissible"><p><strong>Amy’s Recipes is ready!</strong> Your collections and first recipe have been added. <a href="' . esc_url( admin_url( 'admin.php?page=alc-guide' ) ) . '">Open the Website Guide →</a></p></div>';
	}
	if ( ! wp_get_theme()->get_stylesheet() || 'amy-louise-crisp' !== get_stylesheet() ) {
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->id, array( 'dashboard', 'edit-recipe' ), true ) ) {
			echo '<div class="notice notice-info"><p>For the full design, activate the <strong>Amy Louise Crisp</strong> theme in Appearance → Themes.</p></div>';
		}
	}
} );
