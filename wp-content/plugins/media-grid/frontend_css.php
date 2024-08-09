<?php
////////////////////////////////////
// DYNAMICALLY CREATE THE CSS //////
////////////////////////////////////

include_once(MG_DIR . '/classes/loaders_switch.php');


// remove the HTTP/HTTPS for SSL compatibility
$safe_baseurl = str_replace(array('http:', 'https:', 'HTTP:', 'HTTPS:'), '', MG_URL);

$mobile_treshold 	= (int)get_option('mg_mobile_treshold', 800);
$cells_margin 		= (int)get_option('mg_cells_margin', 5);
$items_border_w 	= (int)get_option('mg_cells_img_border', 3);
$side_filters_w 	= (int)get_option('mg_side_filters_width', 160);
$it_padd            = get_option('mg_inl_txt_padding', array('15', '15', '15', '15'));

$items_border_radius    = (int)get_option('mg_cells_radius', 2);
$items_border_color     = get_option('mg_img_border_color', '#ccc');
$outer_border_color      = get_option('mg_cells_border_color', '#444');

$main_ol_color      = get_option('mg_main_overlay_color', '#fff');
$ol_tit_color       = get_option('mg_overlay_title_color', '#222');
$filters_border_col = get_option('mg_filters_border_color', '#999');
$filter_radius      = (int)get_option('mg_filters_radius', 2);

$src_txt_col = get_option('mg_search_txt_color', '#888');
$src_txt_col_h = get_option('mg_search_txt_color_h', '#666');

$lb_padding     = (int)get_option('mg_lb_padding', 20);
$lb_border_w    = (int)get_option('mg_lb_border_w', 0);
$lb_radius      = (int)get_option('mg_item_radius');

$mf_lb_padding      = (int)get_option('mg_mf_lb_padding', 0);
$mf_txt_h_padding   = (int)get_option('mg_mf_lb_txt_h_padding', 25);
$mf_calc_txt_h_padd = $mf_txt_h_padding;

if($mf_txt_h_padding != $mf_lb_padding) {
    $mf_calc_txt_h_padd = $mf_txt_h_padding - $mf_lb_padding;
    
    if($mf_calc_txt_h_padd < 0) {
        $mf_calc_txt_h_padd = 0;    
    }
}

$mf_lb_feat_margin      = (array)get_option('mg_mf_lb_margin', array(7, 10, 7, 10));
$mf_lb_feat_mob_margin  = (array)get_option('mg_mf_lb_mob_margin', array(2, 2, 2, 2));
$mg_mf_lb_txt_w         = (int)get_option('mg_mf_lb_txt_w', 450);
?>



<?php /* PRELOADER */ ?>
<?php mg_loaders_switch() ?>



<?php /* ITEMS MARGIN */ ?>
.mg_box { 
  border-width: 0 <?php echo $cells_margin ?>px <?php echo $cells_margin ?>px 0; 
}

<?php if(get_option('mg_rtl_grid')) : ?>
.mg_rtl_mode .mg_box {
	left: calc((15px + <?php echo $cells_margin ?>px) * -1) !important;
}
<?php endif; ?>

<?php if($cells_margin) : ?>
.mg_items_container {
	width: calc(100% + 20px + <?php echo $cells_margin ?>px);
}
.mg_items_container.mg_not_even_w {
	width: calc(100% + 20px + <?php echo $cells_margin ?>px + 1px);	
}
<?php endif; ?>



<?php /* ITEMS DESKTOP SIZING */ ?>
<?php
foreach(mg_static::item_sizes() as $key => $data) {
	echo '.mgis_w_'.$key.' {width: calc('. $data['perc'] * 100 .'% - '. round($data['perc'] * 20) .'px);}';
	echo '.mgis_h_'.$key.' {padding-bottom: calc('. $data['perc'] * 100 .'% - '. round($data['perc'] * 20) .'px - '. $cells_margin .'px);}';
}
?> 

<?php /* ITEMS MOBILE SIZING */ ?>
<?php
foreach(mg_static::mobile_sizes() as $key => $data) {
	echo '.mg_mobile_mode .mgis_m_w_'.$key.' {width: calc('. $data['perc'] * 100 .'% - '. round($data['perc'] * 20) .'px);}';
	echo '.mg_mobile_mode .mgis_m_h_'.$key.' {padding-bottom: calc('. $data['perc'] * 100 .'% - '. round($data['perc'] * 20) .'px - '. $cells_margin .'px);}';
}
?> 



<?php /* ITEMS SHADOW */ ?>
<?php if(get_option('mg_cells_shadow')) : ?>
.mg_grid_wrap {
	padding: 4px;
}
.mg_box:not(.mg_spacer) .mg_box_inner {
	box-shadow: 0px 0px 4px rgba(25, 25, 25, 0.3);
}
.mg_tu_attach .mgi_txt_under {
	box-shadow: 4px 0px 4px -4px rgba(25, 25, 25, 0.3), -4px 0px 4px -4px rgba(25, 25, 25, 0.3), 0 4px 4px -4px rgba(25, 25, 25, 0.3);
}
<?php endif; ?>



<?php /* ITEMS BORDER */ ?>
.mg_box_inner {
	border-style: solid;
    padding: <?php echo $items_border_w ?>px;
	background: <?php echo $items_border_color ?>;
    border: <?php echo (get_option('mg_cells_border')) ? 1 : 0; ?>px solid <?php echo $outer_border_color ?>; 
}



<?php 
/* LIGHT SELF-HOSTED PLAYER */ 
if(get_option('mg_players_theme') == 'light') :
?>
.mg_me_player_wrap .mejs-controls, 
.mg_me_player_wrap .mejs-volume-slider,
.mg_audio_tracklist {
	background: #fcfcfc !important;
}
.mg_me_player_wrap .mejs-button button:before,
.mg_me_player_wrap .mejs-time,
.mg_audio_tracklist {
	color: #555;
}
.mg_audio_tracklist li {
    border-bottom: 1px solid #ddd;
    border-top: none;
}
.mg_audio_tracklist li:before {
    background: #e3e3e3;
    color: #333;
    border-color: #c9c9c9
}
.mg_me_player_wrap .mejs-mg-tracklist-off button:before,
.mg_me_player_wrap .mejs-mg-loop-off button:before {
	color: #848484 !important;
}
.mg_audio_tracklist li.mg_current_track {
	background-color: #efefef;
	color: #444;
}
.mg_audio_tracklist li:not(.mg_current_track):hover {
	background-color: #f5f5f5;
	color: #484848;
}
.mg_me_player_wrap .mejs-time-slider {
	background: #eee !important;
}
.mg_me_player_wrap .mejs-time-total,
.mg_me_player_wrap .mejs-time-total > span:not(.mejs-time-handle):not(.mejs-time-float) {
    box-shadow: 0 0 0 1px #aaa;
}
.mg_me_player_wrap .mejs-time-current {
	background: #fff !important;
}
.mg_me_player_wrap .mejs-time-loaded,
.mg_me_player_wrap .mejs-controls .mejs-volume-slider .mejs-volume-total {
	background: #d4d4d4 !important;
}
.mg_me_player_wrap .mejs-time-handle {
	box-shadow: 0 0 0 1px #777;
}
.mg_me_player_wrap .mejs-controls .mejs-volume-slider .mejs-volume-handle {
	background: #555;
}
.mg_me_player_wrap .mejs-controls .mejs-volume-slider .mejs-volume-current {
    background: #a2a2a2;
}
<?php endif; ?> 



<?php /* OVERLAYS */ ?>
.mgi_overlays {
    top: <?php echo $items_border_w ?>px; 
    right: <?php echo $items_border_w ?>px; 
    bottom: <?php echo $items_border_w ?>px; 
    left: <?php echo $items_border_w ?>px;
}
.mgi_primary_ol,
.mg_inl_slider_wrap .lcms_content,
.mg_inl_slider_wrap .lcms_nav span {
	background: <?php echo $main_ol_color ?>;
}
.mg_inl_slider_wrap .lcms_content {
	background: <?php echo mg_static::hex2rgba($main_ol_color, 0.85) ?>;
}
body:not(.mg_cust_touch_ol_behav) .mg_box:hover .mgi_primary_ol,
.mg_box.mg_ctob_show .mgi_primary_ol {
   <?php
	// alpha
	$alpha_val = (int)get_option('mg_main_overlay_opacity') / 100;  
	
	echo '
	opacity: '.$alpha_val.';';
	?> 
}
.mgi_item_type_ol {
	border-bottom-color: <?php echo get_option('mg_second_overlay_color', '#474747') ?>;
}
span.mg_overlay_tit,
.mg_inl_slider_wrap .lcms_content,
.mg_inl_slider_wrap .lcms_nav span:before,
.mg_inl_slider_wrap .lcms_play span:before {
	color: <?php echo $ol_tit_color ?>;
}
.mg_overlay_tit {
	background: <?php echo mg_static::hex2rgba($main_ol_color, round($alpha_val * 0.8, 1)) ?>;
    text-shadow: 0px 0 0px <?php echo mg_static::hex2rgba($ol_tit_color, 0.4) ?>;
}
.mg_overlay_tit,
.mg_inl_slider_wrap .lcms_content {    	
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_ol_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
    font-size: <?php echo get_option('mg_ol_font_size', 15) . get_option('mg_ol_font_size_type', 'px') ?>;
}
.mg_mobile_mode .mg_overlay_tit,
.mg_mobile_mode .mg_inl_slider_wrap .lcms_content {
	font-size: <?php echo get_option('mg_mobile_ol_font_size', 13) . get_option('mg_mobile_ol_font_size', 'px') ?>;
}
.mg_overlay_tit:before {
	border-bottom-color: <?php echo mg_static::hex2rgba($ol_tit_color, round($alpha_val * 0.5, 1)) ?>;
}


<?php /* overlay icons color */ ?>
.mgi_item_type_ol span:before {
    color: <?php echo get_option('mg_icons_col') ?>;
}



<?php /* ELEMENTS BORDER RADIUS */ ?>
.mg_box_inner, 
.mg_box .mg_media_wrap,
.mgi_overlays,
.mg_inl_txt_media_bg,
.mg_inl_slider_wrap .lcms_content,
.mg_inl_slider_wrap .lcms_nav *,
.mg_inl_slider_wrap .lcms_play {
  border-radius: <?php echo $items_border_radius ?>px;
}
.mg_tu_attach .mgi_txt_under {
    border-bottom-left-radius: <?php echo $items_border_radius ?>px;
    border-bottom-right-radius: <?php echo $items_border_radius ?>px;	
}



<?php 
// HIDING overlays
if(get_option('mg_hide_overlays')) : ?>
.mgi_overlays {
	display: none !important;
}
<?php endif; ?>



<?php /* TEXTS UNDER ITEMS */ ?>
.mgi_txt_under {
    <?php 
	echo 'color: '. get_option('mg_txt_under_color', '#333') .';';

	$pdg = $items_border_w; 
	
	$pdg_t = ($pdg < 6) ? 6 : $pdg;
	$pdg_r = ($pdg < 10) ? 10 : $pdg; 
	$pdg_b = ($pdg < 10) ? 10 : $pdg; 
	$pdg_l = ($pdg < 6) ? 6 : $pdg;  
	 
	$cust_pdg = get_option('mg_tu_custom_padding');
	if(is_array($cust_pdg)) {
		if($cust_pdg[0] != '') {$pdg_t = $cust_pdg[0];}
		if($cust_pdg[1] != '') {$pdg_r = $cust_pdg[1];}
		if($cust_pdg[2] != '') {$pdg_b = $cust_pdg[2];}
		if($cust_pdg[3] != '') {$pdg_l = $cust_pdg[3];}
	}
	
	?>	
    padding-top: 	<?php echo $pdg_t; ?>px !important;
    padding-right: 	<?php echo $pdg_r; ?>px;
    padding-bottom: <?php echo $pdg_b; ?>px;
    padding-left: 	<?php echo $pdg_l; ?>px;
}
.mg_def_txt_under {  	
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_txt_under_font_family', ''));
    if(!empty($font_family)) {
        echo 'font-family: "'.$font_family.'";';
    }
	?>
    font-size: <?php echo get_option('mg_txt_under_font_size', 15) . get_option('mg_txt_under_font_size_type', 'px') ?>;
}
.mg_mobile_mode .mg_def_txt_under {
	font-size: <?php echo get_option('mg_mobile_txt_under_font_size', 15) . get_option('mg_mobile_txt_under_font_size_type', 'px') ?>;
}
.mg_tu_attach .mgi_txt_under {
	 background: <?php echo $items_border_color ?>;
	 
	 <?php
	if(get_option('mg_cells_border')) {
	  echo '
	  	border-color: '. $outer_border_color .';
		border-width: 0px 1px 1px;
    	border-style: solid;
		
		margin-top: -1px;
	  ';   
	}
	?> 
}
.mg_tu_detach .mgi_txt_under {
	margin-top: 3px;
}



<?php /* INLINE TEXT ITEMS */ ?>
.mg_inl_txt_contents {
    padding: <?php echo '0 '. $it_padd[1].'px 0 '.$it_padd[3].'px' ?>;
    border-width: <?php echo $it_padd[0].'px 0 '.$it_padd[2].'px 0' ?>;
}
.mg_grid_wrap:not(.mg_mobile_mode) .mgis_h_auto .mg_inl_txt_media_bg,
.mg_mobile_mode .mgis_m_h_auto .mg_inl_txt_media_bg {
	top: <?php echo $items_border_w ?>px;
    bottom: <?php echo $items_border_w ?>px;
   	left: <?php echo $items_border_w ?>px;
    right: <?php echo $items_border_w ?>px; 
}


<?php /* INLINE SELF-HOSTED VIDEO */ ?>
.mg_sh_inl_video video {
	background-color: <?php echo $items_border_color; ?>;
}




<?php /* SPACER VISIBILITY */ ?>
.mg_grid_wrap:not(.mg_mobile_mode) .mg_spacer_hidden_desktop,
.mg_mobile_mode .mg_spacer_hidden_mobile {
    max-width: 0 !important;
    max-height: 0 !important;
    padding: 0 !important;
}




<?php /* FILTERS AND SEARCH */ ?>
.mg_filters_wrap .mgf,
.mgf_search_form input, .mgf_search_form i:before {	
	color: <?php echo get_option('mg_filters_txt_color', '#444'); ?>;
    font-size: <?php echo get_option('mg_filters_font_size', 14) . get_option('mg_filters_font_size_type', 'px') ?>;
}
.mg_filters_wrap .mgf,
.mgf_search_form input {
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_filters_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
}
.mg_mobile_mode .mg_filters_wrap .mgf,
.mg_mobile_mode .mgf_search_form input, 
.mg_mobile_mode .mgf_search_form i:before {	
	font-size: <?php echo get_option('mg_mobile_filters_font_size', 12) . get_option('mg_mobile_filters_font_size_type', 'px') ?>;
}


.mg_filters_wrap .mgf:hover {		
	color: <?php echo get_option('mg_filters_txt_color_h', '#666'); ?> !important;
}
.mg_filters_wrap .mgf.mgf_selected, 
.mg_filters_wrap .mgf.mgf_selected:hover {		
	color: <?php echo get_option('mg_filters_txt_color_sel', '#222'); ?> !important;
}
.mg_textual_filters .mgf_selected {
	text-shadow: 0 0.01em 0 <?php echo get_option('mg_filters_txt_color_sel', '#222'); ?>;
}
.mg_button_filters .mgf,
.mg_textual_filters .mgf:after {	
	background-color: <?php echo get_option('mg_filters_bg_color', '#fff'); ?>;
}  
.mg_button_filters .mgf,
.mgf_search_form input {
    border: <?php echo (int)get_option('mg_filter_n_search_border_w', 2) ?>px solid <?php echo $filters_border_col ?>;
    border-radius: <?php echo $filter_radius ?>px;
}

<?php /* hover state */ ?>
.mg_button_filters .mgf:hover,
.mg_textual_filters .mgf:hover:after,
.mgf_search_form input:hover {	
	background-color: <?php echo get_option('mg_filters_bg_color_h', '#fff'); ?>;
}
.mg_button_filters .mgf:hover,
.mgf_search_form input:hover {	   
    border-color: <?php echo get_option('mg_filters_border_color_h', '#666'); ?>;
}

<?php /* selected state */ ?>
.mg_button_filters .mgf_selected, .mg_button_filters .mgf_selected:hover,
.mg_textual_filters .mgf_selected:after, .mg_textual_filters .mgf_selected:hover:after,
.mgf_search_form input:focus {	
	background-color: <?php echo get_option('mg_filters_bg_color_sel', '#fff') ?>;
}
.mg_button_filters .mgf_selected, .mg_button_filters .mgf_selected:hover,
.mgf_search_form input:focus {	   
    border-color: <?php echo get_option('mg_filters_border_color_sel', '#555') ?>;
}


<?php /* side filters */ ?>
.mg_left_filters:not(.mg_mobile_mode) .mg_above_grid,
.mg_right_filters:not(.mg_mobile_mode) .mg_above_grid {
	width: <?php echo $side_filters_w ?>px;
    min-width: <?php echo $side_filters_w ?>px;
}
.mg_left_filters:not(.mg_mobile_mode) .mg_items_container {
    border-left-width: <?php echo $side_filters_w ?>px;
}
.mg_right_filters:not(.mg_mobile_mode) .mg_items_container {
    border-right-width: <?php echo $side_filters_w ?>px;
}
.mg_left_filters:not(.mg_mobile_mode) > .mg_loader {
	transform: translateX(<?php echo ($side_filters_w / 2) ?>px);
} 
.mg_right_filters:not(.mg_mobile_mode) > .mg_loader {
    transform: translateX(-<?php echo ($side_filters_w / 2) ?>px);
}
.mg_has_search.mg_left_filters:not(.mg_mobile_mode) .mgf_search_form:after,
.mg_has_search.mg_right_filters:not(.mg_mobile_mode) .mgf_search_form:after {
	border-bottom-color: <?php echo $outer_border_color ?>;
}


<?php /* SPECIFIC SEARCHBOX COLORS */ ?>
.mgf_search_form input, 
.mgf_search_form i:before {	
	color: <?php echo $src_txt_col ?>;
    background-color: <?php echo get_option('mg_search_bg_color', '#eee'); ?>;
    border-color: <?php echo get_option('mg_search_border_color', '#eee'); ?>
}
.mgf_search_form:hover input, 
.mgf_search_form input:focus, .mgf_search_form:hover input:focus,
.mgf_search_form:hover i:before {
	color: <?php echo $src_txt_col_h ?>;
    background-color: <?php echo get_option('mg_search_bg_color_h', '#eee'); ?>;
	border-color: <?php echo get_option('mg_search_border_color_h', '#eee'); ?>
}	

<?php /* plaeholder color */ ?>
.mgf_search_form input::-webkit-input-placeholder {color: <?php echo $src_txt_col ?>;}
.mgf_search_form input:-ms-input-placeholder {color: <?php echo $src_txt_col ?>;}
.mgf_search_form input::placeholder {color: <?php echo $src_txt_col ?>;}


.mgf_search_form:hover input::-webkit-input-placeholder,
.mgf_search_form input:focus::-webkit-input-placeholder,
.mgf_search_form:hover input:focus::-webkit-input-placeholder {
	color: <?php echo $src_txt_col_h ?>;
} 

.mgf_search_form:hover input:-ms-input-placeholder,
.mgf_search_form input:focus::-ms-input-placeholder,
.mgf_search_form:hover input:focus:-ms-input-placeholder {
	color: <?php echo $src_txt_col_h ?>;
}

.mgf_search_form:hover input::placeholder,
.mgf_search_form input:focus::placeholder,
.mgf_search_form:hover input:focus::placeholder {
	color: <?php echo $src_txt_col_h ?> !important;
}


<?php /* DROPDOWN FILTER */ ?>
.mg_mobile_mode .mg_dd_mobile_filters .mgf_inner {
	border: <?php echo (int)get_option('mg_filter_n_search_border_w', 2) ?>px solid <?php echo $filters_border_col ?>;
    border-radius: <?php echo $filter_radius ?>px;
    color: <?php echo get_option('mg_filters_txt_color', '#444'); ?>;
}
.mg_mobile_mode .mg_dd_mobile_filters .mgf_inner.mgf_dd_expanded .mgf {
	border-bottom-color: <?php echo mg_static::hex2rgba($filters_border_col, 0.5) ?>;	
}


<?php /* "NO RESULTS" BOX */ ?>
.mg_no_results:before {
	background-color: <?php echo get_option('mg_filters_bg_color', '#fff'); ?>;
    box-shadow: 0 0 0 1px <?php echo $filters_border_col ?> inset;
    border-radius: <?php echo $filter_radius ?>px;
    color: <?php echo get_option('mg_filters_txt_color', '#444'); ?>;
}



<?php /* PAGINATION ELEMS */ ?>
.mg_pag_wrap {
	text-align: <?php echo get_option('mg_pag_align', 'center') ?>;
}
.mg_right_filters:not(.mg_mobile_mode) .mg_pag_wrap {
    right: <?php echo $side_filters_w ?>px;
}
.mg_left_filters:not(.mg_mobile_mode) .mg_pag_wrap {
	left: <?php echo $side_filters_w ?>px;
}
.mg_pag_wrap > * {
	color: <?php echo get_option('mg_pag_txt_col', '#666666'); ?>;
    background-color: <?php echo get_option('mg_pag_bg_col', '#ffffff'); ?>;
	border: <?php echo (int)get_option('mg_pag_border_w', 1) ?>px solid <?php echo get_option('mg_pag_border_col', '#bbbbbb'); ?>;
    border-radius: <?php echo $filter_radius ?>px;
}
.mg_pag_wrap > *:not(.mg_pag_disabled):not(.mg_nav_mid):hover,
.mg_sel_pag, .mg_sel_pag:hover {
	color: <?php echo get_option('mg_pag_txt_col_h', '#eeeeee'); ?>;
    background-color: <?php echo get_option('mg_pag_bg_col_h', '#efefef'); ?>;
	border-color: <?php echo get_option('mg_pag_border_col_h', '#aaaaaa'); ?>;
}
.mg_pag_standard .mg_prev_page:before, .mg_pag_onlynum .mg_prev_page:before,
.mg_pag_standard .mg_next_page:before, .mg_pag_onlynum .mg_next_page:before {
	background: <?php echo get_option('mg_pag_border_col_h', '#aaaaaa'); ?>;
}
<?php



/////////////////////////////////////////////////////////////////////////////////////




/*** LIGHTBOX ***/
?>
#mg_lb_loader {
	border-radius: <?php echo get_option('mg_lb_loader_radius', 18) ?>%;
}
#mg_lb_background {
	<?php  
    // color
    if(get_option('mg_item_overlay_color') && get_option('mg_item_overlay_color') != '') {
        $item_ol_col = get_option('mg_item_overlay_color');
    }
    else {$item_ol_col = '#fff';}  

    // pattern
    if(get_option('mg_item_overlay_pattern') && get_option('mg_item_overlay_pattern') != 'none') {
        $pat = 'url('. $safe_baseurl .'/img/patterns/'.get_option('mg_item_overlay_pattern').') repeat top left';
    }
    else {$pat = '';}
    
    echo 'background: '.$pat.' '.$item_ol_col.';';  
    ?>  
}
#mg_lb_background.mg_lb_shown {
	<?php 
	// alpha
    $alpha_val = (int)get_option('mg_item_overlay_opacity', 70) / 100;  
	echo '
	opacity: '.$alpha_val.';';
	?>
}
#mg_lb_contents {
	<?php 
    $w = (get_option('mg_item_width')) ? get_option('mg_item_width') : 70;
    echo 'width: '.$w.'%;';
    
    $w = (get_option('mg_item_maxwidth')) ? get_option('mg_item_maxwidth') : 960;
    echo 'max-width: '.$w.'px;';
	
    if($lb_border_w) {
		echo 'border: '. $lb_border_w .'px solid	'.get_option('mg_item_border_color', '#e5e5e5').';';
	}
	
	echo 'border-radius: '. $lb_radius .'px;';

	$lb_cmd_pos = get_option('mg_lb_cmd_pos', 'inside');
	
	$top_padd = ($lb_cmd_pos == 'inside' || $lb_cmd_pos == 'ins_hidden') ? 52 : $lb_padding;
	echo 'padding: '. $top_padd .'px '. $lb_padding .'px '. $lb_padding .'px;';
    ?>
}
.mg_mf_lb #mg_lb_contents {
	<?php
	echo 'padding: '. $mf_lb_padding .'px;';
    ?>
}
.mg_item_title {
	font-size: <?php echo get_option('mg_lb_title_font_size', 20) . get_option('mg_lb_title_font_size_type', 'px') ?>;
    <?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_lb_title_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
}
.mg_item_text {
    font-size: <?php echo get_option('mg_lb_txt_font_size', 16) . get_option('mg_lb_txt_font_size_type', 'px') ?>;
    line-height: <?php echo (int)get_option('mg_lb_txt_line_height', 120) ?>%;  
}
.mg_item_text,
ul.mg_cust_options {
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_lb_txt_font_family', ''));
    if(!empty($font_family)) {
        echo 'font-family: "'.$font_family.'";';
    }
	?>
}


/* media-focused lightbox mode */
.mg_mf_lb .mg_item_content {
    width: auto !important;
    min-width: <?php echo $mg_mf_lb_txt_w + $mf_lb_padding ?>px;
    max-width: <?php echo $mg_mf_lb_txt_w + $mf_lb_padding ?>px;

    <?php
    $top_pos = $lb_border_w;
    $top_pos = (in_array($lb_cmd_pos, array('inside', 'ins_hidden'))) ? $top_pos + 52 : $top_pos + $mf_lb_padding;
    ?>

    top: <?php echo $top_pos ?>px !important;
    bottom: <?php echo $lb_border_w + $mf_lb_padding ?>px !important;
}
.mg_mf_lb .mg_item_featured {
    max-width: calc(100% - <?php echo $mg_mf_lb_txt_w ?>px);
}
.mg_mf_lb #mg_lb_contents {
    <?php $mf_addit_top_margin = (in_array($lb_cmd_pos, array('inside', 'ins_hidden'))) ? 0 : 52; ?>
    margin: calc(<?php echo $mf_lb_feat_margin[0].'vh + '. $mf_addit_top_margin .'px) '. $mf_lb_feat_margin[1].'vw '. $mf_lb_feat_margin[2].'vh '. $mf_lb_feat_margin[3].'vw' ?> !important;
}

.mg_mf_lb .mg_item_featured,
.mg_mf_lb .mg_item_featured > *,
.mg_mf_lb #mg_lb_feat_img_wrap > img,
.mg_mf_lb .mg_lb_lcms_slider:not(.mg_lb_lcms_thumbs_shown) div.lcms_wrap {
    max-height: calc(100vh - <?php echo (int)$mf_lb_feat_margin[0] + (int)$mf_lb_feat_margin[2] ?>vh - <?php echo ($lb_border_w + $mf_lb_padding) * 2 ?>px  - 52px);
}
.mg_mf_lb .mg_lb_zoom_wrap {
    max-height: calc(100vh - <?php echo (int)$mf_lb_feat_margin[0] + (int)$mf_lb_feat_margin[2] ?>vh - <?php echo ($lb_border_w + $mf_lb_padding) * 2 ?>px);
}
.mg_mf_lb .mg_lb_lcms_thumbs_shown div.lcms_wrap {
    max-height: calc(100vh - <?php echo (int)$mf_lb_feat_margin[0] + (int)$mf_lb_feat_margin[2] ?>vh - 90px  - 52px);
}


<?php
if(get_option('mg_mf_lb_layout', 'right_text') == 'right_text') : 
?>
.mg_mf_lb .mg_item_featured {
    margin-right: <?php echo $mg_mf_lb_txt_w ?>px;
}
.mg_mf_lb .mg_item_content {
    padding-left: <?php echo $mf_txt_h_padding ?>px !important;
    padding-right: <?php echo $mf_calc_txt_h_padd + $mf_lb_padding ?>px !important;
}
<?php else : ?>
.mg_mf_lb .mg_item_featured {
    margin-left: <?php echo $mg_mf_lb_txt_w ?>px;
}
.mg_mf_lb .mg_item_content {
    padding-right: <?php echo $mf_txt_h_padding ?>px !important;
    padding-left: <?php echo $mf_calc_txt_h_padd + $mf_lb_padding ?>px !important;
}
.mg_mf_lb .mg_item_content {  
    direction: rtl;
}
.mg_mf_lb .mg_item_content > * {
    direction: ltr;
}
<?php endif; ?>


@media screen and (max-width: 860px) {
    .mg_mf_lb #mg_lb_contents {
        margin: <?php echo $mf_lb_feat_mob_margin[0].'vh '. $mf_lb_feat_mob_margin[1].'vw '. $mf_lb_feat_mob_margin[2].'vh '. $mf_lb_feat_mob_margin[3].'vw' ?> !important;

        <?php if($mf_lb_feat_mob_margin == array(0, 0, 0, 0)) : ?>
        border: none !important;
        <?php endif; ?>

        <?php
        $top_padd = ($lb_cmd_pos == 'inside' || $lb_cmd_pos == 'ins_hidden') ? 52 : $mf_lb_padding;
        echo 'padding: '. $top_padd .'px '. $mf_lb_padding .'px '. $mf_lb_padding .'px;';
        ?>
    }
    #mg_lb_wrap.mg_mf_lb:after {
        content: "";
        display: block;
        height: <?php echo (int)$mf_lb_feat_mob_margin[2] / 2 ?>vh;
        width: 100vw;
    }
    .mg_mf_lb .mg_item_featured,
    .mg_mf_lb .mg_item_featured > *,
    .mg_mf_lb #mg_lb_feat_img_wrap > img,
    .mg_mf_lb .mg_lb_lcms_slider:not(.mg_lb_lcms_thumbs_shown) div.lcms_wrap,
    .mg_mf_lb .mg_lb_lcms_thumbs_shown div.lcms_wrap {
        max-height: calc(100vh - <?php echo (int)$mf_lb_feat_mob_margin[0] ?>vh - <?php echo $lb_border_w ?>px - 52px - 45px); /* leave title visible */
        max-width: calc(100vw - <?php echo (int)$mf_lb_feat_mob_margin[1] + (int)$mf_lb_feat_mob_margin[3]  ?>vw - <?php echo ($lb_border_w + $mf_lb_padding) * 2 ?>px);
    }
    .mg_mf_lb .mg_lb_lcms_thumbs_shown div.lcms_wrap {
        max-height: calc(100vh - <?php echo (int)$mf_lb_feat_mob_margin[0] ?>vh - <?php echo $lb_border_w ?>px - 52px - 45px - 90px); /* leave title visible */
        max-width: calc(100vw - <?php echo (int)$mf_lb_feat_mob_margin[1] + (int)$mf_lb_feat_mob_margin[3]  ?>vw - <?php echo ($lb_border_w + $mf_lb_padding) * 2 ?>px);
    }
    .mg_mf_lb .mg_item_featured > *:not(img):not(#mg_lb_feat_img_wrap):not(.mg_loader) {
        min-width: calc(100vw - <?php echo (int)$mf_lb_feat_mob_margin[1] + (int)$mf_lb_feat_mob_margin[3]  ?>vw - <?php echo ($lb_border_w + $mf_lb_padding) * 2 ?>px);
    }
    .mg_mf_lb .mg_item_content {
        padding-right: <?php echo $mf_calc_txt_h_padd ?>px !important;
        padding-left: <?php echo $mf_calc_txt_h_padd ?>px !important;
    }
}





/* inner commands */
#mg_lb_ins_cmd_wrap {

    <?php if(in_array($lb_cmd_pos, array('inside', 'ins_hidden')) && !get_option('mg_lb_inner_cmd_boxed')) : ?>
    left: 10px;
    right: 10px;

	<?php elseif($lb_cmd_pos != 'round_hidden') : ?>
    left: <?php echo ($lb_padding < 12) ? 12 : $lb_padding ?>px;
    right: <?php echo ($lb_padding < 12) ? 12 : $lb_padding ?>px;
    <?php endif; ?>
    
    <?php
	// height ok until 27px padding - then change
	if($lb_padding > 27) {
		$new_inner_cmd_height = 52 + ceil( ($lb_padding - 27) * 0.75);
		echo 'height: '. $new_inner_cmd_height .'px;';	
	}
	else {
		$new_inner_cmd_height = 52;	
	}
	?>
}
<?php if($lb_cmd_pos != 'inside' && $lb_cmd_pos != 'round_hidden') : ?>
@media screen and (max-width: 860px) {
	#mg_lb_contents {
		padding-top: <?php echo $new_inner_cmd_height ?>px;
	}
}
<?php endif; ?> 


.mg_mf_lb #mg_lb_ins_cmd_wrap {

	<?php if($lb_cmd_pos != 'round_hidden') : ?>
    left: <?php echo ($mf_lb_padding < 12) ? 12 : $mf_lb_padding ?>px;
    right: <?php echo ($mf_lb_padding < 12) ? 12 : $mf_lb_padding ?>px;
    <?php endif; ?>
    
    <?php
	// height ok until 27px padding - then change
	if($mf_lb_padding > 27) {
		$new_inner_cmd_height = 52 + ceil( ($mf_lb_padding - 27) * 0.75);
		echo 'height: '. $new_inner_cmd_height .'px;';	
	}
	else {
		$new_inner_cmd_height = 52;	
	}
	?>
}
<?php if(!in_array($lb_cmd_pos, array('inside', 'ins_hidden', 'round_hidden'))) : ?>
@media screen and (max-width: 860px) {
	.mg_mf_lb #mg_lb_contents {
		padding-top: <?php echo $new_inner_cmd_height ?>px;
	}
}
<?php endif; ?> 



<?php if($lb_cmd_pos == 'above') : ?>
/* Commands above lightbox */
@media screen and (min-width: 861px) {
    #mg_lb_ins_cmd_wrap {
        top: -55px;
        left: <?php echo $lb_border_w * -1 ?>px !important;
        right: <?php echo $lb_border_w * -1 ?>px !important;
    }
    .mg_lb_nav_inside div span {
        border-radius: 2px;
    } 
}
<?php endif; ?>



/* texts responsivity */
@media screen and (max-width: 860px) { 
    .mg_item_title {
        font-size: <?php echo get_option('mg_mobile_lb_title_font_size', 17) . get_option('mg_mobile_lb_title_font_size_type', 'px') ?>;
    }
    .mg_item_text {
        font-size: <?php echo get_option('mg_mobile_lb_txt_font_size', 14) . get_option('mg_mobile_lb_txt_font_size_type', 'px') ?>;
    }
} 



/* inner lb cmd boxed */
<?php if(
    (in_array($lb_cmd_pos, array('inside', 'ins_hidden')) && get_option('mg_lb_inner_cmd_boxed')) || 
    !in_array($lb_cmd_pos, array('inside', 'ins_hidden'))
) : 
?>
#mg_lb_inside_nav > * > i,
.mg_lb_nav_inside div span,
#mg_inside_close {
	background: <?php echo get_option('mg_item_cmd_bg', '#f1f1f1') ?>;
	border-radius: 2px;
}
.mg_inside_nav_next > i:before {
	margin-right: -3px;
}
.mg_lb_nav_inside div span {
	padding: 5px 9px 4px; 
}
<?php endif; ?>


/* inside cmd - hidden nav */
<?php if($lb_cmd_pos == 'ins_hidden') : ?>
#mg_lb_inside_nav {
    display: none;
}
<?php endif; ?>


/* lb rounded closing btn */
<?php if($lb_cmd_pos == 'round_hidden') : ?>
#mg_lb_ins_cmd_wrap {
	max-height: 0;
}
#mg_lb_inside_nav {
    display: none;
}
#mg_inside_close {
	border-radius: 50%;
	padding: 18px;
	top: -12px;
	right: -25px;
	background: <?php echo get_option('mg_item_bg_color', '#fff') ?>;
	box-shadow: -1px 1px 1px 2px rgba(0, 0, 0, 0.07);
	border: <?php echo $lb_border_w .'px solid '. get_option('mg_item_border_color', '#e2e2e2') ?>;
}
#mg_inside_close:before {
	top: -17px;
	height: 18px;
	display: inline-block;
	width: 18px;
	line-height: 17px;
	left: -9px;
}
<?php 
endif;


// lb contents padding - at least 22px
if($lb_padding != 22) :
  $diff = 22 - $lb_padding;
  $new_padd = ($diff < 0) ? 0 : $diff;
?>
.mg_layout_full .mg_item_content {
	padding: 14px <?php echo $new_padd ?>px <?php echo $new_padd ?>px;	
}
.mg_lb_layout:not(.mg_layout_full) .mg_item_content {
    padding: <?php echo $new_padd ?>px;
}
@media screen and (max-width: 860px) { 
    .mg_lb_layout:not(.mg_layout_full) .mg_item_content {
		padding: 14px <?php echo $new_padd ?>px <?php echo $new_padd ?>px !important;	
	}		
}
<?php endif;


// media focused texts - vertical padding
if($mf_lb_padding != 22) :
  $diff = 22 - $mf_lb_padding;
  $mf_new_padd = ($diff < 0) ? 0 : $diff;
?>
.mg_mf_lb .mg_layout_full .mg_item_content {
	padding: 14px <?php echo $mf_new_padd ?>px <?php echo $mf_new_padd ?>px;	
}
.mg_mf_lb .mg_lb_layout:not(.mg_layout_full) .mg_item_content {
    padding: <?php echo $mf_new_padd ?>px;
}
@media screen and (max-width: 860px) { 
    .mg_mf_lb .mg_lb_layout:not(.mg_layout_full) .mg_item_content {
		padding: 14px <?php echo $mf_new_padd ?>px <?php echo $mf_new_padd ?>px !important;	
	}		
}
<?php endif; ?>



/* side text - desktop mode - inside cmd - top padding */
<?php if($lb_cmd_pos == 'inside' || $lb_cmd_pos == 'ins_hidden') : ?>
@media screen and (min-width: 860px) { 
    .mg_lb_layout:not(.mg_layout_full) .mg_item_content {
        padding-top: <?php echo ($lb_cmd_pos == 'inside') ? 3 : 0; ?>px !important;	
    }
}
<?php endif; ?>


/* colors - shadow */
#mg_lb_wrap #mg_lb_contents,
#mg_lb_loader,
.mg_lb_zoom_in_btn, 
.mg_lb_zoom_out_btn {
    <?php 
	echo 'color: '.get_option('mg_item_txt_color', '#333').';';
	echo 'background-color: '.get_option('mg_item_bg_color', '#fff').';';

	$lb_shadow = get_option('mg_lb_shadow', 'soft');
    if($lb_shadow == 'soft') {
		echo 'box-shadow: 0 2px 5px rgba(15, 15, 15, 0.25);';  
    } 
    elseif($lb_shadow == 'heavy') {
		echo 'box-shadow: 0 3px 8px rgba(15, 15, 15, 0.55);';     
    }
	?>
}
#mg_lb_loader {
	<?php if($lb_shadow != 'none') : ?>
	box-shadow: 0px 2px 5px rgba(10, 10, 10, 0.5);	
    <?php endif; ?>
}
.mg_cust_options,
#mg_lb_comments_wrap {
	border-color: <?php echo get_option('mg_item_hr_color', '#ccc') ?>;
}


/* icons and loader */
.mg_close_lb:before, .mg_nav_prev > i:before, .mg_nav_next > i:before,
#mg_socials span:before,
#mg_woo_item_added i:before {
	color: <?php echo get_option('mg_item_icons_color', '#333333') ?>;
}
#mg_lb_contents .mg-twitter-icon:before {
    background-color: <?php echo get_option('mg_item_icons_color', '#333333') ?>;
}
.mg_round_social_trick {
    box-shadow: 0 0 0 3px <?php echo get_option('mg_item_icons_color', '#333333') ?> inset;
}
 

/* navigation elements background color and border radius */
.mg_lb_nav_side *,
.mg_lb_nav_side_basic,
.mg_lb_nav_top > i, .mg_lb_nav_top > div, .mg_lb_nav_top > div *,
#mg_top_close {
	background-color: <?php echo get_option('mg_item_bg_color', '#fff') ?>; 
}

<?php
/* lightbox navigation - commands position */
$cmd_pos = get_option('mg_lb_cmd_pos', 'inside'); 

// commands NOT inside
if($cmd_pos != 'inside' || $cmd_pos != 'above') : 
	$radius = ($lb_radius > 15) ? 15 : $lb_radius;
    $border_w = ($lb_border_w > 5) ? 5 : $lb_border_w;

	$border_col = get_option('mg_item_border_color', '#e5e5e5');
	$txt_col = get_option('mg_item_txt_color', '#333');
?>
/* top closing button */
#mg_top_close {
	border-style: solid;
    border-color: <?php echo $border_col ?>;
	border-width: 0 0 <?php echo $border_w ?>px <?php echo $border_w ?>px;
    border-radius: 0 0 0 <?php echo $radius ?>px;
}
<?php endif; 

// top position (and media-focused mode)
?>
/* top nav - custom radius and borders */
#mg_lb_top_nav > * > div {
	margin-left: <?php echo $border_w ?>px;
}
#mg_lb_top_nav .mg_nav_prev i {
	border-width: 0 0 <?php echo $border_w ?>px 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
#mg_lb_top_nav .mg_nav_next i,
#mg_lb_top_nav > * > div img {
	border-width: 0 <?php echo $border_w ?>px <?php echo $border_w ?>px 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
    border-radius: 0 0 <?php echo $radius ?>px 0;
}
#mg_lb_top_nav > * > div {
	border-width: 0 <?php echo $border_w ?>px <?php echo $border_w ?>px <?php echo $border_w ?>px;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
    color: <?php echo $txt_col ?>;
}
<?php if($lb_shadow != 'none') : ?>
#mg_lb_top_nav > div:first-child {
    box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
}
#mg_lb_top_nav > div:last-child {
    box-shadow: 3px 2px 3px rgba(10, 10, 10, 0.2);	
}
#mg_lb_top_nav > div:hover > div, #mg_top_close {
    box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
}
#mg_lb_top_nav > div:hover img {
    box-shadow: 2px 2px 2px rgba(10, 10, 10, 0.2);	
}
<?php endif; ?>




<?php if($cmd_pos == 'side') : ?>
/* top nav - custom radius and borders */
.mg_side_nav_prev span {
	border-radius: 0 <?php echo $radius ?>px <?php echo $radius ?>px 0;
	border-width: <?php echo $border_w ?>px <?php echo $border_w ?>px <?php echo $border_w ?>px 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_next span {
	border-radius: <?php echo $radius ?>px 0 0 <?php echo $radius ?>px;
	border-width: <?php echo $border_w ?>px 0 <?php echo $border_w ?>px <?php echo $border_w ?>px;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_prev > img {
	border-radius: 0 <?php echo $radius ?>px 0 0;
	border-width: <?php echo $border_w ?>px <?php echo $border_w ?>px 0 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_next > img {
	border-radius: <?php echo $radius ?>px 0 0 0;
	border-width: <?php echo $border_w ?>px 0 0 <?php echo $border_w ?>px;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_next div {
	border-radius: 0 0 0 <?php echo $radius ?>px;
    color: <?php echo $txt_col ?>;
}
.mg_side_nav_prev div {
	border-radius: 0 0 <?php echo $radius ?>px 0;
    color: <?php echo $txt_col ?>;
}
	<?php if(!empty($border_w)): ?>
    .mg_side_nav {
        height: <?php echo 68 + ($border_w * 2); ?>px;
    }
	.mg_side_nav > span {
    	width: <?php echo 42 + $border_w; ?>px;
    }
    .mg_side_nav > div {
        width: <?php echo 338 - $border_w; ?>px;
    }
    <?php endif; ?>
    
    <?php if($lb_shadow != 'none') : ?>
    .mg_side_nav span, #mg_top_close {
        box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
    }
    .mg_side_nav img {
        box-shadow: 0px -1px 1px rgba(10, 10, 10, 0.2);	
    }
    
    @media screen and (min-width:760px) {
    	#mg_lb_wrap {
        	padding-left: 55px;
            padding-right: 55px;
        }
    }
    <?php endif; ?>
    
    
<?php elseif($cmd_pos == 'side_basic') : ?>
/* top nav - custom radius and borders */
.mg_lb_nav_side_basic {
	border-radius: <?php echo $radius ?>px;
	border: <?php echo $border_w ?>px solid <?php echo $border_col ?>;
}
#mg_top_close {
	border-width: <?php echo $border_w ?>px;
    margin-top: 15px;
    margin-right: 15px;
    box-sizing: border-box;
}
	<?php if(!empty($border_w)): ?>
    .mg_lb_nav_side_basic {
        height: <?php echo 68 + ($border_w * 2); ?>px;
    	width: <?php echo 44 + $border_w; ?>px;
    }
    <?php endif; ?>
    
    <?php if($lb_shadow != 'none') : ?>
    .mg_lb_nav_side_basic, #mg_top_close {
        box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
    }
    
    @media screen and (min-width:760px) {
    	#mg_lb_wrap {
        	padding-left: 55px;
            padding-right: 55px;
            left: -60px;
        }
    }
    <?php endif; ?>
<?php endif; ?>



<?php 
/* LIGHTBOX BG EFFECT */
$lb_bg_fx = get_option('mg_lb_bg_fx', 'genie_b_side');
if(!empty($lb_bg_fx)) :
?>
#mg_lb_background.mg_lb_shown {
    animation: mg_lb_bg_showup <?php echo ((int)get_option('mg_lb_bg_fx_time', 500) / 1000) ?>s forwards <?php echo mg_static::easing_to_css(get_option('mg_lb_bg_fx_easing', 'ease')) ?>; 
}
<?php
switch($lb_bg_fx) {
	case 'zoom-in' :
		$start 	= 'scale(0.6)';
		$end 	= 'scale(1)';
		break;	
		
	case 'zoom-out' :
		$start 	= 'scale(1.4)';
		$end 	= 'scale(1)';
		break;	
	
	case 'zoom-flip' :
		$start 	= 'rotateX(-135deg) scale(0.1); transform-style: preserve-3d; transform-origin: 50% 50%';
		$end 	= 'rotateX(180deg) scale(1); transform-style: preserve-3d; transform-origin: 50% 50%';
		break;	
	
	case 'skew' :
		$start 	= 'skewX(55deg)';
		$end 	= 'skewX(0deg)';
		break;	
		
	case 'symm_vert' :
		$start 	= 'rotateY(90deg)';
		$end 	= 'rotateY(0deg)';
		break;	
		
	case 'symm_horiz' :
		$start 	= 'rotateX(90deg)';
		$end 	= 'rotateX(0deg)';
		break;		
	
	case 'genie_t_side' :
		$start 	= 'translateY(-60%) scale(0)';
		$end 	= 'translateY(0) scale(1)';
		break;	
	
	case 'genie_r_side' :
		$start 	= 'translateX(60%) scale(0)';
		$end 	= 'translateX(0) scale(1)';
		break;	
	
	case 'genie_b_side' :
	default:
		$start 	= 'translateY(60%) scale(0)';
		$end 	= 'translateY(0) scale(1)';
		break;	
		
	case 'genie_l_side' :
		$start 	= 'translateX(-60%) scale(0)';
		$end 	= 'translateX(0) scale(1)';
		break;	
			
	case 'slide_corn_tr' :
		$start 	= 'translate(75%, -75%)';
		$end 	= 'translate(0, 0)';
		break;
		
	case 'slide_corn_br' :
		$start 	= 'translate(75%, 75%)';
		$end 	= 'translate(0, 0)';
		break;
		
	case 'slide_corn_bl' :
		$start 	= 'translate(-75%, 75%)';
		$end 	= 'translate(0, 0)';
		break;			
	
	case 'slide_corn_tl' :
		$start 	= 'translate(-75%, -75%)';
		$end 	= 'translate(0, 0)';
		break;					
		
	
	case 'slide_t_side' :
		$start 	= 'translate(0, -85%)';
		$end 	= 'translate(0, 0)';
		break;		
		
	case 'slide_r_side' :
		$start 	= 'translate(85%, 0)';
		$end 	= 'translate(0, 0)';
		break;		
		
	case 'slide_b_side' :
		$start 	= 'translate(0, 85%)';
		$end 	= 'translate(0, 0)';
		break;		
		
	case 'slide_l_side' :
		$start 	= 'translate(-85%, 0)';
		$end 	= 'translate(0, 0)';
		break;											
}
?>
@keyframes mg_lb_bg_showup {
    0%		{transform: <?php echo $start ?>;}
    100% 	{transform: <?php echo $end ?>;}
}
<?php endif; ?>



<?php 
/* LIGHTBOX ENTRANCE EFFECT */
switch(get_option('mg_lb_entrance_fx', 'static_fade')) : 
	case 'static_fade' :
?>
    #mg_lb_contents.mg_lb_pre_show_prev,
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_switching_prev,
    #mg_lb_contents.mg_lb_switching_next,
    #mg_lb_contents.mg_closing_lb {
        transform: scale(0.95) translate3d(0,8px,0);
        transition: opacity .25s ease-in, transform .5s ease; 
    }
    #mg_lb_contents.mg_lb_shown {
        transition: opacity .25s ease-in, transform .5s ease; 
    }
<?php 
	break;
	case 'slide_bounce' :
?>
	#mg_lb_contents {
        opacity: 1;	
        transition: all .5s cubic-bezier(0.260, 0, 0.300, 1.200); 
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_pre_show_prev {
        transition-duration: 0s !important; 	
    }
    #mg_lb_contents.mg_lb_pre_show_prev,
    #mg_lb_contents.mg_lb_switching_next,
    #mg_lb_contents.mg_closing_lb {
        transform: translateX(-80vw);
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_switching_prev {
        transform: translateX(80vw);
    }
    #mg_lb_contents.mg_lb_shown {
        transform: translateX(0);
    }
<?php 
	break;
	case 'slide_fade' :
?>	
	#mg_lb_contents {
        transition: all .45s ease-out; 
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_pre_show_prev {
        transition-duration: 0s !important; 	
    }
    #mg_lb_contents.mg_lb_pre_show_prev,
    #mg_lb_contents.mg_lb_switching_next,
    #mg_lb_contents.mg_closing_lb {
        transform: translateX(-20%) rotateY(10deg) scale(0.9);
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_switching_prev {
        transform: translateX(20%) rotateY(10deg) scale(0.9);
    }
    #mg_lb_contents.mg_lb_shown {
        transform: translateX(0) rotateY(0deg) scale(1);
    }
<?php
	break;    
endswitch; 
?>



<?php /* lb image zoom */ ?>
.mg_item_featured .easyzoom-notice,
.mg_item_featured .easyzoom-flyout {
	background: <?php echo get_option('mg_item_bg_color', '#fff') ?>;
    color: <?php echo get_option('mg_item_txt_color', '#333') ?>; 
}



/* lightbox slider */
.mg_lb_lcms_slider {
    padding-bottom: <?php echo (int)get_option('mg_slider_main_w', 60) . get_option('mg_slider_main_w_type', '%') ?>;
}
<?php 
if(get_option('mg_lb_slider_extra_nav', 'thumbs') == 'thumbs'): 
    $thumb_sizes  = get_option('mg_lb_slider_thumbs_size', array(65, 45));    
?>
.mg_lb_lcms_slider .lcms_nav_dots span {
    width: <?php echo (int)$thumb_sizes[0] ?>px;
    height: <?php echo (int)$thumb_sizes[1] ?>px;
    border-radius: <?php echo (int)get_option('lb_slider_thumbs_radius', 5) ?>%;
}
.mg_lb_lcms_slider.mg_lb_lcms_thumbs_shown .lcms_wrap {
	max-height: calc(100% - <?php echo (int)$thumb_sizes[1] + 25 ?>px);
	margin-bottom: <?php echo (int)$thumb_sizes[1] + 25 ?>px;
}
.mg_lb_lcms_slider.mg_lb_lcms_thumbs_shown .lcms_nav_dots {
    bottom: -<?php echo (int)$thumb_sizes[1] + 15 ?>px;
}
<?php endif; ?>



<?php /* lb woocommerce add-to-cart system */ ?>
.mg_wc_atc_btn {
	background: <?php echo get_option('mg_wc_atc_btn_bg', '#ccc') ?>;
    color: <?php echo get_option('mg_wc_atc_btn_color', '#3a3a3a') ?>; 
}
.mg_wc_atc_btn:hover,
.mg_wc_atc_btn.mg_wc_atc_btn_disabled,
.mg_wc_atc_btn.mg_wc_atc_btn_acting {
	background: <?php echo get_option('mg_wc_atc_btn_bg_h', '#e3e3e3') ?>;
	color: <?php echo get_option('mg_wc_atc_btn_color_h', '#555') ?>; 
}
#mg_woo_cart_btn_wrap [name=mg_wc_atc_variations_dd],
#mg_woo_cart_btn_wrap [name=mg_wc_atc_quantity] {
	border-color: <?php echo get_option('mg_item_hr_color', '#ccc') ?>;
}


<?php
// MG-ACTION - allow CSS to be inserted before mg_custom_css
do_action('mg_custom_css');


// custom CSS
echo get_option('mg_custom_css');
