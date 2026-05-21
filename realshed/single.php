<?php
/**
* Single Post Template
* @package Realshed
*/

get_header();

// page-title - BREADCRUMB
get_template_part( 'template-parts/sections/breadcrumb', '');

// get_template_part( 'template-parts/content/content', 'single');
if ( have_posts() ) :
  while ( have_posts() ) :
      the_post();
      get_template_part( 'template-parts/content/content', 'single');
  endwhile;
endif;

get_footer();
