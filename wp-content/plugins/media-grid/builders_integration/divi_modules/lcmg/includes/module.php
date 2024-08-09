<?php
// DEFINING MODULE STRUCTURE AND FIELDS


class mg_divi_module extends ET_Builder_Module {

	public $slug       = 'lcmg';
	public $vb_support = 'on';

    
	protected $module_credits = array(
        'module_uri' => 'https://lcweb.it/media-grid',
        'author'     => 'LCweb',
        'author_uri' => 'https://lcweb.it/',
	);

    
    public function get_advanced_fields_config() {
        return unserialize(LC_DIVI_DEF_OPTS_OVERRIDE);
	}

    
	public function init() {
		$this->name               = 'Media Grid';
		$this->icon_path          = $GLOBALS['mg_divi_icon_path'];
		$this->main_css_element   = '%%order_class%%';	
        
        $this->settings_modal_toggles  = array(
			'general'  => array(
				'toggles' => array(
					'main'     => esc_html__('Main Options', 'mg_ml'),
                    'styling'  => esc_html__('Styling', 'mg_ml'),
				),
			),
		);
	}
 
    
	public function get_fields() {
        $letud = esc_html__('Leave empty to use default one', 'mg_ml');
        
        $fields = array(
            'gid' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Grid', 'mg_ml'),
				'type'            => 'select',
                'default'         => 'unset',
				'default_on_front'=> 'unset',
				'options'         => array('unset' => esc_html__('(choose a grid)', 'mg_ml')) + $GLOBALS['mg_divi_grids'],
				//'description'     => esc_html__( 'Choose whether your linklink opens in a new window or not', 'dicm-divi-custom-modules' ),
			),
            'pag_sys' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Pagination system', 'mg_ml'),
				'type'            => 'select',
                'default'         => current(array_keys($GLOBALS['mg_divi_pag_sys'])),
				'default_on_front'=> current(array_keys($GLOBALS['mg_divi_pag_sys'])),
				'options'         => $GLOBALS['mg_divi_pag_sys'],
			),	
            'filter' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Enable filters?', 'mg_ml'),
				'type'            => 'select',
                'default'         => current(array_keys($GLOBALS['mg_divi_filters'])),
				'default_on_front'=> current(array_keys($GLOBALS['mg_divi_filters'])),
				'options'         => $GLOBALS['mg_divi_filters'],
			),	
            'filters_align' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Filters position', 'mg_ml'),
				'type'            => 'select',
                'default'         => 'top',
				'default_on_front'=> 'top',
				'options'         => array(
                    'top' 	=> __('On top', 'mg_ml'),
                    'left'	=> __('Left side', 'mg_ml'),
                    'right' => __('Right side', 'mg_ml')
                ),
			),	
            'def_filter' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Default filter', 'mg_ml'),
				'type'            => 'select',
                'default'         => current(array_keys($GLOBALS['mg_divi_def_filter'])),
				'default_on_front'=> current(array_keys($GLOBALS['mg_divi_def_filter'])),
				'options'         => $GLOBALS['mg_divi_def_filter'],
                'description'     => (class_exists('mgaf_static')) ? esc_html__('(only for default categories filter)', 'mg_ml') : '',
			),	
            'search' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Enable search?', 'mg_ml'),
				'type'            => 'yes_no_button',
                'default'         => 'off',
				'default_on_front'=> 'off',		
				'options'         => array(
					'off' => esc_html__('No', 'mg_ml'),
					'on'  => esc_html__('Yes', 'mg_ml'),
				),
			),	
            'hide_all' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Hide "All" filter?', 'mg_ml'),
				'type'            => 'yes_no_button',
                'default'         => 'off',
				'default_on_front'=> 'off',		
				'options'         => array(
					'off' => esc_html__('No', 'mg_ml'),
					'on'  => esc_html__('Yes', 'mg_ml'),
				),
                'description'     => (class_exists('mgaf_static')) ? esc_html__('(only for default categories filter)', 'mg_ml') : '',
			),	
            'overlay' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Overlay', 'mg_ml'),
				'type'            => 'select',
                'default'         => current(array_keys($GLOBALS['mg_divi_overlays'])),
				'default_on_front'=> current(array_keys($GLOBALS['mg_divi_overlays'])),
				'options'         => $GLOBALS['mg_divi_overlays'],
			),	
            'title_under' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Text under items?', 'mg_ml'),
				'type'            => 'select',
                'default'         => 'unset',
				'default_on_front'=> 'unset',
				'options'         => array(
                    'unset' => __('No', 'mg_ml'),
                    '1'     => __('Yes - attached to item', 'mg_ml'),
                    '2'     => __('Yes - detached from item', 'mg_ml')
                ),
			),	
            'mf_lightbox' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Media-focused lightbox mode?', 'mg_ml'),
				'type'            => 'select',
                'default'         => '',
				'default_on_front'=> '',
				'options'         => array(
                    'unset' => __('as default', 'mg_ml'),
                    '0'     => __('No'),
                    '1'     => __('Yes')
                ),
			),	
            'mobile_tresh' => array(
                'toggle_slug'     => 'main',
				'label'           => esc_html__('Custom mobile threshold', 'mg_ml'),
				'type'            => 'range',
                'default'         => 0,
				'default_on_front'=> 0,
				'range_settings'    => array(
					'min'   => 0,
					'max'   => 1000,
                    'step'  => 1
				),
                'validate_unit' => true,
                'description'   => esc_html__('Overrides global threshold. Use zero to ignore', 'mg_ml'),
			),
            
            
            //////////////////
            
            
            'cell_margin' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__("Items margin", 'mg_ml') .' (px)',
				'type'            => 'text',
                'default'         => '',
				'default_on_front'=> '',
                'description'     => $letud,
			),
            'border_w' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__("Item borders width", 'mg_ml') .' (px)',
				'type'            => 'text',
                'default'         => '',
				'default_on_front'=> '',
                'description'     => $letud,
			),
            'border_col' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__("Item borders color", 'mg_ml'),
				'type'            => 'color',
                'default'         => '',
				'default_on_front'=> '',
                'description'     => $letud,
			),
            'border_rad' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__("Items border radius", 'mg_ml') .' (px)',
				'type'            => 'text',
                'default'         => '',
				'default_on_front'=> '',
                'description'     => $letud,
			),
            'outline' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__('Display items outline?', 'mg_ml'),
				'type'            => 'select',
                'default'         => '',
				'default_on_front'=> '',
				'options'         => array(
                    'unset' => __('as default', 'mg_ml'),
                    '0'     => __('No'),
                    '1'     => __('Yes')
                ),
			),	
            'outline_col' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__('Outline color', 'mg_ml'),
				'type'            => 'color',
                'default'         => '',
				'default_on_front'=> '',
                'description'     => $letud,
			),
            'shadow' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__('Display items shadow?', 'mg_ml'),
				'type'            => 'select',
                'default'         => '',
				'default_on_front'=> '',
				'options'         => array(
                    'unset' => __('as default', 'mg_ml'),
                    '0'     => __('No'),
                    '1'     => __('Yes')
                ),
			),	
            'txt_under_col' => array(
                'toggle_slug'     => 'styling',
				'label'           => esc_html__('Text under images color', 'mg_ml'),
				'type'            => 'color',
                'default'         => '',
				'default_on_front'=> '',
                'description'     => $letud,
			),
		);
        
        
        $GLOBALS[ $this->slug .'_divi_field_indexes'] = array_keys($fields);
        return $fields;
	}


    
    public function render($attrs, $content = null, $render_slug = null) {
        return mg_divi_modules::front_shortcode_render($this->slug, $this->props);  
	}
}

new mg_divi_module;