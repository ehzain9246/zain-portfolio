<?php

/**
 * Element Controls
 */
 
// be sure tax are registered
include_once(MG_DIR .'/admin_menu.php'); 
register_taxonomy_mg_grids();
register_cpt_mg_item();


// grids array
$grids_arr = array(); 
foreach(get_terms('mg_grids', array('hide_empty' => 0, 'orderby' => 'name')) as $grid) {
	$grids_arr[] = array(
		'value' => $grid->term_id,
		'label' => $grid->name
	);
}


// pagination systems
$pag_sys = array(
	0 => array(
		'value' => '',
		'label' => __('default one', 'mg_ml')
	)
);
foreach(mg_static::pag_layouts() as $type => $name) {
	$pag_sys[] = array(
		'value' => $type,
		'label' => $name
	);
}
	

// filters array (use full list for now)
$filters_arr = array(
	0 => array(
		'value' => '',
		'label' => __('no initial filter', 'mg_ml')
	)
); 
foreach(mg_static::item_cats() as $cat_id => $cat_name) {
	$filters_arr[] = array(
		'value' => $cat_id,
		'label' => $cat_name
	);
}
 
 


/* FIELDS */
$fields =  array(
	'gid' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Grid', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => $grids_arr
		),
	),

	'title_under' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Text under items?', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => array(
				array('value' => 0, 'label' => __('No', 'mg_ml')),
				array('value' => 1, 'label' => __('Yes - attached to item', 'mg_ml')),
				array('value' => 2, 'label' => __('Yes - detached from item', 'mg_ml')),
			)
		),
	),
	
	'pag_sys' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Pagination system', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => $pag_sys
		),
	),

	'search' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __('Enable search?', 'mg_ml'),
			'tooltip' => __('Enables search bar for grid items', 'mg_ml'),
		),
	),

	
	/************************/
	'filter' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __('Enable filters?', 'mg_ml'),
			'tooltip' => __('Allows items filtering by category', 'mg_ml'),
		),
	),

	'filters_align' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Filters position', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => array(
				array('value' => 'top', 'label' => __('on top', 'mg_ml')),
				array('value' => 'left', 'label' => __('left side', 'mg_ml')),
				array('value' => 'right', 'label' => __('right side', 'mg_ml')),
			)
		),
	),
	
	'hide_all' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __('Hide "All" filter?', 'mg_ml'),
			'tooltip' => __('Hides the "All" option from filters', 'mg_ml'),
		),
	),
	
	'def_filter' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Default filter', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => $filters_arr
		),
	),
	/***********************/

	'mobile_tresh' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Custom mobile threshold (in pixels)', 'mg_ml'),
			'tooltip' => __('Overrides global threshold. Leave empty to ignore', 'mg_ml'),
		),
	),


	
	/*** STYLING ***/
	'cell_margin' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Items margin', 'mg_ml'),
			'tooltip' => __('Leave empty to use default value', 'mg_ml'),
		),
	),
	'border_w' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Items border width', 'mg_ml'),
			'tooltip' => __('Leave empty to use default value', 'mg_ml'),
		),
	),
	'border_col' => array(
		'type'    => 'color',
		'ui' => array(
			'title'   => __('Items border color', 'mg_ml'),
			'tooltip' => __('Leave empty to use default value', 'mg_ml'),
		),
	),
	'border_rad' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Items border radius', 'mg_ml'),
			'tooltip' => __('Leave empty to use default value', 'mg_ml'),
		),
	),
	'outline' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __("Display items outline?", 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => array(
				array('value' => '', 'label' => __('As default', 'mg_ml')),
				array('value' => 1, 'label' => __('Yes', 'mg_ml')),
				array('value' => 0, 'label' => __('No', 'mg_ml')),
			)
		),
	),
	'outline_col' => array(
		'type'    => 'color',
		'ui' => array(
			'title'   => __('Outline color', 'mg_ml'),
			'tooltip' => __('Leave empty to use default value', 'mg_ml'),
		),
	),
	'shadow' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __("Display items shadow?", 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => array(
				array('value' => '', 'label' => __('As default', 'mg_ml')),
				array('value' => 1, 'label' => __('Yes', 'mg_ml')),
				array('value' => 0, 'label' => __('No', 'mg_ml')),
			)
		),
	),
	'txt_under_col' => array(
		'type'    => 'color',
		'ui' => array(
			'title'   => __('Text under images color', 'mg_ml'),
			'tooltip' => __('Leave empty to use default value', 'mg_ml'),
		),
	),
);



///// OVERLAY MANAGER ADD-ON ///////////
if(defined('MGOM_DIR')) {
	register_taxonomy_mgom(); // be sure tax are registered
	$overlays = get_terms('mgom_overlays', 'hide_empty=0');
	
	$ol_arr = array(
		0 => array(
			'value' => '',
			'label' => __('default one', 'mg_ml')
		)
	);
	foreach($overlays as $ol) {
		$ol_arr[] = array(
			'value' => $ol->term_id,
			'label' => $ol->name
		);
	}
	
	$fields['overlay'] = array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Custom Overlay', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => $ol_arr
		),
	);
}

return $fields;
