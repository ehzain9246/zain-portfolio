<?php
// if deeplink is active - show lightbox on page's loading

add_action('wp_footer', 'mg_lightbox_deeplink', 999);

function mg_lightbox_deeplink() {

	// check deeplink existence
	if(!isset($GLOBALS['mg_deeplinks']) || !isset($GLOBALS['mg_deeplinks']['mgi'])) {
		return false;	
	}
	
	// check item existence and status
	$grid_id 	= (int)$GLOBALS['mg_deeplinks']['mgi']['grid_id'];
	$item_id 	= (int)$GLOBALS['mg_deeplinks']['mgi']['item_id'];
	$status 	= get_post_status($item_id);
	
	if($status != 'publish' || (is_user_logged_in() && !in_array($status, array('publish', 'draft', 'future')) )) {
		return false;	
	}

	// check item type - must have lightbox
	if(get_post_type($item_id) == 'mg_items' && !in_array(get_post_meta($item_id, 'mg_main_type', true), array('single_img', 'img_gallery', 'video', 'audio', 'lb_text', 'post_contents'))) {
		return false;	
	}


	// print lightbox 
	$modal_class = (get_option('mg_modal_lb')) ? 'mg_modal_lb' : 'mg_classic_lb';
   
    $forced_mf_mode = (isset($_GET['mg_mflb']) || get_option('mg_mf_lb_enabled')) ? true : false; 
    $ff_for_js = ($forced_mf_mode) ? 'true' : 'false';
	?>
    
	<div id="mg_lb_wrap" class="mg_displaynone">
    	<div id="mg_lb_loader"><?php echo mg_static::preloader() ?></div>
        <div id="mg_lb_contents" class="mg_lb_pre_show_next">
        	<?php mg_lightbox($item_id, false, false, $forced_mf_mode); ?>
		</div>
        <div id="mg_lb_scroll_helper" class="<?php echo $modal_class ?>"></div>
        <div id="mg_deeplinked_lb" class="mg_displaynone"></div>
	</div>
    
    <div id="mg_lb_background" class="<?php echo $modal_class ?>"></div>
    
    
    <?php // set lightbox contents var and show ?>
    <script type="text/javascript">
    (function($) { 
        "use strict";
        
        $(document).ready(function(e) {
            const gid = <?php echo $grid_id ?>;

            setTimeout(function() {
                $("#mg_lb_background").addClass("mg_lb_shown");

                // check for item existence in the page - otherwise just show without prev/next 
                const $grid_item = $(".mg_grid_wrap[data-grid-id='<?php echo $grid_id ?>'] .mgi_has_lb.mgi_<?php echo $item_id ?>");

                if($grid_item.length) {
                    $("#mg_lb_contents").empty();

                    $mg_sel_grid = $grid_item.first().parents(".mg_grid_wrap");

                    const media_focused_mode = ($mg_sel_grid.hasClass("mg_use_mf_lb")) ? true : false;
                    mg_open_item(<?php echo $item_id ?>, false, media_focused_mode);
                }
                else {
                   $("#mg_lb_contents").attr("class", "mg_lb_shown");
                   mg_open_item(<?php echo $item_id ?>, true, <?php echo $ff_for_js ?>);
                }
            }, 100); // a little delay to let muuri filters to be setup
        });
    })(jQuery); 
    </script>

    <?php
}
