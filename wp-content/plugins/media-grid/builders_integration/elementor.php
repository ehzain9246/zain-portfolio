<?php
// Elementor shortcodes integration

function mg_on_elementor($widgets_manager) {
    $basepath = MG_DIR .'/builders_integration/elementor_elements';
    
    $widgets = array(
        'grid' => 'mg_grid_on_elementor',
    );
    
    foreach($widgets as $filename => $classname) {
        
        include_once($basepath .'/'. $filename .'.php');
        $widgets_manager->register( new $classname() );
    }
}
add_action('elementor/widgets/register', 'mg_on_elementor');





// style needed for LCweb icons
add_action('elementor/editor/after_enqueue_styles', function() {
    wp_enqueue_style('lcweb-elementor-icon', MG_URL .'/builders_integration/elementor_elements/lcweb_icon.css');	
});
