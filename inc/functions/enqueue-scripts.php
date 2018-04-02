<?php

function my_body_class($classes) {
    $classes[] = 'animate';
    return $classes;
}

function global_scripts() {
  global $wp_styles; // Call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
  global $pagename;
    // Adding scripts file in the footer
    wp_enqueue_script( 'global-js', get_template_directory_uri() . '/assets/scripts/dist/scripts.js', array( 'jquery' ), false, true );
    // include fontawesome to your project
    // wp_enqueue_script( 'font-awesome', 'https://use.fontawesome.com/releases/v5.0.6/js/all.js', array( 'jquery' ), '', true );
    if (is_front_page()) {
      # code...
      wp_enqueue_style( 'front-page-styles', get_template_directory_uri() . '/assets/styles/dist/front-page-style.css', array(), false, 'all' );
    } else {
      # code...
      // Register main stylesheet
      if (isset($_COOKIE['prevPage'])) {
        # code...
        add_filter('body_class', 'my_body_class');
      }
      wp_enqueue_style( 'global-styles', get_template_directory_uri() . '/assets/styles/dist/style.css', array(), false, 'all' );
    }


    // Comment reply script for threaded comments
    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
      wp_enqueue_script( 'comment-reply' );
    }
}
// add_action('wp_head', 'global_scripts', 999);
add_action('wp_enqueue_scripts', 'global_scripts', 999);
