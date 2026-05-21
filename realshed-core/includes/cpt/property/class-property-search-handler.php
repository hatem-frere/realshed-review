<?php
/**
 * Property Search Handler Class
 * Updated with amenities, area fix, popular filter, etc.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Realshed_Property_Search_Handler' ) ) {

    class Realshed_Property_Search_Handler {

        public function __construct() {
            add_action( 'pre_get_posts', array( $this, 'modify_property_search_query' ) );
        }

        public function modify_property_search_query( $query ) {
            if ( is_admin() || ! $query->is_main_query() ) return;
            if ( ! isset( $_GET['realshed_search'] ) ) return;

            if ( $query->is_search || is_post_type_archive( 'property' ) ||
                 is_tax( array( 'property_type', 'property_status', 'property_location' ) ) ||
                 get_query_var( 'realshed_page' ) === 'search-properties' ) {

                $query->set( 'post_type', 'property' );
                $query->set( 'posts_per_page', 12 );

                $tax_query  = array( 'relation' => 'AND' );
                $meta_query = array( 'relation' => 'AND' );

                // 1. Keyword search
                if ( ! empty( $_GET['s'] ) ) {
                    $query->set( 's', sanitize_text_field( $_GET['s'] ) );
                }

                // 2. Taxonomy filters
                $tax_map = array(
                    's_status'   => 'property_status',
                    's_type'     => 'property_type',
                    's_location' => 'property_location',
                );
                foreach ( $tax_map as $get_key => $taxonomy ) {
                    if ( ! empty( $_GET[ $get_key ] ) ) {
                        $tax_query[] = array(
                            'taxonomy' => $taxonomy,
                            'field'    => 'slug',
                            'terms'    => sanitize_text_field( $_GET[ $get_key ] ),
                        );
                    }
                }

                // 3. Amenities (multiple)
                if ( ! empty( $_GET['s_amenities'] ) && is_array( $_GET['s_amenities'] ) ) {
                    $tax_query[] = array(
                        'taxonomy' => 'property_amenity',
                        'field'    => 'slug',
                        'terms'    => array_map( 'sanitize_title', $_GET['s_amenities'] ),
                        'operator' => 'AND', // change to 'IN' if you want "any of"
                    );
                }

                // 4. Meta filters
                // Bedrooms (min)
                if ( ! empty( $_GET['s_rooms'] ) ) {
                    $meta_query[] = array(
                        'key'     => '_property_bedrooms',
                        'value'   => intval( $_GET['s_rooms'] ),
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    );
                }
                // Bathrooms (min)
                if ( ! empty( $_GET['s_bath'] ) ) {
                    $meta_query[] = array(
                        'key'     => '_property_bathrooms',
                        'value'   => intval( $_GET['s_bath'] ),
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    );
                }
                // Floor (min) – changed from '=' to '>='
                if ( ! empty( $_GET['s_floor'] ) ) {
                    $meta_query[] = array(
                        'key'     => '_property_floors',
                        'value'   => intval( $_GET['s_floor'] ),
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    );
                }
                // Agency (exact match)
                if ( ! empty( $_GET['s_agency'] ) ) {
                    $meta_query[] = array(
                        'key'     => '_property_agency',
                        'value'   => sanitize_text_field( $_GET['s_agency'] ),
                        'compare' => '=',
                    );
                }

                // 5. “Most Popular” filter
                if ( ! empty( $_GET['s_popular'] ) ) {
                    $popular = sanitize_text_field( $_GET['s_popular'] );
                    if ( $popular === 'featured' ) {
                        $meta_query[] = array(
                            'key'     => '_property_featured',
                            'value'   => '1',
                            'compare' => '=',
                        );
                    } elseif ( $popular === 'hot' ) {
                        $meta_query[] = array(
                            'key'     => '_property_hot_deal',
                            'value'   => '1',
                            'compare' => '=',
                        );
                    }
                }

                // 6. Price range
                $price_min = isset( $_GET['s_price_min'] ) && $_GET['s_price_min'] !== '' ? intval( $_GET['s_price_min'] ) : 0;
                $price_max = isset( $_GET['s_price_max'] ) && $_GET['s_price_max'] !== '' ? intval( $_GET['s_price_max'] ) : 999999999;
                $meta_query[] = array(
                    'key'     => '_property_price',
                    'value'   => array( $price_min, $price_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );

                // 7. Area range – fixed meta key
                $area_min = isset( $_GET['s_area_min'] ) && $_GET['s_area_min'] !== '' ? intval( $_GET['s_area_min'] ) : 0;
                $area_max = isset( $_GET['s_area_max'] ) && $_GET['s_area_max'] !== '' ? intval( $_GET['s_area_max'] ) : 999999999;
                $meta_query[] = array(
                    'key'     => '_property_area',   // fixed from _property_area_value
                    'value'   => array( $area_min, $area_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );

                // 8. Sort order
                if ( ! empty( $_GET['s_sort'] ) ) {
                    switch ( sanitize_text_field( $_GET['s_sort'] ) ) {
                        case 'price-low':
                            $query->set( 'orderby', 'meta_value_num' );
                            $query->set( 'meta_key', '_property_price' );
                            $query->set( 'order', 'ASC' );
                            break;
                        case 'price-high':
                            $query->set( 'orderby', 'meta_value_num' );
                            $query->set( 'meta_key', '_property_price' );
                            $query->set( 'order', 'DESC' );
                            break;
                        case 'newest':
                        default:
                            $query->set( 'orderby', 'date' );
                            $query->set( 'order', 'DESC' );
                            break;
                    }
                }

                if ( count( $tax_query ) > 1 )  $query->set( 'tax_query', $tax_query );
                if ( count( $meta_query ) > 1 ) $query->set( 'meta_query', $meta_query );
            }
        }
    }
    new Realshed_Property_Search_Handler();
}
