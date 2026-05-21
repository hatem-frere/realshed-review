<?php

/**
* Realshed functions and definitions
*
* This file is the primary PHP file for a WordPress theme, where you
* define core functionality, actions, filters, and include other necessary files.
*
* @link https://developer.wordpress.org/themes/basics/theme-functions/
*
* @package Realshed
* @since Realshed 1.0.0 // Adjusted for better versioning clarity
*/

// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===
// 1. Security Check: Prevent Direct Access
// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===
/**
* Exit if accessed directly.
* This is a standard security measure in WordPress development to prevent
* unauthorized access to theme files, which could expose sensitive information
* or lead to malicious execution.
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// echo REALSHED_THEME_DIR . '/classes';
// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===
// 2. Define Theme Constants
// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===
/**
* Define Theme Constants for paths, URIs, and versioning.
*
* It's crucial to use unique prefixes (like 'REALSHED_') for all constants
 * to avoid naming conflicts with plugins or other themes.
 *
 * These constants provide an organized and efficient way to reference
 * directories and files throughout the theme, making the code cleaner
 * and easier to update.
 */

/**
 * Theme Version: Used for cache busting static assets (CSS/JS).
 * Increment this number whenever you update theme's stylesheet or JavaScript files
* to ensure users receive the latest versions.
*/

$theme_data = wp_get_theme();
if ( ! defined( 'REALSHED_THEME_VERSION' ) ) {
define( 'REALSHED_THEME_VERSION', $theme_data->get( 'Version' ) );
}

/**
* Theme Directory Path: Absolute path to the theme's root directory.
 * Useful for including PHP files.
 * Example: /var/www/html/wp-content/themes/realshed
 */
if ( ! defined( 'REALSHED_THEME_DIR' ) ) {
    define( 'REALSHED_THEME_DIR', get_template_directory() );
}

/**
 * Theme Directory URI: URL to the theme's root directory.
* Useful for linking to assets like images, CSS, or JS from the browser.
* Example: https://example.com/wp-content/themes/realshed
*/
if ( ! defined( 'REALSHED_THEME_URI' ) ) {
    define( 'REALSHED_THEME_URI', get_template_directory_uri() );
}

/**
* Base Assets URI: URL to the main 'assets' folder.
* All other asset URIs will be built upon this.
* Example: https://example.com/wp-content/themes/realshed/assets
*/
if ( ! defined( 'REALSHED_ASSETS_URI' ) ) {
    define( 'REALSHED_ASSETS_URI', REALSHED_THEME_URI . '/assets' );
}

/**
* CSS Assets URI: URL to the theme's CSS files.
 * Example: https://example.com/wp-content/themes/realshed/assets/css
 */
if ( ! defined( 'REALSHED_CSS_URI' ) ) {
    define( 'REALSHED_CSS_URI', REALSHED_ASSETS_URI . '/css' );
}

/**
 * CSS Assets URI: URL to the theme's CSS files.
* Example: https://example.com/wp-content/themes/realshed/assets/css/color
*/
if ( ! defined( 'REALSHED_CSS_COLOR_URI' ) ) {
    define( 'REALSHED_CSS_COLOR_URI', REALSHED_CSS_URI . '/color' );
}

/**
* Fonts Assets URI: URL to the theme's custom font files.
 * Example: https://example.com/wp-content/themes/realshed/assets/fonts
 */
if ( ! defined( 'REALSHED_FONTS_URI' ) ) {
    define( 'REALSHED_FONTS_URI', REALSHED_ASSETS_URI . '/fonts' );
}

/**
 * Images Assets URI: URL to the theme's image files.
* Example: https://example.com/wp-content/themes/realshed/assets/images
*/
if ( ! defined( 'REALSHED_IMAGES_URI' ) ) {
    define( 'REALSHED_IMAGES_URI', REALSHED_ASSETS_URI . '/images' );
}

/**
* JavaScript Assets URI: URL to the theme's JavaScript files.
 * Example: https://example.com/wp-content/themes/realshed/assets/js
 */
if ( ! defined( 'REALSHED_JS_URI' ) ) {
    define( 'REALSHED_JS_URI', REALSHED_ASSETS_URI . '/js' );
}


// --- Include Directories (Paths for PHP files) ---
/**
 * Includes Directory Path: Absolute path to the 'inc' folder.
 * This folder commonly stores modular PHP files for theme functionality.
 * Example: /var/www/html/wp-content/themes/realshed/inc
 */
if ( ! defined( 'REALSHED_INC_DIR' ) ) {
    define( 'REALSHED_INC_DIR', REALSHED_THEME_DIR . '/inc' );
}

/**
 * Includes Directory Path: Absolute path to the 'customizer' folder.
 * This folder commonly stores customiztion files for theme functionality.
 * Example: /var/www/html/wp-content/themes/realshed/inc/customizer
 */
if ( ! defined( 'REALSHED_CUSTOMIZER_DIR' ) ) {
    define( 'REALSHED_CUSTOMIZER_DIR', REALSHED_INC_DIR . '/customizer' );
}

/**
 * Includes Directory Path: Absolute path to the 'meta' folder.
 * This folder commonly stores meta files for theme functionality.
 * Example: /var/www/html/wp-content/themes/realshed/inc/meta
 */
if ( ! defined( 'REALSHED_META_DIR' ) ) {
    define( 'REALSHED_META_DIR', REALSHED_INC_DIR . '/meta' );
}

/**
 * Classes Directory Path: Absolute path to the 'classes' folder.
 * Used for storing object-oriented PHP code (e.g., custom post type classes).
 * Example: /var/www/html/wp-content/themes/realshed/classes
 */
if ( ! defined( 'REALSHED_CLASSES_DIR' ) ) {
    define( 'REALSHED_CLASSES_DIR', REALSHED_THEME_DIR . '/classes' );
}

// ==============================================================================
// 3. Include Theme Files
// ==============================================================================
/**
 * Include core theme files.
 */

/**
 * Include the main 'includes.php' file.
 * 'includes.php': This file typically acts as a central hub for including other smaller
 * functionality files located within the 'inc' directory.
 */

if ( file_exists( REALSHED_INC_DIR . '/includes.php' ) ) {
    require_once REALSHED_INC_DIR . '/includes.php';
}
// END
