<?php
/**
 * Breadcrumb Background Meta Box (Per-Page / Post)
 */

function realshed_add_breadcrumb_bg_meta() {
    add_meta_box(
        'realshed_breadcrumb_bg',
        __( 'Breadcrumb Background', 'realshed' ),
        'realshed_breadcrumb_bg_callback',
        ['page', 'post'], // Add CPTs if needed
        'side'
    );
}
add_action( 'add_meta_boxes', 'realshed_add_breadcrumb_bg_meta' );

function realshed_breadcrumb_bg_callback( $post ) {
    $value = get_post_meta( $post->ID, '_realshed_breadcrumb_bg', true );
    wp_nonce_field( 'realshed_breadcrumb_bg_nonce', 'realshed_breadcrumb_bg_nonce' );
    ?>

    <input type="hidden" id="realshed_breadcrumb_bg" name="realshed_breadcrumb_bg" value="<?php echo esc_attr( $value ); ?>" />

    <button class="button realshed-upload-bg">
        <?php esc_html_e( 'Select Image', 'realshed' ); ?>
    </button>

    <p class="description">
        <?php esc_html_e( 'Overrides the default breadcrumb background for this page or post.', 'realshed' ); ?>
    </p>

    <script>
    jQuery(function($){
        $('.realshed-upload-bg').on('click', function(e){
            e.preventDefault();
            const frame = wp.media({
                title: 'Select Background',
                multiple: false
            });
            frame.on('select', function(){
                const image = frame.state().get('selection').first().toJSON();
                $('#realshed_breadcrumb_bg').val(image.url);
            });
            frame.open();
        });
    });
    </script>

<?php }

function realshed_save_breadcrumb_bg( $post_id ) {
    if ( ! isset( $_POST['realshed_breadcrumb_bg_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['realshed_breadcrumb_bg_nonce'], 'realshed_breadcrumb_bg_nonce' ) ) return;

    if ( isset( $_POST['realshed_breadcrumb_bg'] ) ) {
        update_post_meta(
            $post_id,
            '_realshed_breadcrumb_bg',
            esc_url_raw( $_POST['realshed_breadcrumb_bg'] )
        );
    }
}
add_action( 'save_post', 'realshed_save_breadcrumb_bg' );
