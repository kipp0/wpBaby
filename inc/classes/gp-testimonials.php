<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 3/14/2018
 * Time: 3:27 PM
 */

Class GP_Testimonials{

	public static $post_type = 'gp_testimonials';
	public static $get_var = 'id'; // use to permalink single testimonials on a page showing all testimonials

	/**
	 * GP_Testimonials constructor.
	 *
	 * @param bool $init
	 */
	public function __construct( $init = false ){
		if ( $init ) {
			$this->init();
		}
	}

	/**
	 * Gets an array of data for use on the home page. The homepage will handle looping and outputting html
	 * @return array
	 */
	public static function get_home_data() {

		$data = array();

		$args = array(
			'post_status' => 'publish',
			'post_type' => GP_Testimonials::$post_type,
			'orderby' => array(
				'home_order_num' => 'ASC',
				'post_date' => 'DESC',
			),
			'posts_per_page' => - 1, // could limit??
			'meta_query' => array(
				'relation' => 'AND',
				'home_order_num' => array( // use this to order as well
					'key' => 'home_order',
					'value' => 0,
					'compare' => 'NOT LIKE'
				),
				// not sure if we need this?? wondering if not equals would return if key doesnt exist
				array(
					'key' => 'home_order',
					'compare' => 'EXISTS'
				)
			)
		);

		$new_query = new WP_Query( $args );

		if ( $new_query->have_posts() ) {
			while ( $new_query->have_posts() ) {
				$new_query->the_post();
				$p = get_post();

				$button_text = get_post_meta( $p->ID, 'button_text', true );
				$button_text = trim( $button_text );
				$button_text = $button_text ? $button_text : 'Read More';

				if ( strpos( strtolower( $button_text ), 'video' ) !== false ) {
					$button_icon = 'fa-play';
				} else {
					$button_icon = 'fa-angle-right';
				}

				$data[] = array(
					'content' => GP_Testimonials::get_home_excerpt( $p ),
					'credit' => self::get_credit( $p ),
					'button_text' => $button_text,
					'button_icon' => $button_icon,
					'button_url' => GP_Testimonials::get_single_testimonial_url( $p ),
				);

			}
			wp_reset_postdata();
		}
		return $data;
	}

	/**
	 * @param $p
	 */
	public static function get_credit( $post ) {

		$post = gp_make_post( $post );
		if ( ! $post ) {
			return '';
		}

		$credit      = get_post_meta( $post->ID, 'tst_credit', true );
		$credit      = trim( $credit );
		return $credit;
	}

	/**
	 * Check a specific field, otherwise excerptize the post content to trim words.
	 *
	 * I think we're going to "wpautop" this later on... so for now, return value without any p tags.
	 *
	 * @param $post
	 */
	public static function get_home_excerpt( $post ) {

		$post = gp_make_post( $post );
		if ( ! $post ) {
			return '';
		}

		$excerpt = get_post_meta( $post->ID, 'tst_excerpt', true );
		$excerpt = trim( $excerpt );

		if ( ! $excerpt ) {
			$pc = get_post_field( 'post_content', $post );
			$pc = do_shortcode( $pc );
			$excerpt = gp_excerptize( $pc, 20 );
		}

		return $excerpt;
	}

	/**
	 * @param $post
	 */
	public static function get_single_testimonial_url( $post ){

		$post = gp_make_post( $post );
		if ( ! $post ) {
			return '';
		}

		$archive = self::get_archive_page_url();
		$url = add_query_arg( array(
			self::$get_var => $post->ID,
		), $archive );

		return $url;
	}

	/**
	 * This might just be a specific page template, or a default page template
	 * using a shortcode... depends how we do it.  It probably just returns
	 * $home . '/testimonials'
	 */
	public static function get_archive_page_url(){

		global $gp_tst_archive_page_url;

		// in case we have to do a query to find the page, lets store url in a global for repeated use
		if ( $gp_tst_archive_page_url !== null ) {
			return $gp_tst_archive_page_url;
		}

		// hardcoded url for the time being
		$url = get_bloginfo( 'url' ) . '/testimonials';
		$gp_tst_archive_page_url = $url;
		return $gp_tst_archive_page_url;
	}

	/**
	 * Run once per page load only
	 */
	public function init(){
		add_action( 'init', array( $this, 'wp_init' ) );
	}

	/**
	 * @param      $atts
	 * @param null $content
	 *
	 * @return string
	 */
	public function gp_testimonials_shortcode( $atts, $content = null ) {

		$op = '';
		$df = array(
			'ids' => 'all',
		);
		$df = gp_filter_default_shortcode_atts( 'testimonials', $df );
		$aa =  shortcode_atts( $df, $atts );

		$ids = gp_if_set( $aa, 'ids' );
		$ids = trim( $ids );

		if ( $ids === 'all' ) {
			$posts = self::get_all_posts();
		} else if ( strpos( $ids, ',' ) !== false ) {

			// turn comma separated list of IDs into an array
			$posts = gp_something_to_array( $ids, true );

		} else {
			$posts = array();
		}

		// this function should accept an array of post IDs, or an array of WP_Post objects
		return self::html_from_posts( $posts );
	}

	/**
	 * @param $post
	 *
	 * @return string
	 */
	public function get_single_testimonial_html( $post ){

		$post = gp_make_post( $post );
		if ( ! $post ) {
			return '';
		}

		$pc = get_post_field( 'post_content', $post );
		$pc = apply_filters( 'the_content', $pc );

		$op = '';
		$op .= '<div class="gp-testimonial" data-id="' . $post->ID . '">';
		$op .= '<div class="gt-inner">';

		// "quote" icon?
		$op .= '<div class="gt-img">';
		$op .= '<img src="' . get_template_directory_uri() . '/assets/images/quotes-red.png">';
		$op .= '</div>'; // gt-img

		// content
		$op .= '<div class="gt-content">';
		$op .= $pc;
		$op .= '</div>'; // gt-content

		// credit
		$op .= '<div class="gt-credit">';
		$op .= '<p><span class="small">' . self::get_credit( $post ) . '</span></p>';
		$op .= '</div>'; // gt-credit

		$op .= '</div>'; // gt-inner
		$op .= '</div>';

		return $op;
	}

	/**
	 * $posts can be an array of post IDs, or an array of WP_Post objects
	 *
	 * @param $posts
	 *
	 * @return string
	 */
	public function html_from_posts( $posts ) {

		// I think we may not use an outer div here.. this way printing multiple testimonials individually via
		// shortcodes in the editor, is the same as printing many of them in a loop

		$op = '';

		if ( $posts && is_array( $posts ) ) {
			foreach ( $posts as $post ) {

				$post = get_post( $post );

				// careful.. since we are allowing users to specify post IDs from a shortcode attribute
				// which eventually calls this function, so lets make it only work for this post type
				if ( get_post_type( $post ) !== self::$post_type ) {
					continue;
				}

				$op .= self::get_single_testimonial_html( $post );
			}
		}

		return $op;
	}

	/**
	 *
	 */
	public function get_all_posts(){

		$args = array(
		    'post_status' => 'publish',
		    'post_type'   => self::$post_type,
		    'orderby'     => array(
		    	'menu_order' => 'ASC', // not likely we'll change these from 0
		    	'post_date' => 'DESC',
		    ),
		    'posts_per_page' => -1,
		);

		return get_posts( $args );
	}

	/**
	 *
	 */
	public function wp_init(){
		$this->register_post_type();
		add_shortcode( 'testimonials', array( $this, 'gp_testimonials_shortcode' ) );
	}

	/**
	 *
	 */
	public function register_post_type(){

		// *******************************
		//           Testimonials
		// *******************************
		$labels = array(
			'name' => _x( 'Testimonials', 'post type general name', 'your-plugin-textdomain' ),
			'singular_name' => _x( 'Testimonial', 'post type singular name', 'your-plugin-textdomain' ),
			'menu_name' => _x( 'Testimonials', 'admin menu', 'your-plugin-textdomain' ),
			'name_admin_bar' => _x( 'Testimonial', 'add new on admin bar', 'your-plugin-textdomain' ),
			'add_new' => _x( 'Add New', 'Testimonial', 'your-plugin-textdomain' ),
			'add_new_item' => __( 'Add New Testimonial', 'your-plugin-textdomain' ),
			'new_item' => __( 'New Testimonial', 'your-plugin-textdomain' ),
			'edit_item' => __( 'Edit Testimonial', 'your-plugin-textdomain' ),
			'view_item' => __( 'View Testimonial', 'your-plugin-textdomain' ),
			'all_items' => __( 'Testimonials', 'your-plugin-textdomain' ),
			'search_items' => __( 'Search Testimonials', 'your-plugin-textdomain' ),
			'parent_item_colon' => __( 'Parent Testimonials:', 'your-plugin-textdomain' ),
			'not_found' => __( 'No Testimonials found.', 'your-plugin-textdomain' ),
			'not_found_in_trash' => __( 'No Testimonials found in Trash.', 'your-plugin-textdomain' )
		);

		$args = array(
			'labels' => $labels,
			'public' => false, // going to make shortcode to display
			'show_ui' => true,
			'has_archive' => false, // we will use a page template (and shortcodes) to list testimonials
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => 'testimonials',
			),
			'show_in_menu' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		);

		register_post_type( self::$post_type, $args );
	}
}

// register post type, etc.
new GP_Testimonials( true );