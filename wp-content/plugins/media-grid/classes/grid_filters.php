<?php
// managing filters to be printed in grids

class mg_grid_filters {
	
	protected $grid_id;
	protected $filter_rules = array(); 		// (array) associative array of filter rules set in shortcode (eg. alignment, def_filter, hide_all)
	protected $grid_terms = array();	 	// (array) containing terms wrapped from all items to be used as filters
	protected $grid_manag_terms = array();	// (array) associative array containing sorted terms for the grid and fetching associated terms icon
	
	protected $all_txt = 'All'; // (string) the "ALL" filter text
	protected $dl_filter = ''; 	// (int/empty) deeplinked filter's ID
	
	public $applied_filter = ''; // public reference to know which filter has been applied on grid showing
	public $filters_align; // public reference to know which is filteres alignment (useful to allow filters) 
	
	
	/* setup rules and categories to be used in the grid */
	public function __construct($grid_id, $filter_rules, $grid_terms) {
		
		$this->grid_id        = $grid_id;
		$this->filter_rules   = $filter_rules;
		$this->grid_terms     = $grid_terms;	
		$this->all_txt        = get_option('mg_all_filter_txt', __('All', 'mg_ml'));
        $this->filters_align  = $filter_rules['align'];
		
		// check for deeplinked selection
		if(isset($GLOBALS['mg_deeplinks']) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgc'])) {
			$this->dl_filter = $GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgc'];
		}	
			
		// security check		
		if(!isset($GLOBALS['mg_items_term_db'])) {
			$GLOBALS['mg_items_term_db'] = array();
		} 
	}
	
	
	/* 
	 * return filters code - ready to be used in grids 
	 * @param (array) $grid_atts - grid shortcode atts
	 */
	public function get_filters_code($grid_atts) {
		
		///// ADVANCED FILTERS ADD-ON /////////
		/////////////////////////////////////// if add-on filter is used - apply its code
		
		if($grid_atts['filter'] != 1) {
			
			// allows filters align override - passes align, filter ID, grid ID, grid atts 
			$this->filters_align = apply_filters('mg_filters_align', $this->filter_rules['align'], $grid_atts['filter'], $this->grid_id, $grid_atts);
			$this->filter_rules['align'] = $this->filters_align;
			 
			// override MG filters code 
			$code = apply_filters('mg_custom_filters_code', '', $this->grid_terms, $this->grid_id, $this->dl_filter, $grid_atts);
			return $code; 
		}
		
		///////////////////////////////////////
		
		
		
		$this->sort_terms();
		if(empty($this->grid_manag_terms)) {
			return '';
		}
		
		
		// filters style class (old/new)
		$style_class = (get_option('mg_use_old_filters')) ? 'mg_textual_filters' : 'mg_button_filters';
		
		// dropdown on mobile class
		$mobile_dd_class = (get_option('mg_dd_mobile_filter')) ? 'mg_dd_mobile_filters' : '';
		
		// "old-style" separator
		$os_separator = (get_option('mg_use_old_filters')) ? '<span class="mg_txt_filter_sep">'. get_option('mg_os_filters_separator', '/') .'</span>' : '';
		
		
		
		// know which filter to apply by default
		$to_match = (!empty($this->dl_filter)) ? $this->dl_filter : $this->filter_rules['def_filter'];
		
		foreach($this->grid_manag_terms as $term) {
			if($term['term_id'] == $to_match) {
				$this->applied_filter = $term['term_id'];	
			}
		}

		
		// create code
		$code = '
		<div class="mgf_'. $this->grid_id .' mg_filters_wrap '. $style_class .' '. $mobile_dd_class .'">
			<div class="mgf_inner">';
		
			// ALL filter
			if(empty($this->filter_rules['hide_all'])) {
				$sel_class = (empty($this->applied_filter) || $this->applied_filter == '*') ? 'mgf_selected' : '';
				$code .= '<a href="javascript:void(0)" class="mgf mgf_all '.$sel_class.'" data-filter-id="*">'. $this->all_txt .'</a>'. $os_separator;	
			}
			
			// create a placeholder to show in dropdown if there's no ALL filter and neither a chosen one
			else {
				if(empty($this->applied_filter) || $this->applied_filter == '*') {
					$code .= '<a href="javascript:void(0)" class="mgf mgf_noall_placeh mgf_selected" data-filter-id="*"><em>.. '. __('no chosen filter', 'mg_ml') .' ..</em></a>';		
				}
			}
			
			
			$a = 1;
			foreach($this->grid_manag_terms as $term) {
				$icon_code = ($term['icon']) ? '<i class="mgf_icon '. mg_static::fontawesome_v4_retrocomp($term['icon']) .'"></i>' : '';	
				$filter_txt = '<span>'. $icon_code.$term['name'] .'</span>';
				
				$sel_class = ($this->applied_filter == $term['term_id']) ? 'mgf_selected' : '';
				$code .= '<a href="javascript:void(0)" class="mgf mgf_'.$term['term_id'].' '.$sel_class.'" data-filter-id="'. $term['term_id'] .'">'. $filter_txt .'</a>';
				
				if($a < count($this->grid_manag_terms)) {
					$code .= $os_separator;
				}
				
				$a++;		
			}
		
		return $code.'
				<a href="javascript:void(0)" class="mgf mgf_dd_height_trick">&nbsp;</a>
			</div>
		</div>';
	}
	
	
	
	
	
	/* prepare grid terms sorting them and fetching order and icon */
	private function sort_terms() {
		global $mg_items_term_db;
		
		// setup additional params to sort and get icon - avoid if already fetched previously
		foreach($this->grid_terms as $term_id) {	
			if(!isset($mg_items_term_db[ $term_id ]['order'])) {
				$mg_items_term_db[ $term_id ]['order'] = (int)mg_static::retrocomp_get_term_meta($term_id, 'mg_cat_order', "mg_cat_". $term_id ."_order", 0);	
				$mg_items_term_db[ $term_id ]['icon'] = mg_static::retrocomp_get_term_meta($term_id, 'mg_cat_icon', "mg_cat_". $term_id ."_icon", '');
			}
			
			$this->grid_manag_terms[$term_id] = $mg_items_term_db[ $term_id ];	
		}
		
		// sort
		$this->grid_manag_terms = $this->sort_grid_terms($this->grid_manag_terms, 'order', SORT_ASC, 'term_id', SORT_ASC);
	}
	
	
	
	/* 
	 * performs a multisort to sort by order and then by ID 
	 * @return (array) sorted terms array (without term_id indexes)
	 */
	private function sort_grid_terms() {
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
				}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		
		return array_pop($args);
	}
	
	
	
	
	
}