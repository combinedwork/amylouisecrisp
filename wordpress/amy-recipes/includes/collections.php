<?php
/**
 * Collections (recipe categories): photo, where they appear on the homepage, and order.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alc_collection_groups() {
	return array(
		'main'     => 'Main – one of the “Find Something Delicious” tiles',
		'more'     => 'Extra – the “Something a little different” cards',
		'seasonal' => 'Seasonal – Festive Favourites (uses the festive season settings)',
		'tag'      => 'Filter only – appears as a filter on the Recipes page',
	);
}

function alc_collection_fields_html( $term = null ) {
	$img   = $term ? (int) get_term_meta( $term->term_id, 'alc_image', true ) : 0;
	$group = $term ? get_term_meta( $term->term_id, 'alc_group', true ) : 'main';
	$order = $term ? get_term_meta( $term->term_id, 'alc_order', true ) : '';
	$src   = $img ? wp_get_attachment_image_url( $img, 'medium' ) : '';
	ob_start();
	?>
	<div class="alc-image-field">
		<input type="hidden" name="alc_image" value="<?php echo esc_attr( $img ? $img : '' ); ?>">
		<img src="<?php echo esc_url( $src ); ?>" alt="" class="alc-image-preview" <?php echo $src ? '' : 'hidden'; ?>>
		<p>
			<button type="button" class="button alc-image-choose"><?php echo $src ? 'Change photo' : 'Choose photo'; ?></button>
			<button type="button" class="button-link alc-image-remove" <?php echo $src ? '' : 'hidden'; ?>>Remove photo</button>
		</p>
	</div>
	<?php
	$image_html = ob_get_clean();
	$select     = '<select name="alc_group" id="alc_group">';
	foreach ( alc_collection_groups() as $k => $label ) {
		$select .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $group ? $group : 'main', $k, false ), esc_html( $label ) );
	}
	$select .= '</select>';
	return array(
		'image' => $image_html,
		'group' => $select,
		'order' => '<input type="number" name="alc_order" id="alc_order" min="0" style="width:90px" value="' . esc_attr( $order ) . '">',
	);
}

add_action( 'recipe_collection_add_form_fields', function () {
	$f = alc_collection_fields_html();
	?>
	<div class="form-field"><label>Collection photo</label><?php echo $f['image']; // phpcs:ignore ?><p>Shown on the homepage tile and the Recipes page.</p></div>
	<div class="form-field"><label for="alc_group">Where it appears</label><?php echo $f['group']; // phpcs:ignore ?></div>
	<div class="form-field"><label for="alc_order">Position</label><?php echo $f['order']; // phpcs:ignore ?><p>Lower numbers appear first.</p></div>
	<?php
} );

add_action( 'recipe_collection_edit_form_fields', function ( $term ) {
	$f = alc_collection_fields_html( $term );
	?>
	<tr class="form-field"><th scope="row">Collection photo</th><td><?php echo $f['image']; // phpcs:ignore ?><p class="description">Shown on the homepage tile and the Recipes page.</p></td></tr>
	<tr class="form-field"><th scope="row"><label for="alc_group">Where it appears</label></th><td><?php echo $f['group']; // phpcs:ignore ?></td></tr>
	<tr class="form-field"><th scope="row"><label for="alc_order">Position</label></th><td><?php echo $f['order']; // phpcs:ignore ?><p class="description">Lower numbers appear first.</p></td></tr>
	<?php
} );

function alc_save_collection_meta( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- WordPress checks the nonce for the term screens.
	if ( isset( $_POST['alc_image'] ) ) {
		update_term_meta( $term_id, 'alc_image', absint( $_POST['alc_image'] ) );
	}
	if ( isset( $_POST['alc_group'] ) && array_key_exists( sanitize_key( $_POST['alc_group'] ), alc_collection_groups() ) ) {
		update_term_meta( $term_id, 'alc_group', sanitize_key( $_POST['alc_group'] ) );
	}
	if ( isset( $_POST['alc_order'] ) ) {
		update_term_meta( $term_id, 'alc_order', '' === $_POST['alc_order'] ? '' : absint( $_POST['alc_order'] ) );
	}
	// phpcs:enable
}
add_action( 'created_recipe_collection', 'alc_save_collection_meta' );
add_action( 'edited_recipe_collection', 'alc_save_collection_meta' );

/** Friendlier wording on the Collections screen. */
add_filter( 'manage_edit-recipe_collection_columns', function ( $cols ) {
	unset( $cols['description'], $cols['slug'] );
	$out = array( 'cb' => $cols['cb'], 'alc_photo' => 'Photo' );
	unset( $cols['cb'] );
	$out          = array_merge( $out, $cols );
	$out['alc_group'] = 'Where it appears';
	return $out;
} );
add_filter( 'manage_recipe_collection_custom_column', function ( $out, $col, $term_id ) {
	if ( 'alc_photo' === $col ) {
		$img = (int) get_term_meta( $term_id, 'alc_image', true );
		return $img ? wp_get_attachment_image( $img, array( 60, 60 ), false, array( 'style' => 'width:60px;height:60px;object-fit:cover;border-radius:6px' ) ) : '<span style="color:#8c8f94">No photo</span>';
	}
	if ( 'alc_group' === $col ) {
		$g      = get_term_meta( $term_id, 'alc_group', true );
		$groups = alc_collection_groups();
		return esc_html( strtok( $groups[ $g ? $g : 'main' ], '–' ) );
	}
	return $out;
}, 10, 3 );

/** Explain the description box. */
add_action( 'recipe_collection_pre_add_form', function () {
	echo '<p class="alc-tip">Each collection is a group of recipes, like <em>Fakeaways</em> or <em>Baking</em>. The <strong>Description</strong> is the short line shown under its name on the website.</p>';
} );
