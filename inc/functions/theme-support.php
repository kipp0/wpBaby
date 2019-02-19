<?php


// Adding mime types
function wpBaby_mime_types($mimes) {
	# code...
	$mimes['R'] = 'application/.R';
	$mimes['R'] = 'application/.RData';
	$mimes['sas'] = 'application/.sas';
	return $mimes;
}
add_filter( 'upload_mimes', 'wpBaby_mime_types' );

function wpBaby_pagenation($html) {
	$str = '';

	//wrap a's and span's in li's
    $str = str_replace("<div","",$html);
    $str = str_replace("class='wp-pagenavi' role=\"Post Page Navigation\" aria-label=\"Post Page Navigation\">","",$str);
    $str = str_replace("<a","<li><a",$str);
    $str = str_replace("</a>","</a></li>",$str);
    $str = str_replace("<span","<li><span",$str);
    $str = str_replace("</span>","</span></li>",$str);
    $str = str_replace("</div>","",$str);

    return '<div class="pagination pagination-centered">
            <ul>'.$out.'</ul>
        </div>';
}
add_filter( 'wp_pagenavi', 'wpBaby_pagenation', 5, 2 );

// Adding WP Functions & Theme Support
function wpBaby_theme_support() {

	// Add WP Thumbnail Support
	add_theme_support( 'post-thumbnails' );

	// Default thumbnail size
	set_post_thumbnail_size(125, 125, true);

	// Add RSS Support
	add_theme_support( 'automatic-feed-links' );

	// Add Support for WP Controlled Title Tag
	add_theme_support( 'title-tag' );

	// Add HTML5 Support
	add_theme_support( 'html5',
	         array(
	         	'comment-list',
	         	'comment-form',
	         	'search-form',
	         )
	);

	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );

	// Adding post format support
	 add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	);

	// Set the maximum allowed width for any content in the theme, like oEmbeds and images added to posts.
	$GLOBALS['content_width'] = apply_filters( 'wpBaby_theme_support', 1200 );

} /* end theme support */

add_action( 'after_setup_theme', 'wpBaby_theme_support' );
