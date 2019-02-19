<?php
// Register menus
register_nav_menus(
	array(
		'main-nav' => __( 'The Main Menu', 'wpBaby' ),   // topbar nav
		'second-nav' => __( 'The Second Menu', 'wpBaby' ),   // topbar nav
		'footer-learn-more' => __( 'Footer learn more Menu', 'wpBaby' ), // footer nav
		'footer-resources' => __( 'Footer resources Menu', 'wpBaby' ), // footer nav
		'footer-partners' => __( 'Footer partners Menu', 'wpBaby' ), // footer nav
	)
);


// The Top Menu
function build_topbar_nav() {
	wp_nav_menu(array(
		'container' => false,
		'theme_location' => 'main-nav',
		'walker' => new Topbar_Menu_Walker()
	));
}
function build_topbar2_nav() {
	 wp_nav_menu(array(
        'container' => false,
        'theme_location' => 'second-nav',
        'walker' => new Topbar_Menu_Walker()
    ));
}
function build_menu_1() {
	 // code...
	wp_nav_menu(array(
			 'container' => false,                           // Remove nav container
			 'menu_class' => 'footer-menu',       // Adding custom nav class
			 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			 'theme_location' => 'footer-learn-more',        			// Where it's located in the theme
			 'depth' => 2,                                   // Limit the depth of the nav
			 'fallback_cb' => false                         // Fallback function (see below)
	 ));
}
function build_menu_2() {
	 // code...
	wp_nav_menu(array(
			 'container' => false,                           // Remove nav container
			 'menu_class' => 'footer-menu',       // Adding custom nav class
			 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			 'theme_location' => 'footer-resources',        			// Where it's located in the theme
			 'depth' => 2,                                   // Limit the depth of the nav
			 'fallback_cb' => false                         // Fallback function (see below)
	 ));
}
function build_menu_3() {
	 // code...
	wp_nav_menu(array(
			 'container' => false,                           // Remove nav container
			 'menu_class' => 'footer-menu',       // Adding custom nav class
			 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			 'theme_location' => 'footer-partners',        			// Where it's located in the theme
			 'depth' => 2,                                   // Limit the depth of the nav
			 'fallback_cb' => false                         // Fallback function (see below)
	 ));
}

// use to build a navigation based on subpages.
// first make a custom field which allows for menu id to be stored by the page.
// then loop through each parent page and check if it contains a nav id.
// get the nav id and pass it into this.
function build_child_nav($menu_id) {
	# code...
	wp_nav_menu(array(
		'theme_location' => 'side-nav',
		'menu' => $menu_id
  ));
}

// Big thanks to Brett Mason (https://github.com/brettsmason) for the awesome walker
class Topbar_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		
		$output .= "{$n}{$indent}<ul class=''>{$n}";
	}
    function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		$btn = "
			<div class='btn-container'>
				<a href='#login' class='btn text-white bg-blue'>Login</a>
			</div> 
		";
		$output .= "{$indent} {$btn}</ul>{$n}";
	}
}


