<?php
/**
 * Homepage. Words and photos: Appearance → Customise → Homepage.
 * Categories, recipes and the mood picker come from the Recipes section.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$latest = post_type_exists( 'recipe' ) ? get_posts( array( 'post_type' => 'recipe', 'numberposts' => 1 ) ) : array();
$latest = $latest ? $latest[0] : null;

// Slides: anything left blank falls back to something sensible
$auto = array(
	1 => array( 'title' => $latest ? get_the_title( $latest ) : 'Recipes', 'sub' => $latest ? 'New recipe · ready in ' . ( (int) get_post_meta( $latest->ID, 'alc_prep', true ) + (int) get_post_meta( $latest->ID, 'alc_cook', true ) ) . ' mins' : 'Family recipes you’ll actually make', 'link' => $latest ? get_permalink( $latest ) : alc_recipes_url(), 'dot' => $latest ? (string) ( (int) get_post_meta( $latest->ID, 'alc_prep', true ) + (int) get_post_meta( $latest->ID, 'alc_cook', true ) ) : '' ),
	2 => array( 'link' => alc_recipes_url( 'fakeaways' ) ),
	3 => array( 'link' => alc_recipes_url( 'baking' ) ),
	4 => array( 'link' => alc_about_url() ),
);
$slides = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$img = alc_mod( "slide{$i}_image" );
	if ( ! $img ) {
		continue;
	}
	$custom   = alc_mod( "slide{$i}_title" );
	$slides[] = array(
		'img'   => $img,
		'title' => $custom ? $custom : ( $auto[ $i ]['title'] ?? '' ),
		'sub'   => alc_mod( "slide{$i}_sub" ) ? alc_mod( "slide{$i}_sub" ) : ( $custom ? '' : ( $auto[ $i ]['sub'] ?? '' ) ),
		'link'  => alc_mod( "slide{$i}_link" ) ? alc_mod( "slide{$i}_link" ) : $auto[ $i ]['link'],
		'dot'   => $custom ? '' : ( $auto[ $i ]['dot'] ?? '' ),
	);
}
$yt      = function_exists( 'alc_youtube_id' ) ? alc_youtube_id( alc_mod( 'video_url' ) ) : '';
$vrecipe = (int) alc_mod( 'video_recipe' );
$vlink   = $vrecipe && get_post( $vrecipe ) ? get_permalink( $vrecipe ) : ( $latest ? get_permalink( $latest ) : alc_recipes_url() );
?>
  <div class="hero">
    <div class="wrap hero-grid">
      <div class="reveal">
        <span class="eyebrow"><?php echo esc_html( alc_mod( 'hero_eyebrow' ) ); ?></span>
        <h1><?php echo alc_highlight( alc_mod( 'hero_heading' ) ); // phpcs:ignore ?></h1>
        <?php if ( alc_mod( 'hero_text1' ) ) : ?><p><?php echo esc_html( alc_mod( 'hero_text1' ) ); ?></p><?php endif; ?>
        <?php if ( alc_mod( 'hero_text2' ) ) : ?><p><?php echo esc_html( alc_mod( 'hero_text2' ) ); ?></p><?php endif; ?>
        <div class="cta">
          <a href="<?php echo esc_url( alc_recipes_url() ); ?>" class="btn"><?php echo esc_html( alc_mod( 'hero_btn1' ) ); ?></a>
          <a href="<?php echo esc_url( alc_about_url() ); ?>" class="btn line"><?php echo esc_html( alc_mod( 'hero_btn2' ) ); ?></a>
        </div>
      </div>
      <?php if ( $slides ) : $n = count( $slides ); ?>
      <div class="hero-img reveal carousel" id="carousel" role="region" aria-roledescription="carousel" aria-label="Featured recipes" tabindex="0" data-seconds="<?php echo esc_attr( max( 2, (int) alc_mod( 'slides_seconds' ) ) ); ?>">
        <div class="frame">
          <?php foreach ( $slides as $i => $s ) : ?>
            <div class="slide<?php echo 0 === $i ? ' on' : ''; ?>" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $i + 1 ) . ' of ' . $n ); ?>"<?php echo $i ? ' aria-hidden="true"' : ''; ?> data-title="<?php echo esc_attr( $s['title'] ); ?>" data-sub="<?php echo esc_attr( $s['sub'] ); ?>" data-href="<?php echo esc_url( $s['link'] ); ?>"<?php echo $s['dot'] ? ' data-dot="' . esc_attr( $s['dot'] ) . '"' : ''; ?>><img src="<?php echo esc_url( $s['img'] ); ?>" alt="<?php echo esc_attr( $s['title'] ); ?>"<?php echo $i ? ' loading="lazy"' : ''; ?>></div>
          <?php endforeach; ?>
          <?php if ( $n > 1 ) : ?>
          <button class="car-btn prev" aria-label="Previous slide"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg></button>
          <button class="car-btn next" aria-label="Next slide"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 5l7 7-7 7"/></svg></button>
          <div class="car-dots" role="tablist" aria-label="Choose slide">
            <?php for ( $i = 0; $i < $n; $i++ ) : ?><button role="tab" aria-label="Slide <?php echo (int) $i + 1; ?>"<?php echo 0 === $i ? ' class="on"' : ''; ?>><i></i></button><?php endfor; ?>
          </div>
          <button class="car-pause" aria-label="Pause slideshow"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg></button>
          <?php endif; ?>
        </div>
        <a href="<?php echo esc_url( $slides[0]['link'] ); ?>" class="sticker" id="carSticker" aria-live="polite"><span class="dot"><?php echo esc_html( $slides[0]['dot'] ); ?></span><span class="st-txt"><b><?php echo esc_html( $slides[0]['title'] ); ?></b><?php echo esc_html( $slides[0]['sub'] ); ?></span></a>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="picker">
    <div class="wrap">
      <span class="q"><?php echo esc_html( alc_mod( 'mood_heading' ) ); ?></span>
      <div class="pills" id="moodPills"></div>
      <div class="picker-out" id="moodOut" aria-live="polite"></div>
    </div>
  </div>

  <section id="categories">
    <div class="wrap">
      <div class="sec-head reveal"><div><span class="eyebrow"><?php echo esc_html( alc_mod( 'cats_eyebrow' ) ); ?></span><h2><?php echo esc_html( alc_mod( 'cats_heading' ) ); ?></h2></div><a href="<?php echo esc_url( alc_recipes_url() ); ?>" class="more">All recipes →</a></div>
      <div class="cats" id="mainCats"></div>
    </div>
  </section>

  <section class="collections" id="collections">
    <div class="wrap">
      <div class="sec-head reveal"><div><span class="eyebrow"><?php echo esc_html( alc_mod( 'coll_eyebrow' ) ); ?></span><h2><?php echo esc_html( alc_mod( 'coll_heading' ) ); ?></h2></div></div>
      <div class="coll-grid" id="collGrid"></div>
    </div>
  </section>

  <section class="featured" id="recipe"></section>

  <?php if ( alc_mod( 'video_show' ) && $yt ) : ?>
  <section class="video-sec" id="video">
    <div class="wrap video-grid">
      <div class="video-copy reveal">
        <span class="eyebrow"><?php echo esc_html( alc_mod( 'video_eyebrow' ) ); ?></span>
        <h2><?php echo esc_html( alc_mod( 'video_heading' ) ); ?></h2>
        <p><?php echo esc_html( alc_mod( 'video_text' ) ); ?></p>
        <ul class="video-points">
          <?php foreach ( preg_split( '/\r\n|\r|\n/', (string) alc_mod( 'video_points' ) ) as $line ) : if ( ! trim( $line ) ) { continue; } $p = array_map( 'trim', explode( '|', $line, 2 ) ); ?>
            <li><?php if ( count( $p ) > 1 ) : ?><span><?php echo esc_html( $p[0] ); ?></span><?php echo esc_html( $p[1] ); ?><?php else : ?><span>✓</span><?php echo esc_html( $p[0] ); ?><?php endif; ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="video-cta">
          <a href="<?php echo esc_url( $vlink ); ?>" class="btn">Get the Recipe</a>
          <a href="https://youtu.be/<?php echo esc_attr( $yt ); ?>" target="_blank" rel="noopener" class="btn line" id="ytChannel">Watch on YouTube ↗</a>
        </div>
      </div>
      <div class="video-wrap reveal">
        <div class="video-frame" id="videoFrame" data-yt="<?php echo esc_attr( $yt ); ?>" data-title="<?php echo esc_attr( alc_mod( 'video_title' ) ); ?>" data-img="<?php echo esc_url( alc_mod( 'video_image' ) ); ?>" data-label="Watch me make it" data-sub="<?php echo esc_attr( alc_mod( 'video_title' ) ); ?>"></div>
        <p class="video-note">Video not playing? <a href="https://youtu.be/<?php echo esc_attr( $yt ); ?>" target="_blank" rel="noopener">Watch it on YouTube</a></p>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section id="latest">
    <div class="wrap">
      <div class="sec-head reveal"><div><span class="eyebrow"><?php echo esc_html( alc_mod( 'latest_eyebrow' ) ); ?></span><h2><?php echo esc_html( alc_mod( 'latest_heading' ) ); ?></h2></div><a href="<?php echo esc_url( alc_recipes_url() ); ?>" class="more">See all recipes →</a></div>
      <div class="filters reveal" id="filters"></div>
      <div class="grid" id="grid"></div>
    </div>
  </section>

  <section class="about" id="about">
    <div class="wrap about-grid">
      <div class="ph reveal"><img src="<?php echo esc_url( alc_mod( 'about_image' ) ); ?>" alt="<?php echo esc_attr( alc_mod( 'about_heading' ) ); ?>"></div>
      <div class="reveal">
        <span class="eyebrow"><?php echo esc_html( alc_mod( 'about_eyebrow' ) ); ?></span>
        <h2><?php echo esc_html( alc_mod( 'about_heading' ) ); ?></h2>
        <?php echo wpautop( esc_html( alc_mod( 'about_text' ) ) ); // phpcs:ignore ?>
        <?php if ( alc_mod( 'about_sign' ) ) : ?><div class="sig"><?php echo esc_html( alc_mod( 'about_sign' ) ); ?></div><?php endif; ?>
        <a href="<?php echo esc_url( alc_about_url() ); ?>" class="btn line"><?php echo esc_html( alc_mod( 'about_btn' ) ); ?></a>
      </div>
    </div>
  </section>
<?php
get_footer();
