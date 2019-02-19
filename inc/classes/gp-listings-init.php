<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 2/12/2018
 * Time: 12:50 PM
 */

/**
 * Handles the WordPress back-end type stuff for Listings post type.
 * ie. register post type, taxonomies, meta boxes etc.
 * Class GP_Listings_Init
 */
Class GP_Listings_Init {

	public static $post_type = 'gp_listing';
	public static $tax_cat = 'gp_listing_cat';
	public static $tax_type = 'gp_listing_type';
	public static $attachments_1 = 'gp_listing_top_gallery';
	public static $attachments_2 = 'gp_listing_bottom_gallery';

	/**
	 *
	 */
	public function __construct() {
		$this->register_post_type();
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'save_post', array( $this, 'save_post' ), 50, 3 );

		// 'init' may be too late for this..
		add_action( 'attachments_register', array( $this, 'attachments_register' ), 10, 1 );
	}

	/**
	 * @param $post_id
	 * @param $post
	 * @param $update
	 */
	public function save_post( $post_id, $post, $update  ) {

		// autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// ajax ( quick edit ? )
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return;

		// post type
		if ( get_post_type( $post ) !== GP_Listings_Init::$post_type )
			return;

		// capabilities (could use edit_posts but i think this is fine)
		if ( ! current_user_can( 'administrator' ) )
			return;

		// post revision (not sure this is necessary)
		if ( false !== wp_is_post_revision( $post_id ) )
			return;

		// nonce
		$nonce = gp_if_set( $_POST, 'gp_listings_save_post', '' );
		if ( ! wp_verify_nonce( $nonce, 'gp-save-post-listings' ) )
			return;

		// $_POST index => meta_key stored in postmeta (only provide if not the same as $_POST index)
		$fields_keys = array(
			'sold' => false,
			'displayed_address' => '',
			'mls_number' => '',
			'price' => '',
			'sqft' => '',
			'bedrooms' => '',
			'bathrooms' => '',
			'lot_size' => '',
			'age_of_home' => '',
			'exterior' => '',
			'exposure' => '',
			'property_taxes' => '',
			'slip_boathouse' => '',
			'total_rooms' => '',
			'parking' => '',
			'garage' => '',
			'status' => '',
			'style' => '',
		);

		if ( $fields_keys && is_array( $fields_keys ) ) {
			foreach ( $fields_keys as $post_index=>$meta_key ) {

				$meta_key = trim( $meta_key );
				if ( ! $meta_key ) {
					$meta_key = $post_index;
				}

				$val = gp_if_set( $_POST, $post_index, '' );
				$val = trim( $val );

				// todo: may do some sanitation or checking of values
				// todo: note that bedrooms and bathrooms could be 2.5, so we won't be forcing integers here

				update_post_meta( $post_id, $meta_key, $val );
			}
		}
	}

	/**
	 * @param Attachments $attachments
	 */
	public function attachments_register( $attachments ) {

		$fields = array();

		// $fields = array();
		$args = array(
			'label' => 'Top Gallery',
			'post_type' => self::$post_type,
			// 'position'      => 'normal',
			// priority'      => 'high',
			'filetype' => array( 'image' ),
			'note' => '',
			'append' => true,
			'button_text' => 'Add Photos',
			'modal_text' => 'Insert',
			'router' => 'browse',
			'post_parent' => false,
			'fields' => $fields,
		);

		// // top gallery (shows in place of featured image)
		// $attachments->register( self::$attachments_1, $args ); // unique instance name

		// register another one for the bottom page gallery
		$args[ 'label' ] = 'Bottom Gallery';
		$attachments->register( self::$attachments_2, $args ); // unique instance name
	}

	/**
	 *
	 */
	public function register_post_type() {

		// *******************************
		//           Listings
		// *******************************
		$labels = array(
			'name' => _x( 'Listings', 'post type general name', 'your-plugin-textdomain' ),
			'singular_name' => _x( 'Listing', 'post type singular name', 'your-plugin-textdomain' ),
			'menu_name' => _x( 'Listings', 'admin menu', 'your-plugin-textdomain' ),
			'name_admin_bar' => _x( 'Listing', 'add new on admin bar', 'your-plugin-textdomain' ),
			'add_new' => _x( 'Add New', 'Listing', 'your-plugin-textdomain' ),
			'add_new_item' => __( 'Add New Listing', 'your-plugin-textdomain' ),
			'new_item' => __( 'New Listing', 'your-plugin-textdomain' ),
			'edit_item' => __( 'Edit Listing', 'your-plugin-textdomain' ),
			'view_item' => __( 'View Listing', 'your-plugin-textdomain' ),
			'all_items' => __( 'Listings', 'your-plugin-textdomain' ),
			'search_items' => __( 'Search Listings', 'your-plugin-textdomain' ),
			'parent_item_colon' => __( 'Parent Listings:', 'your-plugin-textdomain' ),
			'not_found' => __( 'No Listings found.', 'your-plugin-textdomain' ),
			'not_found_in_trash' => __( 'No Listings found in Trash.', 'your-plugin-textdomain' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'has_archive' => false, // we will use a page template instead to query taxonomies from $_GET vars..
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => 'listing',
			),
			'show_in_menu' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		);

		register_post_type( self::$post_type, $args );

		// *******************************
		//           Categories
		// *******************************
		$cat_labels = array(
			'name' => 'Categories',
			'singular_name' => 'Category',
			'search_items' => 'Search Categories',
			'all_items' => 'All Categories',
			'parent_item' => 'Parent Category',
			'parent_item_colon' => 'Parent Category:',
			'edit_item' => 'Edit Category',
			'update_item' => 'Update Category',
			'add_new_item' => 'Add New Category',
			'new_item_name' => 'New Category Name',
			'menu_name' => 'Categories',
		);

		$cat_args = array(
			'hierarchical' => true,
			'labels' => $cat_labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => false,
			'rewrite' => array( 'slug' => 'listing-category' ),
		);

		register_taxonomy( self::$tax_cat, self::$post_type, $cat_args );

		// *******************************
		//             Types
		// *******************************
		$tax_labels = array(
			'name' => 'Types',
			'singular_name' => 'Type',
			'search_items' => 'Search Types',
			'all_items' => 'All Types',
			'parent_item' => 'Parent Type',
			'parent_item_colon' => 'Parent Type:',
			'edit_item' => 'Edit Type',
			'update_item' => 'Update Type',
			'add_new_item' => 'Add New Type',
			'new_item_name' => 'New Type Name',
			'menu_name' => 'Types',
		);

		$tax_args = array(
			'hierarchical' => true,
			'labels' => $tax_labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => false,
			'rewrite' => array( 'slug' => 'listing-type' ),
		);

		register_taxonomy( self::$tax_type, self::$post_type, $tax_args );
	}

	/**
	 * @param $post
	 * @param $metabox
	 */
	public function add_meta_boxes() {
		add_meta_box( 'gp-listing', 'Details', array( $this, 'metabox_content' ), 'gp_listing', 'normal', 'low' );
	}

	/**
	 *
	 */
	public function metabox_content( $post, $metabox ) {

		$items = array();
		$post_id = $post->ID;

		$listing = new GP_Listing( $post );
		if ( ! $listing->is_valid() ) {
			return;
		}

		$pm = array();

		$pm[] = array(
			'header' => 'General',
		);

		$sold = get_post_meta( $post_id, 'sold', true );
		$checked = $sold ? ' checked' : '';


		$pm[] = array(
			'label' => 'Sold',
			'tooltip' => 'check if sold',
			'input_html' => '<input type="checkbox" name="sold" id="sold" value="1"' . $checked . '>',
			'add_class' => 'long-input',
			'split' => 1,
			'key' => 'sold',
		);


		$pm[] = array(
			'label' => 'Displayed Address/Title',
			'tooltip' => 'This may or may not be the same as the Listing Title at the top of this page',
			'add_class' => 'long-input',
			'split' => 1,
			'value' => gp_get_post_meta_sanitize( $post_id, 'displayed_address', 'price' ),
			'key' => 'displayed_address',
		);

		$pm[] = array(
			'label' => 'MLS Number',
			'value' => gp_get_post_meta_sanitize( $post_id, 'mls_number', 'price' ),
			'key' => 'mls_number',
		);

		$pm[] = array(
			'label' => 'Price ($)',
			'tooltip' => 'Enter a dollar amount with no decimals or dollar sign. Example: 479000',
			'value' => gp_get_post_meta_sanitize( $post_id, 'price', 'price' ),
			'key' => 'price',
		);

		// REQUIRED SEARCH FIELDS (these probably also must be integers)
		$pm[] = array(
			'header' => 'Required Search Fields',
		);

		$pm[] = array(
			'label' => 'SQFT',
			'tooltip' => 'Enter a number so that "Sort By" functionality will work. Example: 5000',
			'value' => gp_get_post_meta_sanitize( $post_id, 'sqft', 'basic' ),
			'key' => 'sqft',
		);

		$pm[] = array(
			'label' => 'Bedrooms',
			'tooltip' => 'Must be numerical for "Sort By" functionality to work. Examples: 2, 2.5, 3',
			'value' => gp_get_post_meta_sanitize( $post_id, 'bedrooms', 'basic' ),
			'key' => 'bedrooms',
		);

		$pm[] = array(
			'label' => 'Bathrooms',
			'tooltip' => 'Must be numerical for "Sort By" functionality to work. Examples: 2, 2.5, 3',
			'value' => gp_get_post_meta_sanitize( $post_id, 'bathrooms', 'basic' ),
			'key' => 'bathrooms',
		);

		// ADDITIONAL DISPLAYED FIELDS
		$pm[] = array(
			'header' => 'Additional Displayed Fields',
		);

		$pm[] = array(
			'label' => 'Age of Home',
			'tooltip' => 'If a whole number is entered (ie. 25), the text "years" will be appended (otherwise, put your own text)',
			'value' => gp_get_post_meta_sanitize( $post_id, 'age_of_home', 'basic' ),
			'key' => 'age_of_home',
		);

		$pm[] = array(
			'label' => 'Property Taxes ($)',
			'tooltip' => 'If a whole number is entered, it will be formatted as dollars (ie. $3,269)',
			'value' => gp_get_post_meta_sanitize( $post_id, 'property_taxes', 'basic' ),
			'key' => 'property_taxes',
		);

		// $pm[] = array(
		// 	'label' => 'Lot Size - Ft',
		// 	'tooltip' => 'Example: 65 X 150 FT. lot',
		// 	'value' => gp_get_post_meta_sanitize( $post_id, 'lot_size', 'basic' ),
		// 	'key' => 'lot_size',
		// );

		$pm[] = array(
			'label' => 'Lot Size - Acres',
			'tooltip' => 'Example: 65 X 150 FT. lot',
			'value' => gp_get_post_meta_sanitize( $post_id, 'lot_size', 'basic' ),
			'key' => 'lot_size',
		);

		$pm[] = array(
			'label' => 'Parking',
			'tooltip' => '',
			'value' => gp_get_post_meta_sanitize( $post_id, 'parking', 'basic' ),
			'key' => 'parking',
		);

		$pm[] = array(
			'label' => 'Garage',
			'tooltip' => '',
			'value' => gp_get_post_meta_sanitize( $post_id, 'garage', 'basic' ),
			'key' => 'garage',
		);

		$pm[] = array(
			'label' => 'Slip Boathouse',
			'tooltip' => 'Amount of slip boathouses the property has',
			'value' => gp_get_post_meta_sanitize( $post_id, 'slip_boathouse', 'basic' ),
			'key' => 'slip_boathouse',
		);
		$pm[] = array(
			'label' => 'Exposure',
			'tooltip' => 'Exposure of the House',
			'value' => gp_get_post_meta_sanitize( $post_id, 'exposure', 'basic' ),
			'key' => 'exposure',
		);

		echo '<input type="hidden" name="gp_listings_save_post" value="' . wp_create_nonce( 'gp-save-post-listings' ) . '">';
		echo gp_admin_post_meta_fields( $pm );
		echo nl2br( "----------------------- \n" );
		var_dump( get_post_meta( $post_id ) );
	}
}

new GP_Listings_Init();
