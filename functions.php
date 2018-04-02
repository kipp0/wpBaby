<?php

/******************************************************************
*
*
*                Functions File For includes only.
*
*                            o  o
*                          --------
*
*              Best NOT to add functions in this file
******************************************************************/




// custom functions to make your life easier don't be shy take a look
require_once(get_template_directory(). '/inc/functions/helpers.php');

// Adds support for multiple languages
require_once(get_template_directory().'/inc/functions/translation/translation.php');
// WP Head and other cleanup functions
require_once(get_template_directory().'/inc/functions/cleanup.php');
// Register scripts and stylesheets
require_once(get_template_directory().'/inc/functions/enqueue-scripts.php');
// Register custom menus and menu walkers
// require_once(get_template_directory().'/inc/functions/menu.php');


// Related post function - no need to rely on plugins
// require_once(get_template_directory().'/inc/functions/related-posts.php');

// For use as a for custom post types template
// require_once(get_template_directory(). '/inc/functions/custom-post-type.php');

// customize your admin login page.
// require_once(get_template_directory(). '/inc/functions/helpers.php');
