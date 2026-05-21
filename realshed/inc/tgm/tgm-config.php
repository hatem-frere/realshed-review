<?php
/**
 * TGM Plugin Activation configuration for Realshed Theme
 * Staged Installation Logic
 */

if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

require_once get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'realshed_register_required_plugins' );

function realshed_register_required_plugins() {

    $plugins = array();

    /**
     * STAGE 1: The Core Foundation
     * We always include the Core plugin in the array.
     */
    $plugins[] = array(
        'name'               => 'Realshed Core',
        'slug'               => 'realshed-core',
        'source'             => get_template_directory() . '/inc/plugins/realshed-core.zip',
        'required'           => true,
        'version'            => '1.0.0',
    );

    /**
     * STAGE 2: The Extended Dependencies
     * We ONLY add these to the list if Realshed Core is already active.
     */
    if ( is_plugin_active( 'realshed-core/realshed-core.php' ) ) {

        $plugins[] = array(
            'name'      => 'Redux Framework',
            'slug'      => 'redux-framework',
            'required'  => true,
        );

        $plugins[] = array(
            'name'      => 'Contact Form 7',
            'slug'      => 'contact-form-7',
            'required'  => false,
        );

        $plugins[] = array(
            'name'      => 'WooCommerce',
            'slug'      => 'woocommerce',
            'required'  => false,
        );
    }

    $config = array(
        'id'           => 'realshed_tgmpa',
        'default_path' => '',
        'menu'         => 'tgmpa-install-plugins',
        'has_notices'  => true,
        'dismissable'  => true,
        'is_automatic' => true,
    );

    tgmpa( $plugins, $config );
}
