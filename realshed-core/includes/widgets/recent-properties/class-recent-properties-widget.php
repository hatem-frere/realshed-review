<?php
/**
 * Recent Properties Widget
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Recent_Properties_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'recent_properties_widget',
            __( 'RealShed: Recent Properties', 'realshed-core' ),
            array( 'description' => __( 'Displays the most recent property listings.', 'realshed-core' ) )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        echo __( 'Recent Properties will appear here.', 'realshed-core' );
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Properties', 'realshed-core' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }
}
