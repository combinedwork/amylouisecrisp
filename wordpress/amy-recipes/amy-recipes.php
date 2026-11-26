<?php
/**
 * Plugin Name: Amy's Recipes
 * Description: Recipes, collections, the festive season and Cook Mode for Amy Louise Crisp. Add a recipe by filling in a simple form – no code needed.
 * Version: 1.0.0
 * Author: Amy Louise Crisp
 * Text Domain: amy-recipes
 * Requires at least: 6.3
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALC_RECIPES_VERSION', '1.0.0' );
define( 'ALC_RECIPES_FILE', __FILE__ );
define( 'ALC_RECIPES_DIR', plugin_dir_path( __FILE__ ) );
define( 'ALC_RECIPES_URL', plugin_dir_url( __FILE__ ) );

require_once ALC_RECIPES_DIR . 'includes/parse.php';
require_once ALC_RECIPES_DIR . 'includes/content-types.php';
require_once ALC_RECIPES_DIR . 'includes/recipe-editor.php';
require_once ALC_RECIPES_DIR . 'includes/collections.php';
require_once ALC_RECIPES_DIR . 'includes/settings.php';
require_once ALC_RECIPES_DIR . 'includes/data.php';
require_once ALC_RECIPES_DIR . 'includes/guide.php';
require_once ALC_RECIPES_DIR . 'includes/setup.php';

register_activation_hook( __FILE__, 'alc_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/** The Amy Louise Crisp theme doesn't use widgets: hide that part of the Customiser to keep it simple.
    (Set here because the Customiser is prepared before the theme loads.) */
add_filter( 'customize_loaded_components', function ( $components ) {
	return 'amy-louise-crisp' === get_option( 'stylesheet' ) ? array_diff( $components, array( 'widgets' ) ) : $components;
} );
