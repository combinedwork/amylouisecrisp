<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $embed = trim( (string) alc_mod( 'news_embed' ) ); ?>
<section id="newsletter">
  <div class="wrap">
    <div class="news reveal">
      <div>
        <?php if ( alc_mod( 'news_eyebrow' ) ) : ?><span class="eyebrow"><?php echo esc_html( alc_mod( 'news_eyebrow' ) ); ?></span><?php endif; ?>
        <h2><?php echo esc_html( alc_mod( 'news_heading' ) ); ?></h2>
        <p><?php echo esc_html( alc_mod( 'news_text' ) ); ?></p>
      </div>
      <div id="newsWrap" class="<?php echo $embed ? 'news-embed' : ''; ?>">
        <?php if ( $embed ) : ?>
          <?php echo do_shortcode( $embed ); // phpcs:ignore WordPress.Security.EscapeOutput -- form code pasted by the site owner ?>
        <?php else : ?>
          <form id="newsForm" novalidate>
            <input type="email" id="email" placeholder="Your email address" aria-label="Email address" autocomplete="email">
            <button class="btn" type="submit">Sign me up</button>
            <div class="err" id="err" role="alert"></div>
          </form>
        <?php endif; ?>
        <?php if ( alc_mod( 'news_small' ) ) : ?><small><?php echo esc_html( alc_mod( 'news_small' ) ); ?></small><?php endif; ?>
      </div>
    </div>
  </div>
</section>
