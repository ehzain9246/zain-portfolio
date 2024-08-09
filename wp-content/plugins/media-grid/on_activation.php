<?php
// EXTRA ACTIONS ON PLUGIN'S ACTIVATION



//// save existing grids in term description (for versions < 3.0)
// use hook - on activation doesn't get custom taxonomy
function mg_update_grids_location() {
	if(get_option('mg_v3_update')) {
		return true;
	}
	$grids = get_terms('mg_grids', 'hide_empty=0');

	foreach($grids as $grid) {
		$items = get_option('mg_grid_'.$grid->term_id.'_items');
		$w = get_option('mg_grid_'.$grid->term_id.'_items_width');
		$h = get_option('mg_grid_'.$grid->term_id.'_items_height');
		$cats = get_option('mg_grid_'.$grid->term_id.'_cats');
		
		// create description array
		$arr = array('items' => array(), 'cats' => $cats);	
		if(is_array($items)) {
			for($a=0; $a < count($items); $a++) {
				if(!$w) {
					$cell_w = get_post_meta($items[$a], 'mg_width', true);
					$cell_h = get_post_meta($items[$a], 'mg_height', true);
				}
				else {
					$cell_w = $w[$a];
					$cell_h = $h[$a];	
				}
				
				$arr['items'][] = array(
					'id'	=> $items[$a],
					'w' 	=> $cell_w,
					'h' 	=> $cell_h,
					'm_w' 	=> (in_array($cell_w, mg_static::mobile_sizes())) ? $cell_w : '1_2',
					'm_h' 	=> (in_array($cell_h, mg_static::mobile_sizes()) || $cell_h == 'auto') ? $cell_h : '1_3'
				);
			}
		}
		wp_update_term($grid->term_id, 'mg_grids', array('description' => serialize($arr)));
	}
	update_option('mg_v3_update', 1);
}
add_action('admin_init', 'mg_update_grids_location', 1);	

