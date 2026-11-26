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
    <?php $r = function_exists( 'alc_recipe_data' ) ? alc_recipe_data( get_the_ID() ) : null; ?>
    <div class="wrap recipe-static">
      <h1><?php the_title(); ?></h1>
      <?php if ( $r ) : ?>
        <?php if ( $r['image'] ) : ?><img src="<?php echo esc_url( $r['image'] ); ?>" alt="<?php the_title_attribute(); ?>" style="max-width:520px;border-radius:10px"><?php endif; ?>
        <p><?php echo esc_html( $r['summary'] ); ?></p>
        <p>Prep <?php echo (int) $r['prep']; ?> mins · Cook <?php echo (int) $r['cook']; ?> mins · Serves <?php echo (int) $r['serves']; ?></p>
        <h2>Ingredients</h2>
        <?php foreach ( $r['ingredients'] as $g ) : ?>
          <?php if ( $g['group'] ) : ?><h3><?php echo esc_html( $g['group'] ); ?></h3><?php endif; ?>
          <ul><?php foreach ( $g['items'] as $it ) : ?><li><?php echo esc_html( trim( ( null === $it[0] ? '' : rtrim( rtrim( number_format( $it[0], 2, '.', '' ), '0' ), '.' ) . ( $it[1] ? ( in_array( $it[1], array( 'g', 'ml', 'kg', 'l' ), true ) ? '' : ' ' ) . $it[1] : '' ) . ' ' ) . $it[2] ) ); ?></li><?php endforeach; ?></ul>
        <?php endforeach; ?>
        <h2>Method</h2>
        <ol><?php foreach ( $r['method'] as $s ) : ?><li><?php echo esc_html( $s ); ?></li><?php endforeach; ?></ol>
        <?php if ( $r['tips'] ) : ?><h2>Tips &amp; storage</h2><?php foreach ( $r['tips'] as $t ) : ?><p><strong><?php echo esc_html( $t[0] ); ?>:</strong> <?php echo esc_html( $t[1] ); ?></p><?php endforeach; ?><?php endif; ?>
      <?php endif; ?>
    </div>
  </section>

  <div class="wrap"><?php alc_ad( 'recipe_after' ); ?></div>

  <section id="more">
    <div class="wrap">
      <div class="sec-head reveal"><div><span class="eyebrow">You might also like</span><h2 id="moreTitle">More recipes</h2></div><a href="<?php echo esc_url( alc_recipes_url() ); ?>" class="more" id="moreLink">See all →</a></div>
      <div class="grid" id="moreGrid"></div>
    </div>
  </section>
  <div class="wrap"><?php alc_ad( 'recipe_more' ); ?></div>
<?php
get_footer();
