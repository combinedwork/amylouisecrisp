<?php
/** The Recipes page: search, filters by collection, saved recipes. Built from the recipes you add. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
  <div class="page-hero">
    <div class="wrap">
      <div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <span id="crumb">Recipes</span></div>
      <span class="season-badge" id="seasonBadge" hidden></span>
      <h1 id="pageTitle">All Recipes</h1>
      <p id="pageSub"></p>
    </div>
  </div>

  <section style="padding-top:0">
    <div class="wrap">
      <div class="toolbar">
        <label class="find"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--sage-d)" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input id="find" placeholder="Search by name or ingredient…" aria-label="Search recipes"></label>
        <select id="sort" aria-label="Sort recipes">
          <option value="new">Newest first</option>
          <option value="quick">Quickest first</option>
          <option value="az">A–Z</option>
        </select>
        <div class="view-toggle" role="group" aria-label="View">
          <button class="on" data-view="grid" aria-label="Grid view"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg></button>
          <button data-view="list" aria-label="List view"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="4" width="18" height="4" rx="1.5"/><rect x="3" y="10" width="18" height="4" rx="1.5"/><rect x="3" y="16" width="18" height="4" rx="1.5"/></svg></button>
        </div>
      </div>
      <div class="filters" id="filters"></div>
      <div class="cat-intro" id="catIntro"><div class="th" id="ciTh"></div><div><h3 id="ciTitle"></h3><p id="ciText"></p></div></div>
      <p class="result-count" id="count" aria-live="polite"></p>
      <div class="grid" id="grid">
        <?php /* Plain list for search engines and if JavaScript is off; replaced by the interactive grid. */
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
          <div class="card in"><a href="<?php the_permalink(); ?>"><div class="ph"><?php the_post_thumbnail( 'large' ); ?></div><h3><?php the_title(); ?></h3></a></div>
        <?php endwhile; endif; ?>
      </div>
      <div class="empty" id="empty"><b class="serif" style="font-size:22px">Nothing here yet</b><p style="margin:6px 0 12px" id="emptyText"></p><button class="btn" id="clearBtn">Show all recipes</button></div>
    </div>
  </section>

  <section style="padding-top:0">
    <div class="wrap">
      <div class="sec-head reveal"><div><span class="eyebrow">Explore</span><h2>Browse by Category</h2></div></div>
      <div class="cat-strip" data-cat-strip></div>
    </div>
  </section>
<?php
get_footer();
