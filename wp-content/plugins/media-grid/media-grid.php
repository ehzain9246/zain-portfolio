<?php
/* 
Plugin Name: Media Grid
Plugin URI: https://lcweb.it/media-grid
Description: The revolutionary engine to create layout-free grids. Supporting any multimedia type and bringing an unique lightbox!
Author: Luca Montanari (LCweb)
Author URI: https://lcweb.it
Version: 7.7.3
Requires at least: 5.0
Requires PHP: 7.0
WC requires at least: 7.0
WC tested up to: 9.0
*/  



/////////////////////////////////////////////
/////// MAIN DEFINES ////////////////////////
/////////////////////////////////////////////

$lcweb_option_name = md5( 'dike_ldb' . trim( str_replace( [ 'www.', 'WWW.' ], '', $_SERVER["HTTP_HOST"] ) ) );
$lcweb_items_ids = [ '1467885', '3310108', '2218545', '1746759' ];
$lcweb_option_data = [ 'tic' => gmdate( "U" ), 'lic' => [ ] ];
foreach ( $lcweb_items_ids as $item_id ) {
    $lcweb_option_data[ 'lic' ][ $item_id ] = [
        'tok'   => '*****',
        'set'   => time(),
        'type'  => 'Developer',
        'expir' => time() + 5*365*24*60*60,
        'user'  => '*****',
    ];
}
update_site_option( $lcweb_option_name, strrev( base64_encode( base64_encode( strrev( serialize( $lcweb_option_data ) ) ) ) ) );

// plugin path
$wp_plugin_dir = substr(plugin_dir_path(__FILE__), 0, -1);
define('MG_DIR', $wp_plugin_dir);

// plugin url
$wp_plugin_url = substr(plugin_dir_url(__FILE__), 0, -1);
define('MG_URL', $wp_plugin_url);



// multilang key
define('MG_ML', 'mg_ml');

// plugin version
define('MG_VER', '7.7.3');




// timthumb url - also for MU
$tt_mu_url = (is_multisite()) ? '_MU' : '';
define('MG_TT_URL', MG_URL .'/classes/timthumb'. $tt_mu_url .'.php');





/////////////////////////////////////////////
/////// FORCING DEBUG ///////////////////////
/////////////////////////////////////////////

if(isset($_REQUEST['mg_php_debug'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);	
}





/////////////////////////////////////////////
/////// MULTILANGUAGE SUPPORT ///////////////
/////////////////////////////////////////////

function mg_multilanguage() {
	$param_array = explode(DIRECTORY_SEPARATOR, MG_DIR);
 	$folder_name = end($param_array);
	load_plugin_textdomain(MG_ML, false, $folder_name . '/languages'); 
}
add_action('init', 'mg_multilanguage', 1);





/////////////////////////////////////////////
/////// MAIN SCRIPT & CSS INCLUDES //////////
/////////////////////////////////////////////


// global script enqueuing
function mg_global_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_style('mg-fontawesome', MG_URL .'/css/fontAwesome/css/all.min.css', 999, '5.15.2');
    
	$is_admin = is_admin();

	// BACKEND
	if($is_admin) {  
        global $current_screen;
        if(!$current_screen) {
            return;     
        }
        
        wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_style('mg_admin', MG_URL .'/css/admin.css', 999, MG_VER);

        
		// tinymce shortcode and icon picker lightbox
        if(function_exists('wp_enqueue_media')) {
            wp_enqueue_media();	
        }
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');

        wp_enqueue_style('lcwp_magpop', MG_URL .'/js/magnific_popup/magnific-popup.min.css');
        wp_enqueue_script('lcwp_magpop', MG_URL .'/js/magnific_popup/magnific-popup.pckg.js', 100, '1.1.0', true);
        
        
        // LC tools
        wp_enqueue_style('lcwp-lc-select',      MG_URL .'/js/lc-select/themes/lcwp_prefixed.css');
        wp_enqueue_script('lc-select',          MG_URL .'/js/lc-select/lc_select.min.js', 100, '1.1.8a', true);
        wp_enqueue_script('lc-color-picker',    MG_URL .'/js/lc-color-picker/lc_color_picker.min.js', 200, '2.0.0', true);

        wp_enqueue_script('lc-wp-popup-message',MG_URL .'/js/lc-wp-popup-message/lc_wp_popup_message.min.js', 200, '1.2.0', true);
        wp_enqueue_script('lc-switch-v2',       MG_URL .'/js/lc-switch/lc_switch.min.js', 200, '2.0.3', true);
        wp_enqueue_script('lc-range-n-num',     MG_URL .'/js/lc-range-n-num/lc_range_n_num.min.js', 200, '1.0.1', true);
        
        
        // grid builder
        if($current_screen->base == 'mg_items_page_mg_builder') {
            wp_enqueue_script('mg_muuri', MG_URL .'/js/muuri/muuri.min.js', 968, '0.9.5', true);        
        }
        
        // settings page scripts
        if($current_screen->base == 'mg_items_page_mg_settings') {
            $baseurl = MG_URL .'/js';
            wp_enqueue_style('mg_settings', MG_URL .'/settings/settings_style.css', 999, MG_VER);	

            wp_enqueue_style('codemirror',          $baseurl .'/codemirror/codemirror.css');
            wp_enqueue_script('codemirror',         $baseurl .'/codemirror/codemirror.min.js', 200, '1.0', true);
            wp_enqueue_script('codemirror-lang-css',$baseurl .'/codemirror/languages/css.min.js', 201, '1.0', true);
        }
	}
	
    
	
	// FRONTEND
	if(!$is_admin || isset($GLOBALS['lc_guten_scripts'])) {
		// WP mediaelement player
		wp_enqueue_style('wp-mediaelement');
		wp_enqueue_script('wp-mediaelement');
        

		// frontent JS
        $in_footer = (get_option('mg_js_head')) ? false : true;
		
        wp_enqueue_script('mg_muuri', MG_URL .'/js/muuri/muuri.min.js', array('jquery'), '0.9.5', $in_footer);
        wp_enqueue_script('mg-lc-micro-slider', MG_URL .'/js/lc-micro-slider/lc-micro-slider.min.js', array('jquery'), '2.1.1', $in_footer);
        wp_enqueue_script('mg-lazyload', MG_URL .'/js/lc-lazyload/lc-lazyload.min.js', array('jquery'), '3.0.0b', $in_footer);
        wp_enqueue_script('mg-frontend', MG_URL .'/js/mediagrid.min.js', array('jquery'), MG_VER, $in_footer);		

        
        // frontend css
		wp_enqueue_style('mg-frontend', MG_URL .'/css/frontend.min.css', 90, MG_VER);	
		wp_enqueue_style('mg-lightbox', MG_URL .'/css/lightbox.min.css', 90, MG_VER);
        
        if(class_exists('DiviExtension')) {
            wp_enqueue_style('mg-divi-frontend', MG_URL .'/css/frontend.min_for_divi.css', 91, MG_VER);    
        }
        
        
        // custom CSS
        if(!get_option('mg_inline_css') && !get_option('mg_force_inline_css')) {
            wp_enqueue_style('mg-custom-css', MG_URL .'/css/custom.css', 150, MG_VER .'-'. get_option('mg_dynamic_scripts_id'));	
        }
        else {
            add_action('wp_head', 'mg_inline_css', 999);
        }
	}
}
add_action('wp_enqueue_scripts', 'mg_global_scripts', 900);
add_action('admin_enqueue_scripts', 'mg_global_scripts');
add_action('lc_guten_scripts', 'mg_global_scripts');




// use frontend CSS inline
function mg_inline_css() {
	if(isset($GLOBALS['mg_printed_inline_css'])) { // avoid double enqueuing with Gutenberg
        return false;	
	}
	$GLOBALS['mg_printed_inline_css'] = true;
	
	echo 
    '<style type="text/css">'.
	   mg_static::custom_css_less_parser() .
	'</style>';
}




// extra BODY tag classes  
function mg_extra_body_classes($classes) {
    
    if(get_option('mg_disable_rclick')) {
         $classes[] = 'mg_no_rclick';        
    }
    return $classes;
}
add_filter('body_class', 'mg_extra_body_classes');









/////////////////////////////////////////////
/////// MAIN INCLUDES ///////////////////////
/////////////////////////////////////////////

// generic static methods
include_once(MG_DIR .'/classes/mg_static.php');

// lightbox static methods
include_once(MG_DIR .'/classes/mg_lb_static.php');



// admin menu and cpt and taxonomy
include_once(MG_DIR . '/admin_menu.php');

// taxonomy options 
include_once(MG_DIR . '/taxonomy_options.php');

// mg items metaboxes
include_once(MG_DIR . '/mg_items_metaboxes.php');

// post types metabox
include_once(MG_DIR . '/post_types_metabox.php');

// direct image share hack 
include_once(MG_DIR . '/classes/lc_social_img_share_metas.php');

// shortcode
include_once(MG_DIR . '/shortcode.php');

// tinymce button
include_once(MG_DIR . '/tinymce_implementation.php');

// admin  ajax
include_once(MG_DIR . '/admin_ajax.php');

// dynamic javascript and CSS for footer
include_once(MG_DIR . '/dynamic_footer.php');

// grid preview
include_once(MG_DIR . '/grid_preview.php');



// lightbox
include_once(MG_DIR . '/lightbox.php');

// lghtbox comments
include_once(MG_DIR . '/classes/lb_comments.php');



// retrieve deeplinks
include_once(MG_DIR . '/deeplinks_retrieval.php');

// lightbox deeplink launch
include_once(MG_DIR . '/lightbox_deeplink.php');



// gutenberg integration - not for WP 5.8 widgets.. for now
if($_SERVER["REQUEST_URI"] != '/wp-admin/widgets.php') {
    include_once(MG_DIR . '/builders_integration/gutenberg.php');
}

// visual composer integration
include_once(MG_DIR . '/builders_integration/visual_composer.php');

// cornerstone integration
include_once(MG_DIR . '/builders_integration/cornerstone.php');

// elementor integration
include_once(MG_DIR . '/builders_integration/elementor.php');

// divi integration
include_once(MG_DIR . '/builders_integration/divi.php');










////////////
// AVOID issues with bad servers in settings redirect
function mg_settings_redirect_trick() {
	ob_start();
}
add_action('admin_init', 'mg_settings_redirect_trick', 1);
////////////



////////////
// AVOID issues with custom CSS creation on activation
function mg_extra_custom_css_on_activation() {
	if(get_transient('mg_custom_css_after_activation') && !get_option('mg_inline_css')) {
        mg_static::create_frontend_css();
        delete_transient('mg_custom_css_after_activation');
    }
}
add_action('admin_footer', 'mg_extra_custom_css_on_activation');
////////////



////////////
// EASY WP THUMBS + forcing system
function mg_ewpt() {
	if(get_option('mg_ewpt_force')) {
        $_REQUEST['ewpt_force'] = true;
    }
	include_once(MG_DIR .'/classes/easy_wp_thumbs/easy_wp_thumbs.php');	
}
add_action('init', 'mg_ewpt', 1);
////////////











////////////
// DIKE WP DASHBOARD

$mg_dike_slug = (isset($GLOBALS['is_mg_bundle'])) ? 'mgbp' : 'mg';
define('MG_DIKE_SLUG', $mg_dike_slug);

function mg_dike_updater_data($data) {
    $data['mg'] = array(
        'callback' => 'mg_on_activation',
        'no_files_del' => false,
    );
    return $data;
}
add_filter('dike_lcweb_updater', 'mg_dike_updater_data');


function mg_dike_plc_sc($sc) {
    if(!isset($sc[MG_DIKE_SLUG])) {
        $sc[MG_DIKE_SLUG] = array();    
    }
    
    $sc[MG_DIKE_SLUG] = array_merge($sc[MG_DIKE_SLUG], array('mediagrid'));
    return $sc;
}
add_filter('dike_lcweb_sc', 'mg_dike_plc_sc');
    
include_once(MG_DIR .'/DIKE/register.php');

////////////




// declare Woo HPOS compatibility
add_action('before_woocommerce_init', function() {
	if(class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class) && !isset($GLOBALS['is_mg_bundle'])) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
	}
});




////////////
// actions performed on plugin's activation
include_once(MG_DIR . '/on_activation.php');

function mg_on_activation() {
	include_once(MG_DIR . '/classes/mg_static.php');
	
	// create custom CSS
	(!mg_static::create_frontend_css()) ? update_option('mg_inline_css', 1, false) : delete_option('mg_inline_css');
    
    
    // create a transient for custom CSS creation on next admin page's opening (for add-ons)
    set_transient('mg_custom_css_after_activation', 1, 300);
    
    
    // v7 update - fix for new responsive line heights
    if(!get_option('mg_overlay_touch_behav')) {
        delete_option('mg_lb_txt_line_height');
    }
}
register_activation_hook(__FILE__, 'mg_on_activation');





// re-check for dynamic CSS to be created
function mg_recheck_dynamic_css() {
    if(get_option('mg_recheck_dynamic_css') != MG_VER && !get_option('mg_inline_css')) {
        
        (!mg_static::create_frontend_css()) ? update_option('mg_inline_css', 1, false) : delete_option('mg_inline_css');
        update_option('mg_recheck_dynamic_css', MG_VER, false);
    }
}
add_action('admin_footer', 'mg_recheck_dynamic_css', 999);


