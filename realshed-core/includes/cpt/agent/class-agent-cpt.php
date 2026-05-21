<?php
/**
 * Agent Custom Post Type Class
 *
 * Registers the 'agent' post type to manage real estate agents/realtors.
 *
 * @package Realshed_Core
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Realshed_Agent_CPT' ) ) {

    class Realshed_Agent_CPT {

        /**
         * Constructor
         */
        public function __construct() {
            // Register the CPT on init
            add_action( 'init', array( $this, 'register_agent_cpt' ) );

            // Register Agent-specific Taxonomies (e.g., Service Areas or Specialties)
            add_action( 'init', array( $this, 'register_agent_taxonomies' ) );
        }

        /**
         * Register Agent Post Type
         */
        public function register_agent_cpt() {

            $labels = array(
                'name'               => _x( 'Agents', 'Post Type General Name', 'realshed-core' ),
                'singular_name'      => _x( 'Agent', 'Post Type Singular Name', 'realshed-core' ),
                'menu_name'          => __( 'Agents', 'realshed-core' ),
                'all_items'          => __( 'All Agents', 'realshed-core' ),
                'add_new_item'       => __( 'Add New Agent', 'realshed-core' ),
                'add_new'            => __( 'Add New', 'realshed-core' ),
                'edit_item'          => __( 'Edit Agent', 'realshed-core' ),
                'update_item'        => __( 'Update Agent', 'realshed-core' ),
                'view_item'          => __( 'View Agent', 'realshed-core' ),
                'search_items'       => __( 'Search Agents', 'realshed-core' ),
                'not_found'          => __( 'No agents found', 'realshed-core' ),
                'not_found_in_trash' => __( 'No agents found in Trash', 'realshed-core' ),
            );

            $args = array(
                'label'               => __( 'Agent', 'realshed-core' ),
                'description'         => __( 'Real Estate Agents for RealShed Marketplace', 'realshed-core' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'menu_position'       => 6, // Placed right below Properties
                'menu_icon'           => 'dashicons-businessman', // Professional person icon
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'show_in_rest'        => true, // Gutenberg support
                'rewrite'             => array( 'slug' => 'agent' ),
            );

            register_post_type( 'agent', $args );
        }

        /**
         * Register Agent Taxonomies
         */
        public function register_agent_taxonomies() {

            // Agent Specialties (e.g., Luxury Homes, Commercial, Rentals)
            register_taxonomy( 'agent_specialty', array( 'agent' ), array(
                'hierarchical'      => true,
                'labels'            => array(
                    'name'              => __( 'Specialties', 'realshed-core' ),
                    'singular_name'     => __( 'Specialty', 'realshed-core' ),
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'rewrite'           => array( 'slug' => 'agent-specialty' ),
            ));
        }
    }

    // Initialize the class
    new Realshed_Agent_CPT();
}
