<?php
/**
* The template for displaying Comments.
*
* The area of the page that contains comments and the comment form.
*
* @package WordPress
* @subpackage Realshed
* @since Realshed 1.0
*/

if ( post_password_required() ) {
    return;
}
?>

<!-- Comments Area -->
<div class='comments-area'>
  <?php if ( have_comments() ) : ?>
  <div class='group-title'>
    <h4>
      <?php
printf( _nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'realshed' ),
number_format_i18n( get_comments_number() ) );
?>
    </h4>
  </div><!-- /.group-title -->

  <div class='comment-box'>
    <?php
wp_list_comments( array(
    'style' => 'div',
    'short_ping' => true,
    'avatar_size' => 60,
    'callback' => 'realshed_comments_helper',
    'reverse_top_level' => false
) );
?>
  </div><!-- .comment-box -->

  <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
  <nav class='navigation comment-navigation' role='navigation'>
    <h1 class='screen-reader-text section-heading'><?php _e( 'Comment navigation', 'realshed' );
?></h1>
    <div class='nav-previous'>
      <?php previous_comments_link( __( '<i class="fas fa-arrow-left"></i> Older Comments', 'realshed' ) );
?>
    </div>
    <div class='nav-next'>
      <?php next_comments_link( __( 'Newest Comments <i class="fas fa-arrow-right"></i>', 'realshed' ) );
?>
    </div>
  </nav><!-- .comment-navigation -->
  <?php endif;
?>

  <?php if ( ! comments_open() && get_comments_number() ) : ?>
  <p class='no-comments'><?php _e( 'Comments are closed.', 'realshed' );
?></p>
  <?php endif;
?>

  <?php else : // No comments ?>
  <div class='group-title'>
    <h4><?php esc_html_e( 'No comments yet.', 'realshed' );
?></h4>
  </div>
  <?php endif;
// have_comments() ?>
</div><!-- .comments-area -->

<?php
// 3. COMMENT FORM VARIABLES
$comment_send = 'Submit Now';
$comment_reply = 'Leave a Comment';
$comment_reply_to = 'Reply';
$comment_author = 'Your name';
$comment_email = 'Your email';
$phone_number = 'Phone number';
$subject = 'Subject';
$comment_body = 'Your Comment';
$comment_cookies_1 = ' By posting your comment, you agree to our ';
$comment_cookies_2 = ' Privacy Policy';
$comment_cancel = 'Cancel Reply';
?>

<!-- Comments Form Area ( SEPARATE from comments area ) -->
<?php if ( comments_open() ) : ?>
<div class='comments-form-area'>
  <div class='group-title'>
    <h4><?php echo esc_html__( $comment_reply, 'realshed' );
?></h4>
  </div>

  <?php
// Get commenter data for prefilling fields ( for non-logged-in users )
$commenter = wp_get_current_commenter();
$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );

// Determine submit button text
$comment_send = is_user_logged_in() ? 'Comment Now' : 'Submit Now';
?>

  <form method='post' action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" class='comment-form default-form' id='commentform'>
    <div class='row'>

      <?php if ( is_user_logged_in() ) : ?>
      <?php
// Get current user info for the logged-in message
$current_user = wp_get_current_user();
?>
      <div class='col-lg-12 col-md-12 col-sm-12 form-group'>
        <p class='logged-in-as'>
          <?php
printf(
    __( 'You are logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'realshed' ),
    admin_url( 'profile.php' ),
    $current_user->display_name,
    wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) )
);
?>
        </p>
      </div>

      <?php else : ?>
      <!-- Name field ( only for non-logged-in ) -->
      <div class='col-lg-6 col-md-6 col-sm-12 form-group'>
        <input id='author' name='author' type='text' value="<?php echo esc_attr( $commenter['comment_author'] ); ?>" placeholder="<?php echo esc_attr($comment_author); ?>" <?php echo $aria_req;
?> required>
      </div>

      <!-- Email field ( only for non-logged-in ) -->
      <div class='col-lg-6 col-md-6 col-sm-12 form-group'>
        <input id='email' name='email' type='email' value="<?php echo esc_attr( $commenter['comment_author_email'] ); ?>" placeholder="<?php echo esc_attr($comment_email); ?>" <?php echo $aria_req;
?> required>
      </div>

      <!-- Phone field ( custom ) -->
      <div class='col-lg-6 col-md-6 col-sm-12 form-group'>
        <input id='phone' name='phone' type='text' placeholder="<?php echo esc_attr($phone_number); ?>" required>
      </div>

      <!-- Subject field ( custom ) -->
      <div class='col-lg-6 col-md-6 col-sm-12 form-group'>
        <input id='subject' name='subject' type='text' placeholder="<?php echo esc_attr($subject); ?>" required>
      </div>

      <?php endif;
?>

      <!-- Comment textarea ( common to both ) -->
      <div class='col-lg-12 col-md-12 col-sm-12 form-group'>
        <textarea id='comment' name='comment' placeholder="<?php echo esc_attr($comment_body); ?>" required></textarea>
      </div>

      <!-- Cookies consent ( common to both, as per WP standards ) -->
      <div class='col-lg-12 col-md-12 col-sm-12 form-group'>
        <label class='comment-form-cookies-consent'>
          <input id='wp-comment-cookies-consent' name='wp-comment-cookies-consent' type='checkbox' value='yes'>
          <?php echo $comment_cookies_1;
?><a href='<?php echo esc_url(get_privacy_policy_url()); ?>'><?php echo $comment_cookies_2;
?></a>
        </label>
      </div>

      <!-- Submit button ( common, but text varies by login status ) -->
      <div class='col-lg-12 col-md-12 col-sm-12 form-group message-btn'>
        <button name='submit' type='submit' id='submit' class='theme-btn btn-one'><?php echo esc_html( $comment_send );
?></button>
      </div>

      <?php comment_id_fields();
?>
      <?php do_action( 'comment_form', get_the_ID() );
?>
    </div>
  </form>
</div>
<?php endif;
?>