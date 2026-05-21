<aside id="secondary" class="widget-area">

  <?php do_action( 'dynamic_sidebar_before', 'realshed-sidebar', is_active_sidebar('realshed-sidebar') ); ?>

  <?php
    if ( ! dynamic_sidebar( 'realshed-sidebar' ) ) :
    ?>
  <!-- الويدجات الافتراضية هنا -->
  <?php //the_widget( 'WP_Widget_Search' ); ?>
  <?php //the_widget( 'WP_Widget_Recent_Posts', 'title=آخر المقالات&number=5' ); ?>
  <div id="primary" class="sidebar">
    <div class="blog-sidebar">
      <?php //do_action( 'before_sidebar' ); ?>

      <?php //if ( ! is_active_sidebar( 'realshed_sidebar' ) ) : ?>

      <div class="sidebar-widget search-widget">
        <?php get_search_form(); ?>
      </div>
      <!-- #search -->

      <div class="sidebar-widget category-widget">
        <div class="widget-title">
          <h4><?php _e( 'Archives', 'realshed' ); ?></h4>
        </div>
        <div class="widget-content">
          <ul class="category-list clearfix">
            <?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
          </ul>
        </div>
      </div>
      <!-- #archives -->

      <div id="meta" class="sidebar-widget meta-widget">
        <div class="widget-title">
          <h4><?php _e( 'Meta', 'realshed' ); ?></h4>
        </div>
        <div class="widget-content">
          <ul class="meta-list clearfix">
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <?php wp_meta(); ?>
          </ul>
        </div>
      </div>
      <!-- #meta -->

      <?php //endif; ?>
    </div>
  </div>
  <!-- باقي الويدجات الافتراضية ... -->
  <?php endif; ?>

</aside>
