<?php
/**
 * Custom options for Tiny MCE Advanced plugin
 *
 * @param $init_array
 *
 * @return mixed
 */
function gp_tiny_mce_custom_styles( $init_array ) {
    // Each array child is a format with it's own settings
    $style_formats = array(
        //    array(
        //       'title' => 'Smaller spacing',
        //       'block' => 'p',
        //       'classes' => 'spacing-small',
        //       'wrapper' => false,
        //       'selector' => 'p, h1, h2, h3, h4, h5, h6',
        //    ),
        //    array(
        //       'title' => 'Very small spacing',
        //       'block' => 'p',
        //       'classes' => 'spacing-very-small',
        //       'wrapper' => false,
        //       'selector' => 'p, h1, h2, h3, h4, h5, h6',
        //    ),
           // array(
           //    'title' => 'No spacing',
           //    'block' => 'p',
           //    'classes' => 'spacing-none',
           //    'wrapper' => false,
           //    'selector' => 'p, h1, h2, h3, h4, h5, h6',
           // ),
    );

    $sizes = array(
        // 'size-h1-xxl' => 'Size: Header 1 XXL',
        // 'size-h1-xl' => 'Size: Header 1 XL',
        // 'size-h1-lg' => 'Size: Header 1 Large',
        // 'size-h1' => 'Size: Header 1',
        // 'size-h2' => 'Size: Header 2',
        // 'size-h3' => 'Size: Header 3',
        // 'size-h4' => 'Size: Header 4',
        // 'size-h5' => 'Size: Header 5',
        // 'size-h6' => 'Size: Header 6',
        // 'size-p' => 'Size: Paragraph', // todo: might need to style this if we end up using it....
        // 'color-orange' => 'Color: Orange',
    );

    if ( ! empty( $sizes ) ) {
        foreach ( $sizes as $css => $label ) {
            $style_formats[] = array(
                'title' => $label,
                'inline' => $css,
                'classes' => $css,
                'wrapper' => false,
                'selector' => 'p, h1, h2, h3, h4, h5, h6',
            );
        }
    }

    $style_formats[] = array(
        'title' => 'Color: Orange',
        'inline' => 'span',
        'classes' => 'text-orange',
        'wrapper' => false,
    );
    $style_formats[] = array(
        'title' => 'Color: Yellow',
        'inline' => 'span',
        'classes' => 'text-yellow',
        'wrapper' => false,
    );

    // $style_formats[] = array(
    //     'title' => 'add Animation Container',
    //     'block' => 'div',
    //     'classes' => 'animation-container',
    //     'wrapper' => true,
    // );

    // $style_formats[] = array(
    //     'title' => 'Reduced Spacing',
    //     'inline' => 'reduced-spacing',
    //     'classes' => 'reduced-spacing',
    //     'wrapper' => false,
    //     'selector' => 'p, h1, h2, h3, h4, h5, h6, li',
    // );

    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array[ 'style_formats' ] = json_encode( $style_formats );

    return $init_array;
}
add_filter( 'tiny_mce_before_init', 'gp_tiny_mce_custom_styles' );
