<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 3/12/2018
 * Time: 5:43 PM
 */

/**
 * This class is capable of building an array of form items, and rendering html from those items.
 * You can also fetch those items, and use a different class or set of functions to handle the form submission.
 * Items may have parameters that specify what type of sanitization or validation to do when the form is submitted.
 *
 * Class GP_Form_Builder
 */
Class GP_Form_Builder {

	/** @var  GP_Error_Handler */
	private $error_handler;

	/** @var  GP_Form_Item_Renderer */
	public $item_renderer;

	/** @var  GP_Form_Preset_Item_Getter */
	public $preset_item_getter;

	private $id;
	private $classes;
	private $action;
	private $method;
	private $prefix;
	private $nonce_string;
	private $items;
	private $wp_ajax_action;

	public function __construct( $error_handler ) {

		$this->error_handler = $error_handler;
		$this->error_handler->add_hidden_message( 'test' );

		// in the future, we may have different versions of GP_Form_Item_Renderer()
		$this->id            = '';
		$this->classes = array( 'gp-ajax' );
		$this->action        = '';
		$this->method = 'post'; // can override with set method
		$this->prefix = '';
		$this->nonce_string = 'better-to-override-this';
		$this->items         = array();
	}

	/**
	 * @param array $item
	 */
	public function add_item( $item ) {
		if ( $item && is_array( $item ) ) {
			$this->items[] = $item;
		}
	}

	/**
	 * @param string $html
	 */
	public function add_html( $html ) {
		if ( $html ) {
			$this->add_item( array(
				'plain_html' => $html,
			));
		}
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function add_submit_btn( $text = 'Submit' ) {
		$op = '';
		$op .= '<div class="form-item item-submit">';
		$op .= '<div class="button-1 size-large align-center">';
		$op .= '<button type="submit" class="reset">' . $text . '</span>';
		$op .= '</div>'; // button-1
		$op .= '</div>'; // form-item.item-submit
		$this->add_html( $op );
	}

	/**
	 * @param $preset_name
	 * @param $overrides
	 */
	public function add_preset_item( $preset_name, $overrides ) {
		// 'get' the item from presets, then 'add' the item to our items here
		$item = $this->preset_item_getter->get( $preset_name, $overrides );
		$this->add_item( $item );
	}

	/**
	 * ie. <form id="">
	 *
	 * @param $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * ie. <form class="generic-class your-own-class">
	 *
	 * @param $cls
	 */
	public function add_class( $cls ) {
		$this->classes[] = $cls;
	}

	/**
	 * ie. 'post', 'get', probably 'post'
	 *
	 * @param $method
	 */
	public function set_method( $method ) {
		$this->method = $method;
	}

	/**
	 * ie. <form action="">
	 *
	 * @param $action
	 */
	public function set_action( $action ) {
		$this->action = $action;
	}

	/**
	 * @param $pre
	 */
	public function set_prefix( $pre ) {
		$this->prefix = $pre;
	}

	/**
	 * @return string
	 */
	public function get_prefix(){
		return $this->prefix ? $this->prefix : '';
	}

	/**
	 * @param $str
	 */
	public function set_nonce_string( $str ) {
		$this->nonce_string = $str;
	}

	public function get_nonce_string(){
		return $this->nonce_string;
	}

	/**
	 * @param $str
	 */
	public function set_wp_ajax_action( $str ) {
		$this->wp_ajax_action = $str;
	}

	/**
	 * Some "items" are just generic html. Others are expected to be present
	 * upon form submission. $filter param controls whether to include these.
	 *
	 * @param bool $filter
	 */
	public function fetch_items( $filter = true ) {
		if ( ! $filter ) {
			return $this->items;
		}
		$filtered = array();
		if ( $this->items && is_array( $this->items ) ) {
			foreach ( $this->items as $index => $data ) {
				if ( ! isset( $data[ 'plain_html' ] ) ) {
					$filtered[ $index ] = $data;
				}
			}
		}

		return $filtered;
	}

	/**
	 * Default attributes that go into the opening <form> tag,
	 * ie, id, class, action
	 */
	public function fetch_default_atts() {
		// defaults
		$df = array();

		if ( $this->id ) {
			$df[ 'id' ] = $this->id;
		}

		if ( $this->method ) {
			$df[ 'method' ] = $this->method;
		}

		if ( $this->action ) {
			$df[ 'action' ] = $this->action;
		}

		if ( $this->classes ) {
			$df[ 'class' ] = gp_parse_css_classes( $this->classes );
		}

		if ( $this->action ) {
			$df[ 'id' ] = $this->id;
		}

		return $df;
	}

	/**
	 * Opens a <form> tag
	 *
	 * @param array $more_atts
	 */
	public function form_html_before( $more_atts = array() ) {

		$atts = $this->fetch_default_atts();

		if ( $more_atts && is_array( $more_atts ) ) {
			// allows specific atts passed in to override defaults
			$atts = wp_parse_args( $more_atts, $atts );
		}

		return gp_atts_to_container( 'form', $atts, false, '' );
	}

	/**
	 * @return string
	 */
	public function items_html_before() {
		$op = '';
		$op .= '<div class="form-inner">';
		$op .= '<div class="form-flex">';

		return $op;
	}

	/**
	 * @return string
	 */
	public function item_html_after() {
		$op = '';
		$op .= '</div>';
		$op .= '</div>';
		$op .= '<div class="ajax-response"></div>';

		return $op;
	}

	/**
	 * @return string
	 */
	public function form_html_after() {
		return '</form>';
	}

	/**
	 * Renders all form items
	 */
	public function render_items() {
		$op    = '';
		$items = $this->fetch_items( false );
		if ( $items && is_array( $items ) ) {
			foreach ( $items as $item ) {
				$op .= $this->item_renderer->render( $item );
			}
		}
		return $op;
	}

	/**
	 * You need to manually set this if you want to use it..
	 *
	 * @return string
	 */
	public function ajax_url(){
		return get_bloginfo( 'url' ) . '/wp-admin/admin-ajax.php';
	}

	/**
	 * Renders all html from <form> to </form>.*
	 * If it doesn't provide what you require, use some or none of the methods within.
	 *
	 * @param array $form_atts
	 *
	 * @return string
	 */
	public function default_render( $form_atts = array() ) {
		$op = '';
		$op .= $this->form_html_before( $form_atts );
		$op .= $this->items_html_before();
		$op .= $this->render_items();
		$op .= $this->item_html_after();
		$op .= $this->hidden_form_fields();
		$op .= $this->form_html_after();
		return $op;
	}

	/**
	 * Nonce, honeypot, action
	 */
	public function hidden_form_fields(){
		$op = '';
		$op .= $this->wp_ajax_action_hidden_input();
		$op .= $this->honeypot_field();
		$op .= $this->nonce_field();
		return $op;
	}

	/**
	 *
	 */
	public function wp_ajax_action_hidden_input(){
		if ( $this->wp_ajax_action ) {
			return '<input type="hidden" name="action" value="' . $this->wp_ajax_action . '">';
		}
		return '';
	}

	/**
	 * Not sure its even beneficial to use this. If its hidden, bots probably wont touch it. If its tabbable,
	 * then some users may fill it in accidentally.
	 *
	 * @return string
	 */
	public function honeypot_field(){
		$op = '';
		$op .= '<input type="hidden" class="hpot-field" name="' . $this->prefix . 'hpot_validate" aria-label="This field must be left empty." value="">';
		return $op;
	}

	/**
	 * @return string
	 */
	public function nonce_field(){
		return wp_nonce_field( $this->nonce_string, $this->prefix . 'nonce', true, false );
	}
}