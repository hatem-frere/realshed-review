<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Realshed_Team_CPT' ) ) {
    class Realshed_Team_CPT {
        public function __construct() {
            add_action( 'init', array( $this, 'register_team_cpt' ) );
        }
        public function register_team_cpt() {
            register_post_type( 'team', array(
                'labels'      => array( 'name' => __( 'Team', 'realshed-core' ), 'singular_name' => __( 'Member', 'realshed-core' ) ),
                'public'      => true,
                'menu_icon'   => 'dashicons-groups',
                'supports'    => array( 'title', 'editor', 'thumbnail' ),
                'show_in_rest'=> true,
            ));
        }
    }
    new Realshed_Team_CPT();
}
