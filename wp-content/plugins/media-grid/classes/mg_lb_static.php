<?php
// LIGHTBOX GENERIC STATIC METHODS
class mg_lb_static {
    
    
    // return lightbox custom options / attributes code
    public static function cust_opts_code($post_id, $type, $wc_prod = false) {
        $code = '';
        
        if($type == 'single_img') {
            $type = 'image';
        }
        elseif($type == 'wp_media') {
            $type = 'post';
        }

        if(!$wc_prod) {
            $type_opts = get_option('mg_'.$type.'_opt');
            $cust_opt = mg_static::item_copts_array($type, $post_id); 
            $icons = get_option('mg_'.$type.'_opt_icon');

            if(count($cust_opt) > 0) {
                $code .= '<ul class="mg_cust_options">';

                $a=0;
                foreach($type_opts as $opt) {
                    if(isset($cust_opt[$opt])) {				
                        $icon = (isset($icons[$a]) && !empty($icons[$a])) ? '<i class="mg_cust_opt_icon '. mg_static::fontawesome_v4_retrocomp($icons[$a]) .'"></i> ' : '';
                        $code .= '<li>'.$icon.'<span>'. mg_static::wpml_string($type, $opt) .'</span> '. do_shortcode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $cust_opt[$opt])) .'</li>';
                    }
                    $a++;
                }

                $code .= '</ul>';
            }
        }

        // woocomm attributes
        else {
            $prod_attr = mg_static::wc_prod_attr($wc_prod);
            if(is_array($prod_attr) && count($prod_attr) > 0 && !get_option('mg_wc_hide_attr')) {
                $code .= '<ul class="mg_cust_options">';

                foreach($prod_attr as $attr => $val) {					
                    $icon = get_option('mg_wc_attr_'.sanitize_title($attr).'_icon');
                    $icon_code = (!empty($icon)) ? '<i class="mg_cust_opt_icon '. mg_static::fontawesome_v4_retrocomp($icon) .'"></i> ' : '';

                    $code .= '<li>'. $icon_code .'<span>'.$attr.'</span> '. do_shortcode(implode(', ', $val)) .'</li>';
                }

                // add rating if allowed and there's any
                if(get_post_field('comment_status', $post_id) != 'closed' && $wc_prod->get_rating_count() > 0) {
                    $rating = round((float)$wc_prod->get_average_rating());
                    $empty_stars = 5 - $rating;

                    $code .= '<li class="mg_wc_rating"><span>'. __('Rating', 'mg_ml') .'</span>';
                    
                    for($a=0; $a < $rating; $a++) 		{
                        $code .= '<i class="fas fa-star"></i>';
                    }
                    for($a=0; $a < $empty_stars; $a++) 	{
                        $code .= '<i class="far fa-star"></i>';
                    }
                    $code .= '</li>';
                }

                $code .= '</ul>';
            }
        }

        return $code;
    }
    
    
    
    // get woocommerce purchase button for lightbox
    public static function wc_purchase_btn($prod_id, $prod_obj) {
        global $mg_lb_img_params_for_woo_variations;
        $iv = $mg_lb_img_params_for_woo_variations;
        
        $code = '';
        $p = $prod_obj;

        // variable product
        if($p->is_type('variable')) {

            $code = '
            <select name="mg_wc_atc_variations_dd" autocomplete="off">';

            foreach($p->get_available_variations() as $v) {
                if(!$v['variation_is_active'] || !$v['variation_is_visible']) {
                    continue;	
                }

                $v_id = $v['variation_id'];

                // variation name 
                $variation = wc_get_product($v_id);
                $v_name = $variation->get_formatted_name();

                $v_name = str_replace('(#'. $v_id .')', '', $v_name);
                $v_name_arr = explode(' - ', $v_name);
                array_shift($v_name_arr);
                $v_name = implode(' - ', $v_name_arr);



                // available?
                $avail = ($v['is_purchasable'] && $v['is_in_stock']) ? 1 : 0;

                // min / max
                $min_purch = (int)$v['min_qty'];
                $max_purch = $v['max_qty'];
                if((int)$max_purch < 1) {$max_purch = '';} 

                // price 
                $price = str_replace('"', "'", $v['price_html']);

                // description
                $descr = (isset($v['variation_description'])) ? str_replace('"', "'", $v['variation_description']) : '';

                // image
                $img_url = (isset($iv['mf_mode']) && is_array($iv)) ? 
                    wp_get_attachment_url($v["image_id"]) : 
                    self::image_optimizer($v["image_id"], $iv['layout'], $iv['lb_max_w'], $iv['img_display_mode'], $iv['img_max_h'], $iv['feat_match_txt']);
                

                $code .= 
                '<option 
                    value="'. esc_attr($v_id) .'" 
                    data-avail="'. esc_attr($avail) .'" 
                    data-min="'. (int)$min_purch .'" 
                    data-max="'. (int)$max_purch .'" 
                    data-price="'. esc_attr($price) .'" 
                    data-img="'. esc_attr($img_url ) .'"
                    data-descr="'. esc_attr($descr) .'">'. $v_name .
                '</option>';	
            }

            $code .= '
            </select>
            <div class="mg_wc_atc_wrap"></div>';

            // show contents for first option
            $code .= '
            <script type="text/javascript">
            (function($) { 
                "use strict"; 
                
                $("[name=mg_wc_atc_variations_dd]").trigger("change");
            })(jQuery); 
            </script>';
        }


        // normal ones
        else {
            $code = '
            <div class="mg_wc_atc_wrap">';

                if($p->is_purchasable() && $p->is_in_stock()) {
                    $min_purch = (int)$p->get_min_purchase_quantity();
                    $max_purch = $p->get_max_purchase_quantity();
                    if($max_purch < 1) {
                        $max_purch = '';
                    } 

                    $quantity_f = (($max_purch && $max_purch <= 1) || $p->is_sold_individually()) ? 
                        '' : 
                        '<br/><input name="mg_wc_atc_quantity" type="number" min="'. $min_purch .'" max="'. $max_purch .'" step="1" value="'. $min_purch .'" autocomplete="off" />';

                    $code .= $prod_obj->get_price_html() . $quantity_f .
                            '<a href="javascript:void(0)" class="mg_wc_atc_btn"><i class="fas fa-shopping-cart" aria-hidden="true"></i> '. __('Add to cart', 'mg_ml') .'</a>';	
                } 
                elseif($p->get_type() == 'external') {
                    $prod_data = $p->get_data();
                    $btn_txt = (empty($prod_data["button_text"])) ? __('Buy now', 'mg_ml') : $prod_data["button_text"]; 
                    
                    $code .= $prod_obj->get_price_html() .
                            '<a href="'. esc_attr($p->get_product_url()) .'" target="_blank" class="mg_wc_atc_btn mg_wc_external_link_btn"><i class="fas fa-shopping-cart" aria-hidden="true"></i> '. $btn_txt .'</a>';    
                }
                else {
                    $code .= $prod_obj->get_price_html() .'<a href="javascript:void(0)" class="mg_wc_atc_btn mg_wc_atc_btn_disabled"><i class="fas fa-ban" aria-hidden="true"></i> '. __('Out of stock', 'mg_ml') .'</a>';	
                }

            $code .= '</div>';		
        }


        return
        '<form id="mg_woo_cart_btn_wrap" data-product="'. $prod_id .'"> 
            '. $code .
        '</form>';
    }
    
    
    
    // lightbox image optimizer - serve best wordpress-managed image depending on featured space sizes
    public static function image_optimizer($img_id, $layout, $lb_max_w, $img_display_mode = 'mg_lb_img_fill_w', $img_max_h = false, $feat_match_txt = false) {

        // calculate image's max width
        if(strpos($layout, 'tripartite')) {
            $img_max_w = ceil($lb_max_w * 0.65);	
        }
        elseif(strpos($layout, 'bipartite') !== false) {
            $img_max_w = ceil($lb_max_w / 2);		
        }
        else {
            $img_max_w = $lb_max_w;		
        }


        // max-height and not fill nor match -> use LC resizing system 
        if($img_max_h && $img_display_mode == 'mg_lb_img_auto_w' && !$feat_match_txt) {
            $canvas_color = substr(get_option('mg_item_bg_color', '#ffffff'), 1);
            return	mg_static::thumb_src($img_id, $img_max_w, $img_max_h, $quality = 95, $thumb_center = 'c', $resize = 3, $canvas_color);
        }

        else {
            //$src = wp_get_attachment_image_src($img_id, array($lb_max_w, $lb_max_w));	
            $src = wp_get_attachment_image_src($img_id, mg_static::thumb_sizes_to_wp_thumb_name($lb_max_w, false));	
            return (is_array($src)) ? $src[0] : 'image not found';	
        }
    }
    
    
    
    // image srcset value comoposer (handling image ID and the full-res image)
    public static function img_srcset_val($img_id, $fullres_url) {
        $sizes = array($fullres_url .' 1600w');
        $intermediate = array('large', 'medium');
        
        foreach($intermediate as $sizename) {
            $img_url = wp_get_attachment_image_url($img_id, $sizename);
            $w = ($sizename == 'large') ? 1024 : 350;
            
            if($img_url) {
                $sizes[] = $img_url .' '. $w .'w';    
            }
        }
        
        // MG-FILTER - allows srcset images filter
        $sizes = apply_filters('mg_img_srcset', $sizes, $img_id, $fullres_url);
        return esc_attr(implode(', ', $sizes));
    }

    

    // lightbox navigation code
    public static function nav_code($prev_next = array('prev' => 0, 'next' => 0), $layout = 'inside') {
        if((!$prev_next['prev'] && !$prev_next['next']) || $layout == 'hidden') {
            return '';
        }

        // thumb sizes for layout
        switch($layout) {
            case 'inside' 	: 
                $ts = array('w'=>60, 'h'=>60); 
                break;	
                
            case 'top' 		: 
                $ts = array('w'=>150, 'h'=>150); 
                break;
                
            case 'side' 	: 
                $ts = array('w'=>340, 'h'=>120); 
                break;
        }

        $code = '';
        foreach($prev_next as $dir => $item_id) {
            $active         = (!empty($item_id)) ? 'mg_nav_active' : '';
            $side_class     = ($layout == 'side') ? 'mg_side_nav' : '';
            $side_vis       = ($layout == 'side') ? 'mg_displaynone' : '';
            $thumb_center   = (get_post_meta($item_id, 'mg_thumb_center', true)) ? get_post_meta($item_id, 'mg_thumb_center', true) : 'c';
            $icon_class     = ($dir == 'next') ? 'fa-chevron-right' : 'fa-chevron-left';
            
            $code .= '
            <div class="mg_lb_nav_'.$layout.' mg_nav_'.$dir.' mg_'.$layout.'_nav_'.$dir.' '.$active.' '.$side_class.' '. $side_vis .'" data-item-id="'.$item_id.'">
                <i class="fas '. $icon_class .'"></i>';

                if($layout == 'side') {
                    $code .= '<span></span>';	
                }

                if(!empty($item_id)) {
                    $title = get_the_title($item_id);

                    if($layout == 'inside') {
                        $code .= '<div><span>'.$title.'</span></div>';
                    }
                    elseif($layout == 'top') {
                        $thumb = mg_static::thumb_src(get_post_thumbnail_id($item_id), $ts['w'], $ts['h'], 80, $thumb_center);
                        $code .= '<div>'.$title.'<img src="'.$thumb.'" alt="'.esc_attr($title).'" /></div>';
                    }
                    elseif($layout == 'side') {
                        $thumb = mg_static::thumb_src(get_post_thumbnail_id($item_id), $ts['w'], $ts['h'], 70, $thumb_center);
                        $code .= '<div>'.$title.'</div><img src="'.$thumb.'" alt="'.esc_attr($title).'" />';
                    }
                }

            $code .= '</div>';
        }	
        return $code;
    }
    
    
    
    /* given opt name and style (minimal, rounded, squared) returns related social icon class */
    public static function get_social_icon($icon, $style = 'inherit') {
        if($style == 'inherit') {
            $style = get_option('mg_lb_socials_style', 'squared');    
        }
        
        $class = '';
        switch($style) {
                
            case 'minimal' :
                if($icon == 'facebook') {
                    $class = 'fab fa-facebook-f';  
                }
                elseif($icon == 'twitter') {
                    $class = 'fab fa-twitter';      
                }
                elseif($icon == 'pinterest') {
                    $class = 'fab fa-pinterest-p';      
                }
                elseif($icon == 'whatsapp') {
                    $class = 'fab fa-whatsapp';      
                }
                break;
                
            case 'rounded' :
                if($icon == 'facebook') {
                    $class = 'fab fa-facebook';  
                }
                elseif($icon == 'twitter') {
                    $class = 'mg_round_social_trick fab fa-twitter-square';      
                }
                elseif($icon == 'pinterest') {
                    $class = 'fab fa-pinterest';      
                }
                elseif($icon == 'whatsapp') {
                    $class = 'mg_round_social_trick fab fa-whatsapp-square';      
                }
                break;
                
            case 'squared' :
            default        :    
                if($icon == 'facebook') {
                    $class = 'fab fa-facebook-square';  
                }
                elseif($icon == 'twitter') {
                    $class = 'fab fa-twitter-square';      
                }
                elseif($icon == 'pinterest') {
                    $class = 'fab fa-pinterest-square';      
                }
                elseif($icon == 'whatsapp') {
                    $class = 'fab fa-whatsapp-square';      
                }
                break;     
        }
        
        return $class .' mg-'.$icon.'-icon';
    }
}
