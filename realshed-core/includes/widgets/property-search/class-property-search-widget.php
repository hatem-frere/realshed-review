<?php
/**
 * Property Search Widget – with AJAX support, numeric sliders, reset button
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Realshed_Property_Search_Widget' ) ) {

    class Realshed_Property_Search_Widget extends WP_Widget {

        public function __construct() {
            parent::__construct(
                'realshed_property_search',
                esc_html__( 'Realshed: Property Sidebar Filter', 'realshed-core' ),
                array( 'description' => esc_html__( 'Complete property filter with price/area sliders, status, amenities.', 'realshed-core' ) )
            );
        }

        public function widget( $args, $instance ) {
            echo $args['before_widget'];
            $current_url = get_post_type_archive_link( 'property' );
            ?>
<div class="default-sidebar property-sidebar">
    <!-- Main Filter Form (AJAX enabled) -->
    <form action="<?php echo esc_url( $current_url ); ?>" method="get" class="realshed-main-filter" data-ajax="1">
        <input type="hidden" name="realshed_search" value="1">

        <div class="filter-widget sidebar-widget">
            <div class="widget-title"><h5><?php esc_html_e( 'Property', 'realshed-core' ); ?></h5></div>
            <div class="widget-content">
                <!-- Property Type -->
                <div class="select-box">
                    <select class="wide" name="s_type">
                        <option data-display="<?php esc_attr_e( 'All Type', 'realshed-core' ); ?>" value=""><?php esc_html_e( 'All Type', 'realshed-core' ); ?></option>
                        <?php
                        $types = get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => false ) );
                        if ( ! is_wp_error( $types ) ) {
                            foreach ( $types as $type ) {
                                echo '<option value="' . esc_attr( $type->slug ) . '" ' . selected( $_GET['s_type'] ?? '', $type->slug, false ) . '>' . esc_html( $type->name ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <!-- Location -->
                <div class="select-box">
                    <select class="wide" name="s_location">
                        <option data-display="<?php esc_attr_e( 'Select Location', 'realshed-core' ); ?>" value=""><?php esc_html_e( 'Select Location', 'realshed-core' ); ?></option>
                        <?php
                        $locs = get_terms( array( 'taxonomy' => 'property_location', 'hide_empty' => false ) );
                        if ( ! is_wp_error( $locs ) ) {
                            foreach ( $locs as $loc ) {
                                echo '<option value="' . esc_attr( $loc->slug ) . '" ' . selected( $_GET['s_location'] ?? '', $loc->slug, false ) . '>' . esc_html( $loc->name ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Max Rooms -->
                <div class="select-box">
                    <select class="wide" name="s_rooms">
                        <option data-display="<?php esc_attr_e( 'Max Rooms', 'realshed-core' ); ?>" value=""><?php esc_html_e( 'Max Rooms', 'realshed-core' ); ?></option>
                        <?php for ( $i = 2; $i <= 5; $i++ ) : ?>
                        <option value="<?php echo $i; ?>" <?php selected( $_GET['s_rooms'] ?? '', $i ); ?>><?php echo $i; ?>+ <?php esc_html_e( 'Rooms', 'realshed-core' ); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <!-- Most Popular -->
                <div class="select-box">
                    <select class="wide" name="s_popular">
                        <option data-display="<?php esc_attr_e( 'Most Popular', 'realshed-core' ); ?>" value=""><?php esc_html_e( 'Most Popular', 'realshed-core' ); ?></option>
                        <option value="featured" <?php selected( $_GET['s_popular'] ?? '', 'featured' ); ?>><?php esc_html_e( 'Featured', 'realshed-core' ); ?></option>
                        <option value="hot" <?php selected( $_GET['s_popular'] ?? '', 'hot' ); ?>><?php esc_html_e( 'Hot Deal', 'realshed-core' ); ?></option>
                    </select>
                </div>
                <!-- Select Floor -->
                <div class="select-box">
                    <select class="wide" name="s_floor">
                        <option data-display="<?php esc_attr_e( 'Select Floor', 'realshed-core' ); ?>" value=""><?php esc_html_e( 'Select Floor', 'realshed-core' ); ?></option>
                        <?php for ( $i = 2; $i <= 4; $i++ ) : ?>
                        <option value="<?php echo $i; ?>" <?php selected( $_GET['s_floor'] ?? '', $i ); ?>><?php echo $i; ?>x <?php esc_html_e( 'Floor', 'realshed-core' ); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Filter & Reset Buttons side by side -->
                <div class="filter-btn-group" style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="theme-btn btn-one" style="flex:1;"><i class="fas fa-filter"></i>&nbsp;<?php esc_html_e( 'Filter', 'realshed-core' ); ?></button>
                    <button type="button" class="theme-btn btn-one reset-filters-btn" style="flex:1; background:#6c757d;"><i class="fas fa-undo-alt"></i>&nbsp;<?php esc_html_e( 'Reset', 'realshed-core' ); ?></button>
                </div>
            </div>
        </div>

        <!-- Area Slider -->
        <div class="area-filter sidebar-widget">
            <label><?php esc_html_e( 'Area (Sq Ft)', 'realshed-core' ); ?></label>
            <div class="range-slider area-range clearfix">
                <div class="clearfix">
                    <div class="input">
                        <input type="text" class="area-amount" readonly placeholder="<?php esc_attr_e( 'Min - Max', 'realshed-core' ); ?>">
                        <input type="hidden" name="s_area_min" class="min-area-hidden" value="<?php echo esc_attr( $_GET['s_area_min'] ?? '' ); ?>">
                        <input type="hidden" name="s_area_max" class="max-area-hidden" value="<?php echo esc_attr( $_GET['s_area_max'] ?? '' ); ?>">
                    </div>
                </div>
                <div class="area-range-slider"></div>
            </div>
        </div>

        <!-- Price Slider -->
        <div class="price-filter sidebar-widget">
            <div class="widget-title"><h5><?php esc_html_e( 'Select Price Range', 'realshed-core' ); ?></h5></div>
            <div class="range-slider clearfix">
                <div class="clearfix">
                    <div class="input">
                        <input type="text" class="property-amount" readonly placeholder="<?php esc_attr_e( 'Min - Max', 'realshed-core' ); ?>">
                        <input type="hidden" name="s_price_min" class="min-price-hidden" value="<?php echo esc_attr( $_GET['s_price_min'] ?? '' ); ?>">
                        <input type="hidden" name="s_price_max" class="max-price-hidden" value="<?php echo esc_attr( $_GET['s_price_max'] ?? '' ); ?>">
                    </div>
                </div>
                <div class="price-range-slider"></div>
            </div>
        </div>
    </form>

    <!-- Status Of Property (links with AJAX) -->
    <div class="category-widget sidebar-widget">
        <div class="widget-title"><h5><?php esc_html_e( 'Status Of Property', 'realshed-core' ); ?></h5></div>
        <ul class="category-list clearfix ajax-status-links">
            <?php
            $status_terms = get_terms( array( 'taxonomy' => 'property_status', 'hide_empty' => true ) );
            $current_status = isset( $_GET['s_status'] ) ? sanitize_text_field( $_GET['s_status'] ) : '';
            if ( ! is_wp_error( $status_terms ) ) {
                foreach ( $status_terms as $term ) {
                    $class = ( $current_status === $term->slug ) ? 'current' : '';
                    echo '<li><a href="#" data-status="' . esc_attr( $term->slug ) . '" class="' . $class . '">' . esc_html( $term->name ) . ' <span>(' . $term->count . ')</span></a></li>';
                }
            } else {
                echo '<li><a href="#">For Rent <span>(0)</span></a></li>';
                echo '<li><a href="#">For Sale <span>(0)</span></a></li>';
            }
            ?>
        </ul>
    </div>

    <!-- Amenities (checkboxes with AJAX) -->
    <div class="category-widget sidebar-widget">
        <div class="widget-title"><h5><?php esc_html_e( 'Amenities', 'realshed-core' ); ?></h5></div>
        <ul class="category-list clearfix ajax-amenities-list">
            <?php
            $amenities = get_terms( array( 'taxonomy' => 'property_amenity', 'hide_empty' => true ) );
            $selected_amenities = isset( $_GET['s_amenities'] ) ? (array) $_GET['s_amenities'] : array();
            if ( ! is_wp_error( $amenities ) ) {
                foreach ( $amenities as $amenity ) {
                    $checked = in_array( $amenity->slug, $selected_amenities ) ? 'checked' : '';
                    echo '<li>
                        <label>
                            <input type="checkbox" name="s_amenities[]" value="' . esc_attr( $amenity->slug ) . '" ' . $checked . ' data-ajax-checkbox="1">
                            ' . esc_html( $amenity->name ) . ' <span>(' . $amenity->count . ')</span>
                        </label>
                    </li>';
                }
            }
            ?>
        </ul>
    </div>
</div>
<?php
            echo $args['after_widget'];
        }

        public function form( $instance ) {
            echo '<p>' . esc_html__( 'Widget settings are handled automatically.', 'realshed-core' ) . '</p>';
        }
    }
}
