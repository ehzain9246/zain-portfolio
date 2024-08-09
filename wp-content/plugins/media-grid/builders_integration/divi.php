<?php
// INITIALIZE DIVI MODULES


class mg_divi_modules {

    // DEEFINE MODULES
    // module slug => php files slug
    private $modules = array(
        'lcmg'    => 'lcmg',
    );
    
    
    
    /* 
     * static method to render elements from both builder and frontend 
     *
     * @param (string) $module_slug
     * @param (array) $vals = values passed by the builder
     */
    public static function front_shortcode_render($module_slug, $vals) {
        switch($module_slug) {
           
            case 'lcmg' :
                $shortcode = '[mediagrid '. self::vals_to_sc_params($module_slug, $vals) .']';
                break;
                
            default :
                return $module_slug .' module not found';  
        }    
        
        //echo $shortcode; // debug
        return do_shortcode($shortcode);
    }
    
    
    
    /* insert here custom actions upon initialization (eg. to create global variable containing galleries array) */
    private function custom_actions() {
        $GLOBALS['mg_divi_icon_path'] = MG_DIR .'/builders_integration/divi_modules/icon.svg';
            
        // grids array
        register_taxonomy_mg_grids(); // be sure tax are registered
        $grids = get_terms('mg_grids', array('hide_empty' => 0, 'orderby' => 'name'));
        $GLOBALS['mg_divi_grids'] = array(); 
        
        if(is_array($grids)) {
            foreach($grids as $grid) {
                $GLOBALS['mg_divi_grids'][ $grid->term_id ] = $grid->name;
            }
        }
            
                           
        // pagination systems
        $GLOBALS['mg_divi_pag_sys'] = array(
            '0' => __('default one', 'mg_ml')
        );
        foreach(mg_static::pag_layouts() as $type => $name) {
            $GLOBALS['mg_divi_pag_sys'][ $type ] = $name;
        }


        // MG item categories array (use full list for now)
        register_cpt_mg_item(); // be sure tax are registered
        $GLOBALS['mg_divi_def_filter'] = array(
            '0' => __('no initial filter', 'mg_ml')
        ); 
        foreach(mg_static::item_cats() as $cat_id => $cat_name) {
            $GLOBALS['mg_divi_def_filter'][ $cat_id ] = $cat_name;
        }


        ///// ADVANCED FILTERS ADD-ON //////////
        ////////////////////////////////////////

        $GLOBALS['mg_divi_filters'] = array(
            '0' => __('No'),
            '1' => __('Yes'),
        );
        if(class_exists('mgaf_static')) {
            $GLOBALS['mg_divi_filters'] = array(
                '0' => __('No', 'mg_ml'),
                '1' => __('Yes (MG categories)', 'mg_ml'),
            ) + mgaf_static::filters_list();
        }


        ///// OVERLAY MANAGER ADD-ON ///////////
        ////////////////////////////////////////


        $GLOBALS['mg_divi_overlays'] = array(
            '0' => __('default one', 'mg_ml')
        );

        if(defined('MGOM_DIR')) {	
            register_taxonomy_mgom(); // be sure tax are registered
            $overlay_terms = get_terms('mgom_overlays', 'hide_empty=0');

            foreach($overlay_terms as $ol) {
                $GLOBALS['mg_divi_overlays'][ $ol->term_id ] = $ol->name;	
            }
        }                 
    }
    
    
    
    

    ####################################################################################################
    ## Common methods
    
    
    function __construct() {
        // initialize modules
        add_action('divi_extensions_init', array($this, 'init_modules'));
        
        // ajax handlers
        foreach($this->modules as $key => $name) {
            add_action('wp_ajax_'. $key .'_for_divi', array($this, 'ajax_handler'));
        }
    }
    
    
    /* include divi integration files */
    public function init_modules() {   
        $this->custom_actions();
        
        foreach($this->modules as $module) {
            include_once(__DIR__ .'/divi_modules/'. $module .'/includes/register.php');
        }    
    } 
    
    
    
    public function ajax_handler() {
        if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'lcwp_nonce')) {
            wp_die('Cheating?');
        };
        
        if(!isset($_POST['module'])) {
            die('module not found');    
        }
        
        echo self::front_shortcode_render($_POST['module'], $_POST['params']);
        die();
    }
    
    
    
    /* 
     * Static method compiling shortcode attributes from values array. Also strips out useless Divi values 
     * @param (array) $exception_indexes = array of extra indexes to save (eg. width and height) 
     */
    private static function vals_to_sc_params($module_slug, $vals, $exception_indexes = array()) {
       
        // strip useless parameters
        if(isset($GLOBALS[$module_slug .'_divi_field_indexes'])) {
            foreach($vals as $key => $val) {
                if(!in_array($key, $GLOBALS[$module_slug .'_divi_field_indexes']) && !in_array($key, (array)$exception_indexes)) {
                    unset($vals[$key]);    
                }
            }
        }
        
        // atts string creator
        $params = '';
        foreach($vals as $key => $val) {
            if($val === 'on' || $val === __('Yes')) {
                $val = 1;
            }
            elseif($val === 'off' || $val === __('No')) {
                $val = 0;
            }
            elseif($val === 'unset') {
                $val = '';
            }

            $params .= $key.'="'. esc_attr((string)$val) .'" ';
        }    
        
        return $params;
    }
}
new mg_divi_modules();








// constant to avoid useless Divi fields on module
if(!defined('LC_DIVI_DEF_OPTS_OVERRIDE')) {
    $indexes = array(
        'link_options',
        'admin_label',
        'background',
        'text',
        'fonts',
        'borders',
        'box_shadow',
        'margin_padding',
        'button',
        'filters',
        'text_shadow',
        'width', 
    );
    $to_return = array();

    foreach($indexes as $i) {
        $to_return[$i] = false;     
    }

    $to_return['width'] = array();
    $to_return['max_width'] = array(
        'use_max_width'        => false,
        'use_module_alignment' => false,
    );
    
    define('LC_DIVI_DEF_OPTS_OVERRIDE', serialize($to_return));
}


