<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 2/23/2018
 * Time: 6:07 PM
 */

/**
 * todo: this code is a mess, but cleaning it up will require re-writing other parts of code... Works fine... but messy.
 *
 * Class GP_Form_Item
 */
Class GP_Form_Item{

	public $html_before;
	public $html_after;

	private $args; // might access args from within different methods

	// stored in the atts_array
	//    public $id;
	//    public $name;
	//    public $placeholder;

	public $prefix; // these should probably be the same for all forms items in the same form
	public $class; // adds item-' . $class to the form item (in $html_before)
	public $add_class; // adds a new class to the form item (in $html_before)
	public $required;
	public $disabled;
	public $label;
	public $options;
	public $type;
	public $before_label;
	public $before_input;
	public $req_html;
	public $additional_atts; // pass in an array
	public $atts_array; // combination of id, name, placeholder, and additional atts for the input or select tag
	public $all_atts_string; // string of atts computed from the array immediately before printing

	public $plain_html; // put something here and this is returned on get_html(), everything else is ignored

	/**
	 * GP_Form_Item constructor.
	 *
	 * @param $args
	 */
	public function __construct( $args ){

		// my apologies for the rest of the function... its not very pretty.
		// I would like to clean it up before I use it again.
		$this->args = $args;

		$plain_html = isset( $args['plain_html'] ) ? $args['plain_html'] : false;

		if ( $plain_html ) {
			$this->plain_html = $plain_html;
			return;
		}

		$defaults = array(
			'type' => 'text',
			'required' => false,
			'req_html' => '*', // can set this empty and just put a * in the label if you want
			'before_label' => '', // could put an empty div to show ajax errors
			'before_input' => '', // could put an empty div to show ajax errors
		);

		$args = wp_parse_args( $args, $defaults );

		// get the args
		$id = isset( $args['id'] ) ? $args['id'] : false;
		$class = isset( $args['class'] ) ? $args['class'] : false; // class for the form item (wrapper) not the input/select tag
		$add_class = isset( $args['add_class'] ) ? $args['add_class'] : false; // class for the form item (wrapper) not the input/select tag
		$name = isset( $args['name'] ) ? $args['name'] : false;
		$required = isset( $args['required'] ) ? $args['required'] : false;
		$disabled = isset( $args['disabled'] ) && $args['disabled'] ? true : false;
		$label = isset( $args['label'] ) ? $args['label'] : false;
		$options = isset( $args['options'] ) ? $args['options'] : array();
		$type = isset( $args['type'] ) ? $args['type'] : false;
		$before_label = isset( $args['before_label'] ) ? $args['before_label'] : false;
		$before_input = isset( $args['before_input'] ) ? $args['before_input'] : false;
		$req_html = isset( $args['req_html'] ) ? $args['req_html'] : false;
		// additional attributes or possibly override other attributes
		$additional_atts = isset( $args['attributes'] ) ? $args['attributes'] : array();
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : false;
		$cb_items = isset( $args['items'] ) ? $args['items'] : false; // used for checkboxes and radio buttons

		// setup some defaults

		if ( ! $id ) {
			$id = $name;
			$id = str_replace( '[]', '', $id );
		}
		if ( ! $class ) {
			$class = $id;
		}

		// set the args
		$this->class = $class; // class for the form item (wrapper) not the input/select tag

		if ( isset( $this->args['split'] ) && $this->args['split'] == 2 ) {
			$this->class = $this->class . ' split-2';
			$this->class = trim( $this->class);
		}

		$this->add_class = $add_class;
		// these are stored in the atts_array
		// $this->id = $id;
		// $this->name = $name;
		// $this->placeholder = $placeholder;
		$this->required = $required;
		$this->disabled = $disabled; // now a boolean
		$this->label = $label;
		$this->options = $options;
		$this->type = $type;
		$this->req_html = $req_html;
		$this->additional_atts = $additional_atts; // these are any additional attributes (ie. data attributes)
		$this->before_input = $before_input;
		$this->before_label = $before_label;

		// change these (if needed) after instantiating the object but before printing the html
		$this->html_before = '<div class="form-item item-' . $this->class. ' type-' . $type . ' ' . $this->add_class . '"><div class="form-item-inner">';
		$this->html_after = '</div></div>';

		// do this last i suppose
		$atts_array = array(
			'id' => $id,
			'name' => $name,
			'placeholder' => $placeholder,
		);

		// for type checkbox, and type radio
		if ( $cb_items ) {
			$atts_array['items'] = $cb_items;
		}

		if ( $disabled ) {
			$atts_array['disabled'] = 'disabled';
		}

		// handle values different depending on form type
		if ( isset( $this->args['value'] ) ) {

			$value = $this->args['value'];

			$basic_types = array(
				'text',
				'number',
				'password',
			);

			if ( in_array( $this->type, $basic_types ) ) {
				$atts_array['value'] = $value;
			} else {

				if ( $this->type == 'select' ) {
					// we need to take care of this later when printing the options
					// when using country region select, we can do this by passing
					// in the correct attributes
				}

			}

		}

		// this will also overwrite the atts above
		if ( $additional_atts && is_array( $additional_atts ) ) {
			foreach ( $additional_atts as $name=>$val ) {
				$atts_array[$name] = $val;
			}
		}

		// this is the final attributes array used in the input or select tag
		// some other data is repeated but its most likely ignored
		// modify this if you need to
		$this->atts_array = $atts_array;

	}

	/**
	 * Optionally overwrite if you dont want to use the default
	 *
	 * You will likely want to use the class/name/id so you can target the form item specifically in the wrapper div
	 *
	 * @param $html
	 */
	public function set_html_before( $html ){
		$this->html_before = $html;
	}

	/**
	 * Optionally overwrite if you dont want to use the default
	 *
	 * @param $html
	 */
	public function set_html_after( $html ){
		$this->html_after = $html;
	}

	/**
	 * Adds a prefix to the input name and id (also the label for attribute)
	 *
	 * @param $prefix
	 */
	/**
	 * Sub items refers to names of radio buttons and checkboxes
	 *
	 * @param string $prefix
	 * @param bool   $to_sub_items
	 */
	public function add_prefix( $prefix = '', $to_sub_items = true ){

		if ( ! $prefix ){
			return;
		}

		// add a prefix to which attributes ?
		$add_to = array(
			'id',
			'name',
		);

		foreach ( $add_to as $array_key ) {

			$current = gp_if_set( $this->atts_array, $array_key );

			if ( $current ) {
				$this->atts_array[$array_key] = $prefix . $current;
			}
		}

		//        if ( $to_sub_items ) {
		//
		//            $sub_items = gp_if_set( $this->args, 'items' );
		//
		//            if ( ! empty( $sub_items ) ) {
		//
		//                foreach ( $sub_items as $sub_item_form_name => $sub_item_form_label ) {
		//
		//                    // careful not to add prefix to
		//                    if ( trim( $sub_item_form_name ) ) {
		//
		//                    }
		//
		//                }
		//
		//            }
		//
		//        }

	}

	/**
	 * Use a separate method other than the construct to get the html
	 *
	 * This should allow you to make any customizable changes right before printing
	 */
	public function get_html(){

		if ( isset( $this->plain_html ) && $this->plain_html ) {
			return $this->plain_html;
		}

		// we want to wait until printing the html to generate the atts string
		// this allows for any last second overrides
		$this->setup_attributes_string();

		//        echo '<pre>' . print_r( $this, true ) . '</pre>';
		//        return;

		if ( $this->type == 'text' ) {
			return $this->get_input();
		}

		if ( $this->type == 'password' ) {
			// same as input type text for now, except for the type attribute
			return $this->get_input();
		}

		if ( $this->type == 'number' ) {
			return $this->get_input();
		}

		if ( $this->type == 'select' ) {
			// different function than for input tags
			return $this->get_select();
		}

		if ( $this->type == 'checkbox' ) {
			// same as input type text
			return $this->get_checkbox_group();
		}

		if ( $this->type == 'textarea' ) {
			// same as input type text
			return $this->get_textarea();
		}

		// Note: if you want to use radio button groups or have a wrapper around your buttons you might have to use custom html
		// i'm not going to support passing in an array of options for radio buttons with the same name
		// there are too many things to consider and its not easy to do it properly.
		// make use of the attributes index of the $args passed into the constructor to customize in any way you like
		// and use css to make them all appear as one item
		if ( $this->type == 'radio' ) {
			return $this->get_radio_button_group();
		}

	}

	public function get_label(){
		$html = '';

		// possibly an empty div for an ajax response
		$html .= $this->before_label;

		// use the atts array to get id
		$id = isset( $this->atts_array['id'] ) ? $this->atts_array['id'] : false;

		// span might not be necessary but it surely won't hurt
		$required_str = $this->required ? '<span class="req">*</span>' : false;

		// print the label
		if ( $this->label ) {
			$html .= '<label for="' . $id . '">' . $this->label . '' . $required_str . '</label>';
		}
		return $html;
	}

	/**
	 * Run this after instantiating (or on printing) to allow for last second modifications
	 *
	 * Put pretty much all attributes (including name, id, placeholder, data attributes) into one string.
	 *
	 * The only attribute not in here would be type since that's not always used (ie. in <select>)
	 */
	public function setup_attributes_string(){

		$str = '';

		// start with a space and end with no space
		if ( $this->atts_array && is_array( $this->atts_array ) ) {
			foreach ( $this->atts_array as $name=>$val ) {
				// note: we did something a little stupid here and accidentally added ['items'] for checkboxes
				// to the atts array, not knowing that the atts array is meant to print inside the html tag
				// since we already grab the items from that location, instead of removing it, lets just skip any array values here
				// so this array that were looping through should only contain html tag attributes like id, class, type, data-atts etc.
				if ( ! is_array( $val ) ) {
					$str .= ' ' . $name . '="' . $val . '"';
				}
			}
		}

		$this->all_atts_string = $str;
	}

	/**
	 * still some room for improvement in case some attributes passed in
	 * dont behave as expected here.
	 */
	public function get_radio_button_group(){
		$html = '';

		$html .= $this->html_before;

		// possibly an empty div for an ajax response
		// this usually goes after label and before input, but doing it a bit differently for checkboxes
		$html .= $this->before_input;

		$html .= $this->get_label();

		$html .= '<div class="cb-wrap">';

		// atts_array is the modified $args passed in to the construct (it may have a prefix)
		$items = gp_if_set( $this->atts_array, 'items' );
		$name = gp_if_set( $this->atts_array, 'name' );

		if ( ! empty( $items ) ) {
			$c = 0;
			foreach ( $items as $input_value=>$text ) {
				$id = $name . '_' . $c;
				// put input first so we can css input::checked + label and style the label accordingly
				// might put the checked indicator inside the label and hide the input
				$html .= '<div class="cb-item">';
				$html .= '<input type="radio" id="' . $id . '" name="' . $name . '" value="' . $input_value . '">';
				$html .= '<label for="' . $id . '">' . $text . '</label>';
				$html .= '</div>';
				$c++;
			}
		}

		// html required attribute
		// $required = $this->required ? ' required' : '';
		$html .= '</div>'; // cb-wrap

		$disclaimer = gp_if_set( $this->args, 'disclaimer' );

		if ( $disclaimer ) {
			$html .= '<p class="info">' . $disclaimer . '</p>';
		}

		// close divs maybe
		$html .= $this->html_after;
		return $html;
	}

	/**
	 * still some room for improvement in case some attributes passed in
	 * dont behave as expected here.
	 */
	public function get_checkbox_group(){
		$html = '';

		$html .= $this->html_before;

		// possibly an empty div for an ajax response
		// this usually goes after label and before input, but doing it a bit differently for checkboxes
		$html .= $this->before_input;

		$html .= $this->get_label();

		$html .= '<div class="cb-wrap">';

		// atts_array is the modified $args passed in to the construct (it may have a prefix)
		$items = gp_if_set( $this->atts_array, 'items' );
		$name = gp_if_set( $this->atts_array, 'name' );
		$id = gp_if_set( $this->atts_array, 'id', $name );

		// store checkboxes are array values regardless of how many checkboxes are present
		if ( strpos( $name, '[]') === false ) {
			$name = $name . '[]';
		}

		if ( ! empty( $items ) ) {

			$c = 0;

			foreach ( $items as $input_value=>$text ) {

				// make sure IDs printed on the page are unique
				$this_id = $id . '_' . $c;

				// put input first so we can css input::checked + label and style the label accordingly
				// might put the checked indicator inside the label and hide the input
				$html .= '<div class="cb-item">';
				$html .= '<input type="checkbox" id="' . $this_id . '" name="' . $name . '" value="' . $input_value . '">';
				$html .= '<label for="' . $this_id . '">' . $text . '</label>';
				$html .= '</div>';

				$c++;

			}

		}

		// html required attribute
		// $required = $this->required ? ' required' : '';

		$html .= '</div>'; // cb-wrap

		$disclaimer = gp_if_set( $this->args, 'disclaimer' );

		if ( $disclaimer ) {
			$html .= '<p class="info">' . $disclaimer . '</p>';
		}

		// close divs maybe
		$html .= $this->html_after;

		return $html;
	}

	/**
	 *
	 */
	//    public function get_checkbox(){
	//
	//        $html = '';
	//
	//        $html .= $this->html_before;
	//
	//        // possibly an empty div for an ajax response
	//        // this usually goes after label and before input, but doing it a bit differently for checkboxes
	//        $html .= $this->before_input;
	//
	//        $html .= '<div class="cb-wrap">';
	//
	//        // html required attribute
	//        $required = $this->required ? ' required' : '';
	//
	//        // print the input tag
	//        $html .= '<input type="' . $this->type . '" ' . trim( $this->all_atts_string ) . ' ' . $required . '>';
	//
	//        $html .= $this->get_label();
	//
	//        $html .= '</div>'; // cb-wrap
	//
	//        // close divs maybe
	//        $html .= $this->html_after;
	//
	//        return $html;
	//
	//    }

	/**
	 * Output for an <input type="text">
	 */
	public function get_input(){
		$html = '';

		$html .= $this->html_before;
		$html .= $this->get_label();

		// possibly an empty div for an ajax response
		$html .= $this->before_input;

		// html required attribute
		$required = $this->required ? ' required' : '';

		// print the input tag
		$html .= '<input type="' . $this->type . '" ' . trim( $this->all_atts_string ) . ' ' . $required . '>';

		// close divs maybe
		$html .= $this->html_after;

		return $html;
	}

	/**
	 *
	 */
	public function get_textarea(){

		$html = '';

		$html .= $this->html_before;
		$html .= $this->get_label();

		// possibly an empty div for an ajax response
		$html .= $this->before_input;

		// html required attribute
		$required = $this->required ? ' required' : '';

		// print the input tag
		$html .= '<textarea ' . trim( $this->all_atts_string ) . ' ' . $required . '></textarea>';

		// close divs maybe
		$html .= $this->html_after;

		return $html;

	}

	/**
	 * Output for a <select> item
	 */
	public function get_select(){

		$html = '';

		$html .= $this->html_before;
		$html .= $this->get_label();

		// possibly an empty div for an ajax response
		$html .= $this->before_input;

		// required?
		$required = $this->required ? ' required' : '';

		// print the input
		$html .= '<select ' . trim( $this->all_atts_string ) . '' . $required . '>';

		if ( $this->options && is_array( $this->options ) ) {
			foreach ( $this->options as $val=>$text ) {

				$selected = '';

				if ( isset( $this->args['value'] ) ) {

					// amazingly, the following line does not work.
					// ( $selected = $val == $this->args['value'] ? 'selected="selected"' : ''; )
					// even though, if you var_dump( $val ) you can get string 'card',
					// and var_dump( $this->args['value'] ) gives you int 0
					// and then in the comparison:
					// string 'card' == int 0. Yes thats right, 'card' IS EQUAL TO F&@#&^@# ZERO with non-strict comparison
					// we get a return value of true. just amazing PHP good work.
					// anyways, strval solves the issue or at least it appears to solve the issue
					// but then again, 'card' is equal to 0, so what do I know...
					$selected = strval( $val ) == strval( $this->args['value'] ) ? 'selected="selected"' : '';
				}

				$html .= '<option value="' . $val . '" ' . $selected . '>' . $text . '</option>';
			}
		}

		$html .= '</select>';


		$html .= $this->html_after;

		return $html;

	}

	/**
	 * Output for an <input type="text">
	 */
	public function get_input_radio(){
		return '';
	}


}

