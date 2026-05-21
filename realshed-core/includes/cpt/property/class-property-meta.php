<?php
/**
 * Property Meta Box Class
 *
 * wp-content\plugins\realshed-core\includes\cpt\property\class-property-meta.php
 *
 * Handles the custom fields and Repeater Logic for the Property Custom Post Type.
 *
 * @package Realshed_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Realshed_Property_Meta' ) ) {

    class Realshed_Property_Meta {

        /**
         * Constructor
         */
        public function __construct() {
            add_action( 'add_meta_boxes', array( $this, 'add_property_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_property_meta_data' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        }

        /**
         * Enqueue Admin Scripts for Repeater Logic
         */
        public function enqueue_admin_assets( $hook ) {
            global $post;
            if ( ! $post || 'property' !== $post->post_type ) {
                return;
            }

            // Using standard WordPress jQuery for the repeater actions
            wp_add_inline_script( 'jquery', "
                jQuery(document).ready(function($) {
                    var container = $('#realshed-floor-plan-container');
                    $('#add-floor-plan').on('click', function(e) {
                        e.preventDefault();
                        var index = container.children('.floor-plan-item').length;
                        var html = `
                            <div class='floor-plan-item' style='background:#f9f9f9; border:1px solid #ccc; padding:15px; margin-bottom:10px; position:relative;'>
                                <div style='margin-bottom:10px;'>
                                    <label style='display:block; font-weight:bold;'>Plan Title:</label>
                                    <input type='text' name='property_floor_plans[\${index}][title]' style='width:100%;' placeholder='e.g. First Floor'>
                                </div>
                                <div style='margin-bottom:10px;'>
                                    <label style='display:block; font-weight:bold;'>Plan Size:</label>
                                    <input type='text' name='property_floor_plans[\${index}][size]' style='width:100%;' placeholder='e.g. 1200 Sq Ft'>
                                </div>
                                <button class='remove-floor button-link-delete' style='color:red; cursor:pointer;'>Remove Floor</button>
                            </div>`;
                        container.append(html);
                    });
                    $(document).on('click', '.remove-floor', function(e) {
                        e.preventDefault();
                        $(this).closest('.floor-plan-item').remove();
                    });
                });
            " );
        }

        /**
         * Add Meta Box
         */
        public function add_property_meta_boxes() {
            add_meta_box(
                'realshed_property_details',
                __( 'Property Details & Floor Plans', 'realshed-core' ),
                array( $this, 'render_property_meta_box' ),
                'property',
                'normal',
                'high'
            );
        }

        /**
         * Render Meta Box Content
         */
        public function render_property_meta_box( $post ) {
            wp_nonce_field( 'realshed_property_meta_nonce', 'realshed_property_meta_nonce_field' );

            $property_id = get_post_meta( $post->ID, '_property_id', true );
            $price       = get_post_meta( $post->ID, '_property_price', true );
            $featured    = get_post_meta( $post->ID, '_property_featured', true ); // Fixed variable
            $area        = get_post_meta( $post->ID, '_property_area', true );
            $bedrooms    = get_post_meta( $post->ID, '_property_bedrooms', true );
            $bathrooms   = get_post_meta( $post->ID, '_property_bathrooms', true );
            $floors      = get_post_meta( $post->ID, '_property_floors', true );
            $floor_plans = get_post_meta( $post->ID, '_property_floor_plans', true );

            ?>
<div class="realshed-meta-wrapper">

    <style>
    .realshed-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .realshed-field {
        margin-bottom: 10px;
    }

    .realshed-field label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .realshed-field input[type="text"],
    .realshed-field input[type="number"],
    .realshed-field select {
        width: 100%;
        padding: 8px;
    }
    </style>

    <h3><?php _e( 'Basic Specifications', 'realshed-core' ); ?></h3>
    <div class="realshed-meta-grid">
        <div class="realshed-field">
            <label for="property_id"><?php _e( 'Property ID:', 'realshed-core' ); ?></label>
            <input type="text" id="property_id" name="property_id" value="<?php echo esc_attr( $property_id ); ?>">
        </div>
        <div class="realshed-field">
            <label for="property_price"><?php _e( 'Price ($):', 'realshed-core' ); ?></label>
            <input type="number" id="property_price" name="property_price" value="<?php echo esc_attr( $price ); ?>" step="0.01">
        </div>
        <div class="realshed-field">
            <label for="property_featured">
                <input type="checkbox" id="property_featured" name="property_featured" value="1" <?php checked( $featured, '1' ); ?>>
                <?php _e( 'Mark as Featured', 'realshed-core' ); ?>
            </label>
        </div>
        <div class="realshed-field">
            <label for="property_hot_deal">
                <input type="checkbox" id="property_hot_deal" name="property_hot_deal" value="1" <?php checked( get_post_meta( $post->ID, '_property_hot_deal', true ), '1' ); ?>>
                <?php _e( 'Mark as Hot Deal', 'realshed-core' ); ?>
            </label>
        </div>
        <div class="realshed-field">
            <label for="property_area"><?php _e( 'Area (Sq Ft):', 'realshed-core' ); ?></label>
            <input type="number" id="property_area" name="property_area" value="<?php echo esc_attr( $area ); ?>">
        </div>
        <div class="realshed-field">
            <label for="property_bedrooms"><?php _e( 'Bedrooms:', 'realshed-core' ); ?></label>
            <input type="number" id="property_bedrooms" name="property_bedrooms" value="<?php echo esc_attr( $bedrooms ); ?>">
        </div>
        <div class="realshed-field">
            <label for="property_bathrooms"><?php _e( 'Bathrooms:', 'realshed-core' ); ?></label>
            <input type="number" id="property_bathrooms" name="property_bathrooms" value="<?php echo esc_attr( $bathrooms ); ?>">
        </div>
        <div class="realshed-field">
            <label for="property_floors"><?php _e( 'Total Floors:', 'realshed-core' ); ?></label>
            <input type="number" id="property_floors" name="property_floors" value="<?php echo esc_attr( $floors ); ?>">
        </div>
    </div>

    <hr>

    <h3><?php _e( 'Floor Plans (Repeater Logic)', 'realshed-core' ); ?></h3>
    <div id="realshed-floor-plan-container">
        <?php if ( ! empty( $floor_plans ) && is_array( $floor_plans ) ) : ?>
        <?php foreach ( $floor_plans as $index => $plan ) : ?>
        <div class="floor-plan-item" style="background:#f9f9f9; border:1px solid #ccc; padding:15px; margin-bottom:10px; position:relative;">
            <div style="margin-bottom:10px;">
                <label style="display:block; font-weight:bold;">Plan Title:</label>
                <input type="text" name="property_floor_plans[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $plan['title'] ); ?>" style="width:100%;">
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block; font-weight:bold;">Plan Size:</label>
                <input type="text" name="property_floor_plans[<?php echo $index; ?>][size]" value="<?php echo esc_attr( $plan['size'] ); ?>" style="width:100%;">
            </div>
            <button class="remove-floor button-link-delete" style="color:red; cursor:pointer;">Remove Floor</button>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" id="add-floor-plan" class="button button-primary"><?php _e( '+ Add New Floor', 'realshed-core' ); ?></button>

</div>
<?php
        }

        /**
         * Save Meta Box Data
         */
        public function save_property_meta_data( $post_id ) {
            // Security Checks
            if ( ! isset( $_POST['realshed_property_meta_nonce_field'] ) ) return;
            if ( ! wp_verify_nonce( $_POST['realshed_property_meta_nonce_field'], 'realshed_property_meta_nonce' ) ) return;
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
            if ( ! current_user_can( 'edit_post', $post_id ) ) return;

            // Simple Text/Number Fields
            $fields = array(
                'property_id'        => '_property_id',
                'property_price'     => '_property_price',
                'property_area'      => '_property_area',
                'property_bedrooms'  => '_property_bedrooms',
                'property_bathrooms' => '_property_bathrooms',
                'property_floors'    => '_property_floors',
            );

            foreach ( $fields as $key => $meta_key ) {
                if ( isset( $_POST[$key] ) ) {
                    update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[$key] ) );
                }
            }

            // Handle Checkbox (Featured) separately
            $featured_val = isset( $_POST['property_featured'] ) ? '1' : '0';
            update_post_meta( $post_id, '_property_featured', $featured_val );

            // Handle Hot Deal checkbox
            $hot_deal_val = isset( $_POST['property_hot_deal'] ) ? '1' : '0';
            update_post_meta( $post_id, '_property_hot_deal', $hot_deal_val );

            // Repeater Floor Plans Logic
            if ( isset( $_POST['property_floor_plans'] ) && is_array( $_POST['property_floor_plans'] ) ) {
                $sanitized_plans = array();
                foreach ( $_POST['property_floor_plans'] as $plan ) {
                    if ( ! empty( $plan['title'] ) ) {
                        $sanitized_plans[] = array(
                            'title' => sanitize_text_field( $plan['title'] ),
                            'size'  => sanitize_text_field( $plan['size'] ),
                        );
                    }
                }
                update_post_meta( $post_id, '_property_floor_plans', $sanitized_plans );
            } else {
                delete_post_meta( $post_id, '_property_floor_plans' );
            }
        }
    }

    new Realshed_Property_Meta();
}
