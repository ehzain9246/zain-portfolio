<?php

add_action('cornerstone_register_elements', 'mg_cornerstone_register_elements');
add_filter('cornerstone_icon_map', 'mg_cornerstone_icon_map', 900);


function mg_cornerstone_register_elements() {
	cornerstone_register_element('lcweb_media_grid', 'lcweb_media_grid', MG_DIR .'/builders_integration/cs_elements/grid');
}


function mg_cornerstone_icon_map( $icon_map ) {
	$icon_map['lcweb_media_grid'] = MG_URL .'/img/cs_icon.svg';
	return $icon_map;
}
