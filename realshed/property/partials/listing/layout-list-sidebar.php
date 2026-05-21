<?php
/**
 * Property List — Sidebar Layout
 * Card partial for the list + sidebar page structure.
 *
 * Page structure: col-lg-4 sidebar | col-lg-8 content
 * Card type:      deals-block-one (horizontal card)
 * Used by:        layout-list-sidebar Redux value
 *                 Also used as the fixed layout for /search-properties/
 *
 * Meta keys (set by class-property-meta.php):
 *   _property_price       — formatted price string
 *   _property_bedrooms    — number of bedrooms
 *   _property_bathrooms   — number of bathrooms
 *   _property_area_value  — area in sq ft
 *   _property_status_label— display label e.g. "For Sale"
 *
 * Path: wp-content/themes/realshed/property/partials/listing/layout-list-sidebar.php
 * @package Realshed
 */
global $realshed_options;

$price_label   = isset( $realshed_options['price_prefix_text'] ) ? $realshed_options['price_prefix_text'] : __( 'Start From', 'realshed' );
$details_label = isset( $realshed_options['see_details_text'] )  ? $realshed_options['see_details_text']  : __( 'See Details', 'realshed' );

$property_id   = get_the_ID();
$price         = get_post_meta( $property_id, '_property_price',        true );
$beds          = get_post_meta( $property_id, '_property_bedrooms',     true );
$baths         = get_post_meta( $property_id, '_property_bathrooms',    true );
$area          = get_post_meta( $property_id, '_property_area',   true );
$status_label  = get_post_meta( $property_id, '_property_status_label', true );

if ( empty( $status_label ) ) {
    $status_terms = get_the_terms( $property_id, 'property_status' );
    if ( $status_terms && ! is_wp_error( $status_terms ) ) {
        $status_label = $status_terms[0]->name;
    }
}
// Featured badge – change logic as needed
$is_featured = get_post_meta( $property_id, '_property_featured', true ); // set this meta in your plugin
?>

<div class="deals-block-one">
    <div class="inner-box">
        <div class="image-box">
            <figure class="image">
                <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?></a>
                <?php else : ?>
                <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/resource/deals-3.jpg" alt="<?php the_title_attribute(); ?>"></a>
                <?php endif; ?>
            </figure>
            <?php if ( get_post_meta( get_the_ID(), '_property_hot_deal', true ) ) : ?>
            <div class="batch"><i class="icon-11"></i></div>
            <?php endif; ?>
            <?php if ( $is_featured ) : ?>
            <span class="category"><?php esc_html_e( 'Featured', 'realshed' ); ?></span>
            <?php endif; ?>
            <div class="buy-btn">
                <a href="<?php the_permalink(); ?>"><?php echo esc_html( $status_label ? $status_label : __( 'View', 'realshed' ) ); ?></a>
            </div>
        </div>

        <div class="lower-content">
            <div class="title-text">
                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
            </div>
            <div class="price-box clearfix">
                <div class="price-info pull-left">
                    <h6><?php echo esc_html( $price_label ); ?></h6>
                    <h4><?php echo '$' . number_format( floatval( $price ), 2 ); ?></h4>
                    <!-- <h4><?php echo esc_html( $price ); ?></h4> -->
                </div>
                <div class="author-box pull-right">
                    <figure class="author-thumb">
                        <?php
                        // echo get_avatar( get_the_author_meta( 'ID' ), 30 );
                        $author_id = get_the_author_meta('ID');
                        echo get_avatar( $author_id, 30 );
                        ?>
                        <span><?php echo esc_html( get_the_author() ); ?></span>
                    </figure>
                </div>
            </div>
            <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?></p>
            <ul class="more-details clearfix">
                <?php if ( $beds ) : ?><li><i class="icon-14"></i><?php echo esc_html( $beds ); ?> <?php esc_html_e( 'Beds', 'realshed' ); ?></li><?php endif; ?>
                <?php if ( $baths ) : ?><li><i class="icon-15"></i><?php echo esc_html( $baths ); ?> <?php esc_html_e( 'Baths', 'realshed' ); ?></li><?php endif; ?>
                <?php if ( $area ) : ?><li><i class="icon-16"></i><?php echo esc_html( $area ); ?> <?php esc_html_e( 'Sq Ft', 'realshed' ); ?></li><?php endif; ?>
            </ul>
            <div class="other-info-box clearfix">
                <div class="btn-box pull-left">
                    <a href="<?php the_permalink(); ?>" class="theme-btn btn-two"><?php echo esc_html( $details_label ); ?></a>
                </div>
                <ul class="other-option pull-right clearfix">
                    <li><a href="<?php the_permalink(); ?>"><i class="icon-12"></i></a></li>
                    <li><a href="<?php the_permalink(); ?>"><i class="icon-13"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END -->
