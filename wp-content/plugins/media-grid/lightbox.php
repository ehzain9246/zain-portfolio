<?php
// LIGHTBOX CODES AND WOOCOMMERCE ADD-TO-CART AJAX HANDLER


// ajax lightbox trigger
function mg_ajax_lightbox() {
    header('Content-Type: text/html; charset=utf-8');

    if(!isset($_POST['pid']) || !(int)$_POST['pid']) {
        wp_die('Missing item id');
    }
    $pid = (int)$_POST['pid'];

    $prev = (isset($_POST['prev_id'])) ? (int)$_POST['prev_id'] : false;
    $next = (isset($_POST['next_id'])) ? (int)$_POST['next_id'] : false;
    
    $media_focused = (isset($_POST['mf_mode'])) ? $_POST['mf_mode'] : false; 
    mg_lightbox($pid, $prev, $next, $media_focused);


    // MG-ACTION - allow custom code printing into lightbox - passes item_id
    do_action('mg_lightbox_code', $pid);
    
    wp_die();
}
add_action('wp_ajax_mg_lb_contents', 'mg_ajax_lightbox');
add_action('wp_ajax_nopriv_mg_lb_contents', 'mg_ajax_lightbox');




// woocommerce - add-to-cart ajax handler
function mg_wc_add_to_cart_ajax() {
	if(!isset($_POST['mg_wc_atc'])) {
		return false;	
	}

	$product_id 	= (int)$_POST['mg_wc_atc'];
	$quantity 		= (int)$_POST['atc_quantity'];
	$variation_id 	= ((int)$_POST['atc_var_id']) ? (int)$_POST['atc_var_id'] : false;
	
	if(!$product_id || !$quantity) {
		die( __('Missing parameters', 'mg_ml') );	
	}
	
	
	// dunno why - but keep to allow product quantity increase into the cart
	$cart = WC()->cart;
	$cart_data = $cart->get_cart();
	/////
   
	global $woocommerce;
	$response = $woocommerce->cart->add_to_cart($product_id, $quantity, $variation_id);
	$message = ($response) ? 
		
		'<p class="mg_wc_atc_response mg_wc_atc_success" style="display: none;">
			<span><i class="fa fa-check-circle" aria-hidden="true"></i>'. __('Product added to cart', 'mg_ml') .' &nbsp; - &nbsp; <a href="'. wc_get_cart_url() .'">'. __('View cart', 'mg_ml') .'</a>
		</p>' : 
		
		'<p class="mg_wc_atc_response mg_wc_atc_error" style="display: none;">
			<i class="fa fa-exclamation-circle" aria-hidden="true"></i>'. __('Error adding product to cart', 'mg_ml') .' 
		</p>';
		
	die($message);
}
add_action('wp_loaded', 'mg_wc_add_to_cart_ajax');






// lightbox code
function mg_lightbox($post_id, $prev_item = false, $next_item = false, $media_focused_mode = false) {
	$post_data = get_post($post_id);
	$GLOBALS['post'] = $post_data; 
    
	// check for publish items
	if(
        ($post_data->post_type != 'attachment' && $post_data->post_status != 'publish') ||
        ($post_data->post_type == 'attachment' && $post_data->post_status != 'inherit')
    ) {
		echo 'Item not found';
		return false;
	}
	
	// track real post ID
	$final_post_id = $post_id;
	
	
	// POST TYPE
	// woocommerce
	if($post_data->post_type == 'product') {
		// simulate standard type and add flag	
		$wc_prod = $wc_prod = wc_setup_product_data( get_post($post_id) ); // use this system - otherwise isn't seen as variable
		
		// Woocomm v3 compatibility
		$wc_gallery = (method_exists($wc_prod, 'get_gallery_image_ids')) ? $wc_prod->get_gallery_image_ids() : $wc_prod->get_gallery_attachment_ids(); 
			
		$type = (is_array($wc_gallery) && count($wc_gallery) > 0) ? 'img_gallery' : 'single_img';
		$show_feat = true;
	}
	
	// WP Media library
	elseif($post_data->post_type == 'attachment') {
		$type = 'wp_media';
		$show_feat = true;  
		$wc_prod = false;
	}
    
    // any other post (not mg item)
	elseif($post_data->post_type != 'mg_items') {
		$type = 'post';
		$show_feat = (get_post_meta($post_id, 'mg_hide_feat_img', true)) ? false : true;  
		$wc_prod = false;
	}
	
	// mg items
	else {
		$type = get_post_meta($post_id, 'mg_main_type', true);
		$wc_prod = false;
		
		// post contents type - manage resulting type and true post ID
		if($type == 'post_contents') {
			$post = mg_static::post_contents_get_post($post_id);
			
			if(!$post) {
                die('no posts found');
            }
			else {
				// if WooCommerce product -> recall
				if($post->post_type == 'product') {
					mg_lightbox($post->ID, $prev_item, $next_item); 
					return true;
				}
				else {
					$pc_post_id 	= $post->ID;	
					$final_post_id 	= $pc_post_id;
					$pc_post_data 	= $post;
					$show_feat 		= (get_post_meta($post_id, 'mg_hide_feat_img', true)) ? false : true;  
				}
			}
		}
		else {
            $show_feat = true;
        }
	}


	// layout
	$layout = get_post_meta($post_id, 'mg_layout', true);
	
	if(!$layout || $layout == 'as_default') {
		$layout = get_option('mg_lb_def_layout', 'full');
	} 
	elseif($layout == 'side') { // retrocompatibility
		$layout = 'side_tripartite';
	}
	
    // media-ocused mode
    if($media_focused_mode && $type != 'post_contents') {
        $layout = 'fs_'. get_option('mg_mf_lb_layout', 'right_text');    
    }
    
	
	$item_title = (isset($pc_post_id)) ? $pc_post_data->post_title : $post_data->post_title;
	$featured = '';
	
	// image display mode
	if(in_array($type, array('single_img', 'audio', 'post_contents', 'post', 'wp_media'))) {
		$img_display_mode = (get_post_meta($post_id, 'mg_lb_img_display_mode', true) == 'img_w' || ($media_focused_mode && $type != 'post_contents')) ? 'mg_lb_img_auto_w' : 'mg_lb_img_fill_w'; 
	} else {
		$img_display_mode = '';	
	}
	
	// image max height
	$img_max_h = ($media_focused_mode) ? 0 : (int)get_post_meta($post_id, 'mg_img_maxheight', true);
	
	// contents match height
	$feat_match_txt = ($layout != 'full' && get_post_meta($post_id, 'mg_lb_feat_match_txt', true)) ? 'mg_lb_feat_match_txt' : '';
	
	// canvas color for TT
	$tt_canvas = substr(get_option('mg_item_bg_color', '#ffffff'), 1);
	
	// maxwidth control
	$lb_max_w = (int)get_option('mg_item_maxwidth', 960);
	if($lb_max_w == 0) {$lb_max_w = 960;}

	// Thumb center
	$tt_center = (get_post_meta($post_id, 'mg_thumb_center', true)) ? get_post_meta($post_id, 'mg_thumb_center', true) : 'c'; 
	
	// lightbox max width for the item
	$fc_max_w = (int)get_post_meta($post_id, 'mg_lb_max_w', true);
	
    if(!$fc_max_w || $fc_max_w < 280) {
        $fc_max_w = false;
    } 
    
	$new_lb_max_w = ($fc_max_w) ? $fc_max_w : $lb_max_w;
	if($media_focused_mode && $type != 'post_contents') {
        $new_lb_max_w = 1920;    
    }
    
    
    // targeted media width for media-focused lightbox
    $mf_lb_media_w = ($media_focused_mode) ? (int)get_post_meta($post_id, 'mg_mf_lb_media_w', true) : 0;
    if(!$mf_lb_media_w) {
        $mf_lb_media_w = 50;    
    }
    $mf_lb_media_w_css = ($media_focused_mode) ? 'width:'. $mf_lb_media_w .'vw;' : '';
    
    
	// item featured image for socials
	$fi_img_id = (isset($pc_post_id)) ? get_post_thumbnail_id($pc_post_id) : get_post_thumbnail_id($post_id);
	$fi_src = wp_get_attachment_image_src($fi_img_id, 'medium');
	$fi_src_pt = wp_get_attachment_url($fi_img_id); // pinterest - use full one
	
	
	// image block for single_item + woocommerce + post contents + audio + post
	$feat_img_code = ''; 
    if(in_array($type, array('single_img', 'audio', 'post_contents', 'post', 'wp_media'))) {
		
        // not for audio with external player
        if($type != 'audio' || ($type == 'audio' && !get_post_meta($post_id, 'mg_soundcloud_url', true))) {
        
            $img_id = (isset($pc_post_id)) ? get_post_thumbnail_id($pc_post_id) : get_post_thumbnail_id($post_id);	
            $feat_img_url = ($media_focused_mode) ? $fi_src_pt : mg_lb_static::image_optimizer($img_id, $layout, $new_lb_max_w, $img_display_mode, $img_max_h, $feat_match_txt);

            $GLOBALS['mg_lb_img_params_for_woo_variations'] = ($media_focused_mode) ? array('mf_mode' => true) : array(
                'layout'            => $layout, 
                'lb_max_w'          => $new_lb_max_w, 
                'img_display_mode'  => $img_display_mode, 
                'img_max_h'         => $img_max_h, 
                'feat_match_txt'    => $feat_match_txt    
            );

            // zoomable image?
            $zoomable_img   = ($type != 'audio' && get_post_meta($post_id, 'mg_lb_img_fx', true) == 'zoom') ? true : false;
            $img_zoom_attr  = (in_array($type, array('single_img', 'post_contents', 'post', 'wp_media')) && $zoomable_img) ? 'data-zoom-image="'. $fi_src_pt .'" data-zoom-ratio="100"' : '';
            $zoomable_class = ($img_zoom_attr) ? 'mg_zoomable_img' : '';
            $img_zoom_code  = '';

            if($img_zoom_attr) {
                $full_img_data = wp_get_attachment_image_src($img_id, 'full');

                $img_zoom_code = '
                <span class="mg_lb_zoom_in_btn fas fa-search-plus"></span>
                <span class="mg_lb_zoom_out_btn fas fa-search-minus mg_displaynone"></span>';
            }

            $feat_img_code = mg_static::preloader().
                '<div id="mg_lb_feat_img_wrap" class="'. $zoomable_class .'" '. $img_zoom_attr .'>	
                    '. $img_zoom_code .'
                    <img srcset="'. mg_lb_static::img_srcset_val($img_id, $feat_img_url) .'" alt="'. esc_attr(strip_tags($item_title)) .'" />'.
                '</div>';	
        }
	}
	

    

	///////////////////////////
	// TYPES - SPECIFIC CODES
    
	if(in_array($type, array('single_img', 'post', 'wp_media')) || isset($pc_post_id)) {
		$featured = ($show_feat) ? $feat_img_code : '';
	}
	
	
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	elseif($type == 'img_gallery') {
		$slider_img   = (isset($wc_gallery)) ? $wc_gallery : get_post_meta($post_id, 'mg_slider_img', true);
		$attach_video = (isset($wc_gallery)) ? false : get_post_meta($post_id, 'mg_slider_vid', true);
        $show_counter = get_option('mg_lb_slider_counter');
		$slider_id    = uniqid();

        // specific slider height
        $cust_slider_h = '';
        $h_val = (int)get_post_meta($post_id, 'mg_slider_h_val', true);
		$h_type = get_post_meta($post_id, 'mg_slider_h_val_type', true);
        $min_slider_h = (int)get_post_meta($post_id, 'mg_slider_min_h', true);
        
        // v6 retrocompatibility
        if(!$h_val) {
            $h_val = (int)get_post_meta($post_id, 'mg_slider_w_val', true);
            $h_type = get_post_meta($post_id, 'mg_slider_w_type', true);        
        }
        
        
        if($h_val) {
            $cust_slider_h = 'padding-bottom: '. $h_val . esc_attr($h_type) .';';        
        }
        
		// images display mode
        $slider_img_mode_class = '';
        if(get_post_meta($post_id, 'mg_slider_crop', true) == 'contain') {
            $slider_img_mode_class = 'mg_lb_lcms_contain_mode';
        }

		
		// slider thumbs visibility class
        $slider_thumbs_class = '';
        
        $thumbs_visiblity = get_post_meta($post_id, 'mg_slider_thumbs', true);
        if(!$thumbs_visiblity) {
            $thumbs_visiblity = get_option('mg_def_lb_thumb_nav', 'always');    
        }
        
        $extra_nav = get_option('mg_lb_slider_extra_nav', 'thumbs');
        
        if($extra_nav == 'thumbs' && $thumbs_visiblity != 'never') {
            $slider_thumbs_class = 'mg_lb_lcms_has_thumbs';
            
            switch($thumbs_visiblity) {       
                case 'always' : 
                case 'yes' :
                    $slider_thumbs_class .= ' mg_lb_lcms_thumbs_shown'; 
                    break;
                    
                case 'no' : 
                default :    
                    $slider_thumbs_class .= ' mg_lb_lcms_thumbs_hidden'; 
                    break;
            }
        }

        $nav_dots     = (get_option('mg_lb_slider_extra_nav', 'none') == 'none' || $thumbs_visiblity == 'never') ? 'false' : 'true'; 
        
        $ss_cmd       = (get_post_meta($post_id, 'mg_slider_autoplay', true)) ? get_post_meta($post_id, 'mg_slider_autoplay', true) : get_option('mg_lb_slider_slideshow', 'yes');
        $ss_autoplay  = ($ss_cmd === '1' || $ss_cmd == 'autoplay') ? 1 : 0; 
        $ss_cmd       = (!$ss_cmd || $ss_cmd == 'no') ? 0 : 1;   
        
        
        // additional commands
        $addit_cmd = '';
        if($thumbs_visiblity != ' never' && $thumbs_visiblity != 'always') {
            $addit_cmd .= '<span class="mg_lb_lcms_toggle_thumbs fas fa-ellipsis-h" title="'. esc_attr__('toggle thumbnails', 'mg_ml') .'"></span>';
        }
        if($show_counter) {
            $addit_cmd .= '<span class="mg_lb_lcms_counter">1 / '. count((array)$slider_img) .'</span>';           
        }
        
        
        $featured = '
        <div id="'. $slider_id .'" class="mg_lb_lcms_slider '. $slider_thumbs_class .' '. $slider_img_mode_class .'" style="'. $mf_lb_media_w_css . $cust_slider_h .' min-height: '. $min_slider_h .'px;" data-extra-nav="'. esc_attr($extra_nav) .'" data-ss-cmd="'. $ss_cmd .'" data-autoplay="'. $ss_autoplay .'">
            <ul style="display: none;">';

            if(is_array($slider_img)) {
                if(get_post_meta($post_id, 'mg_slider_random', true)) {
                    shuffle($slider_img);	
                }

                // woocommerce - if prepend first image
                if(isset($wc_gallery) && get_post_meta($post_id, 'mg_slider_add_featured', true)) {
                    array_unshift($slider_img, $fi_img_id);
                }

                // compose slider structure
                $a = 0;
                foreach($slider_img as $img_id) {

                    // WPML/Polylang integration - get translated ID
                    if(function_exists('icl_object_id')) {
                        $img_id = icl_object_id($img_id, 'attachment', true);	
                    }
                    else if(function_exists('pll_get_post')) {
                        $translated_id = pll_get_post($img_id);
                        if($translated_id) {
                            $img_id = $translated_id;	
                        }	
                    }
                    
                    // max width on media-focused lightbox, calculate basing on a standard 1920w 
                    if($media_focused_mode) {
                        $new_lb_max_w = ($mf_lb_media_w / 100) * 1920;    
                    }
                    $img_url = mg_lb_static::image_optimizer($img_id, $layout, $new_lb_max_w);
                    $img_srcest = mg_lb_static::img_srcset_val($img_id, $img_url);

                    // has attached video?
                    if(is_array($attach_video) && isset($attach_video[$a]) && !empty($attach_video[$a])) {
                        if(mg_static::video_embed_url($attach_video[$a]) == 'wrong_url') {
                            
                            if(strpos($attach_video[$a], '.youtube.') !== false) {
                                $featured .= '  
                                <li data-srcset="'. esc_attr($img_srcest) .'" data-type="mixed">'. esc_html__('wrong video URL', 'mg_ml') .'</li>';
                            }
                            else {
                                $featured .= '
                                <li data-srcset="'. esc_attr($img_srcest) .'" data-type="video">
                                    <video controls="controls" preload="auto" poster="'. esc_attr($img_url) .'">
                                        '. mg_static::sh_video_sources($attach_video[$a]) .'
                                    </video> 
                                </li>';       
                            }
                        }
                        else {
                            // use data-srcset for thumbs nav
                            $featured .= '
                            <li data-src="'. $img_url .'" data-srcset="'. $img_srcest .'" data-type="iframe">
                                <div class="mg_lcms_iframe_icon"></div>
                                <iframe class="mg_video_iframe" src="" data-src="'. mg_static::video_embed_url($attach_video[$a], true) .'" frameborder="0" allowfullscreen></iframe>
                            </li>';                
                        }
                    }
                    else {
                        $caption_code = '';
                        if(get_post_meta($post_id, 'mg_slider_captions', true)) {
                            $img_data = get_post($img_id);
                            $caption_code = (is_object($img_data)) ? trim(strip_tags(apply_filters('the_content', $img_data->post_content), 'br')) : '';
                        }

                        $featured .= '<li data-srcset="'. esc_attr($img_srcest) .'" data-type="image">'. $caption_code .'</li>'; 
                    }

                    $a++;
                }
            }

            $featured .= '    
            </ul>
        </div>
        
        <script type="text/javascript">
        (function($) { 
            "use strict";
        
            mg_lb_slider("'. $slider_id  .'", `'. $addit_cmd .'`);
        })(jQuery); 
        </script>';
	}
		
		
    
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////		
	elseif($type == 'video') {
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
		$video_url          = get_post_meta($post_id, 'mg_video_url', true);
        $autoplay_mode      = get_post_meta($post_id, 'mg_autoplay_inl_video', true); 
        $autoplay           = ($autoplay_mode) ? true : false;
        $aspect_ratio = ((float)get_post_meta($post_id, 'mg_lb_video_h', true)) ? (int)get_post_meta($post_id, 'mg_lb_video_h', true) / 100 : 0.55;

		// poster
		if(get_post_meta($post_id, 'mg_video_use_poster', true) == 1) {
			$img_id = get_post_thumbnail_id($post_id);
            
            // max width on media-focused lightbox, calculate basing on a standard 1920w 
            if($media_focused_mode) {
                $new_lb_max_w = ($mf_lb_media_w / 100) * 1920;    
            }
            
			$poster_img = mg_lb_static::image_optimizer($img_id, $layout, $new_lb_max_w);
			$poster = true;
		}
		else {
			$poster_img = '';
			$poster = false;
		}
		
		if(mg_static::video_embed_url($video_url) == 'wrong_url') {
			
			// get video sources
			$sources = mg_static::sh_video_sources($video_url);

			if(!$sources) {
				$featured = '<p><em>Video extension not supported ..</em></p>';	
			}
			else {
				$poster_attr = (!empty($poster_img)) ? 'poster="'.$poster_img.'"' : ''; 
				$preload_poster = (!$poster_attr) ? '' : mg_static::preloader().'<img src="'.$poster_img.'" />';
				
                // autoplay class
                $autoplay_class = '';
                if(!$poster) {
                    switch($autoplay_mode) {

                        case '1' :
                        case 'normal' :
                            $autoplay_class = 'mg_video_autoplay';
                            break;

                        case 'muted' :
                            $autoplay_class = 'mg_video_autoplay mg_muted_autoplay';
                            break;
                    }
                }
                
				$featured = 
				'<div id="mg_lb_video_wrap" class="mg_me_player_wrap mg_self-hosted-video '.$autoplay_class.'">
					<video width="100%" controls="controls" preload="auto" '.$poster_attr.'>
					  '.$sources.'
					</video> 
					'.$preload_poster.'
				</div>';
			}
		} 
		else {
			if($poster) {
				$v_url =  mg_static::video_embed_url($video_url, false);

				$ifp = mg_static::preloader() . '
				<div id="mg_ifp_ol" class="fa fa-play" style="display: none;"></div>
				<div id="mg_lb_video_poster" data-autoplay-url="'. mg_static::video_embed_url($video_url, true, $autoplay_mode) .'" style="background-image: url('. $poster_img .');"></div>
				<img src="'. $poster_img .'" alt="'.esc_attr(strip_tags($item_title)).'" style="display: none;" />
				
				<script type="text/javascript">
				(function($) { 
	               "use strict";
                   
					$(document).on("touchstart", "#mg_ifp_ol, #mg_lb_video_poster", function() {
						$("#mg_lb_video_poster").trigger("click");
					});
				})(jQuery);
				</script>';
			}
			else {
				$v_url = mg_static::video_embed_url($video_url, $autoplay, $autoplay_mode);
				$ifp = '';
			}
			
			$featured = '
			<div id="mg_lb_video_wrap">
				'.$ifp.'
				<iframe class="mg_video_iframe" width="100%" src="'. $v_url .'" frameborder="0" allowfullscreen></iframe>
			</div>
			';
		}
        
        
        // aspect ratio
        $featured .= '<script type="text/javascript">mg_lb_video_h_ratio = '. $aspect_ratio .'</script>';
        
        // media-focused - set video wrapper's width
        if($mf_lb_media_w_css) {
            $featured .= '<style type="text/css">.mg_item_featured {'. $mf_lb_media_w_css .'}</style>';    
        }
	}
	
    
	
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	elseif($type == 'audio') {
        $external_audio_url = get_post_meta($post_id, 'mg_soundcloud_url', true);
        $autoplay = (get_option('mg_audio_autoplay')) ? 'mg_audio_autoplay' : '';
        
		if(!empty($external_audio_url)) {
            $is_spotify     = (strpos($external_audio_url, 'open.spotify.com') !== false) ? true : false;
            $featured       = '';
            
            if(!$is_spotify) {
                $featured .= '<div class="mg_lb_ext_audio_w_img">' . $feat_img_code;      
            }

                // spotify height control
                if($is_spotify) {
                    $h_val     = get_post_meta($post_id, 'mg_lb_spotify_h', true);
                    $h_type    = get_post_meta($post_id, 'mg_lb_spotify_h_type', true);
                    $featured .= '<div class="mg_lb_spotify_wrap" style="'. $mf_lb_media_w_css .'" data-h-val="'. (int)$h_val .'" data-h-type="'. esc_attr($h_type) .'">'; 
                }

                    $featured .= mg_static::audio_embed_iframe($external_audio_url, false, $autoplay);

                if($is_spotify) {
                    $featured .= '</div>';    
                }

            if(!$is_spotify) {
                $featured .= '</div>';        
            }
		}
		else {
			$tracklist = get_post_meta($post_id, 'mg_audio_tracks', true);
			$show_tracklist = (count($tracklist) > 0 && get_option('mg_show_tracklist')) ? 'mg_show_tracklist' : '';

			// player
			$args = array(
				'posts_per_page'	=> -1,
				'orderby'			=> 'post__in',
				'post_type'       	=> 'attachment',
				'post__in'			=> $tracklist
			);
			$tracks = get_posts($args);
			$player_id = uniqid();

			$featured = $feat_img_code .'

			<div class="mg_media_wrap">
				<div id="'. $player_id .'" class="mg_me_player_wrap mg_lb_audio_player '.$show_tracklist.' '.$autoplay.'" style="display: none;">
					<audio controls="controls" preload="auto" width="100%">';
						foreach($tracks as $track) {
							$featured .= '<source src="'. wp_get_attachment_url($track->ID) .'" type="'. $track->post_mime_type .'">';
						}
				$featured .= '
					</audio>';
					
				$featured .= '
				</div>';
				
				// tracklist
				$tot = (is_array($tracklist)) ? count($tracklist) : 0;
				if($tot > 1) {
					$tl_display = ($show_tracklist) ? 'mg_iat_shown' : '';
					
					$featured .= '
					<ol id="'. $player_id .'-tl" class="mg_audio_tracklist '. $tl_display .'">';
					
					$a = 1;
					foreach($tracks as $track) {
						$current = ($a == 1) ? 'mg_current_track' : '';
						$featured .= '
						<li mg_track="'. wp_get_attachment_url($track->ID) .'" data-track-num="'.$a.'" class="'.$current.'">'. $track->post_title .'</li>';
						
						$a++;
					}
					
					$featured .= '
					</ol>';
				}
				
			$featured .= '	
			</div>';
		}
	}

	
    
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	if($type == 'lb_text') {
		
		// custom contents lightbox - set custom padding and force layout to full
		$layout = 'full';
		
		$lbt_padding = get_post_meta($post_id, 'mg_lb_contents_padding', true);
		if(!is_array($lbt_padding) || count($lbt_padding) != 4) {$lbt_padding = array(0, 0, 0, 0);}
		?>
        <style type="text/css">
		div.mg_item_content.mg_lb_txt_fx {
			padding: <?php for($a=0; $a<4; $a++) {echo (int)$lbt_padding[$a].'px ' ;} ?>;	
		}
		</style>
        <?php
	}
	
	
	
	
	///////////////////////////
	// custom CSS to manage image's max height
	if(in_array($type, array('single_img', 'audio', 'post_contents', 'post', 'wp_media')) && isset($img_max_h) && $img_max_h) {
		
		// if want to fill featured space
		if($img_display_mode == 'mg_lb_img_fill_w') {
			echo '
			<style type="text/css">
			.mg_item_featured:not(.mg_lb_feat_matched) #mg_lb_feat_img_wrap {
				height: '.$img_max_h .'px;
				max-height: '.$img_max_h .'px;
                background-image: url('. $feat_img_url .');
            }
            .mg_item_featured:not(.mg_lb_feat_matched) #mg_lb_feat_img_wrap img:not(.mg_lb_zoomed_img) {
				display: none !important;
				min-width: 100% !important;
				min-height: 100% !important;
			}
            </style>';
		}
		else {
		?>
			<style type="text/css">
            #mg_lb_feat_img_wrap {
                text-align: center;	
            }
            #mg_lb_feat_img_wrap > img,
            #mg_lb_feat_img_wrap > a > img {
                display: inline-block;
                width: auto;
                max-height: <?php echo $img_max_h ?>px;
            }
			.mg_lb_feat_matched #mg_lb_feat_img_wrap img { /* avoid interferences between match-feat-h and max-h */
                max-height: none !important;
            }
            </style>
        <?php
		}
	}
	
	
    
    
	///////////////////////////
	// INNER CODE	
 
	/*** lightbox command codes ***/ 
	$cmd_mode = ($media_focused_mode) ? 'top' : get_option('mg_lb_cmd_pos', 'inside');	
    ?>
    <div id="mg_lb_ins_cmd_wrap" <?php if(!in_array($cmd_mode, array('inside', 'above', 'ins_hidden', 'round_hidden'))) {echo 'style="display: none;"';} ?> data-cmd-mode="<?php echo esc_attr($cmd_mode) ?>">
        <div id="mg_inside_close" class="mg_close_lb"></div>

        <div id="mg_lb_inside_nav" class="noSwipe" <?php if(in_array($cmd_mode, array('hidden', 'ins_hidden', 'round_hidden'))) {echo 'style="display: none; visibility: hidden;"';} ?>>
            <?php echo mg_lb_static::nav_code(array('prev' => $prev_item, 'next' => $next_item), 'inside'); ?>
        </div>
    </div>   
    <?php 
    
	if(!in_array($cmd_mode, array('inside', 'above', 'ins_hidden', 'round_hidden'))) {
		if($cmd_mode == 'top') {
			$code = '
			<div id="mg_top_close" class="mg_close_lb" style="display: none;"></div>
			<div id="mg_lb_top_nav" style="display: none;">'. mg_lb_static::nav_code(array('prev' => $prev_item, 'next' => $next_item), $cmd_mode) .'</div>';
		} 
        else {
			$code = '
			<div id="mg_top_close" class="mg_close_lb" style="display: none;"></div>'.
			mg_lb_static::nav_code(array('prev' => $prev_item, 'next' => $next_item), $cmd_mode);	
		}
		
		echo '
		<script type="text/javascript">
        (function($) { 
            "use strict"; 
            
            $("#mg_top_close, #mg_lb_top_nav, .mg_lb_nav_side, .mg_lb_nav_side_basic").remove();

            $("#mg_lb_contents").before("'. str_replace(array("\r", "\n", "\t", "\v"), '', str_replace('"', '\"', $code)) .'");
            $("#mg_lb_top_nav, .mg_side_nav").fadeIn(250);
            $("#mg_top_close").css("display", "flex");
        })(jQuery);
		</script>';	
	}
	?>
    
    
	<?php 
	/*** internal contents ***/ 
	
    $navless_class      = ($prev_item || $next_item) ? '' : 'mg_navless_lb';
    $no_txt_fx_class    = (get_option('mg_lb_no_txt_fx')) ? '' : 'mg_lb_txt_fx';
    $no_feat_class      = ($show_feat) ? '' : 'mg_no_feat_lb'; 
    
	?>
    <div id="mg_lb_<?php echo (int)$post_id ?>" class="mg_lb_layout mg_layout_<?php echo $layout; ?> mg_lb_<?php echo $cmd_mode ?>_cmd mg_lb_<?php echo $type; ?> <?php echo $navless_class ?>" data-item-id="<?php echo (int)$post_id ?>">
      <div>
      
      	<?php if($type != 'lb_text' && $show_feat) : ?>
		<div class="mg_item_featured <?php echo $img_display_mode.' '. $feat_match_txt ?>">
			<?php echo $featured; ?>
		</div>
        <?php endif; ?>
        
		<div class="mg_item_content <?php echo implode(' ', array($navless_class, $no_txt_fx_class, $no_feat_class)) ?>">
			<?php 
			/* custom options - woocommerce attributes */
			if(isset($pc_post_id)) {
                $opts = '';
            }
			else {
                $opts = mg_lb_static::cust_opts_code($post_id, $type, $wc_prod);
            }

			/* title and options wrap */
			if($layout == 'full' && !empty($opts)) {
                echo '<div class="mg_content_left">';
            } 
    
				$title = apply_filters('the_title', $item_title);
				echo '<h1 class="mg_item_title">'. apply_filters('the_title', $item_title) .'</h1>';
            	echo $opts;
    
            if($layout == 'full' && !empty($opts)) {
                echo '</div>';
            }
			
			
			// adding support to Visual Composer shortcodes
			if(class_exists('WPBMap') && method_exists('WPBMap','addAllMappedShortcodes')) {
			   WPBMap::addAllMappedShortcodes();
			}
    
            // adding support to Divi builder
            if(function_exists('et_builder_add_main_elements')) {
                et_builder_add_main_elements();    
            }
			?>
            
            
			<div class="mg_item_text <?php if($layout == 'full' && empty($cust_opt)) {echo 'mg_widetext';} ?>">
				<?php 
				$raw_contents = (isset($pc_post_id)) ? $pc_post_data->post_content : $post_data->post_content;
				
                // MG-FILTER - allow targeted lightbox texts management
                $raw_contents = apply_filters('mg_lightbox_content', $raw_contents, $final_post_id);
    
                $content = ($type != 'wp_media') ? do_shortcode( apply_filters('the_content', $raw_contents)) : do_shortcode(wpautop($raw_contents)); 
    
                echo $content; 

 
				// add-to-cart for woocommerce
				if($wc_prod && !get_option('mg_wc_hide_add_to_cart')) {
					echo mg_lb_static::wc_purchase_btn($post_id, $wc_prod); 
				} 


				// know if lightbox has to show socials
				$has_socials = (get_option('mg_facebook') || get_option('mg_twitter') || get_option('mg_pinterest') || (get_option('mg_googleplus') && (is_array($deeplinked_elems) && in_array('item', $deeplinked_elems)))) ? true : false;
				
				
				// COMMENTS
				$GLOBALS['mg_comments']->get_comments($post_id, $final_post_id, $title, $has_socials);
				?>
            </div>
           
            
            
            <?php 
			// SOCIALS
			if($has_socials) : 
			  	$deeplinked_elems = get_option('mg_deeplinked_elems', array_keys(mg_static::elem_to_deeplink()) );
				$share_curr_url = urlencode(mg_static::curr_url());  
			 
			  	if(isset($pc_post_id)) {
                    $post_id = $pc_post_id;
                }
			?>
            
            <div id="mg_socials" class="mgls_<?php echo get_option('mg_lb_socials_style', 'squared') ?>">
                <ul>
                    <?php 
                    if(get_option('mg_facebook')) : 
    
                        if(get_option('mg_fb_direct_share_app_id') && function_exists('lcsism_share_url')) {
                            $lcsism_share_url = lcsism_share_url(get_the_title($post_id), strip_shortcodes(get_post_field('post_content', $post_id)), $fi_src_pt);
                            $lcsism_fb = 'https://www.facebook.com/dialog/share?app_id='. get_option('mg_fb_direct_share_app_id') .'&href='. urlencode($lcsism_share_url) .'&redirect_uri='.lcsism_redirect_url();

                            $onclick = "window.open('". $lcsism_fb ."&display=popup','sharer','toolbar=0,status=0,width=548,height=500');";	
                        } 
                        else {
                            $onclick = "window.open('https://www.facebook.com/sharer?u=". $share_curr_url ."&display=popup','sharer','toolbar=0,status=0,width=548,height=500');";		
                        }
                    ?>
                    <li id="mg_fb_share">
                        <a onClick="<?php echo $onclick ?>" href="javascript: void(0)">
                            <span title="<?php _e('Share it!', 'mg_ml') ?>" class="<?php echo mg_lb_static::get_social_icon('facebook') ?>"></span>
                        </a>
                    </li>
                    <?php endif; ?>


                    <?php if(get_option('mg_twitter')): ?>
                    <li id="mg_tw_share">
                        <a onClick="window.open('https://twitter.com/share?url=<?php echo $share_curr_url ?>&text=<?php echo urlencode('Check out "'.get_the_title($post_id).'" on '.get_bloginfo('name')); ?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
                            <span title="<?php _e('Tweet it!', 'mg_ml') ?>" class="<?php echo mg_lb_static::get_social_icon('twitter') ?>"></span>
                        </a>
                    </li>
                    <?php endif; ?>


                    <?php if(get_option('mg_pinterest')): ?>
                    <li id="mg_pn_share">
                        <a onClick="window.open('http://pinterest.com/pin/create/button/?url=<?php echo $share_curr_url ?>&media=<?php echo urlencode($fi_src_pt); ?>&description=<?php echo urlencode(get_the_title($post_id)); ?>','sharer','toolbar=0,status=0,width=680,height=470');" href="javascript: void(0)">
                            <span title="<?php _e('Pin it!', 'mg_ml') ?>" class="<?php echo mg_lb_static::get_social_icon('pinterest') ?>"></span>
                        </a>
                    </li>
                    <?php endif; ?>

                    
                    <?php if(get_option('mg_whatsapp_share')) :
                    ?>
                    <li id="mg_wa_share">
                        <a href="whatsapp://send?text=<?php echo urlencode($share_curr_url) ?>" data-action="share/whatsapp/share">
                            <span title="<?php _e('Share it!', 'mg_ml') ?>" class="<?php echo mg_lb_static::get_social_icon('whatsapp') ?>"></span>
                        </a>
                    </li>
                    
                    <?php endif; ?>
                </ul>

              </div>
            <?php endif; ?>
            
			<br style="clear: both;" />
		</div>
      </div>
	</div> 
	<?php
	
	
	// lightbox custom (item-based) max width
	if($fc_max_w) : ?>
    <style type="text/css">
	#mg_lb_contents {
		max-width: <?php echo $fc_max_w ?>px;
	}
	</style>
	<?php endif; 
	
    
    // media-focused lightbox - custom contents padding
	if($media_focused_mode && ($type != 'lb_text' || !$show_feat)) { 
        echo '<style>.mg_item_featured {';
        
        $pos = array('top', 'right', 'bottom', 'left');    
        $custom_padding = get_post_meta($post_id, 'mg_mf_lb_padding', true);
        
        foreach($pos as $key => $p) {
            if(is_array($custom_padding) && isset($custom_padding[$key]) && $custom_padding[$key] !== '') {
                echo 'padding-'. $p .': '. $custom_padding[$key] .'% !important;';     
            }
        }
        echo '}</style>';
    }
    
    
	// if direct opening - trigger lazyload JS function
	?>
	<script type="text/javascript">
    (function($) { 
        "use strict"; 
        
        $(document).ready(function(e) {
            <?php if($type == 'video') : ?>
            if($('#mg_lb_video_wrap.mg_self-hosted-video').length) {
                mg_video_player('#mg_lb_video_wrap');
            }
            <?php endif; ?>

            mg_lb_lazyload();
            mg_pause_inl_players();

            <?php if($type != 'lb_text') : ?>
            mg_lb_realtime_actions();
            <?php endif; ?>
        });
    })(jQuery);
	</script>
	<?php	
}

