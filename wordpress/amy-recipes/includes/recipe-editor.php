<?php
/**
 * The recipe form: simple boxes with examples, plus a preview of how Cook Mode reads the recipe.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** All the recipe fields, in the order they appear on the form. */
function alc_recipe_fields() {
	return array(
		'alc_summary'     => 'textarea',
		'alc_prep'        => 'int',
		'alc_cook'        => 'int',
		'alc_serves'      => 'int',
		'alc_ingredients' => 'textarea',
		'alc_method'      => 'textarea',
		'alc_tips'        => 'textarea',
		'alc_video'       => 'url',
		'alc_video_title' => 'text',
		'alc_keywords'    => 'text',
		'alc_new'         => 'bool',
	);
}

add_action( 'add_meta_boxes_recipe', function () {
	add_meta_box( 'alc_recipe', 'Recipe details', 'alc_recipe_box', 'recipe', 'normal', 'high' );
	add_meta_box( 'alc_recipe_preview', 'How your recipe will look in Cook Mode', 'alc_recipe_preview_box', 'recipe', 'normal', 'default' );
	add_meta_box( 'alc_recipe_help', 'Quick help', 'alc_recipe_help_box', 'recipe', 'side', 'default' );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	if ( $screen && ( 'recipe' === $screen->post_type || 'edit-recipe_collection' === $screen->id || false !== strpos( $hook, 'alc-' ) ) ) {
		wp_enqueue_style( 'alc-admin', ALC_RECIPES_URL . 'assets/admin.css', array(), ALC_RECIPES_VERSION );
		wp_enqueue_media();
		wp_enqueue_script( 'alc-admin', ALC_RECIPES_URL . 'assets/admin.js', array( 'jquery' ), ALC_RECIPES_VERSION, true );
	}
} );

function alc_field( $post, $key, $default = '' ) {
	$v = get_post_meta( $post->ID, $key, true );
	return '' === $v ? $default : $v;
}

function alc_recipe_box( $post ) {
	wp_nonce_field( 'alc_save_recipe', 'alc_recipe_nonce' );
	?>
	<div class="alc-form">
		<?php if ( ! has_post_thumbnail( $post ) ) : ?>
			<p class="alc-tip alc-tip--photo">📷 Don’t forget the photo: click <strong>“Choose recipe photo”</strong> in the box on the right.</p>
		<?php endif; ?>

		<label class="alc-label" for="alc_summary">Short description</label>
		<p class="alc-help">One or two sentences shown on recipe cards and at the top of the recipe.</p>
		<textarea id="alc_summary" name="alc_summary" rows="3" placeholder="One of those easy family dinners you’ll find yourself making again and again!"><?php echo esc_textarea( alc_field( $post, 'alc_summary' ) ); ?></textarea>

		<div class="alc-row">
			<div><label class="alc-label" for="alc_prep">Prep time</label><span class="alc-unit"><input type="number" min="0" id="alc_prep" name="alc_prep" value="<?php echo esc_attr( alc_field( $post, 'alc_prep' ) ); ?>" placeholder="10"> minutes</span></div>
			<div><label class="alc-label" for="alc_cook">Cook time</label><span class="alc-unit"><input type="number" min="0" id="alc_cook" name="alc_cook" value="<?php echo esc_attr( alc_field( $post, 'alc_cook' ) ); ?>" placeholder="20"> minutes</span></div>
			<div><label class="alc-label" for="alc_serves">Serves</label><span class="alc-unit"><input type="number" min="1" id="alc_serves" name="alc_serves" value="<?php echo esc_attr( alc_field( $post, 'alc_serves', 4 ) ); ?>"> people</span></div>
		</div>

		<label class="alc-label" for="alc_ingredients">Ingredients</label>
		<p class="alc-help">One ingredient per line, starting with the amount, e.g. <code>300g dried pasta</code> or <code>1 white onion, chopped</code>. To split into groups, add a heading line ending with a colon, e.g. <code>Creamy Pesto Sauce:</code>. Amounts change automatically when visitors change the number of servings.</p>
		<textarea id="alc_ingredients" name="alc_ingredients" rows="12" class="alc-mono" placeholder="Pasta &amp; Chicken:&#10;300g dried pasta&#10;1 tbsp olive oil&#10;500g chicken breasts, diced&#10;&#10;Sauce:&#10;50g butter&#10;½ jar green pesto"><?php echo esc_textarea( alc_field( $post, 'alc_ingredients' ) ); ?></textarea>

		<label class="alc-label" for="alc_method">Method</label>
		<p class="alc-help">One step per line, in order (no need to number them). Each line becomes one screen in Cook Mode. To add a timer to a step, put the time in square brackets at the end, e.g. <code>Simmer until thickened. [10 min]</code></p>
		<textarea id="alc_method" name="alc_method" rows="10" placeholder="Bring a large pan of salted water to the boil and cook the pasta.&#10;Heat the oil in a frying pan and add the chicken.&#10;Stir in the flour and cook for 1 minute. [1 min]"><?php echo esc_textarea( alc_field( $post, 'alc_method' ) ); ?></textarea>

		<label class="alc-label" for="alc_tips">Tips &amp; storage <span class="alc-optional">optional</span></label>
		<p class="alc-help">One tip per line, as <code>Heading: your tip</code>, e.g. <code>Freezing: Freeze in portions for up to 3 months.</code></p>
		<textarea id="alc_tips" name="alc_tips" rows="5" placeholder="Make ahead: Chop everything the night before.&#10;Leftovers: Keep in the fridge for up to 2 days."><?php echo esc_textarea( alc_field( $post, 'alc_tips' ) ); ?></textarea>

		<div class="alc-row alc-row--2">
			<div>
				<label class="alc-label" for="alc_video">YouTube video link <span class="alc-optional">optional</span></label>
				<p class="alc-help">Paste the link from YouTube’s <em>Share</em> button. A “Video” tab appears on the recipe.</p>
				<input type="url" id="alc_video" name="alc_video" value="<?php echo esc_attr( alc_field( $post, 'alc_video' ) ); ?>" placeholder="https://youtu.be/...">
			</div>
			<div>
				<label class="alc-label" for="alc_video_title">Video title <span class="alc-optional">optional</span></label>
				<p class="alc-help">Used for screen readers and Google. Leave blank to use the recipe name.</p>
				<input type="text" id="alc_video_title" name="alc_video_title" value="<?php echo esc_attr( alc_field( $post, 'alc_video_title' ) ); ?>">
			</div>
		</div>

		<label class="alc-label" for="alc_keywords">Extra search words <span class="alc-optional">optional</span></label>
		<p class="alc-help">Words people might search for that aren’t already in the recipe, e.g. <code>kids, freezer friendly, budget</code>.</p>
		<input type="text" id="alc_keywords" name="alc_keywords" value="<?php echo esc_attr( alc_field( $post, 'alc_keywords' ) ); ?>">

		<label class="alc-check"><input type="checkbox" name="alc_new" value="1" <?php checked( alc_field( $post, 'alc_new' ), '1' ); ?>> Show a <strong>“New”</strong> badge on this recipe</label>
	</div>
	<?php
}

function alc_recipe_preview_box( $post ) {
	$ing    = alc_parse_ingredients( alc_field( $post, 'alc_ingredients' ) );
	$method = alc_parse_method( alc_field( $post, 'alc_method' ) );
	if ( ! $ing && ! $method['steps'] ) {
		echo '<p class="alc-help">Fill in the ingredients and method, then click <strong>Save Draft</strong> or <strong>Update</strong> to see a preview here.</p>';
		return;
	}
	$count = 0;
	echo '<div class="alc-preview"><div><h4>Ingredients</h4>';
	foreach ( $ing as $g ) {
		if ( $g['group'] ) {
			echo '<p class="alc-group">' . esc_html( $g['group'] ) . '</p>';
		}
		echo '<ul>';
		foreach ( $g['items'] as $it ) {
			$count++;
			if ( null === $it[0] ) {
				echo '<li>' . esc_html( $it[2] ) . ' <span class="alc-note">(no amount – won’t change with servings)</span></li>';
			} else {
				echo '<li><strong>' . esc_html( rtrim( rtrim( number_format( $it[0], 2, '.', '' ), '0' ), '.' ) . ( $it[1] ? ( in_array( $it[1], array( 'g', 'ml', 'kg', 'l' ), true ) ? '' : ' ' ) . $it[1] : '' ) ) . '</strong> ' . esc_html( $it[2] ) . '</li>';
			}
		}
		echo '</ul>';
	}
	echo '</div><div><h4>Cook Mode steps</h4><ol>';
	foreach ( $method['steps'] as $i => $s ) {
		$t = isset( $method['timers']->{$i + 1} ) ? $method['timers']->{$i + 1} : 0;
		echo '<li>' . esc_html( $s ) . ( $t ? ' <span class="alc-timer">⏱ ' . esc_html( $t ) . ' min timer</span>' : '' ) . '</li>';
	}
	echo '</ol></div></div>';
	printf( '<p class="alc-ok">✔ %d ingredients and %d steps ready for the recipe card and Cook Mode.</p>', (int) $count, count( $method['steps'] ) );
}

function alc_recipe_help_box() {
	?>
	<ol class="alc-steps">
		<li>Type the <strong>recipe name</strong> at the top.</li>
		<li>Fill in the <strong>Recipe details</strong> boxes.</li>
		<li>Tick one or more <strong>Collections</strong> (below).</li>
		<li>Choose a <strong>recipe photo</strong>.</li>
		<li>Click <strong>Publish</strong>. Done!</li>
	</ol>
	<p>The newest recipe appears on the homepage automatically, and Cook Mode is created for you.</p>
	<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=alc-guide' ) ); ?>">Open the full website guide →</a></p>
	<?php
}

add_action( 'save_post_recipe', 'alc_save_recipe', 10, 2 );
function alc_save_recipe( $post_id, $post ) {
	if ( ! isset( $_POST['alc_recipe_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alc_recipe_nonce'] ) ), 'alc_save_recipe' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( alc_recipe_fields() as $key => $type ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		switch ( $type ) {
			case 'int':
				$val = '' === $raw ? '' : absint( $raw );
				break;
			case 'url':
				$val = esc_url_raw( $raw );
				break;
			case 'bool':
				$val = $raw ? '1' : '';
				break;
			case 'textarea':
				$val = sanitize_textarea_field( $raw );
				break;
			default:
				$val = sanitize_text_field( $raw );
		}
		update_post_meta( $post_id, $key, $val );
	}
}
