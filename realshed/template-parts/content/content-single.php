<?php
/**
* Single Post Template
*
* @package Realshed
*/
?>
<section class='sidebar-page-container blog-details sec-pad-2'>
  <div class='auto-container'>
    <div class='row clearfix'>
      <div class='col-lg-8 col-md-12 col-sm-12 content-side'>
        <div class='blog-details-content'>
          <div class='news-block-one'>
            <div class='inner-box'>
              <?php if ( has_post_thumbnail() ) {
    ?>
              <div class='image-box'>
                <figure class='image'>
                  <?php the_post_thumbnail();
    ?>
                </figure>
                <span class='category'><?php the_category( ', ' );
    ?></span>
              </div>
              <?php }
    ?>
              <div class='lower-content'>
                <h3><?php the_title();
    ?></h3>
                <ul class='post-info clearfix'>
                  <li class='author-box'>
                    <figure class='author-thumb'>
                      <?php echo get_avatar( get_the_author_meta( 'ID' ), 50 ); ?>
                    </figure>
                    <h5>
                      <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                        <?php the_author(); ?>
                      </a>
                    </h5>
                  </li>

                  <li><?php the_time( 'F jS, Y' ) ?></li>
                  <?php if ( ! has_post_thumbnail() ) {
        ?>
                  <li class='category-no-img'>
                    <span>
                      <?php
        echo esc_html__( 'Category:', 'realshed' ) . ' ';
        the_category( ', ' );
        ?>
                    </span>
                  </li>
                  <?php }
        ?>
                </ul>
                <div class='text'>
                  <?php the_content();
        ?>
                </div>
                <div class='post-tags'>
                  <ul class='tags-list clearfix'>
                    <li>
                      <?php
        echo '<h5>' . esc_html__( 'Tags:', 'realshed' ) . '</h5>';
        ?>
                    </li>
                    <?php
        if ( is_singular( 'post' ) ) {
            global $post;
            if ( has_tag() ) {
                foreach ( get_the_tags( $post->ID ) as $tag ) {
                    ?>
                    <li>
                      <?php
                    echo '<a href=" ' . get_tag_link( $tag->term_id ) . ' ">' . $tag->name . '</a>';
                    ?>
                    </li>
                    <?php
                }
            } else {
                echo '<li><a class="no-tags">' . esc_html__( 'No Tags', 'realshed' ) . '</a></li>';
            }
        }
        ?>
                  </ul>
                </div>
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