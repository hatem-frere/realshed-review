<?php
/**
 * Dynamic Property Search Form
 * Optimized for [realshed_properties_search] shortcode.
 * Advanced fields are outside the form tag but linked via HTML5 form attribute.
 * @package Realshed
 */

// Retrieve taxonomy data for dropdowns
$locations = get_terms( array( 'taxonomy' => 'property_location', 'hide_empty' => false ) );
$types     = get_terms( array( 'taxonomy' => 'property_type',     'hide_empty' => false ) );
$statuses  = get_terms( array( 'taxonomy' => 'property_status',   'hide_empty' => false ) );

// Get current search values for persistence (all fields)
$current_location  = isset( $_GET['s_location'] )  ? sanitize_text_field( $_GET['s_location'] )  : '';
$current_type      = isset( $_GET['s_type'] )       ? sanitize_text_field( $_GET['s_type'] )      : '';
$current_keyword   = isset( $_GET['s'] )            ? sanitize_text_field( $_GET['s'] )           : '';
$current_status    = isset( $_GET['s_status'] )     ? sanitize_text_field( $_GET['s_status'] )    : '';
$current_distance  = isset( $_GET['s_distance'] )   ? sanitize_text_field( $_GET['s_distance'] )  : '';
$current_rooms     = isset( $_GET['s_rooms'] )      ? sanitize_text_field( $_GET['s_rooms'] )     : '';
$current_bath      = isset( $_GET['s_bath'] )       ? sanitize_text_field( $_GET['s_bath'] )      : '';
$current_floor     = isset( $_GET['s_floor'] )      ? sanitize_text_field( $_GET['s_floor'] )     : '';
$current_agency    = isset( $_GET['s_agency'] )     ? sanitize_text_field( $_GET['s_agency'] )    : '';
$current_sort      = isset( $_GET['s_sort'] )       ? sanitize_text_field( $_GET['s_sort'] )      : '';
$current_price_min = isset( $_GET['s_price_min'] )  ? intval( $_GET['s_price_min'] )              : 0;
$current_price_max = isset( $_GET['s_price_max'] )  ? intval( $_GET['s_price_max'] )              : 0;
$current_area_min  = isset( $_GET['s_area_min'] )   ? intval( $_GET['s_area_min'] )               : 0;
$current_area_max  = isset( $_GET['s_area_max'] )   ? intval( $_GET['s_area_max'] )               : 0;
?>

<div class="search-field">
    <div class="tabs-box">

        <!-- Tab Buttons: For Sale, For Rent, etc. — one per property_status term -->
        <div class="tab-btn-box">
            <ul class="tab-btns tab-buttons centred clearfix">
                <?php
                $count = 0;
                if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) ) :
                    foreach ( $statuses as $status ) :
                        $active_class = ( $current_status == $status->slug || ( $current_status == '' && $count == 0 ) ) ? 'active-btn' : '';
                ?>
                <li class="tab-btn <?php echo esc_attr( $active_class ); ?>" data-tab="#tab-<?php echo esc_attr( $status->slug ); ?>">
                    <?php echo esc_html( strtoupper( $status->name ) ); ?>
                </li>
                <?php $count++; endforeach; endif; ?>
            </ul>
        </div>
        <!-- end /.tab-btn-box -->

        <div class="tabs-content info-group">
            <?php
            $count = 0;
            if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) ) :
                foreach ( $statuses as $status ) :
                    $active_tab = ( $current_status == $status->slug || ( $current_status == '' && $count == 0 ) ) ? 'active-tab' : '';
                    // Unique ID per tab — links this form to its advanced fields via HTML5 form attribute
                    $form_id = 'search-form-' . esc_attr( $status->slug );
            ?>
            <div class="tab <?php echo esc_attr( $active_tab ); ?>" id="tab-<?php echo esc_attr( $status->slug ); ?>">
                <div class="inner-box">
                    <div class="top-search">

                        <form
                            action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>"
                            method="get"
                            class="search-form"
                            id="<?php echo esc_attr( $form_id ); ?>"
                        >
                            <!-- Hidden flags required by the search handler -->
                            <input type="hidden" name="post_type"       value="property">
                            <input type="hidden" name="realshed_search" value="1">
                            <input type="hidden" name="s_status"        value="<?php echo esc_attr( $status->slug ); ?>">

                            <div class="row clearfix">

                                <!-- Keyword Search | URL: s | Handler: $query->set('s') -->
                                <div class="col-lg-4 col-md-12 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Search Property', 'realshed' ); ?></label>
                                        <div class="field-input">
                                            <i class="fas fa-search"></i>
                                            <input
                                                type="search"
                                                name="s"
                                                value="<?php echo esc_attr( $current_keyword ); ?>"
                                                placeholder="<?php esc_attr_e( 'Search by Property...', 'realshed' ); ?>"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Location | URL: s_location | Handler: property_location taxonomy -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Location', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <select class="wide" name="s_location">
                                                <option value=""><?php esc_html_e( 'Any Location', 'realshed' ); ?></option>
                                                <?php if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) : foreach ( $locations as $loc ) : ?>
                                                <option value="<?php echo esc_attr( $loc->slug ); ?>" <?php selected( $current_location, $loc->slug ); ?>>
                                                    <?php echo esc_html( $loc->name ); ?>
                                                </option>
                                                <?php endforeach; endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Property Type | URL: s_type | Handler: property_type taxonomy -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Property Type', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <select class="wide" name="s_type">
                                                <option value=""><?php esc_html_e( 'Any Type', 'realshed' ); ?></option>
                                                <?php if ( ! empty( $types ) && ! is_wp_error( $types ) ) : foreach ( $types as $type ) : ?>
                                                <option value="<?php echo esc_attr( $type->slug ); ?>" <?php selected( $current_type, $type->slug ); ?>>
                                                    <?php echo esc_html( $type->name ); ?>
                                                </option>
                                                <?php endforeach; endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- end /.row (top-search fields) -->

                            <!-- Submit Button -->
                            <div class="search-btn">
                                <button type="submit">
                                    <i class="fas fa-search"></i><?php esc_html_e( 'Search', 'realshed' ); ?>
                                </button>
                            </div>

                        </form>
                        <!-- end /.search-form -->

                    </div>
                    <!-- end /.top-search -->

                    <?php
                    /*
                     * Advanced search panel visibility.
                     * Controlled by the Redux "Enable Advanced Search Toggle" option.
                     * class-realshed-shortcodes.php passes the value here via set_query_var().
                     * Defaults to true so the panel shows even when used outside the shortcode.
                     */
                    $show_advanced = get_query_var( 'realshed_show_advanced', true );
                    if ( $show_advanced ) :
                    ?>
                    <div class="switch_btn_one">
                        <button type="button" class="nav-btn nav-toggler navSidebar-button clearfix search__toggler">
                            <i class="fas fa-angle-down"></i><?php esc_html_e( 'Advanced Search', 'realshed' ); ?>
                        </button>

                        <div class="advanced-search">
                            <div class="close-btn">
                                <a href="#" class="close-side-widget"><i class="far fa-times"></i></a>
                            </div>

                            <div class="row clearfix">

                                <!-- Distance from Location | URL: s_distance | Handler: placeholder (geolocation pending) -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Distance from Location', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <select class="wide" name="s_distance" form="<?php echo esc_attr( $form_id ); ?>">
                                                <option value=""><?php esc_html_e( 'Any Distance', 'realshed' ); ?></option>
                                                <option value="5"  <?php selected( $current_distance, '5' );  ?>>5 km</option>
                                                <option value="10" <?php selected( $current_distance, '10' ); ?>>10 km</option>
                                                <option value="20" <?php selected( $current_distance, '20' ); ?>>20 km</option>
                                                <option value="50" <?php selected( $current_distance, '50' ); ?>>50 km</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bedrooms | URL: s_rooms | Handler: _property_bedrooms meta >= -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Bedrooms', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <select class="wide" name="s_rooms" form="<?php echo esc_attr( $form_id ); ?>">
                                                <option value=""><?php esc_html_e( 'Any', 'realshed' ); ?></option>
                                                <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                                <option value="<?php echo $i; ?>" <?php selected( $current_rooms, $i ); ?>><?php echo $i; ?>+</option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sort By | URL: s_sort | Handler: orderby switch in search handler -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Sort By', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <select class="wide" name="s_sort" form="<?php echo esc_attr( $form_id ); ?>">
                                                <option value=""><?php esc_html_e( 'Most Popular', 'realshed' ); ?></option>
                                                <option value="newest"     <?php selected( $current_sort, 'newest' );     ?>><?php esc_html_e( 'Newest', 'realshed' ); ?></option>
                                                <option value="price-low"  <?php selected( $current_sort, 'price-low' );  ?>><?php esc_html_e( 'Price: Low to High', 'realshed' ); ?></option>
                                                <option value="price-high" <?php selected( $current_sort, 'price-high' ); ?>><?php esc_html_e( 'Price: High to Low', 'realshed' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Floor | URL: s_floor | Handler: _property_floor meta = -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Floor', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <select class="wide" name="s_floor" form="<?php echo esc_attr( $form_id ); ?>">
                                                <option value=""><?php esc_html_e( 'Any Floor', 'realshed' ); ?></option>
                                                <option value="1" <?php selected( $current_floor, '1' ); ?>><?php esc_html_e( '1 Floor',   'realshed' ); ?></option>
                                                <option value="2" <?php selected( $current_floor, '2' ); ?>><?php esc_html_e( '2 Floors',  'realshed' ); ?></option>
                                                <option value="3" <?php selected( $current_floor, '3' ); ?>><?php esc_html_e( '3 Floors',  'realshed' ); ?></option>
                                                <option value="4" <?php selected( $current_floor, '4' ); ?>><?php esc_html_e( '4+ Floors', 'realshed' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bathrooms | URL: s_bath | Handler: _property_bathrooms meta >= -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Bathrooms', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <select class="wide" name="s_bath" form="<?php echo esc_attr( $form_id ); ?>">
                                                <option value=""><?php esc_html_e( 'Any', 'realshed' ); ?></option>
                                                <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                                                <option value="<?php echo $i; ?>" <?php selected( $current_bath, $i ); ?>><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agencies | URL: s_agency | Handler: _property_agency meta = -->
                                <div class="col-lg-4 col-md-6 col-sm-12 column">
                                    <div class="form-group">
                                        <label><?php esc_html_e( 'Agencies', 'realshed' ); ?></label>
                                        <div class="select-box">
                                            <!--
                                                Static options — replace with dynamic query
                                                if you have an Agency post type or taxonomy.
                                            -->
                                            <select class="wide" name="s_agency" form="<?php echo esc_attr( $form_id ); ?>">
                                                <option value=""><?php esc_html_e( 'Any Agency', 'realshed' ); ?></option>
                                                <option value="1" <?php selected( $current_agency, '1' ); ?>><?php esc_html_e( 'Agency 01', 'realshed' ); ?></option>
                                                <option value="2" <?php selected( $current_agency, '2' ); ?>><?php esc_html_e( 'Agency 02', 'realshed' ); ?></option>
                                                <option value="3" <?php selected( $current_agency, '3' ); ?>><?php esc_html_e( 'Agency 03', 'realshed' ); ?></option>
                                                <option value="4" <?php selected( $current_agency, '4' ); ?>><?php esc_html_e( 'Agency 04', 'realshed' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Range Slider | URL: s_price_min, s_price_max | Handler: _property_price BETWEEN -->
                                <div class="col-lg-6 col-md-6 col-sm-12 column">
                                    <div class="price-range">
                                        <h6><?php esc_html_e( 'Select Price Range', 'realshed' ); ?></h6>
                                        <div class="range-input">
                                            <!--
                                                Display input: no name attribute — cosmetic only.
                                                JS writes "$1,000 - $5,000" here for the user to read.
                                                Must NOT submit — that is why name is absent.
                                            -->
                                            <input type="text" class="property-amount" readonly>
                                            <input type="hidden" name="s_price_min" class="min-price-hidden" form="<?php echo esc_attr( $form_id ); ?>" value="<?php echo esc_attr( $current_price_min ); ?>">
                                            <input type="hidden" name="s_price_max" class="max-price-hidden" form="<?php echo esc_attr( $form_id ); ?>" value="<?php echo esc_attr( $current_price_max ); ?>">
                                        </div>
                                        <!-- JS target: .price-range-slider — scoped via .closest('.price-range') in property-search.js -->
                                        <div class="price-range-slider"></div>
                                    </div>
                                </div>

                                <!-- Area Range Slider | URL: s_area_min, s_area_max | Handler: _property_area_value BETWEEN -->
                                <div class="col-lg-6 col-md-6 col-sm-12 column">
                                    <div class="area-range">
                                        <h6><?php esc_html_e( 'Select Area', 'realshed' ); ?></h6>
                                        <div class="range-input">
                                            <!--
                                                Display input: no name attribute — cosmetic only.
                                                JS writes "700 - 4,000 sq ft" here for the user to read.
                                            -->
                                            <input type="text" class="area-amount" readonly>
                                            <input type="hidden" name="s_area_min" class="min-area-hidden" form="<?php echo esc_attr( $form_id ); ?>" value="<?php echo esc_attr( $current_area_min ); ?>">
                                            <input type="hidden" name="s_area_max" class="max-area-hidden" form="<?php echo esc_attr( $form_id ); ?>" value="<?php echo esc_attr( $current_area_max ); ?>">
                                        </div>
                                        <!-- JS target: .area-range-slider — scoped via .closest('.area-range') in property-search.js -->
                                        <div class="area-range-slider"></div>
                                    </div>
                                </div>

                            </div>
                            <!-- end /.row (advanced fields) -->

                        </div>
                        <!-- end /.advanced-search -->

                    </div>
                    <!-- end /.switch_btn_one -->
                    <?php endif; // show_advanced ?>

                </div>
                <!-- end /.inner-box -->
            </div>
            <!-- end /.tab -->

            <?php $count++; endforeach; endif; ?>

        </div>
        <!-- end /.tabs-content -->

    </div>
    <!-- end /.tabs-box -->
</div>
<!-- end /.search-field -->
