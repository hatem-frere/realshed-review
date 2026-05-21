<?php
/**
 * Property Custom Post Type Class
 *
 * Registers the 'property' post type and all taxonomies.
 * Also registers the custom rewrite rule for /search-properties/.
 *
 * Path: wp-content/plugins/realshed-core/includes/cpt/property/class-property-cpt.php
 * @package Realshed_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Realshed_Property_CPT' ) ) {

    class Realshed_Property_CPT {

        public function __construct() {
            add_action( 'init', array( $this, 'register_property_cpt' ) );
            add_action( 'init', array( $this, 'register_property_taxonomies' ) );

            /**
             * /search-properties/ rewrite rule.
             *
             * Two pages exist:
             *   /properties/        → CPT native archive. Browse all properties.
             *                         Layout controlled by Redux option.
             *   /search-properties/ → Custom rewrite. Search results page.
             *                         Always uses fixed list-sidebar layout.
             *
             * The rewrite maps /search-properties/ to:
             *   index.php?post_type=property&realshed_page=search-properties
             *
             * includes.php reads 'realshed_page' to route both slugs to
             * archive-property.php, passing a context flag so that file
             * knows which page it is rendering.
             *
             * IMPORTANT: After deploying this file, go to
             * Settings > Permalinks and click Save (or call
             * flush_rewrite_rules()) once to register the new rule.
             */
            add_action( 'init',       array( $this, 'add_search_properties_rewrite' ) );
            add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
        }

        public function add_search_properties_rewrite() {
            add_rewrite_rule(
                '^search-properties/?$',
                'index.php?post_type=property&realshed_page=search-properties',
                'top'
            );
        }

        public function register_query_vars( $vars ) {
            $vars[] = 'realshed_page';
            return $vars;
        }

        public function register_property_cpt() {

            $labels = array(
                'name'               => _x( 'Properties', 'Post Type General Name', 'realshed-core' ),
                'singular_name'      => _x( 'Property', 'Post Type Singular Name', 'realshed-core' ),
                'menu_name'          => __( 'Properties', 'realshed-core' ),
                'all_items'          => __( 'All Properties', 'realshed-core' ),
                'add_new_item'       => __( 'Add New Property', 'realshed-core' ),
                'add_new'            => __( 'Add New', 'realshed-core' ),
                'edit_item'          => __( 'Edit Property', 'realshed-core' ),
                'update_item'        => __( 'Update Property', 'realshed-core' ),
                'view_item'          => __( 'View Property', 'realshed-core' ),
                'search_items'       => __( 'Search Property', 'realshed-core' ),
                'not_found'          => __( 'No properties found', 'realshed-core' ),
                'not_found_in_trash' => __( 'No properties found in Trash', 'realshed-core' ),
            );

            $args = array(
                'label'               => __( 'Property', 'realshed-core' ),
                'description'         => __( 'Real Estate Properties for RealShed Marketplace', 'realshed-core' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
                'taxonomies'          => array( 'property_type', 'property_status' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'menu_position'       => 5,
                'menu_icon'           => 'dashicons-admin-home',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'has_archive'         => 'properties',
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'show_in_rest'        => true,
                'rewrite'             => array( 'slug' => 'property' ),
            );

            register_post_type( 'property', $args );
        }

        public function register_property_taxonomies() {

            register_taxonomy( 'property_type', array( 'property' ), array(
                'hierarchical'      => true,
                'labels'            => array(
                    'name'          => __( 'Property Types', 'realshed-core' ),
                    'singular_name' => __( 'Property Type', 'realshed-core' ),
                    'menu_name'     => __( 'Property Types', 'realshed-core' ),
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'show_in_rest'      => true,
                'rewrite'           => array( 'slug' => 'property-type' ),
            ) );

            register_taxonomy( 'property_status', array( 'property' ), array(
                'hierarchical'      => true,
                'labels'            => array(
                    'name'          => __( 'Property Status', 'realshed-core' ),
                    'singular_name' => __( 'Property Status', 'realshed-core' ),
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'rewrite'           => array( 'slug' => 'property-status' ),
            ) );

            register_taxonomy( 'property_location', array( 'property' ), array(
                'hierarchical'      => true,
                'labels'            => array(
                    'name'          => __( 'Locations', 'realshed-core' ),
                    'singular_name' => __( 'Location', 'realshed-core' ),
                    'menu_name'     => __( 'Locations', 'realshed-core' ),
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'show_in_rest'      => true,
                'rewrite'           => array( 'slug' => 'property-location' ),
            ) );

            register_taxonomy( 'property_amenity', array( 'property' ), array(
                'hierarchical'      => false,
                'labels'            => array(
                    'name'          => __( 'Amenities', 'realshed-core' ),
                    'singular_name' => __( 'Amenity', 'realshed-core' ),
                    'menu_name'     => __( 'Amenities', 'realshed-core' ),
                ),
                'show_ui'           => true,
                'show_admin_column' => false,
                'show_in_rest'      => true,
                'rewrite'           => array( 'slug' => 'amenity' ),
            ) );
        }
    }

    new Realshed_Property_CPT();
}
