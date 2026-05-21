<?php
/**
* Blog Posts Index Template
*
* @package Realshed
*/


// page-title - BREADCRUMB
get_template_part( 'template-parts/sections/breadcrumb', '');
?>

<!-- CONTENT -->
<section class='sidebar-page-container blog-standard sec-pad-2'>
    <div class='auto-container'>
        <div class='row clearfix'>
            <!-- Main Content -->
            <div class='col-lg-8 col-md-12 col-sm-12 content-side'>
                <div class='blog-standard-content'>

                    <?php if ( have_posts() ) : ?>

                    <?php while ( have_posts() ) : the_post(); ?>

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
                            <!-- /.image-box -->
                            <?php endif; ?>

                            <div class='lower-content'>
                                <h3>
                                    <a href='<?php the_permalink(); ?>'>
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <!-- title -->
                                <ul class='post-info clearfix'>
                                    <li class='author-box'>
                                        <figure class='author-thumb'>
                                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 50 ); ?>
                                        </figure>
                                        <h5>
                                            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
                                                <?php the_author(); ?>
                                            </a>
                                        </h5>
                                    </li>
                                    <li class="date-box"><a href="<?php echo esc_url( get_month_link( get_the_time('Y'), get_the_time('m') ) ); ?>">
                                            <?php echo get_the_date(); ?>
                                        </a></li>
                                    <!-- <li><?php echo get_the_date(); ?></li> -->
                                    <?php if ( ! has_post_thumbnail() ) { ?>
                                    <li class="category-no-img">
                                        <span>
                                            <?php
                      echo esc_html__('Category:', 'realshed') . ' ';
                      the_category(', ');
                      ?>
                                        </span>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <!-- post-info -->
                                <div class='text'>
                                    <?php the_excerpt(); ?>
                                </div>
                                <!-- text -->
                                <div class='btn-box'>
                                    <a href='<?php the_permalink(); ?>' class='theme-btn btn-two'>
                                        <?php esc_html_e( 'See Details', 'realshed' ); ?>
                                    </a>
                                </div>
                                <!-- btn-box -->
                            </div>
                            <!-- lower-content -->
                        </div>
                        <!-- inner-box -->
                    </div>
                    <!-- news-block-one -->
                    <?php endwhile; ?>

                    <?php realshed_numbering_paginate(); ?>

                    <?php else : ?>

                    <p><?php esc_html_e( 'No posts found.', 'realshed' ); ?></p>

                    <?php endif; ?>

                </div>
                <!-- blog-standard-content -->
            </div>
            <!-- col-lg-8 col-md-12 col-sm-12 content-side -->
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
<!-- section.sidebar-page-container  -->
