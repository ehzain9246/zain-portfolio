<?php
// create and manage items overlay - with overlay manager add-on integration
class mg_overlay_manager {
	private $preview_mode = false;
	private $title_under = false;
	private $overlay;
	
	// image overlay 
	public $ol_txt_part = '
        <div class="mgi_ol_tit_wrap">
            <span class="mg_overlay_tit">%MG-TITLE-OL%</span>
        </div>';
    
	public $ol_code = '
		<div class="mgi_primary_ol"></div>
		<div class="mgi_item_type_ol"><span></span></div>
	';
	
	// text under images
	public $txt_under_code = '<span class="mg_def_txt_under">%MG-TITLE-OL%</span>';
	
	// image effect attribute
	public $img_fx_attr = '';
	
	// txt visibility trick - classes
	public $txt_vis_class = false;
	
	
	// handle grid global vars
	function __construct($ol_to_use, $title_under, $preview_mode = false) {
		$this->preview_mode = $preview_mode;
		$this->title_under = (!empty($title_under)) ? true : false;
		
		// get the add-on code
		if(!defined('MGOM_DIR') || $ol_to_use == 'default' || !filter_var($ol_to_use, FILTER_VALIDATE_INT)) {
			if(defined('MGOM_DIR')) {
				$global_ol = get_option('mg_default_overlay');
				$overlay = (empty($global_ol)) ? 'default' : (int)$global_ol;
			}
			else {
                $overlay = 'default';
            }
		} 
		else {
			$overlay = (!defined('MGOM_DIR')) ? 'default' : (int)$ol_to_use;	
		}
        
        // MF-FILTER - allow custom overlay usage 
		$this->overlay = apply_filters('mg_grid_overlay_id', $overlay);
		
		if($overlay != 'default') {
			$this->txt_under_code = '<div class="mgi_txt_under">%MG-TITLE-OL%</div>';
			$this->get_om_code($overlay);
		}
	}
	
	
	// get the add-on overlay code
	private function get_om_code($overlay_id) {
			
		if(function_exists('mgom_ol_frontend_code')) {
			$code = mgom_ol_frontend_code($overlay_id, $this->title_under);	

			$this->ol_code = $code['graphic'];
			$this->img_fx_attr = $code['img_fx_elem'];
			$this->txt_vis_class = $code['txt_vis_class'];
			
			if($this->title_under) {
				$this->txt_under_code = $code['txt'];
			} else {
				$this->ol_txt_part = $code['txt'];	
			}
		} 
	}
	
	
	// get the image overlay code
	public function get_img_ol($item_id) {
        
		// MG-FILTER - allow graphical overlay elements management
		$ol_code = apply_filters('mg_graphic_ol_manag', $this->ol_code, $item_id, $this->overlay, $this->preview_mode);	
        
		// if not txt under - execute the text code	
		$txt_part = (!$this->title_under) ? $this->man_txt_part($item_id, $this->ol_txt_part) : '';
		return $ol_code . $txt_part;
	}
	
	
	// get text under image code
	public function get_txt_under($item_id) {
		return '<div class="mgi_txt_under">'. $this->man_txt_part($item_id, $this->txt_under_code) .'</div>';
	}
	
	
	// manage textual part of the overlay (both for normal and text under
	//// $raw_txt = overlay text with placeholders
	private function man_txt_part($item_id, $raw_txt) {
        
        // MG-FILTER - allow textual overlay elements management
		$txt = apply_filters('mg_txt_ol_manag', $raw_txt, $item_id, $this->overlay, $this->preview_mode);	
		
		// if add-on is not installed - insert title for basic overlay
		if(strpos($txt, '%MG-TITLE-OL%') !== false) {
			$txt =	str_replace('%MG-TITLE-OL%', get_the_title($item_id), $txt);	
		}
		
		return $txt;
	}
	
}