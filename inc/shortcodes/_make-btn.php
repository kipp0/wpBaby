<?php

function gp_make_btn_shortcode( $atts, $content = null ) {

  $html = '';
  $defaults = [
    'url' => '#',
    'text' => 'Click Here',
    'classes' => 'bg-green text-white'
  ];
  $props = shortcode_atts($defaults, $atts);
  $url = $props['url'];
  $text = $props['text'];
  $classes = $props['classes'];

  $html = "<a class=\"btn $classes\" href=\"$url\">$text</a>";

  return $html;
}

add_shortcode('gp_make_button', 'gp_make_btn_shortcode');
