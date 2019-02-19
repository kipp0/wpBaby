<?php

function global_scripts() {
  global $wp_styles; // Call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
  global $pagename;
  // Adding styles file in the header
  wp_enqueue_style( 'global-styles', get_template_directory_uri() . '/assets/styles/dist/style.css', array('aos-styles'), false, 'all' );
  wp_enqueue_style( 'select2-styles', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), false, 'all' );
  wp_enqueue_style( 'slick-styles', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), false, 'all' );
  wp_enqueue_style( 'aos-styles', 'https://unpkg.com/aos@2.3.1/dist/aos.css', array(), false, 'all' );
  wp_enqueue_style( 'fancy-styles', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.4.1/jquery.fancybox.min.css', array(), false, 'all' );

  // Adding scripts file in the head
  // wp_enqueue_script('places-async-defer', "", array('jquery'), null, false);
  
  // Adding scripts file in the footer
  wp_enqueue_script( 'global-js', get_template_directory_uri() . '/assets/scripts/dist/scripts.js', array( 'jquery', 'select2-js', 'aos-js'), false, true );
  wp_register_script( 'select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js');
  wp_enqueue_script( 'aos-js', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array( 'jquery' ), false, true );
  wp_enqueue_script( 'fancy-js', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.4.1/jquery.fancybox.min.js', array( 'jquery' ), false, true );
  // wp_enqueue_script('map-async-defer', "", array('global-js'), null, true);
  
  // include fontawesome to your project
  // wp_enqueue_script( 'font-awesome', 'https://use.fontawesome.com/releases/v5.0.6/js/all.js', array( 'jquery' ), '', true );
  // Comment reply script for threaded comments
  // if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
  //   wp_enqueue_script( 'comment-reply' );
  // }
}
// add_action('wp_head', 'global_scripts', 999);
add_action('wp_enqueue_scripts', 'global_scripts', 999);
