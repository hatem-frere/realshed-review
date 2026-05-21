<?php
/**
 * Date Archive Template
 *
 * @package realshed
 */

get_header();

$current_tag = single_tag_title('', false);
$tag_description = tag_description();

// Breadcrumb
get_template_part( 'template-parts/sections/breadcrumb', '' );
?>

<section class="sidebar-page-container blog-standard archive-page sec-pad-2">
    <div class="auto-container">
        <div class="row clearfix">

            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="blog-standard-content">

                    <?php if ( have_posts() ) :
                        $animation_delay = 0;

                        while ( have_posts() ) : the_post();
                        $post_id     = get_the_ID();
                        $post_title  = get_the_title();
                        $post_url    = get_permalink();
                        $post_excerpt = get_the_excerpt();
                        $post_date   = get_the_date();
                        $post_author = get_the_author();
                            $animation_delay += 100;
                        ?>

                        <div class="news-block-one wow fadeInUp animated" data-wow-delay="<?php echo esc_attr($animation_delay); ?>ms" data-wow-duration="1500ms">
                            <div class="inner-box">

                                <?php if ( has_post_thumbnail() ) : ?>
                                <div class="image-box">
                                    <figure class="image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( 'large', array( 'alt' => get_the_title() ) ); ?>
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

                                        <li><?php echo esc_html( get_the_date() ); ?></li>
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

                        <?php endwhile; ?>

                        <div class="pagination-wrapper">
                            <?php realshed_numbering_paginate(); ?>
                        </div>

                    <?php else : ?>
                        <p><?php esc_html_e( 'No posts found for this
                        tag.', 'realshed' ); ?></p>
                    <?php endif; ?>

                </div>
            </div>

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
