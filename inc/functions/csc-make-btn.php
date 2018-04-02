<?php

function gp_make_btn_shortcode( $atts, $content = null ) {

  $html = '';
  $defaults = [
    'url' => '//localhost:3000/geekpower/ONPHEC/wordpress/about/overview/',
    'text' => 'Overview'
  ];
  $props = shortcode_atts($defaults, $atts);
  $url = $props['url'];
  $text = $props['text'];

  $html = "<a class=\"btn bg-orange text-white\" href=\"$url\">$text</a>";

  return $html;
}

add_shortcode('gp_make_button', 'gp_make_btn_shortcode');
