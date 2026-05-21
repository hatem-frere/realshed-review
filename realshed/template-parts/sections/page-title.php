<?php
/**
* Blog Posts Index Template
*
* @package Realshed
*/

// Page Title Background
$blog_bg = get_theme_mod( 'realshed_blog_bg', get_theme_file_uri( '/assets/images/background/page-title-5.jpg' ) );
?>
<!-- BREADCRUMB -->
<section class='page-title centred' style="background-image: url(<?php echo esc_url( $blog_bg ); ?>);">
  <div class='auto-container'>
    <div class='content-box clearfix'>
      <h1><?php esc_html_e( 'Blog Standard', 'realshed' ); ?></h1>
      <ul class='bread-crumb clearfix'>
        <li>
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php esc_html_e( 'Home', 'realshed' );
              ?>
          </a>
        </li>
        <li><?php esc_html_e( 'Blog Standard', 'realshed' ); ?></li>
      </ul>
      <!-- bread-crumb -->
    </div>
    <!-- content-box -->
  </div>
  <!-- auto-container -->
</section>
<!-- page-title - BREADCRUMB -->