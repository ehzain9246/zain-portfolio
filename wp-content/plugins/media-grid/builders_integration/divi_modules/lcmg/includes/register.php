<?php
// REGISTER MODULE IN THE DIVI ENGINE


class mg_grid_for_divi extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'mg_ml';

    
    
	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'lcmg';

    
    
	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = MG_VER;

    
    
	/**
	 * LCWP_LcDiviExt constructor.
	 *
	 * @param string $name
	 * @param array  $args
	 */
	public function __construct($name = 'inherit', $args = array() ) {
        
		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );

		parent::__construct($this->name, $args);
        
        add_action('wp_enqueue_scripts', array($this, 'scripts_management'), 9999);
	}
    
    
    
    
    public function scripts_management() {
        wp_enqueue_script('jquery');
        
        // inject JS on builder mode (hardcoding) 
        if(strpos($_SERVER["REQUEST_URI"], 'et_fb=1') !== false) {
            $js_vars = array(
                'slug'              => $this->name,
                'ajax_url'          => untrailingslashit(site_url()) .'/wp-admin/admin-ajax.php', 
                'default_display'   => '',
                'nonce'             => wp_create_nonce('lcwp_nonce'),
                'field_indexes'     => $GLOBALS[$this->name .'_divi_field_indexes'],
            );
            wp_localize_script('jquery', $this->name .'_divi_vars', $js_vars);
        }

        wp_dequeue_script("{$this->name}-frontend-bundle");
        wp_dequeue_style("{$this->name}-styles");
    }
}

new mg_grid_for_divi;
