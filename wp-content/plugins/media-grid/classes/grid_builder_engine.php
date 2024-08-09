<?php
// showing grid builder panels for a specific grid

class mg_grid_builder_engine {
	private $grid_id;
	private $pt_n_tax = array(); // array containing post types + taxonomies to be used in dropdowns
	
	
	// grid parameters template
	private $grid_params = array(
		'composition'		=> 'manual', // (string) manual/dynamic
        'filtered_per_page' => 0, // (int) how many items show at once while filtering - 0 == no pagination
		
		'manual_items'		=> array( // (array) contains v5 parameters
			/*
			each item is an array(
				id  = manual mode => item's id || paginator || spacer - dynamic mode => item || spacer
				w   = width
				h   = height
				m_w = mobile width
				m_h = mobile height
				
				vis = empty || mobile_hidden || desktop_hidden - ONLY for spacer
			)
			*/
		), 

		'dynamic_structure' => array(), // read manual_items note 
		'dynamic_src'	 	=> array( // sources to dynamically populate the grid to dynamically create the grid
			/*
			each item is an array(
				pt_n_tax	= post type and taxonomy ( pt|||tax )
				term		= specific term - could be empty or the term ID
				mg_type		= specific MG item type - only if post type is mg_items - could be empty or the type name
			)
			*/
		),
		'dynamic_repeat'	=> false, 	// (bool) repeat structure? useful to create uniform layouts
		'dynamic_limit'		=> 20, 		// (int) how many items to create?
		'dynamic_per_page' 	=> 0, 		// (int) how many items to show per page? 0 == no pagination
		'dynamic_orderby'	=> 'date', 	// (string) how to fetch posts? - date | title | modified
		'dynamic_random'	=> false, 	// (bool) whether to randomize featched posts or not
		'dynamic_force_links'=> false, 	// (bool) whether to force links for post type items
		'dynamic_auto_h_fb'	=> array(	// (array) desktop height to use when item has AUTO height but MG item doesn't support it
			'h' 	=> '1_4', 
			'm_h'	=> '1_3'	
		)	
	);
	
	
	
	/*
	 * Initialize class loading grid ID, overlay manager class instance and eventually its managed attributes
	 */
	public function __construct($grid_id) {
		$this->grid_id = $grid_id;
		$this->pt_n_tax = mg_static::get_cpt_with_tax(false);
		
		// retrieve grid data
		if(!empty($grid_id)) {
			$grid_data = mg_static::get_grid_data($this->grid_id);
            
			// setup params
			if(is_array($grid_data)) {
				
				// v5 retrocompatibility
				if(!isset($grid_data['composition']) && isset($grid_data['items'])) {
					$this->grid_params['manual_items'] = $grid_data['items'];	
				}
				
				// v6 structure existing - override values
				else {
					foreach($grid_data as $key => $val) {
						$this->grid_params[$key] = $val;	
					}
				}
			}
		}
	}
	
	
	
	
	
	public function side_block() {
		$gp = $this->grid_params;
		$dynamic_opts_vis     = ($gp['composition'] == 'dynamic') ? '' : 'mg_displaynone';
		$manual_opts_vis      = ($gp['composition'] == 'dynamic') ? 'mg_displaynone' : '';
		$filtered_per_page_vis= (get_option('mg_monopage_filter')) ? 'mg_displaynone' : '';
        
		// repeast dynamic structure - limit field visibility
		$dyn_limit_vis = ($gp['composition'] != 'dynamic' || !$gp['dynamic_repeat']) ? 'mg_displaynone' : '';  
		
		$code = '
		<div class="postbox">
			<h3 class="hndle">'. __('Grid Options', 'mg_ml') .'</h3> 
			<div class="inside mg_builder_side_opt">
				<div>
					<label>'. __('Grid composition', 'mg_ml') .'</label>
					<select name="mg_grid_composition" class="mg_lcsel_dd">
						<option value="manual">'. esc_html__('manual', 'mg_ml') .'</option>
						<option value="dynamic" '. selected($gp['composition'], 'dynamic', false) .'>'. esc_html__('dynamic', 'mg_ml').'</option>
					</select>
			  	</div>  
                <div class="mg_dynamic_grid_opt '. $filtered_per_page_vis .'">
					<label>'. __('Filtered items per page? <em>(zero for unlimited)</em>', 'mg_ml') .'</label>
					<input type="number" name="mg_filtered_per_page" min="0" step="1" max="100" value="'. (int)$gp['filtered_per_page'] .'" autocomplete="off" />
			  	</div>
			  
			  	<div class="mg_manual_grid_opt '.$manual_opts_vis.'">
					<label>'. __("Bulk items width", 'mg_ml') .'</label>
					<select name="mg_bulk_w" id="mg_bulk_w" autocomplete="off">';
  
					  foreach(mg_static::simpler_sizes_array(mg_static::item_sizes()) as $size => $name) {
						  $code .= '<option value="'.$size.'">'.$name.'</option>';
					  }
					
					$code .= '
					</select>
					<select name="mg_bulk_mw" id="mg_bulk_mw" autocomplete="off" class="mg_displaynone">';
		  
					  foreach(mg_static::simpler_sizes_array(mg_static::mobile_sizes()) as $size => $name) {
						  $code .= '<option value="'.$size.'">'.$name.'</option>';
					  }
					
					$code .= '
					</select>
					<input type="button" name="bulk_size" value="'. esc_attr__('Set', 'mg_ml') .'" class="button-secondary" id="mg_bulk_w_btn" />
			  	</div>
				<div class="mg_manual_grid_opt '.$manual_opts_vis.'">
					<label>'. __("Bulk items height", 'mg_ml') .'</label>
					<select name="mg_bulk_h" id="mg_bulk_h" autocomplete="off">';
		  
					  foreach(mg_static::simpler_sizes_array(mg_static::item_sizes()) as $size => $name) {
						  $code .= '<option value="'. $size .'">'. $name .'</option>';
					  }
					 
						$code .= '
						<option value="auto">'. __('auto', 'mg_ml') .'</option>
					</select>
					<select name="mg_bulk_mh" id="mg_bulk_mh" autocomplete="off" class="mg_displaynone">';
		  
					  foreach(mg_static::simpler_sizes_array(mg_static::mobile_sizes()) as $size => $name) {
						  $code .= '<option value="'. $size .'">'. $name .'</option>';
					  }
					  
					  $code .= '
					  <option value="auto">'. __('auto', 'mg_ml') .'</option>
					</select>
					<input type="button" name="bulk_size" value="'. esc_attr__('Set', 'mg_ml') .'" class="button-secondary" id="mg_bulk_h_btn" />
			  	</div>
				
				<div class="mg_dynamic_grid_opt '.$dynamic_opts_vis.'">
					<label>'. __('Repeated structure?', 'mg_ml') .'</label>
					<input type="checkbox" name="mg_dynamic_repeat" value="1" '. checked($gp['dynamic_repeat'], '1', false) .' autocomplete="off" class="mg_lcs_check" />
			  	</div>
			  	<div class="mg_dynamic_grid_opt '.$dyn_limit_vis.'">
					<label>'. __('How many posts to fetch?', 'mg_ml') .'</label>
					<input type="number" name="mg_dynamic_limit" min="1" step="1" max="100" value="'. (int)$gp['dynamic_limit'] .'" autocomplete="off" />
			  	</div>
				<div class="mg_dynamic_grid_opt '.$dynamic_opts_vis.'">
					<label>'. __('Items per page? <em>(zero for unlimited)</em>', 'mg_ml') .'</label>
					<input type="number" name="mg_dynamic_per_page" min="0" step="1" max="100" value="'. (int)$gp['dynamic_per_page'] .'" autocomplete="off" />
			  	</div>
				<div class="mg_dynamic_grid_opt '.$dynamic_opts_vis.'">
					<label>'. __('How to sort items?', 'mg_ml') .'</label>
					<select name="mg_dynamic_orderby" class="mg_lcsel_dd">
						
						<option value="date">'. __('by creation date (old to new)', 'mg_ml') .'</option>
						<option value="date_desc" '. selected($gp['dynamic_orderby'], 'date_desc', false) .'>'. __('by creation date (new to old)', 'mg_ml') .'</option>
						
						<option value="modified" '. selected($gp['dynamic_orderby'], 'modified', false) .'>'. __('by modification date (old to new)', 'mg_ml').'</option>
						<option value="modified_desc" '. selected($gp['dynamic_orderby'], 'modified_desc', false) .'>'. __('by modification date (new to old)', 'mg_ml').'</option>
						
						<option value="title" '. selected($gp['dynamic_orderby'], 'title', false) .'>'. __('aphabetically (A to Z)', 'mg_ml').'</option>
						<option value="title_desc" '. selected($gp['dynamic_orderby'], 'title_desc', false) .'>'. __('aphabetically (Z to A)', 'mg_ml').'</option>
					</select>
			  	</div>
				<div class="mg_dynamic_grid_opt '.$dynamic_opts_vis.'">
					<label>'. __('Display fetched posts randomly?', 'mg_ml') .'</label>
					<input type="checkbox" name="mg_dynamic_random" value="1" '. checked($gp['dynamic_random'], '1', false) .' autocomplete="off" class="mg_lcs_check" />
			  	</div>
				<div class="mg_dynamic_grid_opt '.$dynamic_opts_vis.'">
					<label>'. __('Force links where available?', 'mg_ml') .' <i class="fa fa-question-circle" title="'. esc_attr__("Check to turn every item taken from post types into a direct link", 'mg_ml') .'"></i></label>
					<input type="checkbox" name="mg_dynamic_force_links" value="1" '. checked($gp['dynamic_force_links'], '1', false) .' autocomplete="off" class="mg_lcs_check" />
			  	</div>
				<div class="mg_dynamic_grid_opt mg_dynamic_auto_h_fb_wrap '.$dynamic_opts_vis.'">
					<label>'. __('"auto" height fallback', 'mg_ml') .'
						<i class="fa fa-question-circle" title="'. esc_attr__("Height to use when item has AUTO height but Media Grid item doesn't support it", 'mg_ml') .'"></i>
					</label>
					
					<div style="display: inline-block; width: 49%;">
						<span class="dashicons dashicons-laptop" title="'. esc_attr__('on desktop', 'mg_ml') .'" style="padding: 5px 2px 0 0; color: #888;"></span>
						<select name="dynamic_auto_h_fb" autocomplete="off">';
			  
						  foreach(mg_static::simpler_sizes_array(mg_static::item_sizes()) as $size => $name) {
							  $code .= '<option value="'. $size .'" '.selected($gp['dynamic_auto_h_fb']['h'], $size, false).'>'. $name .'</option>';
						  }
						 
							$code .= '
						</select>
					</div>
					<div style="display: inline-block; width: 49%;">
						<span class="dashicons dashicons-smartphone" title="'. esc_attr__('on mobile', 'mg_ml') .'" style="padding: 5px 2px 0 0; color: #888;"></span>
						<select name="dynamic_auto_mh_fb" autocomplete="off">';
	
						  foreach(mg_static::simpler_sizes_array(mg_static::mobile_sizes()) as $size => $name) {
							  $code .= '<option value="'. $size .'" '.selected($gp['dynamic_auto_h_fb']['m_h'], $size, false).'>'. $name .'</option>';
						  }
						  
						  $code .= '
						</select>
					</div>
			  	</div>
			</div>
		</div>';
		
		
		$code .= '
		<div id="mg_grid_main_btn_wrap" class="postbox">
			<div class="inside">
				<span>
					<input type="button" name="mg_save_grid" value="'. esc_attr__('Save grid', 'mg_ml') .'" class="button-primary" />
				</span>';
				
				if(get_option('mg_preview_pag')) {
					$code .= '	
					<span>
						<input type="button" id="preview_grid" value="'. esc_attr__('Preview', 'mg_ml') .'" class="button-secondary" data-pv-url="'. esc_attr(get_permalink(get_option('mg_preview_pag'))) .'" />
					</span>';
				}
				
			$code .= '	
			</div>
		</div>';
		
		return $code;  
	}
	


	public function main_block($forced_comp = false) {
		$to_use = (!empty($forced_comp)) ? $forced_comp : $this->grid_params['composition']; 
		return ($to_use == 'manual') ? $this->manual_builder_wizard() : $this->dynamic_builder_wizard();
	}
	
	
	
	#######################################################
	
	

	// returns main block's code in case of manual grid composition
	private function manual_builder_wizard() {
		$gp = $this->grid_params;
		$code = '';
		
		if( (float)substr(get_bloginfo('version'), 0, 3) >= 3.8) {
			$code .= '<span id="mg_expand_builder" title="'. __('expand builder', 'mg_ml') .'"></span>';
		}
		
		// default item's picker
		$items_picker = $this->items_picker_code();
		$next_items_vis = ($items_picker['tot_pages'] == 1) ? 'mg_displaynone' : ''; 
		
		$code .= '
		<div class="postbox">
			<h3 class="hndle">'. __('Add Grid Items', 'mg_ml') .'</h3>
			
			<div class="inside">
			  <table id="mg_mgb_picker_wrap" class="widefat mg_builder_items_search">
				
				<tr>
					<td>
						<label>'. __('Items source', 'mg_ml') .'</label>
						<select data-placeholder="'. esc_attr__('Select source', 'mg_ml') .' .." name="mg_items_src" class="mg_lcsel_dd" autocomplete="off">
							<option value="mg_items|||mg_item_categories">'. esc_html__('Media Grid items - Item categories', 'mg_ml')  .'</option>
		                    <option value="attachment|||">'. esc_html__('WP Media Library', 'mg_ml')  .'</option>';					
        
                            // MG-FILTER - allow grid builder injection for custom taxonomies related to WP attachments - must return array(taxonomy_slug => name)            
                            $attach_cust_tax = (array)apply_filters('mg_builder_attach_cust_tax', array());   

                            foreach($attach_cust_tax as $val => $name) {
                                $true_val = 'attachment|||'. $val;
                                $true_name = esc_html__('WP Media Library', 'mg_ml') .' - '. $name;

                                $code .= '<option value="'. $true_val .'" '.selected($src['pt_n_tax'], $true_val, false).'>'. $true_name .'</option>';	                
                            }

                            // classic post type + tax
							foreach($this->pt_n_tax as $val => $name) {
								$code .= '<option value="'. $val .'">'. $name .'</option>';	
							}
							
						$code .= '
						</select>
					</td>
					<td id="mg_items_src_tax_wrap">
						<label>'. __('Specific term association?', 'mg_ml') .'</label>
						'. mg_static::get_taxonomy_terms('mg_items|||mg_item_categories', 'html') .'
					</td>
					<td id="mg_items_type_wrap">
						<label>'. __('Specific item type?', 'mg_ml') .'</label>
						<select data-placeholder="'. esc_attr__('Select type', 'mg_ml') .' .." name="mg_items_type" class="mg_lcsel_dd" autocomplete="off">
							<option value="">'. esc_html__('Any item type', 'mg_ml')  .'</option>';
							
							foreach(mg_static::item_types() as $id => $name) {
								if($id == 'spacer') {continue;}
								$code .= '<option value="'.$id.'">'.$name.'</option>';
							}

						$code .= '
						</select>
					</td>
					<td>
						<label>'. __('Search items', 'mg_ml') .'</label>
						<input type="text" name="mg_gb_item_search" id="mg_gb_item_search" style="width: 75%; padding-right: 28px;" autocomplete="off" />
						
						<i class="mg_gbis_mag" title="'. __('search', 'mg_ml') .'"></i>
						<i class="mg_gbis_del" title="'. __('cancel', 'mg_ml') .'"></i>
					</td>
				</tr>
					
				  
				<tr><td style="padding: 7px !important;"><hr/></td></tr>   
							
				<tr>
					<td colspan="4" style="padding-left: 15px; padding-right: 5px;">
						<ul id="mg_gb_item_picker">
							'. $items_picker['code'] .'
						</ul>
				  	</td>
				</tr>
				<tr>
					<td style="width: 25%;">
						<input type="button" name="mgb_ip_prev" value="&laquo; '. esc_attr__('Previous page', 'mg_ml') .'" class="button-secondary" style="display: none;" />
					</td>
					<td colspan="2" style="text-align: center; width: 50%;">
						<em class="mbb_ip_page_counter">
							'. __('page', 'mg_ml') .' <span>1</span> '. __('of', 'mg_ml') .' <span>'. $items_picker['tot_pages'] .'</span>
						</em> - 
						<input type="text" name="mgb_ip_limit" value="16" size="3" style="text-align: center;" />
						<em>'. __('results per page', 'mg_ml') .'</em>
					</td>
					<td style="text-align: right; width: 25%;">
						<input type="button" name="mgb_ip_next" value="'. esc_attr__('Next page', 'mg_ml') .' &raquo;" class="button-secondary '. $next_items_vis .'" />
					</td>
				</tr>
			</table>  
		  </div>  
		</div>
		
		
		<div class="postbox">
		  <h3 class="hndle">
			'. __('Grid Preview', 'mg_ml') .'
			<a href="javascript:void(0)" id="mg_mobile_view_toggle">'. __('mobile view', 'mg_ml') .' <span>'. __('OFF', 'mg_ml') .'</span></a>
			<a href="javascript:void(0)" id="mg_easy_sorting_toggle">'. __('easy sorting', 'mg_ml') .' <span>'. __('OFF', 'mg_ml') .'</span></a>
			
			<a href="javascript:void(0)" id="mg_add_spacer">'. __('add spacer', 'mg_ml') .'</a>
			<a href="javascript:void(0)" id="mg_add_paginator">'. __('add pagination block', 'mg_ml') .'</a>
		  </h3>
		  
		  <div class="inside">
			<div id="mg_visual_builder_wrap" class="mg_desktop_builder">
				<ul id="mg_sortable">';

					if(is_array($gp['manual_items']) && !empty($gp['manual_items'])) {
						foreach($gp['manual_items'] as $k => $item) {
						
							// paginator block
							if($item['id'] == 'paginator') {
								$code .= $this->paginator_code();
							  	continue;  
							}

							// normal execution
							if($item['id'] != 'spacer' && get_post_status($item['id']) != 'publish') { // be sure it is published
								continue;
							} 
							
							// spacer retrocompatibility
							if(get_post_meta($item['id'], 'mg_main_type', true) == 'spacer') {
								$item['vis'] = get_post_meta($item['id'], 'mg_spacer_vis', true);	
								$item['id'] = 'spacer';
							}
	
							$code .= $this->item_code($item['id'], $item);	
						}
					}
	  
		  $code .= '</ul>'. // IMPORTANT - use URL without spaces to allow CSS :empty
			 '</div>  
		  </div>
		</div>';
		
		return $code;	
	}


	
	/* 
	 * Given source and query params, returns code for item's picker and total pages (manual mode)
	 * @param (string) $pt_n_tax - string containing post type and taxonomy - by default is MG items 
	 * @param (string) $term - term to filter queried posts 
	 * @param (int) $limit - posts per page
	 * @param (int) $page - query offset
	 * @param (string) $search - to perform custom searches
	 * @param (int) $mg_item_type - MG items type to furtherly refine search
	 *
	 * @return (array) ('items' => 'html code', 'tot_pages' => INT)
	 */ 
	public function items_picker_code($pt_n_tax = 'mg_items|||mg_item_categories', $term = '', $limit = 16, $page = 1, $search = '', $mg_item_type = '') {
		list($pt, $tax) = explode('|||', $pt_n_tax);
		
		// sanitize pagination vars
		if((int)$limit < 16 || (int)$limit > 70) {$limit = 16;}
		$offset = ((int)$page < 2) ? 0 : ((int)$page - 1) * (int)$limit;  
		
		// query
		$args = array(
			'post_type' 		=> $pt,  
			'post_status'	 	=> 'publish', 
			'posts_per_page' 	=> $limit,
			'offset' 			=> $offset,
			'meta_query' 		=> array(),
			'ignore_sticky_posts'=> true,
			'suppress_filters'	=> true
		);
		
		if(!empty($search)) {
			$args['s'] = $search;	
		}
        
        
        // special case for attachments
        if($pt == 'attachment') {
            $args['post_status'] = 'inherit';
            $args['post_mime_type'] = 'image/jpeg,image/gif,image/jpg,image/png';
        }
		
		
		// not MG item? - require thumbnail 
		if($pt != 'mg_items' && $pt != 'attachment') {
			$args['meta_query'][] = array(
				'key' => '_thumbnail_id'
			);
		}
		
		
		// term filter
		if(!empty($term)) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $tax,
					'field' => 'id',
					'terms' => array($term),
					'include_children' => true
				)
			);	
		}
		
		
		// MG items - item type
		if($pt == 'mg_items' && !empty($mg_item_type)) {
			$args['meta_query'][] = array(
				'key' 	=> 'mg_main_type',
				'value' => $mg_item_type
			);
		}
		
		$query = new WP_query($args);
		$results = $query->posts;
		$tot_pag = $query->max_num_pages;

		// no results - stop here
		if(!$results || !count($results)) {
			return array(
				'code' 		=> '<h3 class="mg_aligncent">.. '. __('no elements found', 'mg_ml') .' ..</h3>',
				'tot_pages'	=> 0
			);	
		}
		
		$code = '';
		foreach($results as $post) {
			
			// item type - "post" or specific item's type 
			switch($pt) {
                case 'mg_items' :
                    $type = get_post_meta($post->ID, 'mg_main_type', true);
                    break;
                    
                case 'attachment' : 
            }
            
            $type = ($pt == 'mg_items') ? get_post_meta($post->ID, 'mg_main_type', true) : 'post';
            
			if($pt == 'product') { // special case - woocommerce
                $type = 'woocom';
            } 

			
			// special bg
			if(in_array($type, array('inl_slider', 'inl_video', 'inl_text', 'post_contents', 'spacer'))) {
				$bg = 'background: url('. MG_URL .'/img/type_icons/'. $type .'.png) no-repeat center center #7fc241;';	
			} 
			else {
				$thumb_id = ($pt == 'attachment') ? $post->ID : get_post_thumbnail_id($post->ID);
                
                $thumb_data = wp_get_attachment_image_src($thumb_id, 'medium');
				$bg = 'background-image: url('.$thumb_data[0].');';		
			}
			
			// preview icon
			if(in_array($type, array('simple_img', 'inl_slider', 'inl_video', 'link', 'inl_text', 'spacer'))) {
				$preview = '';	
			} else {
				$preview = '<a class="mgi_preview" href="'. site_url() .'?mgi_='. $post->ID .'" title="'. __('preview item', 'mg_ml') .'" target="_blank"></a>';	
			}
			
			// compose
            $title = mg_static::item_types($type);
            
            if($pt == 'attachment') { // special case - wp media library
                $type = 'simple_img';
                $title = esc_attr__('Media');
            } 
            
			$code .= '
			<li style="'. $bg .'" title="'. esc_attr__('add to grid', 'mg_ml') .'" rel="'. $post->ID .'">
				<p>
					<i class="mgi_type mgi_'. $type .'" title="'. $title .'"></i>
					<a class="mgi_edit" href="'. get_admin_url() .'post.php?post='. $post->ID .'&action=edit" title="'. esc_attr__('edit item', 'mg_ml') .'" target="_blank"></a>
					'. $preview .'
				</p>
	
				<div title="'. esc_attr($post->post_title) .'">
					'. $post->post_title .'
				</div>
			</li>';
		}
		
		return array(
			'code' 		=> $code,
			'tot_pages'	=> $tot_pag
		);	
	}
	
	
	
	// paginator's builder structure
	public function paginator_code() {
		$code = '
		<li id="box_'.uniqid().'" class="mg_box mg_paginator_type">
			<input type="hidden" value="paginator" name="grid_items[]" />
		  
			<input type="hidden" value="0" name="items_w[]" class="select_w" />
			<input type="hidden" value="0" name="items_h[]" class="select_h" />
			<input type="hidden" value="0" name="items_mobile_w[]" class="select_m_w" />
			<input type="hidden" value="0" name="items_mobile_h[]" class="select_m_h" />
			
			<div class="handler">
				<div title="'. __('remove paginator', 'mg_ml') .'" class="del_item"></div>
				<h3>
					<img src="'.MG_URL. '/img/type_icons/paginator.png" height="19" width="19" class="thumb" alt="" />
					'. __('Pagination block', 'mg_ml') .'
                    <span class="mg_item_num" title="'. esc_attr__('item order', 'mg_ml') .'"></span>
				</h3>
			</div>
		</li>';
		
		return str_replace(array("\r", "\n", "\t", "\v"), '', $code); // remove space for JS usage
	}
	


	#######################################################



	// returns main block's code in case of dynamic grid composition
	private function dynamic_builder_wizard() {
		$gp = $this->grid_params;
		$code = '';

		if( (float)substr(get_bloginfo('version'), 0, 3) >= 3.8) {
			$code .= '<span id="mg_expand_builder" title="'. __('expand builder', 'mg_ml') .'"></span>';
		}
		
		$code .= '
		<div class="postbox">
			<h3 class="hndle">
				'. __('Item Sources', 'mg_ml') .'
				<a href="javascript:void(0)" id="mg_add_source">'. __('add source', 'mg_ml') .'</a>
			</h3>
			
			<div class="inside">
				<table id="mg_dgb_src_list" class="widefat mg_builder_items_search">';
				
					if(!empty($gp['dynamic_src'])) {
						$code .= '	
						<thead>
							<tr>
								<th>'. __('Post type and taxonomy', 'mg_ml') .'</th>
								<th>'. __('Specific term association?', 'mg_ml') .'</th>
								<th>'. __('Specific item type?', 'mg_ml') .'</th>
								<th></th>
							</tr>
						</thead>
						<tbody>';
						
							foreach($gp['dynamic_src'] as $src) {
								$code .= $this->dynamic_src_code($src);
							}
					
						$code .= '</tbody>';
					}
					
				$code .= '</table>'. // IMPORTANT - use URL without spaces to allow CSS :empty
			'</div>  
		</div>
		
		<div class="postbox">
		  <h3 class="hndle">
			'. __('Grid Structure', 'mg_ml') .'
			<a href="javascript:void(0)" id="mg_mobile_view_toggle">'. __('mobile view', 'mg_ml') .' <span>'. __('OFF', 'mg_ml') .'</span></a>
			<a href="javascript:void(0)" id="mg_easy_sorting_toggle">'. __('easy sorting', 'mg_ml') .' <span>'. __('OFF', 'mg_ml') .'</span></a>
			
			<a href="javascript:void(0)" id="mg_add_spacer">'. __('add spacer', 'mg_ml') .'</a>
			<a href="javascript:void(0)" id="mg_add_block">'. __('add block', 'mg_ml') .'</a>
		  </h3>
		  
		  <div class="inside">
			<div id="mg_visual_builder_wrap" class="mg_desktop_builder">
				<ul id="mg_sortable">';
					
					if(is_array($gp['dynamic_structure']) && !empty($gp['dynamic_structure'])) {
						foreach($gp['dynamic_structure'] as $k => $item) {
							
							$code .= $this->item_code($item['id'], $item);	
						}
					}
	  
		  $code .= '</ul>'. // IMPORTANT - use URL without spaces to allow CSS :empty
			 '</div>  
		  </div>
		</div>';
		
		return $code;	
	}



	// dynamic grid source code
	public function dynamic_src_code($src = array()) {
		if(empty($src)) {
			$src = array(
				'pt_n_tax' 	=> 'mg_items|||mg_item_categories',
				'term'		=> '',
				'mg_type'	=> ''
			);	
		}
		
		$code = '
		<tr>
			<td>
				<select data-placeholder="'. esc_attr__('Select source', 'mg_ml') .' .." name="mg_items_src" class="mg_lcsel_dd" autocomplete="off">
					<option value="mg_items|||mg_item_categories">'. esc_html__('Media Grid items - Item categories', 'mg_ml')  .'</option>
                    <option value="attachment|||" '.selected($src['pt_n_tax'], "attachment|||", false).'>'. esc_html__('WP Media Library', 'mg_ml') .'</option>';
                    
                    // MG-FILTER - allow grid builder injection for custom taxonomies related to WP attachments - must return array(taxonomy_slug => name)            
                    $attach_cust_tax = (array)apply_filters('mg_builder_attach_cust_tax', array());   
        
                    foreach($attach_cust_tax as $val => $name) {
                        $true_val = 'attachment|||'. $val;
                        $true_name = esc_html__('WP Media Library', 'mg_ml') .' - '. $name;
                            
                        $code .= '<option value="'. $true_val .'" '.selected($src['pt_n_tax'], $true_val, false).'>'. $true_name .'</option>';	                
                    }
        
                    // classic post type + tax
					foreach($this->pt_n_tax as $val => $name) {
						$code .= '<option value="'. $val .'" '.selected($src['pt_n_tax'], $val, false).'>'. $name .'</option>';	
					}
					
				$code .= '
				</select>
			</td>
			<td class="mg_items_src_tax_wrap">
				'. mg_static::get_taxonomy_terms($src['pt_n_tax'], 'html', $src['term']) .'
			</td>
			<td class="mg_items_type_wrap">
				<select data-placeholder="'. esc_attr__('Select type', 'mg_ml') .' .." name="mg_items_type" class="mg_lcsel_dd" autocomplete="off">
					<option value="">'. esc_html__('Any item type', 'mg_ml')  .'</option>';
					
					foreach(mg_static::item_types() as $id => $name) {
						if($id == 'spacer') {
                            continue;
                        }
						$code .= '<option value="'.$id.'" '.selected($src['mg_type'], $id, false).'>'.$name.'</option>';
					}

				$code .= '
				</select>
			</td>
			<td>
				<input type="button" name="mg_dgb_del_src" value="'. esc_attr__('remove', 'mg_ml') .'" class="button-secondary" />
			</td>
		</tr>';
		
		return str_replace(array("\r", "\n", "\t", "\v"), '', $code); // remove space for JS usage	
	}
	



	#######################################################
	
	
	
	/* 
	 * Returns item block to be inserted in manual or dynamic grid 
	 * @param (int/string) $item_id - any post id to fetch data from OR "spacer" OR "item" for dynamic grids
	 * @param (array) $item_data - item sizes or just an empty array
	 *
	 * @return (string) html
	 */ 
	public function item_code($item_id, $item_data = array()) {
		$gp = $this->grid_params;
		$fetchable_item = (in_array($item_id, array('item', 'spacer'))) ? false : true;
		$code = '';
		
		// type text
		if($item_id == 'item') {
			$item_type = 'item';
			$type_text = __('Item', 'mg_ml');	
			$orig_item_type = false;
		}
		elseif($item_id == 'spacer') {
			$item_type = 'spacer';
			$type_text = __('Spacer', 'mg_ml');	
			$orig_item_type = 'spacer';
		}
		else {
			$item_type = get_post_meta($item_id, 'mg_main_type', true);
			$orig_item_type = $item_type; // keep it for checks
	
			// post type name or Media Grid item's type
			$post_type = get_post_type($item_id);
			$type_text = ($post_type != 'mg_items') ? mg_static::pt_id_to_name($item_id) : mg_static::item_types($orig_item_type);
		}
		
		
		// has sizes? otherwise set defaults
		if(empty($item_data)) {
			$item_data = array(
				'w'		=> '1_4',
				'h'		=> '1_4',
				'm_w'	=> '1_2',
				'm_h'	=> '1_3',
				'vis'	=> ''
			);	
		}
		
		
		// item sizes
		$h_sizes = $w_sizes = $sizes = mg_static::simpler_sizes_array(mg_static::item_sizes());
		$item_w = $item_data['w'];
		$item_h = $item_data['h'];   
		
		// mobile sizes
		$mh_sizes = $mw_sizes = mg_static::simpler_sizes_array(mg_static::mobile_sizes());
		$mobile_w = (isset($item_data['m_w'])) ? $item_data['m_w'] : $item_w;  
		$mobile_h = (isset($item_data['m_h'])) ? $item_data['m_h'] : $item_h; 
		
		// check mobile limits
		$mobile_w = (isset($mw_sizes[ $mobile_w ])) ? $mobile_w : '1_2';
		$mobile_h = (isset($mh_sizes[ $mobile_h ]) || $mobile_h == 'auto') ? $mobile_h : '1_3';
		
		// add height == auto if type != inline slider or inline video
		if(!in_array($orig_item_type, array('inl_slider', 'inl_video', 'spacer'))) {
			$h_sizes['auto'] = 'auto'; 
			$mh_sizes['auto'] = 'auto'; 
		}

				
		// item's head
		if($fetchable_item) {
			if(in_array($orig_item_type, array('inl_slider', 'inl_video', 'post_contents', 'inl_text', 'spacer'))) {
				$item_thumb = '<img src="'. MG_URL .'/img/type_icons/'.$orig_item_type.'.png" height="19" width="19" class="thumb" alt="" />';	
			} 
			else {
                $thumb_id = ($post_type == 'attachment') ? $item_id : get_post_thumbnail_id($item_id);
                
				$thumb_data = wp_get_attachment_image_src($thumb_id, array(48, 48));
				$item_thumb = '<img src="'.$thumb_data[0].'" class="thumb true_thumb" alt="" />';	
			}	
			
			$head = '
			<a href="'.get_admin_url().'post.php?post='.$item_id.'&action=edit" class="edit_item" target="_blank" title="'. esc_attr__('edit item', 'mg_ml') .'"></a>
			<h3>
				'.$item_thumb.'
				'.strip_tags(get_the_title($item_id)).'
			</h3>';
		}
		else {
			if($orig_item_type == 'spacer') {
				$head = '
				<select name="spacer_vis[]" class="mg_spacer_vis_dd" autocomplete="off">
					<option value="">'. __('always visible', 'mg_ml') .'</option>
					<option value="hidden_desktop" '.selected($item_data['vis'], 'hidden_desktop', false).'>'. esc_html__('hidden on desktop', 'mg_ml') .'</option>
					<option value="hidden_mobile" '.selected($item_data['vis'], 'hidden_mobile', false).'>'. esc_html__('hidden on mobile', 'mg_ml') .'</option>
				</select>';
			}
			else {
				$head = '';
			}
		}
		   
		   
		$code .= '
		<li id="box_'.uniqid().'" class="mg_box mg_'.$item_type.'_type"  data-w="'.$item_w.'" data-h="'.$item_h.'" data-mw="'.$mobile_w.'" data-mh="'.$mobile_h.'">
			<input type="hidden" name="grid_items[]" value="'.$item_id.'" />
		  
			<div class="mg_box_inner">
				<div class="del_item" title="'. esc_attr__('remove item', 'mg_ml') .'"></div>
					
				'. $head .'
				
				<div class="mg_box_ctrl_wrap">
					<p>'. $type_text .'</p>
					<p class="mg_builder_standard_sizes">';
					
						// choose the width
						$code .= __('Width', 'mg_ml').' 
						<select name="items_w[]" class="select_w mg_items_sizes_dd" autocomplete="off">'; 
							
							foreach($w_sizes as $size => $name) {
								$code .= '<option value="'.$size.'" '.selected($item_w, $size, false).'>'. $name .'</option>';	
							}
						
						$code .= '
						</select> <br/> '. __('Height', 'mg_ml').'  
						<select name="items_h[]" class="select_h mg_items_sizes_dd" autocomplete="off">';
	
							foreach($h_sizes as $size => $name) {
								$code .= '<option value="'.$size.'" '.selected($item_h, $size, false).'>'. $name .'</option>';	
							}
	
				   $code .= '
						</select>
					</p>
					<p class="mg_builder_mobile_sizes">';
	
						$code .= __('Width', 'mg_ml').' 
						<select name="items_mobile_w[]" class="select_m_w mg_items_sizes_dd" autocomplete="off">'; 
							
							foreach($mw_sizes as $size => $name) {
								($size == $mobile_w) ? $sel = 'selected="selected"' : $sel = '';
								$code .= '<option value="'.$size.'" '.selected($mobile_w, $size, false).'>'. $name .'</option>';	
							}
						
						$code .= '
						</select> <br/>  '. __('Height', 'mg_ml').' 
						<select name="items_mobile_h[]" class="select_m_h mg_items_sizes_dd" autocomplete="off">';
	
							foreach($mh_sizes as $size => $name) {
								$code .= '<option value="'.$size.'" '.selected($mobile_h, $size, false).'>'. $name .'</option>';	
							}
	
				   $code .= '
						</select>
					</p>
					<p class="mg_builder_arrow_move_wrap">
						<span class="mg_move_item_bw" title="'. __('move item backwards', 'mg_ml') .'"></span>
						<span class="mg_item_num" title="'. esc_attr__('item order', 'mg_ml') .'"></span>
						<span class="mg_move_item_fw" title="'. __('move item forwards', 'mg_ml') .'"></span>
					</p>
				</div>		
			</div>
		</li>';
		
		return str_replace(array("\r", "\n", "\t", "\v"), '', $code); // remove space for JS usage
	}

	
}
