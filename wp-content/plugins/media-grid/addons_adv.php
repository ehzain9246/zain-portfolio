<?php 
$banners_baseurl = MG_URL .'/img/addons/'; 

$addons = mg_static::addons_db();
$missing = mg_static::addons_not_installed();
?>

<div id="mgaa_wrap">
    <h1 id="mgaa_h1">
    	<img src="<?php echo $banners_baseurl ?>mg_logo.png" alt="media grid logo" />
    	Media Grid plugin is just the beginning
        <small>Add-ons push its capabilities to the maximum, offering incredible possibilities!</small>
	</h1>
    
    
    <div id="mgaa_banners_wrap">
        <?php 
        foreach($addons as $id => $data) {
            $owned_class 	= (in_array($id, $missing)) ? '' : 'mgaa_owned';
            $link 			= ($owned_class) ? 'javascript:void(0)' : $data['link'];
			$target 		= ($owned_class) ? '' : 'target="_blank"'; 
			 
			$txt = ($owned_class) ? '<strong>Installed!</strong><i class="dashicons dashicons-thumbs-up"></i>' : '<strong>Check it!</strong> <i class="dashicons dashicons-cart"></i>'; 
			 
            echo '
            <a href="'. $link .'" '. $target .' class="'. $owned_class .'" title="'. addslashes($data['descr']) .'">'.
                '<img src="'. $banners_baseurl . $id .'.jpg" alt="'. $data['name'] .'" />'.
				'<span>'. $txt .'</span>'.
            '</a>';
        }
        ?>
    </div>
    
    
    <div id="mgaa_bundle_wrap">
        <h2 id="mgaa_h2">Need everything? Get the bundle and save up to 20% now!
        <small>Future add-ons will be included for free!</small></h2>
        
        <a id="mgaa_bundle_btn" href="https://charon.lcweb.it/3d15612a" target="_blank">
            <img src="<?php echo $banners_baseurl ?>bundle_logo.png" /> Get the bundle!
        </a>
	</div>
</div>