<?php
/**
* Page Content Template
*
* @package Realshed
*/
?>
<section class="sidebar-page-container blog-details sec-pad-2">
    <div class="auto-container">
        <div class="row clearfix">
          <div class="col-lg-8 col-md-12 col-sm-12 content-side">
          <div class="blog-details-content">
          <div class="news-block-one">
          <div class="inner-box">
          <?php if ( has_post_thumbnail() ) { ?>
          <div class="image-box">
            <figure class="image">
              <?php the_post_thumbnail(); ?>
            </figure>
            <span class="category"><?php the_category( ', ' ); ?></span>
          </div>
          <?php } ?>
          <div class="lower-content">
            <h3><?php the_title(); ?></h3>
            <ul class="post-info clearfix">
              <?php
$author_id    = get_post_field( 'post_author', get_the_ID() );
$author_name  = get_the_author_meta( 'display_name', $author_id );
$author_url   = get_author_posts_url( $author_id );
$author_avatar = get_avatar_url( $author_id, array( 'size' => 50 ) );
if ( ! $author_avatar ) {
    $gender = get_the_author_meta( 'gender', $author_id );
    if ( $gender === 'male' ) {
        $author_avatar = get_theme_file_uri( '/assets/images/icons/man.png' );
    } elseif ( $gender === 'female' ) {
        $author_avatar = get_theme_file_uri( '/assets/images/icons/woman.png' );
    } else {
        $author_avatar = get_theme_file_uri( '/assets/images/icons/profile-picture.png' );
    }
}
?>

<li class="author-box">
    <figure class="author-thumb">
        <img src="<?php echo esc_url( $author_avatar ); ?>" alt="<?php echo esc_attr( $author_name ); ?>">
    </figure>
    <h5>
        <a href="<?php echo esc_url( $author_url ); ?>">
            <?php echo esc_html( $author_name ); ?>
        </a>
    </h5>
</li>

              <li><?php the_time('F jS, Y') ?></li>
            </ul>
            <div class="text">
              <?php the_content(); ?>
            </div>
            <div class="post-tags"></div>
          </div>
          </div>
          </div>
          <!-- /.news-block-one -->
          <?php
          // Load dynamic comments if comments are open or there are comments
          if ( comments_open() || get_comments_number() ) {
          comments_template();
          }
          ?>
          </div>
          <!-- /.blog-details-content -->
          </div>
<!-- /.col-lg-8 col-md-12 col-sm-12 content-side -->
<!-- Sidebar -->
      <div class='col-lg-4 col-md-12 col-sm-12 sidebar-side'>
        <div class='blog-sidebar'>
          <?php
            if ( !is_active_sidebar( 'realshed-sidebar' ) ) {
                get_sidebar();
            } else {
                dynamic_sidebar( 'realshed-sidebar' );
            }
            ?>
        </div>
        <!-- blog-sidebar -->
      </div>
      <!-- col-lg-4 col-md-12 col-sm-12 sidebar-side -->
</div>
<!-- row -->
</div>
<!-- auto-container -->
</section>
<!-- section.sidebar-page-container.blog-details -->
