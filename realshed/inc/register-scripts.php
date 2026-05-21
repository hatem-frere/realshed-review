<?php
/**
* Register Scripts for this theme.
*
* @package WordPress
* @subpackage Realshed
* @since 1.0.0
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
* Define theme version for cache busting.
*/
if ( ! defined( 'REALSHED_THEME_VERSION' ) ) {
    define( 'REALSHED_THEME_VERSION', '1.0.0' );
}

/**
* Register and Enqueue Styles and Scripts.
*
* @return void
*/
function realshed_enqueue_assets() {
    // Access Redux Options
    global $realshed_options;

    // Determine the color scheme from Redux (Defaulting to 'green')
    $color_scheme = isset( $realshed_options['theme_color_scheme'] ) ? $realshed_options['theme_color_scheme'] : 'green';

    // --- CSS Files ---

    wp_enqueue_style(
        'realshed-google-fonts',
        '//fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
        array(),
        REALSHED_THEME_VERSION,
        'all'
    );

    wp_enqueue_style( 'realshed-fontawesome-7', REALSHED_CSS_URI . '/all.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-fontawesome', REALSHED_CSS_URI . '/font-awesome-all.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-flaticon', REALSHED_CSS_URI . '/flaticon.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-owl-carousel-css', REALSHED_CSS_URI . '/owl.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-bootstrap', REALSHED_CSS_URI . '/bootstrap.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-fancybox-css', REALSHED_CSS_URI . '/jquery.fancybox.min.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-animate', REALSHED_CSS_URI . '/animate.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-jquery-ui-css', REALSHED_CSS_URI . '/jquery-ui.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-nice-select-css', REALSHED_CSS_URI . '/nice-select.css', array(), REALSHED_THEME_VERSION, 'all' );

    // Dynamic Theme Color loading based on Redux selection
    wp_enqueue_style(
        'realshed-theme-color',
        REALSHED_CSS_COLOR_URI . '/' . $color_scheme . '.css',
        array(),
        REALSHED_THEME_VERSION,
        'all'
    );

    wp_enqueue_style( 'realshed-switcher-style', REALSHED_CSS_URI . '/switcher-style.css', array(), REALSHED_THEME_VERSION, 'all' );

    wp_enqueue_style( 'realshed-main-style', REALSHED_CSS_URI . '/style.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-responsive', REALSHED_CSS_URI . '/responsive.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-custom-style', REALSHED_CSS_URI . '/custom-style.css', array(), REALSHED_THEME_VERSION, 'all' );
    wp_enqueue_style( 'realshed-custom-responsive', REALSHED_CSS_URI . '/custom-responsive.css', array(), REALSHED_THEME_VERSION, 'all' );

    // Conditionally loaded CSS
    // if ( is_singular() || is_archive() ) {
        // wp_enqueue_style( 'realshed-owl-carousel-css', REALSHED_CSS_URI . '/owl.css', array(), REALSHED_THEME_VERSION, 'all' );
    // }
    // if ( is_singular( 'property' ) ) {
        // wp_enqueue_style( 'realshed-fancybox-css', REALSHED_CSS_URI . '/jquery.fancybox.min.css', array(), REALSHED_THEME_VERSION, 'all' );
    // }
    // if ( is_post_type_archive( 'property' ) || get_query_var( 'realshed_page' ) === 'search-properties' ) {
    //     wp_enqueue_style( 'realshed-jquery-ui-css', REALSHED_CSS_URI . '/jquery-ui.css', array(), REALSHED_THEME_VERSION, 'all' );
    // }

    // --- JavaScript Files ---

    wp_enqueue_script( 'realshed-custom-jquery', REALSHED_JS_URI . '/jquery.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-popper', REALSHED_JS_URI . '/popper.min.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-bootstrap-js', REALSHED_JS_URI . '/bootstrap.min.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-owl-carousel-js', REALSHED_JS_URI . '/owl.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-wow', REALSHED_JS_URI . '/wow.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-validate', REALSHED_JS_URI . '/validation.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-fancybox-js', REALSHED_JS_URI . '/jquery.fancybox.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-appear', REALSHED_JS_URI . '/appear.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-scrollbar', REALSHED_JS_URI . '/scrollbar.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-isotope', REALSHED_JS_URI . '/isotope.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-nice-select-js', REALSHED_JS_URI . '/jquery.nice-select.min.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-jquery-style-switcher', REALSHED_JS_URI . '/jQuery.style.switcher.min.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-jquery-ui-js', REALSHED_JS_URI . '/jquery-ui.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-nav-tool', REALSHED_JS_URI . '/nav-tool.js', array(), REALSHED_THEME_VERSION, true );
    wp_enqueue_script( 'realshed-product-filter', REALSHED_JS_URI . '/product-filter.js', array(), REALSHED_THEME_VERSION, true );

    // Conditionally loaded JS
    // if ( is_singular() || is_archive() ) {
    //     wp_enqueue_script( 'realshed-owl-carousel-js', REALSHED_JS_URI . '/owl.js', array(), REALSHED_THEME_VERSION, true );
    //     wp_enqueue_script( 'realshed-isotope', REALSHED_JS_URI . '/isotope.js', array(), REALSHED_THEME_VERSION, true );
    //     wp_enqueue_script( 'realshed-nice-select-js', REALSHED_JS_URI . '/jquery.nice-select.min.js', array(), REALSHED_THEME_VERSION, true );
    // }
    // if ( is_singular() ) {
    //     wp_enqueue_script( 'realshed-fancybox-js', REALSHED_JS_URI . '/jquery.fancybox.js', array(), REALSHED_THEME_VERSION, true );
    //     wp_enqueue_script( 'realshed-validate', REALSHED_JS_URI . '/validation.js', array(), REALSHED_THEME_VERSION, true );
    // }
    // if ( is_post_type_archive( 'property' ) || get_query_var( 'realshed_page' ) === 'search-properties' ) {
        // wp_enqueue_script( 'realshed-jquery-ui-js', REALSHED_JS_URI . '/jquery-ui.js', array(), REALSHED_THEME_VERSION, true );
        // wp_enqueue_script( 'realshed-product-filter', REALSHED_JS_URI . '/product-filter.js', array(), REALSHED_THEME_VERSION, true );
    // }

    // Theme Main JS File
    wp_enqueue_script( 'realshed-main-script', REALSHED_JS_URI . '/script.js', array(), REALSHED_THEME_VERSION, true );
    // Theme Custom JS File
    wp_enqueue_script( 'realshed-custom-script', REALSHED_JS_URI . '/custom-script.js', array(), REALSHED_THEME_VERSION, true );
    // Localize script to pass the color directory path.
    wp_localize_script( 'realshed-main-script', 'realshedSettings', array(
        'colorPath' => esc_url( REALSHED_CSS_COLOR_URI . '/' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'realshed_enqueue_assets' );

/**
 * Filter to add a custom ID to the 'realshed-theme-color' stylesheet link tag.
 */
function realshed_add_theme_color_id_to_style_tag( $tag, $handle, $href, $media ) {
    if ( 'realshed-theme-color' === $handle ) {
        // Find the rel = 'stylesheet' attribute and insert the id = 'jssDefault' after it.
        $tag = str_replace( 'rel=\'stylesheet\'', 'rel=\'stylesheet\' id=\'jssDefault\'', $tag );
    }
    return $tag;
}
add_filter( 'style_loader_tag', 'realshed_add_theme_color_id_to_style_tag', 10, 4 );
// END
