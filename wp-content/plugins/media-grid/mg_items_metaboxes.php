<?php
// METABOXES FOR ITEMS EDITING

// register
function mg_register_metaboxes() {
	add_meta_box('mg_thumb_center_box', __("Thumbnail's Center", 'mg_ml'), 'mg_thumb_center_box', 'mg_items', 'side', 'low');
	add_meta_box('mg_search_helper_box', __('Search Helper', 'mg_ml'), 'mg_search_helper_box', 'mg_items', 'side', 'low');
	add_meta_box('mg_item_opt_box', __('Item Options', 'mg_ml'), 'mg_item_opt_box', 'mg_items', 'normal', 'default');
}
add_action('admin_init', 'mg_register_metaboxes');




//////////////////////////
// THUMBNAIL CENTER

function mg_thumb_center_box() {
	global $post;

    if($post->post_type == 'mg_items') {
        dike_lc('lcweb', MG_DIKE_SLUG, true);
    }
	
	$tc = get_post_meta($post->ID, 'mg_thumb_center', true);
	if(!$tc) {
        $tc = 'c';
    }

	// array of sizes 
	$vals = mg_static::item_sizes();
	?>
    <div class="mg_sidebox_meta">
        <div class="misc-pub-section">
          <input type="hidden" value="<?php echo $tc; ?>" name="mg_thumb_center" id="mg_thumb_center" />
                
          <table class="mg_sel_thumb_center">
            <tr>
                <td id="mg_tl"></td>
                <td id="mg_t"></td>
                <td id="mg_tr"></td>
            </tr>
            <tr>
                <td id="mg_l"></td>
                <td id="mg_c"></td>
                <td id="mg_r"></td>
            </tr>
            <tr>
                <td id="mg_bl"></td>
                <td id="mg_b"></td>
                <td id="mg_br"></td>
            </tr>
          </table>
        </div>
    </div>

    <script type="text/javascript">
    (function($) { 
        "use strict";     
        
        $(document).ready(function($) {
            const mg_thumb_center = function(position) {
                $('.mg_sel_thumb_center td').removeClass('thumb_center');
                $('.mg_sel_thumb_center #mg_'+position).addClass('thumb_center');

                $('#mg_thumb_center').val(position);	
            };
            mg_thumb_center( $('#mg_thumb_center').val() );

            $(document).on('click', '.mg_sel_thumb_center td', function() {
                var new_position = $(this).attr('id').substr(3);
                mg_thumb_center(new_position);
            });		
        });
    })(jQuery);     
    </script>
 
	<?php
}




//////////////////////////
// SEARCH HELPER

function mg_search_helper_box() {
	global $post;
	$helper = get_post_meta($post->ID, 'mg_search_helper', true);
	?>
    <div class="mg_sidebox_meta">
        <div class="misc-pub-section">
          <textarea name="mg_search_helper" rows="2" style="width: 100%;"><?php echo $helper ?></textarea>
        </div>
    </div>
	<?php
}




//////////////////////////
// ITEM OPTIONS

function mg_item_opt_box() {
	global $post;
	$main_type = get_post_meta($post->ID, 'mg_main_type', true);
	?>
    <div id="mg_item_meta_wrap" class="mg_mainbox_meta">
		<div id="mg_item_meta_type_choser">
            <span><?php _e("Item Type", 'mg_ml'); ?></span>
            
            <select name="mg_main_type" id="mg_main_type" autocomplete="off">
			  <?php 
              $types = mg_static::item_types();

              foreach($types as $key => $val) {
				if($key == 'spacer') {
                    continue;
                }
                  
				echo '<option value="'. $key .'" '. selected($key, $main_type, false) .'>'.$val.'</option>'; 
              }
              ?>
        	</select>
        </div> 
      	
        <div id="mg_item_meta_f_wrap">
			<?php 
			include_once(MG_DIR .'/classes/items_meta_fields.php');
			
			$imf_type = (empty($main_type)) ? 'simple_img' : $main_type;
			$imf = new mg_meta_fields($post->ID, $imf_type);
			
			echo $imf->get_fields_code();
			$imf->echo_type_js_code();
			?>
        </div>
    </div>
    
    <?php // security nonce ?>
    <input type="hidden" name="mg_item_noncename" value="<?php echo wp_create_nonce('lcwp_nonce') ?>" />
    


    <?php // ////////////////////// ?>
    


    <?php // SCRIPTS ?>
    <script type="text/javascript">
    (function($) { 
        "use strict";     
        
        jQuery(document).ready(function($) {
            const lcwp_nonce = '<?php echo wp_create_nonce('lcwp_nonce') ?>';

            // lightbox live preview
            <?php 
            if(!empty($main_type) && !in_array($main_type, array('simple_img','link','inl_slider', 'inl_audio', 'inl_video','inl_text','spacer'))) : ?>
                var lb_preview_link = 
                '<div id="major-publishing-actions" class="misc-pub-section-last">'+
                    '<a href="<?php echo site_url(); ?>?mgi_999=<?php echo $post->ID; ?>" target="_blank" id="mg_item_preview_link">'+
                        '<button type="button" class="button-secondary"><span class="dashicons dashicons-welcome-view-site" style="line-height: 28px; padding-right: 5px;"></span> <?php esc_html_e("Item's lightbox preview", 'mg_ml') ?></button>'+
                    '</a>'+
                '</div>';

                $('#major-publishing-actions').addClass('misc-pub-section');
                $('#submitpost').parent().append(lb_preview_link);
            <?php endif; ?>


            // item type switch
            $(document).on("change", '#mg_main_type', function (e) {
                if(
                    <?php if(empty($main_type)) {echo '1 == 1 || ';} ?> 
                    confirm("<?php esc_attr_e("Changing item type unsaved data will be lost. Continue?", 'mg_ml') ?>")
                ) {

                    // loader and new options
                    var $wrap = $('#mg_item_meta_f_wrap');
                    $wrap.html('<div class="mg_spinner mg_spinner_big"></div>');

                    var data = {
                        action: 	'mg_item_meta_fields',
                        item_id:	<?php echo $post->ID ?>,
                        item_type: 	$(this).val(),
                        lcwp_nonce:	lcwp_nonce
                    };
                    $.post(ajaxurl, data, function(response) {
                        $wrap.html(response);
                        mg_live_js_managed_fields();
                    })
                    .fail(function(e) {
                        console.error(e);
                        alert('error loading type options');
                    });			
                }
            });



            /////////////////////////////////////////////////////////////////////


            //// custom icon - picker
            <?php mg_static::fa_icon_picker_js(); ?>


            /////////////////////////////////////////////////////////////////////



            //// custom file uploader for gallery and audio
            let mg_TB = 0;

            // open tb and hide tabs
            $(document).on('click', '.mg_TB', function(e) {
                const mg_TB_type = ($(this).hasClass('mg_upload_img')) ? 'img' : 'audio';

                // thickBox
                if(typeof(wp.media) == 'undefined') {
                    mg_TB = 1;
                    post_id = $('#post_ID').val();

                    if(mg_TB_type == 'img') {
                        tb_show('', '<?php echo admin_url(); ?>media-upload.php?post_id='+post_id+'&amp;type=image&amp;TB_iframe=true');
                    }
                    else {
                        tb_show('', '<?php echo admin_url(); ?>media-upload.php?post_id='+post_id+'&amp;type=audio&amp;TB_iframe=true');	
                    }

                    mg_media_man = setInterval(function() {
                        if(mg_TB == 1) {
                            if( $('#TB_iframeContent').contents().find('#tab-type_url').is('hidden') ) {
                                return false;
                            }

                            $('#TB_iframeContent').contents().find('#tab-type_url').hide();
                            $('#TB_iframeContent').contents().find('#tab-gallery').hide();

                            clearInterval(mg_media_man);
                        }
                    }, 10);
                }

                // new lightbox management
                else {
                    e.preventDefault();

                    var title = (mg_TB_type == 'img') ? '<?php _e('Image', 'mg_ml')?>' : '<?php _e('Audio', 'mg_ml') ?>';
                    var subj = (mg_TB_type == 'img') ? 'image' : 'audio';

                    var custom_uploader = wp.media({
                        title: 'WP '+ title +' Management',
                        button: { text: 'Ok' },
                        library : { type : subj},
                        multiple: false
                    })
                    .on('select close', function() {
                        if(mg_TB_type == 'img') { 
                            mg_load_img_picker(1); 
                            mg_sel_img_reload();
                        }
                        else {
                            mg_load_audio_picker(1);	
                            mg_sel_tracks_reload();
                        }
                    })
                    .open();	
                }
            });

            // reload picker on thickbox unload
            $(window).on('tb_unload', function() {
                if(mg_TB == 1) {
                    if(mg_TB_type == 'img') { 
                        mg_load_img_picker(1); 
                        mg_sel_img_reload();
                    }
                    else {
                        mg_load_audio_picker(1);	
                        mg_sel_tracks_reload();
                    }

                    mg_TB = 0;		
                }
            });


            ////////////////////////


            //// images & audio
            // remove item
            $(document).on('click', '#mg_gallery_img_wrap ul li span, #mg_audio_tracks_wrap ul li span', function() {
                $(this).parent().remove();	

                if( $('#mg_gallery_img_wrap ul li').length ) {
                    $('#mg_gallery_img_wrap ul').html('<p><?php esc_attr_e('No images selected', 'mg_ml') ?>  .. </p>');
                }
                if( $('#mg_audio_tracks_wrap ul li').length ) {
                    $('#mg_audio_tracks_wrap ul').html('<p><?php esc_attr_e('No tracks selected', 'mg_ml') ?> .. </p>');
                }
            });


            // sort items
            const mg_sort = function() { 
                $( "#mg_gallery_img_wrap ul, #mg_audio_tracks_wrap ul" ).sortable();
                $( "#mg_gallery_img_wrap ul, #mg_audio_tracks_wrap ul" ).disableSelection();
            }
            mg_sort();


            ////////////////////////


            // wrap together all various JS plugins to initialize 
            window.mg_live_js_managed_fields = function() {

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
            };
            mg_live_js_managed_fields();


            // remove subcategories adder
            $('#mg_item_categories-adder').remove();
        });
        
    })(jQuery); 
	</script>
       
    <?php	
	return true;	
}






//////////////////////////
// SAVING METABOXES

function mg_items_meta_save($post_id) {
	if(isset($_POST['mg_item_noncename'])) {
		if(!wp_verify_nonce($_POST['mg_item_noncename'], 'lcwp_nonce') || !current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
		
		include_once(MG_DIR.'/classes/simple_form_validator.php');
		include_once(MG_DIR .'/classes/items_meta_fields.php');
				
		$validator = new simple_fv;
		$indexes = array();
		
		$indexes[] = array('index'=>'mg_thumb_center', 'label'=>'Thumbnail Center');
		$indexes[] = array('index'=>'mg_search_helper', 'label'=>'Search Helper');
		$indexes[] = array('index'=>'mg_main_type', 'label'=>'Item Type');
		
		// type options
		if(isset($_POST['mg_main_type']) && !empty($_POST['mg_main_type'])) {
			$imf = new mg_meta_fields($post_id, $_POST['mg_main_type']);
			$indexes = array_merge($indexes, (array)$imf->get_fields_validation());
		}
		
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
				foreach($val as $arr_val) {$fdata[$key][] = stripslashes($arr_val);}
			}
		}

		// save data
		foreach($fdata as $key=>$val) {
			
			// search helper - sanitize
			if($key == 'mg_search_helper') {
				$fdata[$key] = str_replace(array('"', '<', '>'), '', $fdata[$key]);	
			}
			
			update_post_meta($post_id, $key, $fdata[$key]); 
		}
	}

    return $post_id;
}
add_action('save_post', 'mg_items_meta_save');




//////////////////////////
// WARNING IF FEATURED IMAGE IS NOT SET

add_action('admin_notices', 'mg_item_featured_image');
function mg_item_featured_image(){
	global $current_screen;
	
	if ($current_screen->id == 'mg_items' && $current_screen->parent_base == 'edit') {
     	global $post;
		$main_type = get_post_meta($post->ID, 'mg_main_type', true);

		if(!in_array($main_type, array('inl_slider','inl_video','post_contents','inl_text','spacer')) && get_the_post_thumbnail($post->ID) == '') {
			echo '<div class="error"><p>'. __('Warning - This item has not a featured image', 'mg_ml') .'</p></div>';		
		}
	}
}
