<?php
/**
 * Recipes → Festive Season & Moods: the seasonal collection and the "What are you in the mood for?" buttons.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alc_season_defaults() {
	return array(
		'name'    => 'Christmas',
		'heading' => 'Christmas is coming!',
		'text'    => 'Festive bakes, party food and make-ahead family dinners to take the stress out of the big day.',
		'emoji'   => '🎄',
	);
}

function alc_season_presets() {
	return array(
		'Christmas'       => array( '🎄', 'Christmas is coming!', 'Festive bakes, party food and make-ahead family dinners to take the stress out of the big day.' ),
		'Valentine’s Day' => array( '💕', 'Made with love', 'Sweet treats and cosy dinners for the people you love most.' ),
		'Easter'          => array( '🐣', 'Hoppy Easter!', 'Chocolatey bakes, Easter lunch ideas and treats to make with the kids.' ),
		'Summer'          => array( '☀️', 'Hello summer!', 'Picnics, barbecues and light, easy dinners for long sunny evenings.' ),
		'Back to School'  => array( '🎒', 'Back to school', 'Lunchbox fillers and quick dinners for busy school nights.' ),
		'Halloween'       => array( '🎃', 'Spooky season', 'Fun, frightful bakes and party food the kids will love.' ),
		'Bonfire Night'   => array( '🎆', 'Bonfire Night', 'Warming, hand-held food for chilly nights by the fire.' ),
	);
}

function alc_moods_default() {
	return "Something quick = 30 Minute Meals\nComfort food = Easy Family Dinners, Slow Cooker Stash\nFakeaway night = Fakeaways\nLess washing up = One Pan Wonders\nA sweet treat = Baking\nSchool lunches = Lunchbox Line-Up\nSomething festive = Festive Favourites";
}

function alc_get_season() {
	return wp_parse_args( (array) get_option( 'alc_season', array() ), alc_season_defaults() );
}

add_action( 'admin_menu', function () {
	add_submenu_page( 'edit.php?post_type=recipe', 'Festive Season & Moods', 'Festive Season & Moods', 'edit_posts', 'alc-season', 'alc_settings_page' );
} );

function alc_settings_page() {
	if ( isset( $_POST['alc_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alc_settings_nonce'] ) ), 'alc_settings' ) && current_user_can( 'edit_posts' ) ) {
		update_option( 'alc_season', array(
			'name'    => sanitize_text_field( wp_unslash( $_POST['season_name'] ?? '' ) ),
			'heading' => sanitize_text_field( wp_unslash( $_POST['season_heading'] ?? '' ) ),
			'text'    => sanitize_textarea_field( wp_unslash( $_POST['season_text'] ?? '' ) ),
			'emoji'   => sanitize_text_field( wp_unslash( $_POST['season_emoji'] ?? '' ) ),
		) );
		update_option( 'alc_moods', sanitize_textarea_field( wp_unslash( $_POST['moods'] ?? '' ) ) );
		echo '<div class="notice notice-success is-dismissible"><p><strong>Saved!</strong> Your website has been updated.</p></div>';
	}
	$s     = alc_get_season();
	$moods = get_option( 'alc_moods', alc_moods_default() );
	$names = wp_list_pluck( get_terms( array( 'taxonomy' => 'recipe_collection', 'hide_empty' => false ) ), 'name' );
	?>
	<div class="wrap alc-settings">
		<h1>Festive Season &amp; Moods</h1>
		<form method="post">
			<?php wp_nonce_field( 'alc_settings', 'alc_settings_nonce' ); ?>

			<div class="alc-card">
				<h2>🎄 Festive Favourites</h2>
				<p>Change this through the year. It updates the homepage card, the Festive Favourites page and the mood picker. Recipes go in the <strong>Festive Favourites</strong> collection.</p>
				<p><strong>Quick start:</strong> click a season to fill in the boxes, then change any words you like.</p>
				<p class="alc-presets">
					<?php foreach ( alc_season_presets() as $name => $p ) : ?>
						<button type="button" class="button alc-preset" data-name="<?php echo esc_attr( $name ); ?>" data-emoji="<?php echo esc_attr( $p[0] ); ?>" data-heading="<?php echo esc_attr( $p[1] ); ?>" data-text="<?php echo esc_attr( $p[2] ); ?>"><?php echo esc_html( $p[0] . ' ' . $name ); ?></button>
					<?php endforeach; ?>
				</p>
				<table class="form-table" role="presentation">
					<tr><th><label for="season_name">Season name</label></th><td><input class="regular-text" id="season_name" name="season_name" value="<?php echo esc_attr( $s['name'] ); ?>"><p class="description">e.g. Christmas, Easter, Summer, Halloween</p></td></tr>
					<tr><th><label for="season_heading">Heading</label></th><td><input class="regular-text" id="season_heading" name="season_heading" value="<?php echo esc_attr( $s['heading'] ); ?>"></td></tr>
					<tr><th><label for="season_text">Short description</label></th><td><textarea class="large-text" rows="3" id="season_text" name="season_text"><?php echo esc_textarea( $s['text'] ); ?></textarea></td></tr>
					<tr><th><label for="season_emoji">Picture</label></th><td><input id="season_emoji" name="season_emoji" value="<?php echo esc_attr( $s['emoji'] ); ?>" style="width:70px;font-size:22px;text-align:center"><p class="description">An emoji shown on the Festive card, e.g. 🎄 🐣 ☀️ 🎃</p></td></tr>
				</table>
			</div>

			<div class="alc-card">
				<h2>😋 “What are you in the mood for?”</h2>
				<p>One button per line: the button wording, an equals sign, then the collection(s) it should suggest recipes from.</p>
				<textarea name="moods" rows="9" class="large-text code"><?php echo esc_textarea( $moods ); ?></textarea>
				<p class="description">Your collections: <?php echo esc_html( implode( ', ', $names ) ); ?></p>
			</div>

			<?php submit_button( 'Save changes' ); ?>
		</form>
	</div>
	<?php
}

/** Mood lines → [ ['label' => ..., 'match' => [slugs]] ] */
function alc_get_moods() {
	$terms   = get_terms( array( 'taxonomy' => 'recipe_collection', 'hide_empty' => false ) );
	$by_name = array();
	foreach ( $terms as $t ) {
		$by_name[ alc_norm( $t->name ) ] = $t->slug;
		$by_name[ alc_norm( $t->slug ) ] = $t->slug;
	}
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) get_option( 'alc_moods', alc_moods_default() ) ) as $line ) {
		if ( false === strpos( $line, '=' ) ) {
			continue;
		}
		list( $label, $cols ) = array_map( 'trim', explode( '=', $line, 2 ) );
		$match = array();
		foreach ( explode( ',', $cols ) as $c ) {
			$k = alc_norm( $c );
			if ( isset( $by_name[ $k ] ) ) {
				$match[] = $by_name[ $k ];
			}
		}
		if ( $label && $match ) {
			$out[] = array( 'label' => $label, 'match' => $match );
		}
	}
	return $out;
}

/** Lower-case, ignore dashes/apostrophes, so "Lunchbox Line-Up" matches "lunchbox line up". */
function alc_norm( $s ) {
	return trim( preg_replace( '/[^a-z0-9]+/', ' ', strtolower( remove_accents( (string) $s ) ) ) );
}
