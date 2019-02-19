<?php
function customizer_sample_register( $wp_customize ) {


  $wp_customize->add_panel('panel1',array(
      'title'=>'Main section',
      'description'=> 'This is panel Description',
      'priority'=> 10,
  ));
  $wp_customize->add_section('section1',array(
      'title'=>'Nested section',
      'priority'=>10,
      'panel'=>'panel1',
  ));

  $wp_customize->add_setting( 'test1' , array(
      'transport' => 'refresh',
  ) );
  $wp_customize->add_control( 'test1', array(
      'label'      => __( 'Test input', 'john-angus' ),
      'section'    => 'section1',
      'settings'   => 'test1',
      'type'   => 'text',
  ) );
}
add_action( 'customize_register', 'customizer_sample_register' );
