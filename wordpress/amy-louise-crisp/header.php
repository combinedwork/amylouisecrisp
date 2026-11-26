<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-page="<?php echo esc_attr( alc_page_key() ); ?>">
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<header id="hdr">
  <div class="wrap head">
    <?php if ( has_custom_logo() ) : ?>
      <div class="brand brand-logo"><?php the_custom_logo(); ?></div>
    <?php else : ?>
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand brand-logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> home"><img src="<?php echo esc_url( alc_theme_img( 'logo/logo-wordmark.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) . ' – ' . get_bloginfo( 'description' ) ); ?>" width="1152" height="240"></a>
    <?php endif; ?>
    <nav id="nav" aria-label="Main menu">
      <?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'items_wrap' => '<ul>%3$s</ul>', 'fallback_cb' => 'alc_menu_fallback', 'depth' => 1 ) ); ?>
    </nav>
    <div class="icons">
      <button class="icon-btn" id="favBtn" aria-label="Saved recipes" title="Saved recipes"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg><span class="count" id="favCount">0</span></button>
      <button class="icon-btn" id="searchBtn" aria-label="Search" title="Search ( / )"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></button>
      <button class="icon-btn menu-btn" id="menuBtn" aria-label="Menu" aria-expanded="false"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
    </div>
  </div>
</header>
<main id="main">
