<?php
/**
 * Hero/Banner Section for Homepage
 * @package Realshed
 */

global $realshed_options;

// Get data from Redux with fallbacks
$banner_bg    = isset( $realshed_options['banner_bg']['url'] ) ? $realshed_options['banner_bg']['url'] : get_template_directory_uri() . '/assets/images/banner/banner-1.jpg';
$banner_title = isset( $realshed_options['banner_title'] ) ? $realshed_options['banner_title'] : esc_html__( 'Create Lasting Wealth Through Realshed', 'realshed' );
$banner_desc  = isset( $realshed_options['banner_desc'] ) ? $realshed_options['banner_desc'] : esc_html__( 'Amet consectetur adipisicing elit sed do eiusmod.', 'realshed' );

/**
 * Updated to use the new global toggle established in Redux
 * and the unified properties search shortcode.
 */
$show_search  = isset( $realshed_options['show_properties_search'] ) ? $realshed_options['show_properties_search'] : true;
?>

<section class="banner-section" style="background-image: url(<?php echo esc_url( $banner_bg ); ?>);">
    <div class="auto-container">
        <div class="inner-container">
            <div class="content-box centred">
                <h2><?php echo esc_html( $banner_title ); ?></h2>
                <p><?php echo esc_html( $banner_desc ); ?></p>
            </div>

            <?php
                // We now call the global shortcode which handles its own internal logic and template loading
                if ( $show_search && shortcode_exists( 'realshed_properties_search' ) ) {
                    echo do_shortcode( '[realshed_properties_search]' );
                }
            ?>

        </div>
    </div>
</section>
<!-- end /.banner-section -->
