<?php
// INITIALIZE GUTEN BLOCKS AND DEFINE HANDLERS


// register blocks
function mg_guten_register() {
	if(!function_exists('register_block_type')) {
        return;
    }
	
	include_once(MG_DIR .'/builders_integration/guten_elements/grid.php');	
}
add_action('init', 'mg_guten_register');





// enqueue scripts in gutenberg 
function mg_guten_scripts() {
    global $current_screen;
    
    $deps = array(
        'wp-blocks',
        'wp-i18n',
        'wp-element',
    );  
    if($current_screen->base != 'widgets') {
        $deps[] = 'wp-editor';     
    }
    
    
	wp_enqueue_script(
		'lc_guten_toolkit',
		MG_URL .'/builders_integration/guten_elements/common.js',
		$deps,
		'1.2.3',
		true
	);
	
	
	wp_enqueue_script(
		'lcweb/media-grid',
		MG_URL .'/builders_integration/guten_elements/grid.js',
		$deps,
		MG_VER, 
		true
	);
	
	
	// hook for additional scripts
	if(!did_action('lc_guten_scripts')) {
		$GLOBALS['lc_guten_scripts'] = true;
		do_action('lc_guten_scripts');
	}
}
add_action('enqueue_block_editor_assets', 'mg_guten_scripts');






// hook for custom scripts in gutenberg head
if(!function_exists('lc_scripts_in_guten_head')) {
	function lc_scripts_in_guten_head() {
		do_action('lc_scripts_in_guten_head');
	}
	add_action('admin_head', 'lc_scripts_in_guten_head', 999);
}







// remote handler for ServerSideRender blocks
function mg_guten_handler($atts) {
	$code = '';
	
	if(get_option('mg_inline_css') ||get_option('mg_force_inline_css')) {
		ob_start();
		
		mg_inline_css();
		if(function_exists('mgom_inline_css')) {
			mgom_inline_css();	
		}
		$code .= ob_get_clean();
	}

	// compile atts
	$compiled = array();
	foreach($atts as $key => $val) {
		$compiled[] = $key .'="'. esc_attr($val) .'"'; 	
	}
	
	return $code . do_shortcode('[mediagrid '. implode(' ', $compiled) .']');
}
    




// fixes WP > 5.5 fields type declaration deprecation 
function mg_fix_block_defs($array) {
    foreach($array as $fid => $fdata) {
        $array[$fid]['lc_type'] = $array[$fid]['type'];
        $array[$fid]['type'] = (in_array($array[$fid]['type'], array('number', 'slider'))) ? 'number' : 'string';
    }
    
    return $array;
}




// fixing Gutenberg front rendering issue with ToggleControl field
function mg_guten_ToggleControl_val_fix($parsed_block) {
    if(isset($parsed_block['blockName']) && strpos($parsed_block['blockName'], 'lcweb/') !== false && isset($parsed_block['attrs']) && is_array($parsed_block['attrs'])) {
        
        foreach($parsed_block['attrs'] as $key => $val) {
  
            if($val === true) {
                $parsed_block['attrs'][$key] = '1';    
            }
            elseif($val === false) {
                $parsed_block['attrs'][$key] = '';    
            }        
        } 
    }
    
    return $parsed_block;
}
add_filter('render_block_data', 'mg_guten_ToggleControl_val_fix', 10);
