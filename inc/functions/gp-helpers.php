<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// ******************************
//           Randoms
// ******************************

/**
 * Useful if array keys might be dynamic
 *
 * @param $arr
 */
function gp_array_first( $arr, $default = null ) {
	if ( is_array( $arr ) ) {
		$arr2 = array_values( $arr );

		return gp_if_set( $arr2, 0, $default );
	}

	return $default;
}

/**
 * This can be used to replace statements such as:
 * $var = isset( $data[$index] ) ? $data[$index] : $default.
 * However, we'll use array_key_exists because isset returns false when the array key
 * exists but value is null.
 *
 * @param              $data
 * @param array|string $index
 * @param bool         $default
 *
 * @return bool|mixed
 */
function gp_if_set( $data, $index, $default = false ) {
	if ( ! is_array( $data ) ) {
		return $default;
	} else {
		// lets you check multiple indexes at once, and returns the first one that is set.
		if ( is_array( $index ) ) {
			foreach ( $index as $ii ) {
				if ( isset( $data[$ii] ) ) {
					return $data[ $ii ];
				}
			}
			return $default;
		}

		// array key exists giving errors (warnings actually) on $index === 0 even though it should not..
		// and also doing it sometimes not all the time, im mind boggled as to why
		if ( isset( $data[$index] ) ) {
			return $data[ $index ];
		}
	}

	return $default;
}

/**
 * @param      $data
 * @param      $index
 * @param bool $default
 *
 * @return bool
 */
function gp_first_set_and_not_empty( $data, $index, $default = false ) {

	$ii = is_array( $index ) ? $index : array_filter( array( $index ) );

	foreach ( $ii as $key ) {
		if ( isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ) {
			return $data[ $key ];
		}
	}

	return $default;
}

/**
 * Pass in a string, or an array of strings, or an array of arrays of strings or maybe more arrays
 * returns a bunch of words with spaces.
 *
 * @param $classes
 *
 * @return string
 */
function gp_parse_css_classes( $classes ) {

	// for sake of efficiency, lets just return the string if that was passed in
	if ( is_string( $classes ) ) {
		$classes = trim( $classes );
		$classes = esc_attr( $classes );

		return $classes;
	}

	$classes = is_array( $classes ) ? $classes : (array) $classes;

	// remove empty array values, otherwise might have some double spaces in class list after implode
	$classes = array_filter( $classes );

	// trim each class
	array_map( 'trim', $classes );

	// can do array_unique to remove possible duplicates, but I don't think its needed
	// $classes = array_unique( $classes );

	// for security
	array_map( 'esc_attr', $classes );

	// call the function recursively if an array value is also an array
	if ( $classes ) {
		foreach ( $classes as $index => $class ) {
			if ( is_array( $class ) ) {
				$classes[ $index ] = gp_parse_css_classes( $class );
			}
		}
	}

	return implode( ' ', $classes );
}

/**
 * Used to print opening tags with a large number of, or highly variable attributes.
 * ie. <a target="..." href="..." class="cls1 cls2 maybe-cls3 maybe-cls4" data-something="..." style="...">
 * Lets you build the tag with an array, which is much easier and cleaner for complicated tags.
 * $atts['class'] and $atts['style'] can be arrays of css classes or styles.
 * All other $atts[''] elements can be arrays as well, but we'll json encode the array, assuming we want to read it
 * with javascript.
 *
 * @param       $tag
 * @param array $atts
 */
function gp_atts_to_container( $tag = 'div', $atts = array(), $close_tag = false, $inner_html = '' ) {

	// atts string
	$str = '';

	if ( isset( $atts[ 'class' ] ) && is_array( $atts[ 'class' ] ) ) {
		$atts[ 'class' ] = gp_parse_css_classes( $atts[ 'class' ] );
	}

	$implodes = array(
		// 'class' => ' ', // we have a better function for this already
		'style' => '; ',
	);

	// future: maybe allow for $atts['style'] = array( 'display' => 'block' ) for example
	// future: right now we only allow for array( 'display: block', 'background: red' );
	if ( ! empty( $implodes ) ) {
		foreach ( $implodes as $att => $glue ) {
			if ( isset( $atts[ $att ] ) && is_array( $atts[ $att ] ) ) {
				$att_val      = implode( $glue, $atts[ $att ] );
				$att_val      = trim( $att_val );
				$atts[ $att ] = $att_val;
			}
		}
	}

	if ( ! empty( $atts ) ) {
		foreach ( $atts as $key => $value ) {

			// this lets you pass in array values to be encoded for data attributes
			// should we check other conditions here, like only json encode if the $key has 'data' in its string position?
			// wouldnt want to unintentionally json encode something. But I suppose json encoded
			// is better than getting a php error for trying to store an array into a string
			if ( is_array( $value ) ) {
				$value = gp_encode_json_for_data_attribute( $value );
			}

			// is there ever a time we wouldn't want to do this??
			// mainly with optional classes, sometimes we end up with that extra space at the end
			$value = trim( $value );

			if ( $key === 'style' ) {
				// I think this will trim spaces, then possible semi-colons, then possibly more spaces but im not sure
				$value = trim( $value, ' ; ' );
			}

			$str .= $key . '="' . $value . '" '; // space at end
		}
	}

	// remove last space
	$str = trim( $str );

	if ( strlen( $str ) > 0 ) {
		$r = '<' . $tag . ' ' . $str . ' >';

	} else {
		$r = '<' . $tag . '>';
	}

	$r .= $inner_html;

	if ( $close_tag ) {
		$r .= '</' . $tag . '>';
	}

	return $r;
}

/**
 * to decode the JSON data in javascript, use the following:
 * var data = div.attr('data-json');
 * data = jQuery.parseJSON(data);
 *
 * @param $data
 *
 * @return string
 */
function gp_encode_json_for_data_attribute( $data ) {

	if ( is_array( $data ) ) {
		return htmlspecialchars( json_encode( $data ), ENT_QUOTES, 'UTF-8' );
	}

	return '';
}

/**
 * variation of a function meks_wp_parse_args found online..
 * its like wp_parse_args, but lets your args and defaults be arrays of other arrays,
 * and parses those args as well
 *
 * @param      $a
 * @param      $b
 * @param null $on_keys
 *
 * @return array
 */
function gp_parse_args_recursive( &$args, $defaults, $on_keys = null ) {
	if ( $on_keys === null ) {
		// ie. on all keys..
		$on_keys = array_keys( $defaults );
	}
	$args     = (array) $args;
	$defaults = (array) $defaults;
	$result   = $defaults;
	foreach ( $args as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) && in_array( $k, $on_keys ) ) {
			$result[ $k ] = gp_parse_args_recursive( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}

	return $result;
}

/**
 * @return bool|mixed
 */
function gp_in_development() {
	$key = 'in_development';
	if ( gp_is_global_set( $key ) ) {
		return gp_get_global( $key );
	} else {
		$home = get_bloginfo( 'url' );
		if ( strpos( $home, '.geekpower.ca' ) ) {
			$val = true;
		} else if ( strpos( $home, '.geekpower.ca' ) ) {
			// future: make this work for local installs (maybe storing value in wp_options is a better way to handle this..)
			$val = true;
		} else {
			$val = false;
		}
		gp_set_global( $key, $val );
		return $val;
	}
}

/**
 * @return bool
 */
function gp_in_production() {
	return ! gp_in_development();
}

/**
 * @param string $format
 *
 * @return false|string
 */
function gp_date_now_wp_timezone( $format = 'Y-m-d H:i:s' ) {
	$now = current_time( 'timestamp' );

	return date( $format, $now );
}

/**
 * @param $str
 *
 * @return bool
 */
function gp_is_integer( $str ) {
	$type = gettype( $str );
	if ( $type === 'integer' || $type === 'string' ) {
		// casting as string is important, because otherwise
		// if the integer passed in corresponds to the ASCII code for
		// a letter on the keyboard, ctype_digit returns false instead of true
		// ie.... ctype_digit( 65 ) is actually false b/c charcode of 65 is "A"
		// but ctype_digit( (string) 65 ) is true
		// pretty retarded if you ask me...
		return ctype_digit( (string) $str );
	}

	return false;
}

/**
 * Trim a string or an array of strings of variable length from the end of a string
 * Works how you would want (and maybe even expect) this to work: trim( 'abcdef_random-garbage', '_random-garbage' );
 *
 * @param $string
 * @param $trim
 */
function gp_trim_end( $string, $trim ) {
	$trim = is_array( $trim ) ? array_filter( $trim ) : array_filter( array( $trim ) );
	$trim = array_unique( $trim );
	if ( $trim ) {
		foreach ( $trim as $str ) {
			if ( strpos( $string, $str ) === strlen( $string ) - strlen( $str ) ) {
				$string = substr( $string, 0, strlen( $string ) - strlen( $str ) );
			}
		}
	}

	return $string;
}

/**
 * Used for adding columns on edit.php
 * Lets you anchor your column before another column, ie. the date column.
 *
 * @param      $key
 * @param      $value
 * @param      $target_key
 * @param      $target_arr
 * @param bool $before
 * @param bool $fallback
 *
 * @return array
 */
function gp_insert_before_array_key( $key, $value, $target_key, $target_arr, $before = true, $fallback = true ) {

	$new_arr = array();

	// echo '<pre>' . print_r( $target_arr, true ) . '</pre>';

	if ( is_array( $target_arr ) && $key ) {

		// array_map();
		foreach ( $target_arr as $kk => $vv ) {
			if ( $kk == $target_key ) {
				if ( $before ) {

					$new_arr[ $key ] = $value;
					$new_arr[ $kk ]  = $vv;
				} else {
					$new_arr[ $kk ]  = $vv;
					$new_arr[ $key ] = $value;
				}
			} else {
				$new_arr[ $kk ] = $vv;
			}
		}
	}

	if ( $fallback ) {
		if ( ! array_key_exists( $key, $new_arr ) ) {
			$new_arr[ $key ] = $value;
		}
	}

	return $new_arr;
}

/**
 * Stores a msg in $_SESSION,
 * then on page load, check is_admin(), and if we have messages, show them.
 * This is handy if for example, you save a post, and want to show a message after the post is saved
 * and the page re-loads.
 *
 * @param $msg
 */
function gp_add_session_notice( $msg ) {

	if ( ! is_admin() ) {
		return;
	}

	$notices = array();

	if ( is_array( $_SESSION ) ) {
		if ( array_key_exists( 'gp_notices', $_SESSION ) ) {
			$notices = $_SESSION[ 'gp_notices' ];
		}
	}

	$arr = $notices;

	// try to combine string or array into string
	if ( is_array( $notices ) ) {
		$arr[] = $msg;
	} else {
		$arr   = array();
		$arr[] = $notices;
		$arr[] = $msg;
	}

	$_SESSION[ 'gp_notices' ] = $arr;
}

/**
 * @return string
 */
function gp_current_url( $query_args = false ){

	if ( $query_args ) {
		// dont know how this works but it does..
		$url = home_url( add_query_arg( null, null ));
		return $url;
	}

	global $wp;
	return home_url( $wp->request );
}

/**
 * Used for form actions on custom admin pages added via add_menu_page()
 */
function gp_get_current_admin_url() {
	$this_url = admin_url( 'admin.php' );
	foreach ( $_GET as $get_key => $get_val ) {
		$this_url = add_query_arg( $get_key, urlencode( $get_val ), $this_url );
	}

	return $this_url;
}

/**
 * This is used sometimes in place of confidential logged-in-only user data.
 */
function gp_show_404( $msg = '' ) {
	$content = strip_tags( $msg, '<p><br><a><span><strong><div>' );
	gp_set_global( '404_content', $content );
	$path_404 = get_404_template();
	status_header( '404' );
	include( $path_404 );
	exit; // EXIT IS VERY IMPORTANT
}

/**
 * Redirect to 404 page
 */
function gp_redirect_404() {
	$error_page = get_404_template();
	if ( $error_page ) {
		wp_redirect( $error_page );
		exit;
	} else {
		status_header( '404' );
		exit;
	}
}

/**
 * When you need to get a "single" term attached to a post..
 *
 * If more than 1 exist, just grab 1..
 *
 * @param $post_id
 * @param $taxonomy
 * @param bool $default
 * @return WP_Term|mixed bool|mixed
 */
function gp_get_first_term( $post_id, $taxonomy, $default = false, $priority = array() ) {
	$terms_obj = wp_get_post_terms( $post_id, $taxonomy, array() );

	$out = $default;
	$first = true;

	if ( $terms_obj && is_array( $terms_obj ) ) {
		/**
		 * @var  $index
		 * @var WP_Term $wp_term
		 */
		foreach ( $terms_obj as $index=>$wp_term ) {
			if ( $wp_term instanceof WP_Term ) {
				$tid = $wp_term->term_id;

				if ( $first === true ) {
					$out = $wp_term;
					$first = false;
				}

				// return immediately if we find a term id in the priority array
				if ( is_array( $priority ) && in_array( $tid, $priority ) ) {
					return $wp_term;
				}
			}
		}
	}

	// WP_Term or $default
	return $out;
}

/**
 * @param $post_id
 */
function gp_taxonomy_terms_string( $post_id, $taxonomy, $get = 'string' ) {

	if ( $post_id instanceof WP_Post ) {
		$post_id = $post_id->ID;
	}

	if ( ! gp_is_integer( $post_id ) ) {
		return false;
	}

	$terms_obj = wp_get_post_terms( $post_id, $taxonomy, array() );
	$terms_arr = array();

	if ( is_wp_error( $terms_obj ) ) {
		return false;
	}

	if ( $terms_obj && is_array( $terms_obj ) ) {
		/** @var WP_Term $wp_term */
		foreach ( $terms_obj as $wp_term ) {
			$name = isset( $wp_term->name ) ? $wp_term->name : '';
			if ( $name ) {
				$terms_arr[] = $name;
			}
		}
	}

	if ( $get === 'string' ) {
		if ( $terms_arr ) {
			$str = implode( ', ', $terms_arr );
			$str = trim( $str );

			return $str;
		} else {
			return false;
		}
	} else if ( $get === 'array' ) {
		return $terms_arr;
	}

	return false;
}

/**
 *
 */
function gp_shortcodes_documentation_add() {
	//    add_dashboard_page('Shortcodes', 'Shortcodes', 'read', 'gp-shortcodes-documentation', 'gp_shortcodes_documentation');
}

//add_action('admin_menu', 'gp_shortcodes_documentation_add');
function gp_shortcodes_documentation() {
}

/**
 * Checks if date string is in a specific date format
 *
 * @param        $date
 * @param string $format
 *
 * @return bool
 */
function gp_validate_date_format( $date, $format = 'm/d/Y' ) {
	if ( ! $date ) {
		return false;
	}
	try {
		$d = DateTime::createFromFormat( $format, $date );

		return $d && $d->format( $format ) === $date;
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Checks if something is a valid DateTime string
 *
 * @param $date
 */
function gp_is_date_valid( $date ) {

	if ( ! $date ) {
		return false;
	}

	try {
		$dt = new DateTime( $date );
	} catch ( Exception $e ) {
		return false;
	}

	// if no exception was thrown, then we'll say the date is valid
	return true;
}

/**
 * @return string
 */
function gp_get_menu_button( $class = 'gp-menu-button', $count = 3 ) {
	$btn = '';
	$btn .= '<div class="' . $class . '">';
	if ( $count ) {
		for ( $x = 1; $x <= $count; $x ++ ) {
			$btn .= '<div class="menu-button-bar"></div>';
		}
	}
	$btn .= '</div>';

	return $btn;
}

/**
 * Generates an excerpt of the desired text.
 * If no second paramter is passed then it will generate an excerpt 20 words long.
 * If any words are cut off by the excerpt then ( ... ) will be appended to the text.
 * Returns a string.
 *
 * @param string $content text that you would like an excerpt of
 * @param int    $num     number of words to contain in excerpt
 *
 * @return string
 */
function gp_excerptize( $content, $num = 30 ) {
	$number = $num;
	//$content = apply_filters('the_content', $content);
	//echo "<pre style='display: none;'>".print_r($content, true)."</pre>";
	// $content = strip_tags( $content, '<br>' );
	$content = strip_tags( $content, '' );
	$content = str_replace( '&nbsp;', '', $content );

	$contentArray = explode( ' ', $content, $number + 1 );
	//echo '<pre>'.print_r($contentArray, true).'</pre>';
	$contentString = '';
	foreach ( $contentArray as $key => $value ) {
		if ( $key >= $number ) {
			$contentString .= '...';
			break;
		}
		$contentString .= trim( $value );
		if ( $key < $number - 1 ) {
			$contentString .= ' ';
		}
	}

	return $contentString;
}


/**
 * @param $str
 */
function gp_comma_string_to_array( $str, $sep = ',' ) {
	$str = trim( $str );
	$arr = explode( $sep, $str );
	$arr = array_map( 'trim', $arr );
	$arr = array_filter( $arr );
	return $arr;
}


// ******************************
//      Shortcode Functions
// ******************************

/**
 * Start with $defaults. Make modifications to those if you used gp_set_default_shortcode_attribute()
 * then override $defaults with $args,
 * then override that result with $user_args, but only for those specified $allowed_user_args
 * $user_args may come from a string in shortcode parameter format
 *
 * @param array        $defaults
 * @param array        $args
 * @param array        $user_args
 * @param array|string $allowed_user_args
 * @param string       $shortcode
 */
function gp_combine_args( $defaults = array(), $args = array(), $user_args = '', $allowed_user_args = 'array_keys_defaults', $shortcode = '' ) {

	// note that there's possible security issues with this if you're not being mindful about what
	// you are doing with your args in the end, ie. callback functions. In most cases, defaults are
	// an array of default shortcode params basically, so the user should in fact be able to modify them
	// the $args array may however have other arguments not present in defaults that the user cannot modify
	if ( $allowed_user_args === 'array_keys_defaults' ) {
		$allowed_user_args = array_keys( $defaults );
	}

	// often we'll store this string in post meta, ie. 'option_1="true" option_2="false"'
	if ( is_string( $user_args ) ) {
		$user_args = shortcode_parse_atts( $user_args );
	}

	$defaults          = is_array( $defaults ) ? $defaults : array();
	$args              = is_array( $args ) ? $args : array();
	$user_args         = is_array( $user_args ) ? $user_args : array();
	$allowed_user_args = is_array( $allowed_user_args ) ? $allowed_user_args : array();

	// possible modifications to $defaults set via gp_set_default_shortcode_attribute()
	if ( $shortcode ) {
		$defaults = gp_filter_default_shortcode_atts( $shortcode, $defaults );
	}

	// let the args passed in override the defaults
	$result = wp_parse_args( $args, $defaults );

	if ( $result ) {
		foreach ( $result as $kk => $vv ) {
			if ( in_array( $kk, $allowed_user_args ) && isset( $user_args[ $kk ] ) ) {
				$result[ $kk ] = $user_args[ $kk ];
			}
		}
	}

	return $result;
}

/**
 * @param      $str
 * @param bool $force_boolean
 *
 * @return bool|string
 */
function gp_shortcode_boolean( $str, $force_boolean = false ) {

	if ( $str === 'false' || $str === '0' || ! $str ) {
		return false;
	}

	if ( $str === 'true' || $str === '1' || ( $str && $force_boolean ) ) {
		return true;
	}

	return $str;
}

/**
 * Take user input and turn it into a valid container width (int), or return an empty string.
 * Examples of valid width: (string) "500px", (string) "750"
 * Examples of invalid width: (string) "<script>...", (string) "75%", (string) "2s7fd727d"
 * We sometimes use this to apply an inline style for width or max-width from a shortcode parameter
 *
 * @param $width
 */
function gp_shortcode_inline_width( $width ) {
	if ( $width === '' ) {
		return '';
	}

	$width = gp_trim_end( $width, 'px' );

	if ( gp_is_integer( $width ) ) {
		// gp_is_integer returns true on (string) "456", so cast as int in case it was a string
		$width = (int) $width;
		$width = $width < 2560 ? $width : 2560;
		$width = $width > 200 ? $width : 200;

		return $width;
	}

	return '';
}

/**
 * empty string or false returns an empty string
 * 0, or "false" returns: 'opacity: 0;'
 * other strings that represent numbers return ie, 'opacity: .5;'
 *
 * @param $overlay_opacity
 */
function gp_shortcode_overlay_style( $overlay_opacity ) {

	if ( $overlay_opacity === false || $overlay_opacity === '' || $overlay_opacity === null ) {
		return '';
	}

	$overlay_opacity = $overlay_opacity === "0" ? 0 : $overlay_opacity;
	$overlay_opacity = $overlay_opacity === "false" ? 0 : $overlay_opacity;
	$overlay_opacity = gp_get_shortcode_percent( $overlay_opacity );

	return 'opacity: ' . $overlay_opacity . '";';
}

/**
 * Accepts like 0.85, .85, or 85 to mean the same thing. false probably ends up as zero, as does ''.
 *
 * @param $str
 *
 * @return float
 */
function gp_get_shortcode_percent( $str ) {

	$str = (float) $str;
	if ( $str > 0 && $str < 1 ) {
		$str = $str * 100;
	}
	$str = round( $str / 100, 2 );

	// not a string though!
	return $str;
}

/**
 * Basically could be an attachment ID, or url to an image
 *
 * @param $att
 */
function gp_img_url_from_shortcode_att( $att, $size = 'large' ) {

	$att = trim( $att );

	// ctype_digit on an (int) asci char can return false if the charcode is for a non digit,
	// typecasting to (string) solves this
	if ( ctype_digit( (string) $att ) ) {
		// i dont think we need to check post type
		// as wp_get_attachment_url wont return sensitive information from non attachment post types

		$img_url = gp_get_img_url( $att, $size );

		if ( $img_url ) {
			return $img_url;
		}

		// its not very apparent whether we should return the empty string $img_url here
		// or the original parameter passed in
		// however, sometimes, we chain shortcodes together, and each shortcode basically
		// can accept a url or an integer as image attribute
		// so in some rare cases, it might be better to pass the original value along
		// to the next shortcode atts. Overall, it likely wont make a difference however.
		return $att;
	}

	// could be #, 'http....', or anything else really
	return $att;
}

/**
 * Each time you call the function it returns the opposite of what it did last time..
 * Pass in $why if you might end up using this for more than 1 set of interchangeable values
 *
 * @param string $why
 * @param bool   $first
 */
function gp_get_alternate_value( $why = 'because', $default = true ) {

	$key = 'alt_value_' . $why;

	if ( ! gp_is_global_set( $key ) ) {
		gp_set_global( $key, ! $default );

		return $default;
	}

	$val = gp_get_global( $key, $default );

	if ( $val ) {
		gp_set_global( $key, false );

		return true;
	}

	gp_set_global( $key, true );

	return false;
}

/**
 * $att can be a slug, a post_id, or a full url
 * since were only returning the url to the post_id were not doing much checking to verify
 * public/private post types, but if its post type is media, we'll return the url to the image (full size)
 *
 * @param $att
 */
function gp_href_from_shortcode_att( $att ) {

	$att  = trim( $att );
	$href = '';

	// first character is #
	if ( strpos( $att, '#' ) === 0 ) {
		return $att;
	}

	if ( strpos( $att, 'http' ) !== false || strpos( $att, 'www' ) !== false ) {
		return $att;
	} else if ( gp_is_integer( $att ) ) {

		// image or file url
		if ( get_post_type( $att ) == 'attachment' ) {
			$href = wp_get_attachment_url( $att );
		} else {
			// post url
			$href = get_the_permalink( $att );
		}
	}

	// we may want to make this take into account relative urls other than slugs.. like.. ../something
	// may also want to be careful when doing this for security reasons

	// no http, or post_id, lets assume its a slug, with or without a leading slash
	if ( ! $href && strlen( $att ) > 0 ) {

		// remove the slash then add it, to ensure its there
		$att = trim( $att );
		$att = trim( $att, '/' );
		$att = trim( $att );

		$href = get_bloginfo( 'url' ) . '/' . $att;
	}

	return $href;
}

/**
 * Note that keys not defined inside of $defaults don't end up getting passed through.
 *
 * @param $default_atts
 */
function gp_filter_default_shortcode_atts( $shortcode, $defaults ) {
	$global_defaults           = gp_get_shortcode_defaults();
	$global_shortcode_defaults = gp_if_set( $global_defaults, $shortcode, array() );
	// did we define any additional defaults for this shortcode?
	if ( $global_shortcode_defaults ) {
		return shortcode_atts( $defaults, $global_shortcode_defaults );
	}

	return $defaults;
}

/**
 * @param $shortcode
 * @param $att
 */
function gp_get_default_shortcode_att( $shortcode, $att, $global_default = '' ) {
	$defaults = gp_get_shortcode_defaults();

	if ( isset( $defaults[ $shortcode ][ $att ] ) ) {
		return $defaults[ $shortcode ][ $att ];
	}

	// generally not necessary to put defaults as false or '' here
	$global_defaults = array(
		'gp_button' => array(),
		'split_row' => array(
			'text' => 'right',
		),
	);

	if ( isset( $defaults[ $shortcode ][ $att ] ) ) {
		return $defaults[ $shortcode ][ $att ];
	}

	// global default default, if that makes any sense
	return $global_default;

}

/**
 * @param $defaults
 */
function gp_set_shortcode_defaults( $defaults ) {
	gp_set_global( 'shortcode_defaults', $defaults );
}

/**
 * @return bool|mixed
 */
function gp_get_shortcode_defaults() {
	return gp_get_global( 'shortcode_defaults', array() );
}

/**
 * @param $shortcode
 * @param $att
 */
function gp_reset_default_shortcode_att( $shortcode, $att ) {
	$defaults = gp_get_shortcode_defaults();
	if ( isset( $defaults[ $shortcode ][ $att ] ) ) {
		unset( $defaults[ $shortcode ][ $att ] );

		if ( empty( $defaults[ $shortcode ] ) ) {
			unset( $defaults[ $shortcode ] );
		}

		// update the array
		gp_set_shortcode_defaults( $defaults );
	}
}

/**
 * @param $shortcode
 * @param $att
 */
function gp_set_default_shortcode_att( $shortcode, $att, $default, $override = true ) {

	$defaults = gp_get_shortcode_defaults();

	if ( $override == true ) {
		$defaults[ $shortcode ][ $att ] = $default;
		gp_set_shortcode_defaults( $defaults );

		return;
	} else {
		if ( ! isset( $defaults[ $shortcode ][ $att ] ) ) {
			$defaults[ $shortcode ][ $att ] = $default;
			gp_set_shortcode_defaults( $defaults );

			return;
		}
	}

	// don't bother updating the global array of it wasn't changed
	return;
}

/**
 * currently just supports 2 levels of depth..
 *
 * @param $str
 * @param $sep1
 * @param $sep2
 */
function gp_string_to_nested_array( $str, $sep1, $sep2 ) {

	// assemble items from a string like... "52,53|54,56|34,56" to a nested array
	$ret = array();
	$a1 = gp_something_to_array( $str, $sep1 );

	if ( $a1 && is_array( $a1 ) ) {

		foreach ( $a1 as $str ) {

			$level_1 = array();
			$a2 = gp_something_to_array( $str, $sep2 );

			if ( $a2 && is_array( $a2 ) ) {
				foreach ( $a2 as $s2 ) {
					$level_1[] = $s2;
				}
			}

			$ret[] = $level_1;
		}
	}

	return $ret;
}


/**
 * The something here could be a comma separated list of IDs from a shortcode attribute
 * or maybe its already an array
 *
 * @param        $thing
 * @param string $sep
 *
 * @return array
 */
function gp_something_to_array( $thing, $sep = ',' ) {

	if ( is_array( $thing ) ) {
		return $thing;
	}

	if ( $sep ) {
		if ( strpos( $thing, $sep ) !== false ) {
			$arr = explode( $sep, $thing );
			$arr = array_map( 'trim', $arr );
			$arr = array_filter( $arr );
			return $arr;
		}
	}

	return array( $thing );
}

/**
 * Turns user input into a WP_Term object, or $default (which is probably false)
 *
 * NOTE: I don't think taxonomy is necessary, or does anything normally?? see get_term_by()
 *
 * @param $user_input
 * @param $taxonomy
 */
function gp_shortcode_wp_term( $user_input, $taxonomy = '', $default = false ) {

	if ( ! $user_input ) {
		return $default;
	}

	if ( gp_is_integer( $user_input ) ) {
		$wp_term = get_term_by( 'id', $user_input, $taxonomy );
		return $wp_term instanceof WP_Term ? $wp_term : $default;
	}

	$wp_term = get_term_by( 'slug', $user_input, $taxonomy );
	return $wp_term instanceof WP_Term ? $wp_term : $default;
}

// ******************************
//           Globals
// ******************************

/**
 * @param $key
 *
 * @return bool
 */
function gp_is_global_set( $key ) {
	return isset( $GLOBALS[ 'gpGlobals' ][ $key ] );
}

/**
 * I think its nice to have all of our theme globals stored in the same array, so we can
 * quickly print all when debugging.
 *
 * @param $key
 * @param $value
 */
function gp_set_global( $key, $value ) {
	$GLOBALS[ 'gpGlobals' ][ $key ] = $value;
}

/**
 * @param        $key
 * @param string $default
 *
 * @return bool|mixed
 */
function gp_get_global( $key, $default = '' ) {
	$gpGlobals = gp_if_set( $GLOBALS, 'gpGlobals', array() );

	return gp_if_set( $gpGlobals, $key, $default );
}

Class GP_Get_Next {

	public static $data = array();
	public static $undefined_string = 'undefined';

	public function __construct() {

	}

	/**
	 * Each time you call the function with the same $uid,
	 * it should return the next value in the array you pass in
	 *
	 * @param $uid
	 * @param $default_key
	 * @param $data
	 */
	public static function get( $uid, $default_key, $data ) {
		self::init_data( $uid, $data );

		return self::get_next( $uid, $default_key );
	}

	/**
	 * @param $uid
	 * @param $default_key
	 */
	protected static function get_next( $uid, $default_key ) {

		$data       = gp_if_set( self::$data, $uid, array() );
		$last_key   = gp_if_set( $data, 'last_key', self::$undefined_string );
		$array      = gp_if_set( $data, 'array', array() );
		$array_keys = array_keys( $array );

		// occurs on first call of ::get()
		if ( $last_key === self::$undefined_string ) {

			// make sure the default key is a key of the array
			$default_key = in_array( $default_key, $array_keys ) ? $default_key : gp_if_set( $array_keys, 0 );
			self::set_last_key( $uid, $default_key );

			return gp_if_set( $array, $default_key, false );
		}

		// subsequent calls of ::get()
		if ( $array ) {
			$do_next = false;
			foreach ( $array as $k => $v ) {
				if ( $do_next ) {
					self::set_last_key( $uid, $k );

					return $v;
				}
				if ( $k === $last_key ) {
					$do_next = true;
				}
			}
			// in case last element of array had the last key used
			if ( $do_next ) {
				$first_key = gp_if_set( $array_keys, 0 );
				self::set_last_key( $uid, $first_key );

				return gp_if_set( $array, $first_key );
			}
		}

		// in theory we shouldn't make it to here..
		return false;

	}

	protected static function set_last_key( $uid, $set ) {
		self::$data[ $uid ][ 'last_key' ] = $set;
	}

	/**
	 * @param $uid
	 * @param $data
	 */
	protected static function init_data( $uid, $data ) {
		if ( ! isset( self::$data[ $uid ] ) ) {
			self::$data[ $uid ][ 'last_key' ] = self::$undefined_string;
			self::$data[ $uid ][ 'array' ]    = $data;
		}
	}

}

// *******************************
//       User Input / Forms
// *******************************

/**
 * Use to validate that something isn't way too long
 * like millions of characters long, if we may write to a database somewhere or send
 * in an email.. If someones first name is 35000 characters long, this likely indicates spam.
 * for arrays, its not meant to be a perfectly accurate number (ie. array keys of 0 may contribute 1 to overall length)
 *
 * @param string|array|mixed $thing
 * @param bool               $add_keys
 *
 * @return int
 */
function gp_get_user_input_length( $thing, $add_keys = true ) {

	$ln = 0;

	// not checking this could result in infinite loop
	// careful not to only check is_bool, because is_bool( null ) might be false though im not totally sure
	if ( ! $thing ) {
		return 0;
	}

	if ( is_bool( $thing ) ) {
		return $thing ? 1 : 0;
	}
	// were going to call recursively so $thing might not be an array
	if ( is_string( $thing ) || is_int( $thing ) || is_float( $thing ) ) {
		return strlen( $thing );
	}
	// $thing = (array) $thing;
	if ( is_array( $thing ) || is_object( $thing ) ) {
		foreach ( $thing as $kk => $vv ) {
			if ( $add_keys ) {
				$ln = $ln + gp_get_user_input_length( $kk );
			}
			$ln = $ln + gp_get_user_input_length( $vv );
		}
	} else {
		// kind of a last resort fallback, not sure if we can end up here or not
		ob_start();
		var_dump( $thing );
		$string_thing = ob_get_clean();

		return strlen( $string_thing );
	}
	// the goal here is to never return 0 by default or accidentally
	// im fairly confident that that won't be the case
	return $ln;
}

/**
 * Make sure user input expected to be a string doesn't come through as an array
 */
function gp_make_string( $str ) {
	// if we have object or array, then notice will be printed
	if ( is_int( $str ) || is_float( $str ) ) {
		$str = (string) $str;
	}

	return is_string( $str ) ? $str : '';
}

/**
 * @param       $action
 * @param       $nonce_string
 * @param array $data
 *
 * @return bool|string Returns nonce verified - true/false
 */
function gp_nonce_action( $action, $nonce_string, $data = array() ) {
	$nonce_name  = 'gp_nonce-' . $nonce_string;
	$nonce_field = 'gp_nonce_field-' . $nonce_string;

	// REMEMBER TO ECHO
	if ( $action == 'print' ) {
		return wp_nonce_field( $nonce_name, $nonce_field, true, false );
	}

	if ( $action == 'verify' ) {
		if ( ! isset( $data[ $nonce_field ] ) || ! wp_verify_nonce( $data[ $nonce_field ], $nonce_name ) ) {
			return false; // validation failed
		} else {
			return true; // validation succesful
		}
	}

	return false;
}

/**
 * The main concern is script tags..
 *
 * @param $str
 */
function gp_strip_tags( $str, $tags = null ) {
	// in general I think its pretty safe to add to the default allowable tags here
	// as long as you don't add <script>
	$tags = $tags !== null ? $tags : '<p><span><br><a>';

	return strip_tags( $str, $tags );
}

/**
 * @param $prefix
 * @param $array
 */
function gp_array_keys_strip_prefix( $prefix, $array ) {
	$new_array = $array;
	if ( strlen( $prefix ) > 0 && is_array( $array ) ) {
		// need to start empty to preserve the order
		$new_array = array();
		foreach ( $array as $key => $value ) {
			$new_key               = gp_strip_prefix( $prefix, $key );
			$new_array[ $new_key ] = $value;
		}
	}

	return $new_array;
}

/**
 * @param $pre
 * @param $string
 */
function gp_strip_prefix( $prefix, $string ) {
	if ( substr( $string, 0, strlen( $prefix ) ) == $prefix ) {
		$string = substr( $string, strlen( $prefix ) );
	}

	return $string;
}

/**
 * @param $prefix
 * @param $array
 */
function gp_array_keys_add_prefix( $prefix, $array ) {

	$new_array = $array;

	if ( strlen( $prefix ) > 0 && is_array( $array ) ) {

		// need to start empty to preserve the order
		$new_array = array();

		foreach ( $array as $key => $value ) {
			$new_key               = $prefix . $key;
			$new_array[ $new_key ] = $value;
		}

	}

	return $new_array;
}

/**
 * Simple sanitation of user input
 *
 * @param $data
 *
 * @return string
 */
function gp_test_input( $data ) {
	$data = trim( $data );
	$data = stripslashes( $data );
	$data = htmlspecialchars( $data );

	return $data;
}

/**
 * @param $str
 *
 * @return mixed|string
 */
function trim_br_spaces( $str ) {
	$str = str_replace( '<br />', '', $str );
	$str = str_replace( '<br>', '', $str );
	$str = trim( $str );

	return $str;
}

/**
 * does the opposite of gp_strip_prefix, though its a bit easier to accomplish
 *
 * @param $prefix
 * @param $string
 */
function gp_add_prefix( $prefix, $string ) {
	return $prefix . $string;
}

// *******************************
//             Images
// *******************************

/**
 * @param $img_id
 *
 * @return string
 */
function gp_get_img_alt( $img_id ) {
	if ( get_post_type( $img_id ) == 'attachment' ) {
		$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

		return esc_attr( $alt );
	}

	return '';
}

/**
 * @param $img_id
 *
 * @return string
 */
function gp_get_img_title( $img_id ) {
	if ( get_post_type( $img_id ) == 'attachment' ) {
		$img   = get_post( $img_id );
		$title = get_post_field( 'post_title', $img );
		$title = sanitize_text_field( $title );

		return esc_attr( $title );
	}

	return '';
}

/**
 * @param $img_id
 *
 * @return string
 */
function gp_get_image_caption( $img_id ) {
	// I think its best to check post type here, as $img_id likely comes from shortcode attributes
	// and post_excerpt could contain sensitive information from private post types.
	if ( get_post_type( $img_id ) == 'attachment' ) {
		return get_post_field( 'post_excerpt', $img_id );
	}

	return '';
}

/**
 * Wrapper for wp_get_attachment_image_src..
 *
 * @param        $image_id
 * @param string $size
 *
 * @return bool
 */
function gp_get_img_url_from_post( $post_id, $size = 'large' ) {
	$image_id = get_post_thumbnail_id($post_id);
	$src = wp_get_attachment_image_src( $image_id, $size, false );
	$url = isset( $src[ 0 ] ) ? $src[ 0 ] : false;
	return $url;
}
/**
 * Wrapper for wp_get_attachment_image_src..
 *
 * @param        $image_id
 * @param string $size
 *
 * @return bool
 */
function gp_get_img_url( $image_id, $size = 'large' ) {
	$src = wp_get_attachment_image_src( $image_id, $size, false );
	$url = isset( $src[ 0 ] ) ? $src[ 0 ] : false;
	return $url;
}

/**
 * Wrapper for wp_get_attachment_url
 *
 * @param        $file_id
 * @param string $size
 *
 * @return bool
 */
function gp_get_file_url( $file_id ) {
	$src = wp_get_attachment_url( $file_id );
	return $src;
}

/**
 * @param $img_url
 */
function gp_get_img_style( $img_url ) {
	return $img_url ? 'background-image: url(\'' . $img_url . '\')' : '';
}

/**
 *
 */
function gp_get_placeholder_img() {
	return gp_image_path() . '/placeholder.jpg';
}

/**
 * Because i'm lazy
 */
function gp_image_path() {
	return get_template_directory_uri() . '/assets/images';
}

Class GP_Header_Font_Sizes_Generator {

	public function __construct() {

	}

	public static function br() {
		return '<br>';
	}

	/**
	 * @param $string
	 */
	public static function format_css( $string ) {
		$br       = '<br>';
		$triggers = array(
			'}',
			';',
			'{',
		);
		foreach ( $triggers as $tr ) {
			$string = str_replace( $tr, $tr . $br, $string );
		}

		return $string;
	}

	public static function css( $selector, $styles ) {
		$op = '';
		$op .= $selector . '{';

		if ( is_array( $styles ) ) {
			foreach ( $styles as $kk => $vv ) {
				$op .= $kk . ': ' . $vv . ';';
			}
		} else {
			$op .= $styles;
		}

		$op .= '}';

		return $op;
	}

	public static function get_less_var_name( $selector_str, $breakpoint_var_name = '' ) {
		$selector_str_clean = trim( $selector_str, '.' );
		$op                 = '';
		$op                 .= '@';
		if ( $breakpoint_var_name ) {
			$op .= $breakpoint_var_name;
		}
		$op .= '-' . $selector_str_clean;

		return $op;
	}

	/**
	 * @return string
	 */
	public function test() {

		// css string to print later
		$css = '';

		// css selector => where to look in $vars to get value, defaulting to the array key
		$template_vars = array(
			'.size-h1-xxl' => '',
			'.size-h1-xl'  => '',
			'.size-h1-lg'  => '',
			'h1'           => '',
			'h2'           => '',
			'h3'           => '',
			'h4'           => '',
			'h5'           => '',
			'h6'           => '',
			'.size-h1'     => 'h1',
			'.size-h2'     => 'h2',
			'.size-h3'     => 'h3',
			'.size-h4'     => 'h4',
			'.size-h5'     => 'h5',
			'.size-h6'     => 'h6',
		);

		$breakpoints = array(
			'fs-full-res' => 523234515293, // over 1920 means not in max-width media query
			//            'desktop-sm' => 1550,
			//            'tablet-lg' => 1024,
			'fs-768ish'   => 768,
			'fs-640ish'   => 640,
			'fs-550ish'   => 550,
			'fs-500ish'   => 500,
			'fs-430ish'   => 430,
			'fs-380ish'   => 380,
		);

		$vars = array();

		// reg
		$vars[ 'fs-full-res' ] = array(
			'.size-h1-xxl' => '91',
			'.size-h1-xl'  => '86',
			'.size-h1-lg'  => '74',
			'h1'           => '67',
			'h2'           => '59',
			'h3'           => '51',
			'h4'           => '42',
			'h5'           => '32',
			'h6'           => '22',
		);

		// 768
		$vars[ 'fs-768ish' ] = array(
			'.size-h1-xxl' => '85',
			'.size-h1-xl'  => '80',
			'.size-h1-lg'  => '70',
			'h1'           => '64',
			'h2'           => '54',
			'h3'           => '46',
			'h4'           => '37',
			'h5'           => '30',
			'h6'           => '22',
		);

		// 640
		$vars[ 'fs-640ish' ] = array(
			'.size-h1-xxl' => '75',
			'.size-h1-xl'  => '71',
			'.size-h1-lg'  => '65',
			'h1'           => '60',
			'h2'           => '50',
			'h3'           => '42',
			'h4'           => '35',
			'h5'           => '30',
			'h6'           => '22',
		);

		// 550
		$vars[ 'fs-550ish' ] = array(
			'.size-h1-xxl' => '65',
			'.size-h1-xl'  => '60',
			'.size-h1-lg'  => '55',
			'h1'           => '50',
			'h2'           => '45',
			'h3'           => '37',
			'h4'           => '32',
			'h5'           => '26',
			'h6'           => '20',
		);

		// 500
		$vars[ 'fs-500ish' ] = array(
			'.size-h1-xxl' => '58',
			'.size-h1-xl'  => '54',
			'.size-h1-lg'  => '51',
			'h1'           => '44',
			'h2'           => '39',
			'h3'           => '34',
			'h4'           => '28',
			'h5'           => '24',
			'h6'           => '20',
		);

		// 430
		$vars[ 'fs-430ish' ] = array(
			'.size-h1-xxl' => '53',
			'.size-h1-xl'  => '49',
			'.size-h1-lg'  => '46',
			'h1'           => '42',
			'h2'           => '36',
			'h3'           => '32',
			'h4'           => '28',
			'h5'           => '24',
			'h6'           => '20',
		);

		// 380
		$vars[ 'fs-380ish' ] = array(
			'.size-h1-xxl' => '49',
			'.size-h1-xl'  => '45',
			'.size-h1-lg'  => '41',
			'h1'           => '38',
			'h2'           => '34',
			'h3'           => '30',
			'h4'           => '28',
			'h5'           => '24',
			'h6'           => '20',
			//			'.size-h1-xxl' => '39',
			//			'.size-h1-xl'  => '35',
			//			'.size-h1-lg'  => '31',
			//			'h1'           => '29',
			//			'h2'           => '27',
			//			'h3'           => '25',
			//			'h4'           => '24',
			//			'h5'           => '20',
			//			'h6'           => '18',
		);

		$lovin_it = 0;

		// filter all the $vars so we that if we have for example h1 => 18,
		// it will also generate .size-h1 => 18, but based on our relationships
		// defined in $template_vars
		if ( $vars ) {
			foreach ( $vars as $var_index => $bp_var_data ) {
				if ( $bp_var_data && is_array( $bp_var_data ) ) {
					foreach ( $bp_var_data as $kk => $vv ) {
						if ( $template_vars ) {
							foreach ( $template_vars as $jj => $xx ) {
								$lovin_it ++;
								if ( $xx === $kk && ! isset( $bp_var_data[ $jj ] ) ) {
									$vars[ $var_index ][ $jj ] = $vv;
								}
							}
						}
					}
				}
			}
		}

		$css .= $lovin_it . self::br() . self::br();

		// breakpoint var names
		$bps = '';

		// apply mixins
		$apply = '';

		$var_string   = '';
		$vars_created = array();

		// Generate .less variables, ie. @tablet-lg: 1024px, or @h1-tablet-lg: 50px
		if ( $breakpoints ) {
			foreach ( $breakpoints as $breakpoint => $resolution ) {
				$data = gp_if_set( $vars, $breakpoint );

				if ( ! $data ) {
					continue;
				}

				$var_string .= self::br();
				$var_string .= '// ' . $resolution;
				$var_string .= self::br();


				foreach ( $template_vars as $selector => $use_var ) {

					if ( $use_var ) {
						$less_var_name = self::get_less_var_name( $use_var, $breakpoint );
					} else {
						$less_var_name = self::get_less_var_name( $selector, $breakpoint );
					}

					// dont repeat things for values that map to other values...
					// i couldnt be any less generic (I dont even know what it means)
					if ( in_array( $less_var_name, $vars_created ) ) {
						continue;
					} else {
						$vars_created[] = $less_var_name;
					}

					$value = gp_if_set( $data, $selector );

					if ( isset( $data[ $selector ] ) ) {
						$var_string .= $less_var_name . ': ' . $value . 'px;';
					} else {
						$var_string .= '//' . $less_var_name . ': ' . $value . 'px;';
					}
				}
			}
		}


		// Generate the mixin to apply all the other mixins.. .header-font-sizes-all-resolutions()
		if ( $breakpoints ) {

			$apply .= '.header-font-sizes-all-resolutions(){';
			$apply .= self::br();

			foreach ( $breakpoints as $name => $res ) {
				if ( $res >= 1920 ) {
					$apply .= '.header-font-sizes-' . $name . '();';
				} else {

					// breakpoint var names ie. @desktop-sm: 1440px
					$bps .= '@' . $name . ': ' . $res . 'px;';

					$apply .= '@media screen and (max-width: @' . $name . '){';
					$apply .= '.header-font-sizes-' . $name . '();';
					$apply .= '}';
				}
			}

			$apply .= '}'; // .header-font-sizes-all-resolutions
		}

		// Generate individual mixins, ie, .header-font-sizes-tablet();
		$mixins = '';
		if ( $breakpoints ) {

			foreach ( $breakpoints as $name => $res ) {

				$mixin      = '';
				$tags       = '';
				$ampersands = '';
				$data       = gp_if_set( $vars, $name );

				if ( ! $data ) {
					continue;
				}

				$mixin .= '.header-font-sizes-' . $name . '(){';

				foreach ( $template_vars as $selector => $use_var ) {

					$comment_out = ! isset( $data[ $selector ] );

					if ( $use_var ) {
						$less_var_name = self::get_less_var_name( $use_var, $name );
					} else {
						$less_var_name = self::get_less_var_name( $selector, $name );
					}

					if ( strpos( $selector, '.' ) !== false ) {

						$ampersands .= '&' . $selector . '{';
						if ( $comment_out && $res <= 1920 ) {
							$ampersands .= '    //font-size: ' . $less_var_name . ';';
						} else {
							$ampersands .= '    font-size: ' . $less_var_name . ';';
						}
						$ampersands .= '}';

					} else {

						$tags .= $selector . '{';
						if ( $comment_out ) {
							$tags .= '    //font-size: ' . $less_var_name . ';';
						} else {
							$tags .= '    font-size: ' . $less_var_name . ';';
						}
						$tags .= '}';
					}
				}

				$mixin .= $tags;
				$mixin .= 'h1, h2, h3, h4, h5, h6, p{';
				$mixin .= $ampersands;
				$mixin .= '}';
				$mixin .= '}'; // close mixin

				// mixins plural
				$mixins .= $mixin;
			}
		}

		$css .= self::br();
		$css .= '// breakpoints';
		$css .= self::br();
		$css .= $bps;
		$css .= self::br();
		$css .= '// variables';
		$css .= self::br();
		$css .= $var_string;
		$css .= self::br();
		$css .= '// mixins';
		$css .= self::br();
		$css .= $mixins;
		$css .= self::br();
		$css .= '// apply everything';
		$css .= self::br();
		$css .= $apply;

		$css = self::format_css( $css );
		echo '<pre>' . print_r( $css, true ) . '</pre>';

	}

}

/**
 *
 * Note: this is unfiltered user input, so be mindful of what you do with it..
 *
 * Also, we dont normally use this fn directly, instead use:
 *
 * gp_get_user_page_option()
 *
 * @param $post_id
 * @return array|string
 */
function gp_get_page_options( $post_id ) {

	$post_id = gp_make_post_id( $post_id );
	if ( ! $post_id ) {
		return array();
	}

	$key = 'unfiltered_user_options_' . $post_id;

	if ( gp_is_global_set( $key ) ) {
		$val = gp_get_global( $key );
		return (array) $val;
	}

	$str = get_post_meta( $post_id, 'page_options', true );
	$str = trim( $str );

	$arr = $str ? shortcode_parse_atts( $str ) : array();

	// set global for next time so we dont hit the database again
	gp_set_global( $key, $arr );

	return $arr;
}

/**
 * Note: this is unfiltered user input, so be mindful of what you do with it..
 *
 * @param $post_id
 * @param $option
 * @param null $default
 * @return bool|mixed
 */
function gp_get_page_option( $post_id, $option, $default = null ){

	$post_id = gp_make_post_id( $post_id );
	if ( ! $post_id ) {
		return $default;
	}

	$options = gp_get_page_options( $post_id );
	$val = gp_if_set( $options, $option, $default );
	return $val;
}

/**
 * Used when "$thing" might be a post object, or a post ID
 *
 * @param $thing
 * @param bool $default
 * @return array|bool|null|WP_Post
 */
function gp_make_post( $thing, $default = false ) {

	if ( $thing instanceof WP_Post ) {
		return $thing;
	}
	$post = get_post( $thing );
	return $post ? $post : $default;
}

/**
 * Used when "$thing" might be a post object, or a post ID
 *
 * @param $thing
 * @param bool $default
 * @return bool|int
 */
function gp_make_post_id( $thing, $default = false ) {

	if ( $thing instanceof WP_Post ) {
		return $thing->ID;
	}

	if ( gp_is_integer( $thing ) ) {
		return $thing;
	}

	return $default;
}

/**
 * Make sure we have a semi-colon at the end, unless string is empty
 *
 * @param $str
 */
function gp_fix_style( $str ) {
	if ( $str ) {
		$str = trim( $str );
		$str = trim( $str, ';' );
		$str = trim( $str );
		$str = $str . ';';
	}
	return $str;
}

/**
 * @param $post_id
 * @return string
 */
function gp_container_style( $post_id, $base_styles = '' ){

	$post_id = gp_make_post_id( $post_id );

	$base_styles = gp_fix_style( $base_styles );
	$st = $base_styles;

	if ( gp_is_global_set( 'container_style' ) ) {
		// we may set this up on a page template...
		$st .= gp_get_global( 'container_style', '' );
	} else {

		$width = gp_get_page_option( $post_id, 'width', null );
		$width = (int) $width;

		if ( $width ) {
			$st .= ' width: ' . $width . 'px; ';
			$st .= 'max-width: 100%; ';
			$st = trim( $st );
		}

	}

	return $st;
}

/**
 * @param $arr
 */
function gp_unique_str_arr( $arr, $pre = 'pre_' ) {

	$str = $pre;

	if ( is_array( $arr ) && $arr ) {
		$str .= serialize( $arr );
	}

	return md5( $str );
}

/**
 * @return string
 */
function gp_admin_ajax_url(){
	$admin_ajax = get_bloginfo( 'url' ) . '/wp-admin/admin-ajax.php';
	return $admin_ajax;
}

/**
 *
 */
function gp_custom_select( $args = array() ){

	$not_found = '--not-found--';
	$name = gp_if_set( $args, 'name', '' );
	$options = gp_if_set( $args, 'options', array() );
	$value = gp_if_set( $args, 'value', array() );
	$value_show = gp_if_set( $options, $value, $not_found );
	if ( $value_show === $not_found ) {
		$value_show = gp_if_set( array_values( $options ), 0, '' );
	}

	$icon_html = '<i class="fa fa-angle-down"></i>';
	$button_inner_html = '<span class="text-icon"><span class="text js-current-value">' . $value_show . '</span><span class="icon" role="presentation">' . $icon_html . '</span></span>';

	$op = '';
	$op .= '<div class="gp-radio-select">';
	$op .= '<div class="label"><button class="reset" type="button">' . $button_inner_html . '</button></div>';
	$op .= '<div class="list">';

	if ( $options && is_array( $options ) ) {
		$c = 0;
		foreach ( $options as $kk=>$vv ) {
			$c++;

			$item_id = $name . '_' . $c;

			// strict comparison better ?
			$checked = $kk == $value ? 'checked' : '';

			$op .= '<div class="list-item">';
			$op .= '<input id="' . $item_id . '" type="radio" name="' . $name . '" value="' . $kk . '" ' . $checked . '>';
			$op .= '<label for="' . $item_id . '">' . $vv . '</label>';
			$op .= '</div>';
		}
	}
	$op .= '</div>';
	$op .= '</div>';
	return $op;

}

/**
 * This can be 2nd parameter of get_field() when storing data to
 * taxonomy terms via advanced custom fields
 *
 * @param $term
 *
 * @return bool|string
 */
function gp_get_acf_term_id( $term ) {
	if ( $term instanceof WP_Term ) {
		return $term->taxonomy . '_' . $term->term_id;
	}
	return false;
}

/**
 * @param        $post_id
 * @param        $key
 * @param string $method
 * @param string $default - only used in some rare cases...
 *
 * @return int|mixed|string
 */
function gp_get_post_meta_sanitize( $post_id, $key, $method = 'basic', $default = '' ){

	$val = get_post_meta( $post_id, $key, true );

	$arr = explode( '|', $method );

	if ( $arr && is_array( $arr ) ) {
		foreach ( $arr as $case ) {
			switch ($case){
				case 'raw':
					// do nothing
				case 'trim':
					$val = trim( $val );
					break;
				case 'basic':
					$val = sanitize_text_field( $val );
					break;
				case 'safe_tags':
					$val = strip_tags( $val, '<p><br><a><div>');
					break;
				case 'int':
					$val = trim( $val );
					$val = $val ? (int) $val : $default;
					$val = (int) $val;
					break;
				case 'force_int':
					$val = trim( $val );
					$val = (int) $val;
					break;
				default:
					break;
			}
		}
	}

	return $val;
}


/**
 * Filters items, and then calls another function to print html, based on $args['output_method']
 *
 * @param       $items
 * @param array $args
 *
 * @return mixed
 */
function gp_admin_post_meta_fields( $items, $args = array() ) {

	// loop once to setup 'label_html' and 'input_html' (when not provided), then we'll loop again to generate html
	if ( $items ) {
		foreach ( $items as $ii => $item ) {

			$custom_html = gp_if_set( $item, 'custom_html' );
			if ( $custom_html ) {
				continue;
			}

			// we called this 'key' (as in meta key).. well check both 'name' and 'key' to mean the same
			$name = gp_if_set( $item, 'key', '' );
			$name = $name ? $name : gp_if_set( $item, 'name', '' );
			if ( ! $name ) {
				continue;
			}

			$label = gp_if_set( $item, 'label' );
			$value = gp_if_set( $item, 'value' );
			$id = gp_if_set( $item, 'id', $name );
			// $add_class = gp_if_set( $item, 'add_class' );

			$tt = ''; // tooltip html
			$tooltip = gp_if_set( $item, 'tooltip', '' );

			// setup default label html
			if ( ! isset( $item['label_html'] ) ) {

				if ( $tooltip ) {
					$tt .= '<span class="gp-tooltip"> ?</span>';
				}

				$items[$ii]['label_html'] = '<label for="' . $id . '" title="' . esc_attr( $tooltip ) . '">' . $label . $tt . '</label>';
			}

			// setup default input html
			if ( ! isset( $item['input_html'] ) ) {
				$items[$ii]['input_html'] = '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '">';
			}
		}
	}

	$output_method = gp_if_set( $args, 'output_method', 'table' );

	if ( $output_method === 'table' ) {
		return gp_admin_form_table( $items, $args );
	}

}

/**
 * @param $args
 */
function gp_admin_form_table( $items, $args ) {

	$op = '';

	// might not support anything other than two cols, but use split parameter if needed
	$two_cols = gp_if_set( $args, 'two_cols', true );
	$add_class = gp_if_set( $args, 'add_class', '' );

	$cls = array( 'form-table gp-admin-css gp-form-table' );
	if ( $add_class ) {
		$cls[] = $add_class;
	}

	$op .= '<table class="' . gp_parse_css_classes( $cls ) . '">';

	if ( $items ) {

		$cc = 0;
		$new_row = true;
		foreach ( $items as $ii=>$item ) {
			$cc++;

			$header = gp_if_set( $item, 'header' );
			$cls = gp_if_set( $item, 'add_class' );
			$split = gp_if_set( $item, 'split', 'default' );
			$custom_html = gp_if_set( $item, 'custom_html' );
			$label_html = gp_if_set( $item, 'label_html' );
			$input_html = gp_if_set( $item, 'input_html' );
			$tr_open = '<tr class="' . $cls . '">';
			$tr_close = '</tr>';

			if ( $header ) {
				$op .= $tr_open;
				$op .= '<th colspan="4"><h1>' . $header . '</h1></th>';
				$op .= $tr_close;
				$new_row = true; // next iteration starts a new row
				continue;
			}

			if ( $split === 1 || $split === '1' ) {
				$op .= $tr_open;
				if ( $custom_html ) {
					$op .= '<td colspan="4">' . $custom_html . '</td>';

				} else {
					$op .= '<th colspan="1">' . $label_html . '</th>';
					$op .= '<td colspan="3">' . $input_html . '</td>';
				}
				$op .= $tr_close;
				$new_row = true; // next iteration starts a new row
				continue;
			}

			// maybe open <tr>
			if ( $new_row ) {
				$op .= $tr_open;
			}

			$op .= '<th>' . $label_html . '</th>';
			$op .= '<td>' . $input_html . '</td>';

			// maybe close <tr>
			if ( ! $new_row ) {
				$op .= $tr_close;
			}

			// alternate the value for next time
			$new_row = ! $new_row;

		}
	}

	$op .= '</table>';
	return $op;
}
