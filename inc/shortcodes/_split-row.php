<?php

function gp_split_row_shortcode( $atts, $content = null ) {

    $op = '';
    $df = array(
    	'image' => '',
    	'image_pos' => 'left',
	    'cover' => false,
	    'background' => 'white',
	    'add_class' => '',
    );

    $df = gp_filter_default_shortcode_atts( 'split_row', $df );
    $aa =  shortcode_atts( $df, $atts );

    // dynamically built css classes array
	$classes = array(
		'split-row',
	);

    // this could be one image, or 2 separated by a comma, but 2 images will force us to set $cover = true
    $image = gp_if_set( $aa, 'image' );
    $images = array(); // array may get populated
    $multiple_images = false;

    // in this case, we will use a js lib called ... cocoen?
    if ( strpos( $image, ',' ) !== false && strlen( $image ) > 1 ) {
    	$aa['cover'] = true;
    	$images = gp_something_to_array( $image );
	    $multiple_images = count( $images ) > 1;

    } else {
    	$image = trim( $image );
    	$image = (int) $image;
    }

    $classes[] = $multiple_images ? 'multiple-images' : 'single-image';

    // MAKE SURE COVER GETS CHECKED AFTER IMAGE
	// use background size cover or contain?
    $cover = gp_if_set( $aa, 'cover' );
    $cover = gp_shortcode_boolean( $cover );
	$classes[] = $cover ? 'image-cover' : 'image-no-cover';

    // add a css class? (may not use this)
	$add_class = gp_if_set( $aa, 'add_class' );
	if ( $add_class ) {
		$classes[] = $add_class;
	}

	// black or white
    $background = gp_if_set( $aa, 'background' );
    $background = $background === 'black' ? 'black' : 'white';
    $classes[] = 'bg-' . $background;

    if ( $background === 'black' ) {
    	$classes[] = 'make-text-white';
    }

    // Image Position. Alternates automatically, but can be overriden.
	$image_pos = gp_if_set( $aa, 'image_pos', 'left' );
	$classes[] = $image_pos === 'left' ? 'image-on-left' : 'image-on-right'; // add the class
	$next_image_pos = $image_pos === 'left' ? 'right' : 'left';
	gp_set_default_shortcode_att( 'split_row', 'image_pos', $next_image_pos ); // switch it for next time

	// HTML
	$op .= '<div class="' . gp_parse_css_classes( $classes ) . '">';
	$op .= '<div class="sr-container">';
	$op .= '<div class="sr-flex">';

	// TEXT first
	$op .= '<div class="sr-text">';
	$op .= '<div class="sr-text-inner">';
	$op .= do_shortcode( $content );
	$op .= '</div>'; // sr-text-inner
	$op .= '</div>'; // sr-text

	// IMAGE second
	$op .= '<div class="sr-image">';
	$op .= '<div class="sr-image-inner">';

	// bg image with cover,... the normal way
	if ( $cover ) {

		// we dont define $images array when only 1 image is present i dont think
		if ( $images && count( $images ) > 1 ) {

			// 2 "background" images although they are img tags and use some js effect thing..
			$op .= '<p class="cocoen">';

			$cc = 0;
			foreach ( $images as $img_id ) {
				if ( $cc > 2 ) {
					break;
				}
				// $op .= '<div class="coco-img count-' . $cc . '">';
				//  note that our POS js plugin requires images with the exact same height, hence fixed ratio image size
				$op .= '<img src="' . gp_get_img_url( $img_id, 'reg-md-alt' ) . '">';
				// $op .= GP_Background_Image::get_simple( $img_id, 'square-md' );
				// $op .= GP_Background_Image::get_simple( $img_id, 'square-md' );
//				$op .= GP_Background_Image::get_simple( $img_id, 'square-md' );
//				$op .= '</div>';
				$cc++;
			}
			$op .= '<span class="cocoen-drag my-cocoen-drag">';
			$op .= '<span class="cc-arrows">';
			$op .= '<i class="cc-left fa fa-caret-left" aria-hidden="true"></i>';
			$op .= '<i class="cc-right fa fa-caret-right" aria-hidden="true"></i>';
			$op .= '</span>'; // cc-arrows
			$op .= '</span>'; // cocoen-drag
			$op .= '</p>'; // cocoen
			$op .= '';

		} else {
			// single background image
			$op .= '<div class="bg-img-wrap">';
			$op .= GP_Background_Image::get_simple( $image, array( 'reg-md-alt' ) );
			$op .= '</div>'; // bg-img-wrap
		}

	} else {
		// use built in image size 'large' which does not force a given ratio
		$op .= '<div class="img-wrap">';
		$op .= '<img src="' . gp_get_img_url( $image, 'large' ) . '">';
		$op .= '</div>'; // img-wrap
	}

	$op .= '</div>'; // sr-image-inner
	$op .= '</div>'; // sr-image

	$op .= '</div>';
	$op .= '</div>'; // sr-container
	$op .= '</div>'; // split row


  wp_enqueue_style('split_row_styles', get_template_directory_uri() . '/assets/styles/dist/gp-split-row.css');

    return $op;
}

add_shortcode( 'split_row', 'gp_split_row_shortcode' );
