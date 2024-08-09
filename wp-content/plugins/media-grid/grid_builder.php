<?php 
include_once(MG_DIR . '/classes/grid_builder_engine.php'); 
dike_lc('lcweb', MG_DIKE_SLUG, true);


// builder class to get fixed code blocks
$gbe = new mg_grid_builder_engine(0);


// style to dynamically size items
echo
'<style type="text/css">';

	// default values (valid also for "auto" height)
	echo '.mg_box {
		width: calc((100% - 1px) * 0.25); 
		padding-bottom: calc((100% - 1px) * 0.25 - 6px); /* SUBTRACT ITEMS MARGIN */
	}';

	// desktop sizes
	foreach(mg_static::item_sizes() as $key => $data) {
		
        // special cases
        $to_substract = ($key == '1_4') ? 7 : 6;
        
        echo '.mg_box[data-w="'.$key.'"] {width: calc((100% - 1px) * '. $data['perc'] .');}';
		echo '.mg_box[data-h="'.$key.'"] {padding-bottom: calc((100% - 1px) * '. $data['perc'] .' - '. $to_substract .'px);}'; // SUBTRACT ITEMS MARGIN
	}
	
	// mobile sizes
	foreach(mg_static::mobile_sizes() as $key => $data) {
		echo '.mg_mobile_builder .mg_box[data-mw="'.$key.'"] {width: calc((100% - 1px) * '. $data['perc'].');}';
		echo '.mg_mobile_builder .mg_box[data-mh="'.$key.'"] {padding-bottom: calc((100% - 1px) * '. $data['perc'].' - 6px);}'; // SUBTRACT ITEMS MARGIN
	}

// special cases + closing  
echo
'</style>';
?>


<div class="wrap">  
    <h1 class="mg_page_title">
		Media Grid - <?php _e( 'Grid Builder', 'mg_ml') ?>
        <a href="javascript:void(0)" id="add_grid_trigger" class="page-title-action"><?php _e('Add New Grid', 'mg_ml') ?></a>
    </h1>
    

	<?php // GRIDS DROPDOWN AND MAIN BUTTONS TO PREVIEW AND SAVE ?>
	<div id="mg_grids_choice">
        <form class="form-wrap">
            <div id="mg_grids_dd" class="mg_grids_no_sel mg_grids_dd_shown">
                <div id="mg_grids_dd_sel" title="<?php esc_attr_e('chosen grid', 'mg_ml') ?>"><em>.. <?php _e('select grid', 'mg_ml') ?> ..</em></div>
               
                <div id="mg_grids_dd_list">
                    <?php echo mg_static::builder_grids_list(); ?>
                </div>
            </div>
    	</form>        
    </div>
    

	
    <?php // BUILDER'S BODY ?>
    <div id="poststuff" class="mg_grid_builder_outer_wrap metabox-holder has-right-sidebar">
    	<form class="form-wrap">
        
			<?php // SIDEBAR ?>
            <div id="side-info-column" class="mg_grid_builder_side inner-sidebar">
                <div id="mg_select_grid_infographic">.. <?php _e('no grid selected', 'mg_ml') ?> ..</div>
            </div>
    	
        	<?php // PAGE CONTENT ?>
          	<div id="post-body">
              	<div id="post-body-content" class="mg_grid_builder_main">
					
              	</div>
       		</div>
        
        </form>
        <br class="clear" />
    </div>
</div>  





<?php // SCRIPTS ?>
<script type="text/javascript">
(function($) { 
    "use strict";    
    
jQuery(document).ready(function($) {
    var nonce                   = '<?php echo wp_create_nonce('lcwp_nonce') ?>';
	var ajax_acting_in_popup 	= false;
	var is_loading_grid 		= false;
	
	var is_changing_comp	= false;

	var mg_sel_grid		= 0;
	var mg_mobile 		= false;
	var mg_easy_sorting = false;
	var muuri_obj 		= false;
	var new_item_pos 	= <?php echo (get_option('mg_builder_behav', 'append') == 'prepend') ? 0 : -1; ?>; // where to place new items (before or after existing ones)
	
	
	/*** GRIDS LIST ACTIONS ***/

	// grids selection dropdown - click events
	$(document).on('click', '#mg_grids_dd:not(.mg_grids_no_sel) #mg_grids_dd_sel', function(){
		
		if($('#mg_grids_dd').hasClass('mg_grids_dd_shown')) {
			$('#mg_grids_dd').removeClass('mg_grids_dd_shown').addClass('mg_grids_dd_closed');
		} else {
			$('#mg_grids_dd').addClass('mg_grids_dd_shown').removeClass('mg_grids_dd_closed');	
		}
	});
	

	// grid selection
	$(document).on('click', '#mg_grids_dd_list .mg_dd_list_item:not(.mg_gddl_sel)', function(e) {
		if(is_loading_grid || $(e.target).hasClass('mg_grids_list_btn')) {
            return true;
        }
		
		mg_mobile = false;
		$('.mg_grid_builder_main').removeClass('mg_mobile_builder');
		
		is_loading_grid = true;
		mg_sel_grid = parseInt($(this).attr('rel'), 10);

		// close dropdown and set name
		$('.mg_gddl_sel').removeClass('mg_gddl_sel');
		$(this).addClass('mg_gddl_sel');
		
		$('#mg_grids_dd').removeClass('mg_grids_no_sel mg_grids_dd_shown').addClass('mg_grids_dd_closed');
		$('#mg_grids_dd_sel').text( $(this).find('.mg_grid_tit').text() );		
	
	
		// load builder
		$('.mg_grid_builder_main').empty();
		$('.mg_grid_builder_side > *').not('#mg_select_grid_infographic').remove();
		$('#mg_select_grid_infographic').hide();
		$('.mg_grid_builder_main').html('<div style="margin-top: 50px; width: 170px; height: 170px;" class="mg_spinner mg_spinner_big"></div>');

		var data = {
			action       : 'mg_grid_builder',
			grid_id      : mg_sel_grid,
			lcwp_nonce   : nonce,
		};
		
		$.post(ajaxurl, data, function(response) {
			try {
                var resp = $.parseJSON(response);

                $('.mg_grid_builder_side').prepend( resp.side );
                $('.mg_grid_builder_main').html( resp.main );

                live_lc_select();
                live_lc_switch();

                chitemmuuri();
                mg_items_num_pos();

                // dynamic builder - manage dropdowns visibility
                $("#mg_dgb_src_list select[name=mg_items_src]").trigger("mg_dbs_added");
            }
            catch(e) {
                console.error(e);
                lc_wp_popup_message('error', "<?php esc_attr_e("Error loading the grid", 'mg_ml') ?>");    
                
                is_loading_grid = true;
                $('.mg_grid_builder_main').empty();
            }
		})
        .fail(function(e) {
            console.error(e);
            
            lc_wp_popup_message('error', "<?php esc_attr_e("Error loading the grid", 'mg_ml') ?>");
            $('.mg_grid_builder_main').empty();
        })
        .always(function() {
            mg_mobile = false;	
			mg_easy_sorting = false;
			is_loading_grid = false;    
        });	
	});
	

	// grids search
	$('.mg_dd_list_search input').keydown(function() {
		if(typeof(mg_grid_search_timeout) != 'undefined') {
            clearTimeout(mg_grid_search_timeout);
        }
		
		mg_grid_search_timeout = setTimeout(function() {
			var val = $.trim($('.mg_dd_list_search input').val());
			$('.mg_dd_list_nogrids').remove();
			
			// empty - show all
			if(!val) {
				$('.mg_dd_list_item').show();
			}
			else {
				var src_arr = val.toLowerCase().split(' ');

				// cyle and check each searched term 
				$('.mg_dd_list_item').each(function() {
					var grid_txt = $(this).find('em').text().replace('#', '') +' '+ $(this).find('.mg_grid_tit').text().toLowerCase();
					var matching = true;
					
					$.each(src_arr, function(i, word) {						
						if(grid_txt.indexOf(word) === -1) {
							matching = false;
							return false;	
						}
					});
					
					if(!matching) {
						$(this).hide();	
					} else {
						$(this).show();	
					}
				});

				// no grid left - append a message
				if(!$('.mg_dd_list_item:visible').length) {
					$('.mg_items_list_scroll').append('<div class="mg_dd_list_nogrids"><em><?php echo esc_attr(__('No grids found', 'mg_ml')) ?> ..</em></div>');	
				}
			}
		}, 80);
	});
	
	
	
	///////////////////////////
	
	
	
	// load grid list
	const reload_grids_list = function() {
		$('#mg_grids_dd_list').html('<div class="mg_spinner"></div>');
		
		var data = {
			action       : 'mg_get_grids_list',
			lcwp_nonce   : nonce,
		};
		$.post(ajaxurl, data, function(response) {	
			$('#mg_grids_dd_list').html(response);
		})
        .fail(function(e) {
            console.error(e);            
            lc_wp_popup_message('error', "<?php esc_attr_e("Error loading grids", 'mg_ml') ?>");
        });	
	};
	
	
	
	// add grid
	$('#add_grid_trigger').on('click', function() {
		if(ajax_acting_in_popup) {
            return false;
        }
		
		var html = 
		"<form id='mg_add_grid_form'><input type='text' placeholder='<?php esc_attr_e("Grid name", 'mg_ml') ?> ..' autocomplete='off' maxlength='100' />"+
		"<input type='button' value='<?php esc_attr_e('Add Grid', 'mg_ml') ?>' class='button-primary' />"+
		"<input type='button' value='<?php esc_attr_e('Close', 'mg_ml') ?>' class='button-secondary' /></form>";
		
		lc_wp_popup_message('modal', html);
	});
	$(document).on('click', '#mg_add_grid_form .button-secondary', function() {
		if(ajax_acting_in_popup) {
            return false;
        }
		lcwpm_close();
	});
	
    
	// perform addition
	$(document).on('click', '#mg_add_grid_form .button-primary', function() {
		$('.mg_toast_ajax_response').remove();
		
		var grid_name = $.trim($('#mg_add_grid_form input[type=text]').val());
		if(!$.trim(grid_name)) {
            return false;
        }
		
		ajax_acting_in_popup = true;
		$('#mg_add_grid_form input[type=text]').attr('disabled', 'disabled');
		
		var data = {
			action       : 'mg_add_grid',
			grid_name    : grid_name,
			lcwp_nonce   : nonce,
		};
		$.post(ajaxurl, data, function(response) {
			if($.trim(response) == 'success') {
				$('#mg_add_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_success"><?php esc_attr_e('Grid successfully added!', 'mg_ml') ?></div>');
				
				setTimeout(function() {
					lcwpm_close();
					reload_grids_list();
				}, 1200);
			} 
			else {
				$('#mg_add_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_error">'+ response +'</div>');
			}
		})
        .fail(function(e) {
            console.error(e);
            lc_wp_popup_message('error', "<?php esc_attr_e("Error creating the grid", 'mg_ml') ?>");
        })
        .always(function() {
            ajax_acting_in_popup = false;
            $('#mg_add_grid_form input[type=text]').removeAttr('disabled');
        });	
	});
	
	
	
	// clone grid
	$(document).on('click', '.mg_clone.mg_grids_list_btn', function() {
		if(ajax_acting_in_popup) {
            return false;
        }
		
		var html = 
		"<form id='mg_clone_grid_form'><input type='text' placeholder='<?php esc_attr_e("Cloned grid name", 'mg_ml') ?> ..' autocomplete='off' maxlength='100' />"+
		"<input type='button' value='<?php esc_attr_e('Clone', 'mg_ml') ?>' class='button-primary' rel='"+ $(this).attr('rel') +"' />"+
		"<input type='button' value='<?php esc_attr_e('Close', 'mg_ml') ?>' class='button-secondary' /></form>";
		
		lc_wp_popup_message('modal', html);
	});
	$(document).on('click', '#mg_clone_grid_form .button-secondary', function() {
		if(ajax_acting_in_popup) {
            return false;
        }
		lcwpm_close();
	});
	
    
	// perform cloning
	$(document).on('click', '#mg_clone_grid_form .button-primary', function() {
		var grid_id = $(this).attr('rel');
		$('.mg_toast_ajax_response').remove();
		
		var new_name = $('#mg_clone_grid_form input[type=text]').val();
		if(!$.trim(new_name)) {
            return false;
        }
		
		ajax_acting_in_popup = true;
		$('#mg_clone_grid_form input[type=text]').attr('disabled', 'disabled');
		
		var data = {
			action       : 'mg_clone_grid',
			grid_id      : grid_id,
			new_name     : new_name,
			lcwp_nonce   : nonce,
		};
			
		$.post(ajaxurl, data, function(response) {
			if($.trim(response) == 'success') {
				$('#mg_clone_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_success"><?php esc_attr_e('Grid successfully cloned!', 'mg_ml') ?></div>');
				
				setTimeout(function() {
					lcwpm_close();
					reload_grids_list();
				}, 1200);
			} 
			else {
				$('#mg_clone_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_error">'+ response +'</div>');
			}
		})
        .fail(function(e) {
            console.error(e);
            $('#mg_clone_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_error"><?php esc_attr_e("Error cloning the grid", 'mg_ml') ?></div>');
        })
        .always(function() {
            ajax_acting_in_popup = false;
            $('#mg_clone_grid_form input[type=text]').removeAttr('disabled');
        });
	});
	
	
	
	// rename grid
	$(document).on('click', '.mg_edit_name.mg_grids_list_btn', function() {
		if(ajax_acting_in_popup) {
            return false;
        }
		var curr_name = $(this).parents('.mg_dd_list_item').find('.mg_grid_tit').text().replace(/'/g, "\'"); 
		
		var html = 
		"<form id='mg_rename_grid_form'><input type='text' value='"+ curr_name +"' placeholder='<?php esc_attr_e("New grid name", 'mg_ml') ?> ..' autocomplete='off' maxlength='100' />"+
		"<input type='button' value='<?php esc_attr_e('Rename', 'mg_ml') ?>' class='button-primary' rel='"+ $(this).attr('rel') +"' />"+
		"<input type='button' value='<?php esc_attr_e('Close', 'mg_ml') ?>' class='button-secondary' /></form>";
		
		lc_wp_popup_message('modal', html);
	});
	$(document).on('click', '#mg_rename_grid_form .button-secondary', function() {
		if(ajax_acting_in_popup) {
            return false;
        }
		lcwpm_close();
	});
	
    
	// perform renaming
	$(document).on('click', '#mg_rename_grid_form .button-primary', function() {
		var grid_id  = $(this).attr('rel');
		$('.mg_toast_ajax_response').remove();
		
		var old_name = $('.mgg_'+grid_id+' .mg_grid_tit').text();
		var new_name = $.trim($('#mg_rename_grid_form input[type=text]').val());
		if(!$.trim(new_name) || old_name === new_name) {
            return false;
        }
		
		ajax_acting_in_popup = true;
		$('#mg_rename_grid_form input[type=text]').attr('disabled', 'disabled');
		
		var data = {
			action       : 'mg_rename_grid',
			grid_id      : grid_id,
			new_name     : new_name,
			lcwp_nonce   : nonce,
		};
			
		$.post(ajaxurl, data, function(response) {
			if($.trim(response) == 'success') {
				$('#mg_rename_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_success"><?php esc_attr_e('Grid successfully renamed!', 'mg_ml') ?></div>');
				$('.mgg_'+grid_id+' .mg_grid_tit').text(new_name);
				
				setTimeout(function() {
					lcwpm_close();
				}, 1200);
			} 
			else {
				$('#mg_rename_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_error">'+ response +'</div>');
			}
		})
        .fail(function(e) {
            console.error(e);
            $('#mg_rename_grid_form input[type=text]').after('<div class="mg_toast_ajax_response mg_tar_error"><?php esc_attr_e("Error renaming the grid", 'mg_ml') ?></div>');
        })
        .always(function() {
            ajax_acting_in_popup = false;
            $('#mg_rename_grid_form input[type=text]').removeAttr('disabled');
        });
	});
	
	
	
	// delete grid
	$(document).on('click', '.mg_del_grid.mg_grids_list_btn', function() {
		var $grid_list_item = $(this).parents('.mg_dd_list_item');
		var grid_id  = $grid_list_item.attr('rel');
		
		// not if another grid operation is being performed
		if(ajax_acting_in_popup || is_loading_grid) {
            return false;
        }
		
		// ask before proceeding
		if(confirm('<?php esc_attr_e('This will DEFINITIVELY delete the grid. Continue?', 'mg_ml') ?>')) {
			is_loading_grid = true;
			ajax_acting_in_popup = true;
			
			$grid_list_item.fadeTo(200, 0.5);
			
			var data = {
				action      : 'mg_del_grid',
				grid_id     : grid_id,
                lcwp_nonce  : nonce,
			};
			$.post(ajaxurl, data, function(response) {
				if($.trim(response) == 'success') {
					
					// if is this one opened
					if(mg_sel_grid == grid_id) {
						$('.mg_grid_builder_main').empty();
						$('#mg_select_grid_infographic').show();
						$('.mg_grid_builder_side > *').not('#mg_select_grid_infographic').remove();
						
						mg_sel_grid = false;
					}
					
					$grid_list_item.remove();
				}
				else {
					$grid_list_item.fadeTo(0, 1);
					lc_wp_popup_message('error', response);
				}
			})
            .fail(function(e) {
                console.error(e);
                lc_wp_popup_message('error', "<?php esc_attr_e("Error deleting the grid", 'mg_ml') ?>");
                
                $grid_list_item.fadeTo(0, 1);
            })
            .always(function() {
                is_loading_grid = false;
				ajax_acting_in_popup = false;
            });	
		}
	});
	
	
	
	
	///////////////////////////
	


	
	// manual/dynamic grid switch - reload main builder
	$(document).on('change', 'select[name=mg_grid_composition]', function() {
		if(is_changing_comp) {
            return false;
        }
		
		var new_comp = $(this).val();
		var old_comp = (new_comp == 'dynamic') ? 'manual' : 'dynamic';
		
		// no confirm if no item in manual or no source in dynamic
		if(
			(old_comp == 'manual' && !$('.mg_box').length) ||
			(old_comp == 'dynamic' && !$('.mg_items_source').length && !$('.mg_box').length) ||
			confirm("<?php esc_attr_e('Any unsaved change in grid composition will be lost. Continue?', 'mg_ml') ?>")
		) {
			
			// composition changed
			if(new_comp == 'dynamic') {
				$('.mg_dynamic_grid_opt').slideDown();	
				$('.mg_manual_grid_opt').slideUp();	
				
				if(!$('input[name=mg_dynamic_repeat]').is(':checked')) {
					$('input[name=mg_dynamic_limit]').parents('.mg_dynamic_grid_opt').stop().hide();
				}
			} 
			else {
				$('.mg_dynamic_grid_opt').slideUp();	
				$('.mg_manual_grid_opt').slideDown();
			}
			
			
			// reset bulk sizers
			$('#mg_bulk_w, #mg_bulk_h').show();	
			$('#mg_bulk_mw, #mg_bulk_mh, #dynamic_auto_mh_fb').hide();		
			
						
			// recall main builder
			var $wrap = $('.mg_grid_builder_main');
			$wrap.html('<div class="mg_spinner mg_spinner_big"></div>');
			
			var data = {
				action		: 'mg_bcc_builder_main',
				grid_id		: mg_sel_grid,
				composition	: new_comp,
				lcwp_nonce  : nonce,
			};
			$.post(ajaxurl, data, function(response) {
				$wrap.html(response);
				chitemmuuri();
				
				mg_items_num_pos();
				live_lc_select();
				
				// dynamic builder - manage dropdowns visibility
				$("#mg_dgb_src_list select[name=mg_items_src]").trigger("mg_dbs_added");
			})
            .fail(function(e) {
                console.error(e);
                lc_wp_popup_message('error', "<?php esc_attr_e("Error performing the operation", 'mg_ml') ?>");
            })
            .always(function() {
                mg_mobile = false;	
				mg_easy_sorting = false;
            });	
		}
		
		else {
			$("select[name=mg_grid_composition] option").removeAttr('selected');
			$("select[name=mg_grid_composition] option[value='"+ old_comp +"']").attr('selected', 'selected');
            
			return false;
		}	
	});
	
	
	
	// save grid
	$(document).on('click', 'input[name=mg_save_grid]', function() {
		var $btn = $(this);
		var comp = $('select[name=mg_grid_composition]').val();
		
		if($btn.hasClass('mg_saving_grid')) {
            return false;
        }
		
		// base atts
		var data = {
			action		     : 'mg_save_grid',
			grid_id		     : mg_sel_grid,
			composition      : comp,
			structure 	     : get_grid_structure(),  
            filtered_per_page: parseInt($('input[name=mg_filtered_per_page]').val(), 10),
			lcwp_nonce       : nonce,
		}
		
		// enqueue side opts
		if(comp == 'dynamic') {
			data.dynamic_src 		= mg_dyn_grid_sources(),
			data.dynamic_repeat 	= $('input[name=mg_dynamic_repeat]').is(':checked') ? 1 : 0;
			data.dynamic_limit 		= parseInt($('input[name=mg_dynamic_limit]').val(), 10);	
			data.dynamic_per_page 	= parseInt($('input[name=mg_dynamic_per_page]').val(), 10);	
			data.dynamic_orderby 	= $('select[name=mg_dynamic_orderby]').val();	
			data.dynamic_random 	= $('input[name=mg_dynamic_random]').is(':checked') ? 1 : 0;
			data.dynamic_force_links= $('input[name=mg_dynamic_force_links]').is(':checked') ? 1 : 0;
			data.dynamic_auto_h_fb 	= {
				'h' 	:	$('select[name=dynamic_auto_h_fb]').val(),
				'm_h'	:	$('select[name=dynamic_auto_mh_fb]').val(),
			}
		}

		// call
		$btn.addClass('mg_saving_grid').fadeTo(200, 0.5);
	
		$.post(ajaxurl, data, function(response) {
			var resp = $.trim(response); 

			if(resp == 'success') {
				lc_wp_popup_message('success', "<?php esc_attr_e('Grid successfully saved!', 'mg_ml') ?>");
			} else {
				lc_wp_popup_message('error', resp);
			}
		})
        .fail(function(e) {
            console.error(e);
            lc_wp_popup_message('error', "<?php esc_attr_e("Error saving the grid", 'mg_ml') ?>");
        })
        .always(function() {
            $btn.removeClass('mg_saving_grid').fadeTo(200, 1);
        });
	});
	
	
	// retrieve grid structure to be saved
	var get_grid_structure = function() {
		var struct = [];
		
		muuri_obj.getItems().forEach(function (item, i) {
			var $item = $(item._element);
			var item_data = {
				'id'	: $item.find('input[name="grid_items[]"]').val(),
				'w'		: $item.attr('data-w'),
				'h'		: $item.attr('data-h'),
				'm_w'	: $item.attr('data-mw'),
				'm_h'	: $item.attr('data-mh'),
			};
			
			// if spacer - add also visibility
			if(item_data.id == 'spacer') {
				item_data.vis = $item.find('.mg_spacer_vis_dd').val()				
			}

			struct.push(item_data);
		});
		
		return struct;
	};
	
	
	// preview grid
	$(document).on('click', '#preview_grid', function() {
		var url = $(this).data('pv-url') + '?mg_preview=' + mg_sel_grid;
		window.open(url,'_blank');
	});
	
	
	// expanded mode toggle
	$(document).on('click', '#mg_expand_builder', function() {
		if($('#wpcontent').hasClass('mg_expanded_builder')) {
			$('#wpcontent').removeClass('mg_expanded_builder');	
		} else {
			$('#wpcontent').addClass('mg_expanded_builder');	
		}
		
		mg_relayout_grid();
	});
	
	
	
	
	/**********************************************/
	/************ DYNAMIC COMPOSITION *************/
	/**********************************************/
	
	
	// repeated structure - toggle limit visibility
	$(document).on('lcs-statuschange', 'input[name=mg_dynamic_repeat]', function() {
		if($(this).is(':checked')) {
			$('input[name=mg_dynamic_limit]').parents('.mg_dynamic_grid_opt').slideDown();
		} else {
			$('input[name=mg_dynamic_limit]').parents('.mg_dynamic_grid_opt').slideUp();
		}
	});
	
	
	// add source 
	$(document).on('click', '#mg_add_source', function() {
		if(!$('#mg_dgb_src_list thead').length) {
			$('#mg_dgb_src_list').prepend(
			'<thead>'+
				'<tr>'+
					'<th><?php _e('Post type and taxonomy', 'mg_ml') ?></th>'+
					'<th><?php _e('Specific term association?', 'mg_ml') ?></th>'+
					'<th><?php _e('Specific item type?', 'mg_ml') ?></th>'+
					'<th></th>'+
				'</tr>'+
			'</thead><tbody></tbody>');	
		}
		
		$('#mg_dgb_src_list tbody').append('<?php echo str_replace("'", "\'", $gbe->dynamic_src_code()); ?>');
		
		$('#mg_dgb_src_list select[name=mg_items_src]').last().trigger('mg_dbs_added');
		live_lc_select();
	});
	
	
	// remove source
	$(document).on('click', 'input[name=mg_dgb_del_src]', function() {
		if(confirm("<?php esc_attr_e('Do you really want to remove this source?', 'mg_ml') ?>")) {
			if($('#mg_dgb_src_list tbody tr').length > 1) {
				$(this).parents('tr').remove();	
			} else {
				$('#mg_dgb_src_list').empty();	
			}
		}
		else {return false;}
	});
	
	
	// update terms changing post type
	$(document).on('change', '#mg_dgb_src_list select[name=mg_items_src]', function() {
		const val     = $(this).val(),
              $parent = $(this).parents('tr');

        // hide for wp media lib
        if(val == 'attachment|||') {
            $parent.find('.lcslt-f-mg_cpt_tax_term').css('visibility', 'hidden');   
            return false;
        }
        $parent.find('.lcslt-f-mg_cpt_tax_term').css('visibility', 'visible');   
           
           
		// loader
		$parent.find('.mg_items_src_tax_wrap').html('<div style="width: 20px; height: 20px; margin-top: 5px;" class="mg_spinner mg_spinner_inline"></div>');
		
		var data = {
			action       : 'mg_sel_cpt_source',
			cpt          : val,
            lcwp_nonce   : nonce,
		};
		$.post(ajaxurl, data, function(response) {
			$parent.find('.mg_items_src_tax_wrap').html(response);
			live_lc_select();
		})
        .fail(function(e) {
            console.error(e);
            lc_wp_popup_message('error', "<?php esc_attr_e("Error performing the operation", 'mg_ml') ?>");
        });
	});
	
	
	// toggle MG item dropdown visibility in sources
	$(document).on('change mg_dbs_added', '#mg_dgb_src_list select[name=mg_items_src]', function() {
		
        const $subj1 = $(this).parents('tr').find('.lcslt-wrap.lcslt-f-mg_items_type');
        ($(this).val().indexOf('mg_items') !== -1) ? $subj1.css('visibility', 'visible') : $subj1.css('visibility', 'hidden');   
        
        const $subj2 = $(this).parents('tr').find('.lcslt-wrap.lcslt-f-mg_cpt_tax_term');
        ($(this).val().indexOf('attachment|||') === -1) ? $subj2.css('visibility', 'visible') : $subj2.css('visibility', 'hidden');  
	});
	
	
	// get dynamic grid sources
	var mg_dyn_grid_sources = function() {
		var src = [];
		
		$('#mg_dgb_src_list tbody tr').each(function() {
            var pt_n_tax = $(this).find('select[name=mg_items_src]').val();
			
			var src_data = {
				pt_n_tax	: pt_n_tax,
				term		: ($(this).find('select[name=mg_cpt_tax_term]').length) ? $(this).find('select[name=mg_cpt_tax_term]').val() : '',
				mg_type	    : (pt_n_tax.indexOf('mg_items') !== -1) ? $(this).find('select[name=mg_items_type]').val() : ''
			};
			src.push(src_data);
        });
		return src;
	};
	
	
	// add block to dynamic builder
	$(document).on('click', '#mg_add_block', function() {
		if(!$('#mg_visual_builder_wrap ul .mg_box').length) {
			$('#mg_visual_builder_wrap ul').empty();
		}
		
		var $item = $('<?php echo str_replace("'", "\'", $gbe->item_code('item')); ?>');
		muuri_obj.add([ $item[0] ], {index: new_item_pos});
		
		mg_items_num_pos();
	});
	
	
	
	
	/**********************************************/
	/************* MANUAL COMPOSITION *************/
	/**********************************************/
	
	
	/*** ITEMS PICKER ***/
	var picker_page = 1;
	var querying_items = false;
		
	// update terms changing post type
	$(document).on('change', '#mg_mgb_picker_wrap select[name=mg_items_src]', function() {
		const val = $(this).val();
        query_items();
		
        
		// manage mg items type visibility
		if(val.indexOf('mg_items') !== -1) {
			$('#mg_items_type_wrap').css('visibility', 'visible');   
		} else {
			$('#mg_items_type_wrap').css('visibility', 'hidden');   
		}
		
        
        // hide for wp media lib
        if(val == 'attachment|||') {
            $('#mg_items_src_tax_wrap').css('visibility', 'hidden');   
            return false;
        }
        $('#mg_items_src_tax_wrap').css('visibility', 'visible');   
        
        
		// loader
		$('#mg_items_src_tax_wrap > *').not('label').remove();
		$('#mg_items_src_tax_wrap').append('<div style="width: 20px; height: 20px; margin-top: 5px;" class="mg_spinner mg_spinner_inline"></div>');
		
		var data = {
			action       : 'mg_sel_cpt_source',
			cpt          : val,
            lcwp_nonce   : nonce,
		};
		$.post(ajaxurl, data, function(response) {
			$('#mg_items_src_tax_wrap > div').replaceWith(response);
			live_lc_select();
		})
        .fail(function(e) {
            console.error(e);
            lc_wp_popup_message('error', "<?php esc_attr_e("Error performing the operation", 'mg_ml') ?>");
        });
	});
	
	
	// items search
	$(document).on('keyup', "#mg_gb_item_search", function() {
		if(typeof(mg_gbis_acting) != 'undefined') {clearTimeout(mg_gbis_acting);}
		mg_gbis_acting = setTimeout(function() {

			var src_string = $.trim( $("#mg_gb_item_search").val() );
			if(src_string.length) {
				$('.mg_gbis_del').fadeIn(200);
			}
			else {
				$('.mg_gbis_del').fadeOut(200);
			}	
			
			query_items();
		}, 400);
	});
	$('body').on('click', '.mg_gbis_mag', function() {
		$("#mg_gb_item_search").trigger('keyup');
	});
	$('body').on('click', '.mg_gbis_del', function() {
		$("#mg_gb_item_search").val('').trigger('keyup');
	});
	
	
	// query on term or tem type change
	$(document).on('change', "#mg_mgb_picker_wrap select[name=mg_cpt_tax_term], #mg_mgb_picker_wrap select[name=mg_items_type]", function() {
		query_items();
	});
	
	
	// change items per page
	$(document).on('keyup', "input[name=mgb_ip_limit]", function() {
		if(typeof(mg_gbpp_acting) != 'undefined') {clearTimeout(mg_gbpp_acting);}
		mg_gbpp_acting = setTimeout(function() {

			var val = parseInt($('input[name=mgb_ip_limit]', 10).val());
			
			// sanitize
			if(!val || val < 16 || val > 70) {
				$('input[name=mgb_ip_limit]').val(16);	
			}
			
			// reset page and query
			picker_page = 1;
			query_items();
		}, 400);
	});


	// next/prev page
	$(document).on('click', "input[name=mgb_ip_next], input[name=mgb_ip_prev]", function() {
		if(!querying_items) {

			if($(this).attr('name') == 'mgb_ip_next') {
				picker_page++;
			}
			else {
				picker_page--;
			}
			query_items();
		}
	});

	
	// perform items query
	const query_items = function() {
		if(!querying_items) {
			querying_items = true;	
			
			$('.mbb_ip_page_counter span').first().text(picker_page);
			$('#mg_gb_item_picker').html('<div class="mg_spinner mg_spinner_big"></div>');
			
			var data = {
				action		: 'mg_builder_query_items',
				pt_n_tax	: $('select[name=mg_items_src]').val(),
				term		: ($('select[name="mg_cpt_tax_term"]').length) ? $('select[name="mg_cpt_tax_term"]').val() : '', 
				search		: $('#mg_gb_item_search').val(), 
				mg_item_type: $('select[name="mg_items_type"]').val(), 
				per_page	: $('input[name="mgb_ip_limit"]').val(), 
				page		: picker_page,
				lcwp_nonce  : nonce,
			};
			$.post(ajaxurl, data, function(response) {
                try {
                    var resp = $.parseJSON(response);

                    $('#mg_gb_item_picker').html(resp.items);
                    $('.mbb_ip_page_counter span').last().text(resp.tot_pages);

                    if(picker_page >= resp.tot_pages) {
                        $('input[name=mgb_ip_next]').hide();	
                    } else {
                        $('input[name=mgb_ip_next]').show();		
                    }

                    if(picker_page < 2) {
                        $('input[name=mgb_ip_prev]').hide();	
                    } else {
                        $('input[name=mgb_ip_prev]').show();		
                    }
                }
                catch(e) {
                    console.error(e);
                    lc_wp_popup_message('error', "<?php esc_attr_e("Error retrieving items", 'mg_ml') ?>");        
                }
			})
            .fail(function(e) {
                console.error(e);
                lc_wp_popup_message('error', "<?php esc_attr_e("Error retrieving items", 'mg_ml') ?>");
            })
            .always(function() {
                querying_items = false;    
            });
		}
	};
		
		
	
	
	////////////////////////////////////////////////////
	



	// sortable masonry
	const chitemmuuri = function() {
		muuri_obj = new Muuri( $('#mg_sortable')[0] , {
			items: $('#mg_sortable')[0].children,
			dragEnabled: true,
			dragSortInterval: 50,
			
            dragStartPredicate: function (item, event) {
                if(!event.isFinal && event.target.nodeName === 'SELECT') {
                    return false;
                }
                
                return Muuri.ItemDrag.defaultStartPredicate(item, event, {
                    distance: 40,
                    delay: 40,
                });
            },
			layout : {
				fillGaps 	: true,
				alignRight 	: <?php echo (get_option('mg_rtl_grid')) ? 'true' : 'false' ?>,
				rounding	: false
			},
		});
		
		// after a while - re-layout to fix initial bad positioning
		setTimeout(function() {
			$('#mg_sortable').addClass('mg_muurified');
			mg_relayout_grid();
			mg_items_num_pos();
			
			$('.mg_spacer_vis_dd').trigger('change');
			
			// update numeration on drag end
			muuri_obj.off('dragEnd').on('dragEnd', function (item, event) {
				 mg_items_num_pos();
			});
		}, 70);
	};
	
	
	// be sure grid has always an even width
	const mg_evenize_grid_w = function() {
		var $grid = $('.mg_muurified');
		if(!$grid.length) {
            return false;
        }
		
		// is even - ok
		if($grid.outerWidth() % 2 == 0) {
			return true;
		}
		else {
			// toggle mg_not_even_w class?	
			$('#mg_visual_builder_wrap').toggleClass('mg_not_even_w');
			mg_relayout_grid();
		}
	};
	setInterval(mg_evenize_grid_w, 300);
	

	// re-layout grid
	const mg_relayout_grid = function() {
		var $grid = $('.mg_muurified');
		if(!$grid.length) {
            return false;
        }
		
		muuri_obj.refreshItems();
		muuri_obj.layout(true);	
	};
	
	
	// items numeric position - adds also true numeration attr for arrow move
	var mg_items_num_pos = function() {
		var $grid = $('.mg_muurified');
		if(!$grid.length) {
            return false;
        }
		
		muuri_obj.synchronize();
	
		var a = 1
		var b = 0;
		muuri_obj.getItems().forEach(function (item, i) {
			
			$(item._element).attr('data-position', b);
			var $item_num = $(item._element).find('.mg_item_num');

			if($item_num.length) {
				$item_num.text(a);
				a++;	
			}
			
			b++;
		});
	};
	
	
	///////////////////////////
	
	
	// add item
	$(document).on('click', '#mg_gb_item_picker li', function() {
		
		// if is v5 spacer - block and inform
		if($(this).find('.mgi_spacer').length) {
			alert("<?php echo esc_attr( __('Since Media Grid v6, spacer must be added using the button on top of grid preview. Please update your grids and delete this item.')) ?>");
			return false;	
		}
		
		var $subj = $(this); 
		$subj.fadeTo(200, 0.7);

		var data = {
			action       : 'mg_add_item_to_builder',
			item_id      : $subj.attr('rel'),
			lcwp_nonce  : nonce,
		};
		$.post(ajaxurl, data, function(response) {
			if(!$('#mg_visual_builder_wrap ul .mg_box').length) {
				$('#mg_visual_builder_wrap ul').empty();
			}
			
			var $item = $(response);
			muuri_obj.add([ $item[0] ], {index: new_item_pos});
			mg_items_num_pos();
			
			// re-layout after a little while to avoid any sizing issue
			setTimeout(function() {
				mg_relayout_grid();
			}, 100);
		})
        .fail(function(e) {
            console.error(e);
            lc_wp_popup_message('error', "<?php esc_attr_e("Error adding item", 'mg_ml') ?>");
        })
        .always(function() {
            $subj.fadeTo(200, 1);
        });
	});
	
	
	// add paginator block
	$(document).on('click', '#mg_add_paginator', function() {
		var $grid = $('.mg_muurified');
		if(!$grid.length) {
            return false;
        }

		var $item = $('<?php echo str_replace("'", "\'", $gbe->paginator_code()); ?>');
		muuri_obj.add([ $item[0] ], {index: new_item_pos});
		
		mg_items_num_pos();
	});
	
	
	// add spcer block
	$(document).on('click', '#mg_add_spacer', function() {
		var $grid = $('.mg_muurified');
		if(!$grid.length) {
            return false;
        }

		var $item = $('<?php echo str_replace("'", "\'", $gbe->item_code('spacer')); ?>');
		muuri_obj.add([ $item[0] ], {index: new_item_pos});
		
		mg_items_num_pos();
	});
	
	
	// spacer visibility between modes
	$(document).on('change', '.mg_spacer_vis_dd', function() {
		var $item = $(this).parents('.mg_box');
		
		switch($(this).val()) {
			case 'hidden_desktop' :
				if(mg_mobile) {
					$item.show();	
				} else {
					$item.hide();	
				}
				break;
			
			case 'hidden_mobile' :
				if(mg_mobile) {
					$item.hide();	
				} else {
					$item.show();	
				}
				break;
		
			default :
				$item.show();	 
				break;	
		}
		
		mg_relayout_grid();
	});
	
	
	
	// remove item
	$(document).on('click', '.del_item', function() {
		var $grid = $('.mg_muurified');
		if(!$grid.length) {
            return false;
        }
		
		if(confirm('<?php esc_attr_e('Remove item?', 'mg_ml') ?>')) {
			const item_pos = parseInt( $(this).parents('.mg_box').data('position'), 10); 

            muuri_obj.remove(
                muuri_obj.getItems(item_pos),
                {
                    removeElements: true
                }
            );
			mg_items_num_pos();
		}
	});


    
	///////////////////////////

    
	
	/*** standard layout - live sizing ***/
	// box resize width
	$(document).on('change', '#mg_visual_builder_wrap .select_w', function() {
		$(this).parents('.mg_box').attr('data-w', $(this).val()); // .data() doesn't work .. dunno why..!
		
		if(!mg_easy_sorting) {
			mg_relayout_grid();
		}
	});
	
	
	// box resize height
	$(document).on('change', '#mg_visual_builder_wrap .select_h', function() {
		$(this).parents('.mg_box').attr('data-h', $(this).val());
		
		if(!mg_easy_sorting) {
			mg_relayout_grid();
		}
	});
	
	
	/*** mobile layout - live sizing ***/
	// box resize width
	$(document).on('change', '#mg_sortable .select_m_w', function() {
		$(this).parents('.mg_box').attr('data-mw', $(this).val());
		
		if(!mg_easy_sorting) {
			mg_relayout_grid();
		}
	});
	
	
	// box resize height
	$(document).on('change', '#mg_sortable .select_m_h', function() {
		$(this).parents('.mg_box').attr('data-mh', $(this).val());
		
		if(!mg_easy_sorting) {
			mg_relayout_grid();
		}
	});
	
		
	
	///////////////////////////
	
	
	
	// mobile mode toggle 
	$(document).on('click', '#mg_mobile_view_toggle', function() {
		
		if($('.mg_mobile_builder').length) {
			$(this).find('span').text('OFF');
			mg_mobile = false;
		}
		else {
			$(this).find('span').text('ON');
			mg_mobile = true;	
		}
		
		$(this).toggleClass('mg_active');
		$('.mg_grid_builder_main').toggleClass('mg_mobile_builder');
		
		// bulk sizers - switch dropdowns
		if(mg_mobile) {
			$('#mg_bulk_w, #mg_bulk_h').hide();	
			$('#mg_bulk_mw, #mg_bulk_mh').show();	
		} else {
			$('#mg_bulk_w, #mg_bulk_h').show();	
			$('#mg_bulk_mw, #mg_bulk_mh').hide();		
		}

		$('.mg_spacer_vis_dd').trigger('change');
		mg_relayout_grid();
	});
	
	
	// easy sorting mode toggle
	$(document).on('click', '#mg_easy_sorting_toggle', function() {
		
		if($('.mg_easy_sorting').length) {
			$(this).find('span').text('OFF');
			mg_easy_sorting = false;
		} 
		
		// activate
		else {
			$(this).find('span').text('ON');
			mg_easy_sorting = true;
		}
		
		$(this).toggleClass('mg_active');
		$('#mg_sortable').toggleClass('mg_easy_sorting');	
		mg_relayout_grid();
	});
	
	
	//// bulk sizing system
	// width
	$(document).on('click', '#mg_bulk_w_btn', function() {
		if(confirm("<?php esc_attr_e('Every grid item will be affected, continue?') ?>")) {
			var val = (mg_mobile) ? $('#mg_bulk_mw').val() : $('#mg_bulk_w').val();
			var dd_class = (mg_mobile) ? '.select_m_w' : '.select_w';
			
			$('#mg_sortable .mg_box '+dd_class+' option').attr('selected', false);
			$('#mg_sortable .mg_box '+dd_class+' option[value="'+val+'"]').attr('selected', 'selected');
			
			$('#mg_sortable '+dd_class).trigger('change');
		}
	});
	
	// height
	$(document).on('click', '#mg_bulk_h_btn', function() {
		if(confirm("<?php esc_attr_e('Every grid item will be affected, continue?') ?>")) {
			var val = (mg_mobile) ? $('#mg_bulk_mh').val() : $('#mg_bulk_h').val();
			var dd_class = (mg_mobile) ? '.select_m_h' : '.select_h';
			
			if(val == 'auto') {
				$('#mg_sortable .mg_box').not('.mg_inl_slider_type, .mg_inl_video_type').find(dd_class+' option').attr('selected', false);
				$('#mg_sortable .mg_box').not('.mg_inl_slider_type, .mg_inl_video_type').find(dd_class+' option[value="'+val+'"]').attr('selected', 'selected');
			} else {
				$('#mg_sortable .mg_box '+dd_class+' option').attr('selected', false);
				$('#mg_sortable .mg_box '+dd_class+' option[value="'+val+'"]').attr('selected', 'selected');
			}
				
			$('#mg_sortable '+dd_class).trigger('change');
		}
	});
	
	
	// move item with arrows
	$(document).on('click', '.mg_move_item_bw, .mg_move_item_fw', function(ui) {
		var $grid = $('.mg_muurified');
		if(!$grid.length) {
            return false;
        }
	
		var $item = $(this).parents('li');
		var curr_pos = parseInt( $item.attr('data-position'), 10);
		
		var opt = {
			action : 'swap'
		};

		// backwards
		if($(this).hasClass('mg_move_item_bw') && curr_pos) {
			var $to_swap = $('.mg_box[data-position="'+ (curr_pos - 1) +'"]');
			muuri_obj.move($item[0], $to_swap[0], opt);
		}
		
		// forwards
		if($(this).hasClass('mg_move_item_fw') && curr_pos < ($('.mg_box').length - 1)) {
			var $to_swap = $('.mg_box[data-position="'+ (curr_pos + 1) +'"]');
			muuri_obj.move($item[0], $to_swap[0], opt);
		}

		mg_relayout_grid();
		mg_items_num_pos();
	});
	

	
	///////////////////////////


	// disable enter key on input fields
	$(document).on("keypress", ".form-wrap", function(event) { 
		return event.keyCode != 13;
	});



	// keep sidebar visible
	$(window).scroll(function() {
		var $subj = $('.mg_grid_builder_side');
		
		if($subj.find('.postbox').length) {
			var side_h = $('.mg_grid_builder_side').outerHeight();
			var top_pos = $('.mg_grid_builder_side').parent().offset().top;
			var top_scroll = $(window).scrollTop();
			
			// if is higher that window - ignore
			if((top_pos + side_h + 44) >= $(window).height() || top_scroll <= top_pos) {
				$subj.css('margin-top', 0);	
			}
			else {
				$subj.css('margin-top', (top_scroll - top_pos + 44)); 	
			}	
		}
		else {
			$subj.css('margin-top', 0);	
		}
	});
    
    
    
    // lc switch
    const live_lc_switch = function() {
        lc_switch('.mg_lcs_check', {
            on_txt      : "<?php echo strtoupper(__('yes')) ?>",
            off_txt     : "<?php echo strtoupper(__('no')) ?>",   
        });
    };
    
    
    // LC select
	const live_lc_select = function() {
        new lc_select('.mg_lcsel_dd', {
            wrap_width : '90%',
            addit_classes : ['lcslt-lcwp'],
        });
	}
	live_lc_select();
    
});
})(jQuery); 
</script>
