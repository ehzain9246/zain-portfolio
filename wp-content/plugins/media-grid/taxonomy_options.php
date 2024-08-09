<?php

// add the order field
add_action('mg_item_categories_add_form_fields','mg_cat_cust_fields', 10, 2 );
add_action('mg_item_categories_edit_form_fields' , "mg_cat_cust_fields", 10, 2);


function mg_cat_cust_fields($tax_data) {
    dike_lc('lcweb', MG_DIKE_SLUG, true);
    
    $icon = '';
    $order = 0;

    // check for existing taxonomy meta for term ID
    if(is_object($tax_data)) {
        $term_id = $tax_data->term_id;
        $icon = mg_static::retrocomp_get_term_meta($term_id, 'mg_cat_icon', "mg_cat_". $term_id ."_icon", '');
        $order = (int)mg_static::retrocomp_get_term_meta($term_id, 'mg_cat_order', "mg_cat_". $term_id ."_order", 0);
    }
    
	
	if(!is_object($tax_data)) :
?>
		<div class="form-field">
            <label><?php _e('Icon', 'mg_ml') ?></label>
            
            <div class="mg_icon_trigger">
            	<i class="<?php echo $icon ?>" title="<?php esc_attr_e('set category icon', 'mg_ml') ?>"></i>
                <input type="hidden" name="mg_cat_icon" value="<?php echo $icon ?>" autocomplete="off" /> 
            </div>
            <p><?php _e('Category icon, shown before the category name', 'mg_ml') ?></p>
        </div>
		<div class="form-field">
            <label><?php _e('Order', 'mg_ml') ?></label>
            
           	<input type="number" name="mg_cat_order" value="<?php echo $order ?>" min="0" step="10" max="1000" class="mg_cat_order" /> 
            <p><?php _e('Category order used in grid filters', 'mg_ml') ?></p>
        </div>
	<?php
        
	else:
    
	?>
    <tr class="form-field">
      <th scope="row" valign="top"><label><?php _e('Icon', 'mg_ml') ?></label></th>
      <td>
        <div class="mg_icon_trigger">
            <i class="<?php echo $icon ?>" title="<?php esc_attr_e('set category icon', 'mg_ml') ?>"></i>
            <input type="hidden" name="mg_cat_icon" value="<?php echo $icon ?>" /> 
        </div>
        <p class="description"><?php _e('Category icon, shown before the category name', 'mg_ml') ?></p>
      </td>
    </tr>
	<tr class="form-field">
      <th scope="row" valign="top">
          <label><?php _e('Order', 'mg_ml') ?></label>
        </th>
      <td>
        <input type="number" name="mg_cat_order" value="<?php echo $order ?>" min="0" step="10" max="1000" style="width: 60px;" /> 
        <p class="description"><?php _e('Category order used in grid filters', 'mg_ml') ?></p>
      </td>
    </tr>
<?php
	endif;
}



// save fields
function save_mg_cat_cust_fields( $term_id ) {
    update_term_meta($term_id, 'mg_cat_icon', $_POST['mg_cat_icon']);    
    update_term_meta($term_id, 'mg_cat_order', (int)$_POST['mg_cat_order']);
}

add_action('created_mg_item_categories', 'save_mg_cat_cust_fields', 10, 2);
add_action('edited_mg_item_categories', 'save_mg_cat_cust_fields', 10, 2);






/////////////////////////////
// manage taxonomy table

// add the table column
function mg_cat_cust_fields_column_headers($columns) {
    $prepend_cols = array();
	$append_cols = array();
	
	$prepend_cols['icon'] = __("Icon", 'mg_ml');
    $append_cols['order'] = __("Order", 'mg_ml');
	
	if(count($prepend_cols) > 0) {
		$columns = array_slice($columns, 0, 1, true) + $prepend_cols + array_slice($columns, 1, count($columns)-1, true);
	}
    return array_merge($columns, $append_cols);
}
add_filter( 'manage_edit-mg_item_categories_columns', 'mg_cat_cust_fields_column_headers', 10, 1);



// fill the custom column row
function mg_cat_cust_fields_column_row($row_content, $column_name, $term_id){
	
	if($column_name == 'icon') {
        $icon = mg_static::retrocomp_get_term_meta($term_id, 'mg_cat_icon', "mg_cat_". $term_id ."_icon", '');
		return '<i class="'. mg_static::fontawesome_v4_retrocomp($icon) .'"></i>';	
	}
	elseif($column_name == 'order') {
		return (int)mg_static::retrocomp_get_term_meta($term_id, 'mg_cat_order', "mg_cat_". $term_id ."_order", 0);
	}
	else {
        return '&nbsp;';
    }
}
add_filter('manage_mg_item_categories_custom_column', 'mg_cat_cust_fields_column_row', 10, 3);




/////////////////////////////////////////////////////////////////////////




///////////////////////////////////////////////////////
// REMOVE THE PARENT FIELD FOR THE CUSTOM TAXONOMY
// ADD ICON MANAGEMENT SYSTEM
function mg_edit_item_cat_js(){
	global $current_screen;
    
	// remove parent field
	if($current_screen->id == 'edit-mg_item_categories') {
		?>
		<script type="text/javascript">
        (function($) { 
            "use strict"; 
            
            jQuery(document).ready(function($) {
                $('#parent').parents('.form-field').remove();
            });
        })(jQuery); 
		</script>
		<?php
    }
	
	
	// icon wizard
	if($current_screen->id == 'edit-mg_item_categories') {
		// ICONS LIST CODE 
		echo mg_static::fa_icon_picker_code( __('no icon', 'mg_ml'), true);
		?>
		
        <script type="text/javascript">
        (function($) { 
            "use strict"; 
        
            <?php mg_static::fa_icon_picker_js(); ?>
        })(jQuery); 
		</script>
        <?php
	}
}
add_action('admin_footer', 'mg_edit_item_cat_js');

