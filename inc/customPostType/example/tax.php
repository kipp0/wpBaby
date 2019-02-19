<?php
/******************************************************************
*
*
*                A custom Taxonomy template.
*
*                            o  o
*                          --------
*
* The less time you spend googling the more time we can have fun...
******************************************************************/



/*
for more information on taxonomies, go here:
http://codex.wordpress.org/Function_Reference/register_taxonomy
*/

// now let's add custom categories (these act like categories)
  register_taxonomy( 'custom_cat',
    array('custom_type'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
    array('hierarchical' => true,     /* if this is true, it acts like categories */
      'labels' => array(
        'name' => __( 'Custom Categories', 'wpBabywp' ), /* name of the custom taxonomy */
        'singular_name' => __( 'Custom Category', 'wpBabywp' ), /* single taxonomy name */
        'search_items' =>  __( 'Search Custom Categories', 'wpBabywp' ), /* search title for taxomony */
        'all_items' => __( 'All Custom Categories', 'wpBabywp' ), /* all title for taxonomies */
        'parent_item' => __( 'Parent Custom Category', 'wpBabywp' ), /* parent title for taxonomy */
        'parent_item_colon' => __( 'Parent Custom Category:', 'wpBabywp' ), /* parent taxonomy title */
        'edit_item' => __( 'Edit Custom Category', 'wpBabywp' ), /* edit custom taxonomy title */
        'update_item' => __( 'Update Custom Category', 'wpBabywp' ), /* update title for taxonomy */
        'add_new_item' => __( 'Add New Custom Category', 'wpBabywp' ), /* add new title for taxonomy */
        'new_item_name' => __( 'New Custom Category Name', 'wpBabywp' ) /* name title for taxonomy */
      ),
      'show_admin_column' => true,
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => array( 'slug' => 'custom-slug' ),
    )
  );

  /*
    looking for custom meta boxes?
    check out this fantastic tool:
    https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
  */
