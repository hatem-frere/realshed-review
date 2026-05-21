<?php
/**
* Pagination Helper Class
* Handles all pagination rendering with clean DOM structure
*
* @package Realshed
* @since 1.0.0
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
* Class Realshed_Pagination
*
* Professional pagination with DOM cleanup for perfect HTML structure
*/

class Realshed_Pagination {

    /**
    * Generate numbered pagination with clean DOM
    *
    * This method creates pagination and cleans up the HTML structure
    * using DOMDocument to ensure proper semantic markup
    *
    * @param array $args Optional. Arguments to customize pagination
    * @return string HTML output for pagination
    */
    public static function numbering_paginate( $args = array() ) {

        global $wp_query;

        // Don't show pagination if there's only one page
        if ( $wp_query->max_num_pages <= 1 ) {
            return '';
        }

        // Big number for base replacement
        $big = 999999999;

        // Get current page
        $current_page = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

        // Default arguments
        $defaults = array(
            'prev_text'    => '<i class="fas fa-angle-left"></i>',
            'next_text'    => '<i class="fas fa-angle-right"></i>',
            'mid_size'     => 2,
            'end_size'     => 1,
            'show_all'     => false,
            'add_fragment' => '',
        );

        // Merge with user arguments
        $args = wp_parse_args( $args, $defaults );

        // Get pagination links as array
        $pages = paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => $current_page,
            'total'     => $wp_query->max_num_pages,
            'type'      => 'array',
            'prev_next' => true,
            'prev_text' => $args[ 'prev_text' ],
            'next_text' => $args[ 'next_text' ],
            'mid_size'  => $args[ 'mid_size' ],
            'end_size'  => $args[ 'end_size' ],
            'show_all'  => $args[ 'show_all' ],
            'add_fragment' => $args[ 'add_fragment' ],
        ) );

        // Return empty if no pages
        if ( ! is_array( $pages ) ) {
            return '';
        }

        // Build initial HTML structure
        $output = '<div class="pagination-wrapper">';
        $output .= '<ul class="pagination clearfix">';

        foreach ( $pages as $page ) {
            $output .= '<li>' . $page . '</li>';
        }

        $output .= '</ul>';
        $output .= '</div>';

        // Clean up HTML structure using DOMDocument
        return self::clean_pagination_dom( $output );
    }

    /**
    * Clean pagination DOM structure
    *
    * Removes unnecessary classes and converts spans to anchors
    * for semantic and consistent HTML structure
    *
    * @param string $html Raw HTML string
    * @return string Cleaned HTML
    */
    private static function clean_pagination_dom( $html ) {

        // Create new DOMDocument instance
        $dom = new \DOMDocument();

        // Suppress errors for malformed HTML
        libxml_use_internal_errors( true );

        // Load HTML with UTF-8 encoding
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?>' . $html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        // Clear any libxml errors
        libxml_clear_errors();

        // Remove unwanted classes from <li> elements
        $li_elements = $dom->getElementsByTagName( 'li' );
        foreach ( $li_elements as $li ) {
            $li->removeAttribute( 'class' );
        }

        // Convert <span> to <a> for current page
        $span_elements = iterator_to_array( $dom->getElementsByTagName( 'span' ) );
        foreach ( $span_elements as $span ) {
            // Create new <a> element
            $new_anchor = $dom->createElement( 'a' );
            $new_anchor->setAttribute( 'class', 'current' );
            $new_anchor->setAttribute( 'href', '#' );
            $new_anchor->setAttribute( 'aria-current', 'page' );

            // Move all child nodes from <span> to <a>
            while ( $span->firstChild ) {
                $new_anchor->appendChild( $span->firstChild );
            }

            // Replace <span> with <a>
            $span->parentNode->replaceChild( $new_anchor, $span );
        }

        // Save cleaned HTML
        $clean_output = $dom->saveHTML();

        // Remove XML declaration
        $clean_output = str_replace( '<?xml encoding="utf-8" ?>', '', $clean_output );

        return $clean_output;
    }

    /**
    * Custom query pagination
    * For WP_Query custom loops
    *
    * @param WP_Query $query Custom query object
    * @param array $args Optional arguments
    * @return string HTML output
    */
    public static function custom_query_pagination( $query, $args = array() ) {

        if ( $query->max_num_pages <= 1 ) {
            return '';
        }

        $big = 999999999;

        $current_page = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

        $defaults = array(
            'prev_text' => '<i class="fas fa-angle-left"></i>',
            'next_text' => '<i class="fas fa-angle-right"></i>',
            'mid_size'  => 2,
            'end_size'  => 1,
        );

        $args = wp_parse_args( $args, $defaults );

        $pages = paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => $current_page,
            'total'     => $query->max_num_pages,
            'type'      => 'array',
            'prev_next' => true,
            'prev_text' => $args[ 'prev_text' ],
            'next_text' => $args[ 'next_text' ],
            'mid_size'  => $args[ 'mid_size' ],
            'end_size'  => $args[ 'end_size' ],
        ) );

        if ( ! is_array( $pages ) ) {
            return '';
        }

        $output = '<div class="pagination-wrapper">';
        $output .= '<ul class="pagination clearfix">';

        foreach ( $pages as $page ) {
            $output .= '<li>' . $page . '</li>';
        }

        $output .= '</ul>';
        $output .= '</div>';

        return self::clean_pagination_dom( $output );
    }

    /**
    * Simple prev/next pagination
    *
    * @return string HTML output
    */
    public static function simple_pagination() {

        global $wp_query;

        if ( $wp_query->max_num_pages <= 1 ) {
            return '';
        }

        $output = '<div class="pagination-wrapper simple-pagination">';
        $output .= '<ul class="pagination clearfix">';

        // Previous link
        if ( get_previous_posts_link() ) {
            $output .= '<li class="prev-link">';
            $output .= get_previous_posts_link( '<i class="fas fa-angle-left"></i> ' . __( 'Previous', 'realshed' ) );
            $output .= '</li>';
        }

        // Next link
        if ( get_next_posts_link() ) {
            $output .= '<li class="next-link">';
            $output .= get_next_posts_link( __( 'Next', 'realshed' ) . ' <i class="fas fa-angle-right"></i>' );
            $output .= '</li>';
        }

        $output .= '</ul>';
        $output .= '</div>';

        return $output;
    }

    /**
    * Ajax load more pagination
    *
    * @return string HTML output with data attributes
    */
    public static function ajax_load_more() {

        global $wp_query;

        if ( $wp_query->max_num_pages <= 1 ) {
            return '';
        }

        $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
        $max   = intval( $wp_query->max_num_pages );

        if ( $paged >= $max ) {
            return '';
        }

        $output = '<div class="pagination-wrapper load-more-wrapper text-center">';
        $output .= '<button class="theme-btn btn-two load-more-btn" ';
        $output .= 'data-page="' . esc_attr( $paged ) . '" ';
        $output .= 'data-max="' . esc_attr( $max ) . '">';
        $output .= '<span class="btn-text">' . __( 'Load More Posts', 'realshed' ) . '</span>';
        $output .= '<span class="btn-loading" style="display:none;">';
        $output .= '<i class="fas fa-spinner fa-spin"></i> ' . __( 'Loading...', 'realshed' );
        $output .= '</span>';
        $output .= '</button>';
        $output .= '</div>';

        return $output;
    }

    /**
    * Pagination with page info
    * Shows 'Page X of Y' text
    *
    * @return string HTML output
    */
    public static function pagination_with_info() {

        global $wp_query;

        if ( $wp_query->max_num_pages <= 1 ) {
            return '';
        }

        $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
        $max   = intval( $wp_query->max_num_pages );

        $output = '<div class="pagination-info-wrapper">';

        // Page info text
        $output .= '<div class="pagination-info mb-3">';
        $output .= sprintf(
            __( 'Page %1$s of %2$s', 'realshed' ),
            '<strong>' . number_format_i18n( $paged ) . '</strong>',
            '<strong>' . number_format_i18n( $max ) . '</strong>'
        );
        $output .= '</div>';

        // Pagination links
        $output .= self::numbering_paginate();

        $output .= '</div>';

        return $output;
    }
}