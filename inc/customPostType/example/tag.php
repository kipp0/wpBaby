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



// now let's add custom tags (these act like categories)
  register_taxonomy( 'custom_tag',
    array('custom_type'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
    array('hierarchical' => false,    /* if this is false, it acts like tags */
      'labels' => array(
        'name' => __( 'Custom Tags', 'wpBabywp' ), /* name of the custom taxonomy */
        'singular_name' => __( 'Custom Tag', 'wpBabywp' ), /* single taxonomy name */
        'search_items' =>  __( 'Search Custom Tags', 'wpBabywp' ), /* search title for taxomony */
        'all_items' => __( 'All Custom Tags', 'wpBabywp' ), /* all title for taxonomies */
        'parent_item' => __( 'Parent Custom Tag', 'wpBabywp' ), /* parent title for taxonomy */
        'parent_item_colon' => __( 'Parent Custom Tag:', 'wpBabywp' ), /* parent taxonomy title */
        'edit_item' => __( 'Edit Custom Tag', 'wpBabywp' ), /* edit custom taxonomy title */
        'update_item' => __( 'Update Custom Tag', 'wpBabywp' ), /* update title for taxonomy */
        'add_new_item' => __( 'Add New Custom Tag', 'wpBabywp' ), /* add new title for taxonomy */
        'new_item_name' => __( 'New Custom Tag Name', 'wpBabywp' ) /* name title for taxonomy */
      ),
      'show_admin_column' => true,
      'show_ui' => true,
      'query_var' => true,
    )
  );

  /*
    looking for custom meta boxes?
    check out this fantastic tool:
    https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
  */
