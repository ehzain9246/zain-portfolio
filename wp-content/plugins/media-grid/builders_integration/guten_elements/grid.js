(function($) { 
	"use strict";
	
	
	var icon = wp.element.RawHTML({
    	children: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 18 18"><path  d="M8.17,10.32H2.31a.31.31,0,0,0-.31.32v7a.32.32,0,0,0,.31.33H8.17a.32.32,0,0,0,.31-.33v-7A.31.31,0,0,0,8.17,10.32Z" transform="translate(0 0)"/><path  d="M17.69,2H11.83a.33.33,0,0,0-.31.34v7h0a.32.32,0,0,0,.31.33h5.86A.32.32,0,0,0,18,9.35h0v-7A.33.33,0,0,0,17.69,2Z" transform="translate(0 0)"/><path  d="M18,12.57a.32.32,0,0,0-.31-.33H11.83a.32.32,0,0,0-.31.33v5.1a.32.32,0,0,0,.31.33h5.86a.32.32,0,0,0,.31-.33Z" transform="translate(0 0)"/><path  d="M8.48,2.33A.32.32,0,0,0,8.17,2H2.31A.32.32,0,0,0,2,2.33v5.1a.32.32,0,0,0,.31.33H8.17a.32.32,0,0,0,.31-.33Z" transform="translate(0 0)"/></svg>'
	});
	
		
		
	// recreate attributes from PHP array
	var atts = {};
	$.each(mg_defaults, function(i, v) {
		atts[i] = {default : v.default};
	});
	
	
	
	
	// trick executing javascript on server rendered element
	window.mg_guten_on_display = function(blockId) {
		setTimeout(function() { // wait a bit for possible guten mess
			
			if(!$('#block-'+ blockId +' .mg_grid_wrap').length || $('#block-'+ blockId +' .components-placeholder').length) {
				setTimeout(function() {
					mg_guten_on_display(blockId);
				}, 350);
				return false;
			}
			
			var temp_grid_id 	= $('#block-'+ blockId +' .mg_grid_wrap').attr('id');
			var grid_id 		= $('#block-'+ blockId +' .mg_grid_wrap').attr('data-grid-id');
			
			mg_grid_filters[temp_grid_id] = [];

			// start the engine!
			$(window).trigger("mg_pre_grid_init", [temp_grid_id, grid_id]);
		
			if(typeof(mg_init_grid) == "function" ) {
				mg_init_grid(temp_grid_id, 1);
			}
			$(window).trigger("mg_post_grid_init", [temp_grid_id, grid_id]);
			
			// disable click on grid
			$(document).off('click mousedown mouseup', '.wp-block .mg_grid_wrap *').on('click mousedown mouseup', '.wp-block .mg_grid_wrap *', function(e) {
				e.preventDefault();
				return false;
			});
            
            // track "lazy" module reloads
            const intval = setInterval(() => {
                let live_subj_id = $('#block-'+ blockId +' .mg_grid_wrap').attr('id');
                
                if(temp_grid_id != live_subj_id) {
                    mg_guten_on_display(blockId);
                    clearInterval(intval);
                }
            }, 100);
                
		}, 400);
	};
	
		
	
	// register block
	var args = {
		block_id			: 'lcweb/media-grid',
		title				: 'Media Grid',
		panels				: mg_panels,
		icon				: icon,
		structure			: mg_defaults,
		attributes			: atts,
		on_display_callback : 'mg_guten_on_display',
	};
	lc_register_block(args); 


})(jQuery); 