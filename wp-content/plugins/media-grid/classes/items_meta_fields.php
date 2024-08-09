<?php
// ITEMS META FIELDS FRAMEWORK - DECLARATION AND CODES

class mg_meta_fields {
	private $item_id; // static resource to know which item ID
	private $item_type; // static resource to know which item type
	private $item_keys; // meta keys associated to item - useful to set default values
	
	public $fields = array(); // fields meta structure
	public $groups = array(); // field groups
	
	public $index_to_save = array(); // which field indexes to validate and save - ADD CUSTOM INDEXES IN CUSTOM FIELDS
	
	
	
	/* INIT - declare item type */
	function __construct($item_id, $item_type) {
		$this->item_id = $item_id;
		$this->item_type = $item_type;
		
		$this->setup_fields();
		$this->setup_groups();
	}
	
	
	// setup fields
	private function setup_fields() {
		
		$fields = array(
			'mg_kenburns_fx' => array(
				'label' => __('Ken Burns effect?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> ($this->item_type == 'inl_slider') ? __("If checked applies Ken Burns effect to images", 'mg_ml') : __("If checked applies Ken Burns effect to grid's image <strong>(will discard Overlay Manager effects)</strong>", 'mg_ml'),
				'group' => ($this->item_type == 'inl_slider') ? 'slider_opts' : 'grid_item_opts', 
			), 
			'mg_static_show_overlay' => array(
				'label' => __('Display overlay?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked displays overlay for this item', 'mg_ml'),
				'group' => 'grid_item_opts', 
			), 
			
			'mg_slider_h_val' => array(
				'label' 	=> __("Slider's height", 'mg_ml'),
				'type'		=> 'val_n_type',
				'def'		=> 53,
				'max_val_len' => 4,
				'note'		=> __('% is related to its width, VH to screen height. Leave empty to use default value', 'mg_ml'),
				'group' 	=> ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			),
            'mg_slider_min_h' => array(
				'label' 	=> __("Slider's min-height", 'mg_ml'),
				'type'		=> 'slider',
				'min_val' 	=> 50,
				'max_val' 	=> 2000,
				'step' 		=> 50,
				'value' 	=> 'px',
				'def' 		=> '100',
				'note'		=> __('Setting a minimum value for the slider to avoid tiny results on mobile <strong>(not for media-focused mode)</strong>', 'mg_ml'),
				'group' 	=> ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			), 
			'mg_slider_crop' => array(
				'label' => __("Image's display mode", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
                    'cover'     => __('Fill slider size (crop)', 'mg_ml'),
                    'contain' 	=> __('Show full image (downscale)', 'mg_ml'),
                ),
				'note'	=> __("Choose how images will be managed by slider", 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts',  
			), 
			'mg_slider_autoplay' => array(
				'label' => __('Allow slideshow function?', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'' 			=> __('(as default)', 'mg_ml'), 
					'yes'       => __('Yes', 'mg_ml'), 
					'autoplay'  => __('Yes - autoplay', 'mg_ml'),
					'no' 	    => __('No', 'mg_ml'),
				),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			), 
			'mg_slider_captions' => array(
				'label' => __('Display captions?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked displays slider image captions', 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			),
			'mg_slider_random' => array(
				'label' => __('Random images?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, randomizes slider images', 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			),
			
			'mg_video_use_poster' => array(
				'label' => __('Use featured image as video poster?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, sets featured image as video poster', 'mg_ml'),
				'group' => 'video_opts', 
			),
			'mg_autoplay_inl_video' => array(
				'label' => __('Autoplay video?', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'' 			=> __('no', 'mg_ml'), 
					'muted' 	=> __('Yes, muted', 'mg_ml'), 
					'normal' 	=> __('Yes, with sound', 'mg_ml'), 
				),
				'note'	=> 'NB: '. __('Modern browsers allow autoplay only if user clicked the page or on muted videos', 'mg_ml'),
				'group' => 'video_opts', 
			),
            'mg_lb_video_h' => array(
				'label' 	=> __("Player's height", 'mg_ml'),
                'type'		=> 'slider',
				'min_val' 	=> 0,
				'max_val' 	=> 300,
				'step' 		=> 0.1,
				'value' 	=> '%',
				'def' 		=> '',
				'note'		=> __("Set video's aspect ratio. Set to zero to use the default 16:9 value", 'mg_ml'),
				'optional'	=> true,
				'group' 	=> 'lightbox',
			), 
			
			'mg_soundcloud_url' => array(
				'label' => __("Soundcloud, Mixcloud or Spotify URL", 'mg_ml'),
				'type'	=> 'text',
				'note'	=> __('Filling this field, selected tracklist <strong>will be ignored</strong>', 'mg_ml'),
				'group' => 'audio_opts', 
			),
			'mg_lb_spotify_h' => array(
				'label' 	=> __("Spotify player's height", 'mg_ml'),
				'type'		=> 'val_n_type',
				'def'		=> 100,
				'max_val_len' => 3,
				'note'		=> __('% is related to its width', 'mg_ml'),
				'group' 	=> 'lightbox', 
			), 
			
			'mg_link_url' => array(
				'label' => __("Link URL", 'mg_ml'),
				'type'	=> 'textarea',
				'note'	=> '',
				'group' => 'link_opts', 
			),
			'mg_link_target' => array(
				'label' => __('Link target', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'top' 	=> __('In the same page', 'mg_ml'), 
					'blank' => __('In a new page', 'mg_ml')
				),
				'note'	=> __('Choose how link will be opened', 'mg_ml'),
				'group' => 'link_opts', 
			),
			'mg_link_nofollow' => array(
				'label' => __('Use nofollow?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If enabled, uses rel="nofollow" on link', 'mg_ml'),
				'group' => 'link_opts', 
			), 
			'mg_cpt_source' => array(
				'label' => __('Post type and taxonomy', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_static::get_cpt_with_tax(false),
				'note'	=> __('Choose the post type and taxonomy to fetch the post from', 'mg_ml'),
				'group' => 'pc_opts', 
			),
			'mg_cpt_tax_term' => array(
				'label' => __("Taxonomy's term", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_static::get_taxonomy_terms( mg_static::get_cpt_with_tax(false, true) ),
				'note'	=> __("Choose the taxonomy's term to fetch the post from", 'mg_ml'),
				'group' => 'pc_opts', 
			),
			'mg_post_query_offset' => array(
				'type'		=> 'slider',
				'label' 	=> __('Query offset', 'mg_ml'),
				'min_val' 	=> 0,
				'max_val' 	=> 40,
				'step' 		=> 1,
				'value' 	=> '',
				'def' 		=> 0,
                'respect_limits' => false,
				'note'		=> __('Sets how many posts to skip during the query', 'mg_ml'),
				'group' => 'pc_opts',
			), 
			'mg_use_item_feat_img' => array(
				'label' => __("Use item's featured image?", 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __("If checked, uses item's featured image in grids instead of post's one", 'mg_ml'),
				'group' => 'pc_opts', 
			), 
			'mg_hide_feat_img' => array(
				'label' => __('Hide featured image in lightbox?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __("If checked, hides posts featured image in lightbox", 'mg_ml'),
				'group' => ($this->item_type == 'post') ? 'post_opts' : 'pc_opts', 
			), 
			'mg_link_to_post' => array(
				'label' => __('Direct link to post?', 'mg_ml'),
				'type'		=> 'select',
				'multiple'	=> false,
				'val' 	=> array(
					'' 		=> __('no', 'mg_ml'), 
					'top' 	=> __('Yes, open link in the same page', 'mg_ml'), 
					'blank' => __('Yes, open link in a new page', 'mg_ml'), 
				),
				'note'	=> __('Whether to use the item as direct link to posts', 'mg_ml'),
				'group' => 'pc_opts', 
			), 
			
			
			#####################################
			
			
			### inline texts
			'mg_inl_txt_box_bg' => array(
				'label'         => __('Box background color', 'mg_ml'),
				'type'	        => 'color',
                'extra_modes'   => array('alpha', 'linear-gradient', 'radial-gradient'),
				'note'	        => __('Leave blank to use the default one', 'mg_ml'),
				'group'         => 'inl_txt_opts', 
			), 
			'mg_inl_txt_img_as_bg' => array(
				'label' => __('Use featured image as background?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked uses featured image as box background', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_bg_video_url' => array(
				'type'			=> 'custom',
				'cust_callback' => 'video_bg_url_f_code',
				'group' 		=> 'inl_txt_opts',
			),
			'mg_inl_txt_color' => array(
				'label' => __('Text main color', 'mg_ml'),
				'type'	=> 'color',
				'note'	=> __('Leave blank to use the default one', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_inl_txt_vert_align' => array(
				'label' => __('Vertical alignment', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'middle' => __('middle', 'mg_ml'), 
					'top' => __('top', 'mg_ml'), 
					'bottom' => __('bottom', 'mg_ml')
				),
				'note'	=> __('Text vertical alignment in the box', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			),
			'mg_inl_txt_vert_align' => array(
				'label' => __('Vertical alignment', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'middle' 	=> __('middle', 'mg_ml'), 
					'top' 		=> __('top', 'mg_ml'), 
					'bottom'	=> __('bottom', 'mg_ml')
				),
				'note'	=> __("Text's vertical alignment in the box", 'mg_ml'),
				'group' => 'inl_txt_opts', 
			),
			'mg_inl_txt_resp_behav' => array(
				'label' => __('Contents responsive behavior', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'none' 		=> __('none', 'mg_ml'), 
					'scroll' 	=> __('add scrollers', 'mg_ml'), 
					'txt_resize'=> __('resize text (use only with textual contents)', 'mg_ml')
				),
				'note'	=> __("Choose how contents will behave if they are bigger than item's height", 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_clean_inl_txt' => array(
				'label' => __('Clean mode?', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'' 		=> '('. __('as default', 'mg_ml') .')', 
					'yes' 	=> __('yes', 'mg_ml'), 
					'no'	=> __('no', 'mg_ml')
				),
				'note'	=> __('Check to ignore global shadows and borders', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_inl_txt_custom_css' => array(
				'label' => __('Custom CSS (optional)', 'mg_ml'),
				'type'	=> 'textarea',
				'placeh'=> 'example - background-image: url(the.image.url.jpg);',
				'note'	=> __('custom CSS applied to the item. <strong>DO NOT use selectors</strong>', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 

			
			#####################################
			
			
			### post fields
			'mg_post_cats' => array(
				'label' 	=> __('Associated categories', 'mg_ml'),
				'type'		=> 'select',
				'multiple'	=> true,
				'val' 		=> mg_static::item_cats(),
				'note'		=> '',
				'group' 	=> 'post_opts', 
			),
			'mg_link_only' => array(
				'label' => __('Direct link to post?', 'mg_ml'),
				'type'		=> 'select',
				'multiple'	=> false,
				'val' 	=> array(
					'' 		=> __('no', 'mg_ml'), 
					'top' 	=> __('Yes, open link in the same page', 'mg_ml'), 
					'blank' => __('Yes, open link in a new page', 'mg_ml'), 
				),
				'note'		=> '',
				'group' 	=> 'post_opts', 
			),
			'mg_attach_as_static' => array(
				'label' => __('Use as static image?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, resulting grid item will be a static image, without lightbox', 'mg_ml'),
				'group' => 'post_opts', 
			),
            
			
			#####################################
			
			
			### woocommerce fields
			'mg_slider_add_featured' => array(
				'label' => __('Prepend featured image?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, prepends featured image in slider', 'mg_ml'),
				'group' => 'wc_slider', 
			),
			
			
			#####################################
			
			
			### lightbox fields
			'mg_layout' => array(
				'label' => __("Lightbox Layout", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_static::lb_layouts(),
				'note'	=> '',
				'group' => 'lightbox', 
			), 
			'mg_lb_max_w' => array( 
				'label' 	=> __("Lightbox max-width", 'mg_ml'),
				'type'		=> 'slider',
				'min_val' 	=> 0,
				'max_val' 	=> 2000,
				'step' 		=> 50,
				'value' 	=> 'px',
				'def' 		=> '',
				'note'		=> __('Use zero to use global lightbox sizing <strong>(not for media-focused mode)</strong>', 'mg_ml'),
				'optional'	=> true,
				'group' 	=> 'lightbox',
			), 
			'mg_lb_img_display_mode' => array(
				'label' => __("Image's display mode", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'feat_w'	=> __("Fill wrapper's width", 'mg_ml'),
					'img_w'		=> __("Avoid enlargements", 'mg_ml'),
				),
				'note'	=> __('Set how image will be managed in lightbox', 'mg_ml'),
				'group' => 'lightbox', 
			),
			'mg_img_maxheight' => array(
				'type'		=> 'slider',
				'label' 	=> __("Image's max-height", 'mg_ml'),
				'min_val' 	=> 0,
				'max_val' 	=> 1400,
				'step' 		=> 50,
				'value' 	=> 'px',
				'def' 		=> '',
                'respect_limits' => false,
				'note'		=> __('Leave zero to not resize lightbox image <strong>(not for media-focused mode)</strong>', 'mg_ml'),
				'optional'	=> true,
				'group' 	=> ($this->item_type == 'woocomm') ? 'wc_img' : 'lightbox',
			),
			'mg_lb_feat_match_txt' => array(
				'label' => __('Match contents height?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __("If subject's height is smaller than texts, match it <strong>(only side-text layout, not media-focused)</strong>", 'mg_ml'),
				'group' => 'lightbox', 
			), 
			'mg_lb_img_fx' => array( // keep dropdown to guarantee retrocompatibility
				'label' => __("Zoomable image", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
                    ''      => esc_html__('no'),
                    'zoom'  => esc_html__('yes'),
                ),
				'group' => ($this->item_type == 'woocomm') ? 'wc_img' : 'lightbox', 
			), 
			'mg_lb_contents_padding' => array(
				'label' 	=> __('Contents padding', 'mg_ml'),
				'type'		=> '4_numbers',
				'min_val' 	=> '0',
				'max_val' 	=> '50',
				'value' 	=> 'px',
				'def' 		=> array(0, 0, 0, 0),
				'note'		=> __('Set contents custom padding (top - right - bottom - left)', 'mg_ml'),
				'group' 	=> 'lightbox', 
			), 
            'mg_mf_lb_media_w' => array(
				'type'		=> 'slider',
				'label' 	=> __("Media width in media-focused lightbox", 'mg_ml'),
				'min_val' 	=> 5,
				'max_val' 	=> 100,
				'step' 		=> 1,
				'value' 	=> '%',
				'def' 		=> '50',
                'respect_limits' => false,
				'note'		=> __("Value related to screen's width", 'mg_ml'),
				'group' 	=> ($this->item_type == 'woocomm') ? 'wc_slider' : 'lightbox',
			),
            'mg_lb_media_fill_space' => array(
				'label' => __('Fill available space, ', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('<strong>(not for media-focused mode)</strong>', 'mg_ml'),
				'group' => 'lightbox', 
			), 
			'mg_lb_no_comments' => array(
				'label' => __('Disable comments?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> '',
				'group' => 'lightbox', 
			), 
            'mg_slider_thumbs' => array(
				'label' => __('Show thumbnails?', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_static::lcms_thumb_opts(),
				'note'	=> __("Choose whether to show thumbnails navigation", 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			), 
			
			
			#####################################
			
			
			### custom structure fields
			// custom attributes
			'mg_cust_attr' => array(
				'type'			=> 'custom',
				'cust_callback' => 'cust_attr_f_code',
				'group' 		=> 'cust_attr',
			), 
			
			// custom icon
			'mg_cust_icon' => array(
				'type'			=> 'custom',
				'cust_callback' => 'cust_icon_f_code',
				'group' 		=> 'grid_item_opts',
			), 
			
			// image picker
			'mg_slider_img' => array(
				'type'			=> 'custom',
				'cust_callback' => 'slider_img_f_code',
				'group' 		=> 'slider_img',
			),
		
			// audio picker
			'mg_audio_tracks' => array(
				'type'			=> 'custom',
				'cust_callback' => 'audio_tracks_f_code',
				'group' 		=> 'tracklist',
			),
		
			// video picker
			'mg_video_url' => array(
				'type'			=> 'custom',
				'cust_callback' => 'video_url_f_code',
				'group' 		=> 'video_opts',
			),

		);	
        
        
        // remove lightbox slider thumbs if not needed
        if(get_option('mg_lb_slider_extra_nav', 'thumbs') != 'thumbs') {
            unset($fields['mg_slider_thumbs']);    
        }
        
		
		/* MG-FILTER - manage item meta fields */
		$this->fields = apply_filters('mg_item_meta_fields', $fields);
	}
	
	
	// setup groups
	private function setup_groups() {
		 $groups = array(
			'grid_item_opts'	=> __("Grid Item Options", 'mg_ml'),
			'slider_opts'		=> __('Slider Options', 'mg_ml'),
			'slider_img'		=> __('Slider Images', 'mg_ml'),
			'video_opts' 		=> __('Video Options', 'mg_ml'),
			'audio_opts' 		=> __('Audio Options', 'mg_ml'),
			'tracklist' 		=> __('Tracklist', 'mg_ml'),
			'link_opts' 		=> __('Link Options', 'mg_ml'),
			'pc_opts' 			=> __('Post Content Options', 'mg_ml'),
			'inl_txt_opts' 		=> __('Inline Text Options', 'mg_ml'),
			'spacer_opts' 		=> __('Spacer Options', 'mg_ml'),
			'lightbox' 			=> __('Lightbox Options', 'mg_ml'),
			'cust_attr' 		=> __('Custom Attributes', 'mg_ml'),

			'post_opts' 		=> __('Post Options', 'mg_ml'),
			'wc_img'			=> __('Without gallery images', 'mg_ml'),
			'wc_slider'			=> __('With gallery images', 'mg_ml'),
		);
		
		/* MG-FILTER - manage item meta groups */
		$this->groups = apply_filters('mg_item_meta_groups', $groups);	
	}
	


	////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	/* get type fields 
	 * @return return (array) an associative array of fields split in groups
	 */
	public function type_fields() {
		switch($this->item_type) {
			
			// static image
			case 'simple_img' : 
				$f = array('mg_static_show_overlay', 'mg_kenburns_fx', 'mg_cust_icon'); 
                break;
				
			// single image
			case 'single_img' : 
				$f = array('mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_lb_img_fx', 'mg_cust_attr'); 
                break;
		
			// lightbox slider
			case 'img_gallery' : 
				$f = array('mg_slider_img', 'mg_slider_h_val', 'mg_slider_min_h', 'mg_slider_crop', 'mg_slider_autoplay', 'mg_slider_thumbs','mg_slider_captions', 'mg_slider_random', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_mf_lb_media_w', 'mg_lb_feat_match_txt', 'mg_cust_attr'); 
                break;	
			
			// inline slider
			case 'inl_slider' : 
				$f = array('mg_slider_img', 'mg_slider_autoplay', 'mg_slider_captions', 'mg_slider_random', 'mg_kenburns_fx'); 
                break;
			
			// lightbox video
			case 'video' : 
				$f = array('mg_video_url', 'mg_video_use_poster', 'mg_autoplay_inl_video', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_mf_lb_media_w', 'mg_lb_video_h', 'mg_lb_feat_match_txt', 'mg_cust_attr'); 
                break;	
				
			// inline video
			case 'inl_video' : 
				$f = array('mg_video_url', 'mg_video_use_poster', 'mg_autoplay_inl_video', 'mg_cust_icon'); 
                break;	
				
			// lightbox audio
			case 'audio' : 
				$f = array('mg_audio_tracks', 'mg_soundcloud_url', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_spotify_h', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_mf_lb_media_w', 'mg_cust_attr'); 
                break;	
				
			// inline audio
			case 'inl_audio' : 
				$f = array('mg_audio_tracks', 'mg_soundcloud_url', 'mg_kenburns_fx', 'mg_static_show_overlay', 'mg_cust_icon'); 
                break;	
		
			// link
			case 'link' : 
				$f = array('mg_link_url', 'mg_link_target', 'mg_link_nofollow', 'mg_kenburns_fx', 'mg_cust_icon'); 
                break;
		
			// custom content
			case 'lb_text' : 
				$f = array('mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_contents_padding', 'mg_cust_attr'); 
                break;
				
			// post contents
			case 'post_contents' : 
				$f = array('mg_cpt_source', 'mg_cpt_tax_term', 'mg_post_query_offset', 'mg_use_item_feat_img', 'mg_hide_feat_img', 'mg_link_to_post', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_lb_feat_match_txt', 'mg_lb_img_fx'); 
                break;	
			
			// inline text
			case 'inl_text' : 
				$f = array('mg_inl_txt_box_bg', 'mg_inl_txt_img_as_bg', 'mg_bg_video_url', 'mg_inl_txt_color', 'mg_inl_txt_vert_align', 'mg_inl_txt_resp_behav', 'mg_clean_inl_txt', 'mg_inl_txt_custom_css'); 
                break;
			
			// post type (no mg_items and WC)
			case 'post' : 
				$f = array('mg_post_cats', 'mg_hide_feat_img', 'mg_link_only', 'mg_attach_as_static', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_feat_match_txt', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_lb_img_fx', 'mg_cust_attr'); 
                break;
			
			// WooCommerce product
			case 'woocomm' : 
				$f = array('mg_post_cats', 'mg_link_only', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_feat_match_txt', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_lb_img_fx', 'mg_slider_h_val', 'mg_slider_crop', 'mg_slider_autoplay', 'mg_mf_lb_media_w', 'mg_slider_thumbs', 'mg_slider_captions', 'mg_slider_random', 'mg_slider_add_featured'); 
                break;
				
			default : 
                $f = array(); 
                break;	
		}	
		
		
		// add comments switch if they are active
		if(get_option('mg_lb_comments') && in_array('mg_lb_max_w', $f)) {
			$f[] = 'mg_lb_no_comments';	
		}
		
        
        // attachment post type - remove direct link and add option to show as static
        if(get_post_type($this->item_id) == 'attachment') {
            if(($key = array_search('mg_link_only', $f)) !== false) {
                unset($f[$key]);
            }
        } else {
            if(($key = array_search('mg_attach_as_static', $f)) !== false) {
                unset($f[$key]);
            }    
        }
        
		
		/* MG-FILTER - manage which meta fields are assigned to item types */
		return apply_filters('mg_item_meta_to_type', $f, $this->item_type);	
	}
	
	
	
	/* return fields code */
	public function get_fields_code() {
		$raw_structure = array();
		$structure = array();
		$code = '';
		
		// know which meta keys item has got
		$this->item_keys = get_post_custom_keys($this->item_id);
		
		// get fields
		foreach($this->type_fields() as $f) {
			if(isset($this->fields[$f])) {
                $raw_structure[$f] = $this->fields[$f];
            }
		}
		
		// grab groups and split fields in there
		foreach($raw_structure as $id => $args) {
			if(!isset($structure[ $args['group'] ])) {
				$structure[ $args['group'] ] = array();
			}
			
			$structure[ $args['group'] ][$id] = $args; 
		}
		
		// compose code
		foreach($structure as $group => $fields) {
			$code .= '
			<section class="mg_imf_group mg_imf_'.$group.'">
			<h4>'. $this->groups[$group] .'</h4>';
			
				foreach($fields as $fid => $args) {
					$code .= '
					<div class="mg_imf_field mg_imf_'.$fid.'">
						'. $this->opt_to_code($fid) .'
					</div>';	
				}
			
			$code .= '</section>';	
		}
		
		return $code;
	}
    
	
	
	/* 
	 * get validation indexes for item type 
	 * @return (array)
	 */
	public function get_fields_validation() {
		$indexes = array();
		
		foreach($this->type_fields() as $fid) {
			$f = $this->fields[$fid];
			
			if($f['type'] != 'custom') {
				$indexes[] = array('index'=>$fid, 'label'=>$f['label']);	
			}
			
			if($f['type'] == 'val_n_type') {
				$indexes[] = array('index'=>$fid.'_type', 'label'=>$f['label'].' type');		
			}
			
			
			// custom fields validation
			if($f['type'] == 'custom') {
				switch($fid) {
					
					case 'mg_cust_attr' :
						$co_indexes = mg_static::get_type_opt_indexes($this->item_type);
						if(is_array($co_indexes)) {
							foreach($co_indexes as $copt) {
								$indexes[] = array('index'=>$copt, 'label'=>$copt);
							}
						}
						break;
						
					case 'mg_cust_icon' :
						$indexes[] = array('index'=>'mg_cust_icon', 'label'=>'Custom Icon');
						break;	
					
					case 'mg_slider_img' :
						$indexes[] = array('index'=>'mg_slider_img', 'label'=>'Slider images');
						
						if($this->item_type == 'img_gallery') {
							$indexes[] = array('index'=>'mg_slider_vid', 'label'=>'Slider images video');
						}
						break;
				    
					case 'mg_audio_tracks' :
						$indexes[] = array('index'=>'mg_audio_tracks', 'label'=>'Tracks');
						break;	
					
					case 'mg_video_url' :
						$indexes[] = array('index'=>'mg_video_url', 'label'=>'Video URL');
						break;	
						
					case 'mg_bg_video_url' :
						$indexes[] = array('index'=>'mg_bg_video_url', 'label'=>'Background video URL');
						break;	
				}
			}
		}		
		
		return $indexes;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////////////
	
	
		
	/* Passing field ID, returns its code basing on type */ 	
	public function opt_to_code($field_id) {
		if(!isset($this->fields[$field_id])) {
            return '';
        }
		
		$f 		= $this->fields[$field_id];
		$code 	= '';
		
		// set field value
		if(is_array($this->item_keys)) {
			if(!in_array($field_id, $this->item_keys)) {
				$val = (isset($f['def'])) ? $f['def'] : '';
			} else {
				$val = get_post_meta($this->item_id, $field_id, true);	
			}
		} 
        else {
			$val = (isset($f['def'])) ? $f['def'] : '';	
		}
		
		
		### VALUE FILTER - hook to manage already existing values ###
		$val = $this->filter_field_val($field_id, $val);
		

		// default label block
		if(isset($f['label'])) {
			$def_label = '<label>'. $f['label'] .'</label>';
		}
		
        
		// switch by type
		switch($f['type']) {
			
			// text
			case 'text' :
				$ph = (isset($f['placeh'])) ? $f['placeh'] : ''; 
				$code = $def_label. '
				<input type="text" name="'. $field_id .'" value="'. esc_attr((string)$val) .'" placeholder="'. esc_attr($ph) .'" autocomplete="off" />';
				break;
				
                
			// select
			case 'select' :
				$multiple_attr = (isset($f['multiple']) && $f['multiple']) ? 'multiple="multiple"' : '';
				$multiple_name = (isset($f['multiple']) && $f['multiple']) ? '[]' : '';
				
				$code = $def_label. '
				<select data-placeholder="'. esc_attr__('Select an option', 'mg_ml') .' .." name="'. $field_id . $multiple_name.'" class="mg_lcsel_dd" autocomplete="off" '.$multiple_attr.'>';
				
				foreach((array)$f['val'] as $key => $name) {
					if(isset($f['multiple']) && $f['multiple']) {
						$sel = (in_array($key, (array)$val)) ? 'selected="selected"' : '';
					} else {
						$sel = ($key == (string)$val) ? 'selected="selected"' : '';
					}
					
					$code .= '<option value="'.$key.'" '.$sel.'>'. $name .'</option>';	
				}
				
				$code .= '</select>';
				break;
			
                
			// checkbox
			case 'checkbox' :
				$sel = ($val) ? 'checked="checked"' : '';
                
				$code = $def_label. '
				<input type="checkbox" name="'. $field_id .'" value="1" class="mg_lcs_check" '.$sel.' autocomplete="off" />';
				break;
			
                
			// textarea
			case 'textarea' :
				$ph = (isset($f['placeh'])) ? $f['placeh'] : ''; 
                
				$code = $def_label. '
				<textarea name="'. $field_id .'" placeholder="'. esc_attr($ph) .'" autocomplete="off">'. (string)$val .'</textarea>';
				break;
			
                
			// slider
			case 'slider' :
                $respect_limits = (!isset($f['respect_limits']) || !$f['respect_limits']) ? 0 : 1;
                
				$code = $def_label. '
                <input type="number" value="'. (float)$val .'" name="'. esc_attr($field_id) .'" min="'. (float)$f['min_val'] .'" max="'. (float)$f['max_val'] .'" step="'. (float)$f['step'] .'" class="mg_slider_input" autocomplete="off" data-unit="'. esc_attr($f['value']) .'" data-respect-limits="'. $respect_limits .'" />';
				break;
			
                
			// color
			case 'color' :
				$modes = (isset($f['extra_modes']) && is_array($f['extra_modes'])) ? $f['extra_modes'] : array(); // specific modes classes
                
                $code = $def_label. '
                <input type="text" name="'. esc_attr($field_id) .'" value="'. esc_attr($val) .'" class="mg_colpick" data-modes="'. implode(' ', $modes) .'" data-def-color="" autocomplete="off" />';
				break;
			
                
			// value and type
			case 'val_n_type' :
				$code = $def_label. '
				<input type="number" name="'. esc_attr($field_id) .'" value="'. (float)$val .'" min="0" step="1" class="mg_valntype" autocomplete="off" />';
				
				$sel_vh = (get_post_meta($this->item_id, $field_id .'_type', true) == 'vh') ? 'selected="selected"' : '';
                $sel_px = (get_post_meta($this->item_id, $field_id .'_type', true) == 'px') ? 'selected="selected"' : '';
                
				$code .= '
				<select name="'. esc_attr($field_id) .'_type" class="mg_valntype" autocomplete="off">
					<option value="%">%</option>
                    <option value="vh" '.$sel_vh.'>vh</option>
				  	<option value="px" '.$sel_px.'>px</option>
				</select>';
				break;
				
                
			// 4 numbers
			case '4_numbers' :
				if(!is_array($val) || count($val) != 4) {
                    $val = $f['def'];
                }
				
				$maxlen = 'maxlength="'. strlen($f['max_val']) .'"';
				$min = 'min="'. (int)$f['min_val'] .'"';
				$max = 'max="'. (int)$f['max_val'] .'"';
				
				$code = $def_label;
				
				for($a=0; $a<4; $a++) {
					$code .= '<input type="number" name="'. $field_id .'[]" value="'. $val[$a] .'" '.$maxlen.' '.$min.' '.$max.' class="mg_4nums" autocomplete="off" />' ;	
				}
				
				if(isset($f['value'])) {
					$code .= ' <span>'. $f['value'] .'</span>';
				}
				break;	
				
			
			// custom - use callback
			case 'custom' :
                $code = '';
                
                if(method_exists($this, $f['cust_callback'])) {
                    $code = call_user_func(array($this, $f['cust_callback']));        
                }
                elseif(function_exists($f['cust_callback'])) {
                    $code = call_user_func($f['cust_callback'], $this->item_id, $this->item_type);        
                }
				break; 
		}
		
        
        // note
        if(isset($f['note']) && !empty($f['note'])) {
            $code .= '<em>'. $f['note'] .'</em>';
        }
        
		return $code;
	}
		
    
	
	###############################################
	
    
	
	/* custom attribute fields code */
	public function cust_attr_f_code() {
		
		// convert types to implemented types (old versions mistakes..)
		if($this->item_type == 'single_img') {
            $type = 'image';
        }
		else {
            $type = $this->item_type;
        }
		
		
		// if no attributes for this type
		if(!get_option('mg_'. $type .'_opt')) {
			return '<p><em>'. __('No custom attributes created for this type', 'mg_ml') .' ..</em></p>';
		}
		
		$icons = get_option('mg_'. $type .'_opt_icon');
		$code = '';
		
		// compose
		$a = 0;
		foreach(get_option('mg_'. $type .'_opt') as $opt) {
			$val = get_post_meta($this->item_id, 'mg_'. $type .'_'. mg_static::custom_urlencode($opt), true);
			$icon = (isset($icons[$a])) ? '<i class="mg_item_builder_opt_icon '. mg_static::fontawesome_v4_retrocomp($icons[$a]).'"></i> ' : '';
			
			$code .= '
			<div class="mg_imf_field">
				<label>'. $icon . mg_static::wpml_string($type, $opt) .'</label>
				<input type="text" name="mg_'. $type .'_'. mg_static::custom_urlencode($opt) .'" value="'. esc_attr($val) .'" autocomplete="off" />
			</div>';
			
			$a++;
		}	
		
		return $code;
	}
	
	
	
	/* custom icon field's code */
	public function cust_icon_f_code() {
		$icon = get_post_meta($this->item_id, 'mg_cust_icon', true);
		$code = '
		<div class="mg_icon_trigger">
			<label>'. __("Custom icon <em>To be used in secondary and custom overlays</em>", 'mg_ml') .'</label>
			<i class="fa '.$icon.'" title="set category icon" class="mg_displayib"></i>
			<input type="hidden" name="mg_cust_icon" value="'.$icon.'" autocomplete="off" /> 
		</div>';
		
		
		// hidden code for lightbox
		$code .= mg_static::fa_icon_picker_code( __('use default icon', 'mg_ml') );
		return $code;
	}
	
	
	
	/* images picker code */
	public function slider_img_f_code() {
		$vid_to_img = get_post_meta($this->item_id, 'mg_slider_vid', true); 
		if(empty($vid_to_img)) {
            $vid_to_img = array();
        }
		
		$slider_elem = mg_static::existing_sel( get_post_meta($this->item_id, 'mg_slider_img', true), $vid_to_img); 
		
		$code = '
		<div id="mg_gallery_img_wrap">
        	<ul>
				'. mg_static::sel_slider_img_list($slider_elem) .'
            </ul>	
            <br class="mg_clearboth" />
		</div>
        <div class="mg_clearboth"></div>
              
		<div id="mg_img_search_wrap">
        	<input type="text" placeholder="🔎 '. esc_attr__('search images', 'mg_ml') .' .." class="mg_search_field" autocomplete="off" />
		</div>
			  
       	<h4>'. __('Choose images', 'mg_ml') .' <span class="mg_TB mg_upload_img add-new-h2">'. __('Manage Images', 'mg_ml') .'</span></h4>
		<div id="mg_gallery_img_picker"></div>';
		
		return $code;
	}
	
	
	
	/* tracks picker code */
	public function audio_tracks_f_code() {
		$tracks = mg_static::existing_sel( get_post_meta($this->item_id, 'mg_audio_tracks', true));
		
		$code = '
		<div id="mg_audio_tracks_wrap">
			<ul>';
			
			if(is_array($tracks)) {
				foreach($tracks as $track_id) {
					$track_title =  html_entity_decode(get_the_title($track_id), ENT_NOQUOTES, 'UTF-8');

					$code .= '
					<li id="mgtl_'. $track_id .'">
						<input type="hidden" name="mg_audio_tracks[]" value="'. (int)$track_id .'" />
						<div class="mg_audio_icon dashicons-media-audio dashicons"></div>
						<span class="dashicons dashicons-dismiss" title="'. esc_attr__('remove track', 'mg_ml') .'"></span>
						<p title="'. esc_attr($track_title) .'">'. strip_tags($track_title) .'</p>
					</li>';			
				}
			}
			else {
				$code .= '<p>'. __('No tracks selected', 'mg_ml') .' ..</p>';
			}
			
        $code .= '
            </ul>	
			<br class="mg_clearboth" />
		</div>
		<div class="mg_clearboth"></div>
		
		<div id="mg_audio_search_wrap">
		  <input type="text" placeholder="🔎 '. esc_attr__('search tracks', 'mg_ml') .' .." class="mg_search_field"  />
		  <span class="mg_search_btn" title="search"></span>
		</div>
		
		<h4>'. __('Choose tracks', 'mg_ml') .' <span class="mg_TB mg_upload_audio add-new-h2">'. __('Manage Tracks', 'mg_ml') .'</span></h4>
		<div id="mg_audio_tracks_picker"></div>';	
		
		return $code;
	}
	
	
	
	/* video URL */
	public function video_url_f_code() {
		return '
		<label>'. __('Video URL', 'mg_ml') .'</label>
		<input type="text" value="'.get_post_meta($this->item_id, 'mg_video_url', true) .'" name="mg_video_url" /> 
		
		<span class="dashicons dashicons-exit mg_video_src_trigger" title="'. esc_attr__('search in media library', 'mg_ml') .'"></span>
		<em>'. __('Insert Youtube, Vimeo or Dailymotion clean video url. Otherwise select a video from the media library', 'mg_ml') .'</em>';
	} 
	
	
	
	/* background video URL */
	public function video_bg_url_f_code() {
		return '
		<label>'. __('Background video URL', 'mg_ml') .'</label>
        
        <span class="dashicons dashicons-exit mg_video_src_trigger" title="'. esc_attr__('search in media library', 'mg_ml') .'"></span>
		<input type="text" value="'.get_post_meta($this->item_id, 'mg_bg_video_url', true) .'" name="mg_bg_video_url" />
        <em>'. __('Insert or select a video from the media library. <strong>NB:</strong> overrides background image', 'mg_ml') .'</em>';
	} 
	


	////////////////////////////////////////////////////////////////////////////////////////



	/* echo javascript code used by item types */
	public function echo_type_js_code() {
		$t = $this->item_type;
		
		// image picker
		if($t == 'img_gallery' || $t == 'inl_slider') :
			?>
			<script type="text/javascript">
			(function($) { 
	           "use strict";
                
                const lcwp_nonce = '<?php echo wp_create_nonce('lcwp_nonce') ?>';
                
                let mg_img_pp = 26,
                    search_tout = false,
                    $active_slider_box = false;
			
                // reload the selected images to check changes
                window.mg_sel_img_reload = function() {
                    let sel_img = [],	
                        sel_vid = [];	

                    $('#mg_gallery_img_wrap li').each(function() {
                        sel_img.push( $(this).children('.mg_slider_img_field').val() );
                        sel_vid.push( $(this).children('.mg_slider_video_field').val() );
                    });

                    $('#mg_gallery_img_wrap ul').html('<div style="width: 50px; height: 50px;" class="mg_spinner"></div>');

                    var data = {
                        action: 'mg_sel_img_reload',
                        images: sel_img,
                        videos: sel_vid,
                        lcwp_nonce:	lcwp_nonce
                    };
                    $.post(ajaxurl, data, function(response) {
                        $('#mg_gallery_img_wrap ul').html(response);
                    })
                    .fail(function(e) {
                        console.error(e);
                        alert('error reloading images');
                    });	
                };

                // change slider imges picker page
                $(document).off('click', '.mg_img_pick_back, .mg_img_pick_next');
                $(document).on('click', '.mg_img_pick_back, .mg_img_pick_next', function() {
                    const page = $(this).attr('id').substr(4);
                    mg_load_img_picker(page);
                });

                // change images per page
                $(document).off('change', '#mg_img_pick_pp');
                $(document).on('change', '#mg_img_pick_pp', function() {
                    var pp = $(this).val();

                    if( pp.length >= 2 ) {
                        mg_img_pp = (parseInt(pp, 10) < 26 ) ? 26 : parseInt(pp, 10);
                        mg_load_img_picker(1);
                    }
                });

                // on search
                $(document).off('keyup', '#mg_img_search_wrap .mg_search_field');
                $(document).on('keyup', '#mg_img_search_wrap .mg_search_field', function() {
                    if(search_tout) {
                        clearTimeout(search_tout);    
                    }
                    search_tout = setTimeout(function() { 
                        mg_load_img_picker(1);
                    }, 500);
                });

                // load slider images picker
                window.mg_load_img_picker = function(page) {
                    var data = {
                        action: 'mg_img_picker',
                        page: page,
                        per_page: mg_img_pp,
                        mg_search: $('#mg_img_search_wrap .mg_search_field').val(),
                        lcwp_nonce:	lcwp_nonce
                    };

                    $('#mg_gallery_img_picker').html('<div style="width: 50px; height: 50px;" class="mg_spinner"></div>');

                    $.post(ajaxurl, data, function(response) {
                        $('#mg_gallery_img_picker').html(response);
                    })
                    .fail(function(e) {
                        console.error(e);
                        alert('error loading images');
                    });	
                };
                mg_load_img_picker(1);

                // add slider images
                $(document).off('click', '#mg_gallery_img_picker li');
                $(document).on('click', '#mg_gallery_img_picker li', function() {
                    const img_id    = parseInt($(this).children('figure').attr('rel'), 10),
                          img_url   = $(this).children('figure').attr('style');

                    if($('#mg_gallery_img_wrap ul > p').length) {
                        $('#mg_gallery_img_wrap ul').empty();
                    }
                    
                    if($('#mg_gallery_img_wrap li[data-id="'+ img_id +'"]').length) { 
                        return false;    
                    }

                    $('#mg_gallery_img_wrap ul').append(
                    '<li data-id="'+ img_id +'">'+
                        '<input type="hidden" name="mg_slider_img[]" class="mg_slider_img_field" value="'+ img_id +'" />'+
                        '<input type="hidden" name="mg_slider_vid[]" class="mg_slider_video_field" value="" autocomplete="off" />'+

                        '<figure style="'+ img_url +'"></figure>'+
                        '<span class="dashicons dashicons-dismiss" title="<?php esc_attr_e("remove image", 'mg_ml') ?>"></span>'+
                        '<i class="mg_slider_video_off" title="<?php esc_attr_e("set as video slide", 'mg_ml') ?>"></i>'+
                    '</li>');

                    mg_sort();
                });


                // attach video to image slide
                $(document).off('click', '#mg_gallery_img_wrap li i');
                $(document).on('click', '#mg_gallery_img_wrap li i', function() {
                    const $parent = $(this).parent();
                    $active_slider_box = $parent;
                    
                    var val = $parent.find('.mg_slider_video_field').val();

                    var html = `
                    <form id="mg_imb_attach_video_to_slide">
                        <p><?php esc_attr_e('Insert Youtube (http://youtu.be), Vimeo or Dailymotion clean video url. Otherwise select a video from the media library', 'mg_ml') ?></p>
                        <span class="dashicons dashicons-exit mg_video_src_trigger" title="<?php esc_attr_e('search in media library', 'mg_ml') ?>"></span>                        
                        <input type="text" value="${ val }" autocomplete="off" />

                        <input type="button" value="<?php esc_attr_e('Save', 'mg_ml') ?>" class="button-primary" />
                        <input type="button" value="<?php  esc_attr_e("Close", 'mg_ml') ?>" class="button-secondary imgavts_close" />
                        <input type="button" value="<?php  esc_attr_e("Clear", 'mg_ml') ?>" class="button-secondary imgavts_clear" />
                    </form>`;

                    lc_wp_popup_message('modal', html);
                });
                
                // close
                $(document).on('click', '.imgavts_close', function() {
                    lcwpm_close();
                });
                
                // clear
                $(document).on('click', '.imgavts_clear', function() {
                    $(this).parent().find('input[type=text]').val('');
                });
                
                // save
                $(document).on('click', '#mg_imb_attach_video_to_slide .button-primary', function() {
                    const new_val = $('#mg_imb_attach_video_to_slide input[type="text"]').val().trim();
                    
                    if(!new_val) {
                        $active_slider_box.find('.mg_slider_video_field').val('');
                        $active_slider_box.find('i').removeClass('mg_slider_video_on').addClass('mg_slider_video_off');	
                    }
                    else {
                        $active_slider_box.find('.mg_slider_video_field').val(new_val);
                        $active_slider_box.find('i').removeClass('mg_slider_video_off').addClass('mg_slider_video_on');
                    }
                    
                    lcwpm_close();
                });
            })(jQuery);
			</script>
			<?php
			
			
		// tracks upload and select
		elseif($t == 'audio' || $t == 'inl_audio') :
			?>
			<script type="text/javascript">
            (function($) { 
	           "use strict";
                
                const lcwp_nonce = '<?php echo wp_create_nonce('lcwp_nonce') ?>';
                
                let mg_audio_pp = 26,
                    search_tout = false;

                // reload the selected tracks to refresh their titles
                window.mg_sel_tracks_reload = function() {
                    let sel_tracks = [];	

                    $('#mg_audio_tracks_wrap li').each(function() {
                        var track_id = parseInt($(this).children('input').val(), 10);
                        sel_tracks.push(track_id);
                    });

                    $('#mg_audio_tracks_wrap ul').html('<div style="width: 50px; height: 50px;" class="mg_spinner"></div>');

                    var data = {
                        action: 'mg_sel_audio_reload',
                        tracks: sel_tracks,
                        lcwp_nonce:	lcwp_nonce
                    };

                    $.post(ajaxurl, data, function(response) {
                        $('#mg_audio_tracks_wrap ul').html(response);
                    })
                    .fail(function(e) {
                        console.error(e);
                        alert('error reloading tracks');
                    });	
                };

                // change tracks picker page
                $(document).off('click', '.mg_audio_pick_back, .mg_audio_pick_next');
                $(document).on('click', '.mg_audio_pick_back, .mg_audio_pick_next', function() {
                    const page = parseInt($(this).attr('id').substr(4), 10);
                    mg_load_audio_picker(page);
                });

                // change tracks per page
                $(document).off('change', '#mg_audio_pick_pp');
                $(document).on('change', '#mg_audio_pick_pp', function() {
                    var pp = parseInt($(this).val(), 10);

                    if( pp.length >= 2 ) {
                        mg_audio_pp = (parseInt(pp, 10) < 26) ? 26 : parseInt(pp, 10); 
                        mg_load_audio_picker(1);
                    }
                });

                // on search
                $(document).off('keyup', '#mg_audio_search_wrap .mg_search_field');
                $(document).on('keyup', '#mg_audio_search_wrap .mg_search_field', function() {
                    if(search_tout) {
                        clearTimeout(search_tout);    
                    }
                    search_tout = setTimeout(function() { 
                        mg_load_audio_picker(1);
                    }, 500);
                });

                // load audio tracks picker
                window.mg_load_audio_picker = function (page) {
                    var data = {
                        action      : 'mg_audio_picker',
                        page        : page,
                        per_page    : mg_audio_pp,
                        mg_search   : $('#mg_audio_search_wrap .mg_search_field').val(),
                        lcwp_nonce  :	lcwp_nonce
                    };

                    $('#mg_audio_tracks_picker').html('<div style="width: 50px; height: 50px;" class="mg_spinner"></div>');

                    $.post(ajaxurl, data, function(response) {
                        $('#mg_audio_tracks_picker').html(response);
                    })
                    .fail(function(e) {
                        console.error(e);
                        alert('error loading tracks');
                    });	
                };
                mg_load_audio_picker(1);

                // add audio track
                $(document).off('click', '#mg_audio_tracks_picker li');
                $(document).on('click', '#mg_audio_tracks_picker li', function() {
                    const track_id  = parseInt($(this).attr('id').substr(5), 10),
                          track_tit = $(this).children('p').text();	

                    if($('#mg_audio_tracks_wrap ul > p').length) {
                        $('#mg_audio_tracks_wrap ul').empty();
                    }

                    if($('#mg_audio_tracks_wrap li[data-id="'+ track_id +'"]').length) { 
                        return false;    
                    }
                    
                    $('#mg_audio_tracks_wrap ul').append(
                    '<li data-id="'+ track_id +'">'+
                        '<input type="hidden" name="mg_audio_tracks[]" value="'+ track_id +'" />'+
                        '<div class="mg_audio_icon dashicons-media-audio dashicons"></div>'+
                        '<span class="dashicons dashicons-dismiss" title="<?php esc_attr_e("remove track", 'mg_ml') ?>"></span>'+
                        '<p>'+ track_tit +'</p>'+
                    '</li>');

                    mg_sort();
                });
            })(jQuery);
			</script>
			<?php	
		
		
		// post contents - CPT terms async load
		elseif($t == 'post_contents') : 
			?>
			<script type="text/javascript">
			(function($) { 
	           "use strict";
                
                const lcwp_nonce = '<?php echo wp_create_nonce('lcwp_nonce') ?>';
                let mg_is_acting = false;
                
                
                $(document).off('change', '.mg_imf_mg_cpt_source select');
                $(document).on('change', '.mg_imf_mg_cpt_source select', function() {
                    if(mg_is_acting) {
                        return false;    
                    }
                    mg_is_acting = true

                    $('.mg_imf_mg_cpt_tax_term select').parent().replaceWith('<div style="width: 23px; height: 23px;" class="mg_spinner mg_spinner_inline"></div>');

                    var data = {
                        action      : 'mg_sel_cpt_source',
                        cpt         : $(this).val(),
                        lcwp_nonce  : lcwp_nonce
                    };

                    $.post(ajaxurl, data, function(response) {		
                        $('.mg_imf_mg_cpt_tax_term .mg_spinner').replaceWith(response);
                        
                        // LC select
                        new lc_select('select[name="mg_cpt_tax_term"]', {
                            wrap_width : '90%',
                            addit_classes : ['lcslt-lcwp'],
                        });
                    })
                    .fail(function(e) {
                        console.error(e);
                        alert('error retrieving post types')
                    })
                    .always(function() {
                         mg_is_acting = false;        
                    });		
                });
            })(jQuery);
			</script>
			<?php
		endif;	
        
        
        // common code
        ?>
        <script type="text/javascript">
        (function($) { 
	       "use strict";
     
            $(document).off("click", '.mg_video_src_trigger');
            $(document).on("click", '.mg_video_src_trigger', function (e) {
                e.preventDefault();

                var wp_selector = wp.media({
                    title   : "<?php esc_attr_e('Wordpress Video Management', 'mg_ml') ?>",
                    button  : { text: '<?php esc_attr_e('Select') ?>' },
                    library : { type : 'video'},
                    multiple: false
                })
                .on('select', function() {
                    var selection = wp_selector.state().get('selection').first().toJSON();

                    var itemurl = selection.url;
                    var video_pattern = /(^.*\.mp4|m4v|webm|ogv|wmv|flv*)/gi;

                    if(itemurl.match(video_pattern) ) {
                      $('.mg_video_src_trigger').siblings('input[type=text]').val(itemurl);
                    }
                    else {
                        alert('<?php esc_attr_e('Please select a valid video file for the WP player. Supported extensions:', 'mg_ml') ?> mp4, m4v, webm, ogv, wmv, flv');
                    }
                })
                .open();
            });
        })(jQuery);
        </script>    
        <?php
		return true;
	}


	
	//////////////////////////////////////////////////////
	
	
                                                       
	/* filter values for specific fields */
	private function filter_field_val($field_id, $val) {
		
		// lightbox layout - replace SIDE with side_tripartite
		if($field_id == 'mg_layout' && $val == 'side') {
			$val = 'side_tripartite';
		}
		
		return $val;	
	}
}

