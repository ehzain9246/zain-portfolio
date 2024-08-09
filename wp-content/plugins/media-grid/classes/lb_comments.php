<?php
// prepare and echoes comments block for lightbox - setup website data if facebook is chosen

class mg_lb_comments {
	private $comments_type = '';
	 
	
	// handle grid global vars
	function __construct() {
		$this->comments_type = get_option('mg_lb_comments');
	
	
		// consider also FB direct share
		if($this->comments_type == 'fb' || (get_option('mg_facebook') && get_option('mg_fb_direct_share_app_id'))) {
			add_action('wp_head', array($this, 'setup_fb_metas'));	
			add_action('wp_footer', array($this, 'append_fb_scripts'));	
		}
	}
	
	
	
	// setup facebook metas
	public function setup_fb_metas() {
		if(get_option('mg_lbc_fb_app_id')) :
		?>
        <meta property="fb:app_id" content="<?php echo get_option('mg_lbc_fb_app_id') ?>" />
        <?php
		endif;
		if(get_option('mg_fb_direct_share_app_id')) :
		?>
        <meta property="fb:app_id" content="<?php echo get_option('mg_fb_direct_share_app_id') ?>" />
        <?php
		endif;
	}
	
	// append FB scripts
	public function append_fb_scripts() {
		$app_id = (get_option('mg_fb_direct_share_app_id')) ? get_option('mg_fb_direct_share_app_id') : get_option('mg_lbc_fb_app_id'); // share app takes over on comment app
		?>
        <div id="fb-root"></div>
        
    	<script type="text/javascript">
		(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/<?php echo get_locale() ?>/sdk.js#xfbml=1&version=v17.0&appId=<?php echo urlencode($app_id) ?>&autoLogAppEvents=1";
            fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		</script>
        <?php
	}
	
	
	
	/* Echoes comments 
	 * @param $subj_id (int) = shown element ID (eg. the post ID if is post contents item type) 
	 */
	public function get_comments($item_id, $subj_id, $title, $has_socials) {
		$unique_url = trailingslashit(get_site_url()) . '?mgi_='.$item_id;
		$socials_class = ($has_socials) ? 'mg_lbcw_has_socials' : '';
		
		// check for disabled comments
		if(get_post_meta($item_id, 'mg_lb_no_comments', true)) {
			echo '';
			return true;	
		}
		
		// print
		switch($this->comments_type) {
			case 'disqus' : // Disqus
				?>
				<div id="mg_lb_comments_wrap" class="mg_lb_disqus_cw <?php echo $socials_class ?>">
               		<div id="disqus_thread"></div>
                </div>    
                    
				<script type="text/javascript">
                (function($) { 
                    "use strict"; 
                    
                    $(document).ready(function(e) {
                        var shortname = '<?php echo get_option('mg_lbc_disqus_shortname') ?>';
                        var id = '<?php echo $subj_id ?>';
                        var url = '<?php echo $unique_url ?>';
                        var title = "<?php echo addslashes($title) ?>";

                        if (window.DISQUS) { // Disqus already called - use the hack to reset instance
                            DISQUS.reset({
                                reload: true,
                                config: function () { 
                                    this.page.url = url;
                                    this.page.identifier = id;
                                    this.page.title = title;
                                }
                            });
                        } 
                        else { // normal init
                            var disqus_config = function () {
                                this.page.url = url;
                                this.page.identifier = id;
                                this.page.title = title;
                            };

                            var d = document, s = d.createElement('script');
                            s.src = '//'+ shortname +'.disqus.com/embed.js';					
                            s.setAttribute('data-timestamp', +new Date());
                            (d.head || d.body).appendChild(s);
                        }
                    });
                })(jQuery); 
				</script>
				<?php
				break;
				
			
			case 'fb' : // Facebook	
				?>	
                <div id="mg_lb_comments_wrap" class="mg_lb_fb_cw <?php echo $socials_class ?>">
					<div class="fb-comments" data-href="<?php echo $unique_url ?>" data-width="100%" data-numposts="1" data-colorscheme="<?php echo get_option('mg_lbc_fb_style', 'light') ?>"></div>
                </div>
                
                <script type="text/javascript">
                (function($) { 
                    "use strict"; 
                    
                    if(jQuery('#fb-root').length && window.FB) { // trick to init multiple times 
                        FB.XFBML.parse();
                    }
                })(jQuery); 
				</script>
				<?php
				break;
				
				
			default :
				echo '';
				break;
		}	
	}
}

$GLOBALS['mg_comments'] = new mg_lb_comments;	
