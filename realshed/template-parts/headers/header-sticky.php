<div class="sticky-header">
  <?php
  $logo_url         = realshed_get_logo_url();
  $add_listing_url  = realshed_get_option( 'header_add_listing_url', home_url( '/add-listing' ) );
  $add_listing_text = realshed_get_option( 'header_add_listing_text', __( 'Add Listing', 'realshed' ) );
  ?>
  <div class="outer-box">
    <div class="main-box">
      <div class="logo-box">
        <figure class="logo">
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
          </a>
        </figure>
      </div>
      <div class="menu-area clearfix">
        <nav class="main-menu navbar-expand-md navbar-light">
          <div class="collapse navbar-collapse show clearfix" id="navbarSupportedContent">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'navigation clearfix',
                'container'      => false,
                'walker'         => new Realshed_Nav_Walker(),
                'fallback_cb'    => 'realshed_default_menu_fallback',
            ) );
            ?>
          </div>
        </nav>
      </div>
      <?php if ( $add_listing_url && $add_listing_text ) : ?>
        <div class="btn-box">
          <a href="<?php echo esc_url( $add_listing_url ); ?>" class="theme-btn btn-one"><span>+</span><?php echo esc_html( $add_listing_text ); ?></a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</header>
<!-- main header End -->
