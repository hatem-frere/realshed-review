<?php
/**
* Realshed Bootstrap Navigation Walker
*
* A custom WordPress nav walker for generating Bootstrap 5-compatible navigation menus.
* Outputs markup matching the Realshed theme's header structure, including main, sticky,
* and mobile menus.
*
* @package Realshed
* @since Realshed 1.0.0
* @see https://developer.wordpress.org/reference/classes/walker_nav_menu/
* @see https://getbootstrap.com/docs/5.3/components/navs-tabs/
*/

class Realshed_Nav_Walker extends Walker_Nav_Menu {
    /**
     * Stores the current menu item being processed.
     *
     * @var WP_Post
     */
    private $current_item;

    /**
     * Bootstrap dropdown menu alignment classes for responsive support.
     *
     * @var array
     */
    private $dropdown_menu_alignment_values = [
        'dropdown-menu-start',
        'dropdown-menu-end',
        'dropdown-menu-sm-start',
        'dropdown-menu-sm-end',
        'dropdown-menu-md-start',
        'dropdown-menu-md-end',
        'dropdown-menu-lg-start',
        'dropdown-menu-lg-end',
        'dropdown-menu-xl-start',
        'dropdown-menu-xl-end',
        'dropdown-menu-xxl-start',
        'dropdown-menu-xxl-end',
    ];

    /**
     * Starts a new level (submenu) in the menu.
     *
     * @param string   $output Passed by reference. Used to append additional content.
     * @param int      $depth  Depth of the menu.
     * @param stdClass $args   Arguments from wp_nav_menu().
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        // The template expects a clean <ul> for dropdowns to avoid layout conflicts
        $output .= "\n" . $indent . "<ul>" . "\n";
    }

    /**
     * Starts the individual menu element.
     *
     * @param string   $output Used to append additional content.
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   Arguments from wp_nav_menu().
     * @param int      $id     Menu item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $this->current_item = $item;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        // Add 'current' if WP marks it as active
        if ( in_array( 'current-menu-item', $classes ) || in_array( 'current-menu-ancestor', $classes ) ) {
            $classes[] = 'current';
        }

        // ==============================


        // Add 'dropdown' class for items with children
        if ( isset( $args->walker->has_children ) && $args->walker->has_children ) {
            $classes[] = 'dropdown';
        }

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= $indent . '<li' . $class_names . '>';

        $attributes = '';
        ! empty( $item->url ) && $attributes .= ' href="' . esc_url( $item->url ) . '"';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';

        // Only Top-level items (depth 0) get the <span> tag per template structure
        if ( $depth === 0 ) {
            $item_output .= $args->link_before . '<span>' . apply_filters( 'the_title', $item->title, $item->ID ) . '</span>' . $args->link_after;
        } else {
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        }

        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * Ends the individual menu element.
     *
     * @param string   $output Used to append additional content.
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   Arguments from wp_nav_menu().
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        // Add the dropdown button at the very end of the LI, after the <ul> submenu
        // This ensures the mobile toggle button is correctly placed per the HTML template
        if ( isset( $args->walker->has_children ) && $args->walker->has_children ) {
            $output .= '<div class="dropdown-btn"><span class="fas fa-angle-down"></span></div>';
        }
        $output .= "</li>\n";
    }
}
