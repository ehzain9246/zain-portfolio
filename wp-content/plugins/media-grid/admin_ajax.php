<?php

////////////////////////////////////////////////
////// ITEM TYPE CHANGE - LOAD FIELDS //////////
////////////////////////////////////////////////

function mg_item_meta_fields() {	
	include_once(MG_DIR .'/classes/items_meta_fields.php');
	
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	if(!isset($_POST['item_id']) || !isset($_POST['item_type'])) {
        wp_die('missing data');
    }
	
	$t = $_POST['item_type'];
	$imf = new mg_meta_fields($_POST['item_id'], $t);
	
	echo $imf->get_fields_code();		
	$imf->echo_type_js_code();
	
	wp_die();	
}
add_action('wp_ajax_mg_item_meta_fields', 'mg_item_meta_fields');





////////////////////////////////////////////////
////// MEDIA IMAGE PICKER FOR SLIDERS //////////
////////////////////////////////////////////////

function mg_img_picker() {	
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
    
    $tt_path = MG_TT_URL; 
	
	$page = (!isset($_POST['page'])) ? 1 : (int)$_POST['page'];
	$per_page = (!isset($_POST['per_page'])) ? 15 : (int)$_POST['per_page'];
	
	$search = (isset($_POST['mg_search'])) ? $_POST['mg_search'] : '';
	$img_data = mg_static::library_images($page, $per_page, $search);
	
	echo '<ul>';
	
	if($img_data['tot'] == 0) {
        echo '<p>No images found .. </p>';
    }
	else {
		foreach($img_data['img'] as $img_id) {
			$thumb_data = wp_get_attachment_image_src($img_id, array(90, 90));
			echo '
            <li>
                <figure style="background-image: url(\''. $thumb_data[0] .'\');" rel="'. $img_id .'"></figure>
            </li>';
		}
	}
	
	echo '
	</ul>
	<br class="mg_clearboth" />';
    
    if($img_data['tot_pag'] > 1) {
        echo '
        <table>
            <tr>
                <td>';			
                if($page > 1)  {
                    echo '<input type="button" class="mg_img_pick_back button-secondary" id="slp_'. ($page - 1) .'" name="mgslp_p" value="&laquo; '. esc_attr__('Previous images', 'mg_ml') .'" />';
                }

            echo '</td><td class="mg_aligncent">';

                if($img_data['tot'] > 0 && $img_data['tot_pag'] > 1) {
                    echo '<em>'. __('page', 'mg_ml').' '.$img_data['pag'].' '. __('of', 'mg_ml') .' '.$img_data['tot_pag'].'</em> - <input type="text" size="2" name="mgslp_num" id="mg_img_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('images per page', 'mg_ml') .'</em>';	
                }
                else {
                    echo '<input type="text" size="2" name="mgslp_num" id="mg_img_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('images per page', 'mg_ml') .'</em>';
                }

            echo '</td><td class="mg_alignright">';
                if($img_data['more'] != false)  {
                    echo '<input type="button" class="mg_img_pick_next button-secondary" id="slp_'. ($page + 1) .'" name="mgslp_n" value="'. esc_attr__('Next images', 'mg_ml') .' &raquo;" />';
                }
            echo '</td>
            </tr>
        </table>';
    }
    
	wp_die();
}
add_action('wp_ajax_mg_img_picker', 'mg_img_picker');





///////////////////////////////////////////////////
////// MEDIA IMAGE PICKER - SELECTED RELOAD ///////
///////////////////////////////////////////////////
function mg_sel_img_reload() {	
    if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
    
	$images = (!isset($_POST['images'])) ? array() : $_POST['images'];
	$videos = (!isset($_POST['videos'])) ? array() : $_POST['videos'];
	
	// get the titles and recreate tracks
	$arr = mg_static::existing_sel($images, $videos);
	echo mg_static::sel_slider_img_list($arr);
    
	wp_die();
}
add_action('wp_ajax_mg_sel_img_reload', 'mg_sel_img_reload');





////////////////////////////////////////////////
////// MEDIA AUDIO PICKER  /////////////////////
////////////////////////////////////////////////

function mg_audio_picker() {	
    if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
    
	$page = (!isset($_POST['page'])) ? 1 : (int)$_POST['page'];
	$per_page = (!isset($_POST['per_page'])) ? 15 : (int)$_POST['per_page'];
	
	$search = (isset($_POST['mg_search'])) ? addslashes($_POST['mg_search']) : '';
	$audio_data = mg_static::library_audio($page, $per_page, $search);
	
	echo '<ul>';
	
	if(!$audio_data['tot']) {
        echo '<p>'. __('No audio files found', 'mg_ml') .' .. </p>';
    }
	else {
		foreach($audio_data['tracks'] as $track) {
			echo '
			<li id="mgtp_'.$track['id'].'">
				<div class="mg_audio_icon dashicons-media-audio dashicons"></div>
				<p title="'. esc_attr($track['title']) .'">'. strip_tags($track['title']).'</p>
			</li>';
		}
	}
	
	echo '
	</ul>
	<br class="mg_clearboth" />';
    
    if($audio_data['tot_pag'] > 1) {
        echo '
        <table cellspacing="0" cellpadding="5" border="0" width="100%">
            <tr>
                <td>';			
                if($page > 1)  {
                    echo '<input type="button" class="mg_audio_pick_back button-secondary" id="slp_'. ($page - 1) .'" name="mgslp_p" value="&laquo; '. esc_attr__('Previous tracks', 'mg_ml') .'" />';
                }

            echo '</td><td class="mg_aligncent">';

                if($audio_data['tot'] > 0 && $audio_data['tot_pag'] > 1) {
                    echo '<em>page '.$audio_data['pag'].' '. __('of', 'mg_ml') .' '.$audio_data['tot_pag'].'</em> - <input type="text" size="2" name="mgslp_num" id="mg_audio_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('tracks per page', 'mg_ml') .'</em>';		
                }
                else { 
                    echo '<input type="text" size="2" name="mgslp_num" id="mg_audio_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('tracks per page', 'mg_ml') .'</em>';
                }

            echo '</td><td class="mg_alignright">';
                if($audio_data['more'] != false)  {
                    echo '<input type="button" class="mg_audio_pick_next button-secondary" id="slp_'. ($page + 1) .'" name="mgslp_n" value="'. esc_attr__('Next tracks', 'mg_ml') .' &raquo;" />';
                }
            echo '</td>
            </tr>
        </table>';
    }

	wp_die();
}
add_action('wp_ajax_mg_audio_picker', 'mg_audio_picker');





///////////////////////////////////////////////////
////// MEDIA AUDIO PICKER - SELECTED RELOAD ///////
///////////////////////////////////////////////////
function mg_sel_audio_reload() {	
    if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
    
	$tracks = (!isset($_POST['tracks'])) ? array() : $_POST['tracks'];
	$tracks = mg_static::existing_sel($tracks);
	
	// get the titles and recreate tracks
	$new_tracks = '';
	if(!$tracks) {
		$new_tracks = '<p>'. __('No tracks selected', 'mg_ml') .' .. </p>';
	}
	else {	
		foreach($tracks as $track_id) {
			$title = html_entity_decode(get_the_title($track_id), ENT_NOQUOTES, 'UTF-8');
			
			if($title) {
				$new_tracks .= '
				<li>
					<input type="hidden" name="mg_audio_tracks[]" value="'. $track_id .'" />
					<div class="mg_audio_icon dashicons-media-audio dashicons"></div>
                    
					<span title="remove track"></span>
					<p>'.$title.'</p>
				</li>
				';
			}
		}
	}
	
	echo $new_tracks;
	wp_die();
}
add_action('wp_ajax_mg_sel_audio_reload', 'mg_sel_audio_reload');





///////////////////////////////////////////////////
////// POST CONTENTS - CPT CHANGE -> LOAD TERMS ///
///////////////////////////////////////////////////
function mg_sel_cpt_source() {
    if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
    
	(isset($_POST['cpt'])) ? $cpt = $_POST['cpt'] : wp_die('missing CPT');
	
	echo mg_static::get_taxonomy_terms($cpt, 'html');
	wp_die();
}
add_action('wp_ajax_mg_sel_cpt_source', 'mg_sel_cpt_source');






////////////////////////////////////////////////////////////////////////






////////////////////////////////////////////////
////// CREATE GRID ////////// //////////////////
////////////////////////////////////////////////

function mg_add_grid_term() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	if(!isset($_POST['grid_name'])) {
        wp_die('data is missing');
    }
	$name = $_POST['grid_name'];
	
	$resp = wp_insert_term( $name, 'mg_grids', array(
		'slug' => sanitize_title($name),
		'description' => serialize(array('composition' => 'manual', 'manual_items' => array()))
	));
	
	if(is_array($resp)) {
        wp_die('success');
    }
	else {
		$err_mes = $resp->errors['term_exists'][0];
		wp_die($err_mes);
	}
}
add_action('wp_ajax_mg_add_grid', 'mg_add_grid_term');





////////////////////////////////////////////////
////// LOAD GRID LIST //////////////////////////
////////////////////////////////////////////////

function mg_get_grids_list() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }

	echo mg_static::builder_grids_list();
	wp_die();
}
add_action('wp_ajax_mg_get_grids_list', 'mg_get_grids_list');





////////////////////////////////////////////////
////// CLONE GRID TERM /////////////////////////
////////////////////////////////////////////////

function mg_clone_grid() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	if(!isset($_POST['grid_id']) || !isset($_POST['new_name']) || empty($_POST['new_name'])) {
        wp_die('data is missing');
    }
	
	$grid_id = (int)$_POST['grid_id'];
	$name = $_POST['new_name'];
	
	$to_clone = mg_static::get_grid_data($grid_id);
	if(empty($to_clone) || !is_array($to_clone)) {
		wp_die( __('Source grid not found', 'mg_ml') );	
	}
	
	
	$resp = wp_insert_term($name, 'mg_grids');
	if(is_wp_error($resp)) {
		echo $resp->get_error_message();
	} 
	else {	
		$store = mg_static::save_grid_data($resp['term_id'], $to_clone);
		
		if(is_wp_error($store)) {
			echo $store->get_error_message();
		} 
		else {	
			wp_die('success');
		}
	}
    
    wp_die();
}
add_action('wp_ajax_mg_clone_grid', 'mg_clone_grid');





////////////////////////////////////////////////
////// RENAME GRID TERM ////////////////////////
////////////////////////////////////////////////

function mg_rename_grid() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	if(!isset($_POST['grid_id']) || !isset($_POST['new_name']) || empty($_POST['new_name'])) {
        wp_die('data is missing');
    }

	$grid_id = (int)$_POST['grid_id'];
	$name = $_POST['new_name'];
	
	
	// check if name is already taken
	$check = get_term_by('name', $name, 'mg_grids');
	if($check && $check->term_id != $grid_id) {
		wp_die( __('Another grid has this name', 'mg_ml') );	
	}

	// update
	$args = array(
  		'name' => $name,
	);
	$resp = wp_update_term($grid_id, 'mg_grids', $args);
	
	if(is_wp_error($resp)) {
		echo $resp->get_error_message();
		wp_die();
	} 
	else {	
		wp_die('success');
	}
}
add_action('wp_ajax_mg_rename_grid', 'mg_rename_grid');





////////////////////////////////////////////////
////// DELETE GRID TERM ////////////////////////
////////////////////////////////////////////////

function mg_del_grid_term() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
    if(!isset($_POST['grid_id'])) {
        wp_die('data is missing');
    }
    
	$resp = wp_delete_term((int)$_POST['grid_id'], 'mg_grids');
	($resp == '1') ? wp_die('success') : wp_die('error during the grid deletion');
}
add_action('wp_ajax_mg_del_grid', 'mg_del_grid_term');





////////////////////////////////////////////////
////// DISPLAY GRID BUILDER ////////////////////
////////////////////////////////////////////////

function mg_grid_builder() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	include_once(MG_DIR .'/classes/grid_builder_engine.php');
	
	if(!isset($_POST['grid_id'])) {
        wp_die('data is missing');
    }
    
	$grid_id = (int)$_POST['grid_id'];
	$gbe = new mg_grid_builder_engine($grid_id);
	
	echo json_encode( array(
		'side'	=> $gbe->side_block(),
		'main'	=> $gbe->main_block()
	));
    
	wp_die();
}
add_action('wp_ajax_mg_grid_builder', 'mg_grid_builder');





////////////////////////////////////////////////
////// CHANGE GRID COMPOSITION /////////////////
////////////////////////////////////////////////

function mg_bcc_builder_main() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	include_once(MG_DIR .'/classes/grid_builder_engine.php');
	
	if(!isset($_POST['grid_id'])) {
        wp_die('grid ID missing');
    }
	$grid_id = (int)$_POST['grid_id'];

	if(!isset($_POST['composition'])) {
        wp_die('Composition is missing');
    }
	$gbe = new mg_grid_builder_engine($grid_id);
	
	echo $gbe->main_block( $_POST['composition'] );
	wp_die();
}
add_action('wp_ajax_mg_bcc_builder_main', 'mg_bcc_builder_main');





////////////////////////////////////////////////
////// GET ITEM CATEGORIES POSTS ///////////////
////////////////////////////////////////////////

function mg_builder_query_items() {	
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	
	$to_fetch = array('pt_n_tax', 'term', 'search', 'mg_item_type', 'per_page', 'page');
	foreach($to_fetch as $field) {
		if(!isset($_POST[$field])) {
            wp_die($field.' is missing');
        }	
	}
	
	include_once(MG_DIR .'/classes/grid_builder_engine.php');
	$gbe = new mg_grid_builder_engine(0);
	$items = $gbe->items_picker_code($_POST['pt_n_tax'], $_POST['term'], $_POST['per_page'], $_POST['page'], $_POST['search'], $_POST['mg_item_type']);
	
	echo json_encode(array(
		'items' 	=> $items['code'],
		'tot_pages' => $items['tot_pages']
	));
	wp_die();
}
add_action('wp_ajax_mg_builder_query_items', 'mg_builder_query_items');





////////////////////////////////////////////////
////// ADD AN ITEM TO THE VISUAL BUILDER ///////
////////////////////////////////////////////////

function mg_add_item_to_builder() {	
	include_once(MG_DIR .'/classes/grid_builder_engine.php');
	
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	if(!isset($_POST['item_id'])) {
        wp_die('data is missing');
    }
	
	$item_id = (int)$_POST['item_id'];
	$gbe = new mg_grid_builder_engine(0);
	
	echo $gbe->item_code($item_id);
	wp_die();
}
add_action('wp_ajax_mg_add_item_to_builder', 'mg_add_item_to_builder');





////////////////////////////////////////////
////// SAVE GRID ///////////////////////////
////////////////////////////////////////////

function mg_save_grid() {	
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	
	// empty grid? - set an empty array
	if(!isset($_POST['structure'])) {
		$_POST['structure'] = array();	
	}
	
	// sanitize
	$to_fetch = array(
        'grid_id', 
        'composition', 
        'structure',
        'filtered_per_page'
    );
	if(isset($_POST['composition']) && $_POST['composition'] == 'dynamic') {
		$to_fetch = array_merge($to_fetch, array(
            'dynamic_src', 
            'dynamic_repeat', 
            'dynamic_limit', 
            'dynamic_per_page', 
            'dynamic_orderby', 
            'dynamic_random', 
            'dynamic_auto_h_fb'
        ));	
	}
	foreach($to_fetch as $field) {
		if(!isset($_POST[$field])) {
            wp_die($field.' is missing');
        }	
	}
	
	
	// compose grid structure
	$data = array(
		'composition'       => $_POST['composition'],
        'filtered_per_page' => (int)$_POST['filtered_per_page'],
	);
	
	//// manual
	if($_POST['composition'] == 'manual') {
		$data['manual_items'] = $_POST['structure'];	
	}
	else {
		$data['dynamic_structure'] 	= $_POST['structure'];
		$data['dynamic_src'] 		= $_POST['dynamic_src'];
		$data['dynamic_repeat']		= $_POST['dynamic_repeat'];
		$data['dynamic_limit']		= (int)$_POST['dynamic_limit'];
		$data['dynamic_per_page'] 	= (int)$_POST['dynamic_per_page'];
		$data['dynamic_orderby']	= $_POST['dynamic_orderby'];
		$data['dynamic_random']		= $_POST['dynamic_random'];
		$data['dynamic_force_links']= $_POST['dynamic_force_links'];
		$data['dynamic_auto_h_fb']	= (array)$_POST['dynamic_auto_h_fb']; 
	}
	
	
	// update grid term
	$answer = mg_static::save_grid_data((int)$_POST['grid_id'], $data);
	
	if(is_wp_error($answer)) {
		echo $answer->get_error_message();
	} else {
		echo 'success';
	}
	wp_die();				
}
add_action('wp_ajax_mg_save_grid', 'mg_save_grid');






/////////////////////////////////////////////////////////////////////






////////////////////////////////////////////////
////// SET PREDEFINED GRID STYLES //////////////
////////////////////////////////////////////////

function mg_set_predefined_style() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {
        wp_die('Cheating?');
    }
	if(!isset($_POST['style'])) {
        wp_die('data is missing');
    }

	require_once(MG_DIR .'/settings/preset_styles.php');
	
	$style_data = mg_preset_styles_data($_POST['style']);
	if(empty($style_data)) {
        wp_die('Style not found');
    }
	
	
	// override values
	foreach($style_data as $key => $val) {
		update_option($key, $val);		
	}


	// if is not forcing inline CSS - create static file
	if(!get_option('mg_inline_css')) {
		mg_static::create_frontend_css();
	}
	
	wp_die('success');
}
add_action('wp_ajax_mg_set_predefined_style', 'mg_set_predefined_style');





////////////////////////////////////////////////
////// SYNC OPTIONS WITH WPML //////////////////
////////////////////////////////////////////////

function mg_options_wpml_sync() {
	if(!function_exists('icl_register_string') && !function_exists('pll_register_string')) {
        wp_die('error');
    }
	
	$already_saved = get_option('mg_wpml_synced_opts', array());
	$to_save = array();
	
	foreach(mg_static::main_types() as $type => $name) {
		$type_opts = get_option('mg_'.$type.'_opt');
		$typename = ($type == 'img_gallery') ? 'Image Gallery' : ucfirst($type);
		
		if(is_array($type_opts) && count($type_opts)) {
			foreach($type_opts as $opt) {
				$index = $typename.' Options - '.$opt;
				$to_save[$index] = $index;
				
				
				if(function_exists('icl_register_string')) {
					icl_register_string('Media Grid - Item Attributes', $index, $opt);	
				} else {
					pll_register_string('Media Grid - Item Attributes', $opt, $index);	
				}
				
				if(isset($already_saved[$index])) {unset($already_saved[$index]);}
			}
		}
	}
	
	if(is_array($already_saved) && count($already_saved) > 0) {
		foreach($already_saved as $opt) {
			icl_unregister_string('Media Grid - Item Attributes', $opt);	
		}
	}
	
	update_option('mg_wpml_synced_opts', $to_save);	
	wp_die('success');
}
add_action('wp_ajax_mg_options_wpml_sync', 'mg_options_wpml_sync');
