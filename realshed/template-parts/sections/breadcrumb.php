<?php
/**
 * Dynamic Page Header Template - Structured Global Breadcrumb
 * * Logic:
 * - Home Only: Home (Text only, no icon)
 * - Posts: Home > Blog (Link) > Title (Text)
 * - Categories: Home > Categories (Link) > Category Name (Text)
 * - Tags: Home > Tags (Link) > Tag Name (Text)
 * - Authors: Home > Authors (Link) > Author Name (Text)
 *
 * @package Realshed
 */

$current_object   = get_queried_object();
$default_bg       = 'https://placehold.co/1400x400/dfdfdf/fff';
$page_title       = '';
$background_image = '';

// 1. Background Image Logic
if ( is_singular() && ! is_front_page() && ! is_home() ) {
    $page_meta_bg = get_post_meta( get_the_ID(), '_realshed_breadcrumb_bg', true );
    $background_image = ! empty( $page_meta_bg ) ? $page_meta_bg : '';
}

if ( empty( $background_image ) && ( is_category() || is_tag() || is_tax() ) ) {
    $term_bg = get_term_meta( get_queried_object_id(), '_realshed_term_breadcrumb_bg', true );
    $background_image = ! empty( $term_bg ) ? $term_bg : '';
}

if ( empty( $background_image ) ) {
    if ( is_home() || is_front_page() ) $background_image = get_theme_mod( 'realshed_blog_bg', '' );
    elseif ( is_author() ) $background_image = get_theme_mod( 'realshed_author_bg', '' );
    elseif ( is_search() ) $background_image = get_theme_mod( 'realshed_search_bg', '' );
    elseif ( is_date() )   $background_image = get_theme_mod( 'realshed_archive_bg', '' );
    elseif ( is_404() )    $background_image = get_theme_mod( 'realshed_404_bg', '' );
}

$background_image = ! empty( $background_image ) ? $background_image : $default_bg;

// 2. Page Title Logic
if ( is_singular() )      $page_title = get_the_title();
elseif ( is_category() )  $page_title = single_cat_title( '', false );
elseif ( is_tag() )       $page_title = single_tag_title( '', false );
elseif ( is_author() )    $page_title = get_the_author();
elseif ( is_date() ) {
    if ( is_day() )       $page_title = get_the_date();
    elseif ( is_month() ) $page_title = get_the_date( 'F Y' );
    elseif ( is_year() )  $page_title = get_the_date( 'Y' );
}
elseif ( is_tax() )       $page_title = single_term_title( '', false );
elseif ( is_search() )    $page_title = sprintf( __( 'Search: %s', 'realshed' ), get_search_query() );
elseif ( is_404() )       $page_title = __( '404 - Not Found', 'realshed' );

$page_title = apply_filters( 'realshed_page_header_title', $page_title );

// 3. Section Styling
$background_style = 'style="background-image: url(' . esc_url( $background_image ) . ');"';
$section_classes  = apply_filters( 'realshed_page_header_classes', [ 'page-title', 'centred', 'has-bg-image', 'author-page' ] );
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" <?php echo $background_style; ?>>
    <?php if ( get_theme_mod( 'realshed_page_header_overlay', true ) ) : ?>
    <div class="page-title-overlay"></div>
    <?php endif; ?>

    <div class="auto-container">
        <div class="content-box clearfix">

            <?php if ( is_author() ) : ?>
            <div class="author-header-image text-center">
                <img src="<?php echo esc_url( get_avatar_url( get_the_author_meta('ID'), array('size' => 180) ) ); ?>" alt="<?php echo esc_attr( get_the_author() ); ?>" class="author-profile-image">
            </div>
            <?php elseif ( ! empty( $page_title ) ) : ?>
            <h1><?php echo esc_html( $page_title ); ?></h1>
            <?php endif; ?>

            <ul class="bread-crumb clearfix">

                <?php if ( is_front_page() ) : ?>
                <li><h1 class="home-blog"><?php esc_html_e( 'Home', 'realshed' ); ?></h1></li>
                <?php else : ?>
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'realshed' ); ?></a></li>

                <?php
                    // --- CASE: SINGLE POST ---
                    if ( is_singular( 'post' ) ) : ?>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ); ?>"><?php esc_html_e( 'Blog', 'realshed' ); ?></a></li>
                <li><span><?php the_title(); ?></span></li>
                <?php
                    // --- CASE: CATEGORY ---
                     elseif ( is_category() ) : ?>
                <li><span><?php esc_html_e( 'Categories', 'realshed' ); ?></span></li>
                <li><span><?php single_cat_title(); ?></span></li>
                <?php
                    // --- CASE: TAG ---
                     elseif ( is_tag() ) : ?>
                <li><span><?php esc_html_e( 'Tags', 'realshed' ); ?></span></li>
                <li><span><?php single_tag_title(); ?></span></li>
                <?php
                    // --- CASE: AUTHOR ---
                     elseif ( is_author() ) : ?>
                <li><span><?php esc_html_e( 'Authors', 'realshed' ); ?></span></li>
                <li><span class="author-name"><?php echo esc_html( get_the_author() ); ?></span></li>
                <?php
                    // --- CASE: STATIC PAGE ---
                     elseif ( is_page() ) : ?>
                <li><span><?php the_title(); ?></span></li>
                <?php
                    // --- CASE: EVERYTHING ELSE ---
                     else : ?>
                <li><span><?php echo esc_html( $page_title ); ?></span></li>
                <?php endif; ?>

                <?php endif; // End Front Page Check ?>

            </ul>
        </div>
    </div>
</section>
