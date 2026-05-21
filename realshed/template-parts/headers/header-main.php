<!-- main header -->
<header class="main-header">
    <?php
    $header_address      = realshed_get_option( 'header_address', __( 'Discover St, New York, NY 10012, USA', 'realshed' ) );
    $header_hours        = realshed_get_option( 'header_working_hours', __( 'Mon - Sat 9.00 - 18.00', 'realshed' ) );
    $header_phone        = realshed_get_option( 'header_phone', '+251-235-3256' );
    $header_phone_url    = realshed_get_phone_url( $header_phone );
    $signin_url          = realshed_get_option( 'header_signin_url', home_url( '/signin' ) );
    $add_listing_url     = realshed_get_option( 'header_add_listing_url', home_url( '/add-listing' ) );
    $add_listing_text    = realshed_get_option( 'header_add_listing_text', __( 'Add Listing', 'realshed' ) );
    $logo_url            = realshed_get_logo_url();
    $social_links        = array(
        'facebook-f'    => realshed_get_option( 'social_facebook_url', home_url() ),
        'twitter'       => realshed_get_option( 'social_twitter_url', home_url() ),
        'pinterest-p'   => realshed_get_option( 'social_pinterest_url', home_url() ),
        'google-plus-g' => realshed_get_option( 'social_google_url', home_url() ),
        'vimeo-v'       => realshed_get_option( 'social_vimeo_url', home_url() ),
    );
    ?>
    <!-- header-top -->
    <div class="header-top">
        <div class="top-inner clearfix">
            <div class="left-column pull-left">
                <ul class="info clearfix">
                    <?php if ( $header_address ) : ?>
                        <li><i class="far fa-map-marker-alt"></i><?php echo esc_html( $header_address ); ?></li>
                    <?php endif; ?>
                    <?php if ( $header_hours ) : ?>
                        <li><i class="far fa-clock"></i><?php echo esc_html( $header_hours ); ?></li>
                    <?php endif; ?>
                    <?php if ( $header_phone && $header_phone_url ) : ?>
                        <li><i class="fa-solid fa-phone"></i><a href="<?php echo esc_url( $header_phone_url ); ?>"><?php echo esc_html( $header_phone ); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="right-column pull-right">
                <ul class="social-links clearfix">
                    <?php foreach ( $social_links as $icon => $url ) : ?>
                        <?php if ( ! empty( $url ) ) : ?>
                            <li><a href="<?php echo esc_url( $url ); ?>"><i class="fab fa-<?php echo esc_attr( $icon ); ?>"></i></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php if ( $signin_url ) : ?>
                    <div class="sign-box">
                        <a href="<?php echo esc_url( $signin_url ); ?>"><i class="fas fa-user"></i><?php esc_html_e( 'Sign In', 'realshed' ); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- header-lower -->
    <div class="header-lower">
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
                    <!--Mobile Navigation Toggler-->
                    <div class="mobile-nav-toggler">
                        <i class="icon-bar"></i>
                        <i class="icon-bar"></i>
                        <i class="icon-bar"></i>
                    </div>
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
    <!-- END -->
