<?php
/// debug ///
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
///////////////////////////////


// OPTION TO SET CUSTOM ITEMS BASE-URL

require_once('classes/mg_static.php');
ob_start();

// load WP functions
$curr_path = dirname(__FILE__);
$curr_path_arr = explode(DIRECTORY_SEPARATOR, $curr_path);

$true_path_arr = array();
foreach($curr_path_arr as $part) {
	if($part == 'wp-content') {break;}
	$true_path_arr[] = $part;
}	
$true_path = implode('/', $true_path_arr);


// main functions
if(!file_exists($true_path .'/wp-load.php')) {die('<p>wordpress - wp-load.php file not found</p>');}
else {require_once($true_path .'/wp-load.php');}

if(!function_exists('get_filesystem_method')) {
	// wp-admin/includes/file.php - for wp_filesys
	if(!file_exists(ABSPATH . 'wp-admin/includes/file.php')) {die('<p>wordpress - file.php file not found</p>');}
	else {require_once(ABSPATH . 'wp-admin/includes/file.php');}	
}

/////////////////////////////////////////////////////////////////////////////////////


ob_end_clean();
header("Content-type: text/xml");

echo 
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
    
    // query items having lightbox
    $args = array(
        'post_type' => 'mg_items', 
        'post_status' => 'publish', 
        'posts_per_page' => -1, 
        'meta_query' => array(
			array(
			 'key' => 'mg_main_type',
			 'value' => array('single_img', 'img_gallery', 'video', 'audio', 'post_contents', 'lb_text'),
			 'compare' => 'IN'
		   )
		),
    );
    $query = new WP_Query($args);	
	
    if(is_array($query->posts)) {
        foreach($query->posts as $item) {
			
			// get featured image URL
			$img_url = wp_get_attachment_url(get_post_thumbnail_id($item->ID));
			
			echo '
            <url> 
              <loc>'. mg_static::item_deeplinked_url($item->ID, $item->post_title) .'</loc> 
              <lastmod>'. substr($item->post_modified_gmt, 0, 10) .'</lastmod>
			  <image:image>
                 <image:loc>'. $img_url .'</image:loc>
                 <image:caption>'. get_the_title($item->ID) .'</image:caption>
              </image:image>
            </url>';	
        }
    }
  ?>

</urlset>