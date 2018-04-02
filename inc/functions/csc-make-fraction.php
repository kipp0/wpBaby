<?php

// remove added <p> tags inside of shortcodes
// sometimes this helps fix (/not break) shortcodes,
// sometimes i dont know if it does anything at all..
// maybe depends on presence of tinymce advanced plugin
// It also can break [wp_caption] shortcodes
// so use it or don't, either you're screwed.
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop', 99 );
add_filter( 'the_content', 'shortcode_unautop', 100 );


function gp_make_fraction_shortcode( $atts, $content = null ) {

  $html = '';
  $defaults = [
    'n' => 1,
    'd' => 4
  ];
  $props = shortcode_atts($defaults, $atts);
  $n = $props['n'];
  $d = $props['d'];

  $html = "
    <div class=\"frac\">
      <span>$n</span>
      <span class=\"symbol\">/</span>
      <span class=\"bottom\">$d</span>
    </div>";

    wp_enqueue_style('fraction-styles', get_template_directory_uri() . '/assets/styles/dist/gp-make-fraction.css');
  return $html;
}

add_shortcode('gp_make_fraction', 'gp_make_fraction_shortcode');
