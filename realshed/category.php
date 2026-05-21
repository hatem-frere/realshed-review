<?php
/**
 * Category Archive Template
 *
 * @package realshed
 */

get_header();

// Breadcrumb
get_template_part( 'template-parts/sections/breadcrumb', '' );
?>

<section class="sidebar-page-container blog-standard cat-page sec-pad-2">
  <div class="auto-container">
    <div class="row clearfix">

      <!-- Main Content -->
      <div class="col-lg-8 col-md-12 col-sm-12 content-side">
        <div class="blog-standard-content">

          <?php if ( have_posts() ) : ?>

            <!-- Archive Header (Clean & SEO Friendly) -->
            <header class="archive-header">
              <h1 class="archive-title">
                <?php single_cat_title(); ?>
              </h1>

              <?php if ( category_description() ) : ?>
                <div class="archive-description">
                  <?php echo wp_kses_post( category_description() ); ?>
                </div>
              <?php endif; ?>
            </header>

            <?php while ( have_posts() ) : the_post(); ?>

              <div class="news-block-one wow fadeInUp animated"
                    data-wow-delay="00ms"
                    data-wow-duration="1500ms">

                <div class="inner-box">

                  <?php if ( has_post_thumbnail() ) : ?>
                    <div class="image-box">
                      <figure class="image">
                        <a href="<?php the_permalink(); ?>">
                          <?php the_post_thumbnail( 'large', array(
                            'alt' => get_the_title()
                          ) ); ?>
                        </a>
                      </figure>
                    </div>
                  <?php endif; ?>

                  <div class="lower-content">

                    <h3>
                      <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                      </a>
                    </h3>

                    <ul class="post-info clearfix">
                      <li class="author-box">
                        <figure class="author-thumb">
                          <?php echo get_avatar( get_the_author_meta( 'ID' ), 50 ); ?>
                        </figure>
                        <h5>
                          <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                            <?php the_author(); ?>
                          </a>
                        </h5>
                      </li>

                      <li class="date-box">
                        <a href="<?php echo esc_url( get_month_link( get_the_time('Y'), get_the_time('m') ) ); ?>">
                                                <?php echo get_the_date(); ?>
                                            </a></li>
                      <!-- <li><?php echo esc_html( get_the_date() ); ?></li> -->
                    </ul>

                    <div class="text">
                      <?php the_excerpt(); ?>
                    </div>

                    <div class="btn-box">
                      <a href="<?php the_permalink(); ?>" class="theme-btn btn-two">
                        <?php esc_html_e( 'See Details', 'realshed' ); ?>
                      </a>
                    </div>

                  </div>
                </div>
              </div>
              <!-- /.news-block-one -->

            <?php endwhile; ?>

            <?php realshed_numbering_paginate(); ?>

          <?php else : ?>

            <p><?php esc_html_e( 'No posts found in this category.', 'realshed' ); ?></p>

          <?php endif; ?>

        </div>
      </div>
      <!-- /.col-lg-8 col-md-12 col-sm-12 content-side -->

      <!-- Sidebar -->
      <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
        <div class="blog-sidebar">
          <?php
          if ( is_active_sidebar( 'realshed-sidebar' ) ) {
            dynamic_sidebar( 'realshed-sidebar' );
          } else {
            get_sidebar();
          }
          ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php get_footer(); ?>
