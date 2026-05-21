<?php
/**
 * Property Content Controller
 *
 * Reads the layout value passed by archive-property.php via set_query_var()
 * and runs the WordPress loop, loading the correct card partial for each post.
 *
 * This file only handles the card partial — the page scaffold (sidebar,
 * map columns, section classes) is handled entirely in archive-property.php.
 *
 * Path: wp-content/themes/realshed/property/content-property.php
 * @package Realshed
 */

/**
 * Read the layout passed by archive-property.php.
 * Falls back to layout-list-sidebar if not set (safe default).
 *
 * Valid values and their matching files in property/partials/listing/:
 *
 *   layout-list-sidebar   → layout-list-sidebar.php
 *   layout-grid-sidebar   → layout-grid-sidebar.php
 *   layout-list-full-map  → layout-list-full-map.php
 *   layout-grid-full-map  → layout-grid-full-map.php
 *   layout-list-half-map  → layout-list-half-map.php
 *   layout-grid-half-map  → layout-grid-half-map.php
 */
$layout = get_query_var( 'realshed_layout', 'layout-list-sidebar' );

/**
 * Map Redux value → partial file path (relative to property/ directory).
 * The switch value must match the Redux button_set option value exactly.
 * The file path must match the renamed files in partials/listing/.
 */
switch ( $layout ) {
    case 'layout-list-sidebar':
        $template_part = 'partials/listing/layout-list-sidebar';
        break;
    case 'layout-grid-sidebar':
        $template_part = 'partials/listing/layout-grid-sidebar';
        break;
    case 'layout-list-full-map':
        $template_part = 'partials/listing/layout-list-full-map';
        break;
    case 'layout-grid-full-map':
        $template_part = 'partials/listing/layout-grid-full-map';
        break;
    case 'layout-list-half-map':
        $template_part = 'partials/listing/layout-list-half-map';
        break;
    case 'layout-grid-half-map':
        $template_part = 'partials/listing/layout-grid-half-map';
        break;
    default:
        $template_part = 'partials/listing/layout-list-sidebar';
        break;
}

/**
 * THE LOOP
 * Each property post loads the selected layout partial.
 * get_template_part() looks inside the theme's property/ directory.
 */
while ( have_posts() ) : the_post();
    get_template_part( 'property/' . $template_part );
endwhile;

wp_reset_postdata();
// END
