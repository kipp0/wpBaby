<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 2/12/2018
 * Time: 12:50 PM
 */

Class GP_Listings {

	/** @var  array */
	protected static $clean_data;

	/** @var  WP_Query */
	protected static $query;

	protected static $page_query_var = 'pg';

	// leave null (until we calculate it) need to cache in case we call twice per page
	protected static $pagination_html;

	// indexed array by $term_queries[$taxonomy] to prevent running query twice
	protected static $term_queries;

	public function __construct() {

	}

	/**
	 * @return array - array of listing IDs
	 */
	public static function get_listings_for_sale(){

		$listing_ids = array();
		$args = array(
		    'post_status' => 'publish',
		    'post_type'   => GP_Listings_Init::$post_type,
		    'orderby'     => array(
		    	'sort_featured' => 'DESC',
		    	'post_date' => 'DESC',
		    ),
		    'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'sold',
					'value' => '',
					'compare' => 'EQUALS'
				),
				'sort_featured' => array(
					'key' => 'featured',
					'compare' => 'EXISTS',
				)
			)
		);

		$new_query = new WP_Query( $args );

		if($new_query->have_posts()){
		    while($new_query->have_posts()){
		        $new_query->the_post();
		        $listing_ids[] = get_the_ID();
		    }
		    wp_reset_postdata();
		}

		return $listing_ids;
	}

	/**
	 *
	 */
	public static function get_price_range_options() {
		// javascript will look for the underscore and fill out hidden form fields for min_price and max_price
		$options = array(
			''  => 'Price',
			'0_500000'  => 'Under 500k',
			'500000_750000' => '500k - 750k',
			'750000_1000000' => '750k - 1m',
			'1000000_1500000' => '1m - 1.5m',
			'1500000_2000000' => '1.5m - 2m',
			'2000000_3000000' => '2m - 3m',
			'3000000_0' => 'Over 3m',
		);
		return $options;
	}

	/**
	 * Form submits with values for min_price and max_price... but we print a form
	 * that is a <select> with name price, and the key of each option is a price range..
	 *
	 * This gives you the <option> value based on submitted min_price and max_price values..
	 *
	 * @param $clean_data
	 */
	public static function get_price_range_value( $clean_data ) {
		$min_price = gp_if_set( $clean_data, 'min_price' );
		$min_price = trim( $min_price );
		$min_price = (int) $min_price;
		$max_price = gp_if_set( $clean_data, 'max_price' );
		$max_price = trim( $max_price );
		$max_price = (int) $max_price;

		// kind of redundant check on strict equals zero, but may or may not use the second condition
		if ( $min_price === 0 || ! $min_price ) {
			$min_price = '0';
		}

		if ( $max_price === 0 || ! $max_price ) {
			$max_price = '0';
		}

		$key = $min_price . '_' . $max_price;
		return $key;
	}

	/**
	 * @return array
	 */
	public static function get_sold_options() {
		$options = array(
			''    => 'For Sale / Sold',
			'no'  => 'For Sale',
			'yes' => 'Sold',
		);
		return $options;
	}

	/**
	 *
	 */
	public static function get_category_options(){

		$tq = self::get_term_query( GP_Listings_Init::$tax_cat );

		$options = array(
			'' => 'Select Category',
		);

		if ( $tq && is_array( $tq ) ) {
			/** @var WP_Term $wp_term */
			foreach ( $tq as $wp_term ) {
				$option_val   = esc_attr( $wp_term->slug );
				$option_label = sanitize_text_field( $wp_term->name );
				$options[$option_val] = $option_label;
			}
		}

		return $options;
	}

	/**
	 *
	 */
	public static function get_type_options(){

		$tq = self::get_term_query( GP_Listings_Init::$tax_type );

		$options = array(
			'' => 'Select Type',
		);

		if ( $tq && is_array( $tq ) ) {
			/** @var WP_Term $wp_term */
			foreach ( $tq as $wp_term ) {
				$option_val   = esc_attr( $wp_term->slug );
				$option_label = sanitize_text_field( $wp_term->name );
				$options[$option_val] = $option_label;
			}
		}

		return $options;
	}

	/**
	 * @return array
	 */
	public static function get_per_page_options() {
		$options = array(
			'12' => '12 per page',
			'24' => '24 per page',
			'36' => '36 per page',
		);

		return $options;
	}

	/**
	 * @return array
	 */
	public static function get_sort_options() {

		$options = array(
			'date_desc'     => 'Date (new)',
			'date_asc'      => 'Date (old)',
			'price_desc'    => 'Price (high)',
			'price_asc'     => 'Price (low)',
			'sqft_desc'     => 'Square Feet (high)',
			'sqft_asc'      => 'Square Feet (low)',
			'bedrooms_desc' => 'Bedrooms (high)',
			'bedrooms_asc'  => 'Bedrooms (low)',
			'title_asc'     => 'Title (A - Z)',
			'title_desc'    => 'Title (Z - A)',
		);

		return $options;
	}

	/**
	 * Takes $_POST, $_GET or w/e and returns clean data based on the *expected* values...
	 * Therefore we ignore every submitted value that we're not expecting...
	 * This does will not include nonce fields and the like, just values that are required for filters and sort by
	 * parameters
	 *
	 * @param $user_input
	 */
	public static function sanitize_user_input( $user_input ) {
		return $user_input;

		if ( self::$clean_data !== null && is_array( self::$clean_data ) ) {
			return self::$clean_data;
		}

		$clean_data = array();

		// Min Price
		$min_price = gp_if_set( $user_input, 'min_price' );

		$min_price = trim( $min_price );
		if ( gp_is_integer( $min_price ) ) {
			$clean_data[ 'min_price' ] = (int) $min_price;
		} else {
			$clean_data[ 'min_price' ] = false; // dont use zero it shows up as string 0 in url
		}

		// Max Price
		$max_price = gp_if_set( $user_input, 'max_price' );
		$max_price = trim( $max_price );
		if ( gp_is_integer( $max_price ) ) {
			$clean_data[ 'max_price' ] = (int) $max_price;
		} else {
			$clean_data[ 'max_price' ] = false; // dont use zero it shows up as string 0 in url
		}

		// Sort By
		$sort         = gp_if_set( $user_input, 'sort' );
		$sort         = trim( $sort );
		$sort_options = self::get_sort_options();
		$valid_sort   = array_keys( $sort_options );
		if ( in_array( $sort, $valid_sort ) ) {
			$clean_data[ 'sort' ] = sanitize_text_field( $sort );
		} else {
			$clean_data[ 'sort' ] = false;
		}

		// for now, categories and types will only accept one value

		// Type
		$type        = gp_if_set( $user_input, 'type' );
		$type        = trim( $type );
		$valid_types = self::get_valid_term_values( GP_Listings_Init::$tax_type );
		if ( in_array( $type, $valid_types ) ) {
			$clean_data[ 'type' ] = sanitize_text_field( $type );
		} else {
			$clean_data[ 'type' ] = false;
		}

		// Categories
		$category   = gp_if_set( $user_input, 'category' );
		$category   = trim( $category );
		$valid_cats = self::get_valid_term_values( GP_Listings_Init::$tax_cat );
		if ( in_array( $category, $valid_cats ) ) {
			$clean_data[ 'category' ] = sanitize_text_field( $category );
		} else {
			$clean_data[ 'category' ] = false;
		}

		// Show (per page)
		$show                 = gp_if_set( $user_input, 'show' );
		$show                 = trim( $show );
		$per_page_options     = self::get_per_page_options();
		$clean_data[ 'show' ] = in_array( $show, array_keys( $per_page_options ) ) ? $show : false;

		// Sold (not sold)
		$sold                 = gp_if_set( $user_input, 'sold' );
		$sold                 = trim( $sold );
		$sold_options         = self::get_sold_options();
		$clean_data[ 'sold' ] = in_array( $sold, array_keys( $sold_options ) ) ? $sold : false;

		// pagination variable
		if ( isset( $user_input[ self::$page_query_var ] ) ) {
			$clean_data[ self::$page_query_var ] = (int) $user_input[ self::$page_query_var ];
		}

		return $clean_data;
	}

	/**
	 * @param $taxonomy
	 *
	 * @return array|WP_Term_Query
	 */
	public static function get_term_query( $taxonomy ) {

		if ( self::$term_queries !== null && is_array( self::$term_queries ) && isset( self::$term_queries[$taxonomy] ) ) {
			return self::$term_queries[$taxonomy];
		}

		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		);

		$tq = get_terms( $args );
		$tq = $tq && ! is_wp_error( $tq ) ? $tq : array();
		self::$term_queries[$taxonomy] = $tq;
		return self::$term_queries[$taxonomy];
	}

	/**
	 * User can submit slugs of terms to search filters
	 */
	public static function get_valid_term_values( $taxonomy ) {

		$valid = array();
		$tq    = self::get_term_query( $taxonomy );

		if ( $tq && ! is_wp_error( $tq ) ) {
			/** @var WP_Term $wp_term */
			foreach ( $tq as $wp_term ) {
				$valid[] = $wp_term->slug;
			}
		}

		return $valid;
	}

	/**
	 * @param $user_input
	 */
	public static function filters( $user_input ) {

		$op = '';

		$clean_data = self::sanitize_user_input( $user_input );

		$min_price = gp_if_set( $clean_data, 'min_price' );
		$max_price = gp_if_set( $clean_data, 'max_price' );
		$sort      = gp_if_set( $clean_data, 'sort' );
		$type      = gp_if_set( $clean_data, 'type' );
		$category  = gp_if_set( $clean_data, 'category' );
		$show      = gp_if_set( $clean_data, 'show' );
		$sold      = gp_if_set( $clean_data, 'sold' );

		$op .= '<form id="filter-listings" method="get" action="' . gp_current_url() . '">';

		$op .= '<input placeholder="Min Price" type="hidden"  name="min_price" value="' . $min_price . '">';
		$op .= '<input placeholder="Max Price" type="hidden"  name="max_price" value="' . $max_price . '">';

		// $op .= '<a href="' . gp_current_url( false ) . '" type="reset">Reset</a>';

		$op .= '<div class="form-flex">';

		// nth-child(1) of .form-flex has certain styles, do not put hidden inputs right here

		// *******************************
		//          Price Range
		// *******************************

		// TODO: change to slider
		$op .= '<div class="form-item item-price">';
		$op .= gp_custom_select(array(
			'name' => 'price',
			'id'   => 'ff-price',
			'value' => self::get_price_range_value( $clean_data ),
			'options' => self::get_price_range_options(),
		));
		$op .= '</div>'; // form-item

		// *******************************
		//            Sold
		// *******************************

		$op .= '<div class="form-item item-sold">';
		$op .= gp_custom_select(array(
			'name' => 'sold',
			'id'   => 'ff-sold',
			'value' => $sold,
			'options' => self::get_sold_options(),
		));
		$op .= '</div>'; // form-item

		// *******************************
		//            Type
		// *******************************

		$op .= '<div class="form-item item-type">';
		$op .= gp_custom_select(array(
			'name' => 'type',
			'id'   => 'ff-type',
			'value' => $type,
			'options' => self::get_type_options(),
		));
		$op .= '</div>'; // form-item

		// *******************************
		//            Category
		// *******************************

		$op .= '<div class="form-item item-category">';
		$op .= gp_custom_select(array(
			'name' => 'category',
			'id'   => 'ff-category',
			'value' => $category,
			'options' => self::get_category_options(),
		));
		$op .= '</div>'; // form-item

		// *******************************
		//            Sort
		// *******************************

		$op .= '<div class="form-item item-sort">';
		$op .= gp_custom_select(array(
			'name' => 'sort',
			'id'   => 'ff-sort',
			'value' => $sort,
			'options' => self::get_sort_options(),
		));
		$op .= '</div>'; // form-item

		// *******************************
		//        # Per Page
		// *******************************

		$op .= '<div class="form-item item-show">';
		$op .= gp_custom_select(array(
			'name' => 'show',
			'id'   => 'ff-show',
			'value' => $show,
			'options' => self::get_per_page_options(),
		));
		$op .= '</div>'; // form-item

		// *******************************
		//          Submit
		// *******************************
		$op .= '<div class="form-item item-submit">';
		$op .= '<button class="reset" type="submit"><span class="btn-text">Filter Listings</span></button>';
		$op .= '</div>';

		// *******************************
		//            Category
		// *******************************

		// *******************************
		//            Sold
		// *******************************

//		$sold_options = self::get_sold_options();
//
//		$op .= '<select name="sold">';
//		$op .= '<option value="">For Sale?</option>';
//		if ( $sold_options ) {
//			foreach ( $sold_options as $kk => $vv ) {
//				$selected = $kk === $sold ? ' selected="selected"' : '';
//				$op       .= '<option value="' . $kk . '"' . $selected . '>' . $vv . '</option>';
//			}
//		}
//		$op .= '</select>';


		// *******************************
		//             Type
		// *******************************
//		$type_terms = self::get_term_query( GP_Listings_Init::$tax_type );
//		$op         .= '<select name="type">';
//		$op         .= '<option value="">Select Type</option>';
//
//		if ( $type_terms ) {
//			/** @var WP_Term $wp_term */
//			foreach ( $type_terms as $wp_term ) {
//
//				$option_val   = esc_attr( $wp_term->slug );
//				$option_label = sanitize_text_field( $wp_term->name );
//				$selected     = $option_val === $type ? ' selected="selected"' : '';
//
//				$op .= '<option value="' . $option_val . '"' . $selected . '>' . $option_label . '</option>';
//			}
//		}
//		$op .= '</select>';

		// *******************************
		//             Category
		// *******************************
//		$category_terms = self::get_term_query( GP_Listings_Init::$tax_cat );
//		$op             .= '<select name="category">';
//		$op             .= '<option value="">Select Category</option>';
//
//		if ( $category_terms ) {
//
//			/** @var WP_Term $wp_term */
//			foreach ( $category_terms as $wp_term ) {
//
//				$option_val   = esc_attr( $wp_term->slug );
//				$option_label = sanitize_text_field( $wp_term->name );
//				$selected     = $option_val === $category ? ' selected="selected"' : '';
//
//				$op .= '<option value="' . $option_val . '"' . $selected . '>' . $option_label . '</option>';
//			}
//			$op .= '</select>';
//		}

		// *******************************
		//              Sort
		// *******************************
//		$sort_options = self::get_sort_options();
//
//		$op .= '<select name="sort">';
//		$op .= '<option value="">Sort By</option>';
//		if ( $sort_options ) {
//			foreach ( $sort_options as $kk => $vv ) {
//				$selected = $kk === $sort ? ' selected="selected"' : '';
//				$op       .= '<option value="' . $kk . '"' . $selected . '>' . $vv . '</option>';
//			}
//		}
//		$op .= '</select>';

		// *******************************
		//          # Per Page
		// *******************************
//		$per_page_options = self::get_per_page_options();
//
//		$op .= '<select name="show">';
//		if ( $per_page_options ) {
//			foreach ( $per_page_options as $kk => $vv ) {
//				$selected = $kk == $show ? 'selected="selected"' : '';
//				$op       .= '<option value="' . $kk . '"' . $selected . '>' . $vv . '</option>';
//			}
//		}
//		$op .= '</select>';

		$op .= '</div>'; // form-flex
		$op .= '</form>';

		return $op;

	}

	/**
	 * @param $post
	 */
	public static function get_html_from_post( $post ) {

		if ( get_post_type( $post ) !== GP_Listings_Init::$post_type ) {
			return '';
		}

		$op = '';
		$sold = '';
		$featured = '';
		$listing = new GP_Listing( $post->ID );

		// outer div css classes
		$classes = array(
			'image-container',
		);

		if ( $listing->is_sold() ) {
			$classes[] = 'is-sold';
			$sold = '<div class="sold-img"><img src="' . get_template_directory_uri() . '/assets/images/sold.png"></div>';
		}

		if ( $listing->is_featured() ) {
			$classes[] = 'is-featured';
			$featured = '<div class="featured-wrapper"><p class="featured"><i class="fa fa-star"></i> Featured Listing</p></div>';
		}

		$image = get_post_thumbnail_id( $post->ID );
		$size  = array( 'large' );
		$op .= '<div class="' . gp_parse_css_classes( $classes ) . '">';

		$op .= $sold;
		$op .= $featured;

		$op .= GP_Background_Image::get_simple( $image, $size );
		$op .= '<a class="permalink" href="' . get_the_permalink( $post ) . '"></a>';
		$op .= '<div class="text-wrap">';
		$op .= '<div class="text-wrap-inner">';
		$op .= '<h2 class="title">' . $listing->get_address() . '</h2>';
		$op .= '<p class="price">' . $listing->get_price( true ) . '</p>';
		$op .= '</div>'; // text-wrap-inner
		$op .= '</div>'; // text-wrap

		$op .= '</div>'; // image container

		$op .= '<div class="icon-container">';
		$op .= $listing->get_featured_icons_html();
		$op .= '</div>';

		$op .= '';

		return $op;
	}

	/**
	 * @param       $posts
	 * @param array $args
	 *
	 * @return string
	 */
	public static function listings_from_posts( $posts, $args = array() ) {

		if ( $posts && is_array( $posts ) ) {

			$flex = new GP_Flex_Listings();
			$flex->add_css_class( 'listings-archive' );

			if ( isset( $args['add_class'] ) ) {
				$flex->add_css_class( $args['add_class'] );
			}

			foreach ( $posts as $p ) {
				$p = get_post( $p ); // make this function work where $posts is an array of IDs as well
				if ( get_post_type( $p ) === GP_Listings_Init::$post_type ) {
					$flex->add_item( array(
						'html' => self::get_html_from_post( $p ),
					) );
				}
			}

			return $flex->get_html();
		}

		return '';
	}

	/**
	 * @param WP_Query $query
	 * @param          $user_input
	 */
	public static function pagination( $query, $user_input ) {

		// check the global cache stored in the object so we don't compute everything twice
		if ( self::$pagination_html !== null ) {
			return self::$pagination_html;
		}

		// $clean_data = self::sanitize_user_input( $user_input );

		if ( ! $query instanceof WP_Query ) {
			return '';
		}

		$found_posts   = isset( $query->found_posts ) ? $query->found_posts : false;
		$max_num_pages = isset( $query->max_num_pages ) ? $query->max_num_pages : false;
		$is_paged      = isset( $query->is_paged ) ? $query->is_paged : false;

		// dont use these directly maybe ?? use $current_page instead
		$page         = isset( $query->page ) ? $query->page : false;
		$paged        = $query->get( 'paged', 1 );
		$current_page = $is_paged ? $paged : 1;

		$posts_per_page = $query->get( 'posts_per_page' );

		$stuff = array(
			'page'           => $page,
			'paged'          => $paged,
			'current_page'   => $current_page,
			'found_posts'    => $found_posts,
			'max_num_pages'  => $max_num_pages,
			'posts_per_page' => $posts_per_page,
			'is_paged'       => $is_paged,
		);

		if ( $is_paged ) {
			$prev_page_int = (int) $current_page - 1;
		} else {
			$prev_page_int = false;
		}

		if ( $current_page < $max_num_pages ) {
			$next_page_int = (int) $current_page + 1;
		} else {
			$next_page_int = false;
		}

		if ( $posts_per_page === '-1' || $posts_per_page === - 1 ) {

			$start = 1;
			$end   = $found_posts;
			$of    = $found_posts;

		} else if ( $posts_per_page > $found_posts ) {

			$start = 1;
			$end   = $found_posts;
			$of    = $found_posts;

		} else {

			if ( $is_paged ) {
				$start = (int) ( $posts_per_page * ( $current_page - 1 ) ) + 1;
				$end   = (int) $posts_per_page * $current_page;

				if ( $end > $found_posts ) {
					$end = $found_posts;
				}

			} else {
				$start = 1;
				$end   = (int) $posts_per_page;
			}

			$of = $found_posts;
		}

		// ie. 1-10 of 45
		$text = sprintf( '%s-%s of %s', $start, $end, $of );

		// expecting a lot of $_GET variables for filter params so these def. need to stay in place..
		$cur_url = gp_current_url( true );
		$cur_url = remove_query_arg( self::$page_query_var, $cur_url );

		$op = '';
		$op .= '<div class="pagination-flex">';

		$fa_prev = '<i class="fa fa-angle-left"></i>';
		$fa_next = '<i class="fa fa-angle-right"></i>';

		if ( $prev_page_int ) {

			$prev_href = add_query_arg( array(
				self::$page_query_var => $prev_page_int,
			), $cur_url );
			$op        .= '<a class="prev has-link" title="Go to page ' . $prev_page_int . '" href="' . $prev_href . '">' . $fa_prev . '</a>';
		} else {
			$op .= '<span class="prev">' . $fa_prev . '</span>';
		}

		$op .= '';
		$op .= '<div class="text-wrap">';
		$op .= '<p class="text">' . $text . '</p>';
		$op .= '</div>';

		if ( $next_page_int ) {
			$next_href = add_query_arg( array(
				self::$page_query_var => $next_page_int,
			), $cur_url );
			$op        .= '<a class="prev has-link" title="Go to page ' . $next_page_int . '" href="' . $next_href . '">' . $fa_next . '</a>';
		} else {
			$op .= '<span class="prev">' . $fa_next . '</span>';
		}

		$op .= '</div>'; // pagination-flex

		self::$pagination_html = $op;
		return self::$pagination_html;
	}

	/**
	 * @param WP_Query $query
	 */
	public static function listings_from_query( $query, $args = array() ) {

		if ( ! $query instanceof WP_Query ) {
			return '';
		}

		$posts = array();

		// beware of using $query->get_posts() it does not behave as expected
		// also we'll avoid accessing posts directly via $query->posts as those (I think) are unfiltered
		// so just loop the normal way and assemble an array.
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$p       = get_post();
				$posts[] = $p;
			}
			wp_reset_postdata();
		}

		return self::listings_from_posts( $posts, $args );
	}

	/**
	 * @param $query
	 */
	public static function map_from_query( $query ) {

		if ( ! $query || ! $query instanceof WP_Query ) {
			return '';
		}

		$map = new GP_Google_Map();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$p       = get_post();
				$listing = new GP_Listing( $p );
				$map->add_marker( array(
					'href' => get_the_permalink( $p ),
					'lat'  => $listing->get_latitude(),
					'long' => $listing->get_longitude(),
					'html' => $listing->get_map_marker_html(),
				) );

			}
			wp_reset_postdata();
		}

		return $map->get_html();
	}

	/**
	 * Get a query from raw user input (ie. filters)
	 *
	 * @param $user_input
	 *
	 * @return WP_Query
	 */
	public static function query_from_request( $user_input ) {

		if ( self::$query !== null && self::$query instanceof WP_Query ) {
			return self::$query;
		}

		$clean_data = self::sanitize_user_input( $user_input );

		return self::query_from_clean_data( $clean_data );
	}

	/**
	 * Making this private so it cannot be accessed directly and have $_POST or $_GET passed directly into it.
	 * This function expects clean data.
	 *
	 * @param $args
	 */
	private static function query_from_clean_data( $clean_data ) {

		$sort_by_featured = false; // this should always be true i think, cannot guarantee code works if you change it

		$page      = gp_if_set( $clean_data, self::$page_query_var );
		$min_price = gp_if_set( $clean_data, 'min_price' );
		$max_price = gp_if_set( $clean_data, 'max_price' );
		$sort      = gp_if_set( $clean_data, 'sort' );
		$sold      = gp_if_set( $clean_data, 'sold' );
		$show      = gp_if_set( $clean_data, 'show', -1 );
		$type      = gp_if_set( $clean_data, 'type' );
		$category  = gp_if_set( $clean_data, 'category' );

		// these are all dynamically built. We also won't override them at any point most likely, but
		// instead simply add to each one when a condition is true
		$tax_query  = array();
		$meta_query = array();
		$orderby    = array();

		// currently not supporting multiple types at
		if ( $type ) {
			$tax_query[ 'relation' ] = 'AND';
			$tax_query[]             = array(
				'taxonomy' => GP_Listings_Init::$tax_type,
				'field'    => gp_is_integer($type) ? 'id' : 'slug',
				'terms'    => $type,
			);
		}

		// currently not supporting multiple categories

		if ( $category ) {

			// if (is_array($category)) {
			// 	# code...
			//
			// 	foreach ($category as $key => $value) {
			// 		# code...
			// 		$tax_query[ 'relation' ] = 'AND';
			//
			// 		$tax_query[]             = array(
			// 			'taxonomy' => GP_Listings_Init::$tax_cat,
			// 			'field'    => gp_is_integer($category) ? 'id' : 'slug',
			// 			'terms'    => $category,
			// 		);
			// 	}
			// }

			$tax_query[ 'relation' ] = 'AND';
			$tax_query[]             = array(
				'taxonomy' => GP_Listings_Init::$tax_cat,
				'field'    => gp_is_integer($category) ? 'id' : 'slug',
				'terms'    => $category,
			);
		}

		if ( $sold === 'yes' ) {

			$meta_query['relation'] = 'AND';
			$meta_query['sold_yes'] = array(
				'key' => 'sold',
				'value' => true,
				'compare' => '=',
			);

		} else if ( $sold === 'no' ) {

			$meta_query['relation'] = 'AND';
			$meta_query['sold_no'] = array(
				'key' => 'sold',
				'value' => '', // todo: will this be 0, "0", "false" etc.??? (ie. test to make sure this works)
				'compare' => '=',
			);

		}

		// we probably always sort by featured
		if ( $sort_by_featured ) {
			$meta_query[ 'relation' ]      = 'AND';
			$meta_query[ 'sort_featured' ] = array(
				'key'     => 'featured',
				'compare' => 'exists',
				'type'    => 'numeric',
			);

			$orderby[ 'sort_featured' ] = 'DESC';
		}

		if ( $min_price ) {
			$meta_query[ 'relation' ]  = 'AND';
			$meta_query[ 'min_price' ] = array(
				'key'     => 'price',
				'value'   => $min_price,
				'compare' => '>=',
				'type'    => 'numeric',
			);
		}

		if ( $max_price ) {
			$meta_query[ 'relation' ]  = 'AND';
			$meta_query[ 'max_price' ] = array(
				'key'     => 'price',
				'value'   => $max_price,
				'compare' => '<=',
				'type'    => 'numeric',
			);
		}

		switch ( $sort ) {
			case 'price_desc':
				$orderby[ 'price' ]    = 'DESC';
				$meta_query[ 'price' ] = array(
					'key'     => 'price',
					'compare' => 'exists',
					'type' => 'numeric',
				);
				break;
			case 'price_asc':
				$orderby[ 'price' ]    = 'ASC';
				$meta_query[ 'price' ] = array(
					'key'     => 'price',
					'compare' => 'exists',
					'type' => 'numeric',
				);
				break;
			case 'date_desc':
				$meta_query[ 'date' ] = 'DESC';
				break;
			case 'date_asc':
				$meta_query[ 'date' ] = 'ASC';
				break;
			case 'sqft_desc':
				$orderby[ 'sqft' ]    = 'DESC';
				$meta_query[ 'sqft' ] = array(
					'key'     => 'sqft',
					'compare' => 'exists',
					'type' => 'numeric',
				);
				break;
			case 'sqft_asc':
				$orderby[ 'sqft' ]    = 'ASC';
				$meta_query[ 'sqft' ] = array(
					'key'     => 'sqft',
					'compare' => 'exists',
					'type' => 'numeric',
				);
				break;
			case 'bedrooms_desc':
				$orderby[ 'bedrooms' ]    = 'DESC';
				$meta_query[ 'bedrooms' ] = array(
					'key'     => 'bedrooms',
					'compare' => 'exists',
					'type' => 'numeric',
				);
				break;
			case 'bedrooms_asc':
				$orderby[ 'bedrooms' ]    = 'ASC';
				$meta_query[ 'bedrooms' ] = array(
					'key'     => 'bedrooms',
					'compare' => 'exists',
					'type' => 'numeric',
				);
				break;
			case 'title_asc':
				$orderby[ 'post_title' ] = 'ASC';
				break;
			case 'title_desc':
				$orderby[ 'post_title' ] = 'DESC';
				break;
		}

		$query_args = array(
			'post_type'      => GP_LIstings_Init::$post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $show,
		);

		if ( $meta_query ) {
			$query_args[ 'meta_query' ] = $meta_query;
		}

		if ( $tax_query ) {
			$query_args[ 'tax_query' ] = $tax_query;
		}

		if ( $orderby ) {
			$query_args[ 'orderby' ] = $orderby;
		} else {
			$query_args[ 'orderby' ] = 'post_date DESC';
		}

		if ( $page && gp_is_integer( $page ) && $page > 1 ) {
			$query_args[ 'paged' ] = $page;
		}

		// var_dump($query_args);

		$query = new WP_Query( $query_args );

		$found_posts = isset( $query->found_posts ) ? $query->found_posts : false;

		// if no found posts, and query had a particular page, show query results from page 1 instead
		// note: wordpress puts in this (or similar) functionality by default on its blog pages also
		if ( $found_posts == 0 && isset( $query_args[ 'paged' ] ) ) {
			unset( $query_args[ 'paged' ] );
			// likely we won't need to know about this, but we'll put it into the query args anyways
			$query_args[ 'gp_page_adjusted' ] = 1;
			$fallback_query                   = new WP_Query( $query_args );
			self::$query                      = $fallback_query;

			return $fallback_query;
		}

		self::$query = $query;

		return $query;
	}
}
