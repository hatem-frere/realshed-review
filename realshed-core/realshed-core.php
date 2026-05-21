<?php
/**
 * Plugin Name: Realshed Core
 * Plugin URI:  https://hatemfrere.com/realshed-core
 * Description: Essential functionality for Realshed Theme. Includes Properties, Agents, Team, Testimonials CPTs, Widgets, and Theme Options. Required for theme to function properly.
 * Version:     1.1.0
 * Author:      Hatem Frere
 * Author URI:  https://hatemfrere.com
 * Text Domain: realshed-core
 * Domain Path: /languages
 * Requires at least: 6.8
 * Requires PHP: 8.0
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Realshed_Core
 */

// Prevent direct access for security
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * 1. Define Global Constants
 */
define( 'REALSHED_CORE_VERSION', '1.0.0' );
define( 'REALSHED_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'REALSHED_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'REALSHED_CORE_BASENAME', plugin_basename( __FILE__ ) );

/**
 * 2. Load Main Plugin Class
 */
if ( file_exists( REALSHED_CORE_PATH . 'includes/class-realshed-core.php' ) ) {
    require_once REALSHED_CORE_PATH . 'includes/class-realshed-core.php';
}

/**
 * 3. Initialize the Plugin
 */
function realshed_core() {
    if ( class_exists( 'Realshed_Core' ) ) {
        return Realshed_Core::instance();
    }
    return null;
}
realshed_core();

/**
 * 3.1 Load AJAX handler for instant property filtering
 */
if ( file_exists( REALSHED_CORE_PATH . 'includes/ajax/property-filter-ajax.php' ) ) {
    require_once REALSHED_CORE_PATH . 'includes/ajax/property-filter-ajax.php';
}

/**
 * 4. Activation Hook
 */
register_activation_hook( __FILE__, 'realshed_core_activate' );

function realshed_core_activate() {
    update_option( 'realshed_core_activated_at', time() );
    update_option( 'realshed_core_version', REALSHED_CORE_VERSION );

    if ( class_exists( 'Realshed_Core' ) ) {
        $plugin = Realshed_Core::instance();
        if ( method_exists( $plugin, 'register_cpts' ) ) {
            $plugin->register_cpts();
        }
    }
    flush_rewrite_rules();
}

/**
 * 5. Deactivation Hook
 */
register_deactivation_hook( __FILE__, 'realshed_core_deactivate' );

function realshed_core_deactivate() {
    flush_rewrite_rules();
}

/**
 * 6. Register Plugin Sidebars
 * This allows the plugin to have "Complete Control" over the Sidebar area.
 */
function realshed_core_register_sidebars() {
    register_sidebar( array(
        'name'          => esc_html__( 'Property Sidebar Filter', 'realshed-core' ),
        'id'            => 'property_sidebar',
        'description'   => esc_html__( 'Add widgets here for the property listing page.', 'realshed-core' ),
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="widget-title"><h5>',
        'after_title'   => '</h5></div>',
    ) );
}
add_action( 'widgets_init', 'realshed_core_register_sidebars' );
