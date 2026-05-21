<?php
/**
 * The template for displaying Author Archive pages
 */

get_header();

// Get the author data
$author_name       = get_query_var( 'author_name' );
$author            = get_query_var( 'author' );
$curauth           = ! empty( $author_name ) ? get_user_by( 'slug', sanitize_user( $author_name ) ) : get_userdata( intval( $author ) );
$author_id         = $curauth ? $curauth->ID : 0;
$author_name       = $curauth->display_name;
$author_bio        = $curauth->description;
$author_avatar     = get_avatar_url($author_id, ['size' => 300]);
$author_website    = $curauth->user_url;
$author_posts_count = count_user_posts($author_id);

// Custom Meta Fields (Assumes you use a plugin like ACF or your theme has these fields)
$job_title = get_user_meta($author_id, 'job_title', true);
$phone     = get_user_meta($author_id, 'phone', true);
$address   = get_user_meta($author_id, 'address', true);

// Social Links Array
$social_fields = [
    'facebook'  => get_user_meta($author_id, 'facebook', true),
    'twitter'   => get_user_meta($author_id, 'twitter', true),
    'linkedin'  => get_user_meta($author_id, 'linkedin', true),
    'instagram' => get_user_meta($author_id, 'instagram', true),
    'youtube'   => get_user_meta($author_id, 'youtube', true),
    'pinterest' => get_user_meta($author_id, 'pinterest', true),
    'github'    => get_user_meta($author_id, 'github', true),
];

// Breadcrumb
get_template_part('template-parts/sections/breadcrumb', '');
?>

<section class="author-page sidebar-page-container sec-pad-2">
    <div class="auto-container">
        <div class="row clearfix">

            <div class="col-lg-8 col-md-12 col-sm-12 content-side">

                <div class="author-profile-card blog-standard-content wow fadeInUp" data-wow-delay="0ms" data-wow-duration="1500ms">
                    <div class="inner-box">
                        <div class="author-avatar">
                            <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>">
                        </div>

                        <div class="author-info">
                            <h2 class="author-name"><?php echo esc_html($author_name); ?></h2>

                            <?php if (!empty($job_title)) : ?>
                                <p class="author-job-title"><?php echo esc_html($job_title); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($author_bio)) : ?>
                                <div class="author-bio">
                                    <p><?php echo wp_kses_post($author_bio); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="author-contact">

                                <?php if (!empty($phone)) : ?>
                                    <p><i class="fa-solid fa-square-phone"></i> <strong><?php echo esc_html__( 'Phone:', 'realshed' ) ?></strong> <?php echo esc_html($phone); ?></p>
                                <?php endif; ?>

                                <?php if (!empty($address)) : ?>
                                    <p><i class="fas fa-map"></i> <strong><?php echo esc_html__( 'Address:', 'realshed' ) ?></strong> <?php echo esc_html($address); ?></p>
                                <?php endif; ?>

                                <?php if (!empty($author_website)) : ?>
                                    <p><i class="fas fa-globe"></i> <strong><?php echo esc_html__( 'Website:', 'realshed' ) ?></strong>
                                        <a href="<?php echo esc_url($author_website); ?>" target="_blank"><?php echo esc_html($author_website); ?></a>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <?php
                            //$has_social = array_filter($social_fields);
                            //if ($has_social) : ?>
                                <!-- <div class="author-social">
                                    <ul class="social-links clearfix">
                                        <?php foreach ($social_fields as $platform => $url) :
                                            if (!empty($url)) : ?>
                                                <li>
                                                    <a href="<?php echo esc_url($url); ?>" target="_blank">
                                                        <i class="fab fa-<?php echo esc_attr($platform); ?>"></i>
                                                    </a>
                                                </li>
                                        <?php endif; endforeach; ?>
                                    </ul>
                                </div> -->
                            <?php //endif; ?>
                            <?php
// 1. Filter out empty social links
$has_social = array_filter($social_fields);

if ($has_social) : ?>
    <div class="author-social">
        <ul class="social-links clearfix">
            <?php
            foreach ($social_fields as $platform => $url) :
                if (!empty($url)) :

                    /**
                     * 2. Icon Mapping Logic
                     * Matches your array keys (database) to Font Awesome classes (display)
                     */
                    $icon_name = esc_attr($platform);

                    if ($platform === 'facebook') {
                        $icon_name = 'facebook-f';
                    } elseif ($platform === 'twitter' || $platform === 'x') {
                        $icon_name = 'x-twitter';
                    }
                    ?>

                    <li>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="nofollow noopener">
                            <i class="fab fa-<?php echo $icon_name; ?>"></i>
                        </a>
                    </li>

                <?php endif;
            endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- /.author-profile-card -->

                <div class="author-posts mt-4">
                    <h3 class="section-title mb-4 pt-4">
                        <?php esc_html_e('Publications by:', 'realshed'); ?> <span><?php echo esc_html($author_name); ?></span>
                    </h3>

                    <?php if (have_posts()) : ?>
                        <div class="author-articles-grid">
                            <?php while (have_posts()) : the_post(); ?>
                            <!-- <div class="inner-box"> -->
              <!-- <div class="lower-content"> -->
                                <!-- <article class="author-article-item">
                                    <div class="post-date"><?php echo get_the_date(); ?></div>
                                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                    <p><?php the_excerpt();?></p>
                                </article> -->
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
                    <!-- </div> -->
                    <!-- </div> -->
                            <?php endwhile; ?>
                        </div>

                        <!-- <div class="pagination-wrapper mt-50">
                            <?php //the_posts_pagination(['prev_text' => '<i class="fas fa-angle-left"></i>', 'next_text' => '<i class="fas fa-angle-right"></i>']); ?>
                        </div> -->
                        <?php realshed_numbering_paginate(); ?>
                    <?php else : ?>
                        <div class="no-posts-found"><h3><?php esc_html_e('No publications yet', 'realshed'); ?></h3></div>
                    <?php endif; ?>
                </div>
                <!-- /.author-posts -->
            </div>
            <!-- /.col-lg-8 col-md-12 -->

            <!-- Sidebar -->
            <div class="col-lg-4 col-md-12">
                <?php if (is_active_sidebar('realshed-sidebar')) : ?>
                    <div class="blog-sidebar">
                        <?php dynamic_sidebar('realshed-sidebar'); ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
