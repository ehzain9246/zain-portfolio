<?php

// IMPLEMENTING TINYMCE LIGHTBOX
	
function mg_action_admin_init() {
	if( !current_user_can('edit_posts') && !current_user_can('edit_pages') )
		return;

	if(get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'mg_filter_mce_plugin');
		add_filter('mce_buttons', 'mg_filter_mce_button');
	}
}
add_action('admin_init', 'mg_action_admin_init');

	
function mg_filter_mce_button( $buttons ) {
	array_push( $buttons, 'mg_btn');
	return $buttons;
}

function mg_filter_mce_plugin( $plugins ) {
	$plugins['lcweb_mediagrid'] = MG_URL . '/js/tinymce_btn.js';
	return $plugins;
}





function mg_editor_btn_content() {
	if(strpos($_SERVER['REQUEST_URI'], 'post.php') === false && strpos($_SERVER['REQUEST_URI'], 'post-new.php') === false && !isset($GLOBALS['mg_tinymce_editor'])) {
        return false;
    }
?>

	<div id="mediagrid_sc_wizard" style="display:none;">
    	<div class="lcwp_scw_choser_wrap mg_scw_choser_wrap">
            <select name="mg_scw_choser" class="lcwp_scw_choser mg_scw_choser" autocomplete="off">
                <option value="#mg_sc_main" selected="selected"><?php esc_html_e('Main parameters', 'mg_ml') ?></option>
                <option value="#mg_sc_style"><?php _e('Custom styles', 'mg_ml') ?></option>	
            </select>	
        </div>
        
        
		<div id="mg_sc_main" class="lcwp_scw_block mg_scw_block"> 
            <ul>
                <li class="lcwp_scw_field mg_scw_field">
                	<label><?php _e('Which grid?', 'mg_ml') ?></label>
               		<select id="mg_grid_choose" name="mg_grid_choose" autocomplete="off">
						<?php
						foreach(get_terms('mg_grids', array('hide_empty' => 0, 'orderby' => 'name')) as $grid) {
							echo '<option value="'. $grid->term_id .'">'. $grid->name .'</option>';	
						}
                        ?>
                	</select>
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Text under items?', 'mg_ml') ?></label>
               		<select id="mg_title_under" name="mg_title_under" autocomplete="off">
					
                    	<option value="0"><?php esc_html_e('No') ?></option>
                        <option value="1"><?php esc_html_e('Yes - attached to item', 'mg_ml') ?></option>
                        <option value="2"><?php esc_html_e('Yes - detached from item', 'mg_ml') ?></option>
                	</select>
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Pagination system', 'mg_ml') ?></label>
               		<select id="mg_pag_sys" name="mg_pag_sys" autocomplete="off">
						
                        <option value="">(<?php esc_html_e('default one', 'mg_ml') ?>)</option>
                    	<?php
						foreach(mg_static::pag_layouts() as $type => $name) {
                        	echo '<option value="'. $type .'">'. $name .'</option>';	
						}
						?>
                	</select>
                </li>
                <li class="lcwp_scw_field mc_scw_field lcwp_scwf_half">
                	<label><?php _e('Enable filters?', 'mg_ml') ?></label>
                    
                    <?php 
					
					///// ADVANCED FILTERS ADD-ON //////////
					////////////////////////////////////////  use a dropdown to set custom filters
					$filters_list = (class_exists('mgaf_static')) ? mgaf_static::filters_list() : array();
					
					if(!empty($filters_list)) :
					?>
						<select id="mg_filter_grid" name="mg_filter_grid" autocomplete="off">
                        	<option value="0"><?php esc_html_e('No', 'mg_ml') ?></option>
                            <option value="1"><?php esc_html_e('Yes (MG categories)', 'mg_ml') ?></option>
                            
                            <?php 
							foreach($filters_list as $filter_id => $filter_name) {
                            	echo '<option value="'. esc_attr($filter_id) .'">'. esc_html($filter_name) .'</option>';
							} ?> 
                        </select>
					<?php else : ?>
                    
                  		<input type="checkbox" id="mg_filter_grid" name="mg_filter_grid" value="1" autocomplete="off" />
                	<?php endif;
					////////////////////////////////////////
					
					 ?>
                </li>
                <li class="lcwp_scw_field mc_scw_field lcwp_scwf_half">
                	<label><?php _e('Enable search?', 'mg_ml') ?></label>
                    <input type="checkbox" id="mg_search_bar" name="mg_search_bar" value="1" autocomplete="off" />
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half mg_scw_ff" style="display: none;">
                	<label><?php _e('Filters position', 'mg_ml') ?></label>
               		<select id="mg_filters_align" name="mg_filters_align" autocomplete="off">
					
                    	<option value="top"><?php esc_html_e('On top', 'mg_ml') ?></option>
                        <option value="left"><?php esc_html_e('Left side', 'mg_ml') ?></option>
                        <option value="right"><?php esc_html_e('Right side', 'mg_ml') ?></option>
                	</select>
                </li>
                <li class="lcwp_scw_field mc_scw_field lcwp_scwf_half mg_scw_ff" style="display: none;">
                	<label><?php _e('Hide "All" filter? <em>(to show every item)</em>', 'mg_ml') ?></label>
                    <input type="checkbox" id="mg_hide_all" name="mg_hide_all" value="1" autocomplete="off" />
                </li>
                <li class="lcwp_scw_field mg_scw_field mg_scw_ff mg_scw_def_filter lcwp_scwf_half" style="display: none;">
                	<label><?php _e('Default filter', 'mg_ml') ?></label>
               		<select id="mg_def_filter" name="mg_def_filter" autocomplete="off">
						
                        <option value="">(<?php esc_html_e('none', 'mg_ml') ?>)</option>
						<?php
						foreach(mg_static::item_cats() as $cat_id => $cat_name) {
                        	echo '<option value="'. $cat_id .'">'. $cat_name .'</option>';	
						}
						?>
                	</select>
                </li>
                <li class="lcwp_scw_field mc_scw_field lcwp_scwf_half">
                	<label><?php _e('Media-focused lightbox mode?', 'mg_ml') ?></label>
                    <select name="mg_mf_lb" autocomplete="off">
					
                    	<option value="">(<?php esc_html_e('as default', 'mg_ml') ?>)</option>
                        <option value="1"><?php esc_html_e('yes') ?></option>
                        <option value="0"><?php esc_html_e('no') ?></option>
                	</select>
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label>
						<?php _e('Custom mobile threshold', 'mg_ml') ?> 
                        <span class="dashicons dashicons-info" title="<?php echo esc_attr(__('Overrides global threshold. Leave empty to ignore', 'mg_ml')) ?>" style="cursor: help; opacity: 0.3;"></span>
                        </label>
               		<input type="number" step="10" min="50" max="2000" name="mg_mobile_treshold" id="mg_mobile_treshold" value="" autocomplete="off" /> px
                </li>
                
                <?php 
				// MG-OPTION - allow custom fields insertion into main scw options - structure must comply with existing one
				do_action('mg_scw_main_opts');
				?>
                
                
				<?php 
				///// OVERLAY MANAGER ADD-ON ///////////
				////////////////////////////////////////
				if(defined('MGOM_DIR')) : ?>
				<li class="lcwp_scw_field mg_scw_field">
                	<label><?php _e('Custom Overlay', 'mg_ml') ?></label>
               		<select id="mg_custom_overlay" name="mg_custom_overlay" autocomplete="off">
						
                        <option value="">(<?php esc_html_e('default one', 'mg_ml') ?>)</option>
						<?php
						$overlays = get_terms('mgom_overlays', 'hide_empty=0');
						foreach($overlays as $ol) {
							  $sel = (isset($fdata) && $ol->term_id == $fdata['mg_default_overlay']) ? 'selected="selected"' : '';
							  echo '<option value="'. $ol->term_id .'" '.$sel.'>'. esc_html($ol->name) .'</option>'; 
						}
						?>
                	</select>
                </li>
				<?php endif;
				////////////////////////////////////////
				?>   
                
                
                <li class="lcwp_scw_field mg_scw_field">
                	<input type="button" class="button-primary mg_sc_insert_grid" value="<?php esc_attr_e('Insert Grid', 'mg_ml') ?>" name="submit" />
                </li>
			</ul>
    	</div>
        
        
        <div id="mg_sc_style" class="lcwp_scw_block mg_scw_block"> 
            <ul>
                <li class="lcwp_scw_field mg_scw_field">
                	<strong>NB: <?php _e('leave empty textual fields to use global values', 'mg_ml') ?></strong>
	            </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Items margin', 'mg_ml') ?></label>
               		<input type="number" name="mg_cells_margin" id="mg_cells_margin" value="" autocomplete="off" min="0" step="1" max="100" /> px
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Item borders width', 'mg_ml') ?></label>
               		<input type="number" name="mg_border_w" id="mg_border_w" value="" autocomplete="off" min="0" step="1" max="20" /> px
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Item borders color', 'mg_ml') ?></label>
               		<input type="text" name="mg_border_color" id="mg_border_color" value="" class="mg_sc_col_f" autocomplete="off" />
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Items border radius', 'mg_ml') ?></label>
               		<input type="number" name="mg_cells_radius" id="mg_cells_radius" value="" autocomplete="off" min="0" step="1" max="100" /> px
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e("Display items outline?", 'mg_ml') ?></label>
               		<select id="mg_outline" name="mg_outline" autocomplete="off">
                        <option value="">(<?php esc_html_e('as default', 'mg_ml') ?>)</option>
                        <option value="1"><?php esc_html_e('Yes') ?></option>
                        <option value="0"><?php esc_html_e('No') ?></option>
                	</select>
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Outline color', 'mg_ml') ?></label>
                    <input type="text" name="mg_outline_color" id="mg_outline_color" value="" class="mg_sc_col_f" autocomplete="off" />
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e("Display items shadow?", 'mg_ml') ?></label>
               		<select id="mg_shadow" name="mg_shadow" autocomplete="off">
                        <option value="">(<?php esc_html_e('as default', 'mg_ml') ?>)</option>
                        <option value="1"><?php esc_html_e('Yes') ?></option>
                        <option value="0"><?php esc_html_e('No') ?></option>
                	</select>
                </li>
                <li class="lcwp_scw_field mg_scw_field lcwp_scwf_half">
                	<label><?php _e('Text under images color', 'mg_ml') ?></label>
               		<input type="text" name="mg_txt_under_color" id="mg_txt_under_color" value="" class="mg_sc_col_f" autocomplete="off" />
                </li>
               
                <?php 
				// MG-OPTION - allow custom fields insertion into style scw options - structure must comply with existing one
				do_action('mg_scw_style_opts');
				?>
                
                <li class="lcwp_scw_field mg_scw_field">
                	<input type="button" class="button-primary mg_sc_insert_grid" value="<?php _e('Insert Grid', 'mg_ml') ?>" name="submit" />
                </li>
    		</ul>
    	</div> 
	</div> 
<?php    
}
add_action('admin_footer', 'mg_editor_btn_content');
