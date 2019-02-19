<?php
function customizer_home_register( $wp_customize ) {
  // NOTE: FOOTER: SECTION
  $wp_customize->add_section( 'front_page_section' , array(
    'title'      => __( 'Front Page', 'eyelike-optical' ),
    'priority'   => 110,
  ) );
  $wp_customize->add_panel( 'panel_id', array(
      'priority'       => 100,
      'capability'     => 'edit_theme_options',
      'theme_supports' => '',
      'title'          => 'test',
      'description'    => '',
  ) );
  $wp_customize->add_setting( 'front_page_test' , array(
    'transport' => 'refresh',
  ) );

  // NOTE: FOOTER BACKGROUND IMAGE CONTROLLER
  $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'front_page_test', array(
    'label'      => __( 'Mobile Nav Logo', 'eyelike-optical' ),
    'section'    => 'front_page_section',
    'settings'   => 'front_page_test',
    'priority'   => 0,
  ) ) );
}
add_action( 'customize_register', 'customizer_home_register' );
