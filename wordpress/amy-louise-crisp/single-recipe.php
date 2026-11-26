<?php
/** One recipe: recipe card, Cook Mode, video and tips, all built from the recipe form. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
the_post();
$terms = get_the_terms( get_the_ID(), 'recipe_collection' );
$first = $terms && ! is_wp_error( $terms ) ? $terms[0] : null;
?>
  <div class="recipe-top">
    <div class="wrap crumbs" id="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <a href="<?php echo esc_url( alc_recipes_url() ); ?>">Recipes</a><?php if ( $first ) : ?> / <a href="<?php echo esc_url( alc_recipes_url( $first->slug ) ); ?>"><?php echo esc_html( $first->name ); ?></a><?php endif; ?> / <span><?php the_title(); ?></span></div>
  </div>

  <section class="featured recipe-page" id="recipe">
    <div class="wrap"><h1><?php the_title(); ?></h1><p><?php echo esc_html( get_post_meta( get_the_ID(), 'alc_summary', true ) ); ?></p></div>
  </section>

  <section id="more">
    <div class="wrap">
      <div class="sec-head reveal"><div><span class="eyebrow">You might also like</span><h2 id="moreTitle">More recipes</h2></div><a href="<?php echo esc_url( alc_recipes_url() ); ?>" class="more" id="moreLink">See all →</a></div>
      <div class="grid" id="moreGrid"></div>
    </div>
  </section>
<?php
get_footer();
