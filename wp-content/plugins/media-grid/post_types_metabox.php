<?php
// POST TYPES METABOX - applied to post types that are addable to grids


// register
function mg_pt_metabox_register() {
	foreach(mg_static::pt_list() as $pt) {
		if($pt == 'mg_items') {
            continue;
        }	
		
		add_meta_box('mg_pt_thumb_center', 'Media Grid - '. __("Thumbnail's Center", 'mg_ml'), 'mg_thumb_center_box', $pt, 'side', 'low');
		add_meta_box('mg_pt_search_helper_box', 'Media Grid - '. __('Search Helper', 'mg_ml'), 'mg_search_helper_box', $pt, 'side', 'low');
		add_meta_box('mg_pt_metabox', __('Media Grid Integration', 'mg_ml'), 'mg_pt_metabox', $pt, 'normal', 'default');
	}
}
add_action('admin_init', 'mg_pt_metabox_register');





//////////////////////////////
// CONTENTS MANAGEMENT OPTIONS

function mg_pt_metabox() {
	global $post;
	?>
    <div id="mg_item_meta_wrap" class="mg_mainbox_meta">
        <div id="mg_item_meta_f_wrap">
        	<?php 
			include_once(MG_DIR .'/classes/items_meta_fields.php');
			
			$imf_type = ($post->post_type == 'product') ? 'woocomm' : 'post';
			$imf = new mg_meta_fields($post->ID, $imf_type);
			
			echo $imf->get_fields_code();
			$imf->echo_type_js_code();
			?>
        </div> 
    </div>
    
    
    
    <?php // security nonce ?>
    <input type="hidden" name="mg_pt_noncename" value="<?php echo wp_create_nonce('lcwp_nonce') ?>" />
    
    <?php // ////////////////////// ?>

    <script type="text/javascript">
	(function($) { 
        "use strict"; 
        
        $(document).ready(function() {
            
            // sliders
            new lc_range_n_num('.mg_slider_input', {
                unit_width: 17    
            });


            // colorpicker
            $('.mg_colpick').each(function() {
                let modes = $(this).data('modes'),
                    alpha = (modes && modes.indexOf('alpha') !== -1) ? true : false;

                modes = (modes) ? modes.trim().split(' ') : [];
                modes.push('solid');

                // remove alpha mode
                const index = modes.indexOf('alpha');
                if(index !== -1) {
                    modes.splice(index, 1);
                }

                // def colors 
                let def_color = $(this).data('def-color');
                def_color = (def_color.indexOf('gradient') !== -1) ? ['#008080', def_color] : [def_color, 'linear-gradient(90deg, #ffffff 0%, #000000 100%)']; 

                new lc_color_picker('input[name="'+ $(this).attr('name') +'"]', {
                    modes           : modes,
                    transparency    : alpha,
                    no_input_mode   : false,
                    wrap_width      : '90%',
                    fallback_colors : def_color,
                    preview_style   : {
                        input_padding   : 40,
                        side            : 'right',
                        width           : 35,
                    },
                });
            });
            
            
            // lc switch
            lc_switch('.mg_lcs_check', {
                on_txt      : "<?php echo strtoupper(esc_html__('yes')) ?>",
                off_txt     : "<?php echo strtoupper(esc_html__('no')) ?>",   
            });
        
            
            // LC select
            new lc_select('.mg_lcsel_dd', {
                wrap_width : '90%',
                addit_classes : ['lcslt-lcwp'],
            });
        });
        
        
		//// custom icon - picker
		<?php mg_static::fa_icon_picker_js(); ?>
    })(jQuery);     
	</script>
    <?php	
	return true;	
}





//////////////////////////
// SAVING METABOX

// NB: two params passed only by the WP media filter - first parameter is the media $post array
function mg_pt_meta_save($post, $attachment = false) {
    $post_id = (is_array($post)) ? $post['post_ID'] : $post;

    if(isset($_POST['mg_pt_noncename'])) {
		if(!wp_verify_nonce($_POST['mg_pt_noncename'], 'lcwp_nonce') || !current_user_can('edit_post', $post_id)) {
            return $post;
        }
        
		include_once(MG_DIR.'/classes/simple_form_validator.php');
		include_once(MG_DIR .'/classes/items_meta_fields.php');
				
		$validator = new simple_fv;
		$indexes = array();
		$indexes[] = array('index'=>'mg_thumb_center', 'label'=>'Thumbnail Center');
		$indexes[] = array('index'=>'mg_search_helper', 'label'=>'Search Helper');
		
		// type options
		$imf_type = (get_post_type($post_id) == 'product') ? 'woocomm' : 'post';
		$imf = new mg_meta_fields($post_id, $imf_type);
		$indexes = array_merge($indexes, (array)$imf->get_fields_validation());
        
		$validator->formHandle($indexes);
		$fdata = $validator->form_val;
		$error = $validator->getErrors();
		
		// clean data
		foreach($fdata as $key=>$val) {
			if(!is_array($val)) {
				$fdata[$key] = stripslashes($val);
			}
			else {
				$fdata[$key] = array();
				foreach($val as $arr_val) {
                    $fdata[$key][] = stripslashes($arr_val);
                }
			}
		}

		// save data
		foreach($fdata as $key=>$val) {
			update_post_meta($post_id, $key, $fdata[$key]); 
		}
		
		
		// assign MG cats to this post
		if(isset($fdata['mg_post_cats']))  {
			if(!is_array($fdata['mg_post_cats'])) {
                $fdata['mg_post_cats'] = array();
            }
            
            wp_set_post_terms($post_id, $fdata['mg_post_cats'], 'mg_item_categories', $append = false);
		}
	}

    return $post;
}
add_action('save_post','mg_pt_meta_save');
add_filter('attachment_fields_to_save', 'mg_pt_meta_save', 10, 2);