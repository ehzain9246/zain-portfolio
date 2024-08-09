<?php
// overwrite the page content to display the gallery

function mg_manage_preview($the_content) {
	$target_page = (int)get_option('mg_preview_pag');
	$curr_page_id = (int)get_the_ID();
	
	if($target_page == $curr_page_id && is_user_logged_in() && isset($_REQUEST['mg_preview'])) {
				
		$content = do_shortcode('[mediagrid cat="'.(int)$_REQUEST['mg_preview'].'" filter="0" search="0" title_under="0"]');
		return $content;
	}	
	
	else {return $the_content;}
}
add_filter('the_content', 'mg_manage_preview', 999999);
