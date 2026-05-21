<?php
/**
* Blog Posts Index Template
*
* @package Realshed
*/

get_header();

// Page Title Background
$blog_bg = get_theme_mod( 'realshed_blog_bg', get_theme_file_uri( '/assets/images/background/page-title-5.jpg' ) );
?>

<section class='page-title centred' style="background-image: url(<?php echo esc_url( $blog_bg ); ?>);">
  <div class='auto-container'>
    <div class='content-box clearfix'>
      <h1><?php esc_html_e( 'Blog Standard', 'realshed' );
?></h1>
      <ul class='bread-crumb clearfix'>
        <li>
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php esc_html_e( 'Home', 'realshed' );
?>
          </a>
        </li>
        <li><?php esc_html_e( 'Blog Standard', 'realshed' );
?></li>
      </ul>
    </div>
  </div>
</section>

<section class='sidebar-page-container blog-standard sec-pad-2'>
  <div class='auto-container'>
    <div class='row clearfix'>

      <!-- Main Content -->
      <div class='col-lg-8 col-md-12 col-sm-12 content-side'>
        <div class='blog-standard-content'>

          <?php if ( have_posts() ) : ?>

          <?php while ( have_posts() ) : the_post();
?>

          <div class='news-block-one wow fadeInUp animated' data-wow-delay='00ms' data-wow-duration='1500ms'>
            <div class='inner-box'>

              <?php if ( has_post_thumbnail() ) : ?>
              <div class='image-box'>
                <figure class='image'>
                  <a href='<?php the_permalink(); ?>'>
                    <?php the_post_thumbnail( 'large', array( 'alt' => get_the_title() ) );
?>
                  </a>
                </figure>

                <?php
// Display first category
$categories = get_the_category();
if ( ! empty( $categories ) ) {
    echo '<span class="category">' . esc_html( $categories[ 0 ]->name ) . '</span>';
}
?>
              </div>
              <?php endif;
?>

              <div class='lower-content'>
                <h3>
                  <a href='<?php the_permalink(); ?>'>
                    <?php the_title();
?>
                  </a>
                </h3>

                <ul class='post-info clearfix'>
                  <li class='author-box'>
                    <figure class='author-thumb'>
                      <?php echo get_avatar( get_the_author_meta( 'ID' ), 50 );
?>
                    </figure>
                    <h5>
                      <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
                        <?php the_author();
?>
                      </a>
                    </h5>
                  </li>
                  <li><?php echo get_the_date();?></li>
                </ul>

                <div class='text'>
                  <?php the_excerpt();
?>
                </div>

                <div class='btn-box'>
                  <a href='<?php the_permalink(); ?>' class='theme-btn btn-two'>
                    <?php esc_html_e( 'See Details', 'realshed' );
?>
                  </a>
                </div>
              </div>

            </div>
          </div>

          <?php endwhile;
?>

          <?php
/**
* PAGINATION
*
* Method 1: Standard numbered pagination ( Recommended )
*/
realshed_numbering_paginate();

/**
* Method 2: Custom icons
* realshed_numbering_paginate( array(
    *     'prev_text' => '<i class="fas fa-arrow-left"></i>',
    *     'next_text' => '<i class="fas fa-arrow-right"></i>',
    *     'mid_size'  => 3,
    * ) );
    */

    /**
    * Method 3: With page info
    * realshed_pagination_with_info();
    */

    /**
    * Method 4: Simple prev/next only
    * realshed_simple_pagination();
    */

    /**
    * Method 5: Ajax load more
    * realshed_ajax_load_more();
    */
    ?>

          <?php else : ?>

          <p><?php esc_html_e( 'No posts found.', 'realshed' );
    ?></p>

          <?php endif;
    ?>

        </div>
      </div>

      <!-- Sidebar -->
      <div class='col-lg-4 col-md-12 col-sm-12 sidebar-side'>
        <div class='blog-sidebar'>
          <?php
    if ( is_active_sidebar( 'realshed-sidebar' ) ) :
    dynamic_sidebar( 'realshed-sidebar' );
    else :
    get_sidebar();
    endif;
    ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php get_footer();
    ?>
