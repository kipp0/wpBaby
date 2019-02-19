<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class GP_Flex_Items
 */
Class GP_Flex_Items {

    public $items;
    public $classes; // array of css classes that we'll put on the outer div
    public $flex_wrap_classes;
    public $opening_div_atts;
    public $pre;

    /**
     * GP_Flex_Items constructor.
     *
     * @param array $args | Array of arguments relating to the object as a whole, ie. 'add_class', 'title', etc.
     * @param array $item_defaults | Default arguments applied to each (array) $item
     */
    public function __construct( $args = array(), $item_defaults = array() ) {

        $defaults = array();

        $this->args = wp_parse_args( $args, $defaults );

        $this->items = array();

        $this->item_defaults = $item_defaults;
        $this->opening_div_atts = array();

        // will add to this later
        $this->classes = array( 'gp-flex-items' );
        $this->flex_wrap_classes = array();

        $add_class = gp_if_set( $args, 'add_class' );
        $this->add_css_class( $add_class ); // doesn't matter if we pass in empty value

        $title = gp_if_set( $args, 'title' );
        if ( trim( $title ) ) {
            $this->add_css_class( 'has-title' );
        } else {
            $this->add_css_class( 'no-title' );
        }

    }

    /**
     * Often we want to check this when using the class in a shortcode context
     * because we dont know if a user properly added items or not..
     */
    public function has_items(){
        return is_array( $this->items ) && count( $this->items ) > 0;
    }

    /**
     * @param $item
     */
    public function add_item( $item ) {
        if ( is_array( $item ) ) {

            // for example, we can set $this->item_defaults['image_size'] to 'medium'
            // to affect all items without having to repeat ourselves
            $item = wp_parse_args( $item, $this->item_defaults );
            $this->items[] = $item;
        }
    }

    /**
     * Really simple function for now. Atts will be overriden, rather than added to.
     *
     * @param $atts
     */
    public function add_opening_div_atts( $atts ) {
        $atts = (array) $atts;
        $this->opening_div_atts = wp_parse_args( $atts, $this->opening_div_atts );
    }

    /**
     * @param $css_class
     */
    public function add_css_class( $css_class ) {

        if ( is_array( $css_class ) ) {
            $this->classes = array_merge( $this->classes, $css_class );
            return true;
        }

        $css_class = trim( $css_class );

        if ( $css_class ) {
            $this->classes[] = $css_class;

            return true;
        }

        // class was not added
        return false;
    }

    /**
     *
     */
    public function get_class_string() {

        $cc = count( $this->items );

        $this->add_css_class( 'count-' . $cc );

        return $this->parse_css_classes( $this->classes );
    }

    /**
     * Might return nothing
     */
    public function title_html() {

        if ( isset( $this->args['title_html'] ) ) {
            return $this->args['title_html'];
        }

        $html = '';
        $title = gp_if_set( $this->args, 'title' );

        if ( $title ) {
            $title_tag = gp_if_set( $this->args, 'title_tag', 'h2' );
            $title_class = gp_if_set( $this->args, 'title_class', '' );
            $html .= '';
            $html .= '<div class="gpf-title">';
            $html .= '<' . $title_tag . ' class="' . $title_class . '">' . $title . '</' . $title_tag . '>';
            $html .= '</div>';
        }
        return $html;
    }

    /**
     *
     */
    protected function opening_div( $add_class = '' ) {

        // includes classes passed in, but we'll add some based on item count and/or item properties
        $classes = $this->get_class_string();

        if ( $add_class ) {
            $add_class = trim( $add_class );
            $classes .= ' ' . $add_class;
        }

        $atts = array(
            'class' => $classes
        );
        $atts = array_merge( $atts, $this->opening_div_atts );
        $div = gp_atts_to_container( 'div', $atts );
        return $div;
    }

    /**
     *
     */
    protected function closing_div() {
        return '</div>';
    }

    /**
     *
     */
    public function before_html() {
        $html = '';
        $html .= $this->opening_div();
        $html .= gp_if_set( $this->args, 'html_before', '' );
        $html .= $this->title_html();
        return $html;
    }

    /**
     *
     */
    protected function after_html() {
        $html = '';
        $html .= $this->closing_div();
        return $html;
    }

    /**
     * Probably being a little more than cautious with another 2 divs here
     * but I don't think were causing any harm
     */
    protected function flex_wrap_before() {
        $html = '';
        $classes = gp_parse_css_classes( $this->flex_wrap_classes );
        $classes = trim( $classes );
        $classes = $classes ? ' ' . $classes : '';
        $html .= '<div class="gpf-flex-outer">';
        $html .= '<div class="gpf-flex-inner' . $classes . '">';
        return $html;
    }

    /**
     *
     */
    protected function flex_wrap_after() {
        $html = '';
        $html .= '</div>'; // gpf-flex-inner
        $html .= '</div>'; // gpf-flex-outer

        return $html;
    }

    /**
     * Classes can be an array or string, though normally we expect an array
     *
     * We'll just trim, maybe remove duplicates, then implode to always return a string
     *
     * @param $classes
     */
    public function parse_css_classes( $classes ) {
        return gp_parse_css_classes( $classes );
    }

    /**
     *
     */
    public function get_single_item_add_class( $item ) {
        $classes = array();
        $img_id = gp_if_set( $item, 'img_id' );
        $classes[] = $img_id ? 'has-image' : 'no-image';

        $add_class = gp_if_set( $item, 'add_class' );

        // note that $add_class can actually be a string or an array, and parse_css_classes will handle it properly
        if ( $add_class ) {
            $classes[] = $add_class;
        }

        return $this->parse_css_classes( $classes );
    }

    /**
     * Open Divs
     */
    public function single_item_before( $item ) {
        $add_class = $this->get_single_item_add_class( $item );
        $html = '';
        $html .= '<div class="gpf-item-outer ' . $add_class . '">';
        $html .= '<div class="gpf-item-inner">';
        $html .= '';
        return $html;
    }

    /**
     * Close Divs
     */
    public function single_item_after( $item ) {
        $html = '';
        $html .= '</div>'; // gpf-item-inner
        $html .= '</div>'; // gpf-item-outer
        return $html;
    }

    /**
     * This is the most likely function that you'll want to change when extending the class
     *
     * @param $item
     */
    protected function single_item_inner( $item ) {

        if ( isset( $item['plain_html'] ) ) {
            return $item['plain_html'];
        }

        // this works fine if we have am image top, content bottom kind of format
        // for items where the image overlies the text, then one of the functions will likely have to be extended
        $html = '';
        $html .= $this->single_item_image_html( $item );
        $html .= $this->single_item_content_html( $item );
        return $html;

    }

    /**
     * Before, Inner, After
     */
    protected function single_item_html( $item ) {
        $html = '';
        $html .= $this->single_item_before( $item );
        $html .= $this->single_item_inner( $item );
        $html .= $this->single_item_after( $item );
        return $html;
    }

    /**
     * Displays a single item but without all the extra containers for a flex wrap.
     *
     * @param $item
     */
    public function single_item_no_flex( $item ) {
        $op = '';
        $op .= $this->opening_div( 'single-item');
        $op .= $this->single_item_html( $item );
        $op .= $this->closing_div();
        return $op;
    }

    /**
     * Allows you to pass in $item['img_url'], or $item['img_id']
     *
     * If passing in 'img_id', optionally specify 'image_size' on a per-item basis, or include image_size in $this->args
     *
     * @param $item
     */
    protected function get_item_img_url( $item ){

        $img_url = gp_if_set( $item, 'img_url' );

        if ( $img_url ) {
            return $img_url;
        }

        $img_id = gp_if_set( $item, 'img_id' );

        if ( $img_id ) {
            $image_size = gp_if_set( $item, 'image_size', 'medium' );
            return gp_img_url_from_shortcode_att( $img_id, $image_size );
        }

        return '';

    }

    /**
     * @param $item
     */
    protected function single_item_image_html( $item ){

        $html = '';

        $img_title = gp_if_set( $item, 'img_title' );
        $img_url = $this->get_item_img_url( $item );

        $html .= '<div class="image-top">';
        $html .= '<div class="image-wrap">';
        $html .= '<img title="' . $img_title . '" src="' . $img_url . '">';
        $html .= '</div>'; // image-wrap
        $html .= '</div>'; // image-top
        return $html;
    }

    /**
     * @param $item
     */
    public function single_item_content_html( $item ) {

        // might want to specify .entry-content here, so stuff inside content inherit styles
        // can specify on the item itself, or in the args for all items
        $content_add_class = gp_if_set( $item, 'content_add_class' );
        $content_add_class = $content_add_class ? $content_add_class : gp_if_set( $this->args, 'content_add_class' );

        $content = gp_if_set( $item,'content' );

        $html = '';
        $html .= '<div class="content-bottom">';
        $html .= '<div class="content-bottom-inner ' . $content_add_class . '">';
        $html .= $content;
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     *
     */
    public function get_html() {

        $html = '';

        // opening div, and possibly a 'before' or 'title' div
        $html .= $this->before_html();

        // get items
        if ( ! empty( $this->items ) ) {
            $html .= $this->flex_wrap_before();
            foreach ( $this->items as $item ) {
                $item = $this->filter_item( $item );
                $html .= $this->single_item_html( $item );
            }
            $html .= $this->flex_wrap_after();
        }

        // just closing div(s) most likely
        $html .= $this->after_html();

        return $html;

    }

    /**
     * Filter the item before printing to possibly modify a few things.
     *
     * ie. if img_id is set, but no img_url, set the img_url
     *
     * @param $item
     */
    protected function filter_item( $item ){

        // convert img_id to img_url
        if ( ! isset( $item['img_url'] ) ) {
            $img_id = gp_if_set( $item,'img_id' );
            if ( $img_id ) {
                $size = $this->get_item_property( $item, 'size' );
                $item['img_url'] = gp_get_img_url( $item['img_id'], $size );
            }
        }

        return $item;
    }

    /**
     * Checks the $item passed in, then optionally $this->item_defaults, and if both return nothing
     * returns the $default_default.
     *
     * For boolean values that may be stored in $this->item_defaults, you may just need to do your own logic.
     *
     * This function always attempts to find a non-false value.
     *
     * @param      $item
     * @param      $prop
     * @param bool $fallback
     * @param bool $default_default
     *
     * @return bool|mixed
     */
    protected function get_item_property( $item, $prop, $fallback = true, $default_default = false ) {
        $default = $fallback ? gp_if_set( $this->item_defaults, $prop, $default_default ) : $default_default;
        $val = gp_if_set( $item, $prop, $default );
        return $val;
    }

    /**
     * @param        $item
     * @param string $default
     *
     * @return bool|mixed
     */
    protected function get_image_size( $item, $default = 'large' ) {

        if ( isset( $item['size'] ) ) {
            return $item['size'];
        }

        if ( isset( $this->item_defaults['size'] ) ) {
            return $this->item_defaults['size'];
        }

        return false;

    }
}

Class GP_Flex_Blog extends GP_Flex_Items{

	/**
	 * GP_Flex_Listings constructor.
	 *
	 * @param array $args
	 * @param array $item_defaults
	 */
	public function __construct( $args = array(), $item_defaults = array() ){
		parent::__construct( $args, $item_defaults );
	}

	/**
	 *
	 */
	public function filter_item( $item ){
		// do nothing here instead of what parent class does
		return $item;
	}

	/**
	 * We'll define the html in a different class...
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function single_item_inner( $item ) {

		$image = gp_if_set( $item,'image' );
		$title = gp_if_set( $item, 'title' );
		$date = gp_if_set( $item, 'date' );
		$excerpt = gp_if_set( $item, 'excerpt');
		$url = gp_if_set( $item, 'url' );

		$op = '';
		$op .= '<div class="single-post">';
		$op .= '<a href="' . $url . '" title="' . $title . '" class="sp-image">';
		$op .= GP_Background_Image::get_simple( $image, 'square-lg' );
		$op .= '<span class="read-more">Read More</span>';
		$op .= '</a>';
		$op .= '<div class="sp-content">';
		$op .= '<div class="sp-content-inner">';
		$op .= '<h2 class="sp-title"><a href="' . $url . '">' . $title . '</a></h2>';
		$op .= '<p class="sp-date">' . $date . '</p>';

		$op .= '<div class="sp-excerpt">';
		$op .= wpautop( $excerpt, false );
		$op .= '</div>';

		$op .= '</div>'; // sp-content-inner
		$op .= '</div>'; // sp-content
		$op .= '</div>'; // single-post
		return $op;

	}

}


Class GP_Flex_Listings extends GP_Flex_Items{

	/**
	 * GP_Flex_Listings constructor.
	 *
	 * @param array $args
	 * @param array $item_defaults
	 */
	public function __construct( $args = array(), $item_defaults = array() ){
		parent::__construct( $args, $item_defaults );
	}

	/**
	 *
	 */
	public function filter_item( $item ){
		// do nothing here instead of what parent class does
		return $item;
	}

	/**
	 * We'll define the html in a different class...
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function single_item_inner( $item ) {
		$html = gp_if_set( $item, 'html' );
		return $html;
	}

}

/**
 * going to do a gallery of
 * Class GP_Flex_Reveal_Gallery
 */
Class GP_Flex_Reveal_Gallery extends GP_Flex_Items{

	/**
	 * GP_Flex_Listings constructor.
	 *
	 * @param array $args
	 * @param array $item_defaults
	 */
	public function __construct( $args = array(), $item_defaults = array() ){
		parent::__construct( $args, $item_defaults );
	}

	/**
	 *
	 */
	public function filter_item( $item ){
		// do nothing here instead of what parent class does
		return $item;
	}

	/**
	 * We'll define the html in a different class...
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function single_item_inner( $item ) {

		$op = '';
		$image_1 = gp_if_set( $item, 'image_1' );
		$image_2 = gp_if_set( $item, 'image_2' );

		// 2 "background" images although they are img tags and use some js effect thing..
		$op .= '<p class="cocoen">';
		$op .= '<img src="' . gp_get_img_url( $image_1, 'reg-md-alt' ) . '">';
		$op .= '<img src="' . gp_get_img_url( $image_2, 'reg-md-alt' ) . '">';
		$op .= '<span class="cocoen-drag my-cocoen-drag">';
		$op .= '<span class="cc-arrows">';
		$op .= '<i class="cc-left fa fa-caret-left" aria-hidden="true"></i>';
		$op .= '<i class="cc-right fa fa-caret-right" aria-hidden="true"></i>';
		$op .= '</span>'; // cc-arrows
		$op .= '</span>'; // cocoen-drag
		$op .= '</p>'; // cocoen

		return $op;
	}

}