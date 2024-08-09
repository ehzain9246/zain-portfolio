<?php
// handling grid parameters - returns items code

class mg_items_renderer {
	
	private $grid_id;
	public 	$grid_data; 			// grid data array returned by mg_static::get_grid_data() 
	public  $grid_atts 	= array(); 	// associative array containing grid options

	private $grid_max_width = 1200; // (int) grid max width - to create thumbs
	private $mobile_treshold = 700; // (int) mobile mode treshold - to create thumbs
	private $thumbs_quality = 90; 	// (int) thumbnails quality
	
	public  $ol_manager;		// overlay manager class instance
	private $inner_ol = ''; 	// (string) the overlay code going over images
	private $txt_under_ol = ''; // (string) the overlay code going under media block
	
	private $grid_composition; 			// (string) dynamic/manual
	private $grid_structure = array(); 	// (array) associative array containing items data (to view structure check grid_builder_engine.php at line 16)
	private $manual_items_id= array(); 	// simply items ID
	public  $queried_items 	= array(); 	// WP_query result, containing all items
	
	public  $grid_has_pag = NULL; 	// (bool) whether grid has pages or not
	public 	$page = 1; 				// (int) use it to know how many pages grid has - after having called render_items()
	private $spacer_count = 0; 		// (int) reference to use to setup spacer IDs
	
	public $items_term = array();	// (array) containing terms wrapped from all items to be used as filters

	
	// parameters to be resetted for each item
	private $item_id; 			// item id - as fetched by first wp_query
	private $item_type; 		// (string) mg_item type - set to a generic "post" for non MG items
	private $final_post_id; 	// final post id to get contents from (useful for "post contents" items)

	private $item_has_ol = true;		// (bool) whether item has overlay
	private $item_needs_thumb = true;	// (bool) whether item requires a featured image (and thumbnail)
	private $item_img_id;				// (int) containing image ID to be shown as items thumbnail (empty for !$item_needs_thumb)
	private $direct_link_item = false;	// (bool) whether item wants a direct link to the post

	private $item_classes = array();	// array containing all classes to merge together
	private $item_atts = array(); 		// array containing item attributes array(ATT-NAME => ATT-VALUE)
	 
	 
	
	/*
	 * Initialize class loading grid ID, overlay manager class instance and eventually its managed attributes
	 */
	public function __construct($grid_id, $ol_manager, $atts = array()) {
		$this->grid_id 		= $grid_id;
		$this->ol_manager 	= $ol_manager;
		$this->grid_atts 	= $atts;
		
		$this->grid_max_width 	= get_option('mg_maxwidth', 1200);
		$this->mobile_treshold 	= ((int)$atts['mobile_tresh']) ? (int)$atts['mobile_tresh'] : get_option('mg_mobile_treshold', 800);
		$this->thumbs_quality 	= get_option('mg_thumb_q', 85);
		
		// custom icons global array
		if(!isset($GLOBALS['mg_items_cust_icon'])) {
			$GLOBALS['mg_items_cust_icon'] = array();		
		}
		
		
		// retrieve grid composition
		$this->grid_data = mg_static::get_grid_data($grid_id);
		if(!empty( $this->grid_data ) && (isset($this->grid_data['items']) || $this->grid_data['composition'])) {
			
			// v5 retrocompatibility
			if(isset($this->grid_data['items'])) {
				$this->grid_composition = 'manual';
				$this->grid_structure = (array)$this->grid_data['items'];
				
				$this->setup_items_id_arr();
			}
			
			else {
				$this->grid_composition = $this->grid_data['composition'];
				
				if($this->grid_composition == 'manual') {
					$this->grid_structure = $this->grid_data['manual_items'];
					$this->setup_items_id_arr();
				} 
				else {
					$this->grid_structure = $this->grid_data['dynamic_structure'];
				}
			}	
			
			
			// query items and populate $queried_items
			if(
				($this->grid_composition == 'manual' && !empty($this->manual_items_id)) ||
				($this->grid_composition == 'dynamic' && !empty($this->grid_data['dynamic_src']))
			) {
				$this->queried_items = $this->query_items();
				$this->grid_has_pag = $this->grid_has_pag();
			}
			
			else {
				$this->throw_notice('Grid #'.$grid_id.' is empty', __LINE__);	
			}
		} 
	}
	
	
	
	// setup items ID array for manual grids - counting WPML translations
	private function setup_items_id_arr() {
		foreach($this->grid_structure as $item) {
			if(empty($item['id']) || $item['id'] == 'spacer') {
                continue;
            }
			
			// WPML / Polylang translations check
			if(function_exists('icl_object_id') ) {
				$post_type = get_post_type($item['id']);
				$this->manual_items_id[ $item['id'] ] = icl_object_id($item['id'], $post_type, true);	
			}
			else if(function_exists('pll_get_post')) {
                $translated_id = pll_get_post($item['id']);
                if($translated_id && get_post_status($traslated_id) == 'publish') {
                    $this->manual_items_id[ $item['id'] ] = $translated_id;	
                }
			}
			else {
				$this->manual_items_id[ $item['id'] ] = $item['id'];	
			}
		}	
	}
	
	
	
	// know whether grid has got pages or not - setup $grid_has_pag
	private function grid_has_pag() {
		if(empty($this->queried_items)) {
            return false;
        }
		$has_pag = false;
		
		// cached val
		if(!is_null($this->grid_has_pag)) {
			return $this->grid_has_pag;
		}
		
		// manual
		if($this->grid_composition == 'manual') {
			foreach($this->grid_structure as $item) {
				if($item['id'] == 'paginator') {
					$has_pag = true;
					break;	
				}
			}
		}
		
		// dynamic
		else {
			if($this->grid_data['dynamic_per_page']) {
				$grid_tiles = 0;
				foreach($this->grid_structure as $item) {
					if($item['id'] != 'spacer') {
						$grid_tiles++;	
					}
				}
				$true_items_per_page = ($this->grid_data['dynamic_repeat']) ? 
                    max($grid_tiles, (int)$this->grid_data['dynamic_per_page']) : 
                    min($grid_tiles, (int)$this->grid_data['dynamic_per_page']);
				
				if(count($this->queried_items) > $true_items_per_page) {
					$has_pag = true;	
				}
			}	
		}
		
		$this->grid_has_pag = $has_pag;
		return $has_pag;
	}
	
	
	
	//////////////////////////////////////////////////////
	
	
	
	/*
	 * Perform query to get items data
	 * @return (array) wp_query result
	 */
	private function query_items() {
		$gd = $this->grid_data;
		$posts = array();
		
		// global args
		$args = array(
			'post_status'			=> array('publish', 'inherit'),
			'ignore_sticky_posts' 	=> true,
			'offset'				=> 0,
			'meta_query'			=> array()
		);
		
		
		// MANUAL grid
		if($this->grid_composition == 'manual') {
            $args = array_merge($args, array(
				'post_type' 		=> mg_static::pt_list(),
				'post__in' 			=> array_unique($this->manual_items_id),
				'orderby'			=> 'post__in',
				'nopaging' 			=> true,
				'suppress_filters' 	=> true,
			));
			
			$result = $this->perform_wp_query($args);
			if($result) {
                $posts = $result;
            }
		}
		
		
		// DYNAMIC grid
		else {
			$args['orderby'] = str_replace('_desc', '', $gd['dynamic_orderby']);
			$args['order'] = (strpos($gd['dynamic_orderby'], '_desc') !== false) ? 'DESC' : 'ASC';
			
			// know total posts to fetch from structure
			$limit = 0;
			foreach($gd['dynamic_structure'] as $item) {
				if($item['id'] == 'item') {$limit++;}	
			}
			if($gd['dynamic_repeat']) { // be sure forced limit isn't lower than items num 
				$limit = max($limit, (int)$gd['dynamic_limit'])	;		
			}
			$args['posts_per_page'] = $limit;

			// does this require multiple queries? analyze targets
			$pt 		= array();
			$tax		= array();
			$term 		= array();
			$mg_type 	= array();

			foreach($gd['dynamic_src'] as $src) {
				list($src_pt, $src_tax) = explode('|||', $src['pt_n_tax']);
				
				if(!in_array($src_pt, $pt))					$pt[] = $src_pt;
				if(!in_array($src_tax, $tax))				$tax[] = $src_tax;
				if(!in_array($src['term'], $term))			$term[] = $src['term'];
				if(!in_array($src['mg_type'], $mg_type))	$mg_type[] = $src['mg_type'];;	
			}
	
			// unique query if fetching only MG items with unique term and unique item id
			if(implode('', $pt) == 'mg_items' && count($tax) <= 1 && count($term) == 1 && count($mg_type) == 1) {
				$args['post_type'] = 'mg_items';

				if(!empty($term[0])) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => $tax[0],
							'field' => 'id',
							'terms' => array($term[0]),
							'include_children' => true,
							'operator' => 'IN'
						)
					);		
				}
				
				if(!empty($mg_type[0])) {
					$args['meta_query'][] = array(
						'key' 	=> 'mg_main_type',
						'value' => $mg_type[0]
					);	
				}

				$result = $this->perform_wp_query($args);
				if($result) {
                    $posts = $result;
                }
			}
				
				
			// unique query if there is no mg_item nor attachment and uniform term
			else if(strpos(implode('', $pt), 'mg_items') === false && strpos(implode('', $pt), 'attachment') === false && count($term) == 1) {
				$args['post_type'] = $pt;
				
				// require thumbs
                $args['meta_query'][] = array(
                    'key' => '_thumbnail_id'
                );

				if(!empty($term[0])) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => $tax[0],
							'field' => 'id',
							'terms' => array($term[0]),
							'include_children' => true,
							'operator' => 'IN'
						)
					);		
				}

				$result = $this->perform_wp_query($args);
				if($result) {
                    $posts = $result;
                }	
			}
				
			
			// no way to group them - multiple query needed
			else {
				$queries = array();
				$sorted = array();
				
				foreach($gd['dynamic_src'] as $src) {
					list($src_pt, $src_tax) = explode('|||', $src['pt_n_tax']);
					
					$temp_args = $args;
					$temp_args['post_type'] = $src_pt;

					if(!empty($src['term'])) {
						$temp_args['tax_query'] = array(
							array(
								'taxonomy' => $src_tax,
								'field' => 'id',
								'terms' => array($src['term']),
								'include_children' => ($src_pt == 'attachment') ? false : true,
								'operator' => 'IN'
							)
						);		
					}
                    
                    // any tax term case
					else { 
						if($src_pt != 'attachment' || !empty($src_tax)) {
                            $temp_terms_ids = get_terms(
                                array(
                                    'taxonomy' 	=> $src_tax,
                                    'hide_empty'=> true,
                                    'fields'	=> 'ids'
                                )
                            );

                            if(is_array($temp_terms_ids) && !empty($temp_terms_ids)) {
                                $temp_args['tax_query'] = array(
                                    array(
                                        'taxonomy' => $src_tax,
                                        'field' => 'id',
                                        'terms' => $temp_terms_ids,
                                        'include_children' => ($src_pt == 'attachment') ? false : true,
                                        'operator' => 'IN'
                                    )
                                );		
                            }
                            else {
                                $temp_args['tax_query'] = array();	
                            }
                        }
					}
						
					
                    
					// require thumbs if not MG item and attachment
					if($src_pt != 'mg_items' && $src_pt != 'attachment') {
						$temp_args['meta_query'][] = array(
							'key' => '_thumbnail_id'
						);
					}
					
                    
                    // adjust for attachment pt
                    if($src_pt  == 'attachment') {
                        $temp_args['post_mime_type'] = 'image/jpeg,image/gif,image/jpg,image/png';
                    }
                    
                    
					// MG items - item type
					if($src_pt == 'mg_items' && !empty($src['mg_type'])) {
						$temp_args['meta_query'][] = array(
							'key' 	=> 'mg_main_type',
							'value' => $src['mg_type']
						);
					}
                    
					$result = $this->perform_wp_query($temp_args);
                    
					if(!empty($result)) {
                        $queries[] = $result;
                    }	
				}

								
				// sort by keys and slice
                if(!empty($queries)) {
                    $posts = (count($queries) > 1) ? $this->sort_n_slice_multiquery($queries, $limit) : $queries[0];
                    unset($queries);	
                }
			}
		}

		return array_values($posts);
	}
	
	
	
	// getting args - return posts array resulting from WP_query
	private function perform_wp_query($args) {
		
		// MG-FILTER - allow items query filter before grids rendering - passes also grid id and grid data
		$args = apply_filters('mg_items_rendered_query', $args, $this->grid_id, $this->grid_data);
		$query = new WP_Query($args);
        
		return $query->posts;	
	}
	
	
	
	// getting multiple queries results - returns an unique array properly sorted and containing only $limit elements
	private function sort_n_slice_multiquery($queries, $limit) {
		$merged = array();
		$sortby = 'post_'. str_replace('_desc', '', $this->grid_data['dynamic_orderby']);
		
		foreach($queries as $q) {
			foreach($q as $post) {
				$merged[ $post->$sortby ] = $post;	
			}
		}
		ksort($merged);
		
		// date sorting - reverse
		if(strpos($this->grid_data['dynamic_orderby'], '_desc') !== false) {
			$merged = array_reverse($merged);	
		}
	
		return array_slice($merged, 0, (int)$limit);
	}
	
	
	//////////////////////////////////////////////////////
	
	
	/*
	 * Return items code ready to be used
	 */
	public function render_items() {
		if(empty($this->queried_items)) {
			$this->throw_notice('Trying to render items but no posts found', __LINE__);	
			return '';
		}
		$code = '';
		
		
		// manual structure
		if($this->grid_composition == 'manual') {
			
			// set post ID as queried_items indexes to be called
			$posts = array();
			foreach($this->queried_items as $post) {
				$posts[ $post->ID ] = $post;	
			}
			$this->queried_items = $posts;
			unset($posts);
			
			// cycle items and fill with queried data
			foreach($this->grid_structure as $item_data) {
				
				// consider manual pagination
				if($item_data['id'] == 'paginator') {
					$this->grid_has_pag = true;
					$this->page++;
					
					continue;
				}

				$true_id = $this->manual_items_id[ $item_data['id'] ]; // counting translations
				$post = isset($this->queried_items[$true_id]) ? $this->queried_items[$true_id] : false;
				
				$code .= $this->item_code($post, $item_data); 
			}
		}
		
		
		// dynamic
		else {
			$managed = 0;
			$item_to_use = 0;
			$managed_in_page = 0;
			
			$total_posts = count($this->queried_items);
			$total_items = count($this->grid_structure);
			
			// randomize?
			if($this->grid_data['dynamic_random']) {
				shuffle($this->queried_items);
			}
			
			
			// build
			while($managed <= $total_posts) {
				$item = $this->grid_structure[ $item_to_use ]; 
				
				if($item['id'] == 'spacer') {
					$code .= $this->item_code(false, $item);
				}
				else {
					$post = isset($this->queried_items[ $managed ]) ? $this->queried_items[ $managed ] : false;
					$item_code = $this->item_code($post, $item);
                    $managed++;
                    
                    // no code - there's something wrong - skip
                    if(!empty($item_code)) {
                        $code .= $item_code;
                        $managed_in_page++;

                        // avoid to print further spacers
                        if($managed == $total_posts) {
                            break;	
                        }

                        // pagination
                        $per_page = (int)$this->grid_data['dynamic_per_page'];
                        if($per_page && $managed_in_page >= $per_page) {
                            $this->grid_has_pag = true;
                            $managed_in_page = 0;
                            $this->page++;	
                        }
                    }
				}	
				
				// be sure to not overtake structure
				if(($item_to_use + 1) >= $total_items) {
					$item_to_use = 0;	
				} else {
					$item_to_use++;	
				}
			}
		}
		
		return $code;
	}
	
	
	

	/*
	 * Create single item's code
	 * @attr (object) $post = post object returned by WP_query
	 * @attr (array) $item_params = assiciative array composed on grids saving
		array(
			id  = manual mode => item's id || paginator || spacer - dynamic mode => item || spacer
			w   = width
			h   = height
			m_w = mobile width
			m_h = mobile height
			
			vis = empty || mobile_hidden || desktop_hidden - ONLY for spacer
		);	
	 *
	 * @return (string) item's HTML code
	 */
	private function item_code($post, $item_params = array()) {
		$this->item_classes = array();
		$this->item_atts = array();
		
		if(($item_params['id'] != 'spacer' && empty($post)) || empty($item_params) ) {
            return '';
        }

		// item type - set to a generic "post" for non MG items
		if($item_params['id'] == 'spacer') {
			$this->item_type = 'spacer';
		}
		else if($post->post_type == 'product') {
			$this->item_type = 'woocom';
		} 
        else if($post->post_type == 'attachment') {
			$this->item_type = (get_post_meta($post->ID, 'mg_attach_as_static', true)) ? 'simple_img' : 'wp_media';
		} 
		else {
			$this->item_type = ($post->post_type == 'mg_items') ? get_post_meta($post->ID, 'mg_main_type', true) : 'post';
		}
		
		// specific MG item types
		switch($this->item_type) {
			case 'single_img'	: $this->item_classes[] = 'mg_image'; break;	
			case 'img_gallery'	: $this->item_classes[] = 'mg_gallery'; break;	
			case 'simple_img'	: $this->item_classes[] = 'mg_static_img'; break;
			default 			: $this->item_classes[] = 'mg_'. $this->item_type; break;	 
		}
		
		
		// v5 spacer retrocompatibility - turn normal item into spacer
		if($this->item_type == 'spacer' && is_object($post)) {
			$item_params['id'] = 'spacer';
			$item_params['vis'] = get_post_meta($post->ID, 'mg_spacer_vis', true);	
		}
		
		// spacer count
		if($this->item_type) {
			$this->spacer_count++;	
		}
		
		

		// get item details
		$this->item_id = ($this->item_type == 'spacer') ? 'mg_spacer_'.$this->spacer_count : $post->ID;
		$this->item_classes = array_merge($this->item_classes, array('mg_box', 'mg_pag_'.$this->page, 'mgi_'.$this->item_id));
		$this->item_atts += array('id' => uniqid().mt_rand(0, 999), 'data-item-id' => $this->item_id);
		
		
		// pag_hide class setup - considering deeplink
		$dl_pag = (int)$this->grid_atts['dl_pag'];
		if(
			(!$dl_pag && $this->page > 1) ||
			($dl_pag  && $this->page != $dl_pag)		
		) {
			$this->item_classes[] = 'mg_pag_hide';	
		}
		

		// has overlay? needs thumbnail? triggers lightbox?
		$this->item_has_ol = $this->item_has_ol();		
		$this->item_needs_thumb = $this->item_needs_thumb();
			
		if($this->item_triggers_lb()) {
			$this->item_classes[] = 'mgi_has_lb';	
		}
		
		
		// inline audio/video - if no overlay add class to avoid JS actions
		if($this->item_type == 'inl_video' && !$this->item_has_ol) {
			$this->item_classes[] = 'mgi_iv_shown';		
		}
		else if($this->item_type == 'inl_audio' && !$this->item_has_ol) {
			$this->item_classes[] = 'mgi_ia_shown';		
		}
		

		// MG "post contents" item - retrieve remote 
		if($this->item_type == 'post_contents') {
			$this->final_post_id = $this->post_contents_get_post();
			
			if(empty($this->final_post_id)) {
				$this->throw_notice("'Post Contents' Item ". $post->ID ." - no valid post found", __LINE__); 
				return '';
			}
		}
		else {
            $this->final_post_id = $this->item_id;
        }
		
		
		// sanitize sizes
		$item_params = $this->sanitize_sizes($item_params, $this->item_type);
	
		// setup sizes classes
		foreach($item_params as $stype => $sval) {
			if(!in_array($stype, array('w', 'h', 'm_w', 'm_h'))) {continue;}
			
			$this->item_classes[] = 'mgis_'.$stype.'_'.$sval; // sizing through CSS	
		}
		

		// get thumbnails
		if($this->item_needs_thumb) {
			$thumbs = $this->get_item_thumbs($item_params);
			if(empty($thumbs)) {
				$this->throw_notice("Post ".$this->final_post_id." doesn't have featured image", __LINE__); 	
				return '';
			}
	

			// is direct link?
			$this->direct_link_item = ($this->item_type == 'link' || 
				$this->item_type == 'link' || 
				($this->item_type == 'post_contents' && get_post_meta($post->ID, 'mg_link_to_post', true)) ||
				
				(in_array($this->item_type, array('post', 'woocom')) && isset($this->grid_data['composition']) && $this->grid_data['composition'] == 'dynamic' && $this->grid_data['dynamic_force_links']) ||
				(in_array($this->item_type, array('post', 'woocom')) && get_post_meta($post->ID, 'mg_link_only', true))
			) ? 
                true : false; 
			
			
			if($this->direct_link_item) {
				$key = array_search('mgi_has_lb', $this->item_classes);
				if($key !== false) {
					unset( $this->item_classes[$key] );	
				}	
			}
		}
		else {
			$thumbs = false;
			$this->direct_link_item = false;	
		}
		
		
		
		// setup overlay variables
		$this->inner_ol = (!$this->item_has_ol || get_option('mg_hide_overlays')) ? '' : '<div class="mgi_overlays">'. $this->ol_manager->get_img_ol( $this->final_post_id ) .'</div>';
		$this->txt_under_ol = ($this->grid_atts['title_under']) ? $this->ol_manager->get_txt_under( $this->final_post_id ) : '';
		
		// text under class
		if(
			(
				(
					!in_array($this->item_type, array('inl_slider', 'simple_img', 'inl_text')) || 
					$this->item_type == 'simple_img' && $this->item_has_ol
				) 
				&& $this->item_has_ol
			) ||
			in_array($this->item_type, array('inl_video', 'inl_audio'))
		) {
			$this->item_classes[] = 'mg_has_txt_under';
		}
		
		
		// enqueue term classes for filters and group them in $this->items_cat
		if($this->grid_atts['filter']) {
			$this->enqueue_item_term_classes();	
		}
				
		
		// custom icon check
		if($this->item_has_ol) {
			$cust_icon = get_post_meta($post->ID, 'mg_cust_icon', true);
			if($cust_icon) {
				$GLOBALS['mg_items_cust_icon'][ $post->ID ] = $cust_icon;
			}
		}
		
		// search attribute and eventually deeplinked search elaboration
		if($this->item_type != 'spacer') {
			$this->item_atts['data-mg-search'] = strtolower($post->post_title .' '. get_post_meta($post->ID, 'mg_search_helper', true));	
			
			if(!empty($this->grid_atts['dl_search'])) {
				$search_arr = explode(' ', $this->grid_atts['dl_search']); 
				$matches = false;
				
				foreach($search_arr as $sa) {
					if(strpos($this->item_atts['data-mg-search'], $sa) !== false) {
						$matches = true;
						break;	
					}
				}
				$this->item_classes[] = ($matches) ? 'mg_search_res' : 'mg_search_hide';
			}
		}
		
		
		// text under - clean mode class
		if($this->item_type == 'inl_text') {
			$specific_val = get_post_meta($post->ID, 'mg_clean_inl_txt', true);
			
			if(
				(empty($specific_val) && get_option('mg_clean_inl_txt')) ||
				$specific_val == 'yes'
			) {
				$this->item_classes[] = 'mg_clean_inl_text';
			}
		}
		
		
		// spacer item - visibility class
		if($this->item_type == 'spacer' && $item_params['vis']) {
			$this->item_classes[] = 'mg_spacer_'.$item_params['vis'];
		}
		
        
        // kenburns class
        if(!in_array($this->item_type, array('inl_video', 'inl_slider')) && get_post_meta($this->item_id, 'mg_kenburns_fx', true)) {
            $this->item_classes[] = 'mg_to_kenburn';
        }
		
		//////////
		
		// MG-FILTER - allow custom classes management for each item - passes already enqueued classes array, item id and grid attributes
		$this->item_classes = apply_filters('mg_item_classes', $this->item_classes, $this->item_id, $this->grid_atts);
		
		// MG-FILTER - allow custom attributes management for each item - passes already enqueued attributes array, item id and grid attributes
		$this->item_atts = apply_filters('mg_item_atts', $this->item_atts, $this->item_id, $this->grid_atts);
		
		//////////
		
		
		// wrap up attributes
		$atts = '';
		foreach($this->item_atts as $att => $val) {
			$atts .= ' '. $att .'="'. esc_attr(strip_tags($val)) .'"';	
		}
		
		// start building item's code	
		$code =	
		'<div class="'. implode(' ', $this->item_classes) .'" '. $atts .'>
			<div class="mgi_elems_wrap">';			
			
			
			// spacer doesn't have inner
			if($this->item_type != 'spacer') {
			
				// box inner
				$inner_tag = ($this->direct_link_item) ? 'a' : 'div';
				if($this->direct_link_item) {
					$link 	= ($this->item_type == 'link') ? get_post_meta($post->ID, 'mg_link_url', true) : get_permalink($this->final_post_id);
					$nofollow = ($this->item_type == 'link' && get_post_meta($post->ID, 'mg_link_nofollow', true)) ? 'rel="nofollow noopener"' : '';
					
					$target = ($this->item_type == 'link') ? 'target="_'.get_post_meta($post->ID, 'mg_link_target', true).'"' : 'target="_'.get_post_meta($post->ID, 'mg_link_only', true).get_post_meta($post->ID, 'mg_link_to_post', true).'"';
					
                    if($target == 'target="_"') {
                        $target = 'target="_self"';
                    }
					
					// be sure target is a proper value
					if($target == 'target="_1"') {
						$target = 'target="_self"';
					}
					
					$link_atts = 'href="'. $link .'" '.$target.' '.$nofollow;	
				}
				else {
                    $link_atts = '';
                }
                
				$code .= '
					<'. $inner_tag .' class="mg_box_inner" '. $link_atts .'>';
				
						// media contents
						$code .= '<div class="mg_media_wrap">'. $this->item_media_contents($post, $thumbs, $item_params) .'</div>';	
						
						// overlays code
						if(!get_option('mg_hide_overlays')) {
							$code .= $this->inner_ol;
						}
					
				// box inner closing
				$code .= '
					</'. $inner_tag .'>';
				
				// text under item
				if(in_array('mg_has_txt_under', $this->item_classes)) {
					$code .= $this->txt_under_ol;
				}
			}
		
		// close elems_wrap and mg_box
		return $code .'
			</div>
		</div>';
	}
	
	
	
	
	/*
	 * Fills media part of the item - switching between image, slider, audio, etc etc
	 * @param (object) $post - object containing $final_post_id data fetched with WP_query
	 * @param (array) $thumbs - array containing 'desktop' and 'mobile' indexes and relative thumb URLs
	 * @param (array)$sizes - item sizes (check item_code() legend)
	 *
	 * @return (string) HTML code 
	 */
	private function item_media_contents($post, $thumbs, $sizes) {
		$code = '';
		
		// calculate thumb sizes
		$thb_w = ceil($this->grid_max_width * mg_static::size_to_perc($sizes['w']));
		$thb_h = ceil($this->grid_max_width * mg_static::size_to_perc($sizes['h']));
		
		$m_thb_w = ceil($this->mobile_treshold * mg_static::size_to_perc($sizes['m_w']));
		$m_thb_h = ceil($this->mobile_treshold * mg_static::size_to_perc($sizes['m_h']));
		
		
		### inline slider ###
		if($this->item_type == 'inl_slider') {
			$slider_img     = get_post_meta($this->item_id, 'mg_slider_img', true);
            $attach_video   = get_post_meta($this->item_id, 'mg_slider_vid', true);
            
			if(!is_array($slider_img)) {
                return '';
            }
            
            // slideshow
            $ss_cmd       = (get_post_meta($this->item_id, 'mg_slider_autoplay', true)) ? get_post_meta($this->item_id, 'mg_slider_autoplay', true) : get_option('mg_lb_slider_slideshow', 'yes');
            $ss_autoplay  = ($ss_cmd === '1' || $ss_cmd == 'autoplay') ? 1 : 0; 
            $ss_cmd       = (!$ss_cmd || $ss_cmd == 'no') ? 0 : 1;   
            $kenburns     = (get_post_meta($this->item_id, 'mg_kenburns_fx', true)) ? 1 : 0;
            
			// randomize images?
			if(get_post_meta($this->item_id, 'mg_inl_slider_random', true)) {
				shuffle($slider_img);	
			}
	
			$code .= '
			<div id="'. uniqid() .'" class="mg_inl_slider_wrap" data-ss-cmd="'. $ss_cmd .'" data-autoplay="'. $ss_autoplay .'" data-kenburns="'. $kenburns .'">
				<ul class="mg_displaynone">';
			  
                $a = 0;
				foreach($slider_img as $img_id) {
					
					// WPML / Polylang integration - get translated ID
					if(function_exists('icl_object_id')) {
						$img_id = icl_object_id($img_id, 'attachment', true);	
					}
					else if(function_exists('pll_get_post')) {
                        $translated_id = pll_get_post($img_id);
                        if($translated_id) {
                            $img_id = $translated_id;	
                        }	
					}
					

					// resize if is not an animated gif
					if(!mg_static::img_is_gif($img_id)) {
						if($kenburns) {
							// resizers scale only to lower side - use wordpress thumbs
							$kb_img_h = ($sizes['h'] != 'auto' && $sizes['m_h'] != 'auto') ? (max($thb_h, $m_thb_h) * 1.25) : 0;
							$kb_img_src = wp_get_attachment_image_src($img_id, array((max($thb_w, $m_thb_w) * 1.25), $kb_img_h));
							$slider_thumb = $kb_img_src[0]; 
						}
						else {
							$thumb_sizes = $this->inl_slider_img_sizes( wp_get_attachment_image_src($img_id, 'full'), $sizes);
							$slider_thumb = mg_static::thumb_src($img_id, $thumb_sizes['w'], $thumb_sizes['h'], $this->thumbs_quality);
						}
					}
					else {
						$slider_thumb = mg_static::img_id_to_fullsize_url($img_id);
					}

                    
                    // has attached video?
                    if(is_array($attach_video) && isset($attach_video[$a]) && !empty($attach_video[$a])) {
                        if(mg_static::video_embed_url($attach_video[$a]) == 'wrong_url') {
                            
                            if(strpos($attach_video[$a], '.youtube.') !== false ) {
                                $code .= '  
                                <li data-img="'. esc_attr($slider_thumb) .'" data-type="mixed">'. esc_html__('wrong video URL', 'mg_ml') .'</li>';
                            }
                            else {
                                $code .= '
                                <li data-img="'. esc_attr($slider_thumb) .'" data-type="video">
                                    <video controls="controls" preload="auto" poster="'. esc_attr($slider_thumb) .'">
                                        '. mg_static::sh_video_sources($attach_video[$a]) .'
                                    </video> 
                                </li>';  
                            }
                        }
                        else {
                            $code .= '
                            <li data-img="'. esc_attr($slider_thumb) .'" data-type="iframe">
                                <div class="mg_lcms_iframe_icon"></div>
                                <iframe class="mg_video_iframe" src="" data-src="'. mg_static::video_embed_url($attach_video[$a], false) .'" frameborder="0" allowfullscreen></iframe>
                            </li>';                
                        }
                    }
                    else {
                        $caption = '';
                        if(get_post_meta($this->item_id, 'mg_slider_captions', true)) {
                           $img_data = get_post($img_id);
                           $caption = (empty($img_data->post_content)) ? '' : trim($img_data->post_content);
                        }
                        
                        $code .= '<li data-img="'. esc_attr($slider_thumb) .'" data-type="image">'. $caption.'</li>'; 
                    }
                    
                    $a++;
                }
	
			// slider wrap closing
			$code .= '
				</ul>
			</div>'; 
		}
		
				
		### inline video ###
		if($this->item_type == 'inl_video') {
			$video_url       = get_post_meta($this->item_id, 'mg_video_url', true);
			$poster          = (get_post_meta($this->item_id, 'mg_video_use_poster', true) && $thumbs['desktop']) ? true : false;
			$autoplay_mode   = get_post_meta($this->item_id, 'mg_autoplay_inl_video', true); 
            $z_index = ($poster) ? 'style="z-index: -1;"' : '';
			
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

			// self-hosted
			if(mg_static::video_embed_url($video_url) == 'wrong_url') {
				$sources = mg_static::sh_video_sources($video_url);

				if(!$sources) {
					$code .= '<p><em>Video extension not supported ..</em></p>';	
				}
				else {
					$preload = ($poster) ? 'meta' : 'auto';
	
					$code .= 
					'<div id="'.uniqid().'" class="mg_sh_inl_video mg_me_player_wrap mg_self-hosted-video '.$autoplay_class.'" '.$z_index.'>
						<video width="100%" height="100%" controls="controls" preload="'.$preload.'">
						  '.$sources.'
						</video>
					</div>';
				}
			}
			else {
				$url_to_use = ($poster) ? '' : mg_static::video_embed_url($video_url, $autoplay);
				$autoplay_url = ($poster) ? 'data-autoplay-url="'. mg_static::video_embed_url($video_url, true, $autoplay_mode). '"' : '';
				
				$code .= '<iframe class="mg_video_iframe" src="'.$url_to_use.'" frameborder="0" allowfullscreen '.$autoplay_url.' '.$z_index.'></iframe>';	
			}	
			
		}	
			
		
		### inline audio ###
		else if($this->item_type == 'inl_audio') {
			$external_audio_url = get_post_meta($this->item_id, 'mg_soundcloud_url', true);
								
			if(!empty($external_audio_url)) {
				$ea_lazyload = ($this->item_has_ol) ? true : false;
				$code .= mg_static::audio_embed_iframe($external_audio_url, true, $ea_lazyload);
			}
			else {
				$preload = (!$this->item_has_ol) ? 'auto' : 'metadata'; 
				$tracklist = get_post_meta($this->item_id, 'mg_audio_tracks', true);
				
				// player
				$args = array(
					'posts_per_page'	=> -1,
					'orderby'			=> 'post__in',
					'post_type'       	=> 'attachment',
					'post__in'			=> $tracklist
				);
				$tracks = get_posts($args);
				$player_id = uniqid();
	
				$code .= '
				<div id="'.$player_id.'" class="mg_me_player_wrap mg_inl_audio_player">
					<audio controls="controls" preload="'.$preload.'" width="100%">';
						foreach($tracks as $track) {
							$code .= '<source src="'. wp_get_attachment_url($track->ID) .'" type="'. $track->post_mime_type .'">';
						}
				$code .= '
					</audio>
				</div>';
				
				// tracklist
				$tot = (is_array($tracklist)) ? count($tracklist) : 0;
				if($tot > 1) {
					$code .= '
					<ol id="'.$player_id.'-tl" class="mg_audio_tracklist mg_inl_audio_tracklist mg_displaynone">';
					
						$a = 1;
						foreach($tracks as $track) {
							$current = ($a == 1) ? 'mg_current_track' : '';
							$code .= '<li mg_track="'. wp_get_attachment_url($track->ID) .'" data-track-num="'.$a.'" class="'.$current.'">'. $track->post_title .'</li>';
							$a++;
						}
					
					$code .= 
					'</ol>';
				}
			}
		}


		### inline text ###
		elseif($this->item_type == 'inl_text') {
			$no_txt_resize_class = (get_post_meta($this->item_id, 'mg_inl_txt_no_resize', true)) ? 'mg_inl_txt_no_resize' : '';
			$video_bg = get_post_meta($this->item_id, 'mg_bg_video_url', true);
					
						
			// background image or video
			//// video
			if($video_bg) {
				$code = '
				<div class="mg_inl_txt_media_bg">
					<video class="mg_inl_txt_video_bg" data-object-fit="cover" playsinline muted loop>
						<source src="'. $video_bg .'" />
					</video>
				</div>';	
			}
			
			//// image
			elseif(get_post_meta($this->item_id, 'mg_inl_txt_img_as_bg', true)) {
				$img_id = get_post_thumbnail_id($this->item_id);
				if(!empty($img_id)) {
					
					if(!mg_static::img_is_gif($img_id)) {
						$img_h = ($sizes['h'] != 'auto' && $sizes['m_h'] != 'auto') ? max($thb_h, $m_thb_h) : false;
						$img_url = mg_static::thumb_src($img_id, max($thb_w, $m_thb_w), $img_h, $this->thumbs_quality);
					}
					else {
						$img_url = mg_static::img_id_to_fullsize_url($img_id);
					}
					
					$code = '<div class="mg_inl_txt_media_bg mgi_bg_pos_'. get_post_meta($this->item_id, 'mg_thumb_center', true) .'" style="background: url('. $img_url .') no-repeat center center scroll transparent; background-size: cover;"></div>';
				}	
			}
		
		
			// custom CSS
			$css = $this->inl_txt_custom_css();
			$inl_txt_custom_css = (!empty($css)) ? 'style="'. $css .'"' : '';
			
			
			// responsive behaviot text
			switch(get_post_meta($this->item_id, 'mg_inl_txt_resp_behav', true)) {
				case 'scroll' 		: $resp_behav_class = 'mg_inl_txt_rb_scroll'; break; 	
				case 'txt_resize' 	: $resp_behav_class = 'mg_inl_txt_rb_txt_resize'; break; 
				default 			: $resp_behav_class = ''; break; 
			}


			// inl text contents			
			$code .= '
			<div class="mg_inl_txt_wrap '.$resp_behav_class.'" '. $inl_txt_custom_css .'>
				<div class="mg_inl_txt_contents mg_inl_txt_valign_'.get_post_meta($this->item_id, 'mg_inl_txt_vert_align', true).'">
					'. do_shortcode(wpautop($post->post_content)) .'
				</div>
			</div>';
		}
			
		
	 	##########
		
        
		// single featured image (eventually with kenburns) to be associated with various types
		// not for inline video or soundcloud without overlay
		if(
			!$this->item_needs_thumb ||
			($this->item_type == 'inl_video' && !$this->item_has_ol) ||
			($this->item_type == 'inl_audio' && !$this->item_has_ol && get_post_meta($this->item_id, 'mg_soundcloud_url', true) && strpos(get_post_meta($this->item_id, 'mg_soundcloud_url', true), 'mixcloud.com') === false)
		) {}
		else {
			
			// ken burns maybe?
			if($this->item_type != 'inl_video' && get_post_meta($this->item_id, 'mg_kenburns_fx', true)) {
				
				// resizers scale only to lower side
                $kb_img_w = max($thb_w, $m_thb_w) * 1.25;
                $kb_img_h = ($sizes['h'] != 'auto' && $sizes['m_h'] != 'auto') ? (max($thb_h, $m_thb_h) * 1.25) : 0;
				
                if(mg_static::img_is_gif($this->item_img_id)) {
                    $kb_img_url = mg_static::img_id_to_fullsize_url($this->item_img_id);    
                } else {
                    $kb_img_url = mg_static::thumb_src($this->item_img_id, $kb_img_w, $kb_img_h, $quality = 80, $alignment = 'c', $resize = 1);
                }

                $thumbs['desktop'] = $thumbs['mobile'] = $kb_img_url;
			} 
			
			
			// has auto height? set padding
			if($sizes['h'] == 'auto' || $sizes['m_h'] == 'auto') {
				$img_info = wp_get_attachment_image_src($this->item_img_id, 'full');
					
				if($img_info[2]) {
					$ratio_val = (float)$img_info[2] / (float)$img_info[1];
				}
				else {
					$ratio_val = 0;
					$this->throw_notice("WP doesn't return image sizes for post ".$this->final_post_id, __LINE__); 	
					return false;
				}
				
				$autoheight_padding = 'style="padding-bottom: '. (round($ratio_val, 3) * 100) .'%;"';	
			}
			else {
				$autoheight_padding = '';	
			}


			$code .= 
			'<div class="mgi_thumb_wrap" '.$autoheight_padding.'>'.
				'<div class="mgi_thumb mgi_main_thumb mgi_bg_pos_'. get_post_meta($this->item_id, 'mg_thumb_center', true) .'" data-fullurl="'. $thumbs['desktop'] .'" data-mobileurl="'. $thumbs['mobile'] .'" data-item-title="'. esc_attr($post->post_title) .'"></div>
			</div>
			<noscript>
				<img src="'. $thumbs['desktop'] .'" alt="'. esc_attr($post->post_title) .'" />
			</noscript>';	
		}
		
		
		return $code;
	}
	
	
	
	/*
	 * Get image sizes for inline slider
	 * @param (array) img_data - array returned by wp_get_attachment_image_src()
	 * @return (array) - array(width, height)
	 */
	private function inl_slider_img_sizes($img_data, $item_sizes) {
		$mobile_tres = get_option('mg_mobile_treshold', 800);
		
		if(!isset($item_sizes['m_w'])) {
			$item_sizes['m_w'] = $item_sizes['w'];
			$item_sizes['m_h'] = $item_sizes['h'];
		}
		
		// find item max width
		$nw = $this->grid_max_width * mg_static::size_to_perc($item_sizes['w']);
		$mw = $this->mobile_treshold * mg_static::size_to_perc($item_sizes['m_w']);
		$item_max_w = max($nw, $mw);
		
		// find item max height
		$nh = $this->grid_max_width * mg_static::size_to_perc($item_sizes['h']);
		$mh = $this->mobile_treshold * mg_static::size_to_perc($item_sizes['m_h']);
		$item_max_h = max($nh, $mh);
		
		return array(
			'w' => ($item_max_w < $img_data[1]) ? $item_max_w : $img_data[1],
			'h' => ($item_max_h < $img_data[2]) ? $item_max_h : $img_data[2]
		);	
	}
	
	
	//////////////////////////////////////////////////////
	
	
	
	/*
	 * Understands if item has overlay
	 * @return (bool)
	 */
	private function item_has_ol() {
		switch($this->item_type) {
			
			case 'inl_slider' 	:
			case 'inl_text' 	:
			case 'spacer'		:
				$has_it = false;
				break;	
			
			case 'inl_video' :
				$has_it = (get_post_meta($this->item_id, 'mg_video_use_poster', true)) ? true : false;
				break;	
			
			case 'simple_img':
			case 'inl_audio' : 
				$has_it = (get_post_meta($this->item_id, 'mg_static_show_overlay', true)) ? true : false;
				break;	
			
			default : 
				$has_it = true;
				break;	
		}
		
		
		if(!$has_it) {
			$this->item_classes[] = 'mg_item_no_ol';	
		}
		return $has_it;
	}
	
	
	/*
	 * Understands if item requires a featured image and then a thumbnail
	 * @return (bool)
	 */
	private function item_needs_thumb() {
		switch($this->item_type) {
			
			case 'inl_slider' 	:
			case 'inl_text' 	:
			case 'spacer'		:
				$needs_id = false;
				break;	
			
			case 'inl_video'	:
				// no thumb only if no overlay
				$needs_id = (get_post_meta($this->item_id, 'mg_video_use_poster', true)) ? true : false;
				break;	

			case 'inl_audio' : 
				// no thumb only if is soundcloud and has no overlay
				$needs_id = (get_post_meta($this->item_id, 'mg_soundcloud_url', true) && strpos(get_post_meta($this->item_id, 'mg_soundcloud_url', true), 'mixcloud.com') === false && !get_post_meta($this->item_id, 'mg_static_show_overlay', true)) ? false : true;
				break;	
			
			default : 
				$needs_id = true;
				break;	
		}
		return $needs_id;
	}
	
	
	/*
	 * Understands if item has to trigger lightbox
	 * @return (bool)
	 */
	private function item_triggers_lb() {
		switch($this->item_type) {
			
			case 'single_img'	:
			case 'img_gallery' 	:
			case 'video'		:
			case 'audio' 		:
			case 'lb_text'		:
            case 'wp_media'		:
				$triggers_it = true;
				break;	

			case 'post_contents':
				$triggers_it = (get_post_meta($this->item_id, 'mg_link_to_post', true)) ? false : true;
				break;	
			
			case 'post' : 
			case 'woocom' : 
				$triggers_it = (get_post_meta($this->item_id, 'mg_link_only', true)) ? false : true;
				break;	
			
			default : 
				$triggers_it = false;
				break;	
		}
		return $triggers_it;
	}
	
	
	
	
	/*
	 * Matches detected sizes against allowed values and allow "auto" height only on items showing an image
	 * @param (array) $item_params = check item_code() legend
	 * @param (string) $item_type = MG item types or post or woocomm or spacer
	 *
	 * @return (array) the $item_params array with checked sizes
	 */
	private function sanitize_sizes($item_params, $item_type) {
		$available = array_keys(mg_static::item_sizes());
		$mobile = array_keys(mg_static::mobile_sizes());
		
		// if item doesn't need a thumbnail it can't have "auto" height
		if((!$this->item_needs_thumb && $item_type != 'inl_text') || $item_type == 'inl_video') {
			if($item_params['h'] == 'auto') {
				$item_params['h'] = ($this->grid_composition == 'manual') ? '1_4' : $this->grid_data['dynamic_auto_h_fb']['h'];	
			}
			if($item_params['m_h'] == 'auto') {
				$item_params['m_h'] = ($this->grid_composition == 'manual') ? '1_3' : $this->grid_data['dynamic_auto_h_fb']['m_h'];	
			}
		}
		
		if(!in_array($item_params['w'], $available)) {$item_params['w'] = '1_4';}
		if(!in_array($item_params['h'], array_merge($available, array('auto')) )) {$item_params['h'] = '1_4';}
		
		if(!in_array($item_params['m_w'], $mobile)) {$item_params['m_w'] = '1_2';}
		if(!in_array($item_params['m_h'], array_merge($mobile, array('auto')) )) {$item_params['m_h'] = '1_3';}	

		return $item_params;
	}
	
	
	
	/*
	 * Creates item thumbnail URLs for desktop and mobile modes
	 * @return (bool/array) false if item doesn't have featured image || array('desktop' => url, 'mobile' => url)
	 */
	private function get_item_thumbs($sizes) {
        
		// thumb sizes
		$thb_w = ceil($this->grid_max_width * mg_static::size_to_perc($sizes['w']));
		$thb_h = ceil($this->grid_max_width * mg_static::size_to_perc($sizes['h']));
		
		if(!isset($sizes['m_w'])) {
			$sizes['m_w'] = $sizes['w'];
			$sizes['m_h'] = $sizes['h'];
		}
		$m_thb_w = ceil($this->mobile_treshold * mg_static::size_to_perc($sizes['m_w']));
		$m_thb_h = ceil($this->mobile_treshold * mg_static::size_to_perc($sizes['m_h']));
		
		// where to pick image
		$subj_post_id = ($this->item_type != 'post_contents' || ($this->item_type == 'post_contents' && get_post_meta($this->item_id, 'mg_use_item_feat_img', true))) ? $this->item_id : $this->final_post_id;
		
		// thumb url and center
		$img_id = (get_post_type($this->item_id) == 'attachment') ? $subj_post_id : get_post_thumbnail_id($subj_post_id);
		
        if(empty($img_id)) {
            return false;
        }
		$this->item_img_id = $img_id;
		
		// is animated gif? always use original image
		if(mg_static::img_is_gif($img_id)) {
			$desktop_url = 
			$mobile_url = mg_static::img_id_to_fullsize_url($img_id);
		}
		else {
			$thumb_center = (get_post_meta($subj_post_id, 'mg_thumb_center', true)) ? get_post_meta($subj_post_id, 'mg_thumb_center', true) : 'c'; 
			
			// main thumb
			if($sizes['h'] == 'auto') {
                $thb_h = false;
            }
			$desktop_url = mg_static::thumb_src($img_id, $thb_w, $thb_h, $this->thumbs_quality, $thumb_center);
			
			// mobile thumb
			if($sizes['m_h'] == 'auto') {
                $m_thb_h = false;
            }
			$mobile_url = mg_static::thumb_src($img_id, $m_thb_w, $m_thb_h, $this->thumbs_quality, $thumb_center);
		}
		
		return array(
			'desktop' 	=> $desktop_url,
			'mobile'	=> $mobile_url
		);
	}
	
	
	
	/*
	 * Retrieves remote post to be called by "post contents" item
	 * adds fetched post into $queried_items
	 *
	 * @return (bool/int) post ID or false if no post matched the query
	 */
	private function post_contents_get_post() {
		$cpt_tax_arr = explode('|||', get_post_meta($this->item_id, 'mg_cpt_source', true));
		$term = get_post_meta($this->item_id, 'mg_cpt_tax_term', true); 
		
		$args = array(
			'post_type' => $cpt_tax_arr[0],  
			'post_status' => 'publish', 
			'posts_per_page' => 1,
			'offset' => (int)get_post_meta($this->item_id, 'mg_post_query_offset', true),
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
		
		if(count($query->posts)) {
			$post = $query->posts[0];
			$this->queried_items[ $post->ID ] = $post;	
			
			return $post->ID;
		}
		else {return false;}
	}	
		



	/* return inline CSS to be added in mg_box_inner for inline texts */
	private function inl_txt_custom_css() {
		$css = '';
			
		// background and colors
		if(get_post_meta($this->item_id, 'mg_inl_txt_color', true)) {
			$css .= 'color: '.get_post_meta($this->item_id, 'mg_inl_txt_color', true).';';
		}
		if(get_post_meta($this->item_id, 'mg_inl_txt_box_bg', true)) {
			$css .= 'background-color: '.get_post_meta($this->item_id, 'mg_inl_txt_box_bg', true).';';
		}
		
		$css .= ' '. esc_attr( (string)get_post_meta($this->item_id, 'mg_inl_txt_custom_css', true));
		return $css;
	}
	
	
	
	//////////////////////////////////////////////////////
	
	
	
	/* Enqueue term classes into $this->item_classes[], for filters */
	private function enqueue_item_term_classes() {
		if($this->item_type == 'spacer') {
			return false;	
		}
		
		// take Advanced Filters add-on into account - preload mg_item_categories only if standard filters are used
		$preload = ($this->grid_atts['filter'] == 1) ? wp_get_post_terms($this->item_id, 'mg_item_categories') : array();
		
		// MG-FILTER - allows custom terms association for specific items
		$terms = apply_filters('mg_item_cats', $preload, $this->item_id, $this->grid_atts);
		if(is_wp_error($terms)) {
            return false;
        }
		
		
		// fetch data to be used later by grid_filters class
		$dl_match = false;
		foreach((array)$terms as $term) {
			
			$this->item_classes[] 	= ($term->taxonomy == 'mg_item_categories') ? 'mgc_'.$term->term_id : $term->taxonomy.'_'.$term->term_id;
			$this->items_term[]		= $term->term_id;
		
			// push terms data into a global var
			if(!isset($GLOBALS['mg_items_term_db'])) {
                $GLOBALS['mg_items_term_db'] = array();
            }
			$GLOBALS['mg_items_term_db'][ $term->term_id ] = (array)$term;
			
			// if there's a deeplinked category - match
			if(!empty($this->grid_atts['dl_cat'])) {
				if(!$dl_match && $this->grid_atts['dl_cat'] == $term->term_id) {
					$dl_match = true;	
				}
			}
		}
		
		// is checking also deeplinked cat? eventually setup mg_cat_hide class
		if(!empty($this->grid_atts['dl_cat']) && !$dl_match) {
			$this->item_classes[] = 'mg_cat_hide';	
		}
		
		// care global items term array
		$this->items_term = array_unique($this->items_term);
		return $terms;
	}
	
	
	
	//////////////////////////////////////////////////////
	
	
	
	private function throw_notice($text, $file_line) {
		if(isset($_REQUEST['mg_debug'])) {
			trigger_error($text . ' &nbsp; [line '.$file_line.'] &nbsp; ');	
		}
	}
}