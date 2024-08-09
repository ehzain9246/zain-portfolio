<?php
// GENERIC PLUGIN STATIC METHODS
class mg_static {
    
    
    // get the current URL
    public static function curr_url() {
        $pageURL = 'http';

        if ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || (function_exists('is_ssl') && is_ssl())) {$pageURL .= "s";}
        $pageURL .= "://" . $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];

        return $pageURL;
    }
    
    
    
    // get file extension from a filename
    public static function string_to_ext($string) {
        $pos = strrpos($string, '.');
        $ext = strtolower(substr($string,$pos));
        return $ext;	
    }
    
    
    
    // string to url format (with small adjustments)
    public static function custom_urlencode($string){
        return strtolower(trim(urlencode($string)));
    }
    
    
    
    // hex color to RGBA
    public static function hex2rgba($hex, $alpha) {
        // if is RGB or transparent - return it
        $pattern = '/^#[a-f0-9]{6}$/i';
        if(empty($hex) || $hex == 'transparent' || !preg_match($pattern, $hex)) {
            return $hex;
        }

        $hex = str_replace("#", "", $hex);
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);
        $rgb = 'rgb('. implode(",", $rgb) .')';

        $rgba = str_replace(array('rgb', ')'), array('rgba', ', '.$alpha.')'), $rgb);
        return $rgba;	
    }
    
    
    
    // retrieve term meta value considering the old WP option storing system - automatically moves data to the new storing system
    public static function retrocomp_get_term_meta($term_id, $meta_key, $old_key, $default_val = false) {
        $val = get_term_meta($term_id, $meta_key, true);
       
        if($val === false || $val === '') {
            $val = get_option($old_key, $default_val);
            delete_option($old_key);
            update_term_meta($term_id, $meta_key, $val); 
        }
        
        return $val;
    }
    
    
    
    // create youtube and vimeo embed url
    public static function video_embed_url($raw_url, $manual_autoplay = '', $autoplay_mode = 'normal') {
        if(strpos($raw_url, 'vimeo') !== false) {
            $code = substr($raw_url, (strrpos($raw_url, '/') + 1));
            $url = '//player.vimeo.com/video/'.$code.'?title=0&amp;byline=0&amp;portrait=0';
        }
        
        elseif(strpos($raw_url, 'youtu.be') !== false) {
            $code = substr($raw_url, (strrpos($raw_url, '/') + 1));
            $url = '//www.youtube.com/embed/'.$code.'?rel=0';	
        }
        elseif(strpos($raw_url, 'youtube.com') !== false) {
            $parts = parse_url($raw_url);
            parse_str($parts['query'], $query);
            
            $url = '//www.youtube.com/embed/'.$query['v'].'?rel=0';	
        }
        
        elseif(strpos($raw_url, 'dailymotion.com') !== false || strpos($raw_url, 'dai.ly') !== false) {
            if(substr($raw_url, -1) == '/') {
                $raw_url = substr($raw_url, 0, -1);
            }
            
            $parts = explode('/', $raw_url);
            $arr = explode('_', end($parts));
            $url = '//www.dailymotion.com/embed/video/'.$arr[0];	
        }
        else {
            return 'wrong_url';
        }

        // autoplay
        if( (get_option('mg_video_autoplay') && $manual_autoplay !== false) || $manual_autoplay === true ) {
            $url .= (strpos($raw_url, 'dailymotion.com') !== false) ? '?autoPlay=1' : '&amp;autoplay=1';

            // muted autoplay?
            if($autoplay_mode == 'muted') {
                if(strpos($raw_url, 'dailymotion.com') !== false) {
                    // ... no option     
                }
                elseif(strpos($raw_url, 'vimeo.com') !== false) {
                    $url .= '&amp;muted=1'; 
                }
                else { // youtube
                    $url .= '&amp;mute=1'; 
                } 
            }
        }

        return $url;
    }
    
    
    
    // given video URL - return self-hosted video sources for HTML player
    public static function sh_video_sources($video_url) {
        $ok_src = array();
        $allowed = array('mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv');
        $sources = explode(',', $video_url); 

        foreach($sources as $v_src) {
            $ext = substr(trim(self::string_to_ext($v_src)), 1);
            if(in_array($ext, $allowed)) {
                $ok_src[$ext] = trim($v_src);	
            }
        }

        $man_src = array();
        foreach($ok_src as $v_type => $url) {
            $man_src[] = '<source src="'. esc_attr($url) .'" type="video/'. esc_attr($v_type) .'">';
        }

        return (count($ok_src)) ? implode('', $man_src) : false;	
    }
    
    
    
    // create youtube and vimeo embed code
    public static function audio_embed_iframe($raw_url, $inline_item = false, $autoplay = false) {
        $height = '100%';
        
        if(strpos($raw_url, 'soundcloud.com') !== false) {
            $src_classname = 'soundcloud';
            if(!$inline_item) {
                $height = 120;    
            }
            
            $url = self::get_soundcloud_embed_url($raw_url, $inline_item, $autoplay);      
        }
        
        else if(strpos($raw_url, 'open.spotify.com') !== false) { 
            $src_classname = 'spotify';
            if(!$inline_item) {
                $height = (strpos($raw_url, '/track/') !== false) ? 80 : 500;   
            }
            
            $arr = explode('?', $raw_url);
            $url = str_replace('.com/', '.com/embed/', $arr[0]);
        }
        
        else if(strpos($raw_url, 'mixcloud.com') !== false) { 
            $src_classname = 'mixcloud';
            $height = 120;
            
            $arr = explode('?', $raw_url);
            $arr = explode('mixcloud.com', $arr[0]);
            
            $mc_autoplay = ($autoplay) ? 1 : 0; 
            $url = 'https://www.mixcloud.com/widget/iframe/?hide_cover=1&hide_artwork=1&autoplay='. $mc_autoplay .'&feed='. urlencode($arr[1]);
            
            if(get_option('mg_players_theme') == 'light') {
                $url .= '&light=1';   
            }
        }
        
        else {
            return 'wrong_url';
        }
            
        $lazyload_code = ($inline_item && $autoplay) ? 'src="" data-lazy-src="' : 'src="'; 
        $z_index = ($inline_item && $autoplay) ? 'z-index: -1;' : '';    
        
        $code = '<iframe '. $lazyload_code . $url .'" width="100%" height="'. $height .'" frameborder="0" allowtransparency="true" class="mg_audio_embed mg_'. $src_classname .'_embed" style="'. $z_index .'"></iframe>';
        
        return $code;
    }
    
    
    
    // get soundcloud embed code (needs remote data retrieval)
    public static function get_soundcloud_embed_url($track_url, $inline_item, $autoplay = false) {

        // search for already queried tracks
        $cached = unserialize(get_option('mg_cached_soundcloud', '') );
        if(!is_array($cached)) {
            $cached = array();
        }

        // get track ID
        if(isset($cached[ $track_url ])) {
            $track_id = $cached[ $track_url ];	
        }
        else {
            // not cached - use cURL
            $pub = '69c06a70f88e8ec80a414ae55dab369c'; // soundcloud public key
            $url = 'https://api.soundcloud.com/resolve.json?url='. urlencode($track_url) .'&client_id='.$pub;

            $data = wp_remote_get($url, array(
                'timeout' 		=> 5, 
                'redirection' 	=> 3,
            ));

            // nothing got - use cURL 
            if(is_wp_error($data) || wp_remote_retrieve_response_code($data) != 200 || empty($data['body'])) {
                return 'Soundcloud: retrieval error';
            }
                
            $data = (array)json_decode($data['body']);
           
            // no track found
            if(!isset($data['uri'])) {
                return 'Soundcloud: track not found';	
            }

            $uri_arr = explode('/', untrailingslashit($data['uri']));
            $track_id = end($uri_arr);
            
            // cache
            $cached[$track_url] = $track_id;
            update_option('mg_cached_soundcloud', serialize($cached));
        }

        $autoplay = ($autoplay) ? 'true' : 'false';
        $inline_visual = ($inline_item) ? '&visual=true' : '';

        return 'https://w.soundcloud.com/player/?url='. urlencode('https://api.soundcloud.com/tracks/'. $track_id) .'&color=ff5500&auto_play='. $autoplay .'&hide_related=true&show_artwork=true'.$inline_visual;
    }
    
    
    
    // get translated option name - WPML / Polylang integration
    public static function wpml_string($type, $original_val) {
        if(function_exists('icl_t')) {
            $typename = ($type == 'img_gallery') ? 'Image Gallery' : ucfirst($type);
            $index = $typename.' Attributes - '.$original_val;

            return icl_t('Media Grid - Item Attributes', $index, $original_val);
        }
        elseif(function_exists('pll__')) {
            return pll__($original_val);
        }
        else{
            return $original_val;
        }
    }
    

    
    // preloader code
    public static function preloader($is_grid_loader = false) {
        $no_init_loader_class = ($is_grid_loader && get_option('mg_no_init_loader')) ? 'mg_no_init_loader' : '';

        return '
        <div class="mg_loader '. $no_init_loader_class .'">
            <div class="mgl_1"></div><div class="mgl_2"></div><div class="mgl_3"></div><div class="mgl_4"></div>
        </div>';	
    }
    
    
    
    // image ID to path
    public static function img_id_to_path($img_src) {
        if(is_numeric($img_src)) {
            $wp_img_data = wp_get_attachment_metadata((int)$img_src);
            
            if($wp_img_data && isset($wp_img_data['file'])) {
                $upload_dirs = wp_upload_dir();
                $img_src = $upload_dirs['basedir'] . '/' . $wp_img_data['file'];
            }
        }

        return $img_src;
    }
    
    
    
    // given the image ID, knows if it is a webP
    public static function img_is_webp($img_src) {
        $path = self::img_id_to_path($img_src);
        $arr = explode('.', $path);
        
        return (strtolower(end($arr)) == 'webp') ? true : false;
    }
    


    // thumbnail source switch between timthumb and ewpt
    public static function thumb_src($img_id, $width = false, $height = false, $quality = 80, $alignment = 'c', $resize = 1, $canvas_col = 'FFFFFF', $fx = array()) {
        if(!$img_id) {
            return '';
        }

        $engine = (self::img_is_webp($img_id) && get_option('mg_thumbs_engine') == 'timthumb') ? 'wp_thumbs' : get_option('mg_thumbs_engine', 'ewpt');
        
        switch($engine) {

            case 'ewpt' :
            default 	:
                $thumb_url = easy_wp_thumb($img_id, $width, $height, $quality, $alignment, $resize, $canvas_col , $fx);
                break;


            case 'timthumb' :
                $thumb_url = MG_TT_URL.'?src='. self::img_id_to_path($img_id) .'&w='.$width.'&h='.$height.'&a='.$alignment.'&q='.$quality.'&zc='.$resize.'&cc='.$canvas_col;
                break;


            case 'wp_thumbs' :
                $src = wp_get_attachment_image_src($img_id, self::thumb_sizes_to_wp_thumb_name($width, $height) );	
                $thumb_url = $src[0];	
                break;
        }

        return $thumb_url;
    }
    
    
    
    
    // given target image's width and height, returns the most appropriate wordpress thumbnail size key
    public static function thumb_sizes_to_wp_thumb_name($width, $height) {
        global $_wp_additional_image_sizes;

        // check into cache
        if(isset($GLOBALS['mg_wp_thumb_sizes'])) {
            $sizes = $GLOBALS['mg_wp_thumb_sizes'];	
        }
        else {
            $sizes = array();

            // WP sizes (only ines avoiding crop)
            $wp_sizes = array('medium', 'medium_large', 'large');
            foreach($wp_sizes as $s) {
                $sizes[ $s ][ 'width' ] = intval( get_option( "{$s}_size_w" ) );
                $sizes[ $s ][ 'height' ] = intval( get_option( "{$s}_size_h" ) );	
            }

            // append other ones
            if(is_array($_wp_additional_image_sizes)) {
                foreach($_wp_additional_image_sizes as $name => $data) {
                    if(!$data['crop']) {
                        $sizes[ $name ]	= $data;
                    }	
                }	
            }

            $GLOBALS['mg_wp_thumb_sizes'] = $sizes;
        }


        // check and return
        $to_return = '';
        foreach($sizes as $name => $data) {

            if( (int)$width <= (int)$data['width'] && (!$height || (int)$height <= (int)$data['height']) ) {
                $to_return = $name;
                break;	
            }
        }

        return ($to_return) ? $to_return : 'original';
    }
    
    
    
    // image ID to full-size URL 
    public static function img_id_to_fullsize_url($img_id) {
        if(!isset($GLOBALS['mg_img_id_fullurl'])) {
            $GLOBALS['mg_img_id_fullurl'] = array();
        }

        if(isset($GLOBALS['mg_img_id_fullurl'][$img_id])) {
            return $GLOBALS['mg_img_id_fullurl'][$img_id];
        } 
        else {
            $src = wp_get_attachment_url($img_id);
            $GLOBALS['mg_img_id_fullurl'][$img_id] = $src;	

            return $src;
        }
    }
    
    
    
    // know if image is a gif
    public static function img_is_gif($img_id) {
        $img_url = self::img_id_to_fullsize_url($img_id);
        
        return (!empty($img_url) && substr(strtolower($img_url), -4) == '.gif') ? true : false;
    }
    
    
    
    // FontAwesome v4 class retrocompatibility
    public static function fontawesome_v4_retrocomp($class) {
        if(!empty($class) && strpos($class, ' ') === false) {
            $class = 'fas '. $class;    
        }
        
        return esc_attr($class);
    }
    
    
    
    // font-awesome icon picker - hidden lightbox code
    public static function fa_icon_picker_code($no_icon_text, $form_wrap = false) {
        include_once(MG_DIR .'/classes/lc_fontAwesome_helper.php');

        try{
            return '
            <div id="mg_icons_list" class="mg_displaynone">
                '. lc_fontawesome_helper::html_list(array(
                    'extra_class'   => 'mg_lb_icon_picker',
                    'form_wrap'     => $form_wrap,

                    'labels'        => array(
                        '🔍 '. esc_html__('Search icons ..', 'mg_ml'), 
                        esc_html__('All categories', 'mg_ml'), 
                        esc_html__('Solid', 'mg_ml'), 
                        esc_html__('Regular', 'mg_ml'), 
                        esc_html__('Brands', 'mg_ml'),
                        esc_html__('no icon', 'mg_ml'),
                        esc_html__('.. no icons found ..', 'mg_ml'),
                    )
                )) .'
            </div>';
        }
        catch(Exception $e) {
            var_dump($e);    
        }
    }


    
    // font-awesome icon picker - javascript code - direct print
    public static function fa_icon_picker_js() {
        ?>
        let $sel_type_opt = false;
            
        // launch lightbox
        $(document).on('click', '.mg_icon_trigger i', function() {
            $sel_type_opt = $(this);

            let sel_val = $sel_type_opt.attr('class').trim();
            if(sel_val) {
                sel_val = '.'+ sel_val.replace(' ', '.');    
            }


            tb_show("<?php esc_html_e('Icons picker', 'mg_ml') ?>", '#TB_inline?inlineId=mg_icons_list');
            setTimeout(function() {
                $('#TB_window').addClass('mg_icon_picker_lb')
                $('input[name="lcfah-search"]').val('');

                // reset search
                $('select[name="lcfah-style"] option').removeAttr('selected');
                $('select[name="lcfah-style"]').each(function() {
                    const event = new Event('change');
                    this.dispatchEvent(event);
                });


                // show selected value
                const $sel_obj = (sel_val) ? $('.mg_icon_picker_lb '+sel_val).parent() : $('.lcfah-no-icon'); 

                $('.mg_icon_picker_lb .mg_lb_icon_selected').removeClass('mg_lb_icon_selected');
                $sel_obj.addClass('mg_lb_icon_selected');
            }, 10);
        });

        // select icon
        $(document).on("click", ".mg_icon_picker_lb .lcfah-list li:not(.lcfah-no-results)", function() {
            const val = ($(this).hasClass('lcfah-no-icon')) ? '' : $(this).find('i').attr('class');

            $sel_type_opt.parent().find('input').val(val);
            $sel_type_opt.attr('class', val);

            tb_remove();
            $sel_type_opt = false;
        });	
        <?php
    }
    
    
    
    
    ####################################################################################################
    
    
    
    
    /* LESS-like CSS prefixer */
    public static function getPrefixedCss($css,$prefix) {
        # Wipe all block comments
        $css = preg_replace('!/\*.*?\*/!s', '', $css);

        $parts = explode('}', $css);
        $keyframeStarted = false;
        $mediaQueryStarted = false;

        foreach($parts as &$part) {
            $part = trim($part); # Wht not trim immediately .. ?
            if(empty($part)) {
                $keyframeStarted = false;
                continue;
            }
            else { # This else is also required
                $partDetails = explode('{', $part);

                if (strpos($part, 'keyframes') !== false) {
                    $keyframeStarted = true;
                    continue;
                }

                if($keyframeStarted) {
                    continue;
                }

                if(substr_count($part, "{")==2) {
                    $mediaQuery = $partDetails[0]."{";
                    $partDetails[0] = $partDetails[1];
                    $mediaQueryStarted = true;
                }

                $subParts = explode(',', $partDetails[0]);
                foreach($subParts as &$subPart) {
                    if(trim($subPart)==="@font-face") continue;
                    else $subPart = $prefix . ' ' . trim($subPart);
                }

                if(substr_count($part,"{")==2) {
                    $part = $mediaQuery."\n".implode(', ', $subParts)."{".$partDetails[2];
                }
                elseif(empty($part[0]) && $mediaQueryStarted) {
                    $mediaQueryStarted = false;
                    $part = implode(', ', $subParts)."{".$partDetails[2]."}\n"; //finish media query
                }
                else {
                    if(isset($partDetails[1]))
                    {   # Sometimes, without this check,
                        # there is an error-notice, we don't need that..
                        $part = implode(', ', $subParts)."{".$partDetails[1];
                    }
                }

                unset($partDetails, $mediaQuery, $subParts); # Kill those three ..
            }   unset($part); # Kill this one as well
        }

        # Finish with the whole new prefixed string/file in one line
        return(preg_replace('/\s+/', ' ', implode("} ", $parts)));
    }



    // handles custom CSS written in LESS and returns a CSS string
    public static function custom_css_less_parser() {
        ob_start();
        include_once(MG_DIR .'/frontend_css.php');

        $css = ob_get_clean();
        if(!trim($css)) { 
            return '';    
        }

        // Divi fix
        if(class_exists('DiviExtension')) {
            // fcking divi adjust ...
            $css = str_replace('body:not(.mg_cust_touch_ol_behav)', '', $css);
            
            $css .= self::getPrefixedCss($css, '#et-boc .et-l');
        }

        return $css;
    }
    
    
    
    // create the frontend css
    public static function create_frontend_css() {	
        global $wp_filesystem;

        if(empty($wp_filesystem)) {
            require_once(ABSPATH .'/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $css = self::custom_css_less_parser();
        if(trim($css)) {
            if(!$wp_filesystem->put_contents(MG_DIR .'/css/custom.css', $css)) {
                $error = true;
            }
            else {
                update_option('mg_dynamic_scripts_id', md5($css));	
            }
        }
        else {
            if(file_exists(MG_DIR .'/css/custom.css'))	{
                wp_delete_file(MG_DIR .'/css/custom.css');
            }
        }

        return (isset($error)) ? false : true;
    }
    
    
    
    
    ####################################################################################################
    
    
    
    
    // custom type options - indexes 
    public static function main_types() {
        return array(
            'image'			=> __('Image', 'mg_ml'), 
            'img_gallery' 	=> __('Slider', 'mg_ml'), 
            'video' 		=> __('Video', 'mg_ml'), 
            'audio' 		=> __('Audio', 'mg_ml'),
            'lb_text'		=> __('Custom Content', 'mg_ml'),
            'post' 			=> __('Posts', 'mg_ml'),
        );	
    }


    
    // given the item main type slug - return the name
    public static function item_types($type = false) {
        $types = array(
            'simple_img' 	=> __('Static Image', 'mg_ml'),
            'single_img' 	=> __('Lightbox Image', 'mg_ml'),
            'img_gallery' 	=> __('Lightbox Slider', 'mg_ml'),
            'inl_slider' 	=> __('Inline Slider', 'mg_ml'),
            'video' 		=> __('Lightbox Video', 'mg_ml'),
            'inl_video' 	=> __('Inline Video', 'mg_ml'),
            'audio'			=> __('Lightbox Audio', 'mg_ml'),
            'inl_audio'		=> __('Inline Audio', 'mg_ml'),
            'link'			=> __('Link', 'mg_ml'),
            'lb_text'		=> __('Custom Content', 'mg_ml'),
            'post_contents'	=> __('Post Contents', 'mg_ml'),
            'inl_text'		=> __('Inline Text', 'mg_ml'),
            'spacer'		=> __('Spacer', 'mg_ml'),
        );

        if($type === false) {
            return $types;
        } else {
            return (isset($types[$type])) ? $types[$type] : ''; 	
        }
    }

    

    // pagination styles
    public static function pag_layouts($type = false) {
        $types = array(
            'standard' 	 		=> __('Commands + full text', 'mg_ml'),
            'only_num'  		=> __('Commands + page numbers', 'mg_ml'),
            'only_arr_dt'		=> __('Only arrows', 'mg_ml'),
            'pag_btn_nums'		=> __('Pages button - numbers', 'mg_ml'),
            'pag_btn_dots'		=> __('Pages button - dots', 'mg_ml'),
            'inf_scroll'		=> __('Infinite scroll', 'mg_ml'),
            'auto_inf_scroll'	=> __('Automatic Infinite scroll', 'mg_ml')
        );

        return ($type === false) ? $types : $types[$type];
    }



    // litteral easing to CSS code
    public static function easing_to_css($easing) {
        switch($easing) {
            case 'ease' : $code = 'ease'; break;
            case 'linear' : $code = 'linear'; break;
            case 'ease-in' : $code = 'ease-in'; break;
            case 'ease-out' : $code = 'ease-out'; break;
            case 'ease-in-out' : $code = 'ease-in-out'; break;
            case 'ease-in-back' : $code = 'cubic-bezier(0.600, -0.280, 0.735, 0.045)'; break;
            case 'ease-out-back' : $code = 'cubic-bezier(0.175, 0.885, 0.320, 1.275)'; break;
            case 'ease-in-out-back' : $code = 'cubic-bezier(0.680, -0.550, 0.265, 1.550)'; break;
        }

        return $code;
    }



    // lightbox layouts
    public static function lb_layouts($type = false, $add_as_default = true) {
        $types = array(
            'full' 					=> __('Full Width', 'mg_ml'), 
            'side_tripartite' 		=> __('Text on right side - one third', 'mg_ml'),
            'side_tripartite_tol' 	=> __('Text on left side - one third', 'mg_ml'),
            'side_bipartite' 		=> __('Text on right side - one half', 'mg_ml'),
            'side_bipartite_tol' 	=> __('Text on left side - one half', 'mg_ml'),
        );

        if($add_as_default) {
            $types = array('as_default' => __('As default', 'mg_ml')) + $types;	
        }

        return ($type === false) ? $types : $types[$type];
    }



    // slider thumbs visibility options
    public static function lcms_thumb_opts($type = false) {
        $types = array(
            ''          => __('(as default)', 'mg_ml'),
            'always'	=> __('Always', 'mg_ml'),
            'yes' 		=> __('Yes with toggle button', 'mg_ml'),
            'no' 		=> __('No with toggle button', 'mg_ml'),
            'never' 	=> __('Never', 'mg_ml'),
        );

        return ($type === false) ? $types : $types[$type];
    }



    // deeplinked elements list
    public static function elem_to_deeplink($type = false) {
        $types = array(
            'item' 		=> __("Item's lightbox", 'mg_ml'), 
            'category'	=> __("Category filter", 'mg_ml'), 
            'search'	=> __("Items search", 'mg_ml'),
            'page'		=> __("Grid pagination", 'mg_ml'),
        );

        return ($type === false) ? $types : $types[$type];
    }



    // item categories array
    public static function item_cats() {
        $cats = array();

        foreach((array)get_terms( 'mg_item_categories', 'hide_empty=0') as $cat) {
            if(is_object($cat) && property_exists($cat, 'term_id')) {
                $cats[ $cat->term_id ] = $cat->name;        
            }
        }	
        return $cats;
    }
    
    
    
    // get the patterns list 
    public static function patterns_list() {
        $patterns = array();
        $patterns_list = scandir(MG_DIR."/img/patterns");

        foreach($patterns_list as $pattern_name) {
            if($pattern_name != '.' && $pattern_name != '..') {
                $patterns[] = $pattern_name;
            }
        }
        return $patterns;	
    }
    
    
    
    
    ####################################################################################################
    
    
    
    
    // item sizes array - allow additional values through filter (AUTO is manually added where needed)
    public static function item_sizes() {
        $default = array(
            // base-12 sizes
            '1_12' => array('name' => '1/12', 'mobile_ready' => false, 'perc' => 0.0833333333),
            '2_12' => array('name' => '2/12', 'mobile_ready' => false, 'perc' => 0.1666666667),
            '3_12' => array('name' => '3/12', 'mobile_ready' => false, 'perc' => 0.25),
            '4_12' => array('name' => '4/12', 'mobile_ready' => true, 'perc' => 0.3333333333),
            '5_12' => array('name' => '5/12', 'mobile_ready' => true, 'perc' => 0.4166666667),
            '6_12' => array('name' => '6/12', 'mobile_ready' => true, 'perc' => 0.5),
            '7_12' => array('name' => '7/12', 'mobile_ready' => true, 'perc' => 0.5833333333),
            '8_12' => array('name' => '8/12', 'mobile_ready' => true, 'perc' => 0.6666666667),
            '9_12' => array('name' => '9/12', 'mobile_ready' => true, 'perc' => 0.75),
            '10_12' => array('name' => '10/12', 'mobile_ready' => true, 'perc' => 0.8333333333),
            '11_12' => array('name' => '11/12', 'mobile_ready' => true, 'perc' => 0.9166666667),
            '12_12' => array('name' => '12/12', 'mobile_ready' => true, 'perc' => 1),
            
            // base-10 sizes
            '1_1' => array('name' => '1/1', 'mobile_ready' => true, 'perc' => 1),
            '1_2' => array('name' => '1/2', 'mobile_ready' => true, 'perc' => 0.499),

            '1_3' => array('name' => '1/3', 'mobile_ready' => true, 'perc' => 0.3329),
            '2_3' => array('name' => '2/3', 'mobile_ready' => true, 'perc' => 0.6658),

            '1_4' => array('name' => '1/4', 'mobile_ready' => true, 'perc' => 0.25),
            '3_4' => array('name' => '3/4', 'mobile_ready' => true, 'perc' => 0.7499),

            '1_5' => array('name' => '1/5', 'mobile_ready' => false, 'perc' => 0.20),
            '2_5' => array('name' => '2/5', 'mobile_ready' => false, 'perc' => 0.398),
            '3_5' => array('name' => '3/5', 'mobile_ready' => false, 'perc' => 0.598),
            '4_5' => array('name' => '4/5', 'mobile_ready' => false, 'perc' => 0.798),

            '1_6' => array('name' => '1/6', 'mobile_ready' => false, 'perc' => 0.1658),
            '5_6' => array('name' => '5/6', 'mobile_ready' => false, 'perc' => 0.8329),

            '1_7' => array('name' => '1/7', 'mobile_ready' => false, 'perc' => 0.1428),
            '1_8' => array('name' => '1/8', 'mobile_ready' => false, 'perc' => 0.125),
            '1_9' => array('name' => '1/9', 'mobile_ready' => false, 'perc' => 0.1111),
            '1_10'=> array('name' => '1/10', 'mobile_ready' => false, 'perc' => 0.1),
        );

        // MG-FILTER - allow filters addition - new ones must comply with existing array structure
        return apply_filters('mg_item_sizes', $default);	
    }
    
    
    
    // mobile sizes array
    public static function mobile_sizes() {
        $sizes = array();
        foreach(self::item_sizes() as $val => $data) {
            if(!$data['mobile_ready']) {
                continue;
            }
            
            $sizes[$val] = $data;	
        }

        return $sizes;
    }
    
    
    
    // sizes to percents
    public static function size_to_perc($size, $leave_auto = false) {
        if($leave_auto && $size == 'auto') {
            return 'auto';
        }

        foreach(self::item_sizes() as $key => $data) {
            if($size == $key) {
                return $data['perc'];	
            }
        }

        // size not detected
        return false;
    }
    
    
    
    // given a normal mg_static::item_sizes() array - returns a basic one to be used in cycles 'index' => 'name'
    public static function simpler_sizes_array($sizes) {
        $simplified = array();

        foreach($sizes as $index => $data) {
            $simplified[ $index ] = $data['name'];
        }

        return $simplified;
    }
    
    
    
    
    ####################################################################################################
    
    
    

    // get type options indexes from the main type
    public static function get_type_opt_indexes($type) {
        if($type == 'simple_img' || $type == 'link') {
            return false;
        }

        if($type == 'single_img') {$copt_id = 'image';}
        else {$copt_id = $type;}

        if(!get_option('mg_'.$copt_id.'_opt')) {
            return false;
        }

        $indexes = array();
        foreach(get_option('mg_'.$copt_id.'_opt') as $opt) {
            $indexes[] = 'mg_'. $copt_id .'_'. mg_static::custom_urlencode($opt);
        }

        return $indexes;	
    }
    


    // prepare the array of not empty custom options for an item
    public static function item_copts_array($type, $post_id) {
        if($type == 'single_img') {
            $type = 'image';
        }
        $copts = get_option('mg_'. $type .'_opt');

        $arr = array();
        if(is_array($copts)) {
            foreach($copts as $copt) {
                $val = get_post_meta($post_id, 'mg_'. $type .'_'. self::custom_urlencode($copt), true);

                if($val && $val != '') {
                    $arr[$copt] = $val;	
                }
            }
        }
        return $arr;
    }
    
    
    
    
    ####################################################################################################
    
    
    
    
    /* 
     * get custom post types and taxonomies array 
     * @param (bool) $exclude_mg_linked - to ignore Advanced Filters add-on dynamic taxonomies 
     * @param (bool) $onlyfirst - to return only first value text
     */
    public static function get_cpt_with_tax($exclude_mg_linked = true, $onlyfirst = false) {
        $cpt = get_post_types(array(
            'show_ui' => true, 
            'publicly_queryable' => true
        ), 'objects');
        $usable = array(); 

        /* allow also pages (shown if they have taxonomy attached) */
        $page_data = get_post_type_object('page');
        $cpt = array('page' => $page_data) + $cpt;


        // post types to ignore 
        $to_ignore = array('attachment', 'revision', 'nav_menu_item');

        foreach($cpt as $pt) {
            if(in_array($pt->name, $to_ignore)) { // exclude known ones
                continue;
            } 
            if(!post_type_supports($pt->name, 'thumbnail')) { // exclude ones without featured image
                continue;
            } 

            $tax = get_object_taxonomies($pt->name, 'objects');

            // add only if has a taxonomy
            if(is_array($tax) && !empty($tax)) {
                $tax_array = array();

                foreach($tax as $slug => $data) {
                    if(in_array($slug, array('post_format'))) {
                        continue;
                    }
                    $tax_array[$slug] = $data->labels->name;	
                }

                $usable[ $pt->name ] = array(
                    'name' => ($pt->name == 'mg_items') ? 'Media Grid '. $pt->labels->name : $pt->labels->name,
                    'tax' => $tax_array
                );		
            }
        }

        $to_return = array();

        $a = 0;
        foreach($usable as $slug => $data) {

            $b = 0;
            foreach($data['tax'] as $tax_slug => $tax_name) {
                $val = $slug.'|||'.$tax_slug;
                if($a == 0 && $b == 0) {
                    $first_cpt_cat = $val;
                }

                // excluding MG related taxonomies (Advanced Filters add-on)
                if($exclude_mg_linked && strpos($tax_slug, 'mgaf_') !== false) {
                    continue;	
                }

                $to_return[ $val ] = $data['name'].' - '.$tax_name;
                $b++;
            }
            $a++;
        }

        return ($onlyfirst && isset($first_cpt_cat)) ? $first_cpt_cat : $to_return;
    }


    
    // return post types array from get_cpt_with_tax()
    public static function pt_list() {
        $pts = array('mg_items', 'attachment');

        foreach(self::get_cpt_with_tax() as $val => $name) {
            list($pt, $tax) = explode('|||', $val);
            $pts[] = $pt;
        }
        return array_unique($pts);
    }


    
    // given cpt + taxonomy - get taxonomy terms in a select field
    public static function get_taxonomy_terms($cpt_tax, $return = 'array', $selected = false) {
        $arr = explode('|||', (string)$cpt_tax);
        $cats = get_terms($arr[1], 'orderby=name&hide_empty=0');

        if($return == 'html') {
            $code = '
            <select data-placeholder="'. esc_attr__('Select a term', 'mg_ml') .' .." name="mg_cpt_tax_term" class="mg_lcsel_dd">
                <option value="">'. esc_html__('Any term', 'mg_ml') .'</option>';

                if(is_array($cats)) {
                    foreach($cats as $cat ) {
                        $sel = ($selected !== false && $cat->term_id == $selected) ? 'selected="selected"' : '';
                        $code .= '<option value="'.$cat->term_id.'" '.$sel.'>'. $cat->name .'</option>'; 
                    }
                }

            return $code . '</select>'; 
        }
        else {
            $data = array('' => __('Any term', 'mg_ml'));
            if(is_array($cats)) {
                foreach($cats as $cat ) {
                    $data[ $cat->term_id ] = $cat->name;
                }
            }

            return $data;	
        }
    }
    


    // given post ID, returns its post type name
    public static function pt_id_to_name($post_id) {
        $post_type = get_post_type($post_id);
        $obj = get_post_type_object($post_type);

        return (empty($obj)) ? 'unknown' : $obj->labels->singular_name;	
    }
    
    
    
    
    ####################################################################################################
    
    
    
    
    // get grids list template for builder
    public static function builder_grids_list() {
        $grids = get_terms('mg_grids', array('hide_empty' => 0, 'orderby' => 'name'));

        if(count($grids)) {
            $code = '';

            if(count($grids) > 1) {
                $code = '
                <div class="mg_dd_list_search">
                    <form><input type="text" placeholder="'. esc_attr__("search by typing grid's name or ID", 'mg_ml') .'" autocomplete="off" /></form>	
                </div>';
            }

            $code .= '
            <div class="mg_items_list_scroll">';

                foreach ($grids as $grid) {
                    $code .= '
                    <div class="mg_dd_list_item mgg_'. $grid->term_id .'" rel="'. $grid->term_id .'">
                        <em>#'. $grid->term_id .'</em>
                        <span class="mg_grid_tit">'. $grid->name .'</span>

                        <small class="mg_del_grid mg_grids_list_btn mg_del_row dashicons dashicons-no-alt" rel="'. (int)$grid->term_id .'" title="'. esc_attr__('delete grid', 'mg_ml') .'"></small>
                        <small class="mg_clone mg_grids_list_btn mg_move_row dashicons dashicons-admin-page" rel="'. (int)$grid->term_id .'" title="'. esc_attr__('clone grid', 'mg_ml') .'"></small>
                        <small class="mg_edit_name mg_grids_list_btn mg_move_row dashicons dashicons-edit" rel="'. (int)$grid->term_id .'" title="'. esc_attr__("rename grid", 'mg_ml') .'"></small>
                    </div>';	
                }

            $code .= '</div>';
        }
        else {
            $code = '<div class="mg_dd_list_nogrids"><em>'. __('No grids found', 'mg_ml') .' ..</em></div>';	
        }

        return $code;
    }



    // save grid data - compressing if available
    public static function save_grid_data($grid_id, $arr) {
        $str = serialize($arr);
        $slug = uniqid();

        if(function_exists('gzcompress') && function_exists('gzuncompress')) {
            $str = base64_encode(gzcompress($str, 9));
            $slug = 'mg_gzc_' . $slug;
        }

        // update grid term
        return wp_update_term($grid_id, 'mg_grids', array('slug' => $slug, 'description' => $str));	
    }



    // get grid contents - uncompressing || returns associative array('items' => array(), 'cats' => array())
    public static function get_grid_data($grid_id) {

        $term = get_term_by('id', $grid_id, 'mg_grids');
        if(empty($term->description)) {
            return array('items' => array(), 'cats' => array());
        }

        // if supported - uncompress
        if(strpos($term->slug, 'mg_gzc_') !== false) {
            if(function_exists('gzcompress') && function_exists('gzuncompress')) {
                $data = gzuncompress(base64_decode($term->description));
            }
        }
        else {
            $data = $term->description;
        }

        return (array)unserialize($data); 
    }
    
    
    
    
    ####################################################################################################
    
    
    
    
    // get related post for Post Contents item type
    public static function post_contents_get_post($item_id) {
        $cpt_tax_arr = explode('|||', get_post_meta($item_id, 'mg_cpt_source', true));
        $term = get_post_meta($item_id, 'mg_cpt_tax_term', true); 

        $args = array(
            'post_type' => $cpt_tax_arr[0],  
            'post_status' => 'publish', 
            'posts_per_page' => 1,
            'offset' => (int)get_post_meta($item_id, 'mg_post_query_offset', true),
            'meta_query' => array( 
                array( 'key' => '_thumbnail_id')
            )
        );

        if($term) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $cpt_tax_arr[1],
                    'field' => 'id',
                    'terms' => $term,
                    'include_children' => true
                )
            );	
        } else {
            $args['taxonomy'] = $cpt_tax_arr[1];
        }

        $query = new WP_query($args);
        return (count($query->posts)) ? $query->posts[0] : false;	
    }



    // woocommerce integration - get product attributes
    public static function wc_prod_attr($prod_obj){
        $attributes = $prod_obj->get_attributes();

        $prod_attr = array();
        if (!$attributes) {
            return $prod_attr;
        }

        foreach ($attributes as $attribute) {

            // skip variations
            if(isset($attribute['variation']) && $attribute['variation'] && !get_option('mg_use_wc_attr_variations')) {
                continue;
            }


            if($attribute['is_taxonomy']) {
                $terms = wp_get_post_terms($prod_obj->get_id(), $attribute['name'], 'all');

                // get the taxonomy
                $tax = $terms[0]->taxonomy;

                // get the tax object
                $tax_object = get_taxonomy($tax);

                // get tax label
                if ( isset ($tax_object->labels->name) ) {
                    $tax_label = $tax_object->labels->name;
                } elseif ( isset( $tax_object->label ) ) {
                    $tax_label = $tax_object->label;
                }

                foreach ($terms as $term) {
                    if(isset($prod_attr[$tax_label])) {
                        $prod_attr[$tax_label][] = $term->name;
                    } else {
                        $prod_attr[$tax_label] = array($term->name);	
                    }
                }
            } else {
                if(isset($prod_attr[ $attribute['name'] ])) {
                    $prod_attr[ $attribute['name'] ][] = $attribute['value'];
                } else {
                    $prod_attr[ $attribute['name'] ] = array($attribute['value']);	
                }
            }
        }

        return $prod_attr;
    }

    

    // get WP library images
    public static function library_images($page = 1, $per_page = 15, $search = '') {
        $query_images_args = array(
            'post_type' => 'attachment', 
            'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png,image/webp',
            'post_status' => 'inherit', 
            'posts_per_page' => $per_page, 
            'paged' => $page
        );
        if(isset($search) && !empty($search)) {
            $query_images_args['s'] = $search;	
        }

        $query_images = new WP_Query( $query_images_args );
        $images = array();

        foreach ( $query_images->posts as $image) { 
            $images[] = $image->ID;		
        }

        // global images number
        $img_num = $query_images->found_posts;

        // calculate the total
        $tot_pag = ceil($img_num / $per_page);

        // can show more?
        $shown = $per_page * $page;
        ($shown >= $img_num) ? $more = false : $more = true; 

        return array(
            'img'		=> $images, 
            'pag' 		=> $page, 
            'tot_pag' 	=>$tot_pag, 
            'more' 		=> $more, 
            'tot' 		=> $img_num
        );
    }

    

    // get the audio files from the WP library
    public static function library_audio($page = 1, $per_page = 15, $search = '') {
        $query_audio_args = array(
            'post_type' => 'attachment', 
            'post_mime_type' =>'audio', 
            'post_status' => 'inherit', 
            'posts_per_page' => $per_page, 
            'paged' => $page
        );
        if(isset($search) && !empty($search)) {
            $query_audio_args['s'] = $search;	
        }

        $query_audio = new WP_Query( $query_audio_args );
        $tracks = array();

        foreach ( $query_audio->posts as $audio) { 
            $tracks[] = array(
                'id'	=> $audio->ID,
                'url' 	=> wp_get_attachment_url($audio->ID), 
                'title' => $audio->post_title
            );
        }

        // global images number
        $track_num = $query_audio->found_posts;

        // calculate the total
        $tot_pag = ceil($track_num / $per_page);

        // can show more?
        $shown = $per_page * $page;
        ($shown >= $track_num) ? $more = false : $more = true; 

        return array('tracks' => $tracks, 'pag' => $page, 'tot_pag' =>$tot_pag  ,'more' => $more, 'tot' => $track_num);
    }
    
    
    
    // given an array of selected images or tracks - returns only existing ones
    public static function existing_sel($media, $rel_videos = false) {
        if(is_array($media)) {
            $new_array = array();
            $a = 0;

            foreach($media as $media_id) {
                if(is_object( get_post($media_id) )) {
                    if($rel_videos === false) {
                        $new_array[] = $media_id;
                    } 
                    else {
                        $vid = (isset($rel_videos[$a])) ? $rel_videos[$a] : '';
                        $new_array[] = array('img' => $media_id, 'video' => $vid);
                    }
                }
                $a++;
            }

            return (!count($new_array)) ? false : $new_array;
        }
        else {
            return false;
        }	
    }
                    
                    
                    
    // create selected slider image list - starts from array of associative array
    public static function sel_slider_img_list($data) {
        if(!is_array($data)) {
            return '<p>'. __('No images selected', 'mg_ml') .' .. </p>';
        }
        $code = '';

        foreach($data as $elem) {

            if($elem['video']) {
                $span_title = esc_attr__('Edit video URL', 'mg_ml');
                $span_class = 'mg_slider_video_on'; 	
            } else {
                $span_title = esc_attr__('set as video slide', 'mg_ml');
                $span_class = 'mg_slider_video_off'; 		
            }

            $thumb_data = wp_get_attachment_image_src($elem['img'], array(90, 90));

            $code .= '
            <li>
                <input type="hidden" name="mg_slider_img[]" class="mg_slider_img_field" value="'. esc_attr($elem['img']) .'" />
                <input type="hidden" name="mg_slider_vid[]" class="mg_slider_video_field" value="'. esc_attr($elem['video']) .'" autocomplete="off" />

                <figure style="background-image: url(\''. esc_attr($thumb_data[0]) .'\');"></figure>
                <span class="dashicons dashicons-dismiss" title="'. esc_attr__('remove image', 'mg_ml') .'"></span>
                <i title="'. $span_title .'" class="'. $span_class .'"></i>
            </li>';	
        }

        return $code;
    }  
                    
                    
                    
    // custom excerpt
    public static function custom_excerpt($string, $max) {
        $num = strlen($string);

        if($num > $max) {
            $string = substr($string, 0, $max) . '..';
        }

        return $string;
    }
                    
                    
                    
    // item's deeplink URL - for XML sitemap
    public static function item_deeplinked_url($item_id, $item_title) {
        $base_url = get_option('mg_sitemap_baseurl', get_site_url());
        $txt = (empty($item_title)) ? '' : '/'. sanitize_title($item_title);

        if(strpos($base_url, '?') === false) {
            return $base_url .'?mgi_='.$item_id.$txt;	
        }  else {
            return $base_url .'&mgi_='.$item_id.$txt;		
        }
    }
                    
    
    
    
    ####################################################################################################
    
    
                    
    
    // addons list database
    public static function addons_db($addon = false) {
        $addons = array(
            'mgaf' => array(
                'name' => 'Advanced Filters',
                'descr'	=> 'Your contents, discoverable in a click!<br/>Your items won’t be lost anymore in your grids and visitors will get exactly what they are searching for in few clicks',
                'link'	=> 'https://charon.lcweb.it/ac9f5830',
                'path'	=> 'media-grid-advanced-filters/mg_adv_filters.php'
            ),

            'mgom' => array(
                'name' 	=> 'Overlay Manager',
                'descr'	=> 'Boost Media Grid with your own overlays!<br/>The add-on lets you create unlimited overlays with hundreds of different possible combinations',
                'link'	=> 'https://charon.lcweb.it/1b569de0',
                'path'	=> 'media-grid-overlay-manager/mg_overlay_manager.php'
            ),	
        );	
        return (!$addon || !isset($addons[$addon])) ? $addons : $addons[$addon]; 	
    }

                    

    // returns an array of add-ons not enabled yet 
    public static function addons_not_installed() {
        $found = array();

        foreach(self::addons_db() as $id => $data) {
            if(!is_plugin_active( $data['path'] )) {
                $found[] = $id;	
            }
        }

        return $found;
    }
}
