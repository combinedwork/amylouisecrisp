<?php
/**
 * "Website Guide" page and dashboard shortcuts, so Amy never has to hunt for anything.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alc_links() {
	$customize = function ( $section ) {
		return admin_url( 'customize.php?autofocus[section]=' . $section );
	};
	$about = get_page_by_path( 'about' );
	return array(
		'add'      => array( '➕ Add a new recipe', admin_url( 'post-new.php?post_type=recipe' ) ),
		'recipes'  => array( '📖 Edit my recipes', admin_url( 'edit.php?post_type=recipe' ) ),
		'cols'     => array( '🗂️ Collections & their photos', admin_url( 'edit-tags.php?taxonomy=recipe_collection&post_type=recipe' ) ),
		'season'   => array( '🎄 Change the festive season', admin_url( 'edit.php?post_type=recipe&page=alc-season' ) ),
		'home'     => array( '🏠 Homepage words & photos', $customize( 'alc_home_hero' ) ),
		'slides'   => array( '🖼️ Homepage slideshow', $customize( 'alc_home_slides' ) ),
		'video'    => array( '▶️ Watch & Cook Along video', $customize( 'alc_home_video' ) ),
		'about'    => array( '👋 Edit my About page', $about ? get_edit_post_link( $about->ID, 'raw' ) : admin_url( 'edit.php?post_type=page' ) ),
		'news'     => array( '💌 Newsletter sign-up', $customize( 'alc_newsletter' ) ),
		'social'   => array( '📱 Social media links', $customize( 'alc_social' ) ),
		'logo'     => array( '✨ Logo, fonts & site name', $customize( 'title_tagline' ) ),
		'menu'     => array( '🧭 Menus', admin_url( 'nav-menus.php' ) ),
		'media'    => array( '📷 All my photos', admin_url( 'upload.php' ) ),
		'ads'      => array( '💷 Advertising', $customize( 'alc_ads' ) ),
		'guide'    => array( '❓ Website guide', admin_url( 'admin.php?page=alc-guide' ) ),
	);
}

add_action( 'admin_menu', function () {
	add_menu_page( 'Website Guide', 'Website Guide', 'edit_posts', 'alc-guide', 'alc_guide_page', 'dashicons-heart', 3 );
} );

add_action( 'wp_dashboard_setup', function () {
	wp_add_dashboard_widget( 'alc_dashboard', 'Your website', function () {
		echo '<div class="alc-quick">';
		foreach ( alc_links() as $l ) {
			printf( '<a class="button" href="%s">%s</a>', esc_url( $l[1] ), esc_html( $l[0] ) );
		}
		echo '</div>';
	} );
	// Put it first.
	global $wp_meta_boxes;
	$dash = $wp_meta_boxes['dashboard']['normal']['core'];
	$mine = array( 'alc_dashboard' => $dash['alc_dashboard'] );
	unset( $dash['alc_dashboard'] );
	$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $mine, $dash ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
} );

add_action( 'admin_head-index.php', function () {
	echo '<style>.alc-quick{display:grid;grid-template-columns:1fr 1fr;gap:8px}.alc-quick .button{text-align:left;padding:6px 10px;height:auto;white-space:normal}</style>';
} );

function alc_guide_page() {
	$l = alc_links();
	$a = function ( $k ) use ( $l ) {
		return '<a href="' . esc_url( $l[ $k ][1] ) . '">' . esc_html( $l[ $k ][0] ) . '</a>';
	};
	?>
	<div class="wrap alc-guide">
		<h1>💕 Your Website Guide</h1>
		<p class="alc-lead">Everything on your website can be changed from here. Click a button to go straight there.</p>
		<div class="alc-quick-grid"><?php foreach ( $l as $k => $x ) { echo $a( $k ); } // phpcs:ignore ?></div>

		<div class="alc-card">
			<h2>➕ Add a new recipe</h2>
			<ol>
				<li>Go to <?php echo $a( 'add' ); // phpcs:ignore ?>.</li>
				<li>Type the <strong>recipe name</strong> at the top.</li>
				<li>Fill in <strong>Recipe details</strong>: description, times, servings, ingredients (one per line), method (one step per line), tips and an optional YouTube link.</li>
				<li>Tick the <strong>Collections</strong> it belongs to (on the right). It can be in more than one.</li>
				<li>Click <strong>Choose recipe photo</strong> (on the right) and upload or pick a photo.</li>
				<li>Click <strong>Publish</strong>.</li>
			</ol>
			<p>That’s it. The recipe gets its own page, appears on the Recipes page, in search and the mood picker, and the newest recipe shows on the homepage. Cook Mode, the servings changer and the ingredient checklist are all created for you.</p>
			<h3>Handy tips for the recipe boxes</h3>
			<ul>
				<li><strong>Ingredients:</strong> start each line with the amount, e.g. <code>300g pasta</code>, <code>2 tbsp olive oil</code>, <code>½ jar pesto</code>. Lines without an amount (like “salt and pepper”) are fine too.</li>
				<li><strong>Groups:</strong> a line ending with a colon starts a new group, e.g. <code>For the sauce:</code></li>
				<li><strong>Timers:</strong> add <code>[10 min]</code> at the end of a method step and Cook Mode will have a 10-minute timer ready on that step.</li>
				<li><strong>Check it:</strong> after saving, the box <em>“How your recipe will look in Cook Mode”</em> shows exactly how it was read.</li>
				<li><strong>Drafts:</strong> click <em>Save Draft</em> to finish later. Use <em>Preview</em> to see it before publishing.</li>
			</ul>
		</div>

		<div class="alc-card">
			<h2>📷 Changing photos</h2>
			<ul>
				<li><strong>A recipe photo:</strong> open the recipe in <?php echo $a( 'recipes' ); // phpcs:ignore ?>, click the photo on the right, choose a new one, then <em>Update</em>.</li>
				<li><strong>A collection photo</strong> (the tiles like “Baking”): <?php echo $a( 'cols' ); // phpcs:ignore ?> → hover a collection → <em>Edit</em> → <em>Change photo</em> → <em>Update</em>.</li>
				<li><strong>Homepage slideshow:</strong> <?php echo $a( 'slides' ); // phpcs:ignore ?>. Each slide has a photo, wording and a link.</li>
				<li><strong>Homepage “About me” photo and video thumbnail:</strong> <?php echo $a( 'home' ); // phpcs:ignore ?>.</li>
				<li><strong>About page photos:</strong> <?php echo $a( 'about' ); // phpcs:ignore ?>, click a photo and choose <em>Replace</em>.</li>
				<li><strong>Best photo sizes:</strong> landscape photos around 1600 × 1100 pixels work best for recipes and slides; square photos for collections. Photos straight from a phone are fine – WordPress resizes them.</li>
			</ul>
		</div>

		<div class="alc-card">
			<h2>🎄 Festive Favourites through the year</h2>
			<p>Go to <?php echo $a( 'season' ); // phpcs:ignore ?>, click a season (Christmas, Easter, Summer, Halloween…), change any wording, and click <em>Save changes</em>. Then tick <strong>Festive Favourites</strong> on the recipes you want in it. Untick old ones when the season ends.</p>
		</div>

		<div class="alc-card">
			<h2>🏠 Homepage, menus and everything else</h2>
			<ul>
				<li><strong>Homepage words:</strong> <?php echo $a( 'home' ); // phpcs:ignore ?>. You see changes live before clicking <em>Publish</em>.</li>
				<li><strong>Watch &amp; Cook Along video:</strong> <?php echo $a( 'video' ); // phpcs:ignore ?>. Paste a new YouTube link.</li>
				<li><strong>Mood picker buttons:</strong> on the <?php echo $a( 'season' ); // phpcs:ignore ?> page.</li>
				<li><strong>Menus:</strong> <?php echo $a( 'menu' ); // phpcs:ignore ?>. Drag items to reorder; add pages or collections from the left.</li>
				<li><strong>Logo:</strong> <?php echo $a( 'logo' ); // phpcs:ignore ?> → <em>Select logo</em>.</li>
				<li><strong>Newsletter:</strong> <?php echo $a( 'news' ); // phpcs:ignore ?>. Paste the sign-up form code from your newsletter service (e.g. MailerLite or Kit).</li>
				<li><strong>Social links:</strong> <?php echo $a( 'social' ); // phpcs:ignore ?>.</li>
				<li><strong>Other pages</strong> (Privacy Policy, Contact…): Pages → Add New, then add them to a menu.</li>
			</ul>
		</div>

		<div class="alc-card">
			<h2>💷 Advertising (when you’re ready)</h2>
			<p>Nothing shows until you switch it on. When an ad network (e.g. Mediavine, Raptive or Google AdSense) accepts you, go to <?php echo $a( 'ads' ); // phpcs:ignore ?>, paste their <strong>head code</strong> and <strong>ads.txt</strong> lines, tick <strong>Turn on adverts</strong> and click <em>Publish</em>. To see where the optional ad spaces are, add <code>?ads=preview</code> to the end of any page address while logged in. Cook Mode never shows adverts. Remember a cookie consent banner (most networks provide one).</p>
		</div>

		<div class="alc-card">
			<h2>🛟 If something goes wrong</h2>
			<ul>
				<li>Every recipe and page keeps <strong>Revisions</strong>: open it and look for “Revisions” on the right to go back to an earlier version.</li>
				<li>In the Customiser, nothing changes on the live site until you click <strong>Publish</strong>. Close it to cancel.</li>
				<li>A recipe looks odd? Check the <em>“How your recipe will look in Cook Mode”</em> box. Usually a line just needs its amount at the start.</li>
			</ul>
		</div>
	</div>
	<?php
}
