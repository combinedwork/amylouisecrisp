<?php
/** Ordinary pages such as About Me. Edit them in Pages with the normal WordPress editor. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); ?>
  <div class="page-hero page-hero--slim">
    <div class="wrap">
      <div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <span><?php the_title(); ?></span></div>
      <h1><?php the_title(); ?></h1>
    </div>
  </div>
  <section class="page-body">
    <div class="wrap entry-content">
      <?php the_content(); ?>
    </div>
  </section>
<?php endwhile;
get_footer();
