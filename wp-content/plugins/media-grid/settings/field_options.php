<?php 

// preloader types
function mg_preloader_types($type = false) {
	$types = array(
		'default' 				=> __('Default loader', 'mg_ml'),
		'rotating_square' 		=> __('Rotating square', 'mg_ml'),
		'overlapping_circles' 	=> __('Overlapping circles', 'mg_ml'),
		'stretch_rect' 			=> __('Stretching rectangles', 'mg_ml'),
		'spin_n_fill_square'	=> __('Spinning & filling square', 'mg_ml'),
		'pulsing_circle' 		=> __('Pulsing circle', 'mg_ml'),
		'spinning_dots'			=> __('Spinning dots', 'mg_ml'),
		'appearing_cubes'		=> __('Appearing cubes', 'mg_ml'),
		'folding_cube'			=> __('Folding cube', 'mg_ml'),
		'old_style_spinner'		=> __('Old-style spinner', 'mg_ml'),
		'minimal_spinner'		=> __('Minimal spinner', 'mg_ml'),
		'spotify_like'			=> __('Spotify-like spinner', 'mg_ml'),
		'vortex'				=> __('Vortex', 'mg_ml'),
		'bubbling_dots'			=> __('Bubbling Dots', 'mg_ml'),
		'overlapping_dots'		=> __('Overlapping dots', 'mg_ml'),
		'fading_circles'		=> __('Fading circles', 'mg_ml'),
	);
	return (!$type) ? $types : $types[$type];
}



// inline slider effects
function mg_inl_slider_fx($type = false) {
	$types = array(
		'fadeslide' => __('Fade and slide', 'mg_ml'),
		'fade' 		=> __('Fade', 'mg_ml'),
		'slide'		=> __('Slide', 'mg_ml'),
		'v_slide'	=> __('Vertical slide', 'mg_ml'),
		'overlap'	=> __('Overlap', 'mg_ml'),
		'v_overlap'	=> __('Vertical overlap', 'mg_ml'),
		'zoom-in'	=> __('Zoom-in', 'mg_ml'),
		'zoom-out'	=> __('Zoom-out', 'mg_ml'),
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}



// WP pages ilst
function mg_pages_list() {
	$pages = array();
	
	foreach(get_pages() as $pag) {
		$pages[ $pag->ID ] = $pag->post_title;	
	}
	
	return $pages;	
}



// lightbox command layouts
function mg_lb_cmd_layouts($type = false) {
	$types = array(
		'inside' 	 	=> __('Inside lightbox', 'mg_ml'),
		'above' 	 	=> __('Above lightbox', 'mg_ml'),
		'top' 			=> __('Detached - top of the page', 'mg_ml'),
		'side'			=> __('Detached - on sides', 'mg_ml'),
		'side_basic'	=> __('Detached - on sides (basic)', 'mg_ml'),
		'ins_hidden'	=> __('Inside - hidden navigation', 'mg_ml'),
		'hidden'		=> __('Detached - hidden navigation', 'mg_ml'),
		'round_hidden'	=> __('Rounded - hidden navigation', 'mg_ml')
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// lightbox bg effects
function mg_lb_bg_showing_fx() {
	$opts = array(
		'' => __("no effect", 'mg_ml'),
		'zoom-in' 	=> __("zoom-in", 'mg_ml'),
		'zoom-out' 	=> __("zoom-out", 'mg_ml'),
		'zoom-flip' => __("zoom & flip", 'mg_ml'),
		'skew' 		=> __("skew", 'mg_ml'),
		
		'symm_vert' => __("symmetrical vertical", 'mg_ml'),
		'symm_horiz' => __("symmetrical horizontal", 'mg_ml'),
		
		'genie_t_side' => __("genie | top side", 'mg_ml'),
		'genie_r_side' => __("genie | right side", 'mg_ml'),
		'genie_b_side' => __("genie | bottom side", 'mg_ml'),
		'genie_l_side' => __("genie | left side", 'mg_ml'),
		
		'slide_corn_tr' => __("slide | top-right corner", 'mg_ml'),
		'slide_corn_br' => __("slide | bottom-right corner", 'mg_ml'),
		'slide_corn_bl' => __("slide | bottom-left corner", 'mg_ml'),
		'slide_corn_tl' => __("slide | top-left corner", 'mg_ml'),
		
		'slide_t_side' => __("slide | top side", 'mg_ml'),
		'slide_r_side' => __("slide | right side", 'mg_ml'),
		'slide_b_side' => __("slide | bottom side", 'mg_ml'),
		'slide_l_side' => __("slide | left side", 'mg_ml'),
	);	
	
	return $opts;
}


// lightbox slider thumbs nav opts
function mg_lb_thumb_nav_mode($type = false) {
	$types = array(
		'always'	=> __('Always shown', 'mg_ml'),
        'yes' 		=> __('Show with toggle button', 'mg_ml'),
        'no' 		=> __('Hide with toggle button', 'mg_ml'),
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// easings
function mg_easings() {
	$opts = array(
		'ease' => __("ease", 'mg_ml'),
		'linear' => __("linear", 'mg_ml'),
		'ease-in' => __("ease-in", 'mg_ml'),
		'ease-out' => __("ease-out", 'mg_ml'),
		'ease-in-out' => __("ease-in-out", 'mg_ml'),
		'ease-in-back' => __("ease-in-back", 'mg_ml'),
		'ease-out-back' => __("ease-out-back", 'mg_ml'),
		'ease-in-out-back' => __("ease-in-out-back", 'mg_ml')
	);	
	
	return $opts;
}

