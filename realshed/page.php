<?php
/**
* Static Page Template
* @package Realshed
*/

get_header();

// get_template_part( 'template-parts/content/content', 'single');
if ( have_posts() ) :
  while ( have_posts() ) :
      the_post();
      get_template_part( 'template-parts/content/content', 'page');
  endwhile;
endif;

 get_footer();