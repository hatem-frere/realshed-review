<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Realshed_Testimonial_CPT' ) ) {
    class Realshed_Testimonial_CPT {
        public function __construct() {
            add_action( 'init', array( $this, 'register_testimonial_cpt' ) );
        }
        public function register_testimonial_cpt() {
            register_post_type( 'testimonial', array(
                'labels'      => array( 'name' => __( 'Testimonials', 'realshed-core' ), 'singular_name' => __( 'Testimonial', 'realshed-core' ) ),
                'public'      => true,
                'menu_icon'   => 'dashicons-testimonial',
                'supports'    => array( 'title', 'editor', 'thumbnail' ),
                'show_in_rest'=> true,
            ));
        }
    }
    new Realshed_Testimonial_CPT();
}
