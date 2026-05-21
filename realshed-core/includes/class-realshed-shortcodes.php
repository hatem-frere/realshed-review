<?php
/**
 * Realshed Shortcodes Class
 * Handles the global [realshed_properties_search] shortcode and Admin Editor button.
 *
 * Path: wp-content/plugins/realshed-core/includes/class-realshed-shortcodes.php
 * @package Realshed_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Realshed_Shortcodes' ) ) {

    class Realshed_Shortcodes {

        public function __construct() {
            add_shortcode( 'realshed_properties_search', array( $this, 'render_properties_search' ) );

            if ( is_admin() ) {
                add_action( 'media_buttons', array( $this, 'add_realshed_shortcode_button' ) );
            }
        }

        public function render_properties_search( $atts ) {

            $realshed_options = get_option( 'realshed_options' );

            // Master toggle — if disabled in Redux, render nothing
            $show_search = isset( $realshed_options['show_properties_search'] )
                ? (bool) $realshed_options['show_properties_search']
                : true;

            if ( ! $show_search ) {
                return '';
            }

            /*
             * Advanced search toggle — read from Redux and pass to the template
             * via a query var so search-form.php can conditionally render the
             * .switch_btn_one panel.
             *
             * In search-form.php, read it like this:
             *   $show_advanced = get_query_var( 'realshed_show_advanced', true );
             *   if ( $show_advanced ) { ... render .switch_btn_one ... }
             */
            $show_advanced = isset( $realshed_options['show_advanced_search'] )
                ? (bool) $realshed_options['show_advanced_search']
                : true;

            set_query_var( 'realshed_show_advanced', $show_advanced );

            /*
             * NOTE: The previous set_query_var() calls for search_locations,
             * search_types, and search_statuses have been removed.
             *
             * search-form.php now calls get_terms() directly, which is the
             * correct approach — it keeps the template self-contained and avoids
             * running three extra database queries on every shortcode render
             * even when the template reads the data differently.
             */

            ob_start();
            get_template_part( 'property/search-form' );
            return ob_get_clean();
        }

        public function add_realshed_shortcode_button() {
            if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
                return;
            }

            echo '<div class="realshed-shortcode-container" style="display: inline-block; position: relative; vertical-align: middle;">';

            echo '<button type="button" id="realshed-shortcode-trigger" class="button">
                <span class="dashicons dashicons-shortcode" style="margin-top: 3px;"></span>
                ' . esc_html__( 'Shortcuts', 'realshed-core' ) . '
            </button>';
            ?>
            <div id="realshed-shortcode-dropdown">
                <div class="realshed-dropdown-header">
                    <?php esc_html_e( 'Insert Realshed Shortcode', 'realshed-core' ); ?>
                </div>
                <div class="realshed-dropdown-content">
                    <a href="#" class="realshed-insert-shortcode" data-shortcode="[realshed_properties_search]">
                        <span class="dashicons dashicons-search"></span>
                        <?php esc_html_e( 'Property Search Form', 'realshed-core' ); ?>
                    </a>
                </div>
            </div>
            <?php
            echo '</div>';
        }
    }
}
// END
