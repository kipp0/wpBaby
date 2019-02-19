<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 3/12/2018
 * Time: 5:47 PM
 */

/**
 * Class GP_Form_Sanitizer
 */
Class GP_Form_Sanitizer{

	public $max_arr_depth;

	/**
	 * GP_Form_Sanitizer constructor.
	 */
	public function __construct(){
		$this->max_arr_depth = 12;
	}

	/**
	 * Provides safety measures for all data passed in via $_POST or $_REQUEST.
	 *
	 * We'll use an "over-sanitize when not sure" approach.
	 *
	 * If your input gets over-sanitized, then reference $raw_data, and do your own sanitation..
	 *
	 * For example, DO NOT rely on this for password fields, textarea's, or when you expect html content.
	 *
	 * @param     $raw_data
	 * @param int $arr_depth
	 *
	 * @return array
	 */
	public function run( $raw_data, $arr_depth = 0 ){

		$clean = array();

		// allow $skip values to either have or not have the form prefix
		if ( $raw_data && is_array( $raw_data ) ) {
			foreach ( $raw_data as $key=>$value ) {
				$clean_key = $this->sanitize_key( $key );
				if ( is_int( $value ) || is_float( $value ) || is_string( $value ) || is_bool( $value ) ) {
					$clean[$clean_key] = $this->sanitize_string( $value );
				} else if ( is_array( $value ) && $arr_depth < $this->max_arr_depth) {
					$arr_depth++;
					// Recursive function call
					$clean[$key] = $this->run( $value, $arr_depth );
				}
			}
		}

		return $clean;
	}

	/**
	 * @param $key
	 */
	public function sanitize_key( $key ) {
		// if you are using weird characters in your array keys.. this cause some things not to work..
		$key = trim( $key );
		return htmlspecialchars( $key );
	}

	/**
	 * Sanitizes a string, or singular data, ie. float, bool, int, string, but not array or object
	 * checking the type of param should be done beforehand..
	 *
	 * @param $str
	 */
	public function sanitize_string( $str ) {
		return sanitize_text_field( $str );
	}

}
