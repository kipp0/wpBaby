<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 12/9/2017
 * Time: 4:48 PM
 */

/**
 * Class GP_Background_Image
 */
//Class GP_Background_Image{
//
//    protected $args;
//    protected $sources;
//
//    /**
//     * Pass in one of the following sets of $args
//     *
//     * 1. sources (array)
//     * 2. img_id, sizes (array)
//     * 3. img_id, size
//     * 4. img_url
//     *
//     * GP_Background_Image constructor.
//     *
//     * @param $args
//     */
//    public function __construct( $args ){
//
//        // just for fun
//        //        if ( isset( $args['img_id'] ) ) {
//        //            $args['img_id'] = 55;
//        //        }
//
//        if ( gp_is_integer( $args ) ) {
//            $args = array(
//                'img_id' => $args,
//            );
//        }
//        if ( is_string( $args ) ) {
//            $args = array(
//                'img_url' => $args,
//            );
//        }
//        $this->args = $args;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function get_sources(){
//        $this->setup_sources();
//        return $this->sources;
//    }
//
//    /**
//     * @return bool
//     */
//    protected function setup_sources(){
//        $args = $this->args;
//        $sources = isset( $args['sources'] ) ? $args['sources'] : null;
//
//        if ( $sources && is_array( $sources ) ) {
//            $this->sources = $sources;
//            return true;
//        }
//
//        $img_url = isset( $args['img_url'] ) ? $args['img_url'] : null;
//
//        // not null, not false, not empty...
//        if ( $img_url ) {
//            $this->sources = array(
//                $img_url
//            );
//            return true;
//        }
//
//        $img_id = isset( $args['img_id'] ) ? $args['img_id'] : null;
//        $size = isset( $args['size'] ) ? $args['size'] : null;
//        $sizes = isset( $args['sizes'] ) ? $args['sizes'] : null;
//        if ( ! is_array( $sizes ) ) {
//            $sizes = (array) $sizes;
//        }
//
//        if ( $img_id && gp_is_integer( $img_id ) ) {
//
//            if ( $sizes && is_array( $sizes ) && count( $sizes ) === 1 ) {
//                $size_values = array_values( $sizes );
//                $size = $size_values[0];
//            }
//
//            if ( $size ) {
//                $this->sources = array(
//                    gp_get_img_url( $img_id, $size )
//                );
//                return true;
//            }
//
//            // the main purpose of this class is to maybe get to here
//            // and setup an array of sources based on screen resolution
//            // then use javascript to load the images
//            if ( $sizes && is_array( $sizes ) && count( $sizes ) > 1 ) {
//                foreach ( $sizes as $key=>$value ) {
//                    $this->sources[$key] = gp_get_img_url( $img_id, $value );
//                }
//                return true;
//            }
//        }
//
//        // a valid combination of args was not passed in, and we failed to setup $this->sources
//        return false;
//    }
//
//    /**
//     *  if the default args below need adjustments, then just call $this->get_sources() yourself, then self::html_from_sources
//     *
//     * @return string
//     */
//    public function get_html(){
//        // if the sources get setup properly
//        if ( $this->setup_sources() ) {
//            return self::html_from_sources( $this->sources, $this->args );
//        }
//        return '';
//    }
//
//    /**
//     * @param $args
//     *
//     * @return string
//     */
//    public static function get( $args ) {
//
//        // todo: this is temporary
//        if ( isset( $args['img_id'] ) && $args['img_id'] == 'rand' ) {
//            $args['img_id'] = gp_random_image_id();
//        }
//
//        $bg = new GP_Background_Image( $args );
//        return $bg->get_html();
//    }
//
//    /**
//     * Just easier to pass in the first 2 params as they are usually the only ones used,
//     * so we avoid making arrays for an argument all the time as in self::get()
//     *
//     * @param $image_id
//     * @param $size
//     * @param $args
//     */
//    public static function get_simple( $img_id, $size, $more_args = array() ) {
//
//        $args = array(
//            'img_id' => $img_id
//        );
//
//        if ( is_array( $size ) ) {
//            $args['sizes'] = $size;
//        } else {
//            $args['size'] = $size;
//        }
//
//        if ( $more_args ) {
//            $args = wp_parse_args( $args, $more_args );
//        }
//
//        return self::get( $args );
//    }
//
//    /**
//     * @param array $sources
//     * @param string $classes
//     * @param string $inner_html
//     * @param bool   $close_tag
//     */
//    public static function html_from_sources( $sources, $args = array() ){
//
//        if ( ! is_array( $sources ) ) {
//            $sources = (array) $sources;
//        }
//
//        // actually, we may want to use one image size at different resolutions..
//        // $sources = array_unique( $sources );
//        $sources = array_filter( $sources ); // get rid of possible empty array values just in case
//
//        if ( count( $sources ) < 1 ) {
//            return '';
//        }
//
//        $atts = array(); // attributes of our outer html tag
//        $atts['class'] = ''; // override this later, but set it now so its printed before style or other atts
//        $inner_html = '';
//        $classes = array(
//            'background-image',
//            'standard',
//        );
//
//        $tag = gp_if_set( $args, 'tag', 'div' );
//        $close_tag = gp_if_set( $args, 'close_tag', true );
//        $add_inner_html = gp_if_set( $args, 'inner_html', '' );
//        $bg_overlay = gp_if_set( $args, 'bg_overlay', false );
//        $overlay_opacity = gp_if_set( $args, 'overlay_opacity', '' );
//        $add_class = gp_if_set( $args, 'add_class', '' );
//
//        if ( $add_class ) {
//            $classes[] = $add_class;
//        }
//
//        if ( $bg_overlay ) {
//            // note that empty string here means use default css styles, but false, "false", 0, "0" mean inline style of opacity zero
//            $overlay_style = gp_shortcode_overlay_style( $overlay_opacity );
//            $inner_html .= '<div class="bg-overlay" style="' . $overlay_style  . '"></div>';
//        }
//
//        // do this after bg overlay i suppose..
//        $inner_html .= $add_inner_html;
//
//        if ( count( $sources ) > 1 ) {
//            $atts['data-sources'] = $sources;
//            $classes[] = 'js-set-src';
//        } else {
//            $srcs = array_values( $sources );
//            $img_url = gp_if_set( $srcs, 0 );
//            $atts['style'] = gp_get_img_style( $img_url );
//        }
//
//        // set classes right before printing
//        $atts['class'] = $classes;
//
//        return gp_atts_to_container( $tag, $atts, $close_tag, $inner_html );
//    }
//}

/**
 * Class GP_Background_Image
 */
Class GP_Background_Image {

	protected $args;
	protected $sources;

	/**
	 * Pass in one of the following sets of $args
	 * 1. sources (array)
	 * 2. img_id, sizes (array)
	 * 3. img_id, size
	 * 4. img_url
	 * GP_Background_Image constructor.
	 *
	 * @param $args
	 */
	public function __construct( $args ) {

		// just for fun
		//        if ( isset( $args['img_id'] ) ) {
		//            $args['img_id'] = 55;
		//        }
		$this->args = self::build_args( $args );
	}

	/**
	 * @param $args
	 */
	//	private function get_cache_string( $args ) {
	//		// return gp_unique_str_arr( $args );
	//	}

	/**
	 * Parse/filter args
	 *
	 * todo: add param that says, "use img tag if more than 1 source, otherwise use a background image"
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public static function build_args( $args ) {

		// due to how we code things later on, its not necessary to put all possible args in defaults
		$defaults = array(
			'img_tag' => false, // img tag more or less works, but theres some occassional bugs so not using it for now
			'img_title' => '', // title is normally filename, and shouldn't be shown be default
			'img_alt' => 'AUTO', // requires img_tag = true
			'cache' => true, // whether to store/check wp_options table before getting html..
		);

		$args = wp_parse_args( $args, $defaults );

		$img_id = gp_if_set( $args, 'img_id' );
		$img_tag = gp_if_set( $args, 'img_tag' );

		// Alt/Title
		if ( $img_id && $img_tag ) {
			$img_title = gp_if_set( $args, 'img_title' );
			if ( $img_title === 'AUTO' ) {
				$args[ 'img_title' ] = gp_get_img_title( $img_id );
			}

			$img_alt = gp_if_set( $args, 'img_alt' );
			if ( $img_alt === 'AUTO' ) {
				$args[ 'img_alt' ] = gp_get_img_alt( $img_id );
			}
		}

		return $args;
	}

	/**
	 * Calls self::get()
	 * Just easier to pass in the first 2 params as they are usually the only ones used,
	 * so we avoid making arrays for an argument all the time as in self::get()
	 *
	 * @param $image_id
	 * @param $size
	 * @param $args
	 */
	public static function get_simple( $img_id, $size, $more_args = array() ) {

		$args = array(
			'img_id' => $img_id
		);

		if ( is_array( $size ) ) {
			$args[ 'sizes' ] = $size;
		} else {
			$args[ 'size' ] = $size;
		}

		if ( $more_args ) {
			$args = wp_parse_args( $args, $more_args );
		}

		return self::get( $args );
	}

	/**
	 * Creates instance of self and called $this->get_html();
	 *
	 * @param $args
	 *
	 * @return string
	 */
	public static function get( $args ) {
		$bg = new GP_Background_Image( $args );
		return $bg->get_html();
	}

	/**
	 * @param bool $check_setup
	 *
	 * @return bool
	 */
	protected function setup_sources( $check_setup = true ) {

		// not a perfect check since if we tried to set it up and got nothing the first time
		// we may run the code again and return nothing once more
		if ( $check_setup && is_array( $this->sources ) ) {
			return true;
		}

		$args = $this->args;
		$srcs = array();
		$sources = isset( $args[ 'sources' ] ) ? $args[ 'sources' ] : null;

		if ( $sources && is_array( $sources ) ) {
			$this->sources = $sources;
			return true;
		}

		$img_url = isset( $args[ 'img_url' ] ) ? $args[ 'img_url' ] : null;

		// not null, not false, not empty...
		if ( $img_url ) {
			$srcs[] = array(
				'url' => $img_url,
			);
			$this->sources = $srcs;
			return true;
		}

		$img_id = isset( $args[ 'img_id' ] ) ? $args[ 'img_id' ] : null;

		if ( get_post_type( $img_id ) !== 'attachment' ) {
			$this->sources = array();
			return false;
		}

		$size = isset( $args[ 'size' ] ) ? $args[ 'size' ] : null;
		$sizes = isset( $args[ 'sizes' ] ) ? $args[ 'sizes' ] : null;

		// makes sure $sizes is an array even if just one element
		if ( $size && ! $sizes ) {
			$sizes = $size;
		}
		// order matters (do prev if statement first)
		if ( is_string( $sizes ) ) {
			$sizes = (array) $sizes;
		}

		// todo: maybe we can optimize this by using wp_get_attachment_metadata() and our own logic
		if ( $sizes && is_array( $sizes ) ){
			$srcs = array();
			foreach ( $sizes as $kk=>$size ) {
				$metadata = wp_get_attachment_image_src( $img_id, $size );
				if ( $metadata ) {
					// attributes will be sanitize later before printing
					$srcs[] = array(
						'url' => gp_if_set( $metadata, 0 ),
						'size' => $size,
						'width' => gp_if_set( $metadata, 1 ),
						'height' => gp_if_set( $metadata, 2 ),
						// 'is_intermediate' => gp_if_set( $metadata, 3 ), // may or may not use
					);
				}
			}
		}

		if ( $srcs ) {
			$this->sources = $srcs;
			return true;
		}

		$this->sources = array();
		return false;
	}

	/**
	 * Calls $this->setup_sources(), then uses self::html_from_sources();
	 *  if the default args below need adjustments, then just call $this->get_sources() yourself, then
	 *  self::html_from_sources
	 * @return string
	 */
	public function get_html() {

		// todo: caching?
		// we could generate a unique key from the args array, then use set_transient/get_transient
		// with meta key like... bg_uniquestring..
		// one issue is that when changing the args, the old transients wont be deleted..
		// we would have to maybe periodically clear transients..
		// other than that, i dont see much of an issue arising, and i think the performance boost would be worth it.
		// one possible downside however, is if using img tags, and the user updates the alt attribute..
		// the other issue, is that if using a unique combination of args, we can't target any previously cached
		// results, except for maybe deleting them all. Anyways, something to consider. If we do caching, then inside
		// this function (right here) is probably the best place to do it.

		//		$cache = gp_if_set( $this->args, 'cache' );
		//		if ( $cache ) {}

		// if the sources get setup properly
		if ( $this->setup_sources() ) {
			$html = self::html_from_sources( $this->sources, $this->args );
			// if ( $cache ) {}
			return $html;
		}
		return '';
	}

	/**
	 * @param array  $sources
	 * @param string $classes
	 * @param string $inner_html
	 * @param bool   $close_tag
	 */
	public static function html_from_sources( $sources, $args = array() ) {

		if ( ! is_array( $sources ) ) {
			$sources = (array) $sources;
		}

		// actually, we may want to use one image size at different resolutions..
		// $sources = array_unique( $sources );
		$sources = array_filter( $sources ); // get rid of possible empty array values just in case

		if ( count( $sources ) < 1 ) {
			return '';
		}

		$atts = array(); // attributes of our outer html tag
		$atts[ 'class' ] = ''; // override this later, but set it now so its printed before style or other atts
		$inner_html = '';
		$overlay_html = '';
		$sources_html = '';
		$img_tag_html = '';
		$classes = array(
			'background-image',
			'standard',
		);

		$tag = gp_if_set( $args, 'tag', 'div' );
		$close_tag = gp_if_set( $args, 'close_tag', true );
		$bg_overlay = gp_if_set( $args, 'bg_overlay', false );
		$overlay_opacity = gp_if_set( $args, 'overlay_opacity', '' );
		$add_class = gp_if_set( $args, 'add_class', '' );
		$img_tag = gp_if_set( $args, 'img_tag', false );

		//		$wow_js = gp_if_set( $args, 'wow_js', false ); // js fade in effect..
		//		if ( $wow_js ) {
		//			if ( function_exists( 'gp_wow_delay' ) ) {
		//				$dl = gp_wow_delay();
		//			} else {
		//				$dl = '0.1s';
		//			}
		//			$classes[] = 'wow fadeIn';
		//			$atts['data-wow-delay'] = "0s";
		//		}

		if ( $add_class ) {
			$classes[] = $add_class;
		}

		if ( $bg_overlay ) {
			// note that empty string here means use default css styles, but false, "false", 0, "0" mean inline style of opacity zero
			$overlay_style = gp_shortcode_overlay_style( $overlay_opacity );
			$overlay_html .= '<div class="bg-overlay" style="' . $overlay_style . '"></div>';
		}

		$compile_sources = false; // whether to generate some $sources_html

		// IMAGE TAG
		if ( $img_tag ) {

			$classes[] = 'js-set-src';
			$classes[] = 'type-img-tag';

			$title = gp_if_set( $args, 'img_title' );
			$alt = gp_if_set( $args, 'img_alt' );

			// we'll use javascript to help position the image, so just leave the src attribute empty here and let js handle it
			$img_tag_html = '<img class="src-target" src="" alt="' . $alt . '" title="' . $title . '" />';
			$compile_sources = true;

		} else {
			// BACKGROUND-IMAGE

			// SINGLE SOURCE
			// if single source and doing background image, then javascript is not required, and we just put in the style attribute
			// like we normally would
			if ( count( $sources ) === 1 ) {
				$first_source = gp_array_first( $sources );
				$img_url = gp_if_set( $first_source, 'url', '' );
				$atts[ 'style' ] = gp_get_img_style( $img_url );
			} else {
				$classes[] = 'js-set-src';
				$classes[] = 'type-css'; // may not need this class, but its the opposite of 'set-img-src'
				$compile_sources = true;
			}
		}

		if ( $compile_sources && $sources ) {
			$sc = 0;
			foreach ( $sources as $kk=>$source ) {
				$sc++;
				$source['count'] = $sc;
				$sources_html .= self::source_tag( $source );
			}
		}


		// set dynamic css classes
		$atts[ 'class' ] = $classes;

		$inner_html .= gp_if_set( $args, 'inner_html', '' );
		// if overlay is true
		$inner_html .= $overlay_html;
		$inner_html .= $img_tag_html;
		$inner_html .= $sources_html;

		return gp_atts_to_container( $tag, $atts, $close_tag, $inner_html );
	}

	/**
	 * @param $arr
	 *
	 * @return string
	 */
	public static function source_tag( $arr ) {

		$count = gp_if_set( $arr, 'count' );
		$size = gp_if_set( $arr, 'size' );
		$url = gp_if_set( $arr, 'url' );
		$width = gp_if_set( $arr, 'width' );
		$height = gp_if_set( $arr, 'height' );
		$is_intermediate = gp_if_set( $arr, 'is_intermediate', null );

		$tag = 'span';
		$classes = array(
			'src-element',
			'src-' . $count,
			'src-' . $size,
		);
		$atts = array();
		$atts['class'] = ''; // set empty first, over write later
		$atts['data-url'] = $url;
		$atts['data-width'] = (int) $width;
		$atts['data-height'] = (int) $height;

		if ( $is_intermediate !== null ) {
			$atts['data-intermediate'] = (bool) $is_intermediate;
		}

		$atts['class'] = gp_parse_css_classes( $classes );

		$op = gp_atts_to_container( $tag, $atts, true, '' );
		return $op;
	}

	/**
	 * @return array|false
	 */
	public function get_sources() {
		if ( $this->setup_sources() ) {
			return $this->sources;
		}
		return false;
	}
}
