<?php

/**
 * Button Shortcode
 *
 * @param      $atts
 * @param null $content
 *
 * @return string
 */
function gp_button_shortcode( $atts, $content = null ) {

	$defaults = array(
		'text' => 'Learn More',
		'url' => '',
		'type' => '', // nothing or 'round'
		'target' => '', // target="_blank" ?
    // NOTE: check to see if it does something
		'align' => '', // left or center, not centre
		'size' => '', // 'large' or leave blank for small
		'color' => '',
		'title' => '', // <a title="">
		'arrow' => false,
		'icon' => '', // ie. fa-play, dont use arrow true at the same time
		'add_class' => '',
	);

	$defaults = gp_filter_default_shortcode_atts( 'gp_button', $defaults );
	$aa = shortcode_atts( $defaults, $atts );

	$classes = array(
		'button-1',
		'shortcode-button',
	);

	$add_class = gp_if_set( $aa, 'add_class' );

	if ( $add_class ) {
		$classes[] = $add_class;
	}

	// button text
	$text = gp_if_set( $aa, 'text', '' );
	$arrow = gp_if_set( $aa, 'arrow', false );
	$arrow = gp_shortcode_boolean( $arrow, true );

	$icon = gp_if_set( $aa, 'icon', false );
	$icon = trim( $icon );

	if ( $icon ) {

		if ( strpos( $icon, 'fa-' ) === false ) {
			$icon = 'fa-' . $icon;
		}

		$text .= '<i class="fa ' . $icon . '"></i>';

	} else {
		if ( $arrow ) {
			$classes[] = 'has-arrow';
			$text .= '<i class="fa fa-angle-right"></i>';
		}
	}

	$type = gp_if_set( $aa, 'type', false );
	if ( $type === 'round' ) {
		$classes[] = 'type-round';
	}

	// url, slug, or post_id
	$url = gp_if_set( $aa, 'url' );
	$href = gp_href_from_shortcode_att( $url );

	// size
	$size = gp_if_set( $aa, 'size' );
	if ( $size ) {
		$classes[] = 'size-' . $size;
	}

	// color
	$color = gp_if_set( $aa, 'color' );
	if ( $color ) {
		$classes[] = 'color-' . $color;
	}

	// align
	$align = gp_if_set( $aa, 'align' );
	if ( $align ) {
		$classes[] = 'align-' . $align;
	}

	// other attributes
	$title = gp_if_set( $aa, 'title' );
	$target = gp_if_set( $aa, 'target' );

	// the <a> tag
	$inner_atts = array(
		'href' => $href,
		'target' => $target,
		'title' => $title,
	);

	$inner_html = gp_atts_to_container( 'a', $inner_atts, true, $text );

	// the outer </div>
	$outer_atts = array(
		'class' => gp_parse_css_classes( $classes ),
	);

	// example output: <div class="button-1 size-whatever color-something"><a target="_blank" href="http...">The Text</a></div>
	return gp_atts_to_container( 'div', $outer_atts, true, $inner_html );
}

add_shortcode( 'gp_button', 'gp_button_shortcode' );
