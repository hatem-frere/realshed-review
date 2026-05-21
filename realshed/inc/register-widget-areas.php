<?php
/**
* Register widget areas.
*
* @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
*/

add_action( 'widgets_init', 'realshed_sidebar_widgets_init' );

function realshed_sidebar_widgets_init () {

    // Realshed Sidebar
    register_sidebar( array(
        'name'          => esc_html__( 'Realshed Sidebar', 'realshed' ),
        'id'            => 'realshed-sidebar',
        'description'   => esc_html__( 'Add widgets here.', 'realshed' ),
        'before_widget' => '<div id="%1$s" class="sidebar-widget default-widget %2$s">',
        'after_widget'  => '</div>',

        'before_title'  => '<div class="widget-title"><h4>',
        'after_title'   => '</h4></div>',
    ) );
}
