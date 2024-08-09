<?php
// REGISTER GRID BLOCK



// grids array
$grids_arr = array(); 
foreach(get_terms('mg_grids', array('hide_empty' => 0, 'orderby' => 'name')) as $grid) {
	$grids_arr[ $grid->term_id ] = $grid->name;
}


// pagination systems
$pag_sys = array(
	'' => __('default one', 'mg_ml')
);
foreach(mg_static::pag_layouts() as $type => $name) {
	$pag_sys[ $type ] = $name;
}


// MG item categories array (use full list for now)
$def_filter = array(
	'' => __('no initial filter', 'mg_ml')
); 
foreach(mg_static::item_cats() as $cat_id => $cat_name) {
	$def_filter[ $cat_id ] = $cat_name;
}




///// ADVANCED FILTERS ADD-ON //////////
////////////////////////////////////////

$filters = array(
	'0' => __('No'),
	'1' => __('Yes'),
);
if(class_exists('mgaf_static')) {
	$filters = array(
		'0' => __('No', 'mg_ml'),
		'1' => __('Yes (MG categories)', 'mg_ml'),
	) + mgaf_static::filters_list();
}



///// OVERLAY MANAGER ADD-ON ///////////
////////////////////////////////////////


$overlays = array(
	__('default one', 'mg_ml') => ''
);

if(defined('MGOM_DIR')) {	
	register_taxonomy_mgom(); // be sure tax are registered
	$overlay_terms = get_terms('mgom_overlays', 'hide_empty=0');
	
	foreach($overlay_terms as $ol) {
		$overlays[ $ol->term_id ] = $ol->name;	
	}
}



/////////////////////////////////////////////


$panels = array(
	'main' => array(
		'title' 	=> __('Main parameters', 'mg_ml'),
		'opened' 	=> true
	),
	'styling' => array(
		'title' 	=> __('Custom styles', 'mg_ml'),
		'opened' 	=> false
	)
);


// structure
$defaults = array(
	'gid' => array(
		'label'		=> __('Grid', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> array('' => __('Select a grid', 'mg_ml')) + $grids_arr,
		'default' 	=> '',
		'panel'		=> 'main',
	),
	'pag_sys' => array(
		'label'		=> __('Pagination system', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> $pag_sys,
		'default' 	=> '',
		'panel'		=> 'main',
	),
	'filter' => array(
		'label'		=> __('Enable filters?', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> $filters,
		'default' 	=> current(array_keys($filters)),
		'panel'		=> 'main',
	),
	'filters_align' => array(
		'label'		=> __('Filters position', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> array(
			'top' 	=> __('On top', 'mg_ml'),
			'left'	=> __('Left side', 'mg_ml'),
			'right' => __('Right side', 'mg_ml')
		),
		'default' 	=> 'top',
		'panel'		=> 'main',
		
		'condition' => array(
			'filter' => array(
				'=', 
				array('1')
			)
		)
	),
	'def_filter' => array(
		'label'		=> __('Default filter', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> $def_filter,
		'default' => current(array_keys($def_filter)),
		'panel'		=> 'main',
		
		'condition' => array(
			'filter' => array(
				'=', 
				array('1')
			)
		)
	),
	'search' => array(
		'label'		=> __('Enable search?', 'mg_ml'),
		'type'		=> 'checkbox',
		'default' 	=> '',
		'panel'		=> 'main',
		
		'condition' => array(
			'filter' => array(
				'=', 
				array('0', '1')
			)
		)
	),
	'hide_all' => array(
		'label'		=> __('Hide "All" filter?', 'mg_ml'),
		'type'		=> 'checkbox',
		'default' 	=> '',
		'panel'		=> 'main',
		
		'condition' => array(
			'filter' => array(
				'=', 
				array('1')
			)
		)
	),
	'overlay' => array(
		'label'		=> __('Overlay', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> $overlays,
		'default' => current(array_keys($overlays)),
		'panel'		=> 'main',
	),
	'title_under' => array(
		'label'		=> __('Text under items?', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> array(
			0 => __('No', 'mg_ml'),
			1 => __('Yes - attached to item', 'mg_ml'),
			2 => __('Yes - detached from item', 'mg_ml')
		),
		'default' 	=> 0,
		'panel'		=> 'main',
	),
    'mf_lightbox' => array(
		'label'		=> __('Media-focused lightbox mode?', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> array(
			'' => __('as default', 'mg_ml'),
			0  => __('No'),
			1  => __('Yes')
		),
		'default' 	=> '',
		'panel'		=> 'main',
	),
	'mobile_tresh' => array(
		'label'		=> __('Custom mobile threshold', 'mg_ml'),
		'help'		=> __('Overrides global treshold. Use zero to ignore', 'mg_ml'),
		'type'		=> 'slider',
		'min'		=> 0,
		'max'		=> 1000,
		'default' 	=> 0,
		'panel'		=> 'main',
	),
	
	
	'warning1' => array(
		'type'		=> 'warning',
		'html'		=> __('leave empty textual fields to use global values', 'mg_ml'),
		'panel'		=> 'styling',
		'default' 	=> '',
	),
	'cell_margin' => array(
		'label'		=> __('Items margin', 'mg_ml') .' (px)',
		'type'		=> 'text', // can't use number + empty values because of the fu**ing Guten
		'default' 	=> '',
		'panel'		=> 'styling',
	),
	'border_w' => array(
		'label'		=> __('Item borders width', 'mg_ml') .' (px)',
		'type'		=> 'text',
		'default' 	=> '',
		'panel'		=> 'styling',
	),
	'border_col' => array(
		'label'		=> __('Item borders color', 'mg_ml'),
		'type'		=> 'colorpicker',
		'default' 	=> '',
		'panel'		=> 'styling',
	),
	'border_rad' => array(
		'label'		=> __('Items border radius', 'mg_ml') .' (px)',
		'type'		=> 'text',
		'default' 	=> '',
		'panel'		=> 'styling',
	),
	'outline' => array(
		'label'		=> __('Display items outline?', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> array(
			'' => __('as default', 'mg_ml'),
			0  => __('No'),
			1  => __('Yes')
		),
		'default' 	=> '',
		'panel'		=> 'styling',
	),
	'outline_col' => array(
		'label'		=> __('Outline color', 'mg_ml'),
		'type'		=> 'colorpicker',
		'default' 	=> '',
		'panel'		=> 'styling',
	),
	'shadow' => array(
		'label'		=> __('Display items shadow?', 'mg_ml'),
		'type'		=> 'select',
		'opts'		=> array(
			'' => __('as default', 'mg_ml'),
			0  => __('No'),
			1  => __('Yes')
		),
		'default' 	=> '',
		'panel'		=> 'styling',
	),
	'txt_under_col' => array(
		'label'		=> __('Text under images color', 'mg_ml'),
		'type'		=> 'colorpicker',
		'default' 	=> '',
		'panel'		=> 'styling',
	),
);


$defaults = mg_fix_block_defs($defaults);

register_block_type('lcweb/media-grid', array(
	'editor_script' 	=> 'mg_on_guten',
	'render_callback' 	=> 'mg_guten_handler',
	'attributes' 		=> $defaults
));





wp_localize_script('wp-blocks', 'mg_panels', $panels);
wp_localize_script('wp-blocks', 'mg_defaults', $defaults);
