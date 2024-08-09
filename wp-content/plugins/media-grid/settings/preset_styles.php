<?php
// ARRAY CONTAINING OPTION VALUES TO SETUP PRESET STYLES


// preset style names
function mg_preset_style_names() {
	$ml_key = 'mg_ml';
	
	return array(
		'l_standard'	=> __("Light", $ml_key) .' - '. __('Standard', $ml_key),
		'l_minimal'		=> __("Light", $ml_key) .' - '. __('Minimal', $ml_key),
		'l_noborder'	=> __("Light", $ml_key) .' - '. __('No border', $ml_key),
		'l_photowall'	=> __("Light", $ml_key) .' - '. __('Photo wall', $ml_key),
		'l_txt_under'	=> __("Light", $ml_key) .' - '. __('Made for text under items', $ml_key),	
		
		'd_standard'	=> __("Dark", $ml_key) .' - '. __('Standard', $ml_key),
		'd_minimal'		=> __("Dark", $ml_key) .' - '. __('Minimal', $ml_key),
		'd_noborder'	=> __("Dark", $ml_key) .' - '. __('No border', $ml_key),
		'd_photowall'	=> __("Dark", $ml_key) .' - '. __('Photo wall', $ml_key),
		'd_txt_under'	=> __("Dark", $ml_key) .' - '. __('Made for text under items', $ml_key),		
	);			
}


// option values to apply
function mg_preset_styles_data($style = '') {
	$styles = array();
	
	
	/*** LIGHTS ***/
	$styles['l_standard'] = array(
		'mg_cells_margin' => 7,
		'mg_cells_img_border' => 6,
		'mg_cells_radius' => 1,
		'mg_cells_border' => 0,
		'mg_cells_shadow' => 1,
		'mg_item_radius' => 4,
		'mg_lb_border_w' => 3,
		'mg_item_radius' => 3, 
		'mg_filter_n_search_border_w' => 2,
		
		'mg_loader_color' => '#888888',
		'mg_img_border_color' => '#ffffff',
		'mg_img_border_opacity' => 100,
		'mg_main_overlay_color' => '#FFFFFF',
		'mg_main_overlay_opacity' => 80,
		'mg_second_overlay_color' => '#555555',
		'mg_icons_col' => '#ffffff',
		'mg_overlay_title_color' => '#222222',
		'mg_txt_under_color' => '#333333',
		
		'mg_filters_txt_color' => '#666666', 
		'mg_filters_bg_color' => '#ffffff',
		'mg_filters_border_color' => '#bbbbbb', 
		'mg_filters_txt_color_h' => '#535353', 
		'mg_filters_bg_color_h' => '#fdfdfd', 
		'mg_filters_border_color_h' => '#777777',
		'mg_filters_txt_color_sel' => '#333333', 
		'mg_filters_bg_color_sel' => '#efefef', 
		'mg_filters_border_color_sel' => '#aaaaaa',
		
		'mg_search_txt_color' => '#666666', 
		'mg_search_bg_color' => '#ffffff',
		'mg_search_border_color' => '#bbbbbb',
		'mg_search_txt_color_h' => '#333333', 
		'mg_search_bg_color_h' => '#fdfdfd',
		'mg_search_border_color_h' => '#aaaaaa',
		
		'mg_pag_txt_col' => '#666666', 
		'mg_pag_bg_col' => '#ffffff',
		'mg_pag_border_col' => '#bbbbbb',
		'mg_pag_txt_col_h' => '#333333', 
		'mg_pag_bg_col_h' => '#efefef',
		'mg_pag_border_col_h' => '#aaaaaa',
		
		'mg_lb_loader_radius' => 10,
		'mg_lb_border_w' => 5,
		'mg_lb_padding' => 20,
		'mg_lb_shadow' => 'heavy',
		'mg_lb_inner_cmd_boxed' => 1,
		'mg_lb_socials_style' => 'squared',
		'mg_item_overlay_color' => '#fdfdfd',
		'mg_item_overlay_opacity' => 80,
		'mg_item_bg_color' => '#FFFFFF',
		'mg_item_border_color' => '#e2e2e2',
		'mg_item_txt_color' => '#323232',
		'mg_item_icons_color' => '#7a7a7a',
		'mg_item_cmd_bg' => '#f6f6f6',
		'mg_item_hr_color' => '#d4d4d4'
	);
	
	
	$styles['l_minimal'] = mg_ps_override_indexes($styles['l_standard'], array(
		'mg_cells_radius' => 2,
		'mg_cells_border' => 1,
		'mg_cells_shadow' => 0,
		'mg_item_radius' => 0,
		'mg_filter_n_search_border_w' => 1,
		'mg_cells_border_color' => '#CECECE',
		'mg_img_border_opacity' => 0,
		
		'mg_lb_loader_radius' => 2,
		'mg_lb_border_w' => 2,
		'mg_lb_shadow' => 'soft',
		'mg_lb_inner_cmd_boxed' => 0,
		'mg_lb_socials_style' => 'minimal',
		'mg_item_overlay_opacity' => 60,
		'mg_item_border_color' => '#bbbbbb',
	));
	

	$styles['l_noborder'] = mg_ps_override_indexes($styles['l_standard'], array(
		'mg_cells_margin' => 10,
		'mg_cells_img_border' => 0,
		'mg_cells_radius' => 3,
		'mg_cells_border' => 0,
		'mg_item_radius' => 0,
		'mg_filter_n_search_border_w' => 0,
		
		'mg_filters_txt_color' => '#606060', 
		'mg_filters_bg_color' => '#f5f5f5',
		'mg_filters_txt_color_h' => '#4a4a4a', 
		'mg_filters_bg_color_h' => '#fafafa', 
		'mg_filters_txt_color_sel' => '#333333', 
		'mg_filters_bg_color_sel' => '#dfdfdf', 
		
		'mg_search_txt_color' => '#606060', 
		'mg_search_bg_color' => '#f5f5f5',
		'mg_search_txt_color_h' => '#333333', 
		'mg_search_bg_color_h' => '#eeeeee',
		
		'mg_lb_loader_radius' => 5,
		'mg_lb_border_w' => 0,
		'mg_lb_socials_style' => 'rounded',
	));
	
	
	$styles['l_photowall'] = mg_ps_override_indexes($styles['l_noborder'], array(
		'mg_cells_margin' => 0,
		'mg_cells_radius' => 0,
		'mg_cells_shadow' => 0,
		
		'mg_lb_loader_radius' => 0,
		'mg_lb_border_w' => 0,
		'mg_lb_padding' => 0,
		'mg_lb_inner_cmd_boxed' => 0,
		'mg_lb_shadow' => 'soft',
		'mg_lb_socials_style' => 'minimal',
	));
	
	
	$styles['l_txt_under'] = mg_ps_override_indexes($styles['l_minimal'], array(
		'mg_img_border_color' => '#ffffff',
		'mg_img_border_opacity' => 100,
		
		'mg_second_overlay_color' => '#dfdfdf',
		'mg_icons_col' => '#646464',
	));
	
	
	
	
	/*** DARKS ***/
	$styles['d_standard'] = array(
		'mg_cells_margin' => 7,
		'mg_cells_img_border' => 6,
		'mg_cells_radius' => 1,
		'mg_cells_border' => 0,
		'mg_cells_shadow' => 1,
		'mg_item_radius' => 4,
		'mg_lb_border_w' => 3,
		'mg_item_radius' => 3, 
		'mg_filter_n_search_border_w' => 2,
		
		'mg_loader_color' => '#dddddd',
		'mg_cells_border_color' => '#999999',
		'mg_img_border_color' => '#666666',
		'mg_img_border_opacity' => 100,
		'mg_main_overlay_color' => '#2f2f2f',
		'mg_main_overlay_opacity' => 80,
		'mg_second_overlay_color' => '#7f7f7f',
		'mg_icons_col' => '#fefefe',
		'mg_overlay_title_color' => '#ffffff',
		'mg_txt_under_color' => '#fdfdfd',
		
		'mg_filters_txt_color' => '#eeeeee', 
		'mg_filters_bg_color' => '#4f4f4f',
		'mg_filters_border_color' => '#4f4f4f', 
		'mg_filters_txt_color_h' => '#ffffff', 
		'mg_filters_bg_color_h' => '#585858', 
		'mg_filters_border_color_h' => '#777777',
		'mg_filters_txt_color_sel' => '#f3f3f3', 
		'mg_filters_bg_color_sel' => '#6a6a6a', 
		'mg_filters_border_color_sel' => '#6a6a6a',
		
		'mg_search_txt_color' => '#eeeeee', 
		'mg_search_bg_color' => '#4f4f4f',
		'mg_search_border_color' => '#4f4f4f',
		'mg_search_txt_color_h' => '#f3f3f3', 
		'mg_search_bg_color_h' => '#6a6a6a',
		'mg_search_border_color_h' => '#6a6a6a',
		
		'mg_pag_txt_col' => '#eeeeee', 
		'mg_pag_bg_col' => '#4f4f4f',
		'mg_pag_border_col' => '#4f4f4f',
		'mg_pag_txt_col_h' => '#f3f3f3', 
		'mg_pag_bg_col_h' => '#6a6a6a',
		'mg_pag_border_col_h' => '#6a6a6a',
		
		'mg_lb_loader_radius' => 10,
		'mg_lb_border_w' => 5,
		'mg_lb_padding' => 20,
		'mg_lb_shadow' => 'heavy',
		'mg_lb_inner_cmd_boxed' => 1,
		'mg_lb_socials_style' => 'squared',
		'mg_item_overlay_color' => '#222222',
		'mg_item_overlay_opacity' => 85,
		'mg_item_bg_color' => '#3e3e3e',
		'mg_item_border_color' => '#5e5e5e',
		'mg_item_txt_color' => '#fbfbfb',
		'mg_item_icons_color' => '#f1f1f1',
		'mg_item_cmd_bg' => '#484848',
		'mg_item_hr_color' => '#686868', 
	);
	
	
	$styles['d_minimal'] = mg_ps_override_indexes($styles['d_standard'], array(
		'mg_cells_radius' => 2,
		'mg_cells_border' => 1,
		'mg_cells_shadow' => 0,
		'mg_item_radius' => 0,
		'mg_filter_n_search_border_w' => 1,
		'mg_cells_border_color' => '#666666',
		'mg_img_border_opacity' => 0,
		
		'mg_lb_loader_radius' => 2,
		'mg_lb_border_w' => 2,
		'mg_lb_shadow' => 'soft',
		'mg_lb_inner_cmd_boxed' => 0,
		'mg_lb_socials_style' => 'minimal',
		'mg_item_overlay_opacity' => 70,
		'mg_item_border_color' => '#7e7e7e',
	));
	
	
	$styles['d_noborder'] = mg_ps_override_indexes($styles['d_standard'], array(
		'mg_cells_margin' => 10,
		'mg_cells_img_border' => 0,
		'mg_cells_radius' => 3,
		'mg_cells_border' => 0,
		'mg_item_radius' => 0,
		'mg_filter_n_search_border_w' => 0,
		'mg_filters_border_color_h' => '#585858',
		
		'mg_lb_loader_radius' => 5,
		'mg_lb_border_w' => 0,
		'mg_lb_socials_style' => 'rounded',
	));

	
	$styles['d_photowall'] = mg_ps_override_indexes($styles['d_noborder'], array(
		'mg_cells_margin' => 0,
		'mg_cells_radius' => 0,
		'mg_cells_shadow' => 0,
		
		'mg_lb_loader_radius' => 0,
		'mg_lb_border_w' => 0,
		'mg_lb_padding' => 0,
		'mg_lb_inner_cmd_boxed' => 0,
		'mg_lb_shadow' => 'soft',
		'mg_lb_socials_style' => 'minimal',
	));

	
	$styles['d_txt_under'] = mg_ps_override_indexes($styles['d_minimal'], array(
		'mg_img_border_color' => '#4f4f4f',
		'mg_img_border_opacity' => 100,
		
		'mg_second_overlay_color' => '#636363',
		'mg_icons_col' => '#fdfdfd',
	));	

		
	if(empty($style)) {return $styles;}
	else {
		return (isset($styles[$style])) ? $styles[$style] : false;
	}	
}




// override only certain indexes to write less code
function mg_ps_override_indexes($array, $to_override) {
	foreach($to_override as $key => $val) {
		$array[$key] = $val;	
	}
	
	return $array;
}

