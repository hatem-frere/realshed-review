<?php
/**
 * Page Header / Breadcrumb Global Backgrounds
 */
function realshed_page_header_customizer( $wp_customize ) {

    $wp_customize->add_section( 'realshed_page_header_section', array(
        'title'    => __( 'Page Header / Breadcrumb', 'realshed' ),
        'priority' => 30,
    ) );

    // Archive / System Page Backgrounds
    $archive_pages = [
        'blog_bg'       => __( 'Blog Page', 'realshed' ),
        'category_bg'   => __( 'Category Page', 'realshed' ),
        'tag_bg'        => __( 'Tag Page', 'realshed' ),
        'author_bg'     => __( 'Author Page', 'realshed' ),
        'search_bg'     => __( 'Search Results Page', 'realshed' ),
        'archive_bg'    => __( 'Date Archive Page', 'realshed' ),
        '404_bg'        => __( '404 Page', 'realshed' ),
    ];

    foreach ( $archive_pages as $key => $label ) {
        $wp_customize->add_setting( 'realshed_' . $key, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'realshed_' . $key,
                array(
                    'label'   => sprintf( __( '%s Background', 'realshed' ), $label ),
                    'section' => 'realshed_page_header_section',
                )
            )
        );
    }
}
add_action( 'customize_register', 'realshed_page_header_customizer' );
