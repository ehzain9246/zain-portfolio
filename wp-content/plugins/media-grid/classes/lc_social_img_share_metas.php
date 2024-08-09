<?php

/**
 *
 * Sets Facebook and Twitter sharing metas for specific images
 * requires specific URL parameters to be passed
 *
 * @autor: Luca Montanari (LCweb)
 * @website https://lcweb.it
 *
 * @version 1.0
 *
 **/


if(!function_exists('lcsism_start')) {

	
	// start the engine (requires lcism URL parameter)
	function lcsism_start() {
		ob_start('lcsism_finish');
	}
	if(isset($_REQUEST['lcsism_img'])) {
		add_action('template_redirect', 'lcsism_start', 9999);
		add_action('wp_head', 'lcsism_start', 9999);
		
		//add_action('get_header', 'lcsism_start'); // on some shitty themes it isn't used
	}
	
	
	function lcsism_finish($html) {
		$lcsism = new lcsism($html);
		return $lcsism->get_html();
	}
	
	
	
	
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	
	
	
	// helper function building the share URL - returns it already urlencoded
	function lcsism_share_url($title, $descr, $img) {
		
		// retrieve page's URL
		$pageURL = 'http';
		if ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || (function_exists('is_ssl') && is_ssl())) {$pageURL .= "s";}
		$pageURL .= "://" . $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];	
		
		
		// if already has parameters
		if(strpos($pageURL, '?') !== false) {
			$url_arr = explode('?', $pageURL);
			$params = explode('&', $url_arr[1]);
			
			// already has LCSISM parameters? strip them
			$a = 0;
			foreach($params as $p) {
				if(strpos($p, 'lcsism_title=') !== false || strpos($p, 'lcsism_descr=') !== false || strpos($p, 'lcsism_img=') !== false) {
					unset($params[$a]);	
				}

				$a++;
			}
			
			// re-compose URL
			if(!empty($params)) {
				$pageURL = $url_arr[0] . '?' . implode('&', $params);  	
			}
		}	
		
		
		
		// apply LCSISM params
		if(strpos($pageURL, '?') === false) {
			$pageURL .= '?';	
		} else {
			$pageURL .= '&';	
		}
		
		
		
		$lcsism_params = array(
			'lcsism_title='.	urlencode(strip_tags($title)), 
			'lcsism_descr='.	urlencode(strip_tags(substr($descr, 0, 210))), 
			'lcsism_img='.		urlencode($img)
		);
		$pageURL .= implode('&', $lcsism_params);
		
		return $pageURL;
	}
	
	
	

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	
	
	
	// Facebook redirect hack - REQUIRES WORDPRESS
	function lcsism_redirect_url() {
		if(!function_exists('home_url')) {return false;}
			
		return home_url() . '?lcsism_redirect';	
	}
	
		
	function lcsism_redirect_hack_code($html) {
		
		return '
		<!doctype html>
		<html>
		<head>
			<script type="text/javascript">
            (function() { 
                "use strict"; 
				window.close();
            })();     
			</script>
		</head>
		<body></body>
		</html>';
	}
	
	if(isset($_REQUEST['lcsism_redirect'])) {
		ob_start('lcsism_redirect_hack_code');
		die();
	}
	
	
	
	
	
	
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	
	
	class lcsism {
		
		public $html; // page's HTML fetched	
		
		function __construct($html) {
			$this->html = $html;
			$this->override_existing_metas();
			$this->append_metas();
		}
		
		
		
		/* returns page's HTML */
		public function get_html() {
			return $this->html;	
		}
		
		
		
		/* simply nullify existing metas to avoid doubled ones */
		private function override_existing_metas() {
			
			$to_override = array(
				'og:type', 'og:title', 'og:description', 'og:image', 'og:url', 'og:image:alt', 'og:image:width', 'og:image:height',
				'twitter:title', 'twitter:description', 'twitter:image', 'twitter:card'
			);
			
			
			foreach($to_override as $to) {
				if(strpos($this->html, $to) !== false) {
					
					$this->html = str_replace($to, 'foo_'.$to, $this->html);
				}
			}
				
		}



		/* append image metas to HEAD */
		private function append_metas() {
			$url_params = array(
				'lcsism_title' 	=> '', 
				'lcsism_descr' 	=> '', 
				'lcsism_img'	=> ''
			);
			
			// if there are missing data - set as empty
			foreach($url_params as $index => $val) {
				
				if(isset($_REQUEST[$index])) {
					$url_params[$index] = str_replace(array('"', '<', '>'), array('&rdquo;', '&lt;', '&lt;'), strip_tags((string)$_REQUEST[$index]));	
				}
			}
			
			
			// retrieves current page's URL for og:url
			$pageURL = 'http';
			if ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || (function_exists('is_ssl') && is_ssl())) {$pageURL .= "s";}
			$pageURL .= "://" . $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];			
			
			
			// metas code
			$code = '
			<meta property="og:type" 		content="website" />
			<meta property="og:title" 		content="'. $url_params['lcsism_title'] .'" />
			<meta property="og:image:alt" 	content="'. $url_params['lcsism_title'] .'" />
			<meta property="og:description" content="'. $url_params['lcsism_descr'] .'" />
			<meta property="og:image" 		content="'. $url_params['lcsism_img'] .'" />
			<meta property="og:image:width" content="800" />
			<meta property="og:image:height" content="600" />
			<meta property="og:url" 		content="'. $pageURL .'" />
			
			<meta name="twitter:title" 		content="'. $url_params['lcsism_title'] .'" />
			<meta name="twitter:description" content="'. $url_params['lcsism_descr'] .'" />
			<meta name="twitter:image" 		content="'. $url_params['lcsism_img'] .'" />
			<meta name="twitter:card" 		content="summary_large_image" />
			';
			
			$this->html = str_replace('</head>', $code.'</head>', $this->html);
		}


	}



} // check to avoid multiple enqueuing