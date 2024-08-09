(function($) { 
    "use strict";     
    
	if(typeof(tinymce) == 'undefined') {
        return false;
    }
    let $mg_scw_editor_wrap;
    

	// creates plugin
	tinymce.PluginManager.add('lcweb_mediagrid', function(editor, url) {

        // Add a button that opens a window
        editor.addButton('mg_btn', {
            text		: false,
			title		: 'Media Grid',
            icon		: 'mg_btn',  // css class  mce-i-mg_btn
            onclick		: function() {

				$mg_scw_editor_wrap = $(this).parents('.wp-editor-wrap');
			
				$.magnificPopup.open({
					items : {
						src: '#mediagrid_sc_wizard > *',
						type: 'inline'
					},
					mainClass	: 'mg_sc_wizard_lb',
					closeOnContentClick	: false,
					closeOnBgClick		: false, 
					preloader	: false,
					callbacks	: {
					  beforeOpen: function() {
						if($(window).width() < 800) {
						  this.st.focus = false;
						}
					  },
					  open : function() {

                        // tabify through select
                        var lb_class = ".mg_sc_wizard_lb"

                        $(lb_class+' .lcwp_scw_choser option').each(function() {
                            const val = $(this).attr('value'),
                                  $subj = $(lb_class +' '+ val);

                            (!$(this).is(':selected')) ? $subj.hide() : $subj.show();
                        });

                        // on select change
                        $(lb_class).on('change', '.lcwp_scw_choser', function(e) {
                            e.preventDefault();

                            $(lb_class+' .lcwp_scw_choser option').each(function() {
                                const val = $(this).attr('value'),
                                      $subj = $(lb_class +' '+ val);

                                (!$(this).is(':selected')) ? $subj.hide() : $subj.show();
                            });
                        });
                          
                        // colorpicker
                        new lc_color_picker(lb_class +' .mg_sc_col_f', {
                            modes           : ['solid'],
                            transparency    : true,
                            no_input_mode   : false,
                            wrap_width      : '90%',
                            fallback_colors : '#888888',
                            preview_style   : {
                                input_padding   : 40,
                                side            : 'right',
                                width           : 35,
                            },
                        });


                        // lc switch
                        lc_switch(lb_class +' li input[type="checkbox"]', {
                            on_txt      : "YES",
                            off_txt     : "NO",   
                        });


                        // LC select
                        new lc_select(lb_class +' li select', {
                            wrap_width : '100%',
                            addit_classes : ['lcslt-lcwp', 'mg_scw_field_dd'],
                        });  
                          
					  }
					}
				});
                
				$(document).on('click', '.mfp-wrap.mg_sc_wizard_lb', function(e) {
					if($(e.target).hasClass('mfp-container')) {
						$.magnificPopup.close();
					}
				});
            }
        });
	});
	


	////////////////////////////////////////////////////



	// toggle fields related to filters
	$(document).on('lcs-statuschange', '.mg_sc_wizard_lb #mg_filter_grid',  function() {
		if( $(this).is(':checked') ) {
			$('.mg_scw_ff').slideDown('fast');	
		} else {
			$('.mg_scw_ff').slideUp('fast');	
		}
	});
	



	////////////////////////////////////////////////////////
	///// shortcode insertion
	
	$(document).on("click", '.mg_sc_insert_grid', function(e) {
		var $subj = $('.mg_sc_wizard_lb');
		
		var gid = $subj.find('#mg_grid_choose').val();
		var sc = '[mediagrid gid="'+gid+'"';
		
		//  titles under
		if($subj.find('#mg_title_under').val()) {
			sc += ' title_under="'+ $subj.find('#mg_title_under').val() +'"';
		}
		
		//  pagination system
		if( $subj.find('#mg_pag_sys').val() ) {
			sc += ' pag_sys="'+ $subj.find('#mg_pag_sys').val() +'"';
		}
		
		
		// filter - consider also MGAF
		if( $subj.find('#mg_filter_grid').is('select') ) {
			var filter = $subj.find('#mg_filter_grid').val();
			if(filter) {
				sc += ' filter="'+filter+'"';	
			}
		}
		else {
            var filter = 0;
            
			if( $subj.find('#mg_filter_grid').is(':checked') ) {
				var filter = 1;
				sc += ' filter="'+filter+'"';
			}
		}

		//  search bar
		if( $subj.find('#mg_search_bar').is(':checked') && (filter == 0 || filter == 1) ) {
			sc += ' search="1"';
		}

		// filter options
		if(filter == 1) {
			// filters alignment
			if( $subj.find('#mg_filters_align').val() != 'top' ) {
				sc += ' filters_align="'+ $subj.find('#mg_filters_align').val() +'"';
			}
			
			// hide "all" filter
			if( $subj.find('#mg_hide_all').is(':checked') ) {
				sc += ' hide_all="1"';
			}
			
			// select default filter
			if( $subj.find('#mg_def_filter').val() != '' ) {
				sc += ' def_filter="'+ $subj.find('#mg_def_filter').val() +'"';
			}
		}
		
        // fullscreen lightbox mode
		if($subj.find('select[name="mg_mf_lb"]').val()) {
			sc += ' fs_lightbox="'+ $subj.find('select[name="mg_mf_lb"]').val() +'"';	
		}
        
		// custom mobile treshold
		var cmt = parseInt($subj.find('#mg_mobile_treshold').val(), 10);
		if(cmt) {
			sc += ' mobile_tresh="'+ cmt +'"';	
		}


		////////////////////////////////////////////
		
		
		// custom cells margin
		if($subj.find('#mg_cells_margin').val() != '') {
			sc += ' cell_margin="'+ parseInt($subj.find('#mg_cells_margin').val()) +'"';	
		}
		
		// custom borders width
		if($subj.find('#mg_border_w').val() != '') {
			sc += ' border_w="'+ parseInt($subj.find('#mg_border_w').val()) +'"';	
		}
		
		// custom borders color
		if($subj.find('#mg_border_color').val() != '') {
			sc += ' border_col="'+ $subj.find('#mg_border_color').val() +'"';	
		}
		
		// custom border radius
		if($subj.find('#mg_cells_radius').val() != '') {
			sc += ' border_rad="'+ parseInt($subj.find('#mg_cells_radius').val()) +'"';	
		}
		
		// custom outline display
		if($subj.find('#mg_outline').val() != '') {
			sc += ' outline="'+ parseInt($subj.find('#mg_outline').val()) +'"';	
		}
		
		// custom outline color
		if($subj.find('#mg_outline_color').val() != '') {
			sc += ' outline_col="'+ $subj.find('#mg_outline_color').val() +'"';	
		}

		// custom shadow display
		if($subj.find('#mg_shadow').val() != '') {
			sc += ' shadow="'+ parseInt($subj.find('#mg_shadow').val()) +'"';	
		}

		// custom outline color
		if($subj.find('#mg_txt_under_color').val() != '') {
			sc += ' txt_under_col="'+ $subj.find('#mg_txt_under_color').val() +'"';	
		}


		///// OVERLAY MANAGER ADD-ON ///////////
		////////////////////////////////////////
		if($subj.find('#mg_custom_overlay').length && $subj.find('#mg_custom_overlay').val()) {
			sc += ' overlay="'+ $subj.find('#mg_custom_overlay').val() +'"';	
		}
		////////////////////////////////////////



		// allow add-ons to inject parameters into the shortcode
		let mg_sc = sc;
		$(window).trigger('mg_sc_creation_hook');


		mg_sc += ']';
		
		
		// inserts the shortcode into the active editor
		if( $mg_scw_editor_wrap.find('#wp-content-editor-container > textarea').is(':visible') ) {
			var val = $mg_scw_editor_wrap.find('#wp-content-editor-container > textarea').val() + mg_sc;
			$mg_scw_editor_wrap.find('#wp-content-editor-container > textarea').val(val);	
		}
		else {
            tinymce.activeEditor.execCommand('mceInsertContent', false, mg_sc);
        }
		
		
		// closes magpopup
		$.magnificPopup.close();
	});

})(jQuery);
