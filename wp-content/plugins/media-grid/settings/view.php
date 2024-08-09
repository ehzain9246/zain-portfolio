<?php 
$ml_key = 'mg_ml';
dike_lc('lcweb', MG_DIKE_SLUG, true);


// setup woocommerce data for attribute icons
$wa_array = apply_filters('active_plugins', get_option( 'active_plugins' ));
$GLOBALS['mg_woocom_active'] = (in_array('woocommerce/woocommerce.php', $wa_array)) ? true : false;

if($GLOBALS['mg_woocom_active']) {
	$GLOBALS['mg_woocom_atts'] = wc_get_attribute_taxonomies(); 	
}


// framework engine
include_once(MG_DIR . '/classes/simple_form_validator.php');
include_once(MG_DIR . '/settings/settings_engine.php'); 
include_once(MG_DIR . '/settings/field_options.php'); 
include_once(MG_DIR . '/settings/custom_fields.php');
include_once(MG_DIR . '/settings/structure.php'); 

$engine = new mg_settings_engine('mg_settings', $GLOBALS['mg_settings_tabs'], $GLOBALS['mg_settings_structure']);
?>

<div class="wrap lcwp_settings_wrap">
    <div class="lcwp_settings_head">
        <h2><?php esc_html_e('Media Grid Settings', $ml_key) ?></h2>  
        
        <div>
            <?php echo $engine->import_export_btns() ?>
            <form class="lcwp_sf_search_wrap">
                <i class="dashicons dashicons-no-alt"></i>
                <input type="text" name="lcwp_sf_search" value="" placeholder="<?php esc_attr_e('search fields', $ml_key) ?> .." /> 
            </form>
        </div>
    </div>
    
    
	<?php
    // get fetched data and allow customizations
    if($engine->form_submitted()) {
        $fdata = $engine->form_data;
        $errors = (!empty($engine->errors)) ? $engine->errors : array();
        
		///////////////
		
		// lightbox comments - be sure required data is set
		if(isset($fdata['mg_lb_comments']) && $fdata['mg_lb_comments']) {
			
			if($fdata['mg_lb_comments'] == 'disqus' && empty($fdata['mg_lbc_disqus_shortname'])) {
				$errors[ __('Disqus shortname', 'mg_ml') ] = __('missing value', 'mg_ml');
			}
			else if($fdata['mg_lb_comments'] == 'fb' && empty($fdata['mg_lbc_fb_app_id'])) {
				$errors[ __('Facebook app ID', 'mg_ml') ] = __('missing value', 'mg_ml');	
			}
		}


		// attributes builder custom validation
		foreach(mg_static::main_types() as $type => $name) {
			if($fdata['mg_'. $type .'_opt']) {
				$a = 0;
				foreach($fdata['mg_'. $type .'_opt'] as $opt_val) {
					if(trim($opt_val) == '') {
                        unset($fdata['mg_'. $type .'_opt'][$a]);
                    }
					$a++;
				}
				
				if( count(array_unique($fdata['mg_'. $type .'_opt'])) < count($fdata['mg_'. $type .'_opt']) ) {
					$errors[ $name .' '. __('Options', 'mg_ml') ] = __('There are duplicate values', 'mg_ml');
				}
			}
		}
		
        
		///////////////

        
        // save or print error
        if(empty($errors)) {
            
            // MG-FILTER - allow data manipulation (or custom actions) before settings save - passes form values
            $engine->form_data = apply_filters('mg_before_save_settings', $fdata); 
            
            
            // save
            $engine->save_data();


            // create custom style css file
          	if(!get_option('mg_inline_css')) {
				if(!mg_static::create_frontend_css()) {
					update_option('mg_inline_css', 1);	
					echo '<div class="updated"><p>'. __('An error occurred during dynamic CSS creation. The code will be used inline anyway', 'mg_ml') .'</p></div>';
					$noredirect = true;
				}
				else {
                    delete_option('mg_inline_css');
                }
            }
			
			
			
			// refresh to allow saved values to be spread in structure
			if(!isset($noredirect)) {
				$engine->successful_save_redirect();
			}
        }
        else {
            echo $engine->get_error_message_html($errors);
        }
    }
	
	
	// if successfully saved
	echo $engine->get_success_message_html();
	
	// print form code
    echo $engine->get_code();
    ?>
</div>


<script type="text/javascript">
// show dates legend on click
(function($) { 
    "use strict"; 
    
    $(document).on('click', '.nb_date_guide', function(e) {
        e.preventDefault();

        $.magnificPopup.open({
            items : {
                src: '#nb_date_legend',
                type: 'inline'
            },
            mainClass	: 'nb_magpop nb_dates_helper', 
            preloader	: false,
            callbacks	: {
              beforeOpen: function() {
                if($(window).width() < 800) {
                  this.st.focus = false;
                }
              },
            }
        });
    });
    
})(jQuery); 
</script>
<?php ///////////////////////////// ?>



<?php // SCRIPTS ?>
<script type="text/javascript">
(function($) { 
    "use strict";     
    
    $(document).ready(function($) {
        const lcwp_nonce = '<?php echo wp_create_nonce('lcwp_ajax') ?>',
              settings_baseurl = `<?php echo $engine->baseurl ?>`;
        

        // codemirror - execute before tabbing
        $('.lcwp_sf_code_editor').each(function() {
            CodeMirror.fromTextArea( $(this)[0] , {
                lineNumbers: true,
                mode: "css"
            });
        });


        //////////////////////////////////////////////////


        // options search
        let lcwp_sf_search_tout = false;
        $(document).on('keyup', '.lcwp_sf_search_wrap input', function(e) {
            const val = $(this).val().trim();

            if(lcwp_sf_search_tout) {
                clearTimeout(lcwp_sf_search_tout);    
            }

            lcwp_sf_search_tout = setTimeout(function() {
                // reset
                $('.lcsw_sf_search_no_res').remove();
                $('.lcsw_sf_search_excluded').removeClass('lcsw_sf_search_excluded');

                // elaborate
                if(val.length < 3) {
                    $('.lcwp_sf_search_wrap').removeClass('lcwp_sf_searching');
                }
                else {
                    $('.lcwp_sf_search_wrap').addClass('lcwp_sf_searching');
                    $('.lcwp_sf_spacer').parent().addClass('lcsw_sf_search_excluded');  

                    // cycle through sections
                    $('.lcwp_settings_table').each(function() {
                        let hide_table = true;

                        $(this).find('.lcwp_sf_label label').each(function() {
                            const $tr = $(this).parents('tr').first();

                            let matching_string = $(this).text().trim().toLowerCase();
                            if($tr.find('.lcwp_sf_note').length) {
                                matching_string += ' '+ $tr.find('.lcwp_sf_note').text();
                            }

                            if(matching_string.indexOf( val.toLowerCase() ) === -1) {
                                $tr.addClass('lcsw_sf_search_excluded');    
                            }
                            else {
                                hide_table = false;
                                $tr.removeClass('lcsw_sf_search_excluded');    
                            }
                        });

                        if(hide_table) {
                            $(this).addClass('lcsw_sf_search_excluded');
                            $(this).prev('h3').addClass('lcsw_sf_search_excluded');
                        }
                    });

                    // leave only tabs with matching options
                    $('.lcwp_settings_block').each(function() {
                        if(!$(this).find('> *:not(.lcsw_sf_search_excluded):not(script):not(style)').length) {
                            $('a.nav-tab[href="#'+ $(this).attr('id') +'"]').addClass('lcsw_sf_search_excluded');    
                        }
                    });

                    // select first tab with matching options
                    if($('a.nav-tab').not('.lcsw_sf_search_excluded').length) {
                        $('a.nav-tab').not('.lcsw_sf_search_excluded').first().click();
                    } else {
                        $('.nav-tab-wrapper').append('<span class="lcsw_sf_search_no_res"><?php esc_html_e('No matching options', $ml_key) ?> ..</span>');    
                    }
                }
            }, 500);
        });
        $('.lcwp_sf_search_wrap input').val(''); // avoid browser cache


        // reset search
        $(document).on('click', '.lcwp_sf_search_wrap i', function() {
            $('.lcwp_sf_search_wrap input').val('').trigger('keyup');        
        });

        

        //////////////////////////////////////////////////

        

        // tabify
        $('.lcwp_settings_tabs').each(function() {
            var sel = '';
            var hash = window.location.hash;

            var $form = $(".lcwp_settings_form");
            var form_act = $form.attr('action');

            // track URL on opening
            if(hash && $(this).find('.nav-tab[href="'+ hash +'"]').length) {
                $(this).find('.nav-tab').removeClass('nav-tab-active');
                $(this).find('.nav-tab[href="'+ hash +'"]').addClass('nav-tab-active');	

                $form.attr('action', form_act + hash);
            }

            // if no active - set first as active
            if(!$(this).find('.nav-tab-active').length) {
                $(this).find('.nav-tab').first().addClass('nav-tab-active');	
            }

            // hide unselected
            $(this).find('.nav-tab').each(function() {
                var id = $(this).attr('href');

                if($(this).hasClass('nav-tab-active')) {
                    sel = id
                }
                else {
                    $(id).hide();
                }
            });

            // scroll to top by default
            $("html, body").animate({scrollTop: 0}, 0);

            // track clicks
            if(sel) {
                $(this).find('.nav-tab').click(function(e) {
                    e.preventDefault();
                    if($(this).hasClass('nav-tab-active')) {return false;}

                    var sel_id = $(this).attr('href');
                    window.location.hash = sel_id.replace('#', '');

                    $form.attr('action', form_act + sel_id);

                    // show selected and hide others
                    $(this).parents('.lcwp_settings_tabs').find('.nav-tab').each(function() {
                        var id = $(this).attr('href');

                        if(sel_id == id) {
                            $(this).addClass('nav-tab-active');
                            $(id).show();		
                        }
                        else {
                            $(this).removeClass('nav-tab-active');
                            $(id).hide();	
                        }
                    });
                });
            }
        });


        // sticky tabs on scroll
        let lcwp_sf_sticky_tabs_tout = false;

        const $tabs = $('.lcwp_settings_tabs'),
              tabs_top_pos = Math.round($tabs.offset().top);

        const lcwp_sf_sticky_tabs = function() {
            if(lcwp_sf_sticky_tabs_tout) {
                clearTimeout(lcwp_sf_sticky_tabs_tout);    
            }

            lcwp_sf_sticky_tabs_tout = setTimeout(function() {
                if(document.documentElement.scrollTop > (tabs_top_pos + $tabs.outerHeight(true) + 20)) {
                    $('.lcwp_settings_form').css('margin-top', $tabs.outerHeight(true));
                    $tabs.addClass('lcwp_st_sticky');
                }
                else {
                    $('.lcwp_settings_form').css('margin-top', 0);
                    $tabs.removeClass('lcwp_st_sticky'); 
                }
            }, 10);
        };
        $(window).scroll(function() {
            lcwp_sf_sticky_tabs();
        });
        $(window).resize(function() {
            lcwp_sf_sticky_tabs();    
        });
        lcwp_sf_sticky_tabs(); // on page's show




        // sliders
        new lc_range_n_num('.lcwp_sf_slider_input', {
            unit_width: 17    
        });


        // colorpicker
        $('.lcwp_sf_colpick').each(function() {
            let modes = $(this).data('modes'),
                alpha = (modes.indexOf('alpha') !== -1) ? true : false;

            modes = (modes) ? modes.trim().split(' ') : [];
            modes.push('solid');

            // remove alpha mode
            const index = modes.indexOf('alpha');
            if(index !== -1) {
              modes.splice(index, 1);
            }

            // def colors 
            let def_color = $(this).data('def-color');
            def_color = (def_color.indexOf('gradient') !== -1) ? ['#008080', def_color] : [def_color, 'linear-gradient(90deg, #ffffff 0%, #000000 100%)']; 

            new lc_color_picker('input[name="'+ $(this).attr('name') +'"]', {
                modes           : modes,
                transparency    : alpha,
                no_input_mode   : false,
                wrap_width      : '90%',
                fallback_colors : def_color,
                preview_style   : {
                    input_padding   : 40,
                    side            : 'right',
                    width           : 35,
                },
            });
        });


        // lc switch
        lc_switch('.lcwp_sf_check', {
            on_txt      : "<?php echo strtoupper(__('yes')) ?>",
            off_txt     : "<?php echo strtoupper(__('no')) ?>",   
        });


        // lc select
        const lcwp_sf_live_select = function() { 

            new lc_select('.lcwp_sf_select', {
                wrap_width : '90%',
                addit_classes : ['lcslt-lcwp'],
            });
        }
        lcwp_sf_live_select();


        // auto-height textarea
        window.lcwp_sf_textAreaAdjust = function(o) {
            o.style.height = "1px";
            o.style.height = (4 + o.scrollHeight)+"px";
        };
        $('.lcwp_sf_textarea').each(function() {
            lcwp_sf_textAreaAdjust(this);    
        });



        //////////////////////////////////////////////////


        // fixed submit position
        const lcwp_sf_fixed_submit = function(btn_selector) {
            const $subj = $(btn_selector);
            if(!$subj.length) {return false;}

            let clone = $subj.clone().wrap("<div />").parent().html();

            setInterval(function() {

                // if page has scrollers or scroll is far from bottom
                if(($(document).height() > $(window).height()) && ($(document).height() - $(window).height() - $(window).scrollTop()) > 130) {
                    if(!$('.lcwp_settings_fixed_submit').length) {	
                        $subj.after('<div class="lcwp_settings_fixed_submit">'+ clone +'</div>');
                    }
                }
                else {
                    if($('.lcwp_settings_fixed_submit').length) {	
                        $('.lcwp_settings_fixed_submit').remove();
                    }
                }
            }, 50);
        };
        lcwp_sf_fixed_submit('.lcwp_settings_submit');


        //////////////////////////////////////////////////


        // popup message for better visibility
        if($('.lcwp_settings_result').length) {
            const $subj = $('.lcwp_settings_result');

            // if success - simply hide main one
            if($subj.hasClass('updated')) {
                $subj.remove();	
                lc_wp_popup_message('success', '<p>'+ $subj.find('p').html() +'</p>');   
            }

            // show errors but keep them visible on top
            else {
                const pre_heading = (window.location.href.indexOf('lcwp_sf_import') !== -1) ? 
                    `<?php esc_html_e('One or more errors occurred during the import', $ml_key) ?>` : 
                    `<?php esc_html_e('One or more errors occurred', $ml_key) ?>`;
                      
                const error_contents = ($subj.find('ul').length) ? $subj.find('ul')[0].outerHTML : $subj.find('p')[0].innerHTML;
                
                lc_wp_popup_message('error', "<h4><?php esc_html_e('One or more errors occurred', $ml_key) ?>:</h4>" + error_contents);
                $("html, body").animate({scrollTop: 0}, 0);


                // try adding links bringing directly to option lines
                $subj.find('ul li').each(function() {
                    const $err_li = $(this);

                    let subjs = $err_li.text().split(' - ')[0];
                    subjs = subjs.split(',');

                    $.each(subjs, function(i, label) {
                        label = label.toString().trim();

                        $('.lcwp_sf_label label').each(function() {
                            if( $(this).text().trim().toLowerCase() == label.toLowerCase() ) {
                                $err_li.html( 
                                    $err_li.html().replace(label, '<a href="#'+ $(this).parents('tr').first().attr('class') +'" title="<?php esc_attr_e('go to option', $ml_key) ?>" class="lcwp_sf_err_link">'+ label +'</a>')
                                );

                                return false;
                            }
                        });
                    });
                });
            }
            
            // remove eventual url parameters
            history.replaceState(null, null, settings_baseurl);
        }	


        // error-to-option direct search
        $(document).on('click', '.lcwp_sf_err_link', function(e) {
            e.preventDefault();
            const tr_selector = $(this).attr('href').replace('#', ''),
                  label = $('.'+tr_selector +' .lcwp_sf_label').text();

            $('.lcwp_sf_search_wrap input').val(label).trigger('keyup');
        });
    });
    
})(jQuery);     
</script>


<?php
// NB-ACTION - allow extra code printing in settings (for javascript/css)
do_action('nb_settings_extra_code');
?>
