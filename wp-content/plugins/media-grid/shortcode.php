<?php
// SHORTCODE DISPLAYING THE GRID

// [mediagrid] 
function mg_shortcode( $grid_atts, $content = null ) {
	
	include_once(MG_DIR .'/classes/items_renderer.php');
	include_once(MG_DIR .'/classes/overlay_manager.php');
	
	$grid_atts = shortcode_atts( array(
		'gid'			=> '',
		'cat' 			=> '',
		'pag_sys'		=> '',
		'filter' 		=> 0,
		'title_under' 	=> 0,
		'filters_align' => 'top',
		'hide_all' 		=> 0,
		'def_filter' 	=> 0,
		'search'		=> 0,
        'mf_lightbox'	=> '',
		'mobile_tresh'	=> 0,
		
		'cell_margin'	=> '',
		'border_w'		=> '',
		'border_col'	=> '',
		'border_rad'	=> '',
		'outline'		=> '',
		'outline_col'	=> '',
		'shadow'		=> '',
		'txt_under_col'	=> '',
		
		'overlay'		=> 'default',
	), $grid_atts);
	extract($grid_atts);


	if(empty($cat) && empty($gid)) {
        return '';
    }
	$grid_id = (empty($cat)) ? (int)$gid : (int)$cat;
	
	$grid_temp_id = uniqid();
	$grid_classes = array('mg_grid_wrap', 'mg_grid_'.$grid_id); 
	$grid_atts['grid_temp_id'] = $grid_temp_id;
	
	// if no filter - be sure position is top to avoid search misplacing
	if(empty($filter)) {
		$filters_align = 'top';
		$grid_atts['filters_align'] = 'top';	
	}
	
    
	// deeplink vars setup
	if(isset($GLOBALS['mg_deeplinks']) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]) ) {
		
		// check for deeplinked page
		$grid_atts['dl_pag'] = (isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgp'])) ? (int)$GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgp'] : false;
		
		// check for deeplinked category
		$grid_atts['dl_cat'] = (isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgc'])) ? (int)$GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgc'] : false;
		
		// check for deeplinked search
		$grid_atts['dl_search'] = (isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgs'])) ? $GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgs'] : false;
	} 
	else {
		$grid_atts['dl_pag'] = $grid_atts['dl_search'] = false;		
	}
	
	
	// overlay manager class
	$ol_man = new mg_overlay_manager($overlay, $title_under);
	
	// items rendered  class
	$ir = new mg_items_renderer($grid_id, $ol_man, $grid_atts);
	if(empty($ir->queried_items)) {
        return ''; // no items - stop here
    } 
    
    
	// store items code - this is REQUIRED to setup other elements such as grid filters
	$items_code = $ir->render_items();

	
	// custom styling codes
	if($cell_margin !== '' || $border_w !== '' || !empty($border_col) || $border_rad !== '' || $outline !== '' || !empty($outline_col) || $shadow !== '' || !empty($txt_under_col)) {
		
		$cs_pre = '.mg_'. $grid_temp_id;
		$cust_styles = '';
		
		if($cell_margin !== '')		{
			$cell_margin = (int)$cell_margin;
			
			$cust_styles .= 
			$cs_pre.' .mg_box { 
				border-width: 0 '. $cell_margin .'px '. $cell_margin .'px 0  !important; 
			}'.
			$cs_pre.'.mg_rtl_mode .mg_box {
				left: calc((15px + '. $cell_margin .'px) * -1) !important;
			}'.
			$cs_pre.' .mg_items_container {
				width: calc(100% + 20px + '. $cell_margin .'px)  !important;
			}'.
			$cs_pre.' .mg_items_container.mg_not_even_w {
				width: calc(100% + 20px '. $cell_margin .'px + 1px)  !important;	
			}';
			
			// override items sizing
			foreach(mg_static::item_sizes() as $key => $data) {
				$cust_styles .= $cs_pre.' .mgis_h_'.$key.' {
                    padding-bottom: calc('. $data['perc'] * 100 .'% - '. round($data['perc'] * 20) .'px - '. $cell_margin .'px) !important;
                }';
			}
			foreach(mg_static::mobile_sizes() as $key => $data) {
				$cust_styles .= $cs_pre.'.mg_mobile_mode .mgis_m_h_'.$key.' {
                    padding-bottom: calc('. $data['perc'] * 100 .'% - '. round($data['perc'] * 20) .'px - '. $cell_margin .'px) !important;
                }';
			}
		}
		if($border_w !== '')  		{
			$cust_styles .= 
				$cs_pre.' .mg_box_inner {
					padding: '. (int)$border_w .'px !important;
				}'.
				$cs_pre.' .mg_grid_wrap:not(.mg_mobile_mode) .mgis_h_auto .mg_inl_txt_media_bg,'.
				$cs_pre.' .mg_mobile_mode .mgis_m_h_auto .mg_inl_txt_media_bg,'.
				$cs_pre.' .mgi_overlays {
					top: '. (int)$border_w .'px !important;
					bottom: '. (int)$border_w .'px !important;
					left: '. (int)$border_w .'px !important;
					right: '. (int)$border_w .'px !important; 
				}';
		}
		if(!empty($border_col)) 		{
			$cust_styles .= $cs_pre.' .mg_box_inner, '.$cs_pre.' .mg_tu_attach .mgi_txt_under {
                background: '. $border_col .' !important;
                }';
		}
		if($border_rad !== '')		{
			$cust_styles .= 
				$cs_pre.' .mg_box_inner,'.
				$cs_pre.' .mg_box .mg_media_wrap,'.
				$cs_pre.' .mgi_overlays,'.
				$cs_pre.' .mg_inl_slider_wrap .lcms_content,'.
				$cs_pre.' .mg_inl_slider_wrap .lcms_nav *,'.
				$cs_pre.' .mg_inl_slider_wrap .lcms_play span {
			  		border-radius: '. (int)$border_rad .'px !important;
				}'.
				$cs_pre.' .mg_tu_attach .mgi_txt_under {
					border-bottom-left-radius: '. (int)$border_rad .'px !important;
					border-bottom-right-radius: '. (int)$border_rad .'px !important;	
				}';
		}
		
		if($outline == 1) {
			$cust_styles .= 
				$cs_pre.' .mg_box_inner {border-width: 1px;}'.
				$cs_pre.' .mg_tu_attach .mgi_txt_under {
					border-width: 0px 1px 1px !important;
					margin-top: -1px !important;
				}'; 
		} elseif($outline !== '') {
			$cust_styles .= $cs_pre.' .mg_box_inner, '.$cs_pre.' .mg_tu_attach .mgi_txt_under {border-width: 0px !important;}';	
			$cust_styles .= $cs_pre.' .mg_tu_attach .mgi_txt_under {margin-top: 0 !important;}';	
		}
		
		if(!empty($outline_col)) 	{
			$cust_styles .= $cs_pre.' .mg_box_inner {border-color: '.$outline_col.' !important;}';
		}	
		
		if($shadow == 1) {
			$cust_styles .= $cs_pre.' .mg_grid_wrap {padding: 4px !important;}';
			$cust_styles .= $cs_pre.' .mg_box:not(.mg_spacer) .mg_box_inner {box-shadow: 0px 0px 4px rgba(25, 25, 25, 0.3) !important;}';
			$cust_styles .= 
				$cs_pre.' .mg_tu_attach .mgi_txt_under {
					box-shadow: 4px 0px 4px -4px rgba(25, 25, 25, 0.3), -4px 0px 4px -4px rgba(25, 25, 25, 0.3), 0 4px 4px -4px rgba(25, 25, 25, 0.3) !important;
				}';	
		} 
		elseif($shadow !== '') {
			$cust_styles .= $cs_pre.' .mg_grid_wrap {padding:0;}';
			$cust_styles .= $cs_pre.' .mg_box_inner, '.$cs_pre.' .mg_tu_attach .mgi_txt_under {box-shadow: none !important;}';	
		}
		
		if(!empty($txt_under_col))	{
			$cust_styles .= $cs_pre.' .mgi_txt_under {color: '.$txt_under_col.' !important;}';
		}	
		
		
		// MG-FILTER - allows grid's custom inilne CSS - acts only if custom styling is applied
		$cust_styles = apply_filters('mg_grid_inl_css', $cust_styles, $grid_atts);
		$cust_styles = '<style type="text/css">'. $cust_styles .'</style>';
	}
	else {
        $cust_styles = '';
    }
	
	
	
	// search code template
	if($search && (!$filter || $filter == 1)) {
		$mgs_has_txt_class = ($grid_atts['dl_search']) ? 'mgs_has_txt' : '';
		
		$search_code = '
		<form id="mgs_'.$grid_id.'" class="mgf_search_form '.$mgs_has_txt_class.'">
			<input type="text" value="'. esc_attr($grid_atts['dl_search']) .'" placeholder="'. esc_attr__('search', 'mg_ml') .' .." autocomplete="off" />
			<i class="fas fa-search"></i>
		</form>';
	} 
	else {
        $search_code = '';
    }

	
	
	// filters management
	if($filter) {
		include_once(MG_DIR .'/classes/grid_filters.php');
		
		$filter_rules = array(
			'align' 	 => $filters_align,
			'def_filter' => $def_filter, 
			'hide_all'	 => $hide_all
		);
		$gf = new mg_grid_filters($grid_id, $filter_rules, $ir->items_term);
		$filters = $gf->get_filters_code($grid_atts);
		
		$filters_align = $grid_atts['filters_align'] = $gf->filters_align;
		
		// filters align class and code composition
		switch($filters_align) {
			case 'left' 	: $grid_classes[] = 'mg_left_filters'; break;	
			case 'right' 	: $grid_classes[] = 'mg_right_filters'; break;	
			default 		: $grid_classes[] = 'mg_top_filters'; break;	
		}
	}
	else {
		$filters = '';
		$grid_classes[] = 'mg_no_filters';
	}


	// deeplinking class
	if(!get_option('mg_disable_dl')) {
        $grid_classes[] = 'mg_deeplink';
    } 
	
	// RTL mode class
	if(get_option('mg_rtl_grid')) {
        $grid_classes[] = 'mg_rtl_mode';
    } 
	
	// search box class
	if($search) {
        $grid_classes[] = 'mg_has_search';
    }
    
    // fullscreen lightbox class
    if($mf_lightbox || $mf_lightbox === '' && get_option('mg_mf_lb_enabled')) {
        $grid_classes[] = 'mg_use_mf_lb';    
    }
	
	
    // has pages class
	$curr_pag = 1;
	if($ir->grid_has_pag) {
        $grid_classes[] = 'mg_has_pag';
    }
    
    
    // grid wrap attributes
    $grid_html_attr = array();
    
	// custom mobile treshold
	if((int)$grid_atts['mobile_tresh']) {
        $grid_html_attr['data-mobile-treshold'] = (int)$grid_atts['mobile_tresh'];
    }

    // filtered items pagination attr
    $grid_html_attr['data-filtered-per-page'] = (isset($ir->grid_data['filtered_per_page']) && !get_option('mg_monopage_filter') && get_option('mg_filters_behav') == 'standard') ? (int)$ir->grid_data['filtered_per_page'] : 0; 

	
	// MG-FILTER - allow custom classes to be applied to grid wrapper - passes already applied classes array and grid atts array (given by shortcode)
	$grid_classes = (array)apply_filters('mg_grid_classes', $grid_classes, $grid_atts);
	
	// MG-FILTER - allow custom attributes to be applied to grid wrapper - must be an associative array (att-name => att-val) 
	$grid_html_attr = (array)apply_filters('mg_grid_atts', $grid_html_attr, $grid_atts);
    
	$atts = '';
	foreach($grid_html_attr as $att => $val) {
		$atts .= ' '. $att .'="'. esc_attr($val) .'"';	
	}

	///////////////////////////
 

	### init
	$grid = $cust_styles. 
	'<div id="'.$grid_temp_id.'" data-grid-id="'.$grid_id.'" class="mg_'.$grid_temp_id.' '. implode(' ', $grid_classes) .'" '. $atts .'>';
		
		
		// SEARCH AND FILTERS WRAPPER
		if(!empty($search_code) || !empty($filters)) {
			$ag_elem_align = (empty($search_code) || empty($filters)) ? 'mg_ag_align_'. get_option('mg_filters_align', 'left') : '';
			
			$grid .= '
			<div class="mg_above_grid mgag_'.$grid_id.' '.$ag_elem_align.'">'. 
				$search_code .
				$filters .	
			'</div>';
		}


		// title under - wrap class
		switch($title_under) {
			case 0 : $tit_under_class = ''; break;
			case 1 : $tit_under_class = 'mg_grid_title_under mg_tu_attach'; break;
			case 2 : $tit_under_class = 'mg_grid_title_under mg_tu_detach'; break;	
		}
		
		
		// "no results" text attribute to be used by css 
		$nores_txt = (!get_option('mg_no_results_txt')) ? __('no results', 'mg_ml') : get_option('mg_no_results_txt');
		$nores_attr = 'data-nores-txt="'. esc_attr($nores_txt) .'"';
		
		
		// items container
		$grid .=
		mg_static::preloader(true) . 
		'<div class="mg_items_container mgic_pre_show '.$tit_under_class.' '.$ol_man->txt_vis_class.'"  data-mg-pag="'.$curr_pag.'" '.$nores_attr.' '.$ol_man->img_fx_attr.'>'. 
			
			$items_code;
			
		// close items container
		$grid .= 
		'</div>';
	


	
		/////////////////////////		
		// PAGINATION BUTTONS
		if(in_array('mg_has_pag', $grid_classes)) {
			$tot_pag = $ir->page;
			
			// layout classes
			$pag_layout = (!empty($pag_sys)) ? $pag_sys : get_option('mg_pag_layout', 'standard');

			switch($pag_layout) {
				case 'standard' 	: $pl_class = 'mg_pag_standard'; break;
				case 'only_num' 	: $pl_class = 'mg_pag_onlynum'; break;
				default 			: $pl_class = 'mg_'.$pag_layout; break;
			}

			// deeplinked page check against tot pages
			if($grid_atts['dl_pag']) {
				$curr_pag = $grid_atts['dl_pag'];
				if($grid_atts['dl_pag'] > $tot_pag) {
                    $curr_pag = $tot_pag;
                }
			}
		
			// compose
			$grid .= '
			<div id="mgp_'.$grid_temp_id.'" class="mg_pag_wrap '. $pl_class .'" data-init-pag="'. $curr_pag .'" data-tot-pag="'. $tot_pag .'">';
				
				// next/prev button types
				if(in_array($pag_layout, array('standard', 'only_num', 'only_arr_dt'))) {
				
					// mid nav - layout code
					if($pag_layout == 'standard') {
						$mid_code = '<div class="mg_nav_mid"><div>'. __('page', 'mg_ml') .' <span>'. $curr_pag .'</span> '. __('of', 'mg_ml') .' '. $tot_pag .'</div></div>';	
					}
					elseif($pag_layout == 'only_num') {
						$mid_code = '<div class="mg_nav_mid"><div><span>'. $curr_pag .'</span> <font>/</font> '. $tot_pag .'</div></div>';	
					}
					else {
						$mid_code = '';
					}
					
					// disabled class management
					$prev_dis = ($curr_pag == 1) ? 'mg_pag_disabled' : '';
					$next_dis = ($curr_pag == $tot_pag) ? 'mg_pag_disabled' : '';
					
					$grid .= '
					<div class="mg_prev_page '.$prev_dis.'" title="'. esc_attr__('previous page', 'mg_ml') .'"><i></i></div>
					'.$mid_code.'
					<div class="mg_next_page '.$next_dis.'" title="'. esc_attr__('next page', 'mg_ml') .'"><i></i></div>';
				}
				
				
				// page buttons
				else if(in_array($pag_layout, array('pag_btn_nums', 'pag_btn_dots'))) {
					for($a=1; $a <= $tot_pag; $a++) {
						$sel = ($a == $curr_pag) ? 'mg_sel_pag' : '';
						$grid .= '<div data-pag="'.$a.'" class="mg_hidden_pb '.$sel.'" title="'. esc_attr__('Go to page', 'mg_ml') .' '.$a.'">'. $a .'</div>'; 	
					}	
				}
				
				
				// infinite scroll
				else {
					$grid .= '<div class="mg_load_more_btn"><i class="fa fa-plus-circle" aria-hidden="true"></i> '. get_option('mg_inf_scroll_btn_txt', esc_html__('Show more', 'mg_ml')) .'</div>';
				}
	
			$grid .= '</div>';
		} // pagination end
	
	
    
        /////////////////////////		
		// FILTERED ITEMS PAGINATION BUTTONS
        if(isset($grid_html_attr['data-filtered-per-page']) && $grid_html_attr['data-filtered-per-page']) {
            // layout classes
			$pag_layout = (!empty($pag_sys)) ? $pag_sys : get_option('mg_pag_layout', 'standard');

			switch($pag_layout) {
				case 'standard' 	: $pl_class = 'mg_pag_standard'; break;
				case 'only_num' 	: $pl_class = 'mg_pag_onlynum'; break;
				default 			: $pl_class = 'mg_'.$pag_layout; break;
			}

			// compose
			$grid .= '
			<div class="mg_fpp_pag_wrap mg_pag_wrap mg_displaynone '.$pl_class.'" data-init-pag="1" data-tot-pag="" data-layout="'.esc_attr($pag_layout) .'">';
				
				// next/prev button types
				if(in_array($pag_layout, array('standard', 'only_num', 'only_arr_dt'))) {
				
					// mid nav - layout code
					if($pag_layout == 'standard') {
						$mid_code = '<div class="mg_nav_mid"><div>'. esc_html__('page', 'mg_ml') .' <span class="mg_fpp_curr_pag">1</span> '. esc_html__('of', 'mg_ml') .' <span class="mg_fpp_tot_pag"></span></div></div>';	
					}
					elseif($pag_layout == 'only_num') {
						$mid_code = '<div class="mg_nav_mid"><div><span class="mg_fpp_curr_pag">1</span> <font>/</font> <span class="mg_fpp_tot_pag"></span></div></div>';	
					}
					else {
						$mid_code = '';
					}

					$grid .= '
					<div class="mg_prev_page" title="'. esc_attr__('previous page', 'mg_ml') .'"><i></i></div>
					'.$mid_code.'
					<div class="mg_next_page" title="'. esc_attr__('next page', 'mg_ml') .'"><i></i></div>';
				}
				
				
				// page buttons
				else if(in_array($pag_layout, array('pag_btn_nums', 'pag_btn_dots'))) {
                    $grid .= '<div data-pag="1" class="mg_hidden_pb mg_sel_pag mg_fpp_pag_btn" title="'. esc_attr__('Go to page', 'mg_ml') .' 1">1</div>'; // print first - rest is cloned via JS
				}
				
				
				// infinite scroll
				else {
					$grid .= '<div class="mg_load_more_btn"><i class="fa fa-plus-circle" aria-hidden="true"></i> '. get_option('mg_inf_scroll_btn_txt', esc_html__('Show more', 'mg_ml')) .'</div>';
				}
	
            $grid .= '</div>';        
        }
	
	
	// grid end
	$grid .= 
	'</div>';



    // MG-FILTER - defining which javascript elements must be ready in the page to execute a grid. Must be anything evaluable through 'typeof() == "undefined"'
    $required_js_things = (array)apply_filters('mg_init_grid_js_requirements', array("jQuery", "mg_init_grid", "Muuri"), $grid_atts);
    $required_js_parts = array();
    
    foreach($required_js_things as $rjst) {
        $required_js_parts[] = 'typeof('. $rjst .') == "undefined"';    
    }
    $required_js_code = implode(' || ', $required_js_parts); 
    

	// js - init grid
	$grid .= '
	<script type="text/javascript">
    (function() { 
        "use strict"; 
    
        const intval = setInterval(() => {
            if('. $required_js_code .') {
                return true;
            }
            else {
                clearTimeout(intval);
                const $ = jQuery;
                
                mg_grid_filters["'.$grid_temp_id.'"] = [];';

                // page filter
                if(in_array('mg_has_pag', $grid_classes)) {
                    $grid .= "
                    mg_grid_filters['".$grid_temp_id."']['mg_pag_'] = {
                        condition 	: 'AND',
                        val 		: [".$curr_pag."]
                    };";
                }

                // category filter
                if($filter == 1 && !empty($gf->applied_filter)) {
                    $grid .= "
                    mg_grid_filters['".$grid_temp_id."']['mgc_'] = {
                        condition 	: 'AND',
                        val 		: [".$gf->applied_filter."]
                    };";	
                }

                // search initial filter
                if(($search || strpos($grid, 'mgf_search_form') !== false) && $grid_atts['dl_search']) {
                    $grid .= "
                    mg_grid_filters['".$grid_temp_id."']['mg_search_res'] = {
                        condition 	: 'AND',
                        val 		: ['']
                    };";	
                }


                // start the engine!
                $grid .= '
                $(window).trigger("mg_pre_grid_init", ["'. $grid_temp_id .'", "'. $grid_id .'"]);

                if(typeof(mg_init_grid) == "function" ) {
                    mg_init_grid("'.$grid_temp_id.'", '.$curr_pag.');
                }

                $(window).trigger("mg_post_grid_init", ["'. $grid_temp_id .'", "'. $grid_id .'"]);
            }
        }, 50);
    })(); 
	</script>';


	// MG-FILTER - ablity to injec/edit grid's HTML code - passes grid ID, the temporary ID and grid and grid atts array (given by shortcode)
	$grid = apply_filters('mg_grid_code', $grid, $grid_id, $grid_temp_id, $grid_atts);
	return str_replace(array("\r", "\n", "\t", "\v"), '', $grid);
}
add_shortcode('mediagrid', 'mg_shortcode');
