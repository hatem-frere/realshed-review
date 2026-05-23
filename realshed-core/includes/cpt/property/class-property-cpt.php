<?php
/**
 * Property Custom Post Type Class
 *
 * Registers the 'property' post type and all property-related taxonomies.
 * Also registers the custom rewrite rule for /search-properties/.
 *
 * Path: wp-content/plugins/realshed-core/includes/cpt/property/class-property-cpt.php
 *
 * @package Realshed_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Realshed_Property_CPT' ) ) {

	/**
	 * Property CPT and taxonomy registration.
	 */
	class Realshed_Property_CPT {

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_property_cpt' ) );
			add_action( 'init', array( $this, 'register_property_taxonomies' ) );

			/*
			 * /search-properties/ rewrite rule.
			 *
			 * Important routing decision:
			 *
			 * /properties/ is owned by the Setup Wizard generated WordPress page.
			 * It should NOT be registered as the native CPT archive, because that
			 * creates a URL conflict between:
			 *
			 * 1. WordPress page: /properties/
			 * 2. CPT archive:    /properties/
			 *
			 * Therefore, the property CPT uses:
			 * 'has_archive' => false
			 *
			 * Single property URLs remain:
			 * /property/property-name/
			 *
			 * /search-properties/ remains unchanged and is routed using the
			 * custom query var 'realshed_page'.
			 *
			 * After deploying this change, go to:
			 * WordPress Admin > Settings > Permalinks > Save Changes
			 * to refresh rewrite rules.
			 */
			add_action( 'init', array( $this, 'add_search_properties_rewrite' ) );
			add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		}

		/**
		 * Add custom rewrite rule for /search-properties/.
		 *
		 * @return void
		 */
		public function add_search_properties_rewrite() {
			add_rewrite_rule(
				'^search-properties/?$',
				'index.php?post_type=property&realshed_page=search-properties',
				'top'
			);
		}

		/**
		 * Register custom query vars.
		 *
		 * @param array $vars Existing public query vars.
		 *
		 * @return array
		 */
		public function register_query_vars( $vars ) {
			$vars[] = 'realshed_page';

			return $vars;
		}

		/**
		 * Register the property custom post type.
		 *
		 * @return void
		 */
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
				'description'         => __( 'Real Estate Properties for Realshed Marketplace', 'realshed-core' ),
				'labels'              => $labels,
				'supports'            => array(
					'title',
					'editor',
					'excerpt',
					'author',
					'thumbnail',
					'comments',
					'revisions',
					'custom-fields',
				),
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

				/*
				 * Important:
				 * /properties/ is a Setup Wizard generated WordPress page.
				 * Do not create a competing CPT archive with the same slug.
				 */
				'has_archive'         => false,

				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_rest'        => true,

				/*
				 * Single property URLs:
				 * /property/property-name/
				 */
				'rewrite'             => array(
					'slug'       => 'property',
					'with_front' => false,
				),
			);

			register_post_type( 'property', $args );
		}

		/**
		 * Register property taxonomies.
		 *
		 * @return void
		 */
		public function register_property_taxonomies() {
			register_taxonomy(
				'property_type',
				array( 'property' ),
				array(
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
					'rewrite'           => array(
						'slug'       => 'property-type',
						'with_front' => false,
					),
				)
			);

			register_taxonomy(
				'property_status',
				array( 'property' ),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'          => __( 'Property Status', 'realshed-core' ),
						'singular_name' => __( 'Property Status', 'realshed-core' ),
						'menu_name'     => __( 'Property Status', 'realshed-core' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => true,
					'rewrite'           => array(
						'slug'       => 'property-status',
						'with_front' => false,
					),
				)
			);

			register_taxonomy(
				'property_location',
				array( 'property' ),
				array(
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
					'rewrite'           => array(
						'slug'       => 'property-location',
						'with_front' => false,
					),
				)
			);

			register_taxonomy(
				'property_amenity',
				array( 'property' ),
				array(
					'hierarchical'      => false,
					'labels'            => array(
						'name'          => __( 'Amenities', 'realshed-core' ),
						'singular_name' => __( 'Amenity', 'realshed-core' ),
						'menu_name'     => __( 'Amenities', 'realshed-core' ),
					),
					'show_ui'           => true,
					'show_admin_column' => false,
					'query_var'         => true,
					'show_in_rest'      => true,
					'rewrite'           => array(
						'slug'       => 'amenity',
						'with_front' => false,
					),
				)
			);
		}
	}

	new Realshed_Property_CPT();
}
