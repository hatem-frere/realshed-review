<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Contact_Info_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'contact_info_widget',
            __( 'RealShed: Contact Info', 'realshed-core' ),
            array( 'description' => __( 'Displays office contact details.', 'realshed-core' ) )
        );
    }
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        _e( 'Contact Info placeholder.', 'realshed-core' );
        echo $args['after_widget'];
    }
    public function form( $instance ) { echo 'Settings coming soon.'; }
}
