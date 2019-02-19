<?php

/******************************************************************
*
*
*                  To Making Your Life Easy
*
*                            o  o
*                          --------
*
*                     Toast to that Baby..
******************************************************************/



/**
 * Wrapper for wp_get_attachment_image_src..
 *
 * @param $image_id
 * @param string $size
 *
 * @return bool
 */
 function add_hero_class() {
   // code...

   add_filter( 'body_class','my_body_class' );
   function my_body_class($classes) {

     $classes[] = 'has-hero';

     return $classes;
   }
 }

/**
 * Function to beatify varible output with or without html special chars.
 * @param $var
 * @param $htmlspecialchars
 */
function beautify_var( $var, $htmlspecialchars = false ) {

    $body = print_r( $var, true );

    if ( $htmlspecialchars ) {
        $body = htmlspecialchars( $body );
    }

    $ret = '<pre>' . $body . '</pre>';
    return $ret;
}

 function gp_add_body_class( $cls ){
     $current = gp_get_global( 'body_classes', array() );
     $current = $current && is_array( $current ) ? $current : array();
     $current[] = $cls;
     gp_set_global( 'body_classes', $current );
 }

 function gp_get_body_classes(){
     $current = gp_get_global( 'body_classes', array() );
     $classes = gp_parse_css_classes( $current );
     return $classes ? $classes : '';
 }

 // body_class( gp_get_body_classes() );
/**
 * Function to check if page is child of a particular page.
 *
 * @param $parent_post_id
 *
 * @return bool
 */
 function is_page_child($pid) {// $pid = The ID of the page we're looking for pages underneath
   global $post;         // load details about this page
   $anc = get_post_ancestors( $post->ID );
   foreach($anc as $ancestor) {
       if(is_page() && $ancestor == $pid) {
           return true;
       }
   }
   return false;
   // if(is_page()&&(is_page($pid)))
   //    return true;   // we're at the page or at a sub page
   // else
   //     return false;  // we're elsewhere
 };



 /**
  * Function to check if page is child of a particular page.
  *
  * @param $post_tags Array
  *
  * @return $posts Array
  */
 // needs work Joel HELP
 function get_related_posts($args) {
 	 global $post;

   $tags = $args['tags'];
   $numberOfPosts = $args['numberposts'];

 		$args = array(
 			'tag' => $tags,
 			'numberposts' => $numberOfPosts, /* you can change this to show more */
 			'post__not_in' => array($post->ID)
 		);

 		return get_posts( $args );
 }


 add_filter( 'get_custom_logo',  'custom_logo_url' );
 function custom_logo_url ( $html ) {

 $custom_logo_id = get_theme_mod( 'custom_logo' );
 $url = network_site_url();
 $html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
         esc_url( $url  ),
         wp_get_attachment_image( $custom_logo_id, 'full', false, array(
             'class'    => 'custom-logo',
         ) )
     );
 return $html;
 }
