<?php


function mytheme_customize_footer_register( $wp_customize ) {
   //All our sections, settings, and controls will be added here
   // NOTE: class for WYSIWIG editor
   class WYSIWIG_Custom_Control extends WP_Customize_Control {
         /**
          * Render the content on the theme customizer page
          */
         public function render_content() {

           ?>
              <label>
              <!-- <span class="customize-text_editor"><?php echo esc_html( $this->label ); ?></span> -->
              <?php// $settings = array(
                  //      'textarea_name' => $this->id
                //    );
                  //  wp_editor($this->value(), $this->id, $settings ); ?>
              </label>
              <?php
          }
   }

   $wp_customize->add_panel('footer_panel',array(
        'title'=>'Footer',
        'description'=> 'This is panel Description',
        'priority'=> 100,
    ));

   // NOTE: FOOTER: SECTION
   $wp_customize->add_section( 'footer_default_section' , array(
     'title'      => __( 'Default', 'eyelike' ),
     'priority'   => 100,
     'panel'      => 'footer_panel'
   ) );
   $wp_customize->add_section( 'footer_contact_section' , array(
     'title'      => __( 'Contact', 'eyelike' ),
     'priority'   => 200,
     'panel'      => 'footer_panel'
   ) );
   $wp_customize->add_section( 'footer_links_section' , array(
     'title'      => __( 'Menu', 'eyelike' ),
     'priority'   => 300,
     'panel'      => 'footer_panel'
   ) );

   // NOTE: FOOTER: SETTINGS
   // $wp_customize->add_setting( 'footer_bg_color' , array(
   //   'default'   => '#098642',
   //   'transport' => 'refresh',
   // ) );

   $wp_customize->add_setting( 'footer_logo_image' , array(
     'transport' => 'refresh',
   ) );
   $wp_customize->add_setting( 'footer_slogo_image' , array(
     'transport' => 'refresh',
   ) );
   $wp_customize->add_setting( 'footer_elogo_image' , array(
     'transport' => 'refresh',
   ) );

   $wp_customize->add_setting( 'footer_contact_info_china_town' , array(
     'transport' => 'refresh',
   ) );
   $wp_customize->add_setting( 'footer_contact_info_north_york' , array(
     'transport' => 'refresh',
   ) );
   $wp_customize->add_setting( 'footer_contact_info_uptown' , array(
     'transport' => 'refresh',
   ) );

   // NOTE: FOOTER BACKGROUND COLOR CONTROLLER
   // $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_link_color', array(
   //   'label'      => __( 'Background Color', 'eyelike' ),
   //   'section'    => 'footer_default_section',
   //   'settings'   => 'footer_bg_color',
   // ) ) );

   // NOTE: FOOTER BACKGROUND IMAGE CONTROLLER
   $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'footer_logo_image', array(
     'label'      => __( 'Logo', 'eyelike' ),
     'section'    => 'footer_default_section',
     'settings'   => 'footer_logo_image',
   ) ) );

   $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'footer_slogo_image', array(
     'label'      => __( 'Saigon Logo', 'eyelike' ),
     'section'    => 'footer_default_section',
     'settings'   => 'footer_slogo_image',
   ) ) );

   $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'footer_elogo_image', array(
     'label'      => __( 'Eyelike Logo', 'eyelike' ),
     'section'    => 'footer_default_section',
     'settings'   => 'footer_elogo_image',
   ) ) );


   // NOTE: FOOTER CONTACT INFO
   // email
   $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_contact_info_china_town', array(
     'label'      => __( 'China Town Phone Number', 'eyelike' ),
     'section'    => 'footer_contact_section',
     'settings'   => 'footer_contact_info_china_town',
     'type'   => 'text',
   ) ) );
   // phone
   $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_contact_info_north_york', array(
     'label'      => __( 'North York Phone Number', 'eyelike' ),
     'section'    => 'footer_contact_section',
     'settings'   => 'footer_contact_info_north_york',
     'type'   => 'text',
   ) ) );
   // address
   $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_contact_info_uptown', array(
     'label'      => __( 'Info Uptown Phone Number', 'eyelike' ),
     'section'    => 'footer_contact_section',
     'settings'   => 'footer_contact_info_uptown',
     'type'   => 'text',
   ) ) );


   // NOTE: FOOTER CONTACT FORM
   // $wp_customize->add_control( new WYSIWIG_Custom_Control( $wp_customize, 'footer_contact-form', array(
   //   'label'      => __( 'Contact Form', 'tara' ),
   //   'section'    => 'footer_section',
   //   'settings'   => 'footer_contact_form',
   //   // 'type'   => 'textarea',
   // ) ) );






}
add_action( 'customize_register', 'mytheme_customize_footer_register' );








//
// function mytheme_customize_css()
// {
//
//
//   return "
//   <style type=\"text/css\">
//       h1 { color: ". get_theme_mod('footer_bg_color', '#000000') ." }
//   </style>
//   ";
// }
// add_action( 'wp_head', 'mytheme_customize_css');
