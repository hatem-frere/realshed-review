
<?php
/**
 * Property List — Half Map Layout
 * Card partial for the split map + content page structure.
 *
 * Page structure: left column = sticky map | right column = content
 * Card type:      deals-block-one (horizontal card — same as list-sidebar)
 * Used by:        layout-list-half-map Redux value
 *
 * The map and the two-column page scaffold are rendered by archive-property.php.
 * This file renders the individual property card inside the right column loop.
 *
 * Cards in this layout are slightly more compact than the sidebar version
 * because the right column is narrower. The CSS class 'compact' is added
 * to signal this — apply padding/size adjustments in your stylesheet.
 *
 * data-lat / data-lng feed the sticky map markers via map-helper.js.
 *
 * Path: wp-content/themes/realshed/property/partials/listing/layout-list-half-map.php
 * @package Realshed
 */

global $realshed_options;

$price_label   = isset( $realshed_options['price_prefix_text'] ) ? $realshed_options['price_prefix_text'] : __( 'Start From', 'realshed' );
$details_label = isset( $realshed_options['see_details_text'] )  ? $realshed_options['see_details_text']  : __( 'See Details', 'realshed' );

$property_id  = get_the_ID();
$price        = get_post_meta( $property_id, '_property_price',        true );
$beds         = get_post_meta( $property_id, '_property_bedrooms',     true );
$baths        = get_post_meta( $property_id, '_property_bathrooms',    true );
$area         = get_post_meta( $property_id, '_property_area_value',   true );
$status_label = get_post_meta( $property_id, '_property_status_label', true );
$lat          = get_post_meta( $property_id, '_property_lat',          true );
$lng          = get_post_meta( $property_id, '_property_lng',          true );

if ( empty( $status_label ) ) {
    $status_terms = get_the_terms( $property_id, 'property_status' );
    if ( $status_terms && ! is_wp_error( $status_terms ) ) {
        $status_label = $status_terms[0]->name;
    }
}
?>

<div class="deals-block-one compact"
     data-id="<?php echo esc_attr( $property_id ); ?>"
     data-lat="<?php echo esc_attr( $lat ); ?>"
     data-lng="<?php echo esc_attr( $lng ); ?>"
     data-title="<?php echo esc_attr( get_the_title() ); ?>"
     data-price="<?php echo esc_attr( $price ); ?>">
    <div class="inner-box">

        <div class="image-box">
            <figure class="image">
                <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php echo esc_url( get_permalink() ); ?>">
                        <?php the_post_thumbnail( 'full', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( get_permalink() ); ?>">
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/resource/deals-3.jpg"
                             alt="<?php echo esc_attr( get_the_title() ); ?>">
                    </a>
                <?php endif; ?>
            </figure>

            <div class="batch"><i class="icon-11"></i></div>

            <?php if ( $status_label ) : ?>
                <span class="category"><?php echo esc_html( $status_label ); ?></span>
            <?php endif; ?>

            <div class="buy-btn">
                <a href="<?php echo esc_url( get_permalink() ); ?>">
                    <?php echo esc_html( $status_label ? $status_label : __( 'View', 'realshed' ) ); ?>
                </a>
            </div>
        </div>

        <div class="lower-content">

            <div class="title-text">
                <h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
            </div>

            <div class="price-box clearfix">
                <div class="price-info pull-left">
                    <h6><?php echo esc_html( $price_label ); ?></h6>
                    <h4><?php echo esc_html( $price ); ?></h4>
                </div>
                <div class="author-box pull-right">
                    <figure class="author-thumb">
                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?>
                        <span><?php echo esc_html( get_the_author() ); ?></span>
                    </figure>
                </div>
            </div>

            <ul class="more-details clearfix">
                <?php if ( $beds ) : ?>
                    <li><i class="icon-14"></i><?php echo esc_html( $beds ); ?> <?php esc_html_e( 'Beds', 'realshed' ); ?></li>
                <?php endif; ?>
                <?php if ( $baths ) : ?>
                    <li><i class="icon-15"></i><?php echo esc_html( $baths ); ?> <?php esc_html_e( 'Baths', 'realshed' ); ?></li>
                <?php endif; ?>
                <?php if ( $area ) : ?>
                    <li><i class="icon-16"></i><?php echo esc_html( $area ); ?> <?php esc_html_e( 'Sq Ft', 'realshed' ); ?></li>
                <?php endif; ?>
            </ul>

            <div class="other-info-box clearfix">
                <div class="btn-box pull-left">
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="theme-btn btn-two">
                        <?php echo esc_html( $details_label ); ?>
                    </a>
                </div>
                <ul class="other-option pull-right clearfix">
                    <li><a href="<?php echo esc_url( get_permalink() ); ?>"><i class="icon-12"></i></a></li>
                    <li><a href="<?php echo esc_url( get_permalink() ); ?>"><i class="icon-13"></i></a></li>
                </ul>
            </div>

        </div>
    </div>
</div>
