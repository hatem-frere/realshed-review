<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme Customizer settings
 */
function realshed_customize_register( $wp_customize ) {

// Add section for Mobile Logo (if needed)
$wp_customize->add_section( 'realshed_mobile_logo_section', array(
    'title'    => __( 'Mobile Logo', 'realshed' ),
    'priority' => 30,
) );

// Add setting for Mobile Logo
$wp_customize->add_setting( 'realshed_mobile_logo' );

// Add control for uploading Mobile Logo
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'realshed_mobile_logo', array(
    'label'    => __( 'Upload Mobile Logo', 'realshed' ),
    'section'  => 'realshed_mobile_logo_section',
    'settings' => 'realshed_mobile_logo',
) ) );

}
add_action( 'customize_register', 'realshed_customize_register' );

