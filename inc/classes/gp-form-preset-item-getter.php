<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 3/12/2018
 * Time: 5:47 PM
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class GP_Form_Preset_Item_Getter
 */
Class GP_Form_Preset_Item_Getter {

	protected $preset_items;

	public function __construct() {
		$this->preset_items = array();
		$this->build();
	}

	/**
	 * Build an array of possible preset items
	 * I'd sort of prefer not to build this each time we construct (as we are doing now), but
	 * its a bit hard to see a better way to do this at the moment..
	 *
	 * By pre-building the array of items, it's easier to write the get() method so that
	 * this class can be extended without having to re-define the get() method
	 *
	 * So the main point here, is don't put 10000 items below, and don't run any
	 * expensive code other than simply generating a hardcoded array. Avoid database queries etc.
	 */
	public function build() {

		$this->add_item( 'full_name', array(
			'name'       => 'full_name',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Full Name',
			'split'      => 2,
			'max_length' => 128,
		) );

		$this->add_item( 'name', array(
			'name'       => 'name',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Name',
			'split'      => 2,
			'max_length' => 128,
		) );

		$this->add_item( 'subject', array(
			'name'       => 'subject',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Subject',
			'split'      => 2,
			'max_length' => 356,
		) );

		$this->add_item( 'first_name', array(
			'name'       => 'first_name',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'First Name',
			'split'      => 2,
			'max_length' => 128,
		) );

		$this->add_item( 'last_name', array(
			'name'       => 'last_name',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Last Name',
			'split'      => 2,
			'max_length' => 128,
		) );

		$this->add_item( 'message', array(
			'name'       => 'message',
			'type'       => 'textarea',
			'label'      => 'Message',
			'required'   => true,
			'split'      => 1,
			'attributes' => array(
				'rows' => 6,
			),
			'max_length' => 8000,
		) );

		$this->add_item( 'email', array(
			'name'       => 'email',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Email',
			'split'      => 2,
			'max_length' => 128,
		) );

		$this->add_item( 'phone', array(
			'name'       => 'phone',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Phone',
			'split'      => 2,
			'max_length' => 32,
		) );

		$this->add_item( 'street', array(
			'name'       => 'street',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Street',
			'split'      => 2,
			'max_length' => 256,
		) );

		$this->add_item( 'city', array(
			'name'       => 'city',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'City',
			'split'      => 2,
			'max_length' => 64,
		) );

		$this->add_item( 'province', array(
			'name'       => 'province',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Province',
			'split'      => 2,
			'max_length' => 64,
		) );

		$this->add_item( 'postal', array(
			'name'       => 'postal',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Postal',
			'split'      => 2,
			'max_length' => 10,
		) );

		$this->add_item( 'country', array(
			'name'       => 'country',
			'type'       => 'text',
			'required'   => true,
			'label'      => 'Country',
			'split'      => 2,
			'max_length' => 32,
		) );

		$this->add_item( 'company', array(
			'name'       => 'company',
			'type'       => 'text',
			'label'      => 'Company Name',
			'required'   => true,
			'split'      => 2,
			'max_length' => 64,
		) );
	}

	public function add_item( $key, $item ) {
		if ( is_array( $item ) ) {
			$this->preset_items[ $key ] = $item;
		}
	}

	/**
	 * @param $preset_name
	 * @param $overrides
	 */
	public function get( $preset_name, $overrides ) {
		$item = gp_if_set( $this->preset_items, $preset_name );
		if ( $item && is_array( $item ) ) {
			if ( $overrides && is_array( $overrides ) ) {
				$item = wp_parse_args( $overrides, $item );
			}
			return $item;
		}
		return array();
	}

}
