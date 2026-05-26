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
 * - Shows a property filter sidebar automatically.
 *
 * /search-properties/
 * - Custom search results page.
 * - Shows filtered property results only when filters/search parameters exist.
 * - If opened directly without filters, it shows a search prompt instead of all properties.
 *
 * Property taxonomies:
 * - property_type
 * - property_status
 * - property_location
 * - property_amenity
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
 * Check if the current request contains real search/filter parameters.
 *
 * Sorting and pagination are intentionally ignored because they should not
 * make /search-properties/ behave as a full browse page.
 *
 * @return bool
 */
if ( ! function_exists( 'realshed_property_has_search_filters' ) ) {
	function realshed_property_has_search_filters() {
		$filter_keys = array(
			'realshed_search',
			's',
			's_type',
			's_status',
			's_location',
			's_amenities',
			's_price_min',
			's_price_max',
			's_area_min',
			's_area_max',
			's_rooms',
			's_bath',
			's_floor',
			's_agency',
		);

		foreach ( $filter_keys as $key ) {
			if ( ! isset( $_GET[ $key ] ) ) {
				continue;
			}

			$value = wp_unslash( $_GET[ $key ] );

			if ( is_array( $value ) ) {
				$value = array_filter(
					array_map(
						static function ( $item ) {
							return trim( sanitize_text_field( $item ) );
						},
						$value
					)
				);

				if ( ! empty( $value ) ) {
					return true;
				}

				continue;
			}

			if ( '' !== trim( sanitize_text_field( $value ) ) ) {
				return true;
			}
		}

		return false;
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
			$search_page = get_page_by_path( 'search-properties' );

			if ( $search_page instanceof WP_Post ) {
				return get_permalink( $search_page );
			}

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
 * Get selected request value.
 *
 * @param string $key     Request key.
 * @param string $default Default value.
 *
 * @return string
 */
if ( ! function_exists( 'realshed_property_get_request_value' ) ) {
	function realshed_property_get_request_value( $key, $default = '' ) {
		if ( ! isset( $_GET[ $key ] ) ) {
			return $default;
		}

		$value = wp_unslash( $_GET[ $key ] );

		if ( is_array( $value ) ) {
			return $default;
		}

		return sanitize_text_field( $value );
	}
}

/**
 * Build property archive query args.
 *
 * @param string $context            Current archive context.
 * @param bool   $has_search_filters Whether search filters exist.
 *
 * @return array
 */
if ( ! function_exists( 'realshed_property_get_query_args' ) ) {
	function realshed_property_get_query_args( $context, $has_search_filters = true ) {
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

		/*
		 * /search-properties/ should not display all properties when opened
		 * directly without filters. It should wait for the user to search.
		 */
		if ( 'search' === $context && ! $has_search_filters ) {
			$args['post__in'] = array( 0 );

			return apply_filters( 'realshed_property_archive_query_args', $args, $context );
		}

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

		if ( ! empty( $_GET['s_amenities'] ) ) {
			$amenities = wp_unslash( $_GET['s_amenities'] );

			if ( ! is_array( $amenities ) ) {
				$amenities = array( $amenities );
			}

			$amenities = array_filter(
				array_map(
					static function ( $amenity ) {
						return sanitize_text_field( $amenity );
					},
					$amenities
				)
			);

			if ( ! empty( $amenities ) ) {
				$tax_query[] = array(
					'taxonomy' => 'property_amenity',
					'field'    => 'slug',
					'terms'    => $amenities,
					'operator' => 'AND',
				);
			}
		}

		if ( is_tax( array( 'property_type', 'property_status', 'property_location', 'property_amenity' ) ) ) {
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

		if ( ! empty( $_GET['s_rooms'] ) ) {
			$meta_query[] = array(
				'key'     => '_property_rooms',
				'value'   => absint( wp_unslash( $_GET['s_rooms'] ) ),
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		}

		if ( ! empty( $_GET['s_bath'] ) ) {
			$meta_query[] = array(
				'key'     => '_property_bathrooms',
				'value'   => absint( wp_unslash( $_GET['s_bath'] ) ),
				'type'    => 'NUMERIC',
				'compare' => '>=',
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
 * Render taxonomy dropdown options.
 *
 * @param string $taxonomy      Taxonomy name.
 * @param string $selected_slug Selected term slug.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_taxonomy_options' ) ) {
	function realshed_property_taxonomy_options( $taxonomy, $selected_slug = '' ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		foreach ( $terms as $term ) {
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $term->slug ),
				selected( $selected_slug, $term->slug, false ),
				esc_html( $term->name )
			);
		}
	}
}

/**
 * Render default property filter form.
 *
 * This appears automatically when the Property Sidebar has no widgets.
 *
 * @param string $context Current archive context.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_default_filter_form' ) ) {
	function realshed_property_default_filter_form( $context ) {
		$action_url        = realshed_property_get_archive_action_url( 'search' );
		$keyword          = realshed_property_get_request_value( 'realshed_search' );
		$selected_type    = realshed_property_get_request_value( 's_type' );
		$selected_status  = realshed_property_get_request_value( 's_status' );
		$selected_location = realshed_property_get_request_value( 's_location' );
		$price_min        = realshed_property_get_request_value( 's_price_min' );
		$price_max        = realshed_property_get_request_value( 's_price_max' );
		$area_min         = realshed_property_get_request_value( 's_area_min' );
		$area_max         = realshed_property_get_request_value( 's_area_max' );
		$rooms            = realshed_property_get_request_value( 's_rooms' );
		$baths            = realshed_property_get_request_value( 's_bath' );
		?>
		<div class="property-sidebar default-property-sidebar">
			<div class="filter-widget sidebar-widget">
				<div class="widget-title">
					<h4><?php esc_html_e( 'Find Your Property', 'realshed' ); ?></h4>
				</div>

				<div class="widget-content">
					<form method="get" action="<?php echo esc_url( $action_url ); ?>" class="realshed-property-filter-form">
						<div class="form-group">
							<label for="realshed_search"><?php esc_html_e( 'Keyword', 'realshed' ); ?></label>
							<input
								type="text"
								id="realshed_search"
								name="realshed_search"
								value="<?php echo esc_attr( $keyword ); ?>"
								placeholder="<?php esc_attr_e( 'Search by title or keyword', 'realshed' ); ?>"
							>
						</div>

						<div class="form-group">
							<label for="s_location"><?php esc_html_e( 'Location', 'realshed' ); ?></label>
							<select id="s_location" name="s_location" class="wide">
								<option value=""><?php esc_html_e( 'Any Location', 'realshed' ); ?></option>
								<?php realshed_property_taxonomy_options( 'property_location', $selected_location ); ?>
							</select>
						</div>

						<div class="form-group">
							<label for="s_type"><?php esc_html_e( 'Property Type', 'realshed' ); ?></label>
							<select id="s_type" name="s_type" class="wide">
								<option value=""><?php esc_html_e( 'Any Type', 'realshed' ); ?></option>
								<?php realshed_property_taxonomy_options( 'property_type', $selected_type ); ?>
							</select>
						</div>

						<div class="form-group">
							<label for="s_status"><?php esc_html_e( 'Property Status', 'realshed' ); ?></label>
							<select id="s_status" name="s_status" class="wide">
								<option value=""><?php esc_html_e( 'Any Status', 'realshed' ); ?></option>
								<?php realshed_property_taxonomy_options( 'property_status', $selected_status ); ?>
							</select>
						</div>

						<div class="form-group">
							<label><?php esc_html_e( 'Price Range', 'realshed' ); ?></label>
							<div class="row clearfix">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input
										type="number"
										name="s_price_min"
										value="<?php echo esc_attr( $price_min ); ?>"
										placeholder="<?php esc_attr_e( 'Min', 'realshed' ); ?>"
										min="0"
									>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input
										type="number"
										name="s_price_max"
										value="<?php echo esc_attr( $price_max ); ?>"
										placeholder="<?php esc_attr_e( 'Max', 'realshed' ); ?>"
										min="0"
									>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label><?php esc_html_e( 'Size Range', 'realshed' ); ?></label>
							<div class="row clearfix">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input
										type="number"
										name="s_area_min"
										value="<?php echo esc_attr( $area_min ); ?>"
										placeholder="<?php esc_attr_e( 'Min Area', 'realshed' ); ?>"
										min="0"
									>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input
										type="number"
										name="s_area_max"
										value="<?php echo esc_attr( $area_max ); ?>"
										placeholder="<?php esc_attr_e( 'Max Area', 'realshed' ); ?>"
										min="0"
									>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label><?php esc_html_e( 'Rooms & Bathrooms', 'realshed' ); ?></label>
							<div class="row clearfix">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input
										type="number"
										name="s_rooms"
										value="<?php echo esc_attr( $rooms ); ?>"
										placeholder="<?php esc_attr_e( 'Rooms', 'realshed' ); ?>"
										min="0"
									>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input
										type="number"
										name="s_bath"
										value="<?php echo esc_attr( $baths ); ?>"
										placeholder="<?php esc_attr_e( 'Baths', 'realshed' ); ?>"
										min="0"
									>
								</div>
							</div>
						</div>

						<div class="form-group">
							<button type="submit" class="theme-btn btn-one">
								<?php esc_html_e( 'Search Properties', 'realshed' ); ?>
							</button>
						</div>

						<?php if ( 'search' === $context && realshed_property_has_search_filters() ) : ?>
							<div class="form-group">
								<a href="<?php echo esc_url( realshed_property_get_archive_action_url( 'search' ) ); ?>" class="reset-filter-link">
									<?php esc_html_e( 'Reset Search', 'realshed' ); ?>
								</a>
							</div>
						<?php endif; ?>
					</form>
				</div>
			</div>

			<?php
			$amenities = get_terms(
				array(
					'taxonomy'   => 'property_amenity',
					'hide_empty' => false,
				)
			);

			if ( ! is_wp_error( $amenities ) && ! empty( $amenities ) ) :
				$selected_amenities = isset( $_GET['s_amenities'] ) ? (array) wp_unslash( $_GET['s_amenities'] ) : array();
				$selected_amenities = array_map( 'sanitize_text_field', $selected_amenities );
				?>
				<div class="amenities-widget sidebar-widget">
					<div class="widget-title">
						<h4><?php esc_html_e( 'Amenities', 'realshed' ); ?></h4>
					</div>

					<div class="widget-content">
						<form method="get" action="<?php echo esc_url( $action_url ); ?>">
							<?php
							$preserved = array(
								'realshed_search',
								's_location',
								's_type',
								's_status',
								's_price_min',
								's_price_max',
								's_area_min',
								's_area_max',
								's_rooms',
								's_bath',
							);

							foreach ( $preserved as $param ) :
								$value = realshed_property_get_request_value( $param );

								if ( '' === $value ) {
									continue;
								}
								?>
								<input type="hidden" name="<?php echo esc_attr( $param ); ?>" value="<?php echo esc_attr( $value ); ?>">
							<?php endforeach; ?>

							<ul class="custom-controls-stacked">
								<?php foreach ( $amenities as $amenity ) : ?>
									<li>
										<label class="custom-control material-checkbox">
											<input
												type="checkbox"
												name="s_amenities[]"
												value="<?php echo esc_attr( $amenity->slug ); ?>"
												<?php checked( in_array( $amenity->slug, $selected_amenities, true ) ); ?>
												onchange="this.form.submit();"
											>
											<span class="material-control-indicator"></span>
											<span class="description">
												<?php echo esc_html( $amenity->name ); ?>
												<?php if ( isset( $amenity->count ) ) : ?>
													<span>(<?php echo esc_html( number_format_i18n( $amenity->count ) ); ?>)</span>
												<?php endif; ?>
											</span>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						</form>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 * Render property sidebar.
 *
 * If the user has assigned widgets, show them.
 * If no widgets exist, show the default filter form automatically.
 *
 * @param string $context Current archive context.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_render_sidebar' ) ) {
	function realshed_property_render_sidebar( $context ) {
		if ( is_active_sidebar( 'property_sidebar' ) ) {
			dynamic_sidebar( 'property_sidebar' );
			return;
		}

		realshed_property_default_filter_form( $context );
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

			<?php
			if ( ! empty( $_GET['s_amenities'] ) ) :
				$amenities = wp_unslash( $_GET['s_amenities'] );

				if ( ! is_array( $amenities ) ) {
					$amenities = array( $amenities );
				}

				foreach ( $amenities as $amenity ) :
					?>
					<input type="hidden" name="s_amenities[]" value="<?php echo esc_attr( sanitize_text_field( $amenity ) ); ?>">
					<?php
				endforeach;
			endif;
			?>

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
 * Render no results/search prompt.
 *
 * @param string $context            Current archive context.
 * @param bool   $has_search_filters Whether search filters exist.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_no_results_message' ) ) {
	function realshed_property_no_results_message( $context, $has_search_filters ) {
		$is_empty_search_page = ( 'search' === $context && ! $has_search_filters );
		?>
		<div class="no-results-message text-center" style="padding:100px 30px;background:#f7f7f7;border-radius:8px;margin-top:30px;">
			<div class="icon" style="font-size:80px;color:#d1d1d1;margin-bottom:20px;">
				<i class="icon-35"></i>
			</div>

			<?php if ( $is_empty_search_page ) : ?>
				<h3 style="font-weight:700;color:#222;">
					<?php esc_html_e( 'Start Your Property Search', 'realshed' ); ?>
				</h3>

				<p style="color:#777;max-width:520px;margin:0 auto;">
					<?php esc_html_e( 'Use the filters on the left to search for properties by keyword, location, type, status, price, or size.', 'realshed' ); ?>
				</p>
			<?php else : ?>
				<h3 style="font-weight:700;color:#222;">
					<?php esc_html_e( 'No Properties Found', 'realshed' ); ?>
				</h3>

				<p style="color:#777;max-width:520px;margin:0 auto;">
					<?php esc_html_e( 'We couldn\'t find any properties matching your criteria. Try adjusting your filters.', 'realshed' ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 * Render property loop.
 *
 * @param WP_Query $property_query      Property query object.
 * @param string   $layout              Active layout.
 * @param string   $wrapper_class       Wrapper class.
 * @param string   $context             Current archive context.
 * @param bool     $has_search_filters  Whether search filters exist.
 *
 * @return void
 */
if ( ! function_exists( 'realshed_property_loop' ) ) {
	function realshed_property_loop( $property_query, $layout, $wrapper_class, $context, $has_search_filters ) {
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
				<?php realshed_property_no_results_message( $context, $has_search_filters ); ?>
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

$context            = realshed_property_get_archive_context();
$has_search_filters = realshed_property_has_search_filters();
$layout             = realshed_property_get_archive_layout( $context );
$structure          = realshed_property_get_archive_structure( $layout );
$wrapper_class      = ( false !== strpos( $layout, 'grid' ) ) ? 'grid' : 'list';

$maps_api_key = ! empty( $realshed_options['google_maps_api_key'] ) ? $realshed_options['google_maps_api_key'] : '';
$map_lat      = ! empty( $realshed_options['map_default_lat'] ) ? $realshed_options['map_default_lat'] : '40.712776';
$map_lng      = ! empty( $realshed_options['map_default_lng'] ) ? $realshed_options['map_default_lng'] : '-74.005974';
$map_zoom     = ! empty( $realshed_options['map_default_zoom'] ) ? absint( $realshed_options['map_default_zoom'] ) : 12;

$property_query = new WP_Query( realshed_property_get_query_args( $context, $has_search_filters ) );
?>

<?php if ( 'sidebar' === $structure ) : ?>
	<section class="property-page-section property-<?php echo esc_attr( $wrapper_class ); ?>">
		<div class="auto-container">
			<div class="row clearfix">
				<div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
					<?php realshed_property_render_sidebar( $context ); ?>
				</div>

				<div class="col-lg-8 col-md-12 col-sm-12 content-side">
					<div class="property-content-side">
						<?php realshed_property_shorting_bar( $property_query, $context ); ?>
						<?php realshed_property_loop( $property_query, $layout, $wrapper_class, $context, $has_search_filters ); ?>
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
					<?php realshed_property_render_sidebar( $context ); ?>
				</div>

				<div class="col-lg-8 col-md-12 col-sm-12 content-side">
					<div class="property-content-side">
						<?php realshed_property_shorting_bar( $property_query, $context ); ?>
						<?php realshed_property_loop( $property_query, $layout, $wrapper_class, $context, $has_search_filters ); ?>
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
					<?php realshed_property_loop( $property_query, $layout, $wrapper_class, $context, $has_search_filters ); ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
wp_reset_postdata();

get_footer();
