<?php

remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop', 99 );
add_filter( 'the_content', 'shortcode_unautop', 100 );


function gp_make_wrapper_shortcode( $atts, $content = null ) {

  $html = '';
  $defaults = [
    'alignment' => 'aligncenter',
    'd' => 4
  ];
  $props = shortcode_atts($defaults, $atts);
  $alignment = $props['alignment'];

  $html = "
    <div class=\"$alignment\">".do_shortcode($content)."</div>";
  return $html;
}

add_shortcode('gp_wrapper', 'gp_make_wrapper_shortcode');
