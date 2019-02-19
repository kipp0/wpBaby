<?php


function customizer_tagline_register( $wp_customize ) {

  $wp_customize->add_setting( 'mobile_nav_logo' , array(
    'transport' => 'refresh',
  ) );

  // NOTE: FOOTER BACKGROUND IMAGE CONTROLLER
  $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mobile_nav_logo', array(
    'label'      => __( 'Mobile Nav Logo', 'eyelike-optical' ),
    'section'    => 'title_tagline',
    'settings'   => 'mobile_nav_logo',
    'priority'   => 0,
  ) ) );
}
add_action( 'customize_register', 'customizer_tagline_register' );
