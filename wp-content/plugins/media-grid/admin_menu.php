<?php
// declaring menu, custom post type and taxonomy

///////////////////////////////////
// SETTINGS PAGE

function mg_settings_page() {	
	add_submenu_page('edit.php?post_type=mg_items', __('Grid Builder', 'mg_ml'), __('Grid Builder', 'mg_ml'), 'upload_files', 'mg_builder', 'mg_builder');	
	add_submenu_page('edit.php?post_type=mg_items', __('Settings', 'mg_ml'), __('Settings', 'mg_ml'), 'manage_options', 'mg_settings', 'mg_settings');	
	
	// add-ons sponsor!
	if(!isset($GLOBALS['is_mg_bundle'])) {
		$remaining = mg_static::addons_not_installed();
		if(!empty($remaining)) {
			
			$txt = '<strong class="mg_getaddons_menu"><span class="dashicons dashicons-star-filled"></span> '. __('Get Add-ons!', 'mg_ml') .'</strong>';
			add_submenu_page('edit.php?post_type=mg_items',$txt , $txt, 'upload_files', 'upload_files', 'mg_addons_adv');	
		}
	}
	
}
add_action('admin_menu', 'mg_settings_page');


function mg_builder() {
	include_once(MG_DIR .'/grid_builder.php');	
}
function mg_settings() {
	include_once(MG_DIR. '/settings/view.php');
}
function mg_addons_adv() {
	include_once(MG_DIR.'/addons_adv.php');
} 





//////////////////////
// GRID TAXONOMY

add_action( 'init', 'register_taxonomy_mg_grids', 1);
function register_taxonomy_mg_grids() {
    $labels = array( 
        'name' => __( 'Grids', 'mg_ml'),
        'singular_name' => __( 'Grid', 'mg_ml'),
        'search_items' => __( 'Search Grids', 'mg_ml'),
        'popular_items' => __( 'Popular Grids', 'mg_ml'),
        'all_items' => __( 'All Grids', 'mg_ml'),
        'parent_item' => __( 'Parent Grid', 'mg_ml'),
        'parent_item_colon' => __( 'Parent Grid:', 'mg_ml'),
        'edit_item' => __( 'Edit Grid', 'mg_ml'),
        'update_item' => __( 'Update Grid', 'mg_ml'),
        'add_new_item' => __( 'Add New Grid', 'mg_ml'),
        'new_item_name' => __( 'New Grid', 'mg_ml'),
        'separate_items_with_commas' => __( 'Separate grids with commas', 'mg_ml'),
        'add_or_remove_items' => __( 'Add or remove Grids', 'mg_ml'),
        'choose_from_most_used' => __( 'Choose from most used Grids', 'mg_ml'),
        'menu_name' => __( 'Grids', 'mg_ml'),
    );

    $args = array( 
        'labels' => $labels,
        'public' => false,
        'show_in_nav_menus' => false,
        'show_ui' => false,
        'show_tagcloud' => false,
        'hierarchical' => false,
        'rewrite' => false,
        'query_var' => true
    );

    register_taxonomy( 'mg_grids', null, $args );
}





////////////////////////////////
// ITEM CUSTOM POST TYPE 

add_action( 'init', 'register_cpt_mg_item' );
function register_cpt_mg_item() {

    $labels = array( 
        'name' => __( 'Items', 'mg_ml'),
        'singular_name' => __( 'Item', 'mg_ml'),
        'add_new' => __( 'Add New Item', 'mg_ml'),
        'add_new_item' => __( 'Add New Item', 'mg_ml'),
        'edit_item' => __( 'Edit Item', 'mg_ml'),
        'new_item' => __( 'New Item', 'mg_ml'),
        'view_item' => __( 'View Item', 'mg_ml'),
        'search_items' => __( 'Search Items', 'mg_ml'),
        'not_found' => __( 'No items found', 'mg_ml'),
        'not_found_in_trash' => __( 'No items found in Trash', 'mg_ml'),
        'parent_item_colon' => __( 'Parent Item:', 'mg_ml'),
        'menu_name' => __( 'Media Grid', 'mg_ml'),
    );

    $svg_logo = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNiAxNiI+PHBhdGggZD0iTTYuNjksOEgxLjQ0QS40Mi40MiwwLDAsMCwxLDguMzhIMXY2LjE4YS40Mi40MiwwLDAsMCwuMzguNDRINi42OWEuNDEuNDEsMCwwLDAsLjQzLS4zOGgwVjguNDRBLjQxLjQxLDAsMCwwLDYuNzQsOGgwWiIgc3R5bGU9ImZpbGw6IzljYTJhNyIvPjxwYXRoIGQ9Ik02LjY5LDFIMS40NEEuNDIuNDIsMCwwLDAsMSwxLjM4SDFWNS45YS40Mi40MiwwLDAsMCwuMzguNDRINi42OUEuNDEuNDEsMCwwLDAsNy4xMiw2aDBWMS40NEEuNDEuNDEsMCwwLDAsNi43NCwxaDBaIiBzdHlsZT0iZmlsbDojOWNhMmE3Ii8+PHBhdGggZD0iTTE0LjU2LDFIOS4zMWEuNDMuNDMsMCwwLDAtLjQ0LjM4aDBWNy41NkEuNDIuNDIsMCwwLDAsOS4yNiw4aDUuM0EuNDIuNDIsMCwwLDAsMTUsNy42MmgwVjEuNDRBLjQyLjQyLDAsMCwwLDE0LjYyLDFoLS4wNloiIHN0eWxlPSJmaWxsOiM5Y2EyYTciLz48cGF0aCBkPSJNMTQuNTYsOS41N0g5LjMxYS40OS40OSwwLDAsMC0uNDQuNDR2NC40NmEuNS41LDAsMCwwLC40NC41M2g1LjI1YS40Mi40MiwwLDAsMCwuNDQtLjM4aDBWMTAuMUEuNS41LDAsMCwwLDE0LjU2LDkuNTdaIiBzdHlsZT0iZmlsbDojOWNhMmE3Ii8+PC9zdmc+';
    
    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,      
        'taxonomies' => (defined('MGAF_DIR')) ? array() : array('mg_item_categories'),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
		'menu_icon' => $svg_logo,
        'menu_position' => 52,
        'show_in_nav_menus' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => false,
		'supports' => array('title', 'editor', 'thumbnail'),
        'capability_type' => 'post'
    );
	
	if(defined('MGOM_DIR')) {
        $args['supports'][] = 'excerpt';
    } // OVERLAYS ADD-ON add excerpt
    
    register_post_type('mg_items', $args );	

	//////
	
	$labels = array( 
        'name' => __( 'Item Categories', 'mg_ml'),
        'singular_name' => __( 'Item Category', 'mg_ml'),
        'search_items' => __( 'Search Item Categories', 'mg_ml'),
        'popular_items' => NULL,
        'all_items' => __( 'All Item Categories', 'mg_ml'),
        'parent_item' => __( 'Parent Item Category', 'mg_ml'),
        'parent_item_colon' => __( 'Parent Item Category:', 'mg_ml'),
        'edit_item' => __( 'Edit Item Category', 'mg_ml'),
        'update_item' => __( 'Update Item Category', 'mg_ml'),
        'add_new_item' => __( 'Add New Item Category', 'mg_ml'),
        'new_item_name' => __( 'New Item Category', 'mg_ml'),
        'separate_items_with_commas' => __( 'Separate item categories with commas', 'mg_ml'),
        'add_or_remove_items' => __( 'Add or remove Item Categories', 'mg_ml'),
        'choose_from_most_used' => __( 'Choose from most used Item Categories', 'mg_ml'),
        'menu_name' => __( 'Item Categories', 'mg_ml'),
    );

    $args = array( 
        'labels' 			=> $labels,
        'public' 			=> false,
        'show_in_nav_menus' => false,
        'show_ui' 			=> true,
        'show_tagcloud'		=> false,
        'hierarchical' 		=> true,
        'rewrite' 			=> false,
        'query_var' 		=> true,
		'update_count_callback' => '_update_generic_term_count'
    );
	
	$assoc_with = (defined('MGAF_DIR')) ? array() : array('mg_items');
    register_taxonomy('mg_item_categories', $assoc_with, $args);
}





//////////////////////////////
// VIEW CUSTOMIZATORS

function mg_updated_messages( $messages ) {
  global $post;

  $messages['mg_items'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => __('Item updated', 'mg_ml'),
    2 => __('Item updated', 'mg_ml'),
    3 => __('Item deleted', 'mg_ml'),
    4 => __('Item updated', 'mg_ml'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Item restored to revision from %s', 'mg_ml'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => __('Item published', 'mg_ml'),
    7 => __('Item saved', 'mg_ml'),
    8 => __('Item submitted', 'mg_ml'),
    9 => sprintf( __('Item scheduled for: <strong>%1$s</strong>', 'mg_ml'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ))),
    10 => __('Item draft updated', 'mg_ml'),
  );

  return $messages;
}
add_filter('post_updated_messages', 'mg_updated_messages');




// alterate item link to preview it in lightbox
function mg_change_item_link($permalink, $post) {
    if($post->post_type == 'mg_items') {
        $permalink = home_url('?mgi_='.$post->ID);
    }
    return $permalink;
}
add_filter('post_type_link', 'mg_change_item_link', 10, 2);




// customize the grid items custom post type table
add_filter('manage_edit-mg_items_columns', 'mg_edit_pt_table_head', 10, 2);
function mg_edit_pt_table_head($columns) {
	$new_cols = array();
	
	$new_cols['cb'] = '<input type="checkbox" />';
	$new_cols['title'] = __('Title', 'column name');
	
	$new_cols['mg_cat'] = __('Categories', 'mg_ml');
	$new_cols['mg_type'] = __('Type', 'mg_ml');
	$new_cols['mg_layout'] = __('Lightbox Layout', 'mg_ml');
	$new_cols['date'] = __('Date', 'column name');
	$new_cols['mg_thumb'] = __('Main Image', 'mg_ml');
	
	return array_merge($new_cols, $columns);
}


add_action('manage_mg_items_posts_custom_column', 'mg_edit_pt_table_body', 10, 2);
function mg_edit_pt_table_body($column_name, $id) {
	include_once(MG_DIR . '/classes/items_meta_fields.php');
	
	$item_type = get_post_meta($id, 'mg_main_type', true);
	
	switch ($column_name) {
		case 'mg_cat' :
			$cats = get_the_terms($id, 'mg_item_categories');
            if (is_array($cats)) {
				$item_cats = array();
				foreach($cats as $cat) { $item_cats[] = $cat->name;}
				echo implode(', ', $item_cats);
			}
			else {
                echo '';
            }
			break;

		case 'mg_type' :
			if($item_type) { echo mg_static::item_types($item_type); }
			else {echo '';}
			break;
			
		case 'mg_layout' :
            dike_lc('lcweb', MG_DIKE_SLUG, true);
			$imf = new mg_meta_fields($id, $item_type);
			
			if(in_array('mg_layout', $imf->type_fields() )) {
				
				// lightbox layout - replace SIDE with side_tripartite
				$val = get_post_meta($id, 'mg_layout', true);
				if($val == 'side') {$val = 'side_tripartite';}
				
				echo mg_static::lb_layouts($val);	
			} else {
				echo '';	
			}
			break;	
		
		case 'mg_thumb' :
			echo get_the_post_thumbnail($id, array(110, 110));
			break;
	
		default:
			break;
	}
	return true;
}


//////////////////////////////////////
// ENABLE CPT FILTER BY TAXONOMY

add_action('restrict_manage_posts','mg_items_filter_by_cat');
function mg_items_filter_by_cat() {
    global $typenow, $wp_query;
	
    if(!$typenow || $typenow != 'mg_items') {
        return false;
    }
        
    $tax_name = 'mg_item_categories';
    $sel = (isset($wp_query->query[$tax_name])) ? $wp_query->query[$tax_name] : ''; 

    wp_dropdown_categories(array(
        'show_option_all' =>  __("Any category", 'mg_ml'),
        'taxonomy'        =>  $tax_name,
        'name'            =>  $tax_name,
        'orderby'         =>  'name',
        'selected'        =>  $sel,
        'hierarchical'    =>  false,
        'depth'           =>  1,
        'show_count'      =>  false,
        'hide_empty'      =>  true
    ));
}

add_filter('parse_query', 'mg_cat_id_to_cat_term', 999);
function mg_cat_id_to_cat_term($query) {
	global $pagenow;
	
	$post_type = 'mg_items';
	$taxonomy  = 'mg_item_categories';
	
	$q_vars    = &$query->query_vars;
	if($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy]) {
		
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}



///////////////////////////////////////////////////////
// FIX FOR THEMES THAT DON'T SUPPOR FEATURED IMAGE

function mg_add_thumb_support() {
    $supportedTypes = (function_exists('get_theme_support')) ?  get_theme_support( 'post-thumbnails' ) : false;

	if($supportedTypes === false) {
		 add_theme_support( 'post-thumbnails', array( 'mg_items' ) ); 	
	}
    elseif( is_array( $supportedTypes ) ) {
        $supportedTypes[0][] = 'mg_items';
        add_theme_support( 'post-thumbnails', $supportedTypes[0] );
    }
}
add_action('admin_init', 'mg_add_thumb_support', 999);
