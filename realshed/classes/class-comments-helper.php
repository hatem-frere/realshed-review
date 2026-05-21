<?php
/**
 * https://5balloons.info/custom-html-for-comments-section-in-wordpress-theme/
 */
if ( ! function_exists( 'realshed_comments_helper' ) ) {
    function realshed_comments_helper( $comment, $args, $depth ) {

        if ( ! ( $comment instanceof WP_Comment ) ) {
            return;
        }
        ?>

        <div <?php comment_class( 'comment-box' ); ?> id="comment-<?php comment_ID(); ?>">
            <figure class="thumb-box">
                <?php echo get_avatar( $comment, $size = '60' ); ?>
            </figure>
            <div class="comment-inner">
                <?php if ( $comment->comment_approved == '0' ) { ?>
                    <em><?php esc_html_e( 'Your comment is awaiting moderation.', 'realshed' ); ?></em>
                    <br />
                <?php } ?>
                <div class="comment-info clearfix">
                    <h5><?php echo get_comment_author(); ?></h5>
                    <span><?php printf( esc_html__( '%1$s ', 'realshed' ), get_comment_date() ); ?></span>
                </div>
                <div class="text">
                    <?php comment_text();
                             comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<i class="fas fa-share"></i> ' . esc_html__( 'Reply', 'realshed' ) ) ) );
                         edit_comment_link( __( '(Edit)', 'realshed' ), '  ', '', $comment ); ?>
                </div>
            </div>

        <?php
    }
}