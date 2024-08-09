<?php
// global vars and mediaelement inclusion if not in the page - prevent right click


// JS vars always in head
function mg_dynamic_js_vars() {
    $array = array(
        'ajax_url'          => untrailingslashit(site_url()) .'/wp-admin/admin-ajax.php',
        'dike_slug'         => MG_DIKE_SLUG,
        'audio_loop'        => (get_option('mg_audio_loop_by_default')) ? true : false,
        'rtl'               => (get_option('mg_rtl_grid')) ? true : false,
        'mobile_thold'      => (int)get_option('mg_mobile_treshold', 800),
        'deepl_elems'       => (array)get_option('mg_deeplinked_elems', array_keys(mg_static::elem_to_deeplink())),
        'full_deepl'        => (get_option('mg_full_deeplinking')) ? true : false,
        'kenburns_timing'   => (int)get_option('mg_kenburns_timing', 7500),
        'touch_ol_behav'    => get_option('mg_overlay_touch_behav', 'normal'),
        'filters_behav'     => get_option('mg_filters_behav', 'standard'),
        'video_poster_trick'=> MG_URL .'/img/transparent.png',
        'show_filter_match' => (get_option('mg_show_filter_matchings')) ? true : false,
        'search_behav'      => get_option('mg_search_behav', 'any_word'),
        'scrolltop_on_pag'  => (get_option('mg_scrolltop_on_pag')) ? false : true,

        'inl_slider_fx'     => get_option('mg_inl_slider_fx', 'fadeslide'),
        'inl_slider_easing' => mg_static::easing_to_css(get_option('mg_inl_slider_easing', 'ease')),
        'inl_slider_fx_time'=> (int)get_option('mg_inl_slider_fx_time', 400),
        'inl_slider_intval' => (int)get_option('mg_inl_slider_interval', 3000),

        'lightbox_mode'     => (get_option('mg_modal_lb')) ? 'mg_modal_lb' : 'mg_classic_lb',
        'lb_carousel'       => (get_option('mg_lb_carousel')) ? true : false,
        'lb_touchswipe'     => (get_option('mg_lb_touchswipe')) ? true : false,
        'lb_slider_fx'      => get_option('mg_slider_fx', 'fadeslide'),
        'lb_slider_easing'  => get_option('mg_lb_slider_easing', 'ease'),
        'lb_slider_fx_time' => (int)get_option('mg_slider_fx_time', 400),
        'lb_slider_intval'  => (int)get_option('mg_slider_interval', 3000),
        'lb_slider_counter' => get_option('mg_lb_slider_counter') ? true : false,
        'add_to_cart_str'   => esc_attr__('Add to cart', 'mg_ml'),
        'out_of_stock_str'  => esc_attr__('Out of stock', 'mg_ml')
    );
    $array = (array)apply_filters('mg_dynamic_js_vars', $array);
    
    wp_localize_script('mg-frontend', 'lcmg', $array);
}
add_action('wp_enqueue_scripts', 'mg_dynamic_js_vars', 999);
add_action('lc_guten_scripts', 'mg_dynamic_js_vars', 999);    
        





/* add quick edit link for WP users */
function mg_quick_edit_link() {
	if(!current_user_can('edit_pages')) {
        return false;
    }
	?>
	
	<script type="text/javascript">
    (function($) { 
        "use strict";
        
        let mg_remove_qeb;
        
		$(document).on('mouseenter', '.mg_box:not(.mg_spacer)', function() {
			var iid = $(this).data('item-id');
			if($('#mg_quick_edit_btn.mgqeb_'+ iid).length) {
                return false;
            }
			
			if(typeof(mg_remove_qeb) != 'undefined') {
                clearTimeout(mg_remove_qeb);
            }
			if($('#mg_quick_edit_btn').length) {
                $('#mg_quick_edit_btn').remove();
            }
			
			var item_pos = $(this).offset();
			var item_padding = parseInt( $(this).css('padding-top'));
			var css_pos = 'style="top: '+ (item_pos.top + item_padding) +'px; left: '+ (item_pos.left + item_padding) +'px;"';
			
			var link = "<?php echo admin_url() ?>post.php?post="+ iid +"&action=edit";

			$('body').append('<a id="mg_quick_edit_btn" class="ggqeb_'+ iid +' fas fa-pencil-alt" href="'+ link +'" target="_blank" title="<?php esc_attr_e('edit', 'mg_ml') ?>" '+ css_pos +'></a>');		
		})
        .on('mouseleave', '.mg_box:not(.mg_spacer)', function() {
			if(typeof(mg_remove_qeb) != 'undefined') {
                clearTimeout(mg_remove_qeb);
            }
			mg_remove_qeb = setTimeout(function() {
				if($('#mg_quick_edit_btn').length) {
                    $('#mg_quick_edit_btn').remove();
                }
			}, 700);
		});
        
	})(jQuery);
	</script>
    <?php	
}
add_action('wp_footer', 'mg_quick_edit_link', 9999);






/* custom item icons */
function mg_items_cust_icon_css() {
    if(!isset($GLOBALS['mg_items_cust_icon']) || !is_array($GLOBALS['mg_items_cust_icon']) || !count($GLOBALS['mg_items_cust_icon'])) {
		return false;	
	}
	include_once(MG_DIR .'/classes/lc_fontAwesome_helper.php');

	echo '<style type="text/css">';
    
	foreach($GLOBALS['mg_items_cust_icon'] as $item_id => $icon_id) {
        
        $to_search = str_replace(array('fa-', 'fas-', 'far-', 'fab-', 'fa ', 'fas ', 'far ', 'fab '), '', $icon_id);
        $results = lc_fontawesome_helper::search(trim($to_search));
        
        if(empty($results)) {
            continue;    
        }
        
        foreach($results as $result_id => $result) {
            if($result_id != $to_search) {
                continue;    
            }
            
            $unicode = $result->unicode;
            $weight = (strpos($icon_id, 'fa-') !== false || strpos($icon_id, 'fas-') !== false) ? 900 : 400;

            echo '
            .mgi_'.$item_id.' .mgi_item_type_ol span:before {
                content: "\\'. $unicode .'" !important;
                font-weight: '. $weight .' !important;
            }';
            
            break;
        }
	}
	
	echo '</style>';
}
add_action('wp_footer', 'mg_items_cust_icon_css', 1);
