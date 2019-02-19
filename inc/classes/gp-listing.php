<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 2/13/2018
 * Time: 10:24 AM
 */

Class GP_Listing {

	protected $post;
	protected $post_id;

	protected $categories; // array of WP_Terms
	protected $types; // array of WP_Terms

	public function __construct( $post_id ) {

		$post = get_post( $post_id );

		if ( get_post_type( $post ) !== GP_Listings_Init::$post_type ) {
			throw new Exception( 'Invalid post ID for listing' );
		}

		$this->post    = $post;
		$this->post_id = $post->ID;
	}

	/**
	 * @return bool
	 */
	public function is_valid(){
		return get_post_type( $this->post ) === GP_Listings_Init::$post_type;
	}

	/**
	 * @return int
	 */
	public function get_id(){
		return $this->post_id;
	}

	/**
	 * @return array|null|WP_Post
	 */
	public function get_post(){
		return $this->post;
	}

	/**
	 *
	 */
	public function get_bottom_gallery_html(){

		$op = '';
		$ids = $this->get_bottom_gallery_images();

		// collect image data
		$images = array();
		if ( $ids ) {
			// todo: captions not working
			foreach ( $ids as $id ) {
				$src_md = wp_get_attachment_image_src( $id, 'medium' );
				$src_lg = wp_get_attachment_image_src( $id, 'large' );
				$src_lightbox = wp_get_attachment_image_src( $id, 'full' ); // todo: make new img size ~ 1600px and use that instead of full
				$caption = gp_get_image_caption( $id );
				if ( $src_lg && $src_md ) {
					$image_data = array(
						'url_md' => gp_if_set( $src_md, 0 ),
						'w_md' => gp_if_set( $src_md, 1 ),
						'h_md' => gp_if_set( $src_md, 2 ),
						'url_lg' => gp_if_set( $src_lg, 0 ),
						'w_lg' => gp_if_set( $src_lg, 1 ),
						'h_lg' => gp_if_set( $src_lg, 2 ),
						'before_img' => '<a class="bg-lightbox" data-caption="test23" title="test" href="' . gp_if_set( $src_lightbox, 0 ) . '" data-rel="lightbox" data-lightbox-gallery="bg-gal">',
						'after_img' => '</a>',
						'caption' => 'caption',
					);

					// echo $image_data['before_img'] . 'SADLKJASDLKJHASD' . $image_data['after_img'];

					$images[] = $image_data;
				}


			}


		}

		// store array of all image data in data attribute..
		$op .= '<div class="gp-bottom-gallery" data-images="' . gp_encode_json_for_data_attribute( $images ) . '"></div>';

		return $op;
	}

	/**
	 *
	 */
	public function get_bottom_gallery_images(){
		$ids = array();
		$att = new Attachments( GP_Listings_Init::$attachments_2, $this->post_id  );
		if ( $att->exist() ) {
			while( $attachment = $att->get() ) {
				$id = isset( $attachment->id ) ? $attachment->id : false;
				if ( $id ) {
					$ids[] = $id;
				}
			}
		}

		return $ids;
	}

	/**
	 *
	 */
	public function get_details_btn_url(){
		$url = $this->get_post_meta_basic( 'details_btn_url' );
		$url = trim( $url );
		$href = $url ? gp_href_from_shortcode_att( $url ) : '';
		return $href;
	}

	/**
	 * $type is probably the meta_key in postmeta
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public static function get_icon( $type ) {

		$i = '';

		switch( $type ) {
			case 'bedrooms':
				$i = 'fa-bed';
				break;
			case 'bathrooms':
				$i = 'fa-tint';
				break;
			case 'type':
				$i = 'fa-home'; // this refers to our "type" taxonomy
				break;
			case 'sqft':
				$i = 'fa-arrows-alt';
				break;
			case 'lot_size':
				// $i = 'fa-paglines'; maybe ?
				$i = 'fa-tree';
				break;
			case 'parking':
				$i = 'fa-car';
				break;
			case 'garage':
				$i = 'fa-warehouse';
				break;
			case 'status':
				$i = 'fa-hourglass-start';
				break;
			case 'style':
				$i = 'fa-key'; // i have no idea what to use here...
				break;
			case 'rooms':
				$i = 'fa-lightbulb'; // i really cant find anything better than lightbulb here
				break;
			default:
				break;
		}

		return self::get_icon_html_from_string( $i );
	}

	/**
	 * ie. pass in fa-play, and get <i class="fa fa-play"></i>
	 *
	 * note: font awesome icons must start with fa-, this is not valid: "fa fa-play"
	 *
	 * @param string $str
	 */
	public static function get_icon_html_from_string( $str, $default = '' ) {

		// if starts with fa-
		if ( strpos( $str, 'fa-' ) === 0 ) {
			return '<i class="fa ' . $str . '"></i>';
		}

		// note: this is where we could check for prefix like "gm" if we need to include google material icons as well
		// or maybe another icon set

		// icon string invalid basicaly
		return $default;
	}

	/**
	 * Context does nothing currently, but we can use it to change map marker html
	 * on single page vs. archive page
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function get_map_marker_html( $content = 'archive' ) {

		$image = get_post_thumbnail_id( $this->post_id );
		$href  = get_the_permalink( $this->post );

		$icons   = array();
		$icons[] = array(
			'icon' => '<i class="fa fa-bed"></i>',
			'text' => $this->get_bedrooms() . ' Bedrooms',
		);
		$icons[] = array(
			'icon' => '<i class="fa fa-tint"></i>',
			'text' => $this->get_bathrooms() . ' Bathrooms',
		);

		$op = '';
		$op .= '<div class="map-marker-listing">';

		if ( $image ) {
			$op .= '<a class="thumbnail" href="' . $href . '">';
			$op .= GP_Background_Image::get_simple( $image, 'thumbnail', array(
				'bg_overlay' => true,
			) );
			$op .= '</a>'; // thumbnail
		}

		$op .= '<p class="title"><a href="' . $href . '">' . $this->get_address() . '</a></p>';
		$op .= '<p class="price">' . $this->get_price( true ) . '</p>';
		$op .= '<div class="icons">';
		$op .= self::html_from_icons( $icons );
		$op .= '</div>'; // icons
		$op .= '</div>'; // map marker listing

		return $op;
	}

	/**
	 *
	 */
	public function get_categories() {
		if ( $this->categories !== null ) {
			return $this->categories;
		}

		$terms            = get_the_terms( $this->post_id, GP_Listings_Init::$tax_cat );
		$this->categories = $terms && ! is_wp_error( $terms ) ? $terms : array();

		return $this->categories;
	}

	/**
	 * @return mixed
	 */
	public function get_types() {
		if ( $this->types !== null ) {
			return $this->types;
		}

		$terms       = get_the_terms( $this->post_id, GP_Listings_Init::$tax_type );
		$this->types = $terms && ! is_wp_error( $terms ) ? $terms : array();
		return $this->types;
	}

	/**
	 * Gets term name for first assigned listing type (which is a taxonomy)...
	 *
	 * listings assigned to 2 types only return one...
	 *
	 * @return string
	 */
	public function get_type(){
		$types = $this->get_types();
		$first = gp_if_set( $types, 0 );
		$type = isset( $first->name ) ? $first->name : '';
		return $type;
	}

	/**
	 * Get post meta based on key, and maybe sanitize
	 *
	 * @param        $key
	 * @param string $context
	 */
	public function get_post_meta( $key, $context = 'raw' ){
		$val = get_post_meta( $this->post_id, $key, true );
		return $this->sanitize_by_context( $val, $context );
	}

	/**
	 * @param $value
	 * @param $context
	 */
	public function sanitize_by_context( $value, $context ) {
		switch( $context ) {
			case 'raw':
				// do nothing
				break;
			case 'raw_safe':
				$value = trim( $value );
				// $value = stripslashes( $value ); // dont think this is needed
				$value = htmlspecialchars( $value );
				break;
			case 'basic':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = '';
		}

		return $value;
	}

	/**
	 *
	 */
	public function is_sold() {
		$val = get_post_meta( $this->post_id, 'sold', true );
		$val = trim( $val );
		return (bool) $val;
	}

	/**
	 *
	 */
	public function is_featured() {
		$val = get_post_meta( $this->post_id, 'featured', true );
		$val = trim( $val );

		return (bool) $val;
	}

	/**
	 * todo: will this just be the post title or.. a separate field??
	 */
	public function get_address() {
		$val = get_post_meta( $this->post_id, 'displayed_address', true );
		$val = trim( $val );

		return $val;
	}

	/**
	 * @return string
	 */
	public function get_latitude() {
		$val = get_post_meta( $this->post_id, 'latitude', true );
		$val = trim( $val );
		return sanitize_text_field( $val );
	}

	/**
	 * @return string
	 */
	public function get_longitude() {
		$val = get_post_meta( $this->post_id, 'longitude', true );
		$val = trim( $val );
		return sanitize_text_field( $val );
	}

	/**
	 * @param $key
	 */
	public function get_post_meta_int( $key ) {
		$val = get_post_meta( $this->post_id, $key, true );
		$val = trim( $val );
		return (int) $val;
	}

	/**
	 * @return array
	 */
	public function get_featured_icons() {

		$icons = array();

		$types = $this->get_types();

		$type = gp_if_set( $types, 0 );

		if ( $type && $type instanceof WP_Term ) {
			$icons[] = array(
				'icon' => '<i class="fa fa-home"></i>',
				'text' => sanitize_text_field( $type->name ),
			);
		}

		$bth = get_post_meta( $this->post_id, 'bathrooms', true );
		$bth = trim( $bth );

		$icons[] = array(
			'icon' => '<i class="fa fa-tint"></i>', // water droplet
			'text' => $bth . ' Bathroom',
		);

		$bdr = get_post_meta( $this->post_id, 'bedrooms', true );
		$bdr = trim( $bdr );

		$icons[] = array(
			'icon' => '<i class="fa fa-bed"></i>', // water droplet
			'text' => $bdr . ' Bedroom',
		);

		$sqft = get_post_meta( $this->post_id, 'sqft', true );
		$sqft = trim( $sqft );

		$icons[] = array(
			'icon' => '<i class="fa fa-arrows-alt"></i>', // water droplet
			'text' => $sqft . ' Square Feet',
		);

		return $icons;
	}

	/**
	 * @return mixed|string
	 */
	public function get_bedrooms( $format = false ) {
		$val = get_post_meta( $this->post_id, 'bedrooms', true );
		$val = trim( $val );

		if ( ( $format && $val ) || ( $format && $val === '0' ) ) {
			return $val . ' Bedrooms';
		}

		return $val;
	}

	/**
	 * @param bool $format
	 */
	public function get_square_feet( $format = false ) {
		$val = get_post_meta( $this->post_id, 'sqft', true );
		$val = trim( $val );

		if ( $format && $val ) {
			return $val . ' SQ. FT.';
		}

		return $val;
	}

	/**
	 * @return mixed|string
	 */
	public function get_bathrooms( $format = false ) {
		$val = get_post_meta( $this->post_id, 'bathrooms', true );
		$val = trim( $val );

		if ( ( $format && $val ) || ( $format && $val === '0' ) ) {
			return $val . ' Bathrooms';
		}

		return $val;
	}

	/**
	 * If your value could have special characters or whatever else then this
	 * function may over sanitize some of your data so just use get post meta directly and
	 * do whatever you need to do..
	 *
	 * @param $key
	 *
	 * @return mixed|string
	 */
	public function get_post_meta_basic( $key ){
		$val = get_post_meta( $this->post_id, $key, true );
		$val = sanitize_text_field( $val );
		return $val;
	}

	/**
	 * @return mixed|string
	 */
	public function get_mls_number(){
		return $this->get_post_meta_basic( 'mls_number' );
	}

	/**
	 * @param $icons
	 *
	 * @return string
	 */
	public static function html_from_icons( $icons ) {
		$op = '';

		if ( $icons && is_array( $icons ) ) {


			foreach ( $icons as $icon ) {

				// crappy naming scheme but be careful not to call this $icon..
				$icon_icon = gp_if_set( $icon, 'icon' );
				$text      = gp_if_set( $icon, 'text' );

				$op .= '<p class="icon-text">';
				$op .= '<span class="icon">' . $icon_icon . '</span>';
				$op .= '<span class="text">' . $text . '</span>';
				$op .= '</p>';

			}
		}

		return $op;
	}

	/**
	 * For usage on single listing page sidebar
	 */
	public function get_all_icons() {

		// some reptition below, but it allows for individual logic on each
		// item, which i'm not sure if we'll need to end up using.
		$icons = array();

		$type = $this->get_type();
		if ( $type ) {
			$icons[] = array(
				'icon' => self::get_icon( 'type' ),
				'text' => $type,
			);
		}

		$bedrooms = $this->get_bedrooms( true );
		if ( $bedrooms ) {
			$icons[] = array(
				'icon' => self::get_icon( 'bedrooms' ),
				'text' => $bedrooms,
			);
		}

		$bathrooms = $this->get_bathrooms( true );
		if ( $bathrooms ) {
			$icons[] = array(
				'icon' => self::get_icon( 'bathrooms' ),
				'text' => $bathrooms,
			);
		}

		$sqft = $this->get_square_feet( true );
		if ( $sqft ) {
			$icons[] = array(
				'icon' => self::get_icon( 'sqft' ),
				'text' => $sqft,
			);
		}

		$lot_size = $this->get_lot_size();
		if ( $lot_size ) {
			$icons[] = array(
				'icon' => self::get_icon( 'lot_size' ),
				'text' => $lot_size,
			);
		}

		$parking = $this->get_parking();
		if ( $parking ) {
			$icons[] = array(
				'icon' => self::get_icon( 'parking' ),
				'text' => $parking,
			);
		}

		$garage = $this->get_garage();
		if ( $garage ) {
			$icons[] = array(
				'icon' => self::get_icon( 'garage' ),
				'text' => $garage,
			);
		}

		return $icons;
	}

	/**
	 *
	 */
	public function get_parking( $format = true ){
		$val = $this->get_post_meta_basic( 'parking' );
		return $val;
	}

	/**
	 *
	 */
	public function get_garage( $format = true ){
		$val = $this->get_post_meta_basic( 'garage' );
		return $val;
	}

	/**
	 *
	 */
	public function get_lot_size( $format = true ){
		$val = $this->get_post_meta_basic( 'lot_size' );
		return $val;
	}

	/**
	 * @param bool $format
	 */
	public function get_age_of_home( $format = true ) {
		$val = gp_get_post_meta_sanitize( $this->post_id, 'age_of_home', true );
		if ( $format ) {
			if ( gp_is_integer( $val ) ) {
				$val = $val . ' years';
			}
		}
		return $val;
	}

	/**
	 * @param bool $format
	 *
	 * @return int|mixed|string
	 */
	public function get_property_taxes( $format = true ){
		$val = gp_get_post_meta_sanitize( $this->post_id, 'property_taxes', true );
		if ( $format ) {
			if ( gp_is_integer( $val ) ) {
				$val = '$' . number_format( $val, 0, '.', ',' );
			}
		}
		return $val;
	}

	/**
	 *
	 */
	public function get_details_html() {

		$details = array();

		$details_content = get_post_meta( $this->post_id, 'details_content', true );
		global $listing_details_data;
		$listing_details_data = array();

		// show at top, from custom post meta value
		$age_of_home = $this->get_age_of_home( true );
		if ( $age_of_home ) {
			$listing_details_data[] = array(
				'prop' => 'Age of Home:',
				'value' => $age_of_home,
			);
		}

		// show at top, from custom post meta value
		$property_taxes = $this->get_property_taxes( true );
		if ( $property_taxes ) {
			$listing_details_data[] = array(
				'prop' => 'Property Taxes:',
				'value' => $property_taxes,
			);
		}

		// add more data to global array
		$dont_print = do_shortcode( $details_content );

		return $this->html_from_details( $listing_details_data );

//		$details[] = array(
//			'prop'  => 'Age of Home:',
//			'value' => '65 Years',
//		);
//
//		$details[] = array(
//			'prop'  => 'Property Taxes:',
//			'value' => '$3,269',
//		);
//
//		$details[] = array(
//			'prop'  => 'Public Schools:',
//			'value' => 'Lions Oval P.S., Orillia S.S.',
//		);
//
//		$details[] = array(
//			'prop'  => 'Private Schools:',
//			'value' => 'Kempenfelt Bay School',
//		);
//
//		$details[] = array(
//			'prop'  => 'High Schools:',
//			'value' => 'General Bay High School',
//		);
//
//		$details[] = array(
//			'prop'  => 'Écoles Françaises:',
//			'value' => 'elfontario.ca',
//		);

		// return $this->html_from_details( $details );
	}

	/**
	 * You should wrap this in a <div class="gp-details"> to be sure styles are applied
	 *
	 * @param $details
	 */
	public function html_from_details( $details ) {

		$op = '';

		if ( $details && is_array( $details ) ) {
			foreach ( $details as $kk => $vv ) {

				$cls = array(
					'gp-detail'
				);

				$prop  = gp_if_set( $vv, 'prop', '' );
				$prop = trim( $prop );
				$prop = trim( $prop, ':' );
				$prop = $prop . ':';

				$value = gp_if_set( $vv, 'value', '' );
				$value = trim( $value );

				$classes[] = $prop ? 'has-prop' : 'no-prop';
				$classes[] = $value ? 'has-value' : 'no-value';

				if ( $prop || $value ) {
					$op    .= '<div class="' . gp_parse_css_classes( $cls ) . '">';
					$op    .= '<p class="prop">' . $prop . '</p>';
					$op    .= '<p class="value">' . $value . '</p>';
					$op    .= '</div>';
				}
			}
		}

		return $op;
	}

	/**
	 * @return string
	 */
	public function get_icons_html() {
		return $this->html_from_icons( $this->get_all_icons() );
	}

	/**
	 *
	 */
	public function get_featured_icons_html() {
		return $this->html_from_icons( $this->get_featured_icons() );
	}

	/**
	 * @param bool $format
	 */
	public function get_price( $format = true, $dollar_sign = '$' ) {

		$price = get_post_meta( $this->post_id, 'price', true );
		$price = sanitize_text_field( $price );
		$price = (int) $price;

		if ( ! $format ) {
			return $price;
		}

		$price = $dollar_sign . number_format( $price, 0, '', ',' );

		return $price;
	}

	/**
	 * This might change..
	 *
	 * @return string
	 */
	public function get_schedule_viewing_url(){
		return self::get_schedule_viewing_url_static( $this->post_id );

	}

	/**
	 * We need access from outside the class sometimes
	 *
	 * @param $listing_id
	 *
	 * @return string
	 */
	public static function get_schedule_viewing_url_static( $listing_id ) {
		return get_bloginfo( 'url' ) . '/contact?listing_id=' . $listing_id;
	}

}