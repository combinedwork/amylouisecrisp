<?php
/** Blog posts, search results and anything else. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
  <div class="page-hero page-hero--slim">
    <div class="wrap">
      <h1><?php echo is_search() ? esc_html( sprintf( 'Search results for “%s”', get_search_query() ) ) : ( is_home() ? 'Blog' : wp_kses_post( get_the_archive_title() ) ); ?></h1>
    </div>
  </div>
  <section class="page-body">
    <div class="wrap">
      <?php if ( have_posts() ) : ?>
        <div class="grid">
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="card in"><a href="<?php the_permalink(); ?>"><div class="ph"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } ?></div><h3><?php the_title(); ?></h3><span class="meta"><?php echo esc_html( get_the_date() ); ?></span></a></div>
          <?php endwhile; ?>
        </div>
        <div class="pager"><?php the_posts_pagination(); ?></div>
      <?php else : ?>
        <p>Nothing found. Try the <a href="<?php echo esc_url( alc_recipes_url() ); ?>">recipes page</a>.</p>
      <?php endif; ?>
    </div>
  </section>
<?php get_footer();
