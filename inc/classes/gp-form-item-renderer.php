<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 3/12/2018
 * Time: 5:49 PM
 */

/**
 * Class GP_Form_Item_Renderer
 */
Class GP_Form_Item_Renderer {

	// ie... (possible future classes)
	// private $textarea_renderer;
	// private $input_type_text_renderer;

	/**
	 * future: (see below)
	 * In the future, maybe each 'GP_Form_Item_Renderer' can have its own
	 * renderer class for each type of form object ('text', 'password', etc.)
	 * then to alter how we render form items, we can extend GP_Form_Item_Renderer and define
	 * a different subset of individual form item renderers that are properties of this class
	 * for now however, we'll use the old GP_Form_Item class to handle all rendering of form items
	 * this just means that all items from all forms will be rendered in the same way, which is in most cases
	 * not a problem at all, just not ideal that we can't very easily change one form at a time.
	 * In case its not completely clear, this also means that this entire class is just a simple placeholder
	 * and not necessary in the current state of things, but I want to include it to indicate the intended way
	 * to improve things in the future.
	 * GP_Form_Item_Renderer constructor.
	 */
	public function __construct() {

	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function render( $item, $prefix = '' ) {
		$obj = new GP_Form_item( $item );
		if ( $prefix ) {
			$obj->add_prefix( $prefix );
		}
		return $obj->get_html();
	}

}