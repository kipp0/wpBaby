<?php
// Register menus
register_nav_menus(
	array(
		'main-nav' => __( 'The Main Menu', 'jointswp' ),   // topbar nav
		'child-nav' => __( 'The Child Menu', 'jointswp' ),   // secondary nav
		'footer-links' => __( 'Footer Links', 'jointswp' ) // footer nav
	)
);


// use to build a navigation based on subpages.
// first make a custom field which allows for menu id to be stored by the page.
// then loop through each parent page and check if it contains a nav id.
// get the nav id and pass it into this.
function build_child_nav($menu_id) {
	# code...
	wp_nav_menu(array(
		// 'theme_location' => 'side-nav',
		'menu' => $menu_id
  ));
}

// The Top Menu
function build_topbar_nav() {
	 wp_nav_menu(array(
        'container' => false,                           // Remove nav container
        'menu_class' => 'menu',       // Adding custom nav class
        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'theme_location' => 'main-nav',        			// Where it's located in the theme
        'depth' => 5,                                   // Limit the depth of the nav
        'fallback_cb' => false,                         // Fallback function (see below)
        'walker' => new Topbar_Menu_Walker()
    ));
}

// Big thanks to Brett Mason (https://github.com/brettsmason) for the awesome walker
class Topbar_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = Array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"menu\">\n";
    }
}
