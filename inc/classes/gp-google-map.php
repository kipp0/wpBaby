<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 1/24/2018
 * Time: 2:19 PM
 */

/**
 * Created by PhpStorm.
 * User: dan
 * Date: 7/14/2017
 * Time: 9:45 AM
 */
Class GP_Google_Map {

	protected $args;
	protected $markers = array();
	protected static $default_icon;

	/**
	 * GP_Google_Map constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() )
	{
		$defaults = array(
			'icon' => '',
		);
		$this->args = wp_parse_args( $args, $defaults );
	}

	/**
	 * @return string
	 */
	public function get_default_icon(){
		if ( self::$default_icon !== null ) {
			return self::$default_icon;
		}
		self::$default_icon = get_template_directory_uri() . '/assets/images/map-marker.png';
		return self::$default_icon;
	}

	/**
	 * @param      $lat
	 * @param      $long
	 * @param      $html
	 * @param bool $icon
	 */
	public function add_marker( $args = array() ) {

		$icon = gp_if_set( $args, 'icon', 'none' );
		$icon = $icon === 'none' ? self::get_default_icon() : $icon;

		$marker = array(
			'lat' => gp_if_set( $args, 'lat' ),
			'long' => gp_if_set( $args, 'long' ),
			'html' => gp_if_set( $args, 'html' ),
			'icon' => $icon,
		);

		$marker = wp_parse_args( $marker, $args );
		$this->markers[] = $marker;
	}

	/**
	 *
	 */
	public function get_html( $add_class = '' )
	{
		$op = '';

		// map markers..
		$data = array(
			'markers' => $this->markers,
		);

		$atts = array();
		$atts['id'] = 'gmap-embed';
		if ( $add_class ) {
			$atts['class'] = $add_class;
		}
		$atts['data-options'] = $data;
		$op .= gp_atts_to_container( 'div', $atts, true );
		return $op;
	}

}