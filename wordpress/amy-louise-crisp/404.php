<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
  <div class="page-hero"><div class="wrap"><h1>Oops, that page has wandered off</h1><p>It may have moved. Try the <a href="<?php echo esc_url( alc_recipes_url() ); ?>">recipes page</a> or <a href="<?php echo esc_url( home_url( '/' ) ); ?>">go home</a>.</p></div></div>
<?php get_footer();
