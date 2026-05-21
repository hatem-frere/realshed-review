<?php
/**
 * Property Archive Template
 *
 * Handles two distinct pages:
 *
 *   CONTEXT: 'search'  → /search-properties/
 *   Always renders the sidebar structure with layout-list-sidebar.
 *   This is the search results page — layout is fixed, not user-selectable.
 *
 *   CONTEXT: 'browse'  → /properties/
 *   Renders the layout chosen by the user in Redux Options.
 *   Three possible page structures:
 *     sidebar   → standard col-lg-4 sidebar + col-lg-8 content
 *     full-map  → Google Map above, then sidebar + content below
 *     half-map  → left map column + right content column (no sidebar)
 *
 * Path: wp-content/themes/realshed/property/archive-property.php
 * @package Realshed
 */

get_header();

global $realshed_options;

// -------------------------------------------------------------------------
// 1. Determine context and layout
// -------------------------------------------------------------------------

$context = get_query_var( 'realshed_archive_context', 'browse' );

if ( $context === 'search' ) {
    $layout = 'layout-list-sidebar';
} else {
    $layout = isset( $realshed_options['property_style_selection'] )
        ? $realshed_options['property_style_selection']
        : 'layout-list-sidebar';
}

if ( strpos( $layout, 'half-map' ) !== false ) {
    $structure = 'half-map';
} elseif ( strpos( $layout, 'full-map' ) !== false ) {
    $structure = 'full-map';
} else {
    $structure = 'sidebar';
}

$wrapper_class = ( strpos( $layout, 'grid' ) !== false ) ? 'grid' : 'list';

// -------------------------------------------------------------------------
// 2. Google Maps API key from Redux (used by full-map and half-map)
// -------------------------------------------------------------------------

$maps_api_key  = isset( $realshed_options['google_maps_api_key'] ) ? $realshed_options['google_maps_api_key'] : '';
$map_lat       = isset( $realshed_options['map_default_lat'] )     ? $realshed_options['map_default_lat']     : '40.712776';
$map_lng       = isset( $realshed_options['map_default_lng'] )     ? $realshed_options['map_default_lng']     : '-74.005974';
$map_zoom      = isset( $realshed_options['map_default_zoom'] )    ? $realshed_options['map_default_zoom']    : 12;

// -------------------------------------------------------------------------
// 3. Shared sort form and helpers
// -------------------------------------------------------------------------

function realshed_sort_form() {
    $preserved = array(
        'post_type', 'realshed_search', 's_status', 's_type', 's_location',
        's', 's_distance', 's_rooms', 's_bath', 's_floor', 's_agency',
        's_price_min', 's_price_max', 's_area_min', 's_area_max',
    );
    ?>
    <form method="get" id="sort-form" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">
        <?php foreach ( $preserved as $param ) : ?>
            <?php if ( ! empty( $_GET[ $param ] ) ) : ?>
                <input type="hidden" name="<?php echo esc_attr( $param ); ?>" value="<?php echo esc_attr( $_GET[ $param ] ); ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="select-box">
            <select class="wide" name="s_sort" onchange="document.getElementById('sort-form').submit()">
                <option value="newest"     <?php selected( $_GET['s_sort'] ?? 'newest', 'newest' );     ?>><?php esc_html_e( 'Sort: Newest',        'realshed' ); ?></option>
                <option value="price-low"  <?php selected( $_GET['s_sort'] ?? '',       'price-low' );  ?>><?php esc_html_e( 'Price: Low to High',   'realshed' ); ?></option>
                <option value="price-high" <?php selected( $_GET['s_sort'] ?? '',       'price-high' ); ?>><?php esc_html_e( 'Price: High to Low',   'realshed' ); ?></option>
            </select>
        </div>
    </form>
    <?php
}

function realshed_shorting_bar( $show_layout_buttons = true ) {
    global $wp_query;
    $count = $wp_query->found_posts;
    ?>
    <div class="item-shorting clearfix">
        <div class="left-column pull-left">
            <h5><?php esc_html_e( 'Search Results:', 'realshed' ); ?>
                <span>
                    <?php
                    if ( $count > 0 ) {
                        printf( esc_html__( 'Showing %s Listings', 'realshed' ), $count );
                    } else {
                        esc_html_e( 'No Listings Found', 'realshed' );
                    }
                    ?>
                </span>
            </h5>
        </div>
        <div class="right-column pull-right clearfix">
            <div class="short-box clearfix">
                <?php realshed_sort_form(); ?>
            </div>
            <?php if ( $show_layout_buttons ) : ?>
            <div class="short-menu clearfix">
                <button class="list-view on"><i class="icon-35"></i></button>
                <button class="grid-view"><i class="icon-36"></i></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * CORRECTED PROPERTY LOOP
 * - Directly loads list and grid partials (no content-property.php)
 * - Proper classes for CSS toggle
 * - Each grid item gets its own column
 */
function realshed_property_loop( $layout, $wrapper_class ) {
    // Determine which list partial to use based on $layout
    if ( strpos( $layout, 'full-map' ) !== false ) {
        $list_partial = 'partials/listing/layout-list-full-map';
        $grid_partial = 'partials/listing/layout-grid-full-map';
    } elseif ( strpos( $layout, 'half-map' ) !== false ) {
        $list_partial = 'partials/listing/layout-list-half-map';
        $grid_partial = 'partials/listing/layout-grid-half-map';
    } else {
        $list_partial = 'partials/listing/layout-list-sidebar';
        $grid_partial = 'partials/listing/layout-grid-sidebar';
    }
    ?>
    <div class="wrapper <?php echo esc_attr( $wrapper_class ); ?>">
        <?php if ( have_posts() ) : ?>
            <!-- LIST MODE (visible by default) -->
            <div class="deals-list-content list-item">
                <?php
                // Reset posts in case of previous loops
                rewind_posts();
                while ( have_posts() ) : the_post();
                    get_template_part( 'property/' . $list_partial );
                endwhile;
                ?>
            </div>

            <!-- GRID MODE (hidden initially) -->
            <div class="deals-grid-content grid-item">
                <div class="row clearfix">
                    <?php
                    rewind_posts();
                    while ( have_posts() ) : the_post();
                        ?>
                        <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                            <?php get_template_part( 'property/' . $grid_partial ); ?>
                        </div>
                        <?php
                    endwhile;
                    ?>
                </div>
            </div>

            <div class="pagination-wrapper">
                <?php realshed_numbering_paginate(); ?>
            </div>
        <?php else : ?>
            <div class="no-results-message text-center" style="padding:100px 30px;background:#f7f7f7;border-radius:8px;margin-top:30px;">
                <div class="icon" style="font-size:80px;color:#d1d1d1;margin-bottom:20px;"><i class="icon-35"></i></div>
                <h3 style="font-weight:700;color:#222;"><?php esc_html_e( 'No Properties Found', 'realshed' ); ?></h3>
                <p style="color:#777;max-width:400px;margin:0 auto;">
                    <?php esc_html_e( 'We couldn\'t find any properties matching your criteria. Try adjusting your filters.', 'realshed' ); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function realshed_map_placeholder( $lat, $lng, $zoom, $api_key, $extra_class = '' ) {
    ?>
    <div class="google-map <?php echo esc_attr( $extra_class ); ?>"
         id="realshed-property-map"
         data-lat="<?php echo esc_attr( $lat ); ?>"
         data-lng="<?php echo esc_attr( $lng ); ?>"
         data-zoom="<?php echo esc_attr( $zoom ); ?>"
         style="width:100%;height:500px;background:#e8e8e8;display:flex;align-items:center;justify-content:center;">
        <?php if ( empty( $api_key ) ) : ?>
            <div style="text-align:center;color:#999;padding:20px;">
                <i class="fas fa-map-marker-alt" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                <p style="font-size:14px;">
                    <?php esc_html_e( 'Google Maps API key not configured.', 'realshed' ); ?><br>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=realshed_options&tab=apis_settings' ) ); ?>" style="color:#666;">
                        <?php esc_html_e( 'Add your key in Realshed Options → APIs & Integrations', 'realshed' ); ?>
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
?>

<?php if ( $structure === 'sidebar' ) : ?>
<section class="property-page-section property-<?php echo esc_attr( $wrapper_class ); ?>">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                <?php if ( is_active_sidebar( 'property_sidebar' ) ) dynamic_sidebar( 'property_sidebar' ); ?>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="property-content-side">
                    <?php realshed_shorting_bar(); ?>
                    <?php realshed_property_loop( $layout, $wrapper_class ); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ( $structure === 'full-map' ) : ?>
<section class="property-page-section property-map-section property-<?php echo esc_attr( $wrapper_class ); ?>">
    <div class="map-area">
        <?php realshed_map_placeholder( $map_lat, $map_lng, $map_zoom, $maps_api_key, 'full-map' ); ?>
    </div>
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                <?php if ( is_active_sidebar( 'property_sidebar' ) ) dynamic_sidebar( 'property_sidebar' ); ?>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="property-content-side">
                    <?php realshed_shorting_bar(); ?>
                    <?php realshed_property_loop( $layout, $wrapper_class ); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ( $structure === 'half-map' ) : ?>
<section class="deals-style-two property-<?php echo esc_attr( $wrapper_class ); ?>">
    <div class="page-content">
        <div class="left-column">
            <?php realshed_map_placeholder( $map_lat, $map_lng, $map_zoom, $maps_api_key, 'half-map sticky-map' ); ?>
        </div>
        <div class="right-column">
            <div class="property-content-side">
                <?php realshed_shorting_bar( false ); ?>
                <?php realshed_property_loop( $layout, $wrapper_class ); ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
