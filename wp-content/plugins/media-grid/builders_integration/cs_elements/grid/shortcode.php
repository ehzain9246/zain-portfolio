<?php

/**
 * Shortcode handler
 */

if(!isset($id)) 		{$id = '';}
if(!isset($class)) 		{$class = '';}
if(!isset($style)) 		{$style = '';}
if(!isset($overlay)) 	{$overlay = '';}
 
 
cs_atts( array('id' => $id, 'class' => $class, 'style' => $style ) );

$atts = array(
	'gid' 			=> $gid,
	'title_under' 	=> $title_under,
	'pag_sys'		=> $pag_sys,
	'search'		=> $search,
	'filter' 		=> $filter,
	'filters_align' => $filters_align,
	'hide_all' 		=> $hide_all,
	'def_filter' 	=> $def_filter,
	'mobile_tresh'	=> $mobile_tresh,
	
	'cell_margin'	=> $cell_margin,
	'border_w'		=> $border_w,
	'border_col'	=> $border_col,
	'border_rad'	=> $border_rad,
	'outline'		=> $outline,
	'outline_col'	=> $outline_col,
	'shadow'		=> $shadow,
	'txt_under_col'	=> $txt_under_col,
	
	'overlay'		=> $overlay
);

$params = '';
foreach($atts as $key => $val) {
	$params .= ' '. $key .'="'. esc_attr($val) .'"';
}

echo do_shortcode('[mediagrid '. $params .']');
