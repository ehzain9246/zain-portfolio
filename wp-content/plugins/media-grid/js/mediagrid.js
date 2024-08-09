(function($) {
	"use strict";
    
    if(typeof(window.dike_plc) != 'undefined' && !window.dike_plc('lcweb', lcmg.dike_slug)) {
        console.error('Media Grid - validate the license');
        return false;    
    }
    
    let $mg_lb_contents, 
        mg_get_item_ajax,
        mg_lb_lazyloaded,
        mg_user_interacted; // flag knowing if user interacted with the page, to use videos autoplay with audio
    
    window.$mg_sel_grid = false; // set displayed item's grid id
    
	let mg_muuri_objs 	= {}; // associative array (grid_id => obj) containing muuri objects to perform operations
	let mg_mobile_mode 	= {}; // associative array (grid_id => bool) to know which grid is in mobile mode
	
	let lb_is_shown 	= false; // lightbox shown flag
    let ajax_lb_control = false; // necessary for new AbortController();
	let lb_switch_dir 	= false; // which sense lightbox is switching (prev/next)
	
    window.mg_lb_video_h_ratio = 0.55; // video aspect ratio
    
	let grid_true_ids	= {}; // to avoid useless codes - store IDs related to temp ones 
	let grid_is_shown	= {}; // associative array (grid_id => bool) to know which grid is shown (first items be shown are so)
	let grids_width		= {}; // array (grid_id => size) used to register grid size changes
	
    let mg_grid_pag 	= {}; // associative array (grid_id => int) to know which page the grid is currently displaying
	let mg_fpp_grid_pag = {}; // associative array (grid_id => int) to know which page the grid is currently displaying relatively to paginated filtered results 
    let mg_fpp_base_html= {}; // associative array (grid_id => string) to store the initial pagination HTML to be subsequently alterated
    
	window.mg_grid_filters 	= {}; /* object containing applied filters. NB: filter key is the first class part to use (eg. mg_pag_ or mgc_) 
        (grid_id => array(
            'filter_key' => {
                condition 	: AND / OR (string) - use OR if value is an array 
                val			: the filter value (array) - eg. use [5] to filter category 5 (.mgc_5)
            }
        ) 
       */
	
	let txt_under_h		= {}; // associative array (item_id => val) used to store text under items height for persistent check 
	let items_cache		= {}; // avoid fetching again same item
	
	window.mg_slider_autoplay 	= {}; // array (slider_id => bool) used to know which sliders needs to be autoplayed

    let mg_player_objects 	= {}; // player objects array
	let mg_audio_tracklists = {}; // array of tracklists
	let mg_audio_is_playing = {}; // which track is playing for each player
    
	let mg_deeplinked	= false; // flag to know whether to use history.replaceState
	let mg_hashless_url	= false; // page URL without eventual hashes
	let mg_url_hash		= ''; // URL hashtag
	
	// body/html style vars
	let mg_html_style = ''; 
	let mg_body_style = '';

	// CSS3 loader code
	const mg_loader =
	'<div class="mg_loader">'+
		'<div class="mgl_1"></div><div class="mgl_2"></div><div class="mgl_3"></div><div class="mgl_4"></div>'+
	'</div>';

	// know whether is a touch device
	const mg_mobile_device = (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ? true : false;



	// doc ready - append lightbox codes, manage deeplinks
	$(document).ready(function($) {
		mg_append_lightbox();
		apply_deeplinks(true);
	});
	
	
	
	// dynamic grid initialization
	window.mg_init_grid = function(temp_grid_id, pag) {
		if(!$('#'+temp_grid_id).length) {
            return false;
        }

		grid_true_ids[temp_grid_id] = $('#'+temp_grid_id).data('grid-id');
		grid_is_shown[temp_grid_id] = false;

		mg_grid_pag[temp_grid_id] = pag;
		grid_setup(temp_grid_id);
	};
	window.mg_async_init = function(grid_id, pag) { // retrocompatibility
        mg_init_grid(grid_id, pag);
    }; 



	// layout and execute grid
	const grid_setup = function(grid_id) {
		evenize_grid_w(grid_id, true);
		mg_pagenum_btn_vis(grid_id);
        
        if(lcmg.show_filter_match) {
		  mg_matches_per_filter(grid_id);
        };
        
		item_img_switch(grid_id);
		
		// hook to perform actions right before items showing
		$(window).trigger('mg_pre_grid_init', [grid_id]);	
		
		// allow CSS propagation
		setTimeout(function() {
			mg_txt_under_sizer(grid_id, true);
            
			// initialize muuri and the rest
			chitemmuuri(grid_id);
		}, 60);
	};



	// always keep grids to have even width to reduce sizing problems  - ignore grid_id to evenize all
	const evenize_grid_w = function(grid_id, on_init) {
		var $grid = (typeof(grid_id) == 'undefined') ? $('.mg_items_container') : $('#'+grid_id+' .mg_items_container');
		if(!$grid.length) {
            return false;
        }
		
		if($grid.length == 1) {
			if(!$grid.outerWidth() || $grid.outerWidth() % 2 === 0) {
				return true;
			}
			else {
				// toggle mg_not_even_w class?	
				$grid.toggleClass('mg_not_even_w');
				
				if(typeof(on_init) == 'undefined') {
					mg_relayout_grid(grid_id);
				}
			}	
		}
		else {
			$grid.each(function() { 
				evenize_grid_w( $(this).parents('.mg_grid_wrap').attr('id') );
            });
		}
	};


	// switches images URL between desktop and mobile mode - must be used also to set the initial image
	const item_img_switch = function(grid_id, $forced_items, force_switch) {
		var $grid 			= $('#'+grid_id); 
		var first_init 		= ($('#'+grid_id+'.mg_muurified').length) ? false : true;
		var has_forced_items= (typeof($forced_items) != 'object') ? false : true;
		var trigger_action 	= (first_init || has_forced_items) ? false : true;
		
		// get mobile treshold
		var safe_mg_mobile 	= (typeof(lcmg.mobile_thold) == 'undefined') ? 800 : lcmg.mobile_thold;
		if(typeof($('#'+grid_id).attr('data-mobile-treshold')) != 'undefined') {
			safe_mg_mobile = parseInt($('#'+grid_id).data('mobile-treshold'), 10);	
		}

		// find items
		var $items = (has_forced_items) ? $forced_items.find('.mgi_main_thumb') : $('#'+ grid_id +' .mg_box').not('.mg_pag_hide, .mg_cat_hide, .mg_search_hide').find('.mgi_main_thumb');
		
		// get wrapper's width
		var grid_wrap_width = $('#'+grid_id).parent().width();
		

		// zero width - return false
		if(!grid_wrap_width) {
            return false;
        } 
        
        
		// no mobile mode flag? set it to false by deafult
		if(typeof(mg_mobile_mode[grid_id]) == 'undefined') {
            mg_mobile_mode[grid_id] = false;
        }	

		// mobile
		if(grid_wrap_width < safe_mg_mobile && (!mg_mobile_mode[grid_id] || first_init || has_forced_items || force_switch)) {
            $items.each(function() {
                $(this).css('background-image', "url('"+ $(this).data('mobileurl') +"')");
            });

			mg_mobile_mode[grid_id] = true;
			$grid.addClass('mg_mobile_mode');
			
			if(trigger_action) {
				$(window).trigger('mg_mobile_mode_switch', [grid_id]);
			}
			return true;
		}

		// desktop
		if(grid_wrap_width >= safe_mg_mobile && (mg_mobile_mode[grid_id] || first_init || has_forced_items || force_switch)) {
            $items.each(function() {
                $(this).css('background-image', "url('"+ $(this).data('fullurl') +"')");
            });
			
			mg_mobile_mode[grid_id] = false;
			$grid.removeClass('mg_mobile_mode');
			
			if(trigger_action) {
				$(window).trigger('mg_mobile_mode_switch', [grid_id]);
			}
			return true;
		}
	};
	
	
	// "read" texts under height and manage items to be properly arranged
	const mg_txt_under_sizer = function(grid_id, relayout) {
		const $items = $('#'+ grid_id +' .mg_grid_title_under .mg_has_txt_under');
        
        if(!$items.length) {
            return true;    
        }
        
        $items.each(function() {
			var $item = $(this);
			var iid = $item.attr('id'); 
			
			var old_val = (typeof( txt_under_h[iid] ) == 'undefined') ? false : txt_under_h[iid];
			var new_val = $item.find('.mgi_txt_under').outerHeight(true);
			
			if(old_val === false || old_val != new_val) {
				txt_under_h[iid] = new_val;
				$item.css('margin-bottom', new_val);
			}
		});
		
		if(typeof(relayout) != 'undefined') {
			mg_relayout_grid(grid_id);	
		}
	};
	

	
	////////////////////////////////////////////////////
	
	
	
	const hide_grid_loader = function(grid_id) {
		$('#'+ grid_id +' .mg_items_container').stop().fadeTo(300, 1);
		$('#'+grid_id).find('.mg_loader').stop().fadeOut(300);
	};
	
	
	const show_grid_loader = function(grid_id) {
		$('#'+ grid_id +' .mg_items_container').stop().fadeTo(300, 0.25);
		$('#'+grid_id).find('.mg_loader').stop().fadeIn(300);
	};
	
	
	
	// God bless Muuri
	const chitemmuuri = function(grid_id) {
		if(!$('#'+ grid_id +' .mg_items_container').length || $('#'+ grid_id +'.mg_muurified').length) {
			return false;	
		}
		
		mg_muuri_objs[grid_id] = new Muuri( $('#'+ grid_id +' .mg_items_container')[0] , {
			items					: $('#'+ grid_id +' .mg_items_container')[0].getElementsByClassName('mg_box'),
			containerClass			: 'mg-muuri',
			itemClass				: 'mg-muuri-item',
			itemVisibleClass		: 'mg-muuri-shown',
			itemHiddenClass			: 'mg-muuri-hidden',
			layoutOnResize			: false,
			
			layout					: {
				fillGaps 	: true,
				alignRight 	: lcmg.rtl,
				rounding	: false
			},
				
			layoutDuration	: 450,
  			layoutEasing	: 'ease-out',	
				
			showDuration	: 450,
			showEasing 		: 'ease-out',
			
			hideDuration	: 450,
			hideEasing		: 'ease-out',	
				
			visibleStyles	: {
				opacity: '1',
				transform: 'scale(1)'
			},
			hiddenStyles	: {
				opacity: '0',
				transform: 'scale(0.5)'
			}
		});
		
		$('#'+ grid_id).addClass('mg_muurified');
		
		// run filters - second parameter allows preload and show items
		mg_exec_filters(grid_id, true);
	};
	
	
	// recall muuri to layout again grid elements - ignore grid_id  to relayout all  
	const mg_relayout_grid = function(grid_id) {

		// layout everything or just one?
		if(typeof(grid_id) == 'undefined') {
			$('.mg_muurified').each(function() { 
				mg_relayout_grid( $(this).attr('id') );
            });
		} 
		else {
			if(typeof(mg_muuri_objs[grid_id]) != 'undefined') {
				mg_muuri_objs[grid_id].refreshItems();
				mg_muuri_objs[grid_id].layout(true);	
			}
		}
	};
	
	
	
	// track grids width size change - persistent interval
	$(document).ready(function() {
		setInterval(function() {
			$('.mg_grid_wrap').each(function() {
                var gid = $(this).attr('id');
				var new_w = Math.round($(this).width());
				
				if(typeof(grids_width[gid]) == 'undefined') {
					grids_width[gid] = new_w;	
					return true;
				}
				
				// trigger only if size is different
				if(grids_width[gid] != new_w) {
					grids_width[gid] = new_w;
					
					if(new_w) {
						$(window).trigger('mg_resize_grid', [gid]);		
					}
				}
            });
		}, 210);
	});
	
	// standard MG operations on resize
	$(window).on('mg_resize_grid', function(e, grid_id) {
		
		// if not initialized (eg. tabbed grids) - init now
		if(!$('#'+grid_id+'.mg_muurified').length) {
			grid_setup(grid_id);	
		}
		else {
			mg_relayout_grid(grid_id);
			item_img_switch(grid_id, false, true);
			evenize_grid_w(grid_id);
			
            mg_pagenum_btn_vis(grid_id);
            mg_pagenum_btn_vis(grid_id, true);

			mg_txt_under_sizer(grid_id);
			mg_responsive_txt(grid_id);	
		}
	});
	
	
	
	////////////////////////////////////////////////////
	
	

	// loads only necessary items (passed via $items) and triggers mg_display_boxes()
	const mg_maybe_preload = function(grid_id, $items, callback) {
		mg_responsive_txt(grid_id);
		
		// hide "no items" message
		$('#'+grid_id +'.mg_no_results').removeClass('mg_no_results');
		var $subj = $items;
		
		
		// if no items have a featured image or everything is ready - show directly
		if(!$subj.not('.mgi_ready, .mg_inl_slider, .mg_inl_text').find('.mgi_main_thumb').length) {
			$subj.mg_display_boxes(grid_id);
			
			if(typeof(callback) == 'function') {
				callback.call();	
			}
		}
		
		// otherwise preload images first
		else {
			if($('#'+grid_id +' .mg_loader').is(':hidden')) {
				show_grid_loader(grid_id);	
			}
			
			// trick to use preloader without tweaks - simulate img tags
			var $preload_wrap = $('<div></div>');
			$subj.not('.mgi_ready').find('.mgi_main_thumb').each(function() {
            	var src = (mg_mobile_mode[grid_id]) ? $(this).attr('data-mobileurl') : $(this).attr('data-fullurl');
				$preload_wrap.append('<img src="'+ src +'" />');  
            });
			
			lc_lazyload($preload_wrap.find('img'), {
                
				allLoaded: function(imgs_data) {
                    $subj.mg_display_boxes(grid_id);
					
					if(typeof(callback) == 'function') {
						callback.call();	
					}
				}
			});
		}
	};
	
	
	
	// show boxes, initializing players and sliders
	$.fn.mg_display_boxes = function(grid_id) {
		var $boxes = this;
		var grid_initiated = (grid_is_shown[grid_id]) ? true : false;
		
		hide_grid_loader(grid_id);
		
		var a = 0;
		var delay = (lcmg.delayed_fx && !grid_is_shown[grid_id]) ? 170 : 0; // no delay if grid is already shown
		var total_delay = this.length * delay;
		
		$boxes.each(function(i, v) {
			var $subj = $(this);
			var true_delay = delay * a;
			
			// mark items as managed
			$subj.addClass('mgi_ready');
			
			// show
			setTimeout(function() {
				$subj.addClass('mgi_shown');

				// keburns effects - init
                if($subj.hasClass('mg_to_kenburn')) {
				    $subj.find('.mgi_main_thumb').mg_kenburns();
                }

				// inline slider - init
				if( $subj.hasClass('mg_inl_slider') ) {
					var sid = $subj.find('.mg_inl_slider_wrap').attr('id');
					mg_inl_slider_init(sid);
				}
				
				// inline video - init and eventually autoplay
				if($subj.find('.mg_self-hosted-video').length) {
					var pid = '#' + $subj.find('.mg_sh_inl_video').attr('id');
					mg_video_player(pid, true);
					
					var inl_player = true; 
				}

				// webkit fix for inline vimeo/youtube fullscreen mode + avoid bounce back on self-hosted fullscreen mode
				if( $subj.hasClass('mg_inl_video') && !$subj.find('.mg_sh_inl_video').length) {
					if(navigator.userAgent.indexOf('Chrome/') != -1 || navigator.appVersion.indexOf("Safari/") != -1) {
						setTimeout(function() {
							$subj.find('.mg_shadow_div').css('transform', 'none').css('animation', 'none').css('-webkit-transform', 'none').css('-webkit-animation', 'none').css('opacity', 1);				
						}, 350);
					}	
				}

				// inline audio - init and show
				if( $subj.hasClass('mg_inl_audio') && $subj.find('.mg_inl_audio_player').length ) {
					setTimeout(function() {
						var pid = '#' + $subj.find('.mg_inl_audio_player').attr('id');
						init_inl_audio(pid);
					}, 350);
						
					var inl_player = true; 
				}
				
				// inline text with video bg - init
				if( $subj.find('.mg_inl_txt_video_bg').length ) {
					var video = $subj.find('.mg_inl_txt_video_bg')[0];
					video.currentTime = 0;
					video.play();
				}
				
			}, true_delay);
			
			a++;
		});
		
		
		// actions after grid is fully shown
		setTimeout(function() {
			
			// actions on very first grid showing
			if(!grid_initiated) {
				grid_is_shown[grid_id] = true;
				$('#'+ grid_id +' .mg_no_init_loader').removeClass('mg_no_init_loader');
				
				// remove initial classes and manage everything with muuri
				$('#'+ grid_id).addClass('mgi_shown');

				// add an hook for custom actions
				$(window).trigger('mg_grid_shown', [grid_id]);
			}	
			
			// fix fucking webkit rendering bug
			webkit_blurred_elems_fix(grid_id);		
		}, total_delay);
		
		
		// boxes are ready - trigger action passing grid id, managed items and grid_initiated boolean
		$(window).trigger('mg_items_ready', [grid_id, $boxes, grid_initiated]);
		return true;
	};
	
	
	
	//////////////////////////////////////////////////////////////////////////

	
	
	// EXECUTE FILTERS
	//// elaborates filters and applied the "mg_filtered" class to be used by muuri - reads values from mg_grid_filter 
	window.mg_exec_filters = function(grid_id, on_init) {
		const $grid = $('#'+grid_id),
              filtered_per_page = parseInt($grid.data('filtered-per-page'), 10);
            
		
		if(typeof(mg_grid_filters[grid_id]) != 'object' || $grid.hasClass('mg_is_filtering')) {
			return false;
		}
		var mgf = mg_grid_filters[grid_id];
		
		// reset
		$grid.addClass('mg_is_filtering');
		$grid.find('.mg_no_results').removeClass('mg_no_results');
		$grid.find('.mg_box').removeClass('mg_filtered mg_hidden_pag');
		
		// find items to be shown
		var $all_items = $('#'+grid_id +' .mg_box');
		var $items = $all_items;
		
		
		// ignore pagination?
		if(!lcmg.monopage_filter && Object.keys(mgf).length > 1 && typeof(mgf['mg_pag_']) != 'undefined') {
			$grid.find('.mg_pag_wrap').fadeOut(400); // hide pagination wrap	
			var ignore_pag = true;
		} else {
			$grid.find('.mg_pag_wrap').fadeIn(400); // hide pagination wrap	
			var ignore_pag = false;
		}
		
		
		// filter style (reduce opacity only on 1-page grids or if calculating pagination)
		var behav = 'standard';
		if(lcmg.filters_behav != 'standard') {
			if(!$grid.find('.mg_pag_wrap').length || lcmg.monopage_filter) {
				behav = lcmg.filters_behav;	
			}
		}
			

		// filter
		var keys = Object.keys(mg_grid_filters[grid_id]);	

		$.each(keys, function(i, key) {
			var data = mg_grid_filters[grid_id][key];
			if(typeof(data.val) != 'object' || !data.val.length || typeof(data.condition) == 'undefined') {
                return true;
            }

			// trick to filter on every page
			if(ignore_pag && key == 'mg_pag_') {
                return true;
            }

			// AND condition
			if(data.condition == 'AND') {
				var selector = ''; 	
				$.each(data.val, function(i,v) {
					selector += '.'+ key + v;
				});
			}
			
			// OR condition
			else {
				var selector = []; 	
				$.each(data.val, function(i,v) {
					selector.push( '.'+ key + v);
				});
				selector = selector.join(' , ');
			}
			
			
			//console.log(selector); // debug 
			$items = $items.filter(selector);
			
			
			// if filtering by page - add another class for excluded ones
			if(key == 'mg_pag_') {
				$all_items.not(selector).addClass('mg_hidden_pag');	
			}
		});
		
		// class flagging remaining items 
		$items.addClass('mg_filtered');
		var $shown_items = (behav == 'standard') ? $items : $all_items.not('.mg_hidden_pag');
		
		// which class to use with muuri?
		var muuri_filter = (behav == 'standard') ? '.mg_filtered' : '*:not(.mg_hidden_pag)';

		// switch image for shown items
		item_img_switch(grid_id, $shown_items);
		
		
		////
		// opacity filters - use JS
		if(behav != 'standard') {
			var opacity_val = (behav == '0_opacity') ? 0 : 0.4;
			$all_items.not('.mg_filtered').addClass('mgi_low_opacity_f').fadeTo(450, opacity_val);
			$items.removeClass('mgi_low_opacity_f').fadeTo(450, 1);
		}
		////
		
        
        // filtered items pagination
        if(set_filtered_items_pagination(grid_id, $grid, keys, filtered_per_page, muuri_filter)) {
            $shown_items = $shown_items.filter(':not(.mg_fpp_hidden)');
            muuri_filter += ':not(.mg_fpp_hidden)';
        }

        
		// on grid init - just set classes and trigger preload
		if(typeof(on_init) != 'undefined') {
			$grid.find('.mg_items_container').removeClass('mgic_pre_show');
			
			mg_maybe_preload(grid_id, $shown_items);	
			mg_muuri_objs[grid_id].filter(muuri_filter);	
			
			mg_filter_no_results(grid_id);
			$grid.removeClass('mg_is_filtering');
		}
		
		
		// otherwise be sure items are ready before filtering
		else {
			mg_maybe_preload(grid_id, $shown_items, function() {
				
				if(typeof(mg_muuri_objs[grid_id]) != 'undefined') {
                    mg_muuri_objs[grid_id].filter(muuri_filter);
					
					$grid.removeClass('mg_is_filtering');
					 
					// trigger action to inform that items are filtered (and new ones could be shown)
					$(window).trigger('mg_filtered_grid', [grid_id]);
                    
                    // adjust for text under and "no result" text
                    setTimeout(function() {
                        mg_txt_under_sizer(grid_id, true);
                        mg_filter_no_results(grid_id);
                    }, 85); 
				}
			});
			
			// pause hidden players and sliders (be sure to use it after maybe_preload() )
			mg_pause_inl_players(grid_id);
		}
	};
	
    
    
    // setup filtered items pagination 
    const set_filtered_items_pagination = function(grid_id, $grid, filter_keys, per_page, muuri_filter) {
        const $filtered = $grid.find('.mg_box.mg_filtered'),
              $fpp_wrap = $grid.find('.mg_fpp_pag_wrap');
        
        if(!$fpp_wrap.length) {
            return false;    
        }
        
        if(typeof(mg_fpp_grid_pag[grid_id]) == 'undefined') {
            mg_fpp_grid_pag[grid_id] = 1;       
            mg_fpp_base_html[grid_id] = $fpp_wrap.html();
        }
        
        $filtered.removeClass('mg_fpp_hidden');
        mg_fpp_grid_pag[grid_id] = 1;
        $fpp_wrap.attr('data-init-pag', 1);
        
        if(
            per_page &&
            $filtered.length > per_page &&
            (($.inArray('mg_pag_', filter_keys) === -1 && filter_keys.length) ||
            ($.inArray('mg_pag_', filter_keys) !== -1 && filter_keys.length > 1)) 
        ) {  
            const tot_pag = Math.ceil($filtered.length / per_page);
            
            $fpp_wrap.attr('data-tot-pag', tot_pag);
            $fpp_wrap.find('.mg_fpp_tot_pag').text(tot_pag);
            
            if($fpp_wrap.find('.mg_fpp_pag_btn').length) {
                $fpp_wrap.html( mg_fpp_base_html[grid_id]);
                
                let template_code = $fpp_wrap.find('.mg_fpp_pag_btn')[0].outerHTML.replace('mg_sel_pag', '');
                
                for(let a = 1; a < tot_pag; a++) {
                    $fpp_wrap.append( template_code.replace(/[1]/g, (a + 1)) );        
                }
                
                mg_pagenum_btn_vis(grid_id, true);
            }
            
            else {
                $fpp_wrap.find('.mg_fpp_curr_pag').text(1);
                $fpp_wrap.find('.mg_prev_page').addClass('mg_pag_disabled');
                $fpp_wrap.find('.mg_next_page').removeClass('mg_pag_disabled');
                
                if($fpp_wrap.hasClass('mg_inf_scroll')) {
                    $grid[0].style.paddingBottom = '';
                }
            }
            
            $filtered.slice(per_page).addClass('mg_fpp_hidden');
            $fpp_wrap.fadeIn(200);
        }
        else {
            $grid.find('.mg_fpp_pag_wrap').hide();
        }
        
        return true;
    };
    
    
	
	// shown items count - toggle "no results" box
	const mg_filter_no_results = function(grid_id) {
		if($('#'+ grid_id +' .mg-muuri-shown').length) {
			$('#'+ grid_id +' .mg_items_container').removeClass('mg_no_results');
		} else {
			$('#'+ grid_id +' .mg_items_container').addClass('mg_no_results');
		}
	};
	
	
	// dropdown filters management
    let mg_dd_toggle_timeout;
	$(document).on('click', '.mg_mobile_mode .mg_dd_mobile_filters .mgf_inner', function(e) {
        mg_user_interacted = true;
        
		var $this = $(this);
		if(mg_dd_toggle_timeout) {
            clearTimeout(mg_dd_toggle_timeout)
        }
		
		mg_dd_toggle_timeout = setTimeout(function() {
			$this.toggleClass('mgf_dd_expanded');
		}, 50);
	});
	
	
	
	//////////////////////////////////////////////////////////////////////////
	
	
	
	// PAGINATE ITEMS
	$(document).ready(function() {
		
		// prev/next buttons
		$(document).on('click', '.mg_next_page:not(.mg_pag_disabled), .mg_prev_page:not(.mg_pag_disabled)', function() {
            mg_user_interacted = true;

            var cmd = ($(this).hasClass('mg_next_page')) ? 'next' : 'prev';

            if($(this).parents('.mg_fpp_pag_wrap').length) {
                mg_fpp_paginate(cmd, $(this).parents('.mg_grid_wrap').attr('id') );
            } else {
                mg_paginate(cmd, $(this).parents('.mg_grid_wrap').attr('id') );        
            }
		});
		
		
		// page buttos and dots
		$(document).on('click', '.mg_pag_btn_nums > div:not(.mg_sel_pag), .mg_pag_btn_dots > div:not(.mg_sel_pag)', function() {
			mg_user_interacted = true;
            
            var pag = $(this).data('pag');
			var grid_id = $(this).parents('.mg_grid_wrap').attr('id'); 
			
			$(this).parents('.mg_pag_wrap').find('> div').removeClass('mg_sel_pag');
			$(this).addClass('mg_sel_pag');
			
            if($(this).parents('.mg_fpp_pag_wrap').length) {
                mg_pagenum_btn_vis(grid_id, true);
                mg_fpp_paginate(pag, grid_id);
            }
            else {
                mg_pagenum_btn_vis(grid_id);
                mg_paginate(pag, grid_id);        
            }
		});
	});
	
    
    
    // perform items pagination - direction accepts "next" / "prev" or the page number
	const mg_paginate = function(direction, grid_id) {
		var temp_gid = grid_id;
		var gid = $('#'+temp_gid).data('grid-id');

		var tot_pags = parseInt($('#mgp_'+temp_gid).data('tot-pag'), 10);
		var curr_pag =  parseInt(mg_grid_pag[temp_gid], 10);

		
		// next/prev case
		if($.inArray(direction, ['next', 'prev']) !== -1) {
			if( // ignore in these cases
				(direction == 'next' && curr_pag >= tot_pags) ||
				(direction == 'prev' && curr_pag <= 1)
			) {
				return false;	
			}
			
			// update pag vars
			var new_pag = (direction == 'next') ? curr_pag + 1 : curr_pag - 1;	
		}

		// direct pagenum submission
		else {
			var new_pag = parseInt(direction);
			if(new_pag < 1 || new_pag > tot_pags || new_pag == curr_pag) {
				return false;	
			}
		}

		
		// set class
		mg_grid_pag[temp_gid] = new_pag;	
		
		// set/remove deeplink
		if(new_pag == 1) {
			mg_remove_deeplink('page' ,'mgp_'+gid);
		} else {
			mg_set_deeplink('page', 'mgp_'+gid, new_pag);
		}
		
		// manage disabled class
		if(new_pag == 1) {
			$('#mgp_'+temp_gid+' .mg_prev_page').addClass('mg_pag_disabled');
		} else {
			$('#mgp_'+temp_gid+' .mg_prev_page').removeClass('mg_pag_disabled');
		}
		
		if(new_pag == tot_pags) {
			$('#mgp_'+temp_gid+' .mg_next_page').addClass('mg_pag_disabled');
		} else {
			$('#mgp_'+temp_gid+' .mg_next_page').removeClass('mg_pag_disabled');
		}
		
		// manage current pag number if displayed
		if($('#mgp_'+temp_gid+' .mg_nav_mid span').length) {
			$('#mgp_'+temp_gid+' .mg_nav_mid span').text(new_pag);	
		}
		
		
		// update filter
		mg_grid_filters[ temp_gid ]['mg_pag_'] = {
			condition 	: 'AND',
			val			: [new_pag]
		};
		mg_exec_filters(temp_gid);
		
		
        // fix overlays issues for previously hidden items
        $(window).trigger('mg_resize_grid', [temp_gid]);
        
		// move to grids top
		if(lcmg.scrolltop_on_pag) {
            $('html, body').animate({'scrollTop': $('#'+temp_gid).offset().top - 15}, 300);
        }
	};
    
    
	
    // perform filtered items pagination - direction accepts "next" / "prev" or the page number
	const mg_fpp_paginate = function(direction, grid_id) {
		if($('#'+grid_id).hasClass('mg_is_filtering')) {
			return false;	
		}
		
		let temp_gid    = grid_id,
            $grid       = $('#'+temp_gid),
            gid         = $grid.data('grid-id'),
            $filtered   = $grid.find('.mg_box.mg_filtered'),
            $fpp_wrap   = $grid.find('.mg_fpp_pag_wrap'),
            tot_pags    = parseInt($fpp_wrap.attr('data-tot-pag'), 10),
            curr_pag    = mg_fpp_grid_pag[temp_gid],
            per_page    = $grid.data('filtered-per-page');
        
		// next/prev case
		if($.inArray(direction, ['next', 'prev']) !== -1) {
			if( // ignore in these cases
				(direction == 'next' && curr_pag >= tot_pags) ||
				(direction == 'prev' && curr_pag <= 1)
			) {
				return false;	
			}
			
			// update pag vars
			var new_pag = (direction == 'next') ? curr_pag + 1 : curr_pag - 1;	
		}

		// direct pagenum submission
		else {
			var new_pag = parseInt(direction);
			if(new_pag < 1 || new_pag > tot_pags || new_pag == curr_pag) {
				return false;	
			}
		}

        // set new current page
		mg_fpp_grid_pag[temp_gid] = new_pag;	
		$fpp_wrap.find('.mg_fpp_curr_pag').text(new_pag);

        // manage disabled class
		if(new_pag == 1) {
			$fpp_wrap.find('.mg_prev_page').addClass('mg_pag_disabled');
		} else {
			$fpp_wrap.find('.mg_prev_page').removeClass('mg_pag_disabled');
		}

        // manage shown currpag number
		if(new_pag == tot_pags) {
			$fpp_wrap.find('.mg_next_page').addClass('mg_pag_disabled');
		} else {
			$fpp_wrap.find('.mg_next_page').removeClass('mg_pag_disabled');
		}

        // manage hiding class
        $filtered.addClass('mg_fpp_hidden');
        
        const range_from = ((new_pag - 1) * per_page),
              range_to   = range_from + per_page;
        
        $filtered.slice(range_from, range_to).removeClass('mg_fpp_hidden');
		
        mg_maybe_preload(grid_id, $filtered, function() {
            mg_muuri_objs[grid_id].filter('.mg_filtered:not(.mg_fpp_hidden)');
        });
		
        // fix overlays issues for previously hidden items
        $(window).trigger('mg_resize_grid', [temp_gid]);
        
		// move to grids top
		$('html, body').animate({'scrollTop': $('#'+temp_gid).offset().top - 15}, 300);
	};
    
    
	
	// track grid's width and avoid pagenum and dots to go on two lines
	const mg_pagenum_btn_vis = function(grid_id, filtered_pag_context = false) {
		if(!$('#'+grid_id).find('.mg_pag_btn_nums, .mg_pag_btn_dots').length) {
			return false;	
		}

		var $pag_wrap   = (filtered_pag_context) ? $('#'+grid_id).find('.mg_fpp_pag_wrap') : $('#'+grid_id).find('.mg_pag_wrap:not(.mg_fpp_pag_wrap)'),
            $btns       = $pag_wrap.find('> div');

		// reset
		$pag_wrap.removeClass('mg_hpb_after mg_hpb_before');
		$btns.removeClass('mg_hidden_pb');
        
		// there must be at least 5 buttons
		if($btns.length <= 5) {
            return false;
        }
		
		
		// calculate overall btns width
		var btns_width = 0;
		$btns.each(function() {
            btns_width += $(this).outerWidth(true) + 1; // add 1px to avoid any issue
        });  
		

		// act if is wider
		if(btns_width > $pag_wrap.outerWidth()) {
			var $sel_btn = $('#'+grid_id+' .mg_sel_pag');
			var curr_pag = parseInt($sel_btn.data('pag'));
			var tot_pages = parseInt($btns.last().data('pag'));
			
			// count dots width
			var dots_w = (curr_pag <= 2 || curr_pag >= (tot_pages - 1)) ? 26 : 52; // width = 16px + add 10px margin
			
			var diff = btns_width + dots_w - $pag_wrap.outerWidth() ;
			var last_btn_w = $btns.last().outerWidth(true);
			var to_hide = Math.ceil( diff / last_btn_w );

			// manage pag btn visibility
			if(curr_pag <= 2 || curr_pag >= (tot_pages - 1)) {
			var to_hide_sel = [];
			
				if(curr_pag <= 2) {
					$pag_wrap.addClass('mg_hpb_after');		
					
					for(let a=0; a < to_hide; a++) {
						to_hide_sel.push('[data-pag='+ (tot_pages - a) +']');	
					}
				}
				else if( curr_pag >= (tot_pages - 1)) {
					$pag_wrap.addClass('mg_hpb_before');	
					
					for(let a=0; a < to_hide; a++) {
						to_hide_sel.push('[data-pag='+ (1 + a) +']');	
					}
				}
				
				$btns.filter( to_hide_sel.join(',') ).addClass('mg_hidden_pb');
			}
			
			else {
				$pag_wrap.addClass('mg_hpb_before mg_hpb_after');	
				var to_keep_sel = ['[data-pag='+ curr_pag +']'];
				
				// use opposite system: selected is the center and count how to keep 
				var to_keep = (tot_pages - 1) - to_hide;

				var to_keep_pre = Math.floor( to_keep / 2 );
				var to_keep_post = Math.ceil( to_keep / 2 );
				
				// if pre/post already reaches the edge, sum remaining ones on the other side
				var reach_pre = curr_pag - to_keep_pre;
				var reach_post = curr_pag + to_keep_post;
				
				if(reach_pre <= 1) {
					$pag_wrap.removeClass('mg_hpb_before');	
					to_keep_post = to_keep_post + (reach_pre * -1 + 1);	
				}
				else if(reach_post >= tot_pages) {
					$pag_wrap.removeClass('mg_hpb_after');	
					to_keep_pre = to_keep_pre + (reach_post - (tot_pages - 1));	
				}
				
				for(let a=1; a <= to_keep_pre; a++) {
					to_keep_sel.push('[data-pag='+ (curr_pag - a) +']');	
				}
				for(let b=1; b <= to_keep_post; b++) {
					to_keep_sel.push('[data-pag='+ (curr_pag + b) +']');	
				}
				
				$btns.not( to_keep_sel.join(',') ).addClass('mg_hidden_pb');
			}	
		}
	};
	
	
	//////
	
	
	// Infinite Scroll
	$(document).ready(function() {
		$(document).on('click', '.mg_load_more_btn', function() {
			mg_user_interacted = true;
            
            let grid_id         = $(this).parents('.mg_grid_wrap').attr('id'),
                is_filtered_pag = ($(this).parents('.mg_fpp_pag_wrap').length) ? true : false,
                $pwrap          = (is_filtered_pag) ? $('#'+grid_id).find('.mg_fpp_pag_wrap') : $('#'+grid_id).find('.mg_pag_wrap:not(.mg_fpp_pag_wrap)'),
                curr_pag        = parseInt($pwrap.attr('data-init-pag'), 10),
                tot_pags        = parseInt($pwrap.attr('data-tot-pag'), 10);
			
			if((!$pwrap.hasClass('mg_fpp_pag_wrap') && $('#'+grid_id).hasClass('mg_is_filtering')) || curr_pag >= tot_pags) {
				return false;	
			}
			
			var new_pag = curr_pag + 1;
			$pwrap.attr('data-init-pag', new_pag);
            
            
            if(!is_filtered_pag) {
            
                // filter showing every page until now
                var filter_val = [];
                for(let a = 1; a <= new_pag ; a++) {
                    filter_val.push(a);
                }

                mg_grid_filters[ grid_id ]['mg_pag_'] = {
                    condition 	: 'OR',
                    val			: filter_val
                };
                mg_exec_filters(grid_id);
			
            }
            
            else {
                let $grid     = $('#'+ grid_id),
                    $filtered = $grid.find('.mg_box.mg_filtered'),
                    per_page  = $grid.data('filtered-per-page');

                // manage hiding class
                let range_to      = new_pag * per_page,
                    $selection    = (range_to > $filtered.length) ? $filtered : $filtered.slice(0, range_to); 
                
                $filtered.addClass('mg_fpp_hidden');
                $selection.removeClass('mg_fpp_hidden');
                
                mg_muuri_objs[grid_id].filter('.mg_filtered:not(.mg_fpp_hidden)');

                // fix overlays issues for previously hidden items
                $(window).trigger('mg_resize_grid', [grid_id]);    
            }
            
                
			// reached final page? hide button
			if(new_pag >= tot_pags) {
				$pwrap.fadeOut(300, function() {
					$('#'+grid_id).animate({paddingBottom : 0}, 400);	
					
                    if(!is_filtered_pag) {
                        $pwrap.remove();	
                    }
				});
			}
		});
		
		
		// automatic Infinite scroll
		$(window).scroll(function() {
			var wS = $(this).scrollTop();
			
            $('.mg_auto_inf_scroll:not(:hidden) .mg_load_more_btn').each(function() {
                if($(this).parents('.mg_fpp_pag_wrap').length) {
                    return true;       
                }

                var $aif_subj = $(this); 

                var hT = $aif_subj.offset().top,
                    hH = $aif_subj.outerHeight(),
                    wH = $(window).height();

                if (wS > (hT+hH-wH)){
                    $aif_subj.trigger('click');
                }
            });
		});
	});
	
	
	
	///////////////////////////////////////////////
	


	// items category filter
	$(document).ready(function() {
		$(document).on('click', '.mgf:not(.mgf_selected)', function(e) {
			e.preventDefault();
            mg_user_interacted = true;
            
			var $grid = $(this).parents('.mg_grid_wrap');
			var temp_gid = $grid.attr('id'); 
			var gid = $grid.data('grid-id');
			var sel = $(this).data('filter-id');
			var txt = $(this).text();
			
			// already filtering? stop
			if($grid.hasClass('mg_is_filtering') ) {
                return false;
            }

			// button selection manag
			$grid.find('.mgf').removeClass('mgf_selected');
			$(this).addClass('mgf_selected');
			
			// no filter - clear filtering db and deeplink
			if(!sel || sel == '*') {
				delete mg_grid_filters[ temp_gid ]['mgc_'];
				mg_remove_deeplink('category', 'mgc_'+gid);
			}
			
			// filter selected - update db and deeplink
			else {
				mg_grid_filters[ temp_gid ]['mgc_'] = {
					condition 	: 'AND',
					val			: [sel]
				};
				mg_set_deeplink('category', 'mgc_'+gid, sel, txt);
			}
				
			mg_exec_filters(temp_gid);
			
			
			// mgf_noall_placeh removal
			if($grid.find('.mgf_noall_placeh').length) {
				$grid.find('.mgf_noall_placeh').remove();	
			}
			
            
            // close mobile dropdown
            if($(this).parents('.mgf_dd_expanded').length) {
                $(this).parents('.mgf_dd_expanded').removeClass('mgf_dd_expanded');        
            }
			return false;
		});
	});
    
    
    
    // items counter per filter
    window.mg_matches_per_filter = function(grid_id) {
        const $grid = $('#'+grid_id);
        
        // default filters
        if($grid.find('.mgf_inner .mgf').length) {
            $grid.find('.mgf').not('.mgf_all').each(function() {
                const fid = $(this).data('filter-id');
                if(!fid || $(this).find('.mg_filter_count').length) {
                    return;
                }
                
                $(this).append('<font class="mg_filter_count">'+ $grid.find('.mg_box.mgc_'+ fid).length +'</font>');
            });                
        }
        
        // MGAF
        else if($grid.find('.mgaf_sect').length) {
            $grid.find('.mgaf_sect:not([data-type="range"])').each(function() { 
                const class_prefix = ($(this).data('sect') == 'mg_item_categories') ? '.mgc_' : '.'+ $(this).data('sect') +'_';
                
                $(this).find('.mgaf_opts_list li:not(.mgaf_fake_dd_search), .mgaf_fdd_opt').each(function() {
                    if($(this).hasClass('mgc_reset_filter')) {
                        return;    
                    }
                    
                    const fid = $(this).data('val').substr(4);   
                    if(!fid  || $(this).find('.mg_filter_count').length) {
                        return;
                    }
                    
                    $(this).append('<font class="mg_filter_count">'+ $grid.find('.mg_box'+ class_prefix + fid).length +'</font>');
                });
            });
        }
    };



	///////////////////////////////////////////////
	


	// items search 
    let mg_search_defer;
	$(document).on('keyup', '.mgf_search_form input', function() {
		if(mg_search_defer) {
            clearTimeout(mg_search_defer);
        }
		var $this = $(this); 
		
		mg_search_defer = setTimeout(function() { 
			var $grid = $this.parents('.mg_grid_wrap');
			var temp_gid = $grid.attr('id'); 
			var gid = $grid.data('grid-id');
			var val = $.trim( $this.val() );
			
			// reset class
			$grid.find('.mg_box').removeClass('mg_search_res');
			

			// is searching
			if(val && val.length > 2) {
				$grid.find('.mgf_search_form').addClass('mgs_has_txt');	
				
				// elaborate search string to match items
				var src_arr = (lcmg.search_behav == 'any_word') ? val.toLowerCase().split(' ') : [val.toLowerCase()];
				var matching = [];
	
				// cyle and check each searched term 
				$grid.find('.mg_box:not(.mg_spacer)').each(function() {
					var src_attr = $(this).data('mg-search').toLowerCase();
					var rel = $(this).data('item-id');
					
					$.each(src_arr, function(i, word) {						
						if( src_attr.indexOf(word) !== -1 ) {
							matching.push( rel );
							return false;	
						}
					});
				});
	
				// add class to matched elements
				$.each(matching, function(i, v) {
					$grid.find('.mg_box[data-item-id='+ v +']').addClass('mg_search_res');
				});
				
				
				// set filter engine to match mg_search_res
				mg_grid_filters[ temp_gid ]['mg_search_res'] = {
					condition 	: 'AND',
					val			: ['']
				};
				
				mg_set_deeplink('search', 'mgs_'+gid, val);
			} 
			
			
			// deleting research
			else {
				$grid.find('.mgf_search_form').removeClass('mgs_has_txt');
                $grid.find('.mg_no_results').removeClass('mg_no_results');
                
				delete mg_grid_filters[ temp_gid ]['mg_search_res']; 
				mg_remove_deeplink('search', 'mgs_'+gid);
			}
			
			
			// filter to show results
			mg_exec_filters(temp_gid);
		}, 300);
	});


	// reset search
	$(document).on('click', '.mgf_search_form.mgs_has_txt i', function() {
        mg_user_interacted = true;
        
		var $grid = $(this).parents('.mg_grid_wrap');
		var $input = $grid.find('.mgf_search_form input'); 
		
		if($grid.hasClass('mg_is_filtering')) {
            return false;
        }
		
		if($.trim( $input.val() ) && $input.val().length > 2) {
			$input.val('');
			$input.trigger('keyup');	
		}
	});
	

	// disable enter key
	$(document).on("keypress", ".mgf_search_form input", function(e) { 
		return e.keyCode != 13;
	});
	
    
    // allow link trigger clicking on text under
    $(document).on('click', '.mgi_txt_under', function(e) {
        const $wrap = $(this).parents('.mg_box'),
              $inner = $wrap.find('.mg_box_inner');

        if($(e.target).hasClass('mg_custom_behav_btn') || $(e.target).parents('.mg_custom_behav_btn').length) {
            return true;   
        }
        
        if($inner.is('a')) {
            const target = ($inner[0].hasAttribute('target')) ? $inner.attr('target') : '__self';
            window.open($inner.attr('href'), target);
        }
    });
    

	
	////////////////////////////////////////////
	
	

	// video poster - handle click
	$(document).ready(function() {
		// grid item
		$(document).on('click', '.mg_inl_video:not(.mgi_iv_shown)', function(e){
			var $this = $(this);
            mg_user_interacted = true;
			
			// show overlay on first tap
			if($this.find('.mgi_overlays').length && mg_mobile_device && !$this.hasClass('mg_mobile_hovered')) {
				$this.parents('.mg_grid_wrap').find('.mg_box').removeClass('mg_mobile_hovered');
				$this.addClass('mg_mobile_hovered');
                
				return false;	
			}
			$this.removeClass('mg_mobile_hovered');
			$this.addClass('mgi_iv_shown');
			
			// video iframe
			if($this.find('.mg_video_iframe').length) {
				var autop = $this.find('.mg_video_iframe').data('autoplay-url');
				$this.find('.mg_video_iframe').attr('src', autop).show();
	
				setTimeout(function() { // wait a bit to let iframe populate
					$this.find('.mgi_thumb_wrap, .mgi_overlays').fadeTo(350, 0, function() {
						$this.parents('.mg_video_iframe').css('z-index', 100);
						$(this).remove();
					});
				}, 50);
			}
			
			// self-hosted
			else {
				$this.find('.mgi_thumb_wrap, .mgi_overlays').fadeTo(350, 0, function() {
					$(this).remove();
					
					var pid = '#' + $this.find('.mg_sh_inl_video').attr('id');
					var player_obj = mg_player_objects[pid];
					player_obj.play();
				});
			}
		});

		// lightbox
		$(document).on('click', '#mg_lb_video_poster, #mg_ifp_ol', function(e) {
            mg_user_interacted = true;
            
			var autop = $('#mg_lb_video_poster').data('autoplay-url');
			if(typeof(autop) != 'undefined') {
				$('#mg_lb_video_wrap').find('iframe').attr('src', autop);
			}

			$('#mg_ifp_ol').fadeOut(120);
			$('#mg_lb_video_poster').fadeOut(400);
		});
	});


	// show&play inline audio on overlay click
	$(document).ready(function(e) {
        $(document).on('click', '.mg_box.mg_inl_audio:not(.mgi_ia_shown)', function() {
			var $this = $(this);
            mg_user_interacted = true;
				
			// show overlay on first tap
			if($this.find('.mgi_overlays').length && mg_mobile_device && !$this.hasClass('mg_mobile_hovered')) {
				$this.parents('.mg_grid_wrap').find('.mg_box').removeClass('mg_mobile_hovered');
				$this.addClass('mg_mobile_hovered');
				return false;	
			}
			$this.removeClass('mg_mobile_hovered');
			$this.addClass('mgi_ia_shown');
			
			// external embed media 
			if($this.find('.mg_audio_embed').length) {
				var sc_url = $this.find('.mg_audio_embed').data('lazy-src');
				$this.find('.mg_audio_embed').attr('src', sc_url).removeData('lazy-src');
				
				setTimeout(function() { // wait a bit to let iframe populate
					$this.find('.mgi_overlays').fadeTo(350, 0, function() {
						$this.find('.mg_audio_embed').css('z-index', 100);
						$(this).remove();
					});
				}, 50);
			}
			
			// self-hosted 
			else {
				var player_id = '#' + $this.find('.mg_inl_audio_player').attr('id');
				init_inl_audio(player_id, true);	
				
				$this.find('.mgi_overlays').fadeOut(350, function() {
					$(this).remove();
				});
			}
		});
	});



    
    
    

	//////////////////////////////////////////////////////////////////////////



    
    
    

	/***************************************
	************** LIGHTBOX ****************
	***************************************/


	// append the lightbox code to the website
	const mg_append_lightbox = function() {
        if(typeof(lcmg.lightbox_mode) == 'undefined') {
            return false;    
        }
         
        // deeplinked lightbox - stop here
        if($('#mg_deeplinked_lb').length) {
            $mg_lb_contents = $('#mg_lb_contents');
            $('html').addClass('mg_lb_shown');
            lb_is_shown = true;

            lb_touchswipe_setup();
            return true;
        }


        /// remove existing one
        if($('#mg_lb_wrap').length) {
            $('#mg_lb_wrap, #mg_lb_background').remove();
        }

        $('body').append(`
        <div id="mg_lb_wrap">
            <div id="mg_lb_loader">${ mg_loader }</div>
            <div id="mg_lb_contents" class="mg_lb_pre_show_next"></div>
            <div id="mg_lb_scroll_helper" class="${ lcmg.lightbox_mode }"></div>
        </div>
        <div id="mg_lb_background" class="${ lcmg.lightbox_mode }"></div>`);

        $mg_lb_contents = $('#mg_lb_contents');
        
        // touchswipe
        lb_touchswipe_setup();
	};
    
    
    
    // attaching touchswipe events to lightbox
    const lb_touchswipe_setup = function() {
        if(!lcmg.lb_touchswipe) {
            return false;    
        }
        
        new lc_swiper('#mg_lb_wrap', function(directions, $el, target) {
            const $target = $(target);

            if(
                $target.hasClass('mg_lb_lcms_slider') || $target.parents('.mg_lb_lcms_slider').length ||
                $target.hasClass('mg_is_zooming_img') || $target.parents('.mg_is_zooming_img').length ||
                $target.hasClass('mg_noswipe_el') || $target.parents('.mg_noswipe_el').length
            )  {
                return false;    
            }

            if(directions.right > 60) {
                $('.mg_nav_prev > *').trigger('click');
            } 
            else if(directions.left > 60) {
                $('.mg_nav_next > *').trigger('click');
            }
        });     
    };
    


	// open item trigger
	$(document).ready(function() {
		$(document).on('click', '.mgi_has_lb:not(.mg-muuri-hidden, .mgi_low_opacity_f)', function(e) {
            mg_user_interacted = true;
            
			// elements to ignore -> mgom socials
			var $e = $(e.target);
			if(
				!lb_is_shown && 
				!$e.hasClass('mgom_fb') && !$e.hasClass('mgom_tw') && !$e.hasClass('mgom_pt') && !$e.hasClass('mgom_wa') && 
				!$e.hasClass('mg_quick_edit_btn') && 
				!$e.hasClass('mg_custom_behav_btn')
			) {
				const $subj = $(this);
				
				// show overlay on first tap
				if($subj.find('.mgi_overlays').length && mg_mobile_device && !$subj.hasClass('mg_mobile_hovered')) {
					$subj.parents('.mg_grid_wrap').find('.mg_box').removeClass('mg_mobile_hovered');
					$subj.addClass('mg_mobile_hovered');
					return false;	
				}
				$subj.removeClass('mg_mobile_hovered');
				
				
				const pid = $subj.data('item-id');
				$mg_sel_grid = $subj.parents('.mg_grid_wrap');

                const media_focused_mode = ($mg_sel_grid.hasClass('mg_use_mf_lb')) ? true : false;
				mg_open_item(pid, false, media_focused_mode);
			}
		});
	});

	
	// remove site scrollbar when lightbox is on
	const mg_remove_scrollbar = function() {
		mg_html_style = (typeof($('html').attr('style')) != 'undefined') ? $('html').attr('style') : '';
		mg_body_style = (typeof($('body').attr('style')) != 'undefined') ? $('body').attr('style') : '';
		
		// avoid page scrolling and maintain contents position
		var orig_page_w = $(window).width();
		$('html').css({
			'overflow' 		: 'hidden',
			'touch-action'	: 'none'
		});

		$('body').css({
			'overflow' 		: 'visible',
			'touch-action'	: 'none'	
		});	
		
		$('html').css('margin-right', ($(window).width() - orig_page_w));
	};



	// OPEN ITEM
	window.mg_open_item = function(item_id, deeplinked_lb, media_focused_mode) {
		mg_remove_scrollbar();
        
        if(media_focused_mode) { 
            $('#mg_lb_wrap').addClass('mg_mf_lb');
            $('#mg_lb_wrap').attr('mg_mf_lb', 1);
        }
        else {
            $('#mg_lb_wrap').removeClass('mg_mf_lb');
            $('#mg_lb_wrap').removeAttr('mg_mf_lb');
        }
        
        $('#mg_lb_wrap').removeClass('mg_displaynone'); // for deeplinked lightbox
		$('#mg_lb_wrap').css('display', 'flex');

		// mobile trick to focus lightbox contents
		if($(window).width() < 1000) {
            setTimeout(() => {
                $mg_lb_contents.trigger('click');    
            }, 20);
		}

		// open only if is not deeplinked
		if(typeof(deeplinked_lb) == 'undefined' || !deeplinked_lb) {
			setTimeout(function() {
				$('#mg_lb_loader, #mg_lb_background').addClass('mg_lb_shown');
				mg_get_item_content(item_id, media_focused_mode);
			}, 50);
		}
	};


	// get item's content
	const mg_get_item_content = async function(pid, media_focused_mode, on_item_switch, prepare_it = false) {
		var gid = $mg_sel_grid.attr('id');
		var true_gid = $mg_sel_grid.data('grid-id');
        
        if(!prepare_it) {
            $mg_lb_contents.removeClass('mg_lb_shown'); 
            
            // set attributes to know related grid and item ID
            $('#mg_lb_wrap').data('item-id', pid).data('grid-id', gid);
            
            // set deeplink
            var item_title = $('.mgi_'+pid+' .mgi_main_thumb').data('item-title');
            mg_set_deeplink('item', 'mgi_'+true_gid, pid, item_title);
        }

		// get prev and next items ID to compose nav arrows
		var nav_arr = [];
		var curr_pos = 0;

		$mg_sel_grid.find('.mgi_has_lb').not('.mg-muuri-hidden').each(function(i, el) {
			var item_id = $(this).data('item-id');

			nav_arr.push(item_id);
			if(item_id == pid) {
                curr_pos = i;
            }
		});
		
		// lightbox must include also contextual prev/next switches. Calculate them 
		if(lcmg.lb_carousel) {
			// nav - prev item
			var prev_id = (curr_pos !== 0) ? nav_arr[(curr_pos - 1)] : nav_arr[(nav_arr.length - 1)];
			
			// nav - next item
			var next_id = (curr_pos != (nav_arr.length - 1)) ? nav_arr[(curr_pos + 1)] : nav_arr[0];
		}
		else {
			// nav - prev item
			var prev_id = (curr_pos !== 0) ? nav_arr[(curr_pos - 1)] : 0;
			
			// nav - next item
			var next_id = (curr_pos != (nav_arr.length - 1)) ? nav_arr[(curr_pos + 1)] : 0;
		}
	
        
		// create a static cache id to avoid doubled ajax calls
		var static_cache_id = ''+ prev_id + pid + next_id + media_focused_mode;
	

		// check in static cache
		if(typeof(items_cache[static_cache_id]) != 'undefined') {
            if(prepare_it) {
                return true;
            }
            
            // preload next and prev to navigate faster
            if(typeof(window.mg_not_preload_np_lb_items) == 'undefined' || !window.mg_not_preload_np_lb_items) {
                if(prev_id) {
                    mg_get_item_content(prev_id, media_focused_mode, false, true);
                }
                if(next_id) {
                    mg_get_item_content(next_id, media_focused_mode, false, true);
                }
            }
            
            // show
			var delay = (typeof(on_item_switch) == 'undefined' || on_item_switch || lb_is_shown) ? 320 : 0; // avoid lightbox to be faster than background on initial load

			setTimeout(function() {
				fill_lightbox( items_cache[static_cache_id] );	
                
                // no media-focused mode for custom contents
                if(media_focused_mode) {
                    mf_lb_mode_fix_for_lb_txt();
                }
			}, delay);
		}
		
		// perform ajax call
		else {
            let formData = new FormData();
            formData.append('action', 'mg_lb_contents');
            formData.append('pid', pid);
            
            formData.append('prev_id', prev_id);
            formData.append('next_id', next_id);
            formData.append('mf_mode', (media_focused_mode) ? 1 : 0);

            // perform ajax
            ajax_lb_control = new AbortController();
            return await fetch(
                lcmg.ajax_url,
                {
                    method      : 'POST',
                    credentials : 'same-origin',
                    keepalive   : false,
                    signal      : ajax_lb_control.signal,
                    body        : formData,
                }
            )
            .then(async response => {
                if(!response.ok) {return Promise.reject(response);}
                response = (await response.text());
                
                if(static_cache_id) {
					items_cache[static_cache_id] = response;
				}
				
                if(!prepare_it) {
                    // show
				    fill_lightbox(response);
                    
                    // preload next and prev to navigate faster
                    if(typeof(window.mg_not_preload_np_lb_items) == 'undefined' || !window.mg_not_preload_np_lb_items) {
                        if(prev_id) {
                            mg_get_item_content(prev_id, media_focused_mode, true, true);
                        }
                        if(next_id) {
                            mg_get_item_content(next_id, media_focused_mode, true, true);
                        }
                    }
                    
                    // no media-focused mode for custom contents
                    if(media_focused_mode) {
                        mf_lb_mode_fix_for_lb_txt();
                    }
                }
            })
            .catch(e => {
                if(e.status && !prepare_it) {
                    console.error(e);
                    return fill_lightbox('Error retrieving contents. Try again please');
                }
                return false;
            });
		}

		return true;
	};
	
    
    
    // fixing media-focused lightbox layout with custom text item type
    const mf_lb_mode_fix_for_lb_txt = function() {
        if($('.mg_lb_lb_text').length || $('.mg_no_feat_lb').length) {
            $('#mg_lb_wrap').removeClass('mg_mf_lb');
            $('#mg_lb_wrap').css('display', 'flex'); 
        }
        else {
            $('#mg_lb_wrap').addClass('mg_mf_lb');
            $('#mg_lb_wrap').css('display', 'flex'); 
        }            
    };
    
    
	
	// POPULATE LIGHTBOX AND SHOW BOX
	const fill_lightbox = function(lb_contents) {
		if(!lb_switch_dir) {
            lb_switch_dir = 'next';
        }
		$mg_lb_contents.html(lb_contents).attr('class', 'mg_lb_pre_show_'+lb_switch_dir);

		// init self-hosted videos without poster
		if($('.mg_item_featured .mg_me_player_wrap.mg_self-hosted-video').length && !$('.mg_item_featured .mg_me_player_wrap.mg_self-hosted-video > img').length) {
			mg_video_player('#mg_lb_video_wrap');
		}
		
        // JS trigger to allow extra operations
        const item_id = $('.mg_lb_layout').data('item-id');
        $(window).trigger('mg_lb_contents_shown', [item_id]);
        
		// show with a little delay to be smoother
		setTimeout(function() {
			$('#mg_lb_loader').removeClass('mg_lb_shown');
			$mg_lb_contents.attr('class', 'mg_lb_shown').focus();
			$('html').addClass('mg_lb_shown');
			
			lb_is_shown = true;
			lb_switch_dir = false;
		}, 50);
	};
	

	// switch item - arrow click
	$(document).ready(function() {
		$(document).on('click', '.mg_nav_active > *', function() {
            mg_user_interacted = true;
            
			lb_switch_dir = ($(this).parents('.mg_nav_active').hasClass('mg_nav_next')) ? 'next' : 'prev';
			
			const pid = parseInt($(this).parents('.mg_nav_active').data('item-id'), 10);
			mg_switch_item_act(pid);
		});
	});

	// switch item - keyboards events
	$(document).keydown(function(e) {
		if(!lb_is_shown) {
            return true;    
        }

        // prev
        if (e.keyCode == 37 && $('.mg_nav_prev.mg_nav_active').length) {
            var pid = parseInt($('.mg_nav_prev.mg_nav_active').data('item-id'), 10);
            lb_switch_dir = 'prev';
            mg_switch_item_act(pid);
        }

        // next
        if (e.keyCode == 39 && $('.mg_nav_next.mg_nav_active').length) {
            var pid = parseInt($('.mg_nav_next.mg_nav_active').data('item-id'), 10);
            lb_switch_dir = 'next';
            mg_switch_item_act(pid);
        }
	});
        


	// SWITCH ITEM IN LIGHTBOX
	const mg_switch_item_act = function(pid) {
		$('#mg_lb_loader').addClass('mg_lb_shown');
		$mg_lb_contents.attr('class', 'mg_lb_switching_'+lb_switch_dir);
		
		$('#mg_lb_top_nav, .mg_side_nav, .mg_lb_nav_side_basic, #mg_top_close').fadeOut(350, function() {
			$(this).remove();
		});

        // media-focused mode? 
        const media_focused_mode = ($('#mg_lb_wrap')[0].hasAttribute('mg_mf_lb')) ? true : false;
        
		// wait CSS3 transitions
		setTimeout(function() {
			mg_unload_lb_scripts();
			$mg_lb_contents.empty();
			mg_get_item_content(pid, media_focused_mode);
			
			lb_is_shown = false;
		}, 400);
	};


    
	// CLOSE LIGHTBOX
	const mg_close_lightbox = function() {
        if(ajax_lb_control) {
            ajax_lb_control.abort();    
        }
        
		mg_unload_lb_scripts();
		if(mg_get_item_ajax) {
            mg_get_item_ajax.abort();
        }
		
		if(mg_lb_realtime_actions_intval) {
			clearInterval(mg_lb_realtime_actions_intval);	
		}

		$('#mg_lb_loader').removeClass('mg_lb_shown');
		$mg_lb_contents.attr('class', 'mg_closing_lb');
		
        setTimeout(() => {
            $('#mg_lb_background').removeClass('mg_lb_shown');
        }, 120);
		
        $('#mg_lb_top_nav, .mg_side_nav, #mg_top_close').fadeOut(350);
        setTimeout(function() {
            $('#mg_lb_top_nav, .mg_side_nav, #mg_top_close').remove();  
        }, 351);
		
		setTimeout(function() {
			$('#mg_lb_wrap').hide();
			$mg_lb_contents.empty();

			// restore html/body inline CSS
			if(typeof(mg_html_style) != 'undefined') {
                $('html').attr('style', mg_html_style);
            } else {
                $('html').removeAttr('style');
            }

			if(typeof(mg_body_style) != 'undefined') {
                $('body').attr('style', mg_body_style);
            } else {
                $('body').removeAttr('style');
            }

			if(typeof(mg_scroll_helper_h) != 'undefined') {
				clearTimeout(mg_scroll_helper_h);
			}
			$('#mg_lb_scroll_helper').removeAttr('style');
			
			$mg_lb_contents.attr('class', 'mg_lb_pre_show_next');
			$('html').removeClass('mg_lb_shown');
			
			lb_is_shown = false;
		}, 500); // wait for CSS transitions

        if($mg_sel_grid) {
            mg_remove_deeplink('item', 'mgi_'+ $mg_sel_grid.data('grid-id') );
        }
	};

	$(document).ready(function() {
		$(document).on('click', '#mg_lb_background.mg_classic_lb, #mg_lb_scroll_helper.mg_classic_lb, .mg_close_lb', function() {
            mg_user_interacted = true;
			mg_close_lightbox();
		});
	});


    // escape key pressed
	$(document).keydown(function(e){
		if( $('#mg_lb_contents .mg_close_lb').length && e.keyCode == 27) {
			mg_close_lightbox();
		}
	});


	// unload lightbox scripts
	var mg_unload_lb_scripts = function() {
		
		// stop persistent actions
		if(mg_lb_realtime_actions_intval) {
			clearInterval(mg_lb_realtime_actions_intval);	
			$('#mg_lb_scroll_helper').css('margin-top', 0);
		}
	};


	// lightbox images lazyload
	window.mg_lb_lazyload = function() {
		const $ll_img = $('.mg_item_featured > div > img, #mg_lb_video_wrap img, .mg_lb_ext_audio_w_img img');
        
		if(!$ll_img.length) {
            return true;
        }
            
        
        $ll_img.fadeTo(0, 0);

        lc_lazyload($ll_img, {
            allLoaded: function(imgs_data) {
                mg_lb_lazyloaded = imgs_data;

                $ll_img.fadeTo(300, 1);
                $('.mg_item_featured .mg_loader').fadeOut('fast');

                if($('#mg_lb_feat_img_wrap').length) {
                    $('#mg_lb_feat_img_wrap').fadeTo(300, 1);	
                }

                // for video poster
                if( $('#mg_ifp_ol').length )  {
                    setTimeout(() => {
                        $('#mg_ifp_ol').fadeIn(300);
                    }, 300);
                    
                    setInterval(function() {
                        $('#mg_lb_video_wrap > img').css('display', 'block'); // fix for poster image click
                    }, 200);
                }

                // for self-hosted video
                if( $('.mg_item_featured .mg_self-hosted-video').length )  {
                    $('#mg_lb_video_wrap').fadeTo(0, 0);
                    mg_video_player('#mg_lb_video_wrap');
                    $('#mg_lb_video_wrap').fadeTo(300, 1);
                }

                // for mp3 player
                if( $('.mg_item_featured .mg_lb_audio_player').length )  {

                    var player_id = '#' + $('.mg_lb_audio_player').attr('id');
                    mg_audio_player(player_id);

                    $('.mg_item_featured .mg_lb_audio_player').fadeIn();
                }
            }
        });
	};


	// lightbox persistent interval actions
    let mg_lb_realtime_actions_intval;
    
	window.mg_lb_realtime_actions = function() {
		if(mg_lb_realtime_actions_intval) {
			clearInterval(mg_lb_realtime_actions_intval);	
		}
		mg_lb_realtime_actions_intval = setInterval(function() {
            const $feat = $('.mg_item_featured'),
                  media_focused_mode = $('#mg_lb_wrap').hasClass('mg_mf_lb');
            
            let txt_h       = Math.round($('.mg_item_content').outerHeight()),
                mf_lb_max_h = 0;
            
            if(media_focused_mode) {
                const cmd_mode = $('#mg_lb_ins_cmd_wrap').data('data-cmd-mode');
                
                if($(window).width() > 860 && $.inArray(cmd_mode, ['inside', 'ins_hidden']) === -1) {
                    mf_lb_max_h = Math.round($(window).height() - ($('#mg_lb_contents').outerHeight(true) - $('#mg_lb_contents').height()));    
                }
                else {
                    mf_lb_max_h = Math.round($(window).height() - (($('#mg_lb_contents').outerHeight(true) - $('#mg_lb_contents').height()) / 2)) + 25; // 25 == half mobile cmds height        
                }
            }

            
            // keep scrollhelper visible
            $('#mg_lb_scroll_helper').css('margin-top', $('#mg_lb_wrap').scrollTop());

            
            // common actions
			if(!media_focused_mode) {
                
                // if scroller is shown - manage HTML margin and external buttons position
                if($('#mg_lb_contents').outerHeight(true) > $(window).height()) {
                    $('#mg_lb_wrap').addClass('mg_lb_has_scroll');

                    var diff = $(window).width() - $('#mg_lb_scroll_helper').outerWidth(true);

                    if(diff > 0) {
                        $('#mg_top_close, .mg_side_nav_next').css('right', diff);
                    }
                }
                else {
                    $('#mg_lb_wrap').removeClass('mg_lb_has_scroll');
                    $('#mg_top_close, .mg_side_nav_next').css('right', 0);
                }
            }
			
			
			// video - prior checks and height calculation
			if($('.mg_lb_video').length) {
				if( $('.mg_item_featured .mg_video_iframe').length ) {	// iframe
					var $video_subj = $('#mg_lb_video_wrap, #mg_lb_video_wrap .mg_video_iframe');
				}
				else { // self-hosted
					var $video_subj = $('.mg_item_featured .mg_self-hosted-video .mejs-container, .mg_item_featured .mg_self-hosted-video video, .mejs-overlay');
				}
				
				var new_video_h = Math.ceil($feat.width() * mg_lb_video_h_ratio);
                
                if(media_focused_mode && new_video_h > mf_lb_max_h) {
                    new_video_h = mf_lb_max_h;    
                }
			}
            
			
            // zoomable image in media-focused lb, only if image is bigger - TODO
            if($('.mg_mf_lb_zoom_btn').length) {
                const $img = $('.mg_zoomable_img img');
                
                ($('.mg_mf_lb_zoomed').length || ($img.width() < $img.data('w') && $img.height() < $img.data('h')) ) ? $('.mg_mf_lb_zoom_btn').show() : $('.mg_mf_lb_zoom_btn').hide();
            }
            
            
			/////////

            
            // media-focused + mobile mode = keep text wide as featured contents
            if($(window).width() <= 860 && !$('.mg_lb_layout.mg_lb_lb_text').length) {
                $('.mg_item_content').css('max-width', $('.mg_item_featured').width());
            }
            else {
                if($('.mg_item_content').length) {
                    $('.mg_item_content')[0].style.maxWidth = '';        
                }
            }
            
            
			/////////

            
			// fill side-layout space if lightbox is smaller than screen's height 
			if(
                $('.mg_lb_feat_match_txt').length && 
                $('#mg_lb_contents').outerHeight(true) < $(window).height() && // text is too high, avoid doing it
                $(window).width() > 860 &&
                !media_focused_mode
            ) {
                $feat.addClass('mg_lb_feat_matched');
                
					
				// single image and audio
				if(mg_lb_lazyloaded) {
                    var player_h = 0;
                    if($('.mg_lb_audio').length) {
                        player_h = ($('.mg_lb_audio .mg_audio_embed').length) ? $('.mg_lb_audio .mg_audio_embed').height() : $('.mg_lb_audio_player').outerHeight(true);         
                    }
                    
					// calculate what would be original height
					var first_val = mg_lb_lazyloaded[ Object.keys(mg_lb_lazyloaded)[0] ]; 
                    var real_img_h = Math.round((first_val.h * $feat.width()) / first_val.w);

					if((real_img_h + player_h) != txt_h) {
						$feat.find('img').not('.mg_lb_zoomed_img, .mg_lb_img_fill').css('height', (txt_h - player_h)).addClass('mg_lb_img_fill');	
					} 
				}
			
				// video
				if($('.mg_lb_video').length) {
					if(new_video_h != txt_h) {
                        new_video_h = txt_h;
                    }
					
					if($video_subj.height() != new_video_h) {
						if($('.mg_item_featured .mg_video_iframe').length) {
							$video_subj.attr('height', new_video_h);
						} else {
							$video_subj.css('height', new_video_h).css('max-height', new_video_h).css('min-height', new_video_h);
						}	
					}
				}
                
                // spotify audio embed (without featured image)
                if($('.mg_lb_audio .mg_lb_spotify_wrap').length && !$feat.find('img').length) {
                    const player_h = $('.mg_item_featured').height();

                    if(player_h != txt_h) {
                        const $spotify_iframe = $('.mg_lb_spotify_wrap .mg_spotify_embed');

                        $spotify_iframe.attr('height', (
                            txt_h - 
                            parseInt($spotify_iframe.css('margin-top'), 10) - 
                            parseInt($('.mg_item_featured').css('padding-bottom'), 10)
                        ));
                    }                
                }
			}
				
            
			// normal sizing
			else {
                if(!media_focused_mode) {
                    $feat.removeClass('mg_lb_feat_matched');
                    $feat.find('img').not('.mg_lb_zoomed_img').removeAttr('style').removeClass('mg_lb_img_fill');

                    // single image and audio
                    if(mg_lb_lazyloaded && $feat.hasClass('mg_lb_feat_matched')) {
                        $feat.removeClass('mg_lb_feat_matched');
                        $feat.find('img').not('.mg_lb_zoomed_img').removeAttr('style').removeClass('mg_lb_img_fill');	
                    }
                }

                    
                // video
                if($('.mg_lb_video').length) {
                    if($video_subj.height() != new_video_h) {
                        if($video_subj.is('div')) {
                            $video_subj.css('height', new_video_h).css('max-height', new_video_h).css('min-height', new_video_h);
                        } else {
                            $video_subj.attr('height', new_video_h);
                        }
                    }
                }
                
                
                // Spotify embed height
                if($('.mg_lb_spotify_wrap').length) {
                    const h_type = $('.mg_lb_spotify_wrap').data('h-type');
                    let h_val = (h_type == 'px') ? 
                        parseInt($('.mg_lb_spotify_wrap').data('h-val'), 10) : 
                        Math.ceil( $('.mg_lb_spotify_wrap').width() * ($('.mg_lb_spotify_wrap').data('h-val') / 100) );

                    // media-focused mode - max height
                    if(media_focused_mode && h_val > mf_lb_max_h) {
                        h_val = mf_lb_max_h;
                    }
                    
                    if($('.mg_lb_spotify_wrap .mg_spotify_embed').height() != h_val) {
                        $('.mg_lb_spotify_wrap .mg_spotify_embed').attr('height', h_val);
                    }
                }
                
                
                // slider's height in media-focused lightbox
                if(media_focused_mode) {
                    const predef_h = $('.mg_lb_lcms_slider').outerHeight();

                    let good_h = (predef_h > mf_lb_max_h) ? mf_lb_max_h: predef_h;
                    good_h = good_h - parseInt($('.mg_lb_lcms_slider .lcms_wrap').css('margin-bottom'), 10); 
                    
                    $('.mg_lb_lcms_slider .lcms_wrap').css('height', good_h);        
                }
			}
			
			/////////
			
			// hook for customizations
			$(window).trigger('mg_lb_realtime_actions');
		}, 20);
	};



	//////


    
    // lightbox slider
    window.mg_lb_slider = function(slider_id, extra_cmd_code) {
        const $slider   = $("#"+ slider_id),
              extra_nav = $slider.data('extra-nav');    
        
        // dots-to-thumbs trick
        if(extra_nav == 'thumbs') {
            $slider[0].addEventListener("lcms_ready", function() {
                $slider.find(".lcms_nav_dots span").each(function() {
                    
                    // find the smallest srcset image
                    const img_src = $(this).data("image").split(',');
                    const lower_res_src = img_src.reduce(
                        (acc, item) => {
                            let [url, width] = item.trim().split(" ");
                            width = parseInt(width, 10);

                            if(width < acc.width){
                                return { width, url };
                            }
                            return acc;
                        },
                        {width: 9999, url: img_src[0].split(' ')[0]}
                    ).url;
                    
                    $(this).css("background-image", "url('"+ lower_res_src +"')");
                });
                
                $slider.find(".mg_lb_lcms_toggle_thumbs").on("click", function() {
                    $slider.toggleClass("mg_lb_lcms_thumbs_shown").toggleClass("mg_lb_lcms_thumbs_hidden");    
                });
            });    
        }
        
        // draggable extra nav
        if(extra_nav != 'none') {
            $slider[0].addEventListener("lcms_ready", function() {
                lc_mouseDrag("#"+ slider_id +" .lcms_nav_dots", 0.3, false, true);
            });
            
            $slider[0].addEventListener("lcms_changing_slide", function(e) {
                let left = (e.detail.new_index - 3) * $("#"+ slider_id +" .lcms_nav_dots span").outerWidth(true);
                if(left < 0) {
                    left = 0;
                }
                
                $("#"+ slider_id +" .lcms_nav_dots")[0].scroll({
                    behavior: "smooth",
                    left    : left, 
                });
            });    
        }
        
        // mediaelement video in slider
        $slider[0].addEventListener("lcms_first_populated", function(e) {
            mg_lcms_setup_mediael($slider, 0, e.detail.slide_data);
        });
        $slider[0].addEventListener("lcms_changing_slide", function(e) {
            mg_lcms_setup_mediael($slider, e.detail.new_index, e.detail.slide_data);

            // slider elems counter
            if(lcmg.lb_slider_counter) {     
                const $target = $slider.find(".mg_lb_lcms_counter");
                let txt = $target.text().split(" / ");

                txt[0] = e.detail.new_index + 1;
                $target.text( txt.join(" / ") );
            }
        });
        
        // init
        const instance = new lc_micro_slider($slider, {
            slide_fx            : lcmg.lb_slider_fx,
            slide_easing	    : lcmg.lb_slider_easing,
            nav_arrows		    : true,
            nav_dots		    : (extra_nav == 'none') ? false : true,
            slideshow_cmd	    : $slider.data('ss-cmd'),
            autoplay		    : $slider.data('autoplay'),
            animation_time	    : parseInt(lcmg.lb_slider_fx_time, 10),
            slideshow_time	    : parseInt(lcmg.lb_slider_intval, 10),	
            extra_cmd_code      : extra_cmd_code,
            loader_code		    : mg_loader,
            addit_classes       : ["mg_lcms_slider"],
        });
    };
    


	//////


    
	// lightbox zoomable image - zoom-in
    $(document).on('click', '.mg_lb_zoom_in_btn', function(e) {
        const $wrap     = $(this).parents('#mg_lb_feat_img_wrap'),
              img_url   = $wrap.data('zoom-image'),
              mf_mode   = ($('#mg_lb_wrap')[0].hasAttribute('mg_mf_lb')) ? true : false;
        
        if(!$('.mg_lb_zoom_wrap').length) {
            $wrap.prepend('<div class="mg_lb_zoom_wrap"><img src="'+ img_url +'" class="mg_lb_zoomed_img" style="width: 100%;" /><div>'); 
            $wrap.find('.mg_lb_zoom_out_btn').removeClass('mg_displaynone');
            lc_mouseDrag( $wrap.find('.mg_lb_zoom_wrap')[0], 0.3);
        }

        const new_zoom = parseInt($wrap.attr('data-zoom-ratio'), 10) + 20;
        $wrap.addClass('mg_is_zooming_img').attr('data-zoom-ratio', new_zoom);
        $wrap.find('.mg_lb_zoomed_img').css('width', new_zoom +'%');

        if(mf_mode) {
            $('#mg_lb_feat_img_wrap').css('height', $wrap.find('.mg_lb_zoomed_img').height());
        }
    });
    
    
    // lightbox zoomable image - zoom-out
    $(document).on('click', '.mg_lb_zoom_out_btn', function(e) {
        const $wrap     = $(this).parents('#mg_lb_feat_img_wrap'),
              img_url   = $wrap.data('zoom-image'),
              mf_mode   = ($('#mg_lb_wrap')[0].hasAttribute('mg_mf_lb')) ? true : false;

        const new_zoom = parseInt($wrap.attr('data-zoom-ratio'), 10) - 20;

        if(new_zoom <= 100) {
            $wrap.find('.mg_lb_zoom_wrap').remove(); 
            $wrap.find('.mg_lb_zoom_out_btn').addClass('mg_displaynone');
            $wrap.removeClass('mg_is_zooming_img');
            
            if(mf_mode) {
                $('#mg_lb_feat_img_wrap').css('height', 'auto');
            }
        }
        else {
            $wrap.attr('data-zoom-ratio', new_zoom);
            $wrap.find('.mg_lb_zoomed_img').css('width', new_zoom +'%');
            
            if(mf_mode) {
                $('#mg_lb_feat_img_wrap').css('height', $wrap.find('.mg_lb_zoomed_img').height());
            }
        }
    });
    
    


	//////


	// woocommerce "add-to-cart" - display data for variable product
	$(document).on('change', '#mg_woo_cart_btn_wrap [name=mg_wc_atc_variations_dd]', function(e) {
		var $dd          = $(this), 
            variable_id  = $(this).val(),
			$wrap        = $('#mg_woo_cart_btn_wrap .mg_wc_atc_wrap'),
			$opt         = $(this).find('option[value="'+ variable_id +'"]');
		
		// clean up
		$wrap.slideUp(200, function() {
			$(this).empty();
		});
		$('#mg_woo_cart_btn_wrap .mg_wc_atc_response').slideUp(200, function() {
			$(this).remove();	
		});
		
		setTimeout(function() {
			
			// available
			if($opt.data('avail')) {
				var descr = ( $.trim($opt.data('descr')) ) ? '<div class="mg_wc_atc_descr">'+ $.trim($opt.data('descr')) +'</div>' : ''; 

				var quantity_f = ($opt.data('max') && $opt.data('max') <= 1) ? 
					'' : 
					'<br/><input name="mg_wc_atc_quantity" type="number" min="'+ $opt.data('min') +'" max="'+ $opt.data('max') +'" step="1" value="'+ $opt.data('min') +'" autocomplete="off" />';
	
				$wrap.html( 
                    $opt.data('price') + quantity_f +'<a href="javascript:void(0)" class="mg_wc_atc_btn"><i class="fa fa-shopping-cart" aria-hidden="true"></i> '+ lcmg.add_to_cart_str +'</a>'+ descr
                );	
			}
			
			// out of stock
			else {
				$wrap.html( $opt.data('price') +'<a href="javascript:void(0)" class="mg_wc_atc_btn mg_wc_atc_btn_disabled">'+ lcmg.out_of_stock_str +'</a>');	
			}
		
			// show
			$wrap.slideDown(200);

            // change image if is not a slider
            if(!$('.mg_lb_lcms_slider').length) {   
                $('#mg_lb_feat_img_wrap > img').attr('src', $opt.data('img'));  // TODO - adapt for zoom 
                
                // preload every image
                $dd.find('option').each(function() {
                    let img = new Image();
                    img.src = $(this).data('img');        
                });
            }
		}, 210);
	});

	

	// woocommerce "add-to-cart" - be sure quantity field is properly filled
	$(document).on('keyup', '#mg_woo_cart_btn_wrap [name=mg_wc_atc_quantity]', function(e) {
		var $this = $(this),
			min = parseInt( $(this).attr('min') );
			max = ($(this).attr('max')) ? parseInt( $(this).attr('max') ) : false;

		if(typeof(mg_wc_atc_q_tout) != 'undefined') {
            clearTimeout(mg_wc_atc_q_tout);
        }
		
		mg_wc_atc_q_tout = setTimeout(function() {
			var val = parseInt($this.val());
			
			if(isNaN(val) || val < min) {
				$this.val(min);
			}
			if(max && val > max) {
				$this.val(max);	
			}
		}, 300);
	});
	
	
	
	// woocommerce "add-to-cart" management
	$(document).on('click', '.mg_wc_atc_btn:not(.mg_wc_atc_btn_disabled, .mg_wc_atc_btn_acting, .mg_wc_external_link_btn)', function(e) {
		e.preventDefault();
		
		var $wrap = $(this).parents('#mg_woo_cart_btn_wrap'),
			$btn  = $(this);
		
		$wrap.find('.mg_wc_atc_response').slideUp(200, function() {
			$(this).remove();	
		});
		
		// quantity check
		var quantity = ($wrap.find('[name=mg_wc_atc_quantity]').length) ? parseInt($wrap.find('[name=mg_wc_atc_quantity]').val()) : 1; 
		if(isNaN(quantity) || !quantity) {
			alert('Quantity required');
			return false;	
		}
		
		$btn.addClass('mg_wc_atc_btn_acting');
		
		// ajax call
		var data = {
			mg_wc_atc	: $wrap.data('product'),
			atc_quantity: quantity,
			atc_var_id	: ($wrap.find('[name=mg_wc_atc_variations_dd]').length) ? $wrap.find('[name=mg_wc_atc_variations_dd]').val() : false
		};
		$.post(location.href, data, function(response) {
			$wrap.append(response);
			$wrap.find('.mg_wc_atc_response').slideDown(200);
		})
        .fail(function(e) {
            console.error(e);
            alert('error adding product to cart');
        })
        .always(function() {
            $btn.removeClass('mg_wc_atc_btn_acting');        
        });
		
	});



	////////////////////////////////////////////////



	// get URL query vars and returns them into an associative array
	const get_url_qvars = function() {
		mg_hashless_url = decodeURIComponent(window.location.href);
		
		if(mg_hashless_url.indexOf('#') !== -1) {
			var hash_arr = mg_hashless_url.split('#');
			mg_hashless_url = hash_arr[0];
			mg_url_hash = '#' + hash_arr[1];
		}
		
		// detect
		var qvars = {};
		var raw = mg_hashless_url.slice(mg_hashless_url.indexOf('?') + 1).split('&');
		
		$.each(raw, function(i, v) {
			var arr = v.split('=');
			qvars[arr[0]] = arr[1];
		});	
		
		return qvars;
	};
	
	
	// create slug from a string - for better deeplinked urls
	const string_to_slug = function(str) {
		str = str.toString().replace(/^\s+|\s+$/g, ''); // trim
		str = str.toLowerCase();
		
		// remove accents, swap ñ for n, etc
		var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
		var to   = "aaaaeeeeiiiioooouuuunc------";
		for (var i=0, l=from.length ; i<l ; i++) {
		  str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
		}
		
		str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		  .replace(/\s+/g, '-') // collapse whitespace and replace by -
		  .replace(/-+/g, '-'); // collapse dashes
		
		return str;
	};


	/*
	 * Global function to set media grid deeplinks
	 *
	 * key (string) - the subject - to know if it has to be deeplinked (item, category, search, page)
	 * subj (string) - attribute name
	 * val (int) - deeplink value (cat ID - item ID - etc)
	 * txt (string) - optional value to attach a text to value 
	 */
	window.mg_set_deeplink = function(key, subj, val, txt) {
		if(!lcmg.deepl_elems.length || $.inArray(key, lcmg.deepl_elems) === -1) {
            return false;
        }
		
		var qvars = get_url_qvars(); // get query vars and set clean URL + eventual hash 

		// setup deeplink part
		var true_val = (typeof(txt) != 'undefined' && txt) ? val +'/'+ string_to_slug(txt) : val;
		var dl_part = subj +'='+ true_val + mg_url_hash;
		
		
		// if URL doesn't have attributes
		if(mg_hashless_url.indexOf('?') === -1) {
			history.pushState(null, null, mg_hashless_url +'?'+ dl_part);
		}
		else {

			// if new deeplink already exists
			if(typeof(qvars[subj]) != 'undefined' && qvars[subj] == true_val) {
				return true;	
			}
			
			// re-compose URL
			var new_url = mg_hashless_url.slice(0, mg_hashless_url.indexOf('?') + 1);

			// (if found) discard attribute to be set
			var a = 0;
			var has_other_qvars = false;
			var this_attr_exists = false;
			
			$.each(qvars, function(i, v) {
				if(typeof(i) == 'undefined') {
                    return;
                }
				if(a > 0) {
                    new_url += '&';
                }
				
				if(i != subj) {
					new_url += (v) ? i+'='+v : i; 
					
					has_other_qvars = true;
					a++;	

				}
				else {
					this_attr_exists = true;	
				}
			});
				
			if(has_other_qvars) {
                new_url += '&';
            }		
			new_url += dl_part;


			if(mg_deeplinked && this_attr_exists && !lcmg.full_deepl) { 
				history.replaceState(null, null, new_url);
			} else {
				history.pushState(null, null, new_url);	
				mg_deeplinked = true;
			}
		}
	};



	// apply deeplink to page grids
	const apply_deeplinks = function(on_init) {
		var qvars = get_url_qvars();
		
		$.each(qvars, function(subj, val) {
			if(typeof(val) == 'undefined') {
                return;
            }
			var gid = subj.substr(4);
			
			// clean texts from deeplinked val
			var raw_val = val.split('/');
			val = raw_val[0]; 
            
			// at the moment - no actions on init except search
			if(!on_init) {
			
				// item deeplink - not on first init
				if(subj.indexOf('mgi_') !== -1) {
	
					// check item existence
					if(!$('.mg_grid_'+ gid +' .mgi_has_lb.mgi_'+ val).length) {
                        return;
                    }

					// if lightbox is already opened
					if($('.mg_item_content').length) {
                        
						// grid item is already shown?
						if($('#mg_lb_wrap').data('item-id') == val && $('#mg_lb_wrap').data('grid-id') == gid) {
                            return;
                        }
	
						// load lightbox
						$mg_sel_grid = $('.mg_grid_'+gid).first();
						$('#mg_lb_loader').addClass('mg_lb_shown');
                        
                        // media-focused mode?
                        const media_focused_mode = ($('#mg_lb_wrap').hasClass('mg_mf_lb')) ? true : false;
						mg_get_item_content(val, media_focused_mode, true);
					}
					
					else {
						// simulate click on item
						$('.mg_grid_'+ gid +' .mgi_'+ val).trigger('click');
					}
				}
				
				// category deeplink - not on first init
				if(subj.indexOf('mgc_') !== -1) {
					var $f_subj = (val == '*') ? $('#mgf_'+ gid +' .mgf_all') : $('#mgf_'+ gid +' .mgf_id_'+ val);
					
					// check filter existence
					if(!$f_subj.not('.mg_cats_selected').length) {
                        return;
                    }
					$f_subj.trigger('click');
				}
				
				// pagination deeplink - not on first init
				if(subj.indexOf('mgp_') !== -1 && $('#mgp_'+gid).length) {
					if(typeof(mg_grid_pag['mg_grid_' + gid ]) == 'undefined' || mg_grid_pag['mg_grid_' + gid ] == val) {return;}
					
					var subj = (mg_grid_pag['mg_grid_' + gid ] > val) ? '.mg_prev_page' : '.mg_next_page'; 
					$('#mgp_'+gid+' '+subj).not('.mg_pag_disabled').trigger('click');
				}
				
			}
				
			
			// search deeplink
			if(subj.indexOf('mgs_') !== -1) {
				if(typeof(on_init) == 'undefined') {
					$('#mgs_'+ gid+' input').val(decodeURIComponent(val)).submit();
				} else {
					setTimeout(function() {
						$('#mgs_'+ gid+' input').submit();
					}, 20);	
				}
			}
		});
		
				
		// step back from opened lightbox
		if(mg_hashless_url.indexOf('mgi_') === -1 && $('.mg_item_content').length) {
			$('.mg_close_lb').trigger('click');	
		}	
		
		// step back for each grid
		$('.mg_grid_wrap').each(function() {
			var gid = $(this).attr('id').substr(8);

			// from category deeplink
			var $mgc = $(this).find('.mg_cats_selected');
			if(mg_hashless_url.indexOf('mgc_'+gid) === -1 && $mgc.length && !$mgc.hasClass('mg_def_filter')) {
				$(this).find('.mg_def_filter').trigger('click');	
			}
			
			// from pagination
			if(mg_hashless_url.indexOf('mgp_'+gid) === -1 && $('#mgp_'+gid).length && $('#mgs_'+ gid+' input').val()) {
				mavo_to_pag_1(gid, $('#mgp_'+gid+' .mg_prev_page'));
			}
			
			// from search
			if(mg_hashless_url.indexOf('mgs_'+gid) === -1 && $('#mgs_'+gid).length && $('#mgs_'+ gid+' input').val()) {
				$('#mgs_'+ gid+' input').val('').submit();
			}
		});
	};
	
	
	// remove deeplink - check mg_set_deeplink() legend to know more about params
	window.mg_remove_deeplink = function(key, subj) {
		if(!lcmg.deepl_elems.length || $.inArray(key, lcmg.deepl_elems) === -1) {
            return false;
        }
		
		var qvars = get_url_qvars();
		if(typeof(qvars[subj]) == 'undefined') {
            return false;
        }
		
		// discard attribute to be removed
		var parts = [];
		$.each(qvars, function(i, v) {
			if(typeof(i) != 'undefined' && i && i != subj) {
				var val = (v) ? i+'='+v : i;
				parts.push(val);	
			}
		});
		
		var qm = (parts.length) ? '?' : '';	
		var new_url = mg_hashless_url.slice(0, mg_hashless_url.indexOf('?')) + qm + parts.join('&') + mg_url_hash;

		history.pushState(null, null, new_url);	
		
		if(mg_hashless_url.indexOf('mgi_') === -1 && mg_hashless_url.indexOf('mgc_') === -1 && mg_hashless_url.indexOf('mgp_') === -1 && mg_hashless_url.indexOf('mgs_') === -1) {
			mg_deeplinked = false;
		}	
	};
	
	
	// detect URL changes
	window.addEventListener('popstate', function(e) {
		apply_deeplinks();
		
		if(mg_hashless_url.indexOf('mgi_') === -1 && mg_hashless_url.indexOf('mgc_') === -1 && mg_hashless_url.indexOf('mgp_') === -1 && mg_hashless_url.indexOf('mgs_') === -1) {
			mg_deeplinked = false;
		}
	});
	
	
	
	////////////////////////////////////////////////////////////////
	// initialize inline sliders 
	const mg_inl_slider_init = function(sid) {
        const $slider = $('#'+sid);
        
        new lc_micro_slider($slider, {
            slide_fx            : lcmg.inl_slider_fx,
            slide_easing	    : lcmg.inl_slider_easing,
            nav_arrows		    : true,
            nav_dots		    : false,
            slideshow_cmd	    : $slider.data('ss-cmd'),
            autoplay		    : $slider.data('autoplay'),
            animation_time	    : parseInt(lcmg.inl_slider_fx_time, 10),
            slideshow_time	    : parseInt(lcmg.inl_slider_intval, 10),	
            pause_on_video_play : false,
            loader_code		    : mg_loader,
            addit_classes       : ["mg_lcms_slider"],
        });
        
        // kenburns?
        if($slider.data('kenburns')) {
            $slider[0].addEventListener('lcms_slide_shown', (e) => { 
                const $subj = $slider.find('.lcms_slide[data-index="'+ e.detail.slide_index +'"]');  
                
                if($subj.data('type') == 'image') {
                    $subj.find('.lcms_bg').mg_kenburns();        
                }
            });
        }
    };
	
    
    // setup mediaelement for slider's self-hosted videos
    window.mg_lcms_setup_mediael = function($slider_obj, slide_index, slide_data) {
        const $slide = $slider_obj.find(".lcms_slide[data-index="+ slide_index +"]");

        if(slide_data.type == "video" && slide_data.content.indexOf("<video") !== -1) {
            const video_id = Math.random().toString(36).substr(2, 9); 
            $slide.find(".lcms_content").attr("id", video_id).addClass("mg_lcms_mediael mg_me_player_wrap");

            mg_video_player("#"+ video_id, false);   
            $('.mg_lcms_slider .mejs-mediaelement video').attr('poster', lcmg.video_poster_trick);
            
            // show video commands on first click
            $slide.on('click touchend', function() { 
                $slide.find('.mg_me_player_wrap').addClass('mg_clicked_poster');
            });
        } 
    };
    
    
    
    // LC micro slider - external video iframe poster trick
    $(document).on('click touchend', '.mg_lcms_iframe_icon', function() {
        const $slide = $(this).parents('.lcms_slide'), 
              $iframe = $slide.find('.mg_video_iframe');
        
        $slide.addClass('mg_clicked_poster');
        $slide.find('.mg_lcms_iframe_icon').remove();
        
        $iframe.attr('src', $iframe.data('src'));
        $iframe.removeAttr('data-src');      
    });
    
    
	



	//////////////////////////////////////////////////////////////////
	// mediaelement audio/video player functions

	// init video player
	window.mg_video_player = function(player_id, is_inline) {
		if(!$(player_id).length) {
            return false;
        }
		
		// wait until mediaelement script is loaded
		if(typeof(MediaElementPlayer) != 'function') {
			setTimeout(function() {
				mg_video_player(player_id, is_inline);
			}, 50);
			return false;
		}

		if(typeof(is_inline) == 'undefined') {
			var features = ['playpause','current','progress','duration','volume','fullscreen'];
		} else {
			var features = ['playpause','current','progress','volume','fullscreen'];
		}
		
		$(player_id+' video').mediaelementplayer({
			audioVolume: 'vertical',
			startVolume: 1,
			features: features,
			success: function(player, originalNode, instance) {
				mg_player_objects[player_id] = player;
				
				// autoplay
				if($(player_id).hasClass('mg_video_autoplay')) {
					
                    // modern browsers allows it only if muted
                    if(!mg_user_interacted && $(player_id).hasClass('mg_muted_autoplay')) {
                        player.setMuted(true);
                    }
                    
                    if(typeof(is_inline) == 'undefined') {
						player.play();
					} else {
						autoplay_inl_video(player_id, player);
					}
				}
                
                // show video commands on first click
                $(player_id).on('click touchend', function() { 
                    $(player_id).addClass('mg_clicked_poster');
                });
			}
		});
	};

	
	
	// autoplays inline video only if it is shown
	const autoplay_inl_video = function(player_id, player) {
		if(!$(player_id).parents('.mg_box').hasClass('mgi_shown')) {
			setTimeout(function() {
				autoplay_inl_video(player_id, player);
			}, 100);	
            
            return true;
		}
        
        player.play();	
	};
	


	// store player playlist and the currently played track - init player
	const mg_audio_player = function(player_id, is_inline) {
		let success_function;
        
		// wait until mediaelement script is loaded
		if(typeof(MediaElementPlayer) != 'function') {
			setTimeout(function() {
				mg_audio_player(player_id, is_inline);
			}, 50);
			return false;
		}

		// if has multiple tracks
		if($(player_id).find('source').length > 1) {

			mg_audio_tracklists[player_id] = [];
			$(player_id).find('source').each(function(i, v) {
                mg_audio_tracklists[player_id].push( $(this).attr('src') );
            });

			if(typeof(is_inline) == 'undefined') {
				var features = ['mg_prev','playpause','mg_next','current','progress','duration','mg_loop','volume','mg_tracklist'];
			} else {
				var features = ['mg_prev','playpause','mg_next','current','progress','mg_loop','volume','mg_tracklist'];
			}

			success_function = function(player, originalNode, instance) {
				var player_id = '#'+ $(originalNode).parents('.mg_me_player_wrap').attr('id');
				mg_player_objects[player_id] = player;
				
				player.addEventListener('ended', function (e) {
					mg_audio_go_to(player_id, 'next', true);
				}, false);
				
				// autoplay
				if($(player_id).hasClass('mg_audio_autoplay')) {
					player.play();
				}
			};
		}

		else {
			var features = ['playpause','current','progress','duration','mg_loop','volume'];
			
			success_function = function(player, originalNode, instance) {
				var player_id = '#'+ $(originalNode).parents('.mg_me_player_wrap').attr('id');
				mg_player_objects[player_id] = player;
				
				// autoplay
				if($(player_id).hasClass('mg_audio_autoplay')) {
					player.play();
				}
			};
		}

		
		$(player_id+' audio').mediaelementplayer({
			audioVolume: 'vertical',
			startVolume: 1,
			features: features,
			loop: lcmg.audio_loop,
			success: success_function,
			alwaysShowControls: true
		});
		mg_audio_is_playing[player_id] = 0;
	};


	// go to track - prev / next / track_num
	const mg_audio_go_to = function(player_id, direction, autonext) {
		var t_list = mg_audio_tracklists[player_id];
		var curr = mg_audio_is_playing[player_id];

		if(direction == 'prev') {
			var track_num = (!curr) ? (t_list.length - 1) : (curr - 1);
			var track_url = t_list[track_num];
			mg_audio_is_playing[player_id] = track_num;
		}
		else if(direction == 'next') {
			// no loop and reached the last tracklist element? stop
			if(!$(player_id+' .mejs-mg-loop-on').length && $(player_id+'-tl .mg_current_track').is(':last-child')) {
				return false;
			}

			var track_num = (curr == (t_list.length - 1)) ? 0 : (curr + 1);
			var track_url = t_list[track_num];
			mg_audio_is_playing[player_id] = track_num;
		}
		else {
			var track_url = t_list[(direction - 1)];
			mg_audio_is_playing[player_id] = (direction - 1);
		}

		// set player to that url
		var $subj = mg_player_objects[player_id];
		$subj.pause();
		$subj.setSrc(track_url);
		$subj.play();

		// set tracklist current track
		$(player_id +'-tl li').removeClass('mg_current_track');
		$(player_id +'-tl li[data-track-num='+ (mg_audio_is_playing[player_id] + 1) +']').addClass('mg_current_track');
	};
	
	
	
	// initialize inline audio player
	const init_inl_audio = function(player_id, autoplay) {
		mg_audio_player(player_id, true);
		
		$(player_id).addClass('mg_inl_audio_shown');
		
		// enable playlist
		if($(player_id+'-tl').length) {
			$(player_id+'-tl').show();	
		}
		
		// autoplay
		setTimeout(function() {
			mg_check_inl_audio_icons_vis();
			
			if(typeof(autoplay) != 'undefined') {
				var player_obj = mg_player_objects[player_id];
				player_obj.play();		
			}
		}, 300);
	};
	
	// commands visibility also on resize
	$(window).on('mg_resize_grid', function(e, gid) {	
		mg_check_inl_audio_icons_vis();
	});
	
	
	

	// add custom mediaelement buttons
	$(document).ready(function(e) {
		mg_mediael_add_custom_functions();
	});
	
	const mg_mediael_add_custom_functions = function() {
		
		// wait until mediaelement script is loaded
		if(typeof(MediaElementPlayer) != 'function') {
			setTimeout(function() {
				mg_mediael_add_custom_functions();
			}, 50);
			return false;
		}
		
		// prev
		MediaElementPlayer.prototype.buildmg_prev = function(player, controls, layers, media) {
			var prev = $('<div class="mejs-button mejs-mg-prev" title="previous track"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)


			// add a click toggle event
			.click(function() {
				var player_id = '#' + $('#'+player.id).parent().attr('id');
				mg_audio_go_to(player_id, 'prev');
			});
		}

		// next
		MediaElementPlayer.prototype.buildmg_next = function(player, controls, layers, media) {
			var prev = $('<div class="mejs-button mejs-mg-next" title="previous track"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)
			.click(function() {
				var player_id = '#' + $('#'+player.id).parent().attr('id');
				mg_audio_go_to(player_id, 'next');
			});
		}

		// tracklist toggle
		MediaElementPlayer.prototype.buildmg_tracklist = function(player, controls, layers, media) {
			var tracklist =
			$('<div class="mejs-button mejs-mg-tracklist-button ' +
				(($('#'+player.id).parent().hasClass('mg_show_tracklist')) ? 'mejs-mg-tracklist-on' : 'mejs-mg-tracklist-off') + '" title="'+
				(($('#'+player.id).parent().hasClass('mg_show_tracklist')) ? 'hide' : 'show') +' tracklist"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)
			.click(function() {
				if ($('#'+player.id).find('.mejs-mg-tracklist-on').length) {
					$('#'+player.id).parents('.mg_media_wrap').find('.mg_audio_tracklist').removeClass('mg_iat_shown');
					tracklist.removeClass('mejs-mg-tracklist-on').addClass('mejs-mg-tracklist-off').attr('title', 'show tracklist');
				} 
				else {
					$('#'+player.id).parents('.mg_media_wrap').find('.mg_audio_tracklist').addClass('mg_iat_shown');
					tracklist.removeClass('mejs-mg-tracklist-off').addClass('mejs-mg-tracklist-on').attr('title', 'hide tracklist');
				}
			});
		}

		// loop toggle
		MediaElementPlayer.prototype.buildmg_loop = function(player, controls, layers, media) {
			var loop =
			$('<div class="mejs-button mejs-mg-loop-button ' +
				((player.options.loop) ? 'mejs-mg-loop-on' : 'mejs-mg-loop-off') + '" title="'+
				((player.options.loop) ? 'disable' : 'enable') +' loop"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)
			.click(function() {
				player.options.loop = !player.options.loop;
				if (player.options.loop) {
					loop.removeClass('mejs-mg-loop-off').addClass('mejs-mg-loop-on').attr('title', 'disable loop');
				} else {
					loop.removeClass('mejs-mg-loop-on').addClass('mejs-mg-loop-off').attr('title', 'enable loop');
				}
			});
		}
	};


	// change track clicking on tracklist
	$(document).ready(function(e) {
        $(document).on('click', '.mg_audio_tracklist li:not(.mg_current_track)', function() {
			mg_user_interacted = true;
            
            var player_id = '#' + $(this).parents('ol').attr('id').replace('-tl', '');
			var num = $(this).attr('rel');

			mg_audio_go_to(player_id, num);
		});
    });

	
	// pause inline players and inl text's video bg and sliders
	window.mg_pause_inl_players = function(grid_id) {
		var $subj = $('#'+ grid_id+' .mg-muuri-hidden, #'+ grid_id+' .mgi_low_opacity_f');
		
		// audio/video player
		$subj.find('.mg_sh_inl_video, .mg_inl_audio_player').each(function() {
			if( typeof(mg_player_objects) != 'undefined' && typeof( mg_player_objects[ '#' + this.id ] ) != 'undefined') {
				var $subj = mg_player_objects[ '#' + this.id ];
				$subj.pause();
			}
		});	
		
		// inline text's video bg
		$subj.find('.mg_inl_txt_video_bg').each(function() {
			var video = $(this)[0];
			video.pause();
		});	
		
		// inline slider
		$subj.find('.mg_inl_slider_wrap .lcms_wrap').each(function() { 
            lcms_stop( $('#'+ $(this).parents('.mg_inl_slider_wrap').attr('id') )[0] );
        });
	};

	
	
	// hide audio player commands in tiny items
	const mg_check_inl_audio_icons_vis = function() {
		$('.mg_inl_audio').not('.mg-muuri-hidden').each(function() {
			var $to_toggle = $(this).find('.mg_inl_audio_player').find('.mejs-time, .mejs-time-rail');
			
			( $(this).find('.mg_media_wrap').width() >= 250) ? $to_toggle.show() : $to_toggle.hide();
		});
	};
	
    
    
    
    /////////////////////////////////////////////////////////////
	// EXTRA
	
    
    // disable right click
    if($('body.mg_no_rclick').length) {
        $(document).on("contextmenu", '.mg_grid_wrap *, #mg_lb_wrap *, #mg_wp_video_wrap .wp-video *, #mg_lb_contents img, #mg_lb_contents .wp-video *', function(e) {
            e.preventDefault();
        }); 
    }
    
    
    // custom overlay touch behavior
    if(lcmg.touch_ol_behav == 'custom' && mg_mobile_device) {
        $('body').addClass('mg_cust_touch_ol_behav');
        let ctob_timing = 0;
        
        $(document).on("touchstart", '.mg_box', function(e) {
            $(this).addClass('mg_ctob_show');
            
            const d = new Date();
            ctob_timing = d.getTime();
        })
        .on("touchend", '.mg_box', function(e) {
            $(this).removeClass('mg_ctob_show');
            $(this).parents('.mg_grid_wrap').trigger('tap');
            
            const d = new Date();

            // emulate click if timing is under 250ms
            if(d.getTime() - ctob_timing < 250 && $(this).hasClass('mgi_has_lb')) {
                if($(this).find('a.mg_box_inner').length) {
                    window.open(
                        $(this).find('a.mg_box_inner').attr('href'),
                        $(this).find('a.mg_box_inner').attr('target')
                    );
                }
                else {
                    $(this).trigger('click');
                }    
            }
        });
    }
    



	/////////////////////////////////////////////////////////////
	// UTILITIES
	
    
    // ken burns effect applied on elements having a background image and absolute positioning
    $.fn.mg_kenburns = function() {
        this.each(function() {
            const $elem = $(this);    
            $elem.addClass('mg_kenburnsed_item');

            let intval      = null,
                curr_comb   = '';

            const ken_burns_fx = function() {
                  const vert_opts     = ["top", "bottom"],
                        horiz_opts    = ["left", "right"];

                const vert_rule  = vert_opts[Math.floor(Math.random() * vert_opts.length)], 
                      horiz_rule = horiz_opts[Math.floor(Math.random() * horiz_opts.length)];

                // force movement
                if(curr_comb == vert_rule+horiz_rule) {
                    ken_burns_fx();      
                }

                let animation = {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                };
                animation[ vert_rule ] = '-20%';
                animation[ horiz_rule ] = '-20%';

                $elem[0].animate(
                    animation,
                    {
                        duration: parseInt(lcmg.kenburns_timing, 10),
                        iterations: 1,
                        fill: 'forwards'
                    }
                );       
            };  


            if(intval) {
                clearInterval(intval);    
            }
            intval = setInterval(() => {
                ken_burns_fx();
            }, lcmg.kenburns_timing);    

            ken_burns_fx();
        });
    };
    
    
	
	// responsive text - font size decrease
	const mg_responsive_txt = function(gid) {
		var $subj = $('#'+gid+ ' .mg_inl_txt_rb_txt_resize .mg_inl_txt_contents').find('p, b, div, span, strong, em, i, h6, h5, h4, h3, h2, h1');

		// setup original text sizes and reset
		$('#'+gid+' .mg_inl_txt_wrap').removeClass('mg_it_resized');
		$subj.each(function() {
			if(typeof( $(this).data('orig-size') ) == 'undefined') {
				$(this).data('orig-size', $(this).css('font-size'));
				$(this).data('orig-lheight', $(this).css('line-height'));
			}

			// reset
			$(this).removeClass('mg_min_reached mg_inl_txt_top_margin_fix mg_inl_txt_btm_margin_fix mg_inl_txt_top_padding_fix mg_inl_txt_btm_padding_fix');
			$(this).css('font-size', $(this).data('orig-size'));
			$(this).css('line-height', $(this).data('orig-lheight'));
        });

		$('#'+ gid +' .mg_inl_txt_contents').each(function() {

			// not for auto-height
			if(
				(!mg_mobile_mode[gid] && !$(this).parents('.mg_box').hasClass('mgis_h_auto')) ||
				(mg_mobile_mode[gid] && !$(this).parents('.mg_box').hasClass('mgis_m_h_auto'))
			) {
				var max_height = $(this).parents('.mg_media_wrap').height();

				if(max_height < $(this).outerHeight()) {
					$('#'+gid+' .mg_inl_txt_wrap').addClass('mg_it_resized');
					
					var a = 0;
					while( max_height < $(this).outerHeight() && a < 100) {
						if(a == 0) {
							// check and eventually reduce big margins and paddings at first
							$subj.each(function(i, v) {
								if( parseInt($(this).css('margin-top')) > 10 ) {
                                    $(this).addClass('mg_inl_txt_top_margin_fix');
                                }
								if( parseInt($(this).css('margin-bottom')) > 10 ) {
                                    $(this).addClass('mg_inl_txt_btm_margin_fix');
                                }

								if( parseInt($(this).css('padding-top')) > 10 ) {
                                    $(this).addClass('mg_inl_txt_top_padding_fix');
                                }
								if( parseInt($(this).css('padding-bottom')) > 10 ) {
                                    $(this).addClass('mg_inl_txt_btm_padding_fix');
                                }
							});
						}
						else {
							$subj.each(function(i, v) {
								var new_size = parseFloat( $(this).css('font-size')) - 1;
								if(new_size < 11) {
                                    new_size = 11;
                                }

								var new_lheight = parseInt( $(this).css('line-height')) - 1;
								if(new_lheight < 14) {
                                    new_lheight = 14;
                                }

								$(this).css('font-size', new_size).css('line-height', new_lheight+'px');

								if(new_size == 11 && new_lheight == 14) { // resizing limits
									$(this).addClass('mg_min_reached');
								}
							});

							// if any element has reached min size
							if( $('#'+gid+ ' .mg_inl_txt_contents .mg_min_reached').length ==  $subj.length) {
								return false;
							}
						}

						a++;
					}
				}
			}
        });
	};


	// webkit transformed items rendering fix
	const webkit_blurred_elems_fix = function(grid_id) {
		if('WebkitAppearance' in document.documentElement.style) {

			$('#mg_wbe_fix_'+grid_id).remove();
	
			setTimeout(function() {
				$('head').append('<style type="text/css" id="mg_wbe_fix_'+ grid_id +'">.mg_'+grid_id+' .mg_box_inner {-webkit-font-smoothing: subpixel-antialiased;}</style>');
			}, 600);
		}
	};
})(jQuery);