<?php function dike_init($author_slug,$config,$products){$author_slug=sanitize_title((string)$author_slug);if(empty($author_slug)||empty($config)||!is_array($config)||empty($products)||!is_array($products)){return new WP_Error('dike_bad_init',sprintf('Dike WordPress Dashboard - Author "%s" - bad initilization',$author_slug));}$config_indexes=array('menu_label','author_name','logo_url','lic_check_endpoint_url','updates_endpoint_url','no_support_no_upd_faq_url','custom_head_code','custom_footer_code','pre_products_txt','how_to_get_token_txt',);foreach($config_indexes as $ci){if(!isset($config[$ci])){return new WP_Error('dike_config_key_missing',sprintf('Dike WordPress Dashboard - Author "%s" - "%s" configuration key missing',$author_slug,$ci));}}if(!isset($GLOBALS['dike_authors'])){$GLOBALS['dike_authors']=array();$GLOBALS['dike_products']=array();}$GLOBALS['dike_authors'][$author_slug]=$config;$prod_slugs=array();$prod_envato_ids=array();$prod_indexes=array('slug','name','envato_id','prod_pag_link','buy_prod_link','doc_link','changelog_link','has_trial','logo_url');foreach($products as $prod_path=>$pdata){$skip=false;foreach($prod_indexes as $pi){if(!isset($config[$ci])){trigger_error(sprintf('Dike WordPress Dashboard - "%s" - "%s" configuration key missing',$prod_path,$pi));$skip=true;break;}}if($skip){continue;}$pdata['slug']=(!is_string($pdata['slug']))?false:(string)$pdata['slug'];$pdata['envato_id']=(empty($pdata['envato_id']))?'':(int)$pdata['envato_id'];$pdata['author']=$author_slug;if(empty($pdata['slug'])){trigger_error(sprintf('Dike WordPress Dashboard - Author "%s" - empty product slug',$author_slug));continue;}if(in_array($pdata['slug'],$prod_slugs)){trigger_error(sprintf('Dike WordPress Dashboard - Author "%s" - product slug %s already exists',$author_slug,$pdata['slug']));continue;}$prod_slugs[]=$pdata['slug'];if(!empty($pdata['envato_id'])){if(in_array($pdata['envato_id'],$prod_envato_ids)){trigger_error(sprintf('Dike WordPress Dashboard - Author "%s" - Envato product ID %s already exists',$author_slug,$pdata['envato_id']));continue;}$prod_envato_ids[]=$pdata['envato_id'];}if(!isset($GLOBALS['dike_products'][$author_slug])){$GLOBALS['dike_products'][$author_slug]=array();}$GLOBALS['dike_products'][$author_slug][$prod_path]=$pdata;}}function dike_lc($author,$slug,$js_redirect=false){return dike_licenses::plc($author,$slug,$js_redirect);}function dike_dashboard_message($author,$is_author_dashboard=false){global $dike_authors;$transient_name='dike_'.sanitize_title($author).'_dashboard_mess';$transient_duration=60*15;$data=(isset($_GET['dike_refresh_dashboard_mex']))?false:get_transient($transient_name);if($data=='failed_call'){return false;}elseif(!is_array($data)){$action='dashboard_message';$params=array('auth_key'=>'customer.website','action'=>$action,'username'=>$author,);$endpoint=$dike_authors[$author]['lic_check_endpoint_url'];if(!wp_http_supports(array('ssl'))){$endpoint=set_url_scheme($endpoint,'http');}$params=(array)apply_filters('dike_wp_remote_post_params',$params,$author,$action);add_filter('https_ssl_verify','__return_false',99999999);$data=wp_remote_post($endpoint,array('timeout'=>2,'redirection'=>1,'body'=>$params,'sslverify'=>false,'reject_unsafe_urls'=>false,));if(is_wp_error($data)||wp_remote_retrieve_response_code($data)!=200){set_transient($transient_name,'failed_call',$transient_duration);return false;}else{$data=json_decode(wp_remote_retrieve_body($data),true);if(json_last_error()!==JSON_ERROR_NONE){set_transient($transient_name,'failed_call',$transient_duration);return false;}set_transient($transient_name,$data,$transient_duration);}}$indexes=array('txt','type','custom_border_color','valid_from','valid_until','extra_classes','dismissible','admin_wide');foreach($indexes as $i){if(!isset($data[$i])){return false;}}$notice_id=md5(json_encode($data));if(!$is_author_dashboard&&!$data['admin_wide']){return false;}if($data['dismissible']&&isset($_COOKIE['dike_notice_'.$notice_id.'_dismissed'])){return false;}if(gmdate('U')<(int)$data['valid_from']||gmdate('U')>(int)$data['valid_until']){return false;}$dismissible_class=($data['dismissible'])?'is-dismissible':'';$custom_color_css=($data['type']=='custom')?'style="border-left-color: '.$data['custom_border_color'].';"':'';$type_class_part=($data['type']=='custom')?'info':$data['type'];$txt=(strip_tags($data['txt'])==$data['txt'])?'<div class="dike_basic_notice_txt" style="margin: 0.65rem 0;">'.nl2br($data['txt']).'</div>':$data['txt'];echo '
    <div class="dike_author_notice notice notice-'.$type_class_part.' '.$dismissible_class.' '.$data['extra_classes'].'" data-notice-author="'.$author.'" data-notice-id="'.$notice_id.'" '.$custom_color_css.'>'.$txt.'</div>';}function dike_output_enabled_prod_block($prod_path,$pdata,$licenses){$has_prod_lic=(isset($licenses[$pdata['slug']]))?true:false;$lic=($has_prod_lic)?$licenses[$pdata['slug']]:false;if(!isset($lic['shop'])&&$lic){$lic['shop']=($lic['type']=='trial')?'trial':'envato';}$shop=(isset($lic['shop']))?$lic['shop']:false;$ren_supp_link=(isset($pdata['ren_supp_link'])&&is_array($pdata['ren_supp_link'])&&isset($pdata['ren_supp_link'][$shop])&&!empty($pdata['ren_supp_link'][$shop]))?$pdata['ren_supp_link'][$shop]:$pdata['prod_pag_link']; ?>
    
    <li class="dike_prod" data-prod-envato-id="<?php echo esc_attr($pdata['envato_id']) ?>" data-prod-slug="<?php echo esc_attr($pdata['slug']) ?>" data-prod-changelog="<?php echo esc_attr($pdata['changelog_link']) ?>">
        <div class="dike_prod_firstblock">
            <span>
                <img src="<?php echo esc_attr($pdata['logo_url']) ?>" alt="<?php echo esc_attr($pdata['name']) ?>" />
            </span>

            <div>
                <h4>
                    <?php echo $pdata['name'];if(!$has_prod_lic){echo '<span href="javascript:void(0)" data-tooltip="Product locked. Validate the license domain token to unlock" class="dashicons dashicons-warning dike_prod_not_validated_warn dike_tooltip"></span>';}if(!empty($pdata['changelog_link'])){echo '<a href="javascript:void(0)" class="dashicons dashicons-editor-ul dike_changelog_trigger dike_tooltip" data-tooltip="view changelog"></a>';}if(!empty($pdata['doc_link'])){echo '<a href="'.esc_attr($pdata['doc_link']).'" target="_blank" class="dashicons dashicons-flag dike_doc_link dike_tooltip" data-tooltip="view documentation"></a>';}echo '<a href="'.esc_attr($pdata['buy_prod_link']).'" target="_blank" class="dashicons dashicons-cart dike_shop_link dike_tooltip" data-tooltip="need a new license?"></a>'; ?>
                </h4>

                <?php if(!dike_licenses::supports_lc_dashboard($prod_path,$GLOBALS['dike_dashboad_author'])): ?>
                    <span class="dike_update_to_use_warn">Please update the product to the latest version in order to integrate it here</span>

                <?php else: ?>

                    <?php if($has_prod_lic): ?>
                        <form method="post" class="dike_inserted_token_form form-wrap">
                            <fieldset>
                                <input type="text" name="dike_inserted_token" value="<?php echo $lic['tok'] ?>" disabled="disabled" />
                                <input type="button" name="dike_change_token_btn" value="Change" class="button-secondary" />
                            </fieldset>
                        </form>   
                    <?php endif; ?>

                    <form method="post" class="dike_insert_token_form form-wrap <?php if($lic)echo 'dike_displaynone' ?>">
                        <fieldset>
                            <input type="text" name="dike_prod_token" value="" placeholder="activation token" autocomplete="off" maxlength="32" required="required" />
                            <input type="button" name="dike_prod_activation_btn" value="Validate" class="button-secondary" />

                            <input type="text" name="dike_purch_code" value="" placeholder="purchase code" autocomplete="off" maxlength="36" class="dike_displaynone" />
                            <input type="text" name="dike_username" value="" placeholder="envato username" autocomplete="off" maxlength="125" class="dike_displaynone" />
                            <a href="javascript:void(0)" class="dike_offline_valid">Offline validation</a>
                        </fieldset>    
                    </form>

                    <?php if(!$has_prod_lic){echo '<a href="javascript:void(0)" class="dike_how_get_token">How to get the token?</a>';}endif; ?>
            </div>
        </div>

        <?php if(dike_licenses::supports_lc_dashboard($prod_path,$GLOBALS['dike_dashboad_author'])&&$has_prod_lic): ?>
            <div class="dike_lic_details">
                <p>
                    <i class="dike_tooltip" data-tooltip="Envato Username">
                        <span class="dashicons dashicons-admin-users"></span> <?php echo $lic['user'] ?>
                    </i>
                    <i>
                        <span class="dashicons dashicons-awards"></span> <?php echo ucfirst($lic['type']) ?> license
                        <?php if($lic['expir']){echo '('.dike_licenses::human_lic_expir_remaining_time($lic['expir'],$lic['shop']).')';} ?>
                    </i>
                </p>

                <?php if(in_array($lic['type'],array('production','staging'))): ?>
                <p>
                    <label><?php echo dike_licenses::human_support_remaining_time($lic['set'],$shop) ?></label>
                    <a href="<?php echo esc_attr($ren_supp_link) ?>" target="_blank">
                        <button type="button" name="dike_extend_support_btn" class="button-secondary">
                            <span class="dashicons dashicons-cart"></span>
                            <span>Extend it</span> 
                        </button>    
                    </a>
                </p>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </li>
    <?php }function dike_output_missing_prod_block($author,$pdata){ ?>
    <li class="dike_prod" data-prod-slug="<?php echo esc_attr($pdata['slug']) ?>" data-prod-envato-id="<?php echo (int)$pdata['envato_id'] ?>">
        <div class="dike_prod_firstblock">
            <span>
                <img src="<?php echo esc_attr($pdata['logo_url']) ?>" alt="<?php echo esc_attr($pdata['name']) ?>" />
            </span>

            <div class="dike_not_own_btn">
                <h4><?php echo $pdata['name'] ?></h4>
                <a href="<?php echo esc_attr($pdata['prod_pag_link']) ?>" target="_blank">
                    <button type="button" name="dike_knowmore_btn" class="button-secondary">
                        <span class="dashicons dashicons-share-alt"></span>
                        <span>Know more</span>         
                    </button>    
                </a>

                <?php if($pdata['has_trial']): ?>
                    <a href="https://<?php echo $author  ?>.dikelicensing.com/trial-license-request/?prod=<?php echo urlencode($pdata['slug']).'&domain='.urlencode(dike_licenses::get_site_domain()).'&disabled=domain' ?>" target="_blank">
                        <button type="button" name="dike_gettrial_btn" class="button-secondary">
                            <span class="dashicons dashicons-tickets"></span>
                            <span>Request a trial</span>         
                        </button>    
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
    <?php }function dike_menu(){global $dike_authors,$submenu;foreach((array)$dike_authors as $author_slug=>$config){if(!isset($GLOBALS['dike_menu_setup_config'])){$GLOBALS['dike_menu_setup_config']=array();}$GLOBALS['dike_menu_setup_config'][$author_slug]=$config['menu_label'];$no_menu=apply_filters('dike_skip_'.$author_slug.'_menu',false);if($no_menu){continue;}add_submenu_page('index.php',$config['menu_label'],$config['menu_label'],'manage_options','dike_wpd','dike_wpd');}add_action('admin_footer',function(){ ?>
        <script type="text/javascript">
        (function() { 
            "use strict";   

            let menus = JSON.parse(`<?php echo json_encode($GLOBALS['dike_menu_setup_config']) ?>`);
            
            document.querySelectorAll("#menu-dashboard .wp-submenu a").forEach((function(e){let t=e.getAttribute("href"),n=e.innerText;"index.php?page=dike_wpd"==t&&Object.keys(menus).some((t=>{menus[t]==n&&(e.setAttribute("href","index.php?page=dike_wpd&author="+t),delete menus[t])}))}));
        })();    
        </script>
        <?php });}function dike_wpd(){include_once(__DIR__.'/licenses.php');include_once(__DIR__.'/dashboard.php');}function dike_require_author(){global $current_screen,$dike_authors;if(!is_admin()){return true;}if(property_exists($current_screen,'base')&&$current_screen->base=='dashboard_page_dike_wpd'){if(!isset($_GET['author'])){wp_safe_redirect(admin_url());exit;}$author=$_GET['author'];if(isset($dike_authors[$author])&&$dike_authors[$author]['custom_head_code']){add_action('admin_head',function(){echo $GLOBALS['dike_authors'][$_GET['author']]['custom_head_code'];});}if(isset($dike_authors[$author])&&$dike_authors[$author]['custom_footer_code']){add_action('admin_footer',function(){echo $GLOBALS['dike_authors'][$_GET['author']]['custom_footer_code'];});}}}add_action('current_screen','dike_require_author');add_action('admin_enqueue_scripts',function(){global $current_screen;wp_enqueue_style('dike_admin',$GLOBALS['dike_baseurl'].'/assets/css.min.css',100,$GLOBALS['dike_init']);if($current_screen->base=='dashboard_page_dike_wpd'||$current_screen->base=='plugins'||$current_screen->base=='themes'){wp_enqueue_script('lc-wp-popup-message',$GLOBALS['dike_baseurl'].'/assets/lc_wp_popup_message.min.js',200,'1.0');}},99999);add_action('admin_footer',function(){global $current_screen;if($current_screen->base!='plugins'){return false;} ?>
    <script type="text/javascript">
    (function() { 
        "use strict";   

        document.querySelectorAll('.lcwpau_wizard_btn').forEach(function($btn) {
            $btn.setAttribute('href', "<?php echo admin_url() ?>index.php?page=dike_wpd");
            $btn.classList.remove('thickbox');
        });
    })();    
    </script>
    <?php });add_action('admin_notices',function(){$versions_opt_name='dike_activation_redirect_helper';if(!current_user_can('manage_options')){return false;}$stored_versions=get_site_option($versions_opt_name);$to_save=array();$prods_author=array();$has_changelog=array();$prods_data=array();foreach($GLOBALS['dike_products']as $author=>$products){foreach($products as $path=>$pdata){$subj_data=false;if(!dike_licenses::prod_is_theme($path)){if(!is_plugin_active($path)){continue;}$subj_data=get_plugin_data(WP_PLUGIN_DIR.'/'.$path);}elseif(dike_licenses::prod_is_theme($path)){if(get_template()!=$path){continue;}$subj_data=wp_get_theme($path);}if(!$subj_data){continue;}$to_save[$pdata['slug']]=$subj_data['Version'];$prods_author[$pdata['slug']]=$author;$has_changelog[$pdata['slug']]=(empty($pdata['changelog_link']))?false:true;$prods_data[$pdata['slug']]=$pdata;}}if($stored_versions==$to_save){return true;}update_site_option($versions_opt_name,$to_save);foreach($to_save as $slug=>$new_version){$author=$prods_author[$slug];$pdata=$prods_data[$slug];if(empty($stored_versions)||!isset($stored_versions[$slug])||(isset($stored_versions[$slug])&&$stored_versions[$slug]!=$new_version)){$code='
            <div class="dike_welcome_update_notice notice notice-info is-dismissible">

                    <img src="'.esc_attr($pdata['logo_url']).'" alt="'.esc_attr($slug).'" />
                    <div>';if(!isset($stored_versions[$slug])){$code.='<strong>Thanks for choosing '.$pdata['name'].'!</strong>';if(!empty($pdata['doc_link'])){$code.='<span>Get started in minutes checking the <a href="'.esc_attr($pdata['doc_link']).'" target="_blank">documentation</a></span>';}}else if(!empty($stored_versions)&&isset($stored_versions[$slug])){$code.='<strong>Thanks for updating '.$pdata['name'].' to v'.$to_save[$slug].'!</strong>';if(!empty($pdata['changelog_link'])){$code.='<span>Check out what\'s new in the <a href="'.admin_url('index.php?page=dike_wpd&author=').urlencode($author).'&prod_updated&prod_cl='.urlencode($slug).'">changelog</a></span>';}}echo $code.'
                    </div>
            </div>';}}});add_action('init',function(){global $dike_authors;if(!class_exists('dike_licenses')||!is_array($dike_authors)){return false;}$an_author=array_keys($dike_authors)[0];$lic_class=new dike_licenses($an_author);$lic_class->get_licenses(false);},PHP_INT_MAX);