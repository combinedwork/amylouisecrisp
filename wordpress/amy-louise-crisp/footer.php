<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_template_part( 'template-parts/newsletter' );
$icons = array(
	'alc_instagram' => array( 'Instagram', '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>' ),
	'alc_facebook'  => array( 'Facebook', '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M14 8h3V4h-3c-2.8 0-4 1.8-4 4.5V11H7v4h3v7h4v-7h3l1-4h-4V8.6c0-.4.3-.6.6-.6z"/></svg>' ),
	'alc_pinterest' => array( 'Pinterest', '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.4 1.8-2.4.9 0 1.3.6 1.3 1.4 0 .9-.5 2.1-.8 3.3-.2 1 .5 1.8 1.5 1.8 1.8 0 3.1-1.9 3.1-4.6 0-2.4-1.7-4.1-4.2-4.1-2.9 0-4.5 2.1-4.5 4.4 0 .9.3 1.8.8 2.3l.1.4-.3 1.1c0 .2-.2.3-.4.2-1.3-.6-2.1-2.5-2.1-4C5.6 8.2 8 5 12.6 5c3.7 0 6.5 2.6 6.5 6.2 0 3.7-2.3 6.6-5.5 6.6-1.1 0-2.1-.6-2.4-1.2l-.7 2.5c-.2.9-.9 2.1-1.3 2.8A10 10 0 1 0 12 2z"/></svg>' ),
	'alc_tiktok'    => array( 'TikTok', '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.4 2.2 1.8 3.7 4 4v3.2c-1.5 0-2.9-.4-4-1.2v6.6a5.6 5.6 0 1 1-5.6-5.6c.3 0 .6 0 .9.1v3.3a2.4 2.4 0 1 0 1.5 2.2V3z"/></svg>' ),
	'alc_youtube'   => array( 'YouTube', '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8c1.6.4 7.8.4 7.8.4s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8zM10 15V9l5.2 3z"/></svg>' ),
);
?>
</main>
<footer>
  <div class="wrap">
    <div class="foot">
      <div>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand"><span class="name"><?php bloginfo( 'name' ); ?></span><span class="tag"><?php bloginfo( 'description' ); ?></span></a>
        <div class="socials">
          <?php foreach ( $icons as $key => $ic ) : $url = alc_mod( $key ); if ( ! $url ) { continue; } ?>
            <a href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $ic[0] ); ?>" target="_blank" rel="noopener"><?php echo $ic[1]; // phpcs:ignore ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div><h4>Recipes</h4><ul data-cat-links></ul></div>
      <div><h4>More</h4><?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'items_wrap' => '<ul>%3$s</ul>', 'fallback_cb' => 'alc_footer_fallback', 'depth' => 1 ) ); ?></div>
    </div>
    <div class="legal">© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
