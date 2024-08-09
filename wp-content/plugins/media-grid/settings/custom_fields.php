<?php 

// preset styles preview and setter 
function mg_preset_styles($field_id, $field, $value, $all_vals) {
	
	// build code
	echo '
	<table id="mg_preset_styles_cmd_wrap" class="widefat lcwp_settings_table">
		<tr class="mg_'. $field_id .'">
			<td class="lcwp_sf_label"><label>'. __('Choose style', 'mg_ml') .'</label></td>
			<td class="lcwp_sf_field">
				<select name="'. $field_id .'" id="mg_pred_styles" class="lcwp_sf_select mg_pred_styles_cf_select" autocomplete="off">
					<option value="">('. esc_html__('choose an option to preview', 'mg_ml') .')</option>';
				
					foreach(mg_preset_style_names() as $id => $name) {
						echo '<option value="'. esc_attr($id) .'">'. $name .'</option>';	
					}
		  echo '
				</select>   
			</td>
			<td style="width: 50px;">
				<input name="mg_set_style" id="mg_set_style" value="'. esc_attr__('Set', 'mg_ml') .'" class="button-secondary" type="button" />
			</td>
			<td><p class="lcwp_sf_note">'. __('Overrides styling options and applies preset styles', 'mg_ml') .'. '. __('Once applied, <strong>page will be reloaded</strong> showing updated options', 'mg_ml') .'</p></td>
		</tr>
		<tr class="mg_displaynone">
			<td class="lcwp_sf_label"><label>'. __('Preview', 'mg_ml') .'</label></td>
			<td colspan="3" id="mg_preset_styles_preview"></td>
		</tr>
	</table>';
	
	?>
    <script type="text/javascript">
    (function($) { 
        "use strict";    

        // predefined style - preview toggle
        $(document).on("change", '#mg_pred_styles', function() {
            var sel = $('#mg_pred_styles').val();

            if(!sel) {
                $('#mg_preset_styles_cmd_wrap tr').last().hide();
                $('#mg_preset_styles_preview').empty();	
            }
            else {
                $('#mg_preset_styles_cmd_wrap tr').last().show();

                var img_url = '<?php echo MG_URL ?>/img/preset_styles_demo/'+ sel +'.jpg';
                $('#mg_preset_styles_preview').html('<img src="'+ img_url +'" />');		
            }
        });


        // set predefined style 
        $(document).on('click', '#mg_set_style', function() {
            var sel_style = $('#mg_pred_styles').val();
            if(!sel_style) {
                return false;
            }

            if(confirm('<?php esc_attr_e('This will overwrite your current settings, continue?', 'mg_ml') ?>')) {
                $(this).replaceWith('<div style="width: 30px; height: 30px;" class="mg_spinner mg_spinner_inline"></div>');

                var data = {
                    action     : 'mg_set_predefined_style',
                    style      : sel_style,
                    lcwp_nonce : '<?php echo wp_create_nonce('lcwp_nonce') ?>'
                };
                $.post(ajaxurl, data, function(response) {
                    if($.trim(response) == 'success') {
                        lc_wp_popup_message('success', "<?php esc_attr_e('Style successfully applied!', 'mg_ml') ?>");	

                        setTimeout(function() {
                            window.location.reload();	
                        }, 1500);
                    }
                    else {
                        lc_wp_popup_message('error', response);	
                    }
                })
                .fail(function(e) {
                    console.error(e);
                    lc_wp_popup_message('error', "Error applying preset style");	
                });	
            }
        });
    })(jQuery); 
    </script>
    <?php
}



// Easy WP thumbs - status
function mg_ewpt_status($field_id, $field, $value, $all_vals) {
	?>
	<table id="mg_ewpt_status_wrap" class="widefat lcwp_settings_table">
		<tr class="mg_<?php echo $field_id ?>">
			<td>
            	<input type="hidden" name="ewpt_status_f" value="" /> <?php //JS vis trick ?>
				<?php ewpt_wpf_form('mg_ml'); ?>
			</td>
		</tr>	
	</table>
    
    <script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        const $ = jQuery;
        
        const toggle = function() {
            ($('select[name="mg_thumbs_engine"]').val() == 'ewpt') ? $('#mg_ewpt_status_wrap').show() : $('#mg_ewpt_status_wrap').hide();        
        };
        toggle(); // on opening
        
        $(document).on('change', 'select[name="mg_thumbs_engine"]', function() {
            toggle();        
        });
    });
	</script>
    <?php
}




// Lightbox overlay pattern
function mg_item_overlay_pattern_f($field_id, $field, $value, $all_vals) {
	$no_pattern_sel = (!$value || $value == 'none') ? 'mg_pattern_sel' : '';
	
	echo '
	<tr class="pc_'. $field_id .'">
		<td class="lcwp_sf_label"><label>'. __("Overlay's pattern", 'mg_ml') .'</label></td>
		<td class="lcwp_sf_field" colspan="2" style="padding-bottom: 0;">
    		<input type="hidden" value="'. $value .'" name="mg_item_overlay_pattern" id="mg_item_overlay_pattern" />
			
			<div class="mg_setting_pattern '.$no_pattern_sel.'" rel="none"> no pattern </div>';
			
			foreach(mg_static::patterns_list() as $pattern) {
				$sel = ($value == $pattern) ? 'mg_pattern_sel' : '';  
				echo '<div class="mg_setting_pattern '.$sel.'" rel="'.$pattern.'" style="background: url('.MG_URL.'/img/patterns/'.$pattern.') repeat top left transparent;"></div>';		
			}
	
	echo '
		</td>
	</tr>';
	
	?>
	<script type="text/javascript">
    jQuery(document).ready(function($) {
		$(document).on('click', '.mg_setting_pattern', function() { // select a pattern
			$('.mg_setting_pattern').removeClass('mg_pattern_sel');
			$(this).addClass('mg_pattern_sel'); 
			
			$('#mg_item_overlay_pattern').val( $(this).attr('rel') );
		});
	});
	</script>
    <?php	
}





// Item type attributes
function mg_item_atts_f($field_id, $field, $value, $all_vals) {
	echo '<div id="mg_type_opt_wrap">';
	
	// WPML / Polylang sync button
	if(function_exists('icl_register_string')) {
		echo '
		<p id="mg_wpml_opt_sync_wrap">
			<input type="button" value="'. esc_attr( __('Sync with WPML', 'mg_ml')) .'" class="button-secondary" />
			<span><em>'. __('Remember to save settings before sync', 'mg_ml') .'</em></span>
		</p>';	
	} 
	elseif(function_exists('pll_register_string')) {
		echo '
		<p id="mg_wpml_opt_sync_wrap">
			<input type="button" value="'. esc_attr( __('Sync with Polylang', 'mg_ml')) .'" class="button-secondary" />
			<span><em>'. __('Remember to save settings before sync', 'mg_ml') .'</em></span>
		</p>';	
	} 
			
    
	foreach(mg_static::main_types() as $type => $name) :
	?>
    	<div class="mg_type_opt_block">
            <h3>
                <?php echo $name ?>
                <a id="opt_<?php echo $type; ?>" href="javascript:void(0)" class="mg_type_opt_add_option page-title-action"><?php _e('Add option', 'mg_ml') ?></a>
            </h3>
            <table class="widefat" id="<?php echo $type; ?>_opt_table">
            	<thead>
              		<tr>
                        <th style="width: 30px;"><?php _e('Icon', 'mg_ml') ?></th>
                        <th><?php _e('Attribute Name', 'mg_ml') ?></th>
                        <th style="width: 15px;"></th>
                        <th style="width: 15px;"></th>
              		</tr>
              	</thead>
              	<tbody>
                <?php
                if(is_array($all_vals['mg_'.$type.'_opt']) && count($all_vals['mg_'.$type.'_opt'])) {
                    $a = 0;
                    foreach($all_vals['mg_'.$type.'_opt'] as $type_opt) {
                        $icon = (isset($all_vals['mg_'.$type.'_opt_icon'][$a])) ? mg_static::fontawesome_v4_retrocomp($all_vals['mg_'.$type.'_opt_icon'][$a]) : '';
                        
                        echo '
                        <tr>
                            <td class="mg_icon_trigger">
                                <i class="'. esc_attr($icon) .'" title="'. esc_attr__("set attribute's icon", 'mg_ml') .'"></i>
                                <input type="hidden" name="mg_'. $type .'_opt_icon[]" value="'. esc_attr($icon) .'" autocomplete="off" />
                            </td>
                            <td>
                                <input type="text" name="mg_'. $type .'_opt[]" value="'. esc_attr($type_opt) .'" maxlenght="150" autocomplete="off" />
                            </td>
                            <td><span class="mg_move_row dashicons dashicons-move"></span></td>
                            <td><span class="mg_del_row dashicons dashicons-no-alt"></span></td>
                        </tr>';	
                        
                        $a++;
                    }
                }
				else  {
					 echo '
                        <tr>
                            <td class="mg_icon_trigger">
                                <i class="" title="'. esc_attr__("set attribute's icon", 'mg_ml') .'"></i>
                                <input type="hidden" name="mg_'. $type .'_opt_icon[]" value="" autocomplete="off" />
                            </td>
                            <td class="mg_field_td">
                                <input type="text" name="mg_'. $type .'_opt[]" value="" maxlenght="150" autocomplete="off" />
                            </td>
                            <td><span class="mg_move_row dashicons dashicons-move"></span></td>
                            <td><span class="mg_del_row dashicons dashicons-no-alt"></span></td>
                        </tr>';	
				}
                ?>
				</tbody>
            </table>
    	</div>        
	<?php endforeach; ?>
    
    <?php 
	// WOOCOMMERCE ATTRIBUTES
	if($GLOBALS['mg_woocom_active'] && is_array($GLOBALS['mg_woocom_atts']) && count($GLOBALS['mg_woocom_atts'])) :
	?>
    	<div class="mg_type_opt_block">
            <h3 style="border: none;"><?php _e('WooCommerce products', 'mg_ml') ?></h3>
            <table class="widefat mg_table" id="woocom_opt_table" style="width: 100%; max-width: 450px;">
            	<thead>
              		<tr>
                		<th style="width: 30px;"><?php _e('Icon', 'mg_ml') ?></th>
                		<th><?php _e('Attribute Name', 'mg_ml') ?></th>
              		</tr>
            	</thead>
             	<tbody>
					<?php
					foreach($GLOBALS['mg_woocom_atts'] as $attr) {
						$icon = (isset($all_vals['mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon'])) ? 
							$all_vals['mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon'] : '';
						
						echo '
						<tr>
							<td class="mg_icon_trigger">
								<i class="fa '.esc_attr($icon).'" title="'. __("set attribute's icon", 'mg_ml') .'"></i>
								<input type="hidden" name="mg_wc_attr_'. sanitize_title($attr->attribute_label) .'_icon" value="'.esc_attr($icon).'" />
							</td>
							<td class="mg_field_td">
								'. $attr->attribute_label .'
							</td>
						</tr>';	
					}
                ?>
            	</tbody>
            </table>
    	</div>
    <?php endif;
	
	echo '</div>'; // wrapper's closing
	
	
	// ITEM ATTRIBUTES - ICON WIZARD
	echo mg_static::fa_icon_picker_code( __('no icon', 'mg_ml') );
	?>
	
    
    <script type="text/javascript">
    (function($) { 
        "use strict";     
        
        $(document).ready(function($) {

            // WPML sync button
            $(document).on('click', '#mg_wpml_opt_sync_wrap input', function() {
                $('#mg_wpml_opt_sync_wrap span').html('<div class="mg_spinner"></div>');

                var data = {
                    action: 'mg_options_wpml_sync'
                };
                $.post(ajaxurl, data, function(response) {
                    var resp = $.trim(response);

                    if(resp == 'success') {
                        $('#mg_wpml_opt_sync_wrap span').html('<?php esc_attr_e('Options synced succesfully!', 'mg_ml') ?>');
                    } else {
                        $('#mg_wpml_opt_sync_wrap span').html('<?php esc_attr_e('Error syncing', 'mg_ml') ?> ..');
                    }
                })
                .fail(function(e) {
                    console.error(e);
                    $('#mg_wpml_opt_sync_wrap span').html('<?php esc_attr_e('Error syncing', 'mg_ml') ?> ..');
                });	
            });


            // launch option icon wizard
            <?php mg_static::fa_icon_picker_js(); ?>


            // add options
            $('.mg_type_opt_add_option').on('click', function(){
                var type_subj = $(this).attr('id').substr(4);

                var optblock = `
                '<tr>
                    <td class="mg_icon_trigger">
                        <i class="fa" title="<?php esc_attr_e("set attribute's icon", 'mg_ml') ?>"></i>
                        <input type="hidden" name="mg_'+ type_subj +'_opt_icon[]" value="" />
                    </td>
                    <td class="mg_field_td">
                        <input type="text" name="mg_${ type_subj }_opt[]" maxlenght="150" autocomplete="off" />
                    </td>
                    <td><span class="mg_move_row dashicons dashicons-move"></span></td>
                    <td><span class="mg_del_row dashicons dashicons-no-alt"></span></td>
                </tr>`;

                $('#'+ type_subj + '_opt_table tbody').append(optblock);
            });


            // remove opt 
            $(document).on("click", '.lcwp_del_row', function() {
                if(confirm('<?php esc_attr_e('WARNING: deleting this option, also related item values will be lost. Continue?', 'mg_ml') ?>')) {
                    $(this).parents('tr').first().remove();
                }
            });


            // sort opt
            $('#mg_type_opt_wrap table').each(function() {
                $(this).children('tbody').sortable({
                    items: "> tr",
                    handle: '.mg_move_row'
                });
                $(this).find('.mg_move_row').disableSelection();
            });

        });
        
    })(jQuery);     
    </script>
	<?php	
}
