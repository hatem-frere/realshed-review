<div class="widget-title">
  <h4><?php esc_html_e( 'Search', 'realshed' ); ?></h4>
</div>
<div class="search-inner">
  <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
    <div class="form-group">
      <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search', 'realshed' ); ?>" required />
      <button type="submit">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </form>
</div>