<?php
/**
 * Property Archive Template
 *
 * Handles:
 *
 * /properties/
 * - Setup Wizard generated WordPress page.
 * - Uses a custom WP_Query for real "property" CPT posts.
 * - Layout is controlled by Redux option: property_style_selection.
 *
 * /search-properties/
 * - Custom rewrite page.
 * - Uses a custom WP_Query for real "property" CPT posts.
 * - Layout is fixed to list + sidebar.
 *
 * Property taxonomies:
 * - property_type
 * - property_status
 * - property_location
 *
 * Path: wp-content/themes/realshed/property/archive-property.php
 *
 * @package Realshed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $realshed_options;

/**
 * Get current property archive context.
 *
 * @return string
 */
if ( ! function_exists( 'realshed_property_get_archive_context' ) ) {
	function realshed_property_get_archive_context() {
		$context = get_query_var( 'realshed_archive_context', 'browse' );

		if ( 'search' === $context ) {
			return 'search';
		}

		return 'browse';
	}
}

/**
 * Get active property listing layout.
 *
 * @param string $context Current archive context.
 *
 * @return string
 */
if ( ! function_exists( 'realshed_property_get_archive_layout' ) ) {
	function realshed_property_get_archive_layout( $context ) {
		global $realshed_options;

		if ( 'search' === $context ) {
			return 'layout-list-sidebar';
		}

		if ( ! empty( $realshed_options['property_style_selection'] ) ) {
			return sanitize_key( $realshed_options['property_style_selection'] );
		}

		return 'layout-list-sidebar';
	}
}

/**
 * Determine page structure from selected layout.
 *
 * @param string $layout Selected layout option.
 *
 * @return string
 */
if ( ! function_exists( 'realshed_property_get_archive_structure' ) ) {
	function realshed_property_get_archive_structure( $layout ) {
		if ( false !== strpos( $layout, 'half-map' ) ) {
			return 'half-map';
		}

		if ( false !== strpos( $layout, 'full-map' ) ) {
			return 'full-map';
		}

		return 'sidebar';
	}
}

/**
 * Return browse/search page URL.
 *
 * @param string $context Current archive context.
 *
 * @return string
 */
if ( ! function_exists( 'realshed_property_get_archive_action_url' ) ) {
	function realshed_property_get_archive_action_url( $context ) {
		if ( 'search' === $context ) {
			return home_url( '/search-properties/' );
		}

		$properties_page = get_page_by_path( 'properties' );

		if ( $properties_page instanceof WP_Post ) {
			return get_permalink( $properties_page );
		}

		return home_url( '/properties/' );
	}
}

/**
 * Build property archive query args.
 *
 * @param string $context Current archive context.
 *
 * @return array
 */
if ( ! function_exists( 'realshed_property_get_query_args' ) ) {
	function realshed_property_get_query_args( $context ) {
		$paged = max(
			1,
			absint( get_query_var( 'paged' ) ),
			absint( get_query_var( 'page' ) )
		);

		$args = array(
			'post_type'           => 'property',
			'post_status'         => 'publish',
			'posts_per_page'      => (int) get_option( 'posts_per_page', 10 ),
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
		);

		$keyword = '';

		if ( ! empty( $_GET['realshed_search'] ) ) {
			$keyword = sanitize_text_field( wp_unslash( $_GET['realshed_search'] ) );
		} elseif ( ! empty( $_GET['s'] ) ) {
			$keyword = sanitize_text_field( wp_unslash( $_GET['s'] ) );
		}

		if ( '' !== $keyword ) {
			$args['s'] = $keyword;
		}

		$tax_query = array();

		$taxonomy_map = array(
			's_type'     => 'property_type',
			's_status'   => 'property_status',
			's_location' => 'property_location',
		);

		foreach ( $taxonomy_map as $request_key => $taxonomy ) {
			if ( empty( $_GET[ $request_key ] ) ) {
				continue;
			}

			$value = sanitize_text_field( wp_unslash( $_GET[ $request_key ] ) );

			if ( '' === $value ) {
				continue;
			}

			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $value,
			);
		}

		if ( is_tax( array( 'property_type', 'property_status', 'property_location' ) ) ) {
			$queried_object = get_queried_object();

			if ( $queried_object instanceof WP_Term ) {
				$tax_query[] = array(
					'taxonomy' => $queried_object->taxonomy,
					'field'    => 'term_id',
					'terms'    => absint( $queried_object->term_id ),
				);
			}
		}

		if ( ! empty( $tax_query ) ) {
			if ( count( $tax_query ) > 1 ) {
				$tax_query['relation'] = 'AND';
			}

			$args['tax_query'] = $tax_query;
		}

		$meta_query = array();

		if ( ! empty( $_GET['s_price_min'] ) ) {
			$meta_query[] = array(
				'key'     => '_property_price',
				'value'   => floatval( wp_unslash( $_GET['s_price_min'] ) ),
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		}

		if ( ! empty( $_GET['s_price_max'] ) ) {
			$meta_query[] = array(
				'key'     => '_property_price',
				'value'   => floatval( wp_unslash( $_GET['s_price_max'] ) ),
				'type'    => 'NUMERIC',
				'compare' => '<=',
			);
		}

		if ( ! empty( $_GET['s_area_min'] ) ) {
			$meta_query[] = array(
				'key'     => '_property_area',
				'value'   => floatval( wp_unslash( $_GET['s_area_min'] ) ),
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		}

		if ( ! empty( $_GET['s_area_max'] ) ) {
			$meta_query[] = array(
				'key'     => '_property_area',
				'value'   => floatval( wp_unslash( $_GET['s_area_max'] ) ),
				'type'    => 'NUMERIC',
				'compare' => '<=',
			);
		}

		if ( ! empty( $meta_query ) ) {
			if ( count( $meta_query ) > 1 ) {
				$meta_query['relation'] = 'AND';
			}

			$args['meta_query'] = $meta_query;
		}

		$sort = '';

		if ( ! empty( $_GET['s_sort'] ) ) {
			$sort = sanitize_key( wp_unslash( $_GET['s_sort'] ) );
		}

		switch ( $sort ) {
			case 'price-low':
				$args['meta_key'] = '_property_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;

			case 'price-high':
				$args['meta_key'] = '_property_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'newest':
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}

		/**
		 * Allow child themes/plugins to adjust property archive query.
		 *
		 * @param array  $args    Query arguments.
		 * @param string $context Archive context.
		 */
		return apply_filters( 'realshed_property_archive_query_args', $args, $context );
	}
}

/**
 * Render sort form.
 *
 * @param string $context Current archive context.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_sort_form' ) ) {
	function realshed_property_sort_form( $context ) {
		$preserved = array(
			'post_type',
			'realshed_search',
			's_status',
			's_type',
			's_location',
			's',
			's_distance',
			's_rooms',
			's_bath',
			's_floor',
			's_agency',
			's_price_min',
			's_price_max',
			's_area_min',
			's_area_max',
		);

		$selected_sort = ! empty( $_GET['s_sort'] ) ? sanitize_key( wp_unslash( $_GET['s_sort'] ) ) : 'newest';
		?>
		<form method="get" id="sort-form" action="<?php echo esc_url( realshed_property_get_archive_action_url( $context ) ); ?>">
			<?php foreach ( $preserved as $param ) : ?>
				<?php if ( ! empty( $_GET[ $param ] ) ) : ?>
					<input type="hidden" name="<?php echo esc_attr( $param ); ?>" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) ); ?>">
				<?php endif; ?>
			<?php endforeach; ?>

			<div class="select-box">
				<select class="wide" name="s_sort" onchange="document.getElementById('sort-form').submit()">
					<option value="newest" <?php selected( $selected_sort, 'newest' ); ?>><?php esc_html_e( 'Sort: Newest', 'realshed' ); ?></option>
					<option value="price-low" <?php selected( $selected_sort, 'price-low' ); ?>><?php esc_html_e( 'Price: Low to High', 'realshed' ); ?></option>
					<option value="price-high" <?php selected( $selected_sort, 'price-high' ); ?>><?php esc_html_e( 'Price: High to Low', 'realshed' ); ?></option>
				</select>
			</div>
		</form>
		<?php
	}
}

/**
 * Render property shorting bar.
 *
 * @param WP_Query $property_query      Property query object.
 * @param string   $context             Current archive context.
 * @param bool     $show_layout_buttons Whether to show grid/list buttons.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_shorting_bar' ) ) {
	function realshed_property_shorting_bar( $property_query, $context, $show_layout_buttons = true ) {
		$count = absint( $property_query->found_posts );
		?>
		<div class="item-shorting clearfix">
			<div class="left-column pull-left">
				<h5>
					<?php esc_html_e( 'Search Results:', 'realshed' ); ?>
					<span>
						<?php
						if ( $count > 0 ) {
							printf(
								esc_html( _n( 'Showing %s Listing', 'Showing %s Listings', $count, 'realshed' ) ),
								esc_html( number_format_i18n( $count ) )
							);
						} else {
							esc_html_e( 'No Listings Found', 'realshed' );
						}
						?>
					</span>
				</h5>
			</div>

			<div class="right-column pull-right clearfix">
				<div class="short-box clearfix">
					<?php realshed_property_sort_form( $context ); ?>
				</div>

				<?php if ( $show_layout_buttons ) : ?>
					<div class="short-menu clearfix">
						<button type="button" class="list-view on"><i class="icon-35"></i></button>
						<button type="button" class="grid-view"><i class="icon-36"></i></button>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

/**
 * Render property pagination for a custom query.
 *
 * @param WP_Query $property_query Property query object.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_archive_pagination' ) ) {
	function realshed_property_archive_pagination( $property_query ) {
		if ( $property_query->max_num_pages <= 1 ) {
			return;
		}

		$current = max(
			1,
			absint( get_query_var( 'paged' ) ),
			absint( get_query_var( 'page' ) )
		);

		$links = paginate_links(
			array(
				'total'     => (int) $property_query->max_num_pages,
				'current'   => $current,
				'type'      => 'list',
				'prev_text' => esc_html__( 'Prev', 'realshed' ),
				'next_text' => esc_html__( 'Next', 'realshed' ),
			)
		);

		if ( $links ) {
			echo '<div class="pagination-wrapper">' . wp_kses_post( $links ) . '</div>';
		}
	}
}

/**
 * Render property loop.
 *
 * @param WP_Query $property_query Property query object.
 * @param string   $layout         Active layout.
 * @param string   $wrapper_class  Wrapper class.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_loop' ) ) {
	function realshed_property_loop( $property_query, $layout, $wrapper_class ) {
		if ( false !== strpos( $layout, 'full-map' ) ) {
			$list_partial = 'partials/listing/layout-list-full-map';
			$grid_partial = 'partials/listing/layout-grid-full-map';
		} elseif ( false !== strpos( $layout, 'half-map' ) ) {
			$list_partial = 'partials/listing/layout-list-half-map';
			$grid_partial = 'partials/listing/layout-grid-half-map';
		} else {
			$list_partial = 'partials/listing/layout-list-sidebar';
			$grid_partial = 'partials/listing/layout-grid-sidebar';
		}
		?>
		<div class="wrapper <?php echo esc_attr( $wrapper_class ); ?>">
			<?php if ( $property_query->have_posts() ) : ?>
				<div class="deals-list-content list-item">
					<?php
					while ( $property_query->have_posts() ) :
						$property_query->the_post();

						get_template_part( 'property/' . $list_partial );
					endwhile;
					?>
				</div>

				<?php $property_query->rewind_posts(); ?>

				<div class="deals-grid-content grid-item">
					<div class="row clearfix">
						<?php
						while ( $property_query->have_posts() ) :
							$property_query->the_post();
							?>
							<div class="col-lg-6 col-md-6 col-sm-12 feature-block">
								<?php get_template_part( 'property/' . $grid_partial ); ?>
							</div>
							<?php
						endwhile;
						?>
					</div>
				</div>

				<?php realshed_property_archive_pagination( $property_query ); ?>

			<?php else : ?>
				<div class="no-results-message text-center" style="padding:100px 30px;background:#f7f7f7;border-radius:8px;margin-top:30px;">
					<div class="icon" style="font-size:80px;color:#d1d1d1;margin-bottom:20px;">
						<i class="icon-35"></i>
					</div>

					<h3 style="font-weight:700;color:#222;">
						<?php esc_html_e( 'No Properties Found', 'realshed' ); ?>
					</h3>

					<p style="color:#777;max-width:400px;margin:0 auto;">
						<?php esc_html_e( 'We couldn\'t find any properties matching your criteria. Try adjusting your filters.', 'realshed' ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php

		wp_reset_postdata();
	}
}

/**
 * Render map placeholder.
 *
 * @param string $lat         Latitude.
 * @param string $lng         Longitude.
 * @param int    $zoom        Zoom level.
 * @param string $api_key     Google Maps API key.
 * @param string $extra_class Extra CSS class.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_map_placeholder' ) ) {
	function realshed_property_map_placeholder( $lat, $lng, $zoom, $api_key, $extra_class = '' ) {
		?>
		<div
			class="google-map <?php echo esc_attr( $extra_class ); ?>"
			id="realshed-property-map"
			data-lat="<?php echo esc_attr( $lat ); ?>"
			data-lng="<?php echo esc_attr( $lng ); ?>"
			data-zoom="<?php echo esc_attr( $zoom ); ?>"
			style="width:100%;height:500px;background:#e8e8e8;display:flex;align-items:center;justify-content:center;"
		>
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
}

$context       = realshed_property_get_archive_context();
$layout        = realshed_property_get_archive_layout( $context );
$structure     = realshed_property_get_archive_structure( $layout );
$wrapper_class = ( false !== strpos( $layout, 'grid' ) ) ? 'grid' : 'list';

$maps_api_key = ! empty( $realshed_options['google_maps_api_key'] ) ? $realshed_options['google_maps_api_key'] : '';
$map_lat      = ! empty( $realshed_options['map_default_lat'] ) ? $realshed_options['map_default_lat'] : '40.712776';
$map_lng      = ! empty( $realshed_options['map_default_lng'] ) ? $realshed_options['map_default_lng'] : '-74.005974';
$map_zoom     = ! empty( $realshed_options['map_default_zoom'] ) ? absint( $realshed_options['map_default_zoom'] ) : 12;

$property_query = new WP_Query( realshed_property_get_query_args( $context ) );
?>

<?php if ( 'sidebar' === $structure ) : ?>
	<section class="property-page-section property-<?php echo esc_attr( $wrapper_class ); ?>">
		<div class="auto-container">
			<div class="row clearfix">
				<div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
					<?php
					if ( is_active_sidebar( 'property_sidebar' ) ) {
						dynamic_sidebar( 'property_sidebar' );
					}
					?>
				</div>

				<div class="col-lg-8 col-md-12 col-sm-12 content-side">
					<div class="property-content-side">
						<?php realshed_property_shorting_bar( $property_query, $context ); ?>
						<?php realshed_property_loop( $property_query, $layout, $wrapper_class ); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( 'full-map' === $structure ) : ?>
	<section class="property-page-section property-map-section property-<?php echo esc_attr( $wrapper_class ); ?>">
		<div class="map-area">
			<?php realshed_property_map_placeholder( $map_lat, $map_lng, $map_zoom, $maps_api_key, 'full-map' ); ?>
		</div>

		<div class="auto-container">
			<div class="row clearfix">
				<div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
					<?php
					if ( is_active_sidebar( 'property_sidebar' ) ) {
						dynamic_sidebar( 'property_sidebar' );
					}
					?>
				</div>

				<div class="col-lg-8 col-md-12 col-sm-12 content-side">
					<div class="property-content-side">
						<?php realshed_property_shorting_bar( $property_query, $context ); ?>
						<?php realshed_property_loop( $property_query, $layout, $wrapper_class ); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( 'half-map' === $structure ) : ?>
	<section class="deals-style-two property-<?php echo esc_attr( $wrapper_class ); ?>">
		<div class="page-content">
			<div class="left-column">
				<?php realshed_property_map_placeholder( $map_lat, $map_lng, $map_zoom, $maps_api_key, 'half-map sticky-map' ); ?>
			</div>

			<div class="right-column">
				<div class="property-content-side">
					<?php realshed_property_shorting_bar( $property_query, $context, false ); ?>
					<?php realshed_property_loop( $property_query, $layout, $wrapper_class ); ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
wp_reset_postdata();

get_footer();
