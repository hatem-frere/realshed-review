<?php
/**
 * The Front Page Template (front-page.php)
 *
 * @package Realshed
 */

get_header();

// 1. Access Redux Global Variable
global $realshed_options;

// 2. Check if the Custom Homepage toggle is enabled
$is_custom = isset($realshed_options['enable_custom_homepage']) ? $realshed_options['enable_custom_homepage'] : false;

if ( $is_custom && isset($realshed_options['homepage_sections']['enabled']) ) {

    // 3. Get the enabled sections (hero, category, etc.)
    $sections = $realshed_options['homepage_sections']['enabled'];

    if ( !empty( $sections ) ) {
        foreach ( $sections as $key => $name ) {
            // This pulls files from: template-parts/sections/{$key}.php
            get_template_part( 'template-parts/sections/' . $key );
        }
    }

} else {
    // 4. FALLBACK: Show standard blog content if the toggle is OFF
    // This uses your existing content template
    get_template_part( 'template-parts/content/content', '' );
}

get_footer();
