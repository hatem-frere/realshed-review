<?php
/**
 * AJAX Handler for Instant Property Filtering
 *
 * This file processes filter requests without reloading the page.
 * It returns the updated property listings HTML.
 *
 * @package Realshed_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register AJAX actions for both logged-in and non-logged-in users.
 */
add_action('wp_ajax_realshed_ajax_filter', 'realshed_ajax_filter_callback');
add_action('wp_ajax_nopriv_realshed_ajax_filter', 'realshed_ajax_filter_callback');

function realshed_ajax_filter_callback() {
    check_ajax_referer( 'realshed_ajax_filter', 'nonce' );

    $raw_filters = isset( $_POST['filters'] ) ? wp_unslash( $_POST['filters'] ) : array();
    $filters     = is_array( $raw_filters ) ? realshed_sanitize_property_ajax_filters( $raw_filters ) : array();

    // Build base query arguments.
    $query_args = array(
        'post_type'      => 'property',
        'posts_per_page' => 12,
        'paged'          => ! empty( $filters['paged'] ) ? max( 1, absint( $filters['paged'] ) ) : 1,
    );

    $tax_query  = array( 'relation' => 'AND' );
    $meta_query = array( 'relation' => 'AND' );

    // --------------------------------------------------------------
    // 1. Taxonomy filters: status, type, location, amenities.
    // --------------------------------------------------------------
    $tax_map = array(
        's_status'   => 'property_status',
        's_type'     => 'property_type',
        's_location' => 'property_location',
    );

    foreach ( $tax_map as $filter_key => $taxonomy ) {
        if ( ! empty( $filters[ $filter_key ] ) ) {
            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $filters[ $filter_key ],
            );
        }
    }

    // Amenities (multiple checkboxes).
    if ( ! empty( $filters['s_amenities'] ) && is_array( $filters['s_amenities'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'property_amenity',
            'field'    => 'slug',
            'terms'    => $filters['s_amenities'],
            'operator' => 'AND', // Property must have ALL selected amenities.
        );
    }

    // --------------------------------------------------------------
    // 2. Meta filters: bedrooms, bathrooms, floors, popular, price, area.
    // --------------------------------------------------------------
    if ( ! empty( $filters['s_rooms'] ) ) {
        $meta_query[] = array(
            'key'     => '_property_bedrooms',
            'value'   => absint( $filters['s_rooms'] ),
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
    }

    if ( ! empty( $filters['s_bath'] ) ) {
        $meta_query[] = array(
            'key'     => '_property_bathrooms',
            'value'   => absint( $filters['s_bath'] ),
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
    }

    if ( ! empty( $filters['s_floor'] ) ) {
        $meta_query[] = array(
            'key'     => '_property_floors',
            'value'   => absint( $filters['s_floor'] ),
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
    }

    if ( ! empty( $filters['s_popular'] ) ) {
        if ( 'featured' === $filters['s_popular'] ) {
            $meta_query[] = array(
                'key'     => '_property_featured',
                'value'   => '1',
                'compare' => '=',
            );
        } elseif ( 'hot' === $filters['s_popular'] ) {
            $meta_query[] = array(
                'key'     => '_property_hot_deal',
                'value'   => '1',
                'compare' => '=',
            );
        }
    }

    // Price range: only add the meta query when the user actually filters by price.
    if ( '' !== $filters['s_price_min'] || '' !== $filters['s_price_max'] ) {
        $price_min = '' !== $filters['s_price_min'] ? absint( $filters['s_price_min'] ) : 0;
        $price_max = '' !== $filters['s_price_max'] ? absint( $filters['s_price_max'] ) : 999999999;

        $meta_query[] = array(
            'key'     => '_property_price',
            'value'   => array( $price_min, $price_max ),
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN',
        );
    }

    // Area range: only add the meta query when the user actually filters by area.
    if ( '' !== $filters['s_area_min'] || '' !== $filters['s_area_max'] ) {
        $area_min = '' !== $filters['s_area_min'] ? absint( $filters['s_area_min'] ) : 0;
        $area_max = '' !== $filters['s_area_max'] ? absint( $filters['s_area_max'] ) : 999999999;

        $meta_query[] = array(
            'key'     => '_property_area',
            'value'   => array( $area_min, $area_max ),
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN',
        );
    }

    // --------------------------------------------------------------
    // 3. Sorting.
    // --------------------------------------------------------------
    switch ( $filters['s_sort'] ) {
        case 'price-low':
            $query_args['orderby']  = 'meta_value_num';
            $query_args['meta_key'] = '_property_price';
            $query_args['order']    = 'ASC';
            break;

        case 'price-high':
            $query_args['orderby']  = 'meta_value_num';
            $query_args['meta_key'] = '_property_price';
            $query_args['order']    = 'DESC';
            break;

        case 'newest':
        default:
            $query_args['orderby'] = 'date';
            $query_args['order']   = 'DESC';
            break;
    }

    // Apply tax and meta queries only if they have more than the initial relation.
    if ( count( $tax_query ) > 1 ) {
        $query_args['tax_query'] = $tax_query;
    }

    if ( count( $meta_query ) > 1 ) {
        $query_args['meta_query'] = $meta_query;
    }

    $query = new WP_Query( $query_args );

    // --------------------------------------------------------------
    // 4. Generate the HTML output (same as the main archive).
    // --------------------------------------------------------------
    global $realshed_options;
    $layout        = isset( $realshed_options['property_style_selection'] ) ? $realshed_options['property_style_selection'] : 'layout-list-sidebar';
    $wrapper_class = ( false !== strpos( $layout, 'grid' ) ) ? 'grid' : 'list';

    ob_start();

    if ( $query->have_posts() ) {
        ?>
        <div class="property-content-side">
            <?php realshed_shorting_bar(); ?>
            <div class="wrapper <?php echo esc_attr( $wrapper_class ); ?>">
                <!-- List mode (visible by default) -->
                <div class="deals-list-content list-item">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <?php get_template_part( 'property/partials/listing/layout-list-sidebar' ); ?>
                    <?php endwhile; ?>
                </div>
                <!-- Grid mode (hidden initially) -->
                <div class="deals-grid-content grid-item" style="display: none;">
                    <div class="row clearfix">
                        <?php $query->rewind_posts(); while ( $query->have_posts() ) : $query->the_post(); ?>
                            <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                                <?php get_template_part( 'property/partials/listing/layout-grid-sidebar' ); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="pagination-wrapper">
                    <?php echo wp_kses_post( realshed_numbering_paginate( $query ) ); ?>
                </div>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    } else {
        echo '<div class="no-results-message text-center" style="padding:100px 30px;"><h3>' . esc_html__( 'No properties found', 'realshed-core' ) . '</h3></div>';
    }

    $html = ob_get_clean();

    wp_send_json_success( array( 'html' => $html ) );
}

/**
 * Sanitize AJAX property filter values without mutating $_GET.
 *
 * @param array $filters Raw filter input from the AJAX request.
 * @return array Sanitized filters.
 */
function realshed_sanitize_property_ajax_filters( $filters ) {
    $sanitized = array(
        'paged'       => 1,
        's_status'    => '',
        's_type'      => '',
        's_location'  => '',
        's_amenities' => array(),
        's_rooms'     => '',
        's_bath'      => '',
        's_floor'     => '',
        's_popular'   => '',
        's_price_min' => '',
        's_price_max' => '',
        's_area_min'  => '',
        's_area_max'  => '',
        's_sort'      => 'newest',
    );

    $slug_fields = array( 's_status', 's_type', 's_location' );
    foreach ( $slug_fields as $field ) {
        if ( isset( $filters[ $field ] ) && '' !== $filters[ $field ] ) {
            $sanitized[ $field ] = sanitize_title( $filters[ $field ] );
        }
    }

    if ( isset( $filters['s_amenities'] ) && is_array( $filters['s_amenities'] ) ) {
        $sanitized['s_amenities'] = array_filter( array_map( 'sanitize_title', $filters['s_amenities'] ) );
    }

    $integer_fields = array( 'paged', 's_rooms', 's_bath', 's_floor', 's_price_min', 's_price_max', 's_area_min', 's_area_max' );
    foreach ( $integer_fields as $field ) {
        if ( isset( $filters[ $field ] ) && '' !== $filters[ $field ] ) {
            $sanitized[ $field ] = absint( $filters[ $field ] );
        }
    }

    if ( isset( $filters['s_popular'] ) && in_array( $filters['s_popular'], array( 'featured', 'hot' ), true ) ) {
        $sanitized['s_popular'] = sanitize_key( $filters['s_popular'] );
    }

    if ( isset( $filters['s_sort'] ) && in_array( $filters['s_sort'], array( 'newest', 'price-low', 'price-high' ), true ) ) {
        $sanitized['s_sort'] = sanitize_key( $filters['s_sort'] );
    }

    return $sanitized;
}
