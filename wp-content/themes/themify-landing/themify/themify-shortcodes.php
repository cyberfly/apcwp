<?php
/***************************************************************************
 *
 * 	----------------------------------------------------------------------
 * 						DO NOT EDIT THIS FILE
 *	----------------------------------------------------------------------
 * 
 *  				     Copyright (C) Themify
 * 
 *	----------------------------------------------------------------------
 *
 * Shortcodes:
 * 		button
 * 		col
 * 		img
 * 		hr
 * 		quote
 * 		is_logged_in
 * 		is_guest
 * 		map
 * 		video
 * 		flickr
 * 		twitter
 * 		instagram
 * 		post_slider
 * 		slider
 * 		list_posts
 * 		box
 * 		author-box
 *		icon
 * 
 * Functions:
 *		themify_shortcode_list
 * 		themify_shortcodes_js_css
 * 		themify_shortcode
 * 		themify_shortcode_list_posts
 * 		themify_shortcode_flickr
 *		themify_shortcode_twitter
 *		themify_shortcode_instagram
 * 		themify_shortcode_slide
 * 		themify_shortcode_slider
 * 		themify_shortcode_post_slider
 * 		themify_shortcode_author_box
 * 		themify_shortcode_box
 *		themify_fix_shortcode_empty_paragraph
 * 
 ***************************************************************************/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'themify_shortcode_list' ) ) {
	/**
	 * Return list of Themify shortcodes.
	 *
	 * @since 1.9.4
	 *
	 * @return array Collection of shortcodes as keys and callbacks as values.
	 */
	function themify_shortcode_list() {
		return array(
			'is_logged_in' => 'themify_shortcode',
			'is_guest'     => 'themify_shortcode',
			'button'       => 'themify_shortcode',
			'quote'        => 'themify_shortcode',
			'col'          => 'themify_shortcode',
			'sub_col'      => 'themify_shortcode',
			'img'          => 'themify_shortcode',
			'hr'           => 'themify_shortcode',
			'map'          => 'themify_shortcode',
			'list_posts'   => 'themify_shortcode_list_posts',
			'flickr'       => 'themify_shortcode_flickr',
			'twitter'      => 'themify_shortcode_twitter',
			'box'          => 'themify_shortcode_box',
			'post_slider'  => 'themify_shortcode_post_slider',
			'slider'       => 'themify_shortcode_slider',
			'slide'        => 'themify_shortcode_slide',
			'author_box'   => 'themify_shortcode_author_box',
			'icon'         => 'themify_shortcode_icon',
		);
	}
}

/**
 * Enqueues JS, CSS and writes inline scripts
 */
add_action( 'wp_enqueue_scripts', 'themify_shortcodes_js_css' );

/**
 * Add Themify Shortcodes, an unprefixed version and a prefixed version.
 */
foreach( themify_shortcode_list() as $themify_sc => $themify_sc_callback) {
	add_shortcode( $themify_sc, $themify_sc_callback );
	add_shortcode( 'themify_' . $themify_sc, $themify_sc_callback );
}
// Backwards compatibility
add_shortcode( 'themify_video', 'wp_video_shortcode' );

/**
 * Fix empty auto paragraph in shortcodes
 */
add_filter( 'the_content', 'themify_fix_shortcode_empty_paragraph' );

/**
 * Enable shortcodes in footer text areas
 */
add_filter( 'themify_the_footer_text_left', 'do_shortcode' );
add_filter( 'themify_the_footer_text_right', 'do_shortcode' );

/**
 * Enable shortcode in excerpt
 */
add_filter('the_excerpt', 'do_shortcode');	
add_filter('the_excerpt', 'shortcode_unautop');

/**
 * Enable shortcode in text widget
 */
add_filter('widget_text', 'do_shortcode');	
add_filter('widget_text', 'shortcode_unautop');

/**
 * Flush twitter transient data
 */
add_action( 'save_post', 'themify_twitter_flush_transient' );

/**
 * Enqueue JavaScript and stylesheets required by shortcodes
 * @since 1.1.2
 */	
function themify_shortcodes_js_css() {
	global $themify_twitter_instance;
	$themify_twitter_instance = 0;
	//Use expanded versions for development or minified versions for production
	$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	
	//Enqueue general shortcodes style
	wp_enqueue_style( 'themify-framework', THEMIFY_URI . '/css/themify.framework.css', array(), THEMIFY_VERSION);
	
	//Enqueue general shortcodes script
	wp_register_script('themify-shortcodes-js', THEMIFY_URI . "/js/themify.shortcodes.js", array('jquery'), THEMIFY_VERSION, true );

	//Register carousel script
	wp_register_script('themify-carousel-js', THEMIFY_URI . "/js/carousel$min.js", '', THEMIFY_VERSION, true);
	
	//Register map scripts
	wp_register_script('themify-map-script', themify_https_esc('http://maps.google.com/maps/api/js').'?sensor=false', array(), THEMIFY_VERSION, true);
	wp_register_script('themify-map-shortcode', THEMIFY_URI . '/js/themify.mapa.js', array(), THEMIFY_VERSION, true);
	
	//Register video script
	wp_register_script('themify-video-script', THEMIFY_URI.'/js/flowplayer-3.2.4.min.js', array(), THEMIFY_VERSION, true);

}

/**
 * Creates shortcodes
 * @param Object $atts
 * @param String $content
 * @param String $code
 * @return String
 */
function themify_shortcode( $atts, $content = null, $code = '' ) {
	switch ( $code ) {
		case 'is_logged_in':
		case 'themify_is_logged_in':
			if ( is_user_logged_in() ) {
				return do_shortcode( $content );
			}
		break;
		case 'is_guest':
		case 'themify_is_guest':
			if ( ! is_user_logged_in() ) {
				return do_shortcode( $content );
			}
		break;
		case 'button':
		case 'themify_button':
			extract( shortcode_atts( array(
				'color' => '',
				'size' 	=> '',
				'style'	=> '',
				'link' 	=> '#',
				'target'=> '',
				'text'	=> ''
			), $atts, 'themify_button' ) );
			if($color != ''){
				$color = "background-color: $color;";
			}
			if($text != ''){
				$text = "color: $text;";	
			}
			return '<a href="' . esc_url( $link ) . '" class="shortcode button ' . esc_attr( $style.' '.$size ) . '" style="'.esc_attr( $color.$text ).'" target="'.esc_attr( $target ).'">'.do_shortcode($content).'</a>';
		break;
		case 'quote':
		case 'themify_quote':
			return '<blockquote class="shortcode quote">' . do_shortcode( preg_replace( array( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '~\s?<p>(\s|&nbsp;)+</p>\s?~' ), '', force_balance_tags( $content ) ) ) . '</blockquote>';
		break;
		case 'col':
		case 'themify_col':
			wp_enqueue_script('themify-shortcodes-js');
			extract( shortcode_atts( array( 'grid' => '' ), $atts, 'themify_col' ) );
			return '<div class="shortcode col' . esc_attr( $grid ) . '">' . do_shortcode( preg_replace( array( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '~\s?<p>(\s|&nbsp;)+</p>\s?~' ), '', force_balance_tags( $content ) ) ) . '</div>';
		break;
		case 'sub_col':
		case 'themify_sub_col':
			wp_enqueue_script('themify-shortcodes-js');
			extract( shortcode_atts( array( 'grid' => '' ), $atts, 'themify_sub_col' ) );
			return '<div class="shortcode col' . esc_attr( $grid ) . '">' . do_shortcode( preg_replace( array( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '~\s?<p>(\s|&nbsp;)+</p>\s?~' ), '', force_balance_tags( $content ) ) ) . "</div>";
		break;
		case 'img':
		case 'themify_img':
			extract( shortcode_atts( array(	'class' => '',
											'src' 	=> '',
											'id'	=> '',
											'h'		=> '',
											'w'		=> '',
											'crop'	=> true
											), $atts, 'themify_img' ) );	
			return themify_get_image("class=$class&src=$src&id=$id&h=$h&w=$w&crop=$crop");
		break;
		case 'hr':
		case 'themify_hr':
			extract( shortcode_atts( array( 'color' => '',
											'width' => '',
											'border_width' => ''
			), $atts, 'themify_hr' ));
			if( '' != $width || '' != $border_width  ){
				$hrstyle = 'style="';
				if( '' != $width  ){
					$hrstyle .= 'width:' . $width . ';';
				}
				if( '' != $border_width  ){
					if( preg_match('/MSIE 7/i', $_SERVER['HTTP_USER_AGENT'] ) ){
						$hrstyle .= 'height:' . $border_width . ';';
					}
					$hrstyle .= 'border-width:' . $border_width . ';';
				}
				$hrstyle .= '"';
			} else {
				$hrstyle = '';
			}
			return '<hr class="shortcode hr ' . esc_attr( $color ) . '" ' . $hrstyle . ' />';
		break;
		case 'map':
		case 'themify_map':
			wp_enqueue_script('themify-map-script');
			wp_enqueue_script('themify-map-shortcode');
			extract( shortcode_atts(
				array(
					'address' => '99 Blue Jays Way, Toronto, Ontario, Canada',
					'width' => '500px',
					'height' => '300px',
					'zoom' => 15,
					'type' => 'ROADMAP',
					'scroll_wheel' => 'yes',
					'draggable' => 'yes',
				),
				$atts,
				'themify_map'
			));
			$num = rand(0,10000);
			return '<script type="text/javascript">	
						jQuery(document).ready(function() {
					  		ThemifyMap.initialize( "'.$address.'", '.$num.', '.$zoom.', "'.$type.'", "'.$scroll_wheel.'", "' . $draggable . '" );
						});
					</script>
					<div class="shortcode map">
						<div id="themify_map_canvas_' . esc_attr( $num ) . '" style="display: block;width:' . esc_attr( $width ) . ';height:' . esc_attr( $height ) . ';" class="map-container">&nbsp;</div>
					</div>';
		break;
		case 'video':
		case 'themify_video':
			wp_enqueue_script('themify-video-script');
			extract( shortcode_atts(
				array(
					'width' => '500px',
					'height' => '300px',
					'src' => '#'
				),
				$atts,
				'themify_video'
			));
			$num = rand(0,10000);
			if( stripos($_SERVER['HTTP_USER_AGENT'], 'iPod') || stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone') ||
				stripos($_SERVER['HTTP_USER_AGENT'], 'iPad') ||	stripos($_SERVER['HTTP_USER_AGENT'], 'Android') ) {
				return '<div class="shortcode video"><video src="' . esc_url( $src ) . '"></video></div>';
			} else {
				return '<div class="shortcode video"><a href="' . esc_url( $src ) . '" style="display:block;width:' . esc_attr( $width ) . ';height:' . esc_attr( $height ) . '" id="themify_player_' . esc_attr( $num ) . '"></a></div><script type="text/javascript">jQuery(document).ready(function(){ flowplayer("themify_player_' . esc_attr( $num ) . '", "' . THEMIFY_URI . '/js/flowplayer-3.2.5.swf", { clip: { autoPlay:false } }); });</script>';
			}
		break;
	}
	return '';
}

/**
 * List posts using get_posts
 * @param Object $atts
 * @param String $content
 * @return String
 */
function themify_shortcode_list_posts( $atts, $content = null ) {
	global $themify;
	wp_enqueue_script('themify-shortcodes-js');
	extract(shortcode_atts(array(
		'title' => 'yes',
		'category' => '0',
		'limit' => '5',
		'offset' => '0',
		'more_text' => __('More...', 'themify'),
		'excerpt_length' => '',
		'image' => 'yes',
		'image_w' => '220',
		'image_h' => '150',
		'display' => 'none',
		'style' => 'list-post',
		'post_date' => 'no',
		'post_meta' => 'no',
		'unlink_title' => 'no',
		'unlink_image' => 'no',
		'image_size' => 'thumbnail',
		'post_type' => 'post',
		'taxonomy' => 'category',
		'order' => 'DESC',
		'orderby' => 'date'
	), $atts, 'themify_list_posts' ));

	if ( 'post' != $post_type && 'category' == $taxonomy ) {
		$taxonomy = $post_type . '-category';
	}

	$query_args = array(
		'numberposts' => $limit,
		'offset' => $offset,
		'post_type' => $post_type,
		'taxonomy' => $taxonomy,
		'order' => $order,
		'orderby' => $orderby,
		'suppress_filters' => false,
		'post__not_in' => array(get_the_ID())
	);
	if ('0' != $category) {
		$tax_query_terms = explode(',', $category);
		if(preg_match('#[a-z]#', $category)){
			$query_args['tax_query'] = array( array(
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $tax_query_terms
			));
		} else {
			$exclude = array_filter( $tax_query_terms, 'themify_is_negative_number' );
			$query_args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => array_filter( $tax_query_terms, 'themify_is_positive_number' ),
				),
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => array_map( 'themify_make_absolute_number', $exclude ),
					'operator' => 'NOT IN',
				),
			);
		}
	}
	$posts = get_posts($query_args);
	
	// save a copy
	$themify_save = clone $themify;

	// override $themify object
	$themify->hide_image = 'yes' == $image? 'no' : 'yes';
	$themify->unlink_image = $unlink_image;
	$themify->hide_title = 'yes' == $title? 'no' : 'yes';
	$themify->width = $image_w;
	$themify->height = $image_h;
	$themify->image_setting = 'ignore=true&';
	$themify->unlink_title = $unlink_title;
	$themify->display_content = $display;
	$themify->hide_date = 'yes' == $post_date? 'no' : 'yes';
	$themify->hide_meta = 'yes' == $post_meta? 'no' : 'yes';
	$themify->post_layout = $style;
	$themify->is_shortcode = true;

	$out = '';
	if ($posts) {
		$out = '<!-- shortcode list_posts --><div class="loops-wrapper shortcode clearfix list-posts layout ' . esc_attr( $style ) . ' ">';
		$out .= themify_get_shortcode_template( $posts, 'includes/loop', $post_type );
		$out .= '</div><!-- /shortcode list_posts -->';
	}
	
	// revert to original $themify state
	$themify = clone $themify_save;
	
	return $out;
}

/**
 * Insert Flickr Gallery by user, set or group
 * @param Object $atts
 * @param String $content
 * @return String
 */	
function themify_shortcode_flickr( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'user' => '',
		'set' => '',
		'group' => '',
		'limit' => '8',
		'size' => 's',
		'display' => 'latest'
	), $atts, 'themify_flickr' ));
	$flickrstr = '';
	if ( $user ) {
		$flickrstr = '<!-- shortcode Flickr --> <div class="shortcode clearfix flickr"><script type="text/javascript" src="' . esc_url( themify_https_esc( 'http://www.flickr.com/badge_code_v2.gne' ) . '?count=' . $limit . '&amp;display=' . $display . '&amp;size=' . $size . '&amp;layout=x&amp;source=user&amp;user=' . $user ) . '"></script></div>';
	}

	if ( $set ) {
		if ( '' == $flickrstr ) {
			$flickrstr = '<div class="shortcode clearfix flickr"><script type="text/javascript" src="' . esc_url( themify_https_esc( 'http://www.flickr.com/badge_code_v2.gne' ) . '?count=' . $limit . '&amp;display=' . $display . '&amp;size=' . $size . '&amp;layout=x&amp;source=user_set&amp;set=' . $set ) . '"></script></div>';
		}
	}
	if ( $group ) {
		if ( $flickrstr == '' ) {
			$flickrstr = '<div class="shortcode clearfix flickr"><script type="text/javascript" src="' . esc_url( themify_https_esc( 'http://www.flickr.com/badge_code_v2.gne' ) . '?count=' . $limit . '&amp;display=' . $display . '&amp;size=' . $size . '&amp;layout=x&amp;source=group&amp;group=' . $group ) . '"></script></div> <!-- /shortcode Flickr -->';
		}
	}
	return $flickrstr;
}
/**
 * Creates one slide for the slider shortcode
 * @param Object $atts
 * @param String $content
 * @return String
 */
function themify_shortcode_slide( $atts, $content = null ) {
	extract( shortcode_atts( array(), $atts ) );
	$output = '<li><div class="slide-wrap">' . do_shortcode( $content ) . '</div></li>';
	return $output;
}

/**
 * Creates a slider using the slide shortcode
 * @param Object $atts
 * @param String $content
 * @return String
 */
function themify_shortcode_slider( $atts, $content = null ){
	wp_enqueue_script('themify-carousel-js');
	extract(shortcode_atts(array(
		'wrap' => 'yes',
		'visible' => '1',
		'scroll' => '1',
		'auto' => '0',
		'pause_hover' => 'no',
		'speed' => 'normal',
		'slider_nav' => 'yes',
		'pager' => 'yes',
		'effect' => 'scroll',
		'class' => ''
	), $atts, 'themify_slider' ));
	$numsldrtemp = rand( 0, 10000 );
	$content = do_shortcode( shortcode_unautop( $content ) );

	if( '0' == $auto )
		$play = 'false';
	else
		$play = 'true';
	switch ( $speed ) {
		case 'fast':
			$speed = '.5';
		break;
		case 'normal':
			$speed = '1';
		break;
		case 'slow':
			$speed = '4';
		break;
	}
	
	$wrapvar = 'false';
	if ( 'yes' == $wrap ) {
		$wrapvar = 'true';
	}
	$pause_hover = ( $pause_hover == 'yes' ) ? 'true' : 'false';
	
	$class .= ' effect-' . $effect;
	
	$strsldr = '<!-- shortcode slider --><div id="slider-' . esc_attr( $numsldrtemp ) . '" class="shortcode clearfix slider ' . esc_attr( $class ) . '">
	
	<ul class="slides">' . $content . '</ul>';
	
	$strsldr .= '</div>
	<script type="text/javascript">
	(function($){
		$(window).load(function() {
			var $slider_slides = $("#slider-'.$numsldrtemp.' .slides");
			$slider_slides.find( "> br, > p" ).remove();
			$slider_slides.carouFredSel({
			responsive: true,';
				
		if ( 'yes' == $slider_nav ) {
			$strsldr .= '
				prev: "#slider-'.$numsldrtemp.' .carousel-prev",
				next: "#slider-'.$numsldrtemp.' .carousel-next",';
		}
		if( 'yes' == $pager ){
			$strsldr .= '
				pagination: "#slider-'.$numsldrtemp.' .carousel-pager",';
		}
		$strsldr .= '
			circular: '.$wrapvar.',
			infinite: '.$wrapvar.',
			auto: {
				play : '.$play.',
				timeoutDuration: '.$auto.'*1000,
				duration: '.$speed.'*1000,
				pauseOnHover: '. $pause_hover .'
			},
			swipe: true,
			scroll: {
				items: '.$scroll.',
				duration: '.$speed.'*1000,
				fx: "'.$effect.'"
			},
			items: {
				visible: {
					min: 1,
					max: '.$visible.'
				},
				width: 120
			},
			onCreate : function (){
					$(".slider").css( {
					"height": "auto",
					"visibility" : "visible"
				});
			}
		});
			
			var tscsDidResize = false;
			$(window).on("resize", function() {
				tscsDidResize = true;
			});
			setInterval(function() {
				if ( tscsDidResize ) {
					tscsDidResize = false;

					$(".slides").each(function(){
						var heights = [],
							newHeight,
							$self = $(this);
						$self.find("li").each( function() {
							heights.push( $(this).outerHeight() );
						});
						newHeight = Math.max.apply( Math, heights );
						$self.outerHeight( newHeight );
						$(".caroufredsel_wrapper").outerHeight( newHeight );
					});

				}
			}, 500);

	});
	})(jQuery);
	</script> <!-- /shortcode slider -->';
	return $strsldr;
}

/**
 * Create a slider with posts retrieved through get_posts
 * @param Object $atts
 * @param String $content
 * @return String
 */


function themify_shortcode_post_slider( $atts, $content = null ) {
	wp_enqueue_script( 'themify-carousel-js' );
	extract(shortcode_atts(array(
		'visible' => '1',
		'scroll' => '1',
		'auto' => '0',
		'pause_hover' => 'no',
		'wrap' => 'yes',
		'excerpt_length' => '20',
		'speed' => 'normal',
		'slider_nav' => 'yes',
		'pager' => 'yes',
		'limit' => '5',
		'offset' => '0',
		'category' => '',
		'image' => 'yes',
		'image_w' => '240px',
		'image_h' => '180px',
		'more_text' => __('More...', 'themify'),
		'title' => 'yes',
		'display' => 'none',
		'post_meta' => 'no',
		'post_date' => 'no',
		'width' => '',
		'height' => '',
		'class' => '',
		'unlink_title' => 'no',
		'unlink_image' => 'no',
		'image_size' => 'thumbnail',
		'post_type' => 'post',
		'taxonomy' => 'category',
		'order' => 'DESC',
		'orderby' => 'date',
		'effect' => 'scroll'
	), $atts, 'themify_post_slider' ));
	
	$wrapvar = 'false';
	if ( 'yes' == $wrap ) {
		$wrapvar = 'true';
	}
	if ( '0' == $auto )
		$play = 'false';
	else
		$play = 'true';
	
	switch ( $speed ) {
		case 'fast':
			$speed = '.5';
		break;
		case 'normal':
			$speed = '1';
		break;
		case 'slow':
			$speed = '4';
		break;
	}
	$pause_hover = ( $pause_hover == 'yes' ) ? 'true' : 'false';
	
	$numsldr = rand( 0, 10000 );
	$postsliderstr = '';
	global $post;

	$query_args = array(
		'numberposts' => $limit,
		'offset' => $offset,
		'post_type' => $post_type,
		'order' => $order,
		'orderby' => $orderby,
		'suppress_filters' => false,
		'post__not_in' => array(get_the_ID())
	);
	if ('' != $category) {
		$tax_query_terms = explode(',', $category);
		if(preg_match('#[a-z]#', $category)){
			$query_args['tax_query'] = array( array(
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $tax_query_terms
			));
		} else {
			$exclude = array_filter( $tax_query_terms, 'themify_is_negative_number' );
			$query_args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => array_filter( $tax_query_terms, 'themify_is_positive_number' ),
				),
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => array_map( 'themify_make_absolute_number', $exclude ),
					'operator' => 'NOT IN',
				),
			);
		}
	}
	$posts = get_posts($query_args);
	
	$class .= ' effect-' . $effect;
	
	if ($posts) {
		$postsliderstr = '<!-- shortcode post_slider --> <div id="post-slider-' . esc_attr( $numsldr ) . '" style="width: ' . esc_attr( $width ) . '; height: ' . esc_attr( $height ) . ';" class="shortcode clearfix post-slider ' . $class . '">
		<ul class="slides">';
		foreach ($posts as $post):
			setup_postdata($post);
			global $more;
			$more       = 0;
			$post_class = '';
			foreach (get_post_class() as $postclass) {
				$post_class .= " " . $postclass;
			} //get_post_class() as $postclass
			$postsliderstr .= '<li><div  class="slide-wrap ' . esc_attr( $post_class ) . '">';

			if ( 'yes' == $image ) {
				$video_url = themify_get( 'video_url' );
				if ( '' != $video_url ) {
					$postsliderstr .= '<div class="post-video">';
						global $wp_embed;
						$postsliderstr .= $wp_embed->run_shortcode('[embed]' . $video_url . '[/embed]');
					$postsliderstr .= '</div>';
				} else {
					if( 'no' == $unlink_image ) {
						$postsliderstr .= themify_get_image('image_size='.$image_size.'&ignore=true&w=' . $image_w . '&h=' . $image_h . '&alt=' . get_the_title() . '&before=<p class="post-image"><a href="' . themify_get_featured_image_link() . '">&after=</a></p>');
					} else {
						$postsliderstr .= themify_get_image('image_size='.$image_size.'&ignore=true&w=' . $image_w . '&h=' . $image_h . '&alt=' . get_the_title() . '&before=<p class="post-image">&after=</p>');
					}
				}
			} //'yes' == $image

			if ( 'yes' == $title ) {
				if( 'no' == $unlink_title ){
					$postsliderstr .= '<h3 class="post-title"><a href="' . themify_get_featured_image_link() . '">' . get_the_title() . '</a></h3>';
				}
				else{
					$postsliderstr .= '<h3 class="post-title">' . get_the_title() . '</h3>';
				}
			} //$title == "yes"
			if ($post_date == "yes") {
				$postsliderstr .= '<p class="post-date">' . get_the_date() . '</p>';
			} //$post_date == "yes"
			if ( 'yes' == $post_meta ) {
				$postsliderstr .= '<p class="post-meta">
					<span class="post-author">' . get_the_author() . '</span>
					<span class="post-category">' . get_the_category_list(', ') . '</span>';

				if ( comments_open() ) {
					ob_start();
					comments_popup_link('0', '1', '%', 'comments-link', '');
					$write_comments = ob_get_contents();
					ob_clean();
				} //comments_open()
				else {
					$write_comments = '';
				}
				$postsliderstr .= '<span class="post-comment">' . $write_comments . '</span>';
				if ( has_tag() ) {
					$postsliderstr .= '<span class="post-tag">' . get_the_tag_list('', ', ') . '</span>';
				}
				$postsliderstr .= '</p>';
			} //$post_meta == "yes"
			if ( 'content' == $display ) {
				$postsliderstr .= '<div class="post-content">' . wp_kses_post( themify_get_content($more_text) ) . '</div></div></li>
';
			} //$display == "content"
			if ( 'excerpt' == $display ) {
				
				$postsliderstr .= '<div class="post-content">' . wp_kses_post( themify_excerpt($excerpt_length) ) . '</div></div></li>
';
		
			} //$display == "excerpt"
		endforeach;
		$postsliderstr .= '</ul>';

		$postsliderstr .= '</div>
		<script type="text/javascript">
		(function($){
			$(window).load(function() {
				var $slider_slides = $("#post-slider-'.$numsldr.' .slides");
				$("#post-slider-'.$numsldr.' .slides").carouFredSel({
			responsive: true,';
				
		if ( 'yes' == $slider_nav ) {
			$postsliderstr .= '
				prev: "#post-slider-'.$numsldr.' .carousel-prev",
				next: "#post-slider-'.$numsldr.' .carousel-next",';
		}
		if( 'yes' == $pager ){
			$postsliderstr .= '
				pagination: "#post-slider-'.$numsldr.' .carousel-pager",';
		}
		$postsliderstr .= '
			circular: '.$wrapvar.',
			infinite: '.$wrapvar.',
			auto: {
				play : '.$play.',
				timeoutDuration: '.$auto.'*1000,
				duration: '.$speed.'*1000,
				pauseOnHover: '. $pause_hover .'
			},
			swipe: true,
			scroll: {
				items: '.$scroll.',
				duration: '.$speed.'*1000,
				fx: "'.$effect.'"
			},
			items: {
				visible: {
					min: 1,
					max: '.$visible.'
				},
				width: 120
			},
			onCreate : function (){
				jQuery(".post-slider").css( {
					"height": "auto",
					"visibility" : "visible"
				});
			}
		});
			
				var tscpsDidResize = false;
				$(window).on("resize", function() {
					tscpsDidResize = true;
	});
				setInterval(function() {
					if ( tscpsDidResize ) {
						tscpsDidResize = false;

						$(".slides").each(function(){
							var heights = [],
								newHeight,
								$self = $(this);
							$self.find("li").each( function() {
								heights.push( $(this).outerHeight() );
							});
							newHeight = Math.max.apply( Math, heights );
							$self.outerHeight( newHeight );
							$(".caroufredsel_wrapper").outerHeight( newHeight );
						});

					}
				}, 500);
			});
		})(jQuery);
	</script> <!-- /shortcode post_slider -->';
		wp_reset_postdata();
	} //$posts
	return $postsliderstr;
}


/**
 * Creates an author box to display your profile
 * @param Object $atts
 * @param String $content
 * @return String
 */
function themify_shortcode_author_box( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'avatar' => 'yes',
		'avatar_size' => '48',
		'style' => '',
		'author_link' => 'no'
	), $atts, 'themify_author_box' ));
	/** 
	 * Filtered name of author
	 * @var String */
	$nicename = get_the_author_meta( 'nicename' );
	$authorboxstr = '<!-- shortcode author_box --> <div class="shortcode clearfix author-box ' . esc_attr( $style . ' ' . $nicename ) . ' ">';
	if ( 'yes' == $avatar ) {
		$authorboxstr .= '<p class="author-avatar">' . get_avatar( get_the_author_meta( 'user_email' ), $avatar_size, '' ) . '</p>';
	}
	if ( get_the_author_meta( 'user_url' ) ) {
		$authorboxstr .= '<div class="author-bio">
			<h4 class="author-name"><a href="' . esc_url( get_the_author_meta( 'user_url' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></h4>
		' . get_the_author_meta( 'description' );
	} else {
		$authorboxstr .= '<div class="author-bio">
		<h4 class="author-name">' . get_the_author_meta( 'display_name' ) . '</h4>
	' . get_the_author_meta( 'description' );
	}
	if ( 'yes' == $author_link ) {
		if ( get_the_author_meta( 'user_url' ) ) {
			$authorboxstr .= '<p class="author-link"><a href="' . get_the_author_meta( 'user_url' ) . '">&rarr; ' . get_the_author_meta( 'display_name' ) . ' </a></p>';
		} else {
			$authorboxstr .= '<p class="author-link">&rarr; ' . get_the_author_meta( 'display_name' ) . ' </p>';
		}
	}
	$authorboxstr .= '</div>
	</div> <!-- /shortcode author_box -->';
	return $authorboxstr;
}

/**
 * Creates a box to enclose content
 * @param Object $atts
 * @param String $content
 * @return String
 */
function themify_shortcode_box( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'style' => ''
	), $atts, 'themify_box' ));
	$boxstr = '<!-- shortcode box --> <div class="shortcode clearfix box ' . esc_attr( $style ) . '">' . do_shortcode( preg_replace( array( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '~\s?<p>(\s|&nbsp;)+</p>\s?~' ), '', force_balance_tags( $content ) ) ) . '</div> <!-- /shortcode box -->';
	return $boxstr;
}

/**
 * Remove paragraphs wrapping shortcodes
 *
 * @param string $content
 *
 * @since 1.9.4
 *
 * @return string
 */
function themify_fix_shortcode_empty_paragraph( $content ) {
	$block = join( '|', array_keys( themify_shortcode_list() ) ) . '|themify_' . join( '|themify_', array_keys( themify_shortcode_list() ) );
	return preg_replace( array( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/" ), array( '[$2$3]', '[/$2]' ), $content );
}

/**
 * Display tweets by user
 * @param Object $atts
 * @param String $content
 * @return String
 */
function themify_shortcode_twitter( $atts, $content = null ) {
	global $themify_twitter_instance, $post;
	$themify_twitter_instance++;

	extract(shortcode_atts(array(
		'username' => '',
		'show_count' => 5,
		'show_timestamp' => 'true',
		'show_follow' => 'false',
		'follow_text' => __('&rarr; Follow me', 'themify'),
		'include_retweets' => 'false',
		'exclude_replies' => 'false',
		'is_widget' => 'false',
		'widget_id' => ''
	), $atts, 'themify_twitter' ));
	
	$is_shortcode = '';
	$transient_id = $themify_twitter_instance . '_' . $post->ID;
	if ( 'false' == $is_widget ) {
		$is_shortcode = 'shortcode';
	}

	if ( 'true' == $is_widget ) {
		$transient_id = $widget_id;
	}

	$args = array(
		'username' => sanitize_user( strip_tags( $username ) ),
		'limit' => intval( $show_count ),
		'include_retweets' => $include_retweets,
		'exclude_replies' => $exclude_replies
	);

	$tweets = themify_twitter_get_data( $transient_id, $args );

	$out = '<div class="twitter-list '.$is_shortcode.'">
			<div id="twitter-block-'.$themify_twitter_instance.'">';

	if ( is_array( $tweets ) && count( $tweets ) > 0 ) {
		$out .= '<ul class="twitter-list">';

		foreach( $tweets as $tweet ) {
			$text = $tweet->text; 
			foreach ( $tweet->entities as $type => $entity ) {
				if( 'urls' == $type ) {
					foreach($entity as $j => $url) {
						$update_with = '<a href="' . esc_url( $url->url ) . '" target="_blank" title="' . esc_attr( $url->expanded_url ) . '" class="twitter-user">' . $url->display_url . '</a>';
						$text = str_replace($url->url, $update_with, $text);
					}
				} else if( 'hashtags' == $type ) {
					foreach($entity as $j => $hashtag) {
						$update_with = '<a href="' . esc_url( 'https://twitter.com/search?q=%23' . $hashtag->text . '&src=hash' ) . '" target="_blank" title="' . esc_attr( $hashtag->text ) . '" class="twitter-user">#' . $hashtag->text . '</a>';
						$text = str_replace('#'.$hashtag->text, $update_with, $text);
					}
				} else if( 'user_mentions' == $type ) {
					foreach($entity as $j => $user) {
						$update_with = '<a href="' . esc_url( 'https://twitter.com/' . $user->screen_name ) . '" target="_blank" title="' . esc_attr( $user->name ) . '" class="twitter-user">@' . $user->screen_name . '</a>';
						$text = str_replace('@'.$user->screen_name, $update_with, $text);
					}
				}					
			}
			$out .= '<li class="twitter-item">'.$text;
			if ( 'false' != $show_timestamp ) {
				// hour ago time format
				$time = sprintf( __('%s ago', 'themify'), human_time_diff( strtotime( $tweet->created_at ), current_time( 'timestamp' ) ) );
				$out .= '<br /><em class="twitter-timestamp"><small>' . wp_kses_post( $time ) . '</small></em>';
			}
			$out .= '</li>';
		}
		$out .= '</ul>';
	}
	$out .= '</div>';
		if ( 'false' != $show_follow ) {
			$out .= '<div class="follow-user"><a href="' . esc_url( 'http://twitter.com/' . $username ) . '">' . $follow_text . '</a></div>';
		}

	$out .= '</div>';

	return $out;
}

/**
 * Get twitter data store from cache
 * @param $transient_id
 * @param $args
 * @return array|mixed
 */
function themify_twitter_get_data ( $transient_id, $args ) {
	$data = array();
	$transient_key = $transient_id . '_themify_twitter_feeds_transient';

	$transient = get_transient( $transient_key );
	
	if ( false === $transient ) {
		$response = themify_request_tweets( $args );

		if ( ! is_wp_error( $response ) && is_array( $response ) && isset( $response[0]->user->id ) ) {
			$data = $response;
			set_transient( $transient_key, $data, 10 * 60 ); // 10 min cache
		}
	} else {
		$data = $transient;
	}
	return $data;
}

/**
 * Get request tweets from service api
 * @param $args
 * @return bool|object
 */
function themify_request_tweets($args) {
	$data = themify_get_data();
	$prefix = 'setting-twitter_settings_';
	
	$screen_name = urlencode(strip_tags( sanitize_user( $args['username'] ) ));
	
	if ( $args['limit'] != '' ) {
		$count = intval( $args['limit'] );
	}
	if ( $args['include_retweets'] == 'true' ) {
		$include_rts = '1';
	} else {
		$include_rts = '0';
	}
	$exclude_replies = $args['exclude_replies'];
	
	$consumer_key = isset( $data[$prefix.'consumer_key'] )? $data[$prefix.'consumer_key'] : '';
	$consumer_secret = isset( $data[$prefix.'consumer_secret'] )? $data[$prefix.'consumer_secret'] : '';

	if ( ! class_exists( 'Wp_Twitter_Api' ) ) {
		// Require twitter oauth class
		require 'twitteroauth/class-wp-twitter-api.php';
	}
	$credentials = array(
		'consumer_key' => $consumer_key,
		'consumer_secret' => $consumer_secret
	);
	
	$query = 'screen_name='.$screen_name.'&count='.$count.'&include_rts='.$include_rts.'&exclude_replies='.$exclude_replies.'&include_entities=true';
	
	$twitterConnection = new Wp_Twitter_Api( $credentials );
	$tweets = $twitterConnection->query($query);
	
	return $tweets;
}

/**
 * Flush transient when post is saved.
 * @param $post_id
 */
function themify_twitter_flush_transient( $post_id ) {
	//verify post is not a revision
	if ( ! wp_is_post_revision( $post_id ) ) {
		// Count unprefixed and/or prefixed shortcode instances
		$post_content = '';
		if ( isset( $_POST['content'] ) ) {
			$post_content = $_POST['content'];
		}
		$unprefixed_shortcode = substr_count($post_content, '[twitter');
		$prefixed_shortcode = substr_count($post_content, '[themify-twitter');
		$shortcode_count = $unprefixed_shortcode + $prefixed_shortcode;
		if ( $shortcode_count > 0 ) {
			// delete transients
			for ($i=1; $i <= $shortcode_count; $i++) { 
				delete_transient( $i.'_'.$post_id.'_themify_twitter_feeds_transient' );
			}
		}
	}
}

/**
 * Renders a font icon.
 *
 * @since 1.9.1
 *
 * @param array $atts
 * @param null $content
 * @return string
 */
function themify_shortcode_icon( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'icon'       => '',
		'label'      => '',
		'link'       => '',
		'style'      => '',
		'icon_bg'    => '',
		'icon_color' => '',
		'target'=> '',
	), $atts );

	// Set .fa class if icon entered begins with fa-
	if ( 0 === stripos( $atts['icon'], 'fa-' ) ) {
		$atts['icon'] = 'fa ' . $atts['icon'];
	}

	// Set front and background colors.
	$colors = '';
	$style_attr = '';
	if ( ! empty( $atts['icon_bg'] ) ) {
		$colors .= "background-color: {$atts['icon_bg']};";
	}
	if ( ! empty( $atts['icon_color'] ) ) {
		$colors .= "color: {$atts['icon_color']};";
	}
	if ( ! empty( $colors ) ) {
		$style_attr = 'style="' . esc_attr( $colors ) . '"';
	}

	// Begin building markup for icon.
	$out = '';

	// Build icon
	if ( ! empty( $atts['icon'] ) ) {
		$out .= '<i class="themify-icon-icon ' . esc_attr( $atts['icon'] ) . '" ' . $style_attr . '></i>';
	}

	// Build label
	if ( ! empty( $atts['label'] ) ) {
		$out .= '<span class="themify-icon-label">' . $atts['label'] . '</span>';
	}

	// Sanitize link
	$link = $atts['link'];
	if ( '' != $link && '' != $out ) {
		$target = !empty( $target )? 'target="' . esc_attr( $target ) . '"' : '';
		$out = '<a href="' . esc_url( $link ) . '" class="themify-icon-link" ' . $target . '>' . $out . '</a>';
	}

	return '<span class="shortcode themify-icon ' . esc_attr( $atts['style'] ) . '">' . $out . '</span>';
}