<?php

/******************************************************************
*
*
*                A custom post type template.
*
*                            o  o
*                          --------
*
* The less time you spend googling the more time we can have fun...
******************************************************************/


// cpt > tax > tax term

// let's create the function for the custom type
function custom_post_example() {
	// creating (registering) the custom type
	register_post_type( 'example', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
	 	// let's now add all the options for this post type
		array('labels' => array(
			'name' => __('Custom Types', 'wpBabywp'), /* This is the Title of the Group */
			'singular_name' => __('Custom Post', 'wpBabywp'), /* This is the individual type */
			'all_items' => __('All Custom Posts', 'wpBabywp'), /* the all items menu item */
			'add_new' => __('Add New', 'wpBabywp'), /* The add new menu item */
			'add_new_item' => __('Add New Custom Type', 'wpBabywp'), /* Add New Display Title */
			'edit' => __( 'Edit', 'wpBabywp' ), /* Edit Dialog */
			'edit_item' => __('Edit Post Types', 'wpBabywp'), /* Edit Display Title */
			'new_item' => __('New Post Type', 'wpBabywp'), /* New Display Title */
			'view_item' => __('View Post Type', 'wpBabywp'), /* View Display Title */
			'search_items' => __('Search Post Type', 'wpBabywp'), /* Search Custom Type Title */
			'not_found' =>  __('Nothing found in the Database.', 'wpBabywp'), /* This displays if there are no entries yet */
			'not_found_in_trash' => __('Nothing found in Trash', 'wpBabywp'), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'This is the example custom post type', 'wpBabywp' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 11, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => 'dashicons-book', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
			'rewrite'	=> array( 'slug' => 'custom_type', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'custom_type', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky')
	 	) /* end of options */
	); /* end of register post type */

	/* this adds your post categories to your custom post type */
	// register_taxonomy_for_object_type('category', 'example');
	/* this adds your post tags to your custom post type */
	// register_taxonomy_for_object_type('post_tag', 'example');

}

	// adding the function to the Wordpress init
	add_action( 'init', 'custom_post_example');
