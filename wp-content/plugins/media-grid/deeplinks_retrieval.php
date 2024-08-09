<?php
// scan page's URL and setup media grid deeplinked values in a global variable 

add_action('wp_body_open', 'mg_deeplinks_retrieval');
add_action('template_redirect', 'mg_deeplinks_retrieval');

function mg_deeplinks_retrieval() {
	if(isset($GLOBALS['mg_deeplinks'])) {
        return true;
    }
    
    $GLOBALS['mg_deeplinks'] = array();
	$to_use = array();
	
	$curr_url = urldecode(mg_static::curr_url());
	$dl_vars = array('mgi_', 'mgc_', 'mgp_', 'mgs_');
	
	// MG-FILTER - allow custom deeplink parameters to be setup in $GLOBALS['mg_deeplinks']
	$dl_vars = apply_filters('mg_dl_vars', $dl_vars);
	
	
	// if has no query vars - stop here
	if(strpos($curr_url, '?') === false) {
        return false;
    }

	$raw_url_arr = explode('?', $curr_url);
	$qvars = explode('&', $raw_url_arr[1]); 
	
	// detect MG-related part
	$found_qvars = array();
	foreach($dl_vars as $dlv) {
		
		foreach($qvars as $part) {
			if(strpos($part, $dlv) !== false) {
				$raw = explode('=', $part);
				if(count($raw) == 1) {continue;}
				
				$val_arr 	= explode('/', $raw[1]);
				$found_qvars[ $raw[0] ] = $val_arr[0]; 	
			}
		}
	}
	
	// if none is found - stop here
	if(empty($found_qvars)) {
        return false;
    }

	
	// refine and populate global 
	foreach($found_qvars as $qv => $val) {
		$qv_arr = explode('_', $qv);
		
		if($qv_arr[0] == 'mgi') {
			$to_use['mgi'] = array(
				'grid_id' => (empty($qv_arr[1])) ? 0 : $qv_arr[1],
				'item_id' => $val
			); 
		} 
		else {
			
			// allow params having underscores
			if(count($qv_arr) > 2) {
				$end = end($qv_arr);
				unset( $qv_arr[ (count($qv_arr) - 1) ] );
					
				$qv_arr = array(
					implode('_', $qv_arr),
					$end
				);
			}
			
			
			if(!isset($to_use[ 'gid_'.$qv_arr[1] ])) {
				$to_use[ 'gid_'.$qv_arr[1] ] = array();	
			}
			
			$to_use[ 'gid_'.$qv_arr[1] ][ $qv_arr[0] ] = $val;	
		}
			
	}
    
	$GLOBALS['mg_deeplinks'] = $to_use;
}
