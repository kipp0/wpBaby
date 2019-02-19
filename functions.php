<?php

/******************************************************************
*
*
*                Functions File For includes only.
*
*                            o  o
*                          --------
*
*             Best NOT to add functions in this file
******************************************************************/

// CPT setup
require_once(get_template_directory(). '/inc/customPostType/example/example.php');

// customizer setup
require_once(get_template_directory(). '/inc/customizer/customizer.php');

// classes setup
require_once(get_template_directory(). '/inc/classes/classes.php');

// functions setup
require_once(get_template_directory(). '/inc/functions/functions.php');

// includes all custom shortcodes.
require_once(get_template_directory(). '/inc/shortcodes/shortcodes.php');






// Related post function - no need to rely on plugins
// require_once(get_template_directory().'/inc/functions/related-posts.php');

// For use as a for custom post types template
// require_once(get_template_directory(). '/inc/functions/custom-post-type.php');

// customize your admin login page.
// require_once(get_template_directory(). '/inc/functions/helpers.php');
