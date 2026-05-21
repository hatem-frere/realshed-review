<?php
/**
 * Redux Framework Configuration
 *
 * Path: wp-content/plugins/realshed-core/redux/config-template.php
 *
 * @package Realshed_Core
 */

if ( ! class_exists( 'Redux' ) ) {
	return;
}

$opt_name = 'realshed_options';
$theme    = wp_get_theme();

$args = array(
	'display_name'    => $theme->get( 'Name' ),
	'display_version' => $theme->get( 'Version' ),
	'menu_type'       => 'menu',
	'admin_bar'       => true,
	'menu_title'      => esc_html__( 'Realshed Options', 'realshed-core' ),
	'page_title'      => esc_html__( 'Realshed Options', 'realshed-core' ),
	'global_variable' => $opt_name,
	'page_slug'       => 'realshed_options',
	'save_defaults'   => true,
	'footer_credit'   => esc_html__( 'Created by Hatem Frere', 'realshed-core' ),
);

Redux::setArgs( $opt_name, $args );

// -------------------------------------------------------------------------
// Section 1 — Setup Wizard
// -------------------------------------------------------------------------
$realshed_core_instance = class_exists( 'Realshed_Core' ) ? Realshed_Core::instance() : null;

Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Setup Wizard', 'realshed-core' ),
		'id'     => 'setup_wizard',
		'icon'   => 'el el-magic',
		'fields' => array(
			array(
				'id'      => 'setup_wizard_content',
				'type'    => 'raw',
				'content' => $realshed_core_instance && method_exists( $realshed_core_instance, 'get_setup_wizard_redux_content' )
					? $realshed_core_instance->get_setup_wizard_redux_content()
					: esc_html__( 'Setup Wizard is currently unavailable.', 'realshed-core' ),
			),
		),
	)
);

// -------------------------------------------------------------------------
// Section 2 — Header Settings
// -------------------------------------------------------------------------
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Header Settings', 'realshed-core' ),
		'id'     => 'header_settings',
		'icon'   => 'el el-website',
		'fields' => array(
			array(
				'id'    => 'site_logo',
				'type'  => 'media',
				'url'   => true,
				'title' => esc_html__( 'Main Logo', 'realshed-core' ),
			),
			array(
				'id'      => 'header_address',
				'type'    => 'text',
				'title'   => esc_html__( 'Header Address', 'realshed-core' ),
				'default' => esc_html__( 'Discover St, New York, NY 10012, USA', 'realshed-core' ),
			),
			array(
				'id'      => 'header_working_hours',
				'type'    => 'text',
				'title'   => esc_html__( 'Header Working Hours', 'realshed-core' ),
				'default' => esc_html__( 'Mon - Sat 9.00 - 18.00', 'realshed-core' ),
			),
			array(
				'id'      => 'header_phone',
				'type'    => 'text',
				'title'   => esc_html__( 'Header Phone', 'realshed-core' ),
				'default' => '+251-235-3256',
			),
			array(
				'id'      => 'header_signin_url',
				'type'    => 'text',
				'title'   => esc_html__( 'Sign In URL', 'realshed-core' ),
				'default' => home_url( '/signin' ),
			),
			array(
				'id'      => 'header_add_listing_url',
				'type'    => 'text',
				'title'   => esc_html__( 'Add Listing URL', 'realshed-core' ),
				'default' => home_url( '/add-listing' ),
			),
			array(
				'id'      => 'header_add_listing_text',
				'type'    => 'text',
				'title'   => esc_html__( 'Add Listing Button Text', 'realshed-core' ),
				'default' => esc_html__( 'Add Listing', 'realshed-core' ),
			),
			array(
				'id'      => 'social_facebook_url',
				'type'    => 'text',
				'title'   => esc_html__( 'Facebook URL', 'realshed-core' ),
				'default' => home_url(),
			),
			array(
				'id'      => 'social_twitter_url',
				'type'    => 'text',
				'title'   => esc_html__( 'Twitter / X URL', 'realshed-core' ),
				'default' => home_url(),
			),
			array(
				'id'      => 'social_pinterest_url',
				'type'    => 'text',
				'title'   => esc_html__( 'Pinterest URL', 'realshed-core' ),
				'default' => home_url(),
			),
			array(
				'id'      => 'social_google_url',
				'type'    => 'text',
				'title'   => esc_html__( 'Google Plus URL', 'realshed-core' ),
				'default' => home_url(),
			),
			array(
				'id'      => 'social_vimeo_url',
				'type'    => 'text',
				'title'   => esc_html__( 'Vimeo URL', 'realshed-core' ),
				'default' => home_url(),
			),
		),
	)
);

// -------------------------------------------------------------------------
// Section 3 — Homepage Settings
// -------------------------------------------------------------------------
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Homepage Settings', 'realshed-core' ),
		'id'     => 'homepage_settings',
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => 'enable_custom_homepage',
				'type'    => 'switch',
				'title'   => esc_html__( 'Enable Custom Homepage', 'realshed-core' ),
				'default' => false,
			),
			array(
				'id'      => 'homepage_sections',
				'type'    => 'sorter',
				'title'   => esc_html__( 'Homepage Sections Manager', 'realshed-core' ),
				'options' => array(
					'enabled'  => array(
						'hero'     => esc_html__( 'Main Banner', 'realshed-core' ),
						'category' => esc_html__( 'Property Categories', 'realshed-core' ),
						'featured' => esc_html__( 'Featured Properties', 'realshed-core' ),
					),
					'disabled' => array(
						'deals' => esc_html__( 'Hot Deals', 'realshed-core' ),
						'video' => esc_html__( 'Video Section', 'realshed-core' ),
						'news'  => esc_html__( 'Latest News', 'realshed-core' ),
					),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// Section 4 — Banner / Hero Settings
// -------------------------------------------------------------------------
Redux::setSection(
	$opt_name,
	array(
		'title'      => esc_html__( 'Banner Settings', 'realshed-core' ),
		'id'         => 'banner_settings',
		'subsection' => true,
		'icon'       => 'el el-photo',
		'fields'     => array(
			array(
				'id'      => 'banner_bg',
				'type'    => 'media',
				'title'   => esc_html__( 'Banner Background', 'realshed-core' ),
				'default' => array(
					'url' => get_template_directory_uri() . '/assets/images/banner/banner-1.jpg',
				),
			),
			array(
				'id'      => 'banner_title',
				'type'    => 'text',
				'title'   => esc_html__( 'Banner Title', 'realshed-core' ),
				'default' => esc_html__( 'Create Lasting Wealth Through Local Real Estate', 'realshed-core' ),
			),
			array(
				'id'      => 'banner_desc',
				'type'    => 'textarea',
				'title'   => esc_html__( 'Banner Description', 'realshed-core' ),
				'default' => esc_html__( 'Amet consectetur adipisicing elit sed do eiusmod.', 'realshed-core' ),
			),
		),
	)
);

// -------------------------------------------------------------------------
// Section 5 — Search Settings
// -------------------------------------------------------------------------
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Search Settings', 'realshed-core' ),
		'id'     => 'search_settings',
		'icon'   => 'el el-search',
		'desc'   => esc_html__( 'Global configuration for the properties search system.', 'realshed-core' ),
		'fields' => array(
			array(
				'id'       => 'show_properties_search',
				'type'     => 'switch',
				'title'    => esc_html__( 'Enable Properties Search', 'realshed-core' ),
				'subtitle' => esc_html__( 'When enabled, the search form can be displayed using the shortcode.', 'realshed-core' ),
				'default'  => true,
			),
			array(
				'id'       => 'search_shortcode_info',
				'type'     => 'info',
				'required' => array( 'show_properties_search', '=', true ),
				'title'    => esc_html__( 'How to use:', 'realshed-core' ),
				'desc'     => sprintf(
					'%1$s<br><br><strong class="realshed-shortcode-preview">%2$s</strong>',
					esc_html__( 'Copy this shortcode and paste it where you want the form to appear:', 'realshed-core' ),
					esc_html__( '[realshed_properties_search]', 'realshed-core' )
				),
				'style'    => 'info',
			),
			array(
				'id'       => 'show_advanced_search',
				'type'     => 'switch',
				'required' => array( 'show_properties_search', '=', true ),
				'title'    => esc_html__( 'Enable Advanced Search Toggle', 'realshed-core' ),
				'default'  => true,
			),
		),
	)
);

// -------------------------------------------------------------------------
// Section 6 — Styling Options
// -------------------------------------------------------------------------
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Styling Options', 'realshed-core' ),
		'id'     => 'styling_settings',
		'icon'   => 'el el-brush',
		'fields' => array(
			array(
				'id'      => 'theme_color_scheme',
				'type'    => 'select',
				'title'   => esc_html__( 'Theme Color Scheme', 'realshed-core' ),
				'options' => array(
					'green'       => esc_html__( 'Green', 'realshed-core' ),
					'theme-color' => esc_html__( 'Blue', 'realshed-core' ),
					'crimson'     => esc_html__( 'Crimson', 'realshed-core' ),
					'orange'      => esc_html__( 'Orange', 'realshed-core' ),
					'pink'        => esc_html__( 'Pink', 'realshed-core' ),
					'violet'      => esc_html__( 'Violet', 'realshed-core' ),
				),
				'default' => 'green',
			),
		),
	)
);

// -------------------------------------------------------------------------
// Section 7 — Property Listing Styles
//
// Redux value → PHP layout file in property/partials/listing/
//
// layout-list-sidebar   → layout-list-sidebar.php
// layout-grid-sidebar   → layout-grid-sidebar.php
// layout-list-full-map  → layout-list-full-map.php
// layout-grid-full-map  → layout-grid-full-map.php
// layout-list-half-map  → layout-list-half-map.php
// layout-grid-half-map  → layout-grid-half-map.php
//
// The /search-properties/ page always uses layout-list-sidebar.
// Only /properties/ respects this option.
// -------------------------------------------------------------------------
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Property Listing Styles', 'realshed-core' ),
		'id'     => 'property_listing_styles',
		'icon'   => 'el el-screen',
		'desc'   => esc_html__( 'Choose the default layout for the /properties/ browse page. The search results page (/search-properties/) always uses the List + Sidebar layout.', 'realshed-core' ),
		'fields' => array(
			array(
				'id'       => 'property_style_selection',
				'type'     => 'button_set',
				'title'    => esc_html__( 'Listing Layout', 'realshed-core' ),
				'subtitle' => esc_html__( 'Select the layout for the property browse page.', 'realshed-core' ),
				'options'  => array(
					'layout-list-sidebar'  => esc_html__( 'List — Sidebar', 'realshed-core' ),
					'layout-grid-sidebar'  => esc_html__( 'Grid — Sidebar', 'realshed-core' ),
					'layout-list-full-map' => esc_html__( 'List — Full Map', 'realshed-core' ),
					'layout-grid-full-map' => esc_html__( 'Grid — Full Map', 'realshed-core' ),
					'layout-list-half-map' => esc_html__( 'List — Half Map', 'realshed-core' ),
					'layout-grid-half-map' => esc_html__( 'Grid — Half Map', 'realshed-core' ),
				),
				'default'  => 'layout-list-sidebar',
			),
		),
	)
);

// -------------------------------------------------------------------------
// Section 8 — APIs & Integrations
//
// Google Maps API key is used by:
// - map panels in full-map and half-map property layouts
// - future geolocation/distance filtering
// -------------------------------------------------------------------------
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'APIs & Integrations', 'realshed-core' ),
		'id'     => 'apis_settings',
		'icon'   => 'el el-cog',
		'fields' => array(
			array(
				'id'          => 'google_maps_api_key',
				'type'        => 'text',
				'title'       => esc_html__( 'Google Maps API Key', 'realshed-core' ),
				'subtitle'    => esc_html__( 'Required for property map layouts and the distance search filter.', 'realshed-core' ),
				'desc'        => sprintf(
					wp_kses(
						__( 'Get your key at <a href="%s" target="_blank" rel="noopener">Google Cloud Console</a>. Enable the Maps JavaScript API and Geocoding API.', 'realshed-core' ),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
								'rel'    => array(),
							),
						)
					),
					'https://console.cloud.google.com/apis/credentials'
				),
				'default'     => '',
				'placeholder' => 'AIzaSy...',
			),
			array(
				'id'      => 'map_default_lat',
				'type'    => 'text',
				'title'   => esc_html__( 'Default Map Latitude', 'realshed-core' ),
				'desc'    => esc_html__( 'Center latitude when no property is selected.', 'realshed-core' ),
				'default' => '40.712776',
			),
			array(
				'id'      => 'map_default_lng',
				'type'    => 'text',
				'title'   => esc_html__( 'Default Map Longitude', 'realshed-core' ),
				'desc'    => esc_html__( 'Center longitude when no property is selected.', 'realshed-core' ),
				'default' => '-74.005974',
			),
			array(
				'id'      => 'map_default_zoom',
				'type'    => 'slider',
				'title'   => esc_html__( 'Default Map Zoom Level', 'realshed-core' ),
				'desc'    => esc_html__( '1 = world, 12 = city, 17 = street.', 'realshed-core' ),
				'default' => 12,
				'min'     => 1,
				'max'     => 20,
				'step'    => 1,
			),
		),
	)
);
