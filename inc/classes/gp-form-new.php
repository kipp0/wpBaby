<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 2/23/2018
 * Time: 5:57 PM
 */

// stores errors and success messages on submission
require_once 'gp-error-handler.php';

// builds a form item from an array of attributes
require_once 'gp-form-item.php';

// builds an html form from an array of items
require_once 'gp-form-builder.php';

// sanitizes raw user input
require_once 'gp-form-sanitizer.php';

// stores default items
require_once 'gp-form-preset-item-getter.php';

// renders items
require_once 'gp-form-item-renderer.php';

/**
 * future: in the future, I want to update some of the component classes used below, but for now we may just
 * simplify some of their actions into a single function..
 *
 * Class GP_Form_1
 */
Class GP_Form_1 {

	private $builder; // html form item builder
	public static $wp_ajax_action = 'gp_form_1';
	public static $class = 'GP_Form_1';

	// we'll store user submitted data under each $item[$this->email_value_key]
	public $email_value_key = '_email_value';

	private $item_validator;

	// note that $items are also stored in $builder, but upon form submission
	// we more store a version of $items here.
	private $items;
	private $raw_data;
	private $clean_data;
	private $error_handler;

	/**
	 * GP_Form_1 constructor.
	 */
	public function __construct() {

		// may pass this object by reference into some classes below
		$this->error_handler = new GP_Error_Handler();

		$this->builder                = new GP_Form_Builder( $this->error_handler );
		$this->builder->item_renderer = new GP_Form_Item_Renderer();
		$this->builder->preset_item_getter = new GP_Form_Preset_Item_Getter();
		$this->sanitizer = new GP_Form_Sanitizer();
		$this->item_validator = new GP_Form_Item_Basic_Validator();
		$this->emailer = new GP_Form_Emailer( $this->error_handler );
		$this->build();
	}

	/**
	 * @return array
	 */
	public function get_email_args(){
		$args = array();
		$args['subject'] = 'Contact Form Submission';
		$args['top'] = '<p>You a new Contact Form submission.</p>';
		$args['to'] = get_option( 'admin_email' );
		return $args;
	}

	/**
	 * Assembles an array of $items. Each $item is also an array of data, which is used
	 * to print html for a form item, and also to do generic validation.
	 *
	 * This method is used to print the form, and to validate it. So it must always produce the exact same
	 * results, therefore be careful about making it rely on global state.
	 */
	public function build() {

		// Initialize some data
		$this->builder->set_id( 'contact-1' );
		$this->builder->add_class( 'contact-1' );
		$this->builder->set_method ( 'post' );
		$this->builder->set_action( $this->builder->ajax_url() );
		$this->builder->set_prefix( 'cf_' );
		$this->builder->set_nonce_string( 'gp-contact-1' );
		$this->builder->set_wp_ajax_action( self::$wp_ajax_action );

		// Add the items
		$this->builder->add_preset_item( 'first_name', array(
			'required' => true,
		) );
		$this->builder->add_preset_item( 'last_name', array(
			'required' => true,
		) );
		$this->builder->add_preset_item( 'email', array(
			'required' => true,
		) );
		$this->builder->add_preset_item( 'phone', array(
			'required' => false,
		) );

		$this->builder->add_item( array(
			'name' => 'listing_id',
			'required' => false,
			'type' => 'select',
			'label' => 'Schedule a Viewing',
			'options' => $this->get_schedule_viewing_options( array( '' => 'Choose Listing' ) ),
			'value' => isset( $_GET['listing_id'] ) ? (int) $_GET['listing_id'] : '',
		));

		$this->builder->add_preset_item( 'message', array() );
		$this->builder->add_submit_btn( 'Submit' );
	}

	/**
	 * Pass in some options to be prepended if you wish (ie. array( '' => 'Select a thing...' )
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_schedule_viewing_options( $options = array() ){

		$options = (array) $options;

		$listings = GP_Listings::get_listings_for_sale();

		if ( $listings && is_array( $listings ) ) {
			foreach ( $listings as $listing_id ) {
				$ll = new GP_Listing( $listing_id );
				$options[$listing_id] = $ll->get_address();
			}
		}
		return $options;
	}

	/**
	 * Get form html
	 *
	 * @param $raw_data
	 *
	 */
	public function render() {
		// hide from client temporarily
		return $this->builder->default_render();
	}

	/**
	 * Sanitizes all data passed in via $_POST. Will over-sanitize data for password fields
	 * or textarea's. Therefore, always use a mix of $clean_data, and $raw_data + your own specific sanitization
	 * methods.
	 */
	public function sanitize(){
		return $this->sanitizer->run( $this->raw_data );
	}

	/**
	 * $raw_data is probably $_POST.
	 *
	 * @param $raw_data
	 */
	public function submit( $raw_data ) {

		// check ajax referer requires the key to check for in $_REQUEST (as 2nd param), rather then the value
		$nonce_input_name = $this->builder->get_prefix() . 'nonce';
		if ( ! check_ajax_referer( $this->builder->get_nonce_string(), $nonce_input_name ) ){
			// check ajax referer runs wp_die() by default so we won't get to here..
			return;
		}

		if ( $this->error_handler->has_errors() ){
			return;
		}

		$this->items = $this->builder->fetch_items( true );
		$this->raw_data = $raw_data;
		$this->clean_data = $this->sanitize();

		if ( $this->error_handler->has_errors() ){
			return;
		}

		$this->validate();

		if ( $this->error_handler->has_errors() ){
			return;
		}

		// compile email data and send
		$this->email();
	}

	/**
	 *
	 */
	public function check_nonce(){

	}

	/**
	 * Void function. Use $this->error_handler->add_error() if an error is found.
	 */
	public function validate(){

		$this->do_general_required_field_validation();

		if ( $this->error_handler->has_errors() ){
			return;
		}

		$this->do_general_max_length_validation();

		if ( $this->error_handler->has_errors() ){
			return;
		}

		$email_item = $this->get_item_by_name( 'email' );

		if ( $email_item && is_array( $email_item ) ) {
			$email = gp_if_set( $this->clean_data, 'email', '' );
			if ( ! $this->validate_email( $email ) ) {
				$this->error_handler->add_error( 'Please use a valid email address.' );
			}
		}
	}

	/**
	 * IMPORTANT: Validation and sanitation must be done before you get here..
	 *
	 * ie. Send an email.
	 */
	public function email(){

		$args = $this->get_email_args();

		$subject = gp_if_set( $args, 'subject' );
		$top = gp_if_set( $args,'top' );
		$to = gp_if_set( $args, 'to' );

		$message = '';
		$message .= $top;
		$message .= $this->email_main_body_html( true );

		$fname = gp_if_set( $this->clean_data, 'first_name' );
		$lname = gp_if_set( $this->clean_data, 'last_name' );
		$from_name = $fname . ' ' . $lname;
		$from_email = gp_if_set( $this->clean_data, 'email' );

		$reply_to_name = $from_name;
		$reply_to_email = gp_if_set( $this->clean_data, 'email' );;

		if ( isset ( $args[ 'headers' ] ) ) {
			$headers = $args[ 'headers' ];
		} else {
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$headers[] = 'From: "' . $from_name . '" <' . $from_email . '>';
			$headers[] = 'Reply-to: "' . $reply_to_name . '" <' . $reply_to_email . '>';
		}


		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		$send = wp_mail( $to, $subject, $message, $headers );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		if ( $send ) {
			$this->error_handler->set_success( true );
			$this->error_handler->add_success_message( 'Thank you for your email.' );
		} else {
			$this->error_handler->add_error( 'Error sending email.' );
		}
	}

	/**
	 * @return string
	 */
	public function get_content_type(){
		return 'text/html';
	}

	/**
	 *
	 */
	public function email_main_body_html( $process_input = false ){

		$content = '';

		$referer = gp_if_set( $this->clean_data, '_wp_http_referer' );

		$content .= $this->basic_email_row( 'Date', gp_date_now_wp_timezone() );
		$content .= $this->basic_email_row( 'URL', $referer );

		if ( $process_input ) {
			// store the values we'll email in the items..
			$this->add_email_values_to_items();
		}

		if ( $this->items && is_array( $this->items ) ) {
			foreach ( $this->items as $item ) {

				$email_label = gp_first_set_and_not_empty( $item, array(
					'email_name',
					'label',
					'placeholder',
					'name',
				), '??');

				$type = gp_if_set( $item, 'type' );
				$email_value = gp_if_set( $item, $this->email_value_key );

				if ( $type === 'textarea' ) {
					$content .= $this->textarea_email_row( $email_label, $email_value );
					continue;
				}

				$content .= $this->basic_email_row( $email_label, $email_value );
			}
		}

		return $content;
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return string
	 */
	public function textarea_email_row( $key, $value ) {
		$op = '';
		$op .= '<p><strong>' . $key . ': </strong></p>';
		$op .= '<div>' . $value . '</div>';
		return $op;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function basic_email_row( $key, $value ) {
		$value = strip_tags( $value );
		$op = '';
		$op .= '<p><strong>' . $key . ': </strong>' . $value . '</p>';
		return $op;
	}

	/**
	 * Takes an item as array, and adds an index including the email value, based on user submitted input.
	 *
	 * We attempt to use a sanitized version of the user input, but should always verify this for yourself.
	 *
	 * @param $item
	 *
	 * @return mixed
	 */
	protected function add_email_value_to_item( $item ) {

		if ( ! is_array( $item ) ) {
			return $item;
		}

		// text/checkbox/textarea etc.
		$type = gp_if_set( $item, 'type' );
		$name = gp_if_set( $item, 'name' );

		if ( ! $name ) {
			return $item;
		}

		// sanitized user submitted value
		$value = gp_if_set( $this->clean_data, $name );

		// un-sanitized user submitted value (we need this for textarea)
		$raw_value = gp_if_set( $this->raw_data, $name );

		// this is a default value but be careful, $value could be an array right now
		// below, we'll check for that..
		$email_value = $value;

		// ie. write "No" instead of "" in the email..
		if ( ! $raw_value || ! $value ) {
			$email_value = gp_if_set( $item, 'default_email_value', '' );
		}

		// Radio / Checkbox
		// note that radio likely will not come in as array value, mainly this is for checkboxes
		if ( is_array( $value ) && ( $type == 'checkbox' || $type == 'radio' ) ) {

			$str_val = '';
			$sub_items = gp_if_set( $item, 'items' );

			foreach ( $value as $kk => $vv ) {

				// displayed value is what was sent to user when the form got printed, not necessarily
				// what the user submitted
				$displayed_value = gp_if_set( $sub_items, $vv );
				$displayed_value = gp_make_string( $displayed_value );
				$displayed_value = trim( $displayed_value );
				if ( $displayed_value ) {
					$str_val .= $displayed_value . ', ';
				}
			}

			$str_val = trim( $str_val );
			$str_val = trim( $str_val, ',' );
			$str_val = trim( $str_val );

			$email_value = $str_val;
		}

		// Select
		if ( $type == 'select' ) {
			$options = gp_if_set( $item, 'options' );
			// using $raw_value here because were checking array key based on this, not actually sending it in email..
			$email_value = gp_if_set( $options, $raw_value, '' );
		}

		// Textarea
		if ( $type == 'textarea' ) {
			// grab un-sanitized first, then sanitize
			$email_value = $this->get_textarea_email_content( $raw_value );
		}

		// does nothing if we already have a string. But may help prevent array to string conversion errors
		$email_value = gp_make_string( $email_value );

		// add index to the item..
		$item[$this->email_value_key] = $email_value;
		return $item;
	}

	/**
	 * Modifies the $this->items array to include user submitted values in each item, taking into account
	 */
	protected function add_email_values_to_items(){
		if ( $this->items && is_array( $this->items ) ) {
			foreach ( $this->items as $key=>$value ) {
				$this->items[$key] = $this->add_email_value_to_item( $value );
			}
		}
	}

	/**
	 * Loops through items, checks the type attribute, finds (cleaned) value submitted by user
	 * Example return value:
	 * array( array( 'name' => 'first_name', 'value' => 'Doug' ) )
	 * The 'col' is the form name of the item... we're going to leave this as is for now.
	 * You should manually convert it to a proper form name later, ie 'First Name'. Its more flexible this way,
	 * because you have a chance to intercept the value being emailed..
	 *
	 * @param $items
	 * @param $clean_data
	 * @param $raw_data
	 *
	 * @return array|string
	 */
	protected function get_email_data( $items, $clean_data, $raw_data ) {

		$rows = array();
		$html = '';

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {

				// these are <div class="form_item">'s, but not for actual form fields
				// could be line breaks, submit buttons, additional titles or html tags etc.
				if ( isset( $items[ 'just_html' ] ) ) {
					continue;
				}

				$index = gp_if_set( $item, 'name' );
				$type = gp_if_set( $item, 'type' );

				if ( $index ) {

					// value submitted by user in $_POST (but cleaned)
					$value = gp_if_set( $clean_data, $index );

					if ( ! $value ) {
						// ie for checkboxes or radio buttons etc. not checked, or not passed in
						// we can print 'No' or 'False' in the email if needed
						$default_value = gp_if_set( $item, 'default_email_value', '' );
						$data[$index] = $default_value;
						continue;
					}

					// not expecting type=radio to be stored as an array, but if it is that's fine
					if ( is_array( $value ) && ( $type == 'checkbox' || $type == 'radio' ) ) {

						$str_val = '';
						$sub_items = gp_if_set( $item, 'items' );

						foreach ( $value as $kk => $vv ) {

							// displayed value is what was sent to user when the form got printed, not necessarily
							// what the user submitted
							$displayed_value = gp_if_set( $sub_items, $vv );
							$displayed_value = gp_make_string( $displayed_value );
							$displayed_value = trim( $displayed_value );
							if ( $displayed_value ) {
								$str_val .= $displayed_value . ', ';
							}
						}

						$str_val = trim( $str_val );
						$str_val = trim( $str_val, ',' );
						$str_val = trim( $str_val );
						$data[ $index ] = $str_val;
						continue;

					}

					if ( $type == 'select' ) {
						$options = gp_if_set( $item, 'options' );
						$displayed_value = gp_if_set( $options, $value, '' );
						$data[ $index ] = $displayed_value;
						continue;
					}

					// in case of text area
					if ( $type == 'textarea' ) {
						// grab un-sanitized first, then sanitize
						$raw_value = gp_if_set( $raw_data, $index );
						$raw_value = $this->sanitize_textarea( $raw_value ); // strip tags
						$data[ $index ] = $this->get_textarea_email_content( $raw_value );
						continue;
					}

					// likely this won't be triggered since our checkbox check is above this
					if ( is_array( $value ) ) {
						$value = implode( ', ', $value );
						$value = trim( $value );
					}

					// last second fallback to make sure were only storing strings.
					// in rare cases this could prevent array to string conversion errors
					$value = gp_make_string( $value );

					$rows[] = array( $index, $value );
					// other input types
					// $data[ $index ] = $value;
				}
			}
		}

		return $rows;
	}

	/**
	 * make sure you sanitize first
	 *
	 * @param $input
	 */
	protected function get_textarea_email_content( $input ) {
		$html = '';
		$html .= '<br>==========================================<br>';
		$html .= $input;
		$html .= '<br>==========================================<br>';
		return $html;
	}

	/**
	 * Date, http referer, and that's probably all we need
	 */
	protected function get_top_details() {

		$rows = array();

		$rows[] = array(
			'label' => 'Date',
			'value' => gp_date_now_wp_timezone(),
		);

		$rows[] = array(
			'label' => 'URL',
			'value' => gp_if_set( $this->clean_data, '_wp_http_referer' ),
		);

		return $rows;
	}

	/**
	 * @param $input
	 *
	 * @return string
	 */
	public function sanitize_textarea( $input ) {
		return sanitize_textarea_field( $input );
	}

	/**
	 * Use a public static method, so we can hook this onto 'wp_ajax...' actions
	 *
	 * @param $raw_data
	 */
	public static function wp_ajax() {
		// note: new static() here is like new This_Class(). supported in php 5.3+
		// new self() can (/does) behave differently when using a child class
		$self = new static();
		$self->submit( $_POST );
		$response = $self->error_handler->get_ajax_response();
		echo json_encode( $response );
		exit;
	}

	/**
	 *
	 */
	public static function wp_init(){
		add_action('wp_ajax_' . self::$wp_ajax_action, array( self::$class, 'wp_ajax' ));
		add_action('wp_ajax_nopriv_' . self::$wp_ajax_action, array( self::$class, 'wp_ajax' ));
	}

	/**
	 *
	 */
	protected function validate_email( $email ) {
		if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Does required form validation based on the $form_items array used to generate the html
	 */
	protected function do_general_required_field_validation() {

		if ( $this->items && is_array( $this->items ) ) {
			foreach ( $this->items as $item ) {

				$required = gp_if_set( $item, 'required' );

				// ie. name attribute of <input>
				$form_name = gp_if_set( $item, 'name' );

				if ( $required && $form_name ) {

					// the "name" displayed on front-end
					$validation_name = $this->get_validation_name( $item );

					// from $_POST
					$submitted_value = gp_if_set( $this->clean_data, $form_name );

					if ( ! $submitted_value ) {

						$msg = gp_if_set( $item, 'required_error', '' );

						if ( ! $msg ) {
							$msg = 'The <strong>' . $validation_name . '</strong> field is required.';
						}

						$this->error_handler->add_error( $msg );
					}
				}
			}
		}
	}

	/**
	 * Does max length validation based on the $form_items array used to generate the html
	 * Note: we shouldn't use max_length for form items that are expected to have array values submitted
	 */
	protected function do_general_max_length_validation() {

		if ( $this->items && is_array( $this->items ) ) {
			foreach ( $this->items as $item ) {

				// ie. name attribute of <input>
				$form_name = gp_if_set( $item, 'name', '' );

				$max_length = gp_if_set( $item, 'max_length', false );
				$max_length = intval( $max_length );

				// from $_POST
				$submitted_value = gp_if_set( $this->clean_data, $form_name );
				// gives a length for strings or arrays..
				$user_length = gp_get_user_input_length( $submitted_value, true );

				if ( $max_length && $user_length > $max_length ) {
					// the "name" displayed on front-end
					$validation_name = $this->get_validation_name( $item );
					$msg = 'The <strong>' . $validation_name . '</strong> field is longer than the allowed character limit (' . $max_length . ').';
					$this->error_handler->add_error( $msg );
				}
			}
		}
	}

	/**
	 * @param $key
	 */
	protected function get_item_by_name( $name ) {
		if ( $this->items && is_array( $this->items ) ) {
			foreach ( $this->items as $item ) {
				$this_name = gp_if_set( $item, 'name' );
				if ( $this_name === $name ) {
					return $item;
				}
			}
		}
		return false;
	}

	/**
	 * The name for a form field that gets shown to a user when there is an error with the field.
	 */
	protected function get_validation_name( $item, $default = '' ) {
		$check = array(
			'validation_name',
			'placeholder',
			'label',
			'name',
		);
		return gp_first_set_and_not_empty( $item, $check, $default );
	}
}

GP_Form_1::wp_init();

/**
 *
 * @param      $atts
 * @param null $content
 *
 * @return string
 */
function gp_contact_form_shortcode( $atts = array(), $content = null ) {

//	$op = '';
//	$df = array(
//		'form' => 1, // only 1 form currently
//	);
//	$df = gp_filter_default_shortcode_atts( '', $df );
//	$aa = shortcode_atts( $df, $atts );
	// $form = gp_if_set( $aa, 'form' );

	$form = 1;

	if ( $form === 1 ) {
		$cf1 = new GP_Form_1();
		return $cf1->render();
	}

	return '';
}

/**
 * todo: may hook up this class one day
 *
 * Class GP_Form_Emailer
 */
Class GP_Form_Emailer{

	/** @var  GP_Error_Handler */
	private $error_handler;

	/**
	 * GP_Form_Emailer constructor.
	 */
	public function __construct( $error_handler ){
		$this->error_handler = $error_handler;
	}

	/**
	 *
	 */
	public function run( $items, $clean_data, $raw_data ){
		$this->error_handler->add_error( 'Email not hooked up yet' );
	}

}

/**
 * not in use
 *
 * Class GP_Form_Generic_Validator
 */
Class GP_Form_Generic_Validator{

	private $item_validator;

	public function __construct( $items, $raw_data ) {

	}

	public function run(){

	}
}

/**
 * Not in use...
 *
 * Handles generic validation methods for many different form items, including whether or not
 *
 * Class GP_Form_Item_Basic_Validator
 */
Class GP_Form_Item_Basic_Validator{

	public function __construct(){
	}

	/**
	 * @param $item
	 */
	public function validate( $item ) {
	}
}