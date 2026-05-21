<?php
/**
 * The header for the Realshed theme.
 *
 * Contains the <head> section and dynamically loads the header style from template-parts/headers.
 * Uses the Realshed_Nav_Walker for Bootstrap-compatible navigation.
 *
 * @package Realshed
 * @since Realshed 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

  <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title><?php bloginfo('name'); wp_title('|', true, 'right'); ?></title>
    <link rel="icon" href="<?php echo esc_url(get_theme_file_uri('/assets/images/favicon.ico')); ?>" type="image/x-icon">
    <?php wp_head(); ?>
    <!-- Temporary JS to force hide preloader -->
    <script>
    window.addEventListener('load', function() {
      document.querySelector('.loader-wrap').style.display = 'none';
    });
    </script>
  </head>

  <body <?php body_class(); ?>>
    <div class="boxed_wrapper">
      <!-- Preloader -->
      <div class="loader-wrap">
        <div class="preloader">
          <div class="preloader-close"><i class="far fa-times"></i></div>
          <div id="handle-preloader" class="handle-preloader">
            <div class="animation-preloader">
              <div class="spinner"></div>
              <div class="txt-loading">
                <?php
              $site_name = get_bloginfo('name') ?: 'realshed';
              $clean_name = preg_replace('/[^\p{L}\p{N}\s]/u', '', $site_name);
              $clean_name = strtolower(trim($clean_name));
              $letters = str_split($clean_name);
              $letters = array_slice($letters, 0, 12);
              foreach ($letters as $letter) {
                  if ($letter !== '') {
                      echo '<span data-text-preloader="' . esc_attr($letter) . '" class="letters-loading">' . esc_html($letter === ' ' ? '&nbsp;' : $letter) . '</span>';
                  }
              }
              ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Preloader End -->

      <!-- Style Switcher Menu -->
      <!-- <div class="switcher">
        <div class="switch_btn">
          <button><i class="fas fa-palette"></i></button>
        </div>
        <div class="switch_menu">
          <div class="switcher_container">
            <ul id="styleOptions" title="switch styling">
              <li><a href="javascript:void(0)" data-theme="green" class="green-color"></a></li>
              <li><a href="javascript:void(0)" data-theme="pink" class="pink-color"></a></li>
              <li><a href="javascript:void(0)" data-theme="violet" class="violet-color"></a></li>
              <li><a href="javascript:void(0)" data-theme="crimson" class="crimson-color"></a></li>
              <li><a href="javascript:void(0)" data-theme="orange" class="orange-color"></a></li>
            </ul>
          </div>
        </div>
      </div> -->
      <!-- End Style Switcher Menu -->
      <?php
      get_template_part( 'template-parts/headers/header', 'main' );
      get_template_part( 'template-parts/headers/header', 'sticky' );
      get_template_part( 'template-parts/headers/header', 'mobile' );
      ?>
