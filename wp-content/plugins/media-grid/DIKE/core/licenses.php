<?php class dike_licenses{public static function prod_is_theme($path){return(strpos($path,'/')!==false)?false:true;}public static function owned_products($author){$owned=array();if(!isset($GLOBALS['dike_products'][$author])){return $owned;}if(!function_exists('is_plugin_active')){include_once(ABSPATH.'wp-admin/includes/plugin.php');}if(!function_exists('get_template')){include_once(ABSPATH.'wp-admin/includes/theme.php');}foreach($GLOBALS['dike_products'][$author]as $path=>$pdata){if((!self::prod_is_theme($path)&&is_plugin_active($path))||(self::prod_is_theme($path)&&get_template()==$path)){$owned[$path]=$pdata;}}return $owned;}public static function missing_products($author){$owned=self::owned_products($author);$missing=array();foreach($GLOBALS['dike_products'][$author]as $path=>$data){if(isset($owned[$path])){continue;}$missing[$path]=$data;}$missing=(array)apply_filters('dike_'.$author.'_missing_products',$missing,$owned);return $missing;}public static function supports_lc_dashboard($path,$author){global $wp_filesystem;if(strpos($path,'pvtcontent_bundle/')!==false&&strpos($path,'/private_content.php')===false){$target_path='pvtcontent_bundle/plugins/private-content/private_content.php';return self::supports_lc_dashboard($target_path,$author);}elseif(strpos($path,'private-content-')!==false){$target_path='private-content/private_content.php';return self::supports_lc_dashboard($target_path,$author);}elseif(strpos($path,'media-grid-bundle/')!==false&&strpos($path,'/media-grid.php')===false){$target_path='media-grid-bundle/plugins/media-grid/media-grid.php';return self::supports_lc_dashboard($target_path,$author);}elseif(strpos($path,'media-grid-')!==false&&strpos($path,'media-grid-bundle/')===false){$target_path='media-grid/media-grid.php';return self::supports_lc_dashboard($target_path,$author);}WP_Filesystem();$arr=explode('/',$path);unset($arr[(count($arr)-1)]);$target_old=WP_PLUGIN_DIR.'/'.implode('/',$arr).'/classes/lc_wp_auto_updater';$target_dike=WP_PLUGIN_DIR.'/'.implode('/',$arr).'/DIKE';return($wp_filesystem->exists($target_old)&&!$wp_filesystem->exists($target_dike))?false:true;}public static function get_site_domain(){return self::ignore_www(site_url());}public static function ignore_www($str){$str=untrailingslashit(strtolower($str));$str=trim(untrailingslashit(str_replace(array('www.','https://','http://'),'',$str)));$arr=explode('/',$str);return $arr[0];}public static function human_remaining_time($time,$short=false){$secs=$time-gmdate('U');$units=array("month"=>30*24*3600,"week"=>7*24*3600,"day"=>24*3600,"hour"=>3600,"minute"=>60);$singular_names=array('month'=>_('month'),'week'=>_('week'),'day'=>_('day'),'hour'=>_('hour'),'minute'=>_('minute'));$plural_names=array('month'=>_('months'),'week'=>_('weeks'),'day'=>_('days'),'hour'=>_('hours'),'minute'=>_('minutes'));$human='';$second_cycle=false;foreach($units as $unit=>$divisor){$result=$secs/$divisor;if($result>=1){if($short&&$second_cycle){break;}$int_val=floor($result);$txt=($int_val>1)?$plural_names[$unit]:$singular_names[$unit];$and=($second_cycle)?' '._('and').' ':'';$human.=$and.floor($result).' '.$txt;if(is_float($result)&&!$second_cycle){$secs=$secs-($divisor*$int_val);$second_cycle=true;}else{break;}}}return $human;}public static function human_support_remaining_time($time,$shop){$secs=$time-gmdate('U');$pre='<span class="dashicons dashicons-sos"></span> ';if($secs<120){$txt=($shop=='envato')?'Expired support entitlement':'Expired support and updates entitlement';return '<span class="dike_expired_support">'.$pre.$txt.'</span>';}$txt=($shop=='envato')?'Support entitlement ends in':'Support and updates entitlement ends in';$human=self::human_remaining_time($time,true);return $pre.$txt.' '.$human;}public static function human_lic_expir_remaining_time($time){$secs=$time-gmdate('U');if($secs<120){return '<span class="dike_expired_support">expired token</span>';}$human=self::human_remaining_time($time,true);return $human.' left';}public static function init_updater(){global $dike_authors;foreach(array_keys($dike_authors)as $author){$updater_prods_data=(array)apply_filters('dike_'.$author.'_updater',array());if($author=='lcweb'){$updater_prods_data=apply_filters('lcwpb_updater_data',$updater_prods_data);}foreach(self::owned_products($author)as $prod_path=>$pdata){$true_slug=str_replace($author.'-','',$pdata['slug']);if($author=='lcweb'&&!self::supports_lc_dashboard($prod_path,$author)){continue;}if(!isset($updater_prods_data[$true_slug])){$callback=false;$no_files_del=false;}else{$upd_data=$updater_prods_data[$true_slug];$callback=(isset($upd_data['callback']))?(string)$upd_data['callback']:false;$no_files_del=(isset($upd_data['no_files_del'])&&$upd_data['no_files_del']===true)?true:false;}if(apply_filters('dike_skip_'.$author.'_updater',false,$pdata['slug'],$pdata)){continue;}$prod_type=(self::prod_is_theme($prod_path))?'theme':'plugin';$prod_path=($prod_type=='theme')?get_template_directory():WP_PLUGIN_DIR.'/'.$prod_path;$updater=new dike_product_updater($author,$dike_authors[$author],$pdata['slug'],$prod_type,$prod_path,$callback,$no_files_del);$updater->dike_prod_data=$pdata;}}}public static function decrypt_js_crypted($encoded){$decoded="";for($i=0;$i<strlen($encoded);$i++){$b=ord($encoded[$i]);$a=$b^88;$decoded.=chr($a);}return json_decode($decoded,true);}public static function plc($author,$slug,$js_redirect=false){global $dike_products;$GLOBALS['dike_is_checking_lic']=true;$slug_exists=false;if(!isset($dike_products[$author])){return true;}foreach($dike_products[$author]as $pdata){if($pdata['slug']==$slug){$slug_exists=true;break;}}if(!$slug_exists){return true;}$class=new dike_licenses($author);$db=$class->get_db();$licenses=(array)$db['lic'];$to_return=(isset($licenses[$slug]))?true:false;$to_return=apply_filters('dike_'.$author.'_plc',$to_return,$slug,$js_redirect,$author);if(!$to_return&&$js_redirect&&function_exists('current_user_can')&&current_user_can('manage_options')&&!wp_doing_ajax()){if(!is_admin()||(is_admin()&&strpos($_SERVER["REQUEST_URI"],'page=dike_wpd')===false)){echo str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),'','
                <script type="text/javascript">
                (function() {
                    "use strict";
                    window.location.href = `'.admin_url('index.php?page=dike_wpd&author='.$author.'&failed_lic_check='.$slug).'`;
                })();
                </script>');}}return $to_return;}public static function plc_js(){global $dike_authors,$dike_products;$GLOBALS['dike_is_checking_lic']=true;$verified_slugs=array();foreach(array_keys($dike_authors)as $author){$class=new dike_licenses($author);$db=$class->get_db();$licenses=(array)$db['lic'];if(is_array($dike_products)&&is_array($dike_products[$author])){foreach($dike_products[$author]as $pdata){if(isset($licenses[$pdata['slug']])){$verified_slugs[]=$pdata['slug'];}}}}$code=str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),'','
        <script type="text/javascript">
        (function() {
            "use strict";
            
            const vps = JSON.parse(`'.json_encode($verified_slugs).'`);
            
            window.dike_plc=(i,d,e=!1)=>{const n=i+"-*";return-1!==vps.indexOf(d)||void 0!==window.dike_tvb&&(-1!==window.dike_tvb.indexOf(d)||-1!==window.dike_tvb.indexOf(n))||(e&&(window.location.href="'.admin_url().'index.php?page=dike_wpd&author="+i+"&failed_lic_check="+i+"-"+d),!1)};
        })();
        </script>');echo apply_filters('dike_plc_js',$code);}public static function plc_sc(){global $dike_authors;foreach(array_keys($dike_authors)as $author){$to_manage=(array)apply_filters('dike_'.$author.'_sc',array());foreach($to_manage as $slug=>$shortcodes){if(!self::plc($author,$slug)){foreach((array)$shortcodes as $sc){remove_shortcode($sc);add_shortcode($sc,function($atts,$content,$sc_name){return(current_user_can('manage_options'))?'['.$sc_name.'] - product license not validated':'';});}}}}}private $dbsk;private $author;public function __construct($author){$this->dbsk=md5('dike_ldb'.self::get_site_domain());$this->author=$author;}public function get_db(){if(isset($GLOBALS['dike_db_cache'])){return $GLOBALS['dike_db_cache'];}$db=$this->decrypt_array(get_site_option($this->dbsk));if(!is_array($db)){$db=array('tic'=>gmdate('U'),'lic'=>array());}else{foreach(self::owned_products($this->author)as $pdata){if(!empty($pdata['envato_id'])&&isset($db['lic'][$pdata['envato_id']])){$db['lic'][$pdata['slug']]=$db['lic'][$pdata['envato_id']];unset($db['lic'][$pdata['envato_id']]);}}}$GLOBALS['dike_db_cache']=$db;return $db;}private function call_lclh($action,$params=array()){global $dike_authors;$params['auth_key']='customer.website';$params['action']=$action;$endpoint=$dike_authors[$this->author]['lic_check_endpoint_url'];if(!wp_http_supports(array('ssl'))){$endpoint=set_url_scheme($endpoint,'http');}$params=(array)apply_filters('dike_'.$this->author.'_wp_remote_post_params',$params,$this->author,$action);add_filter('https_ssl_verify','__return_false',99999999);$data=wp_remote_post($endpoint,array('timeout'=>3,'redirection'=>1,'body'=>$params,'sslverify'=>false,'reject_unsafe_urls'=>false,));if(is_wp_error($data)){return array('status'=>400,'content'=>'WP Error - '.$data->get_error_message());}elseif(wp_remote_retrieve_response_code($data)!=200){return array('status'=>wp_remote_retrieve_response_code($data),'content'=>'HTTP Status '.wp_remote_retrieve_response_code($data).' - '.wp_remote_retrieve_body($data));}return array('status'=>200,'content'=>json_decode(wp_remote_retrieve_body($data),true));}private function encrypt_array($array){if(empty($array)){return $array;}return strrev(base64_encode(base64_encode(strrev(serialize((array)$array)))));}private function decrypt_array($str){if(empty($str)){return $str;}return (array)unserialize(strrev(base64_decode(base64_decode(strrev($str)))));}public function validate_domain_token($ajax_str){$data=(array)self::decrypt_js_crypted($ajax_str);if(!isset($data['prod_slug'])||!$this->prod_slug_exists($data['prod_slug'])){return 'Product not among your owned ones';}if(!isset($data['token'])||strlen($data['token'])!=32){return 'Validation token must be 32 characters long';}if(substr($data['token'],0,4)!='olv-'&&!ctype_alnum($data['token'])){return 'Invalid token';}$localhost_split=explode(':',self::get_site_domain());if(!isset($data['domain'])||(count($localhost_split)!=2&&$data['domain']!=self::get_site_domain())){return 'Domain not matching (js-php)';}if(isset($data['purch_code'])&&substr($data['token'],0,4)=='olv-'){if(empty($data['purch_code'])){return 'Please specify the license purchase code<br/><small>(did you click the "offline validation" link?)</small>';}if(empty($data['username'])){return 'Username required for offline token<br/><small>(did you click the "offline validation" link?)</small>';}if($data['purch_code']&&strlen($data['purch_code'])!=36){return 'Validation token must be 36 characters long';}return $this->offline_validation($data['token'],$data['purch_code'],$data['username'],$data['prod_slug']);}$response=$this->call_lclh('validate_domain_token',array('product_slug'=>$data['prod_slug'],'domain'=>self::get_site_domain(),'token'=>$data['token']));if($response['status']!=200){return $response['content'];}else{$check_status=(int)$response['content']['response'];if(!$check_status){switch($response['content']['err_code']){case 'token_not_found':return 'Token not found';break;case 'domain_not_matching':return 'Token not linked to this domain';break;case 'product_not_matching':return 'Token not linked to this product';break;case 'token_expired':return 'Token expired';break;default:return 'Unknown error - '.$response['content']['err_code'];break;}}$db=$this->get_db();if(!is_array($db)){update_site_option($this->dbsk,array());$db=array('tic'=>gmdate('U'),'lic'=>array());}$resp_cont=$response['content'];$db['lic'][$data['prod_slug']]=array('tok'=>$data['token'],'set'=>(int)$resp_cont['support_expir'],'type'=>$resp_cont['token_subj'],'expir'=>(int)$resp_cont['token_exp'],'user'=>$resp_cont['username'],'shop'=>(isset($resp_cont['shop']))?$resp_cont['shop']:'envato',);update_site_option($this->dbsk,$this->encrypt_array($db));if(isset($GLOBALS['dike_db_cache'])){unset($GLOBALS['dike_db_cache']);}$this->remove_refused_lic_opt_item($data['prod_slug']);return true;}}public function offline_validation($token,$purch_code,$username,$prod_slug){$matching=false;for($a=0;$a<=10;$a++){$to_match=md5(json_encode(array(self::get_site_domain(),(int)$this->prod_slug_to_envato_id($prod_slug),$purch_code,$username,gmdate('d'),$a)));if(substr($to_match,4)==substr($token,4)){$matching=true;break;}}if(!$matching){return 'Wrong token or associated data';}$db=$this->get_db();if(!is_array($db)){update_site_option($this->dbsk,array());$db=array('tic'=>gmdate('U'),'lic'=>array());}$resp_cont=$response['content'];$db['lic'][$prod_slug]=array('tok'=>$token,'set'=>0,'type'=>'offline','expir'=>0,'purch_code'=>$purch_code,'user'=>$username,'shop'=>'author');update_site_option($this->dbsk,$this->encrypt_array($db));if(isset($GLOBALS['dike_db_cache'])){unset($GLOBALS['dike_db_cache']);}$this->remove_refused_lic_opt_item($prod_slug);return true;}private function prod_slug_exists($prod_slug){$exists=false;foreach(self::owned_products($this->author)as $pdata){if($pdata['slug']==$prod_slug){$exists=true;break;}}return $exists;}private function prod_slug_to_envato_id($prod_slug){foreach(self::owned_products($this->author)as $pdata){if($pdata['slug']==$prod_slug){return $pdata['envato_id'];break;}}return false;}public function get_licenses($force_check=false,$all_licenses=false){$db=$this->get_db();if(!is_array($db)||empty($db['lic'])){return array();}if(!wp_doing_ajax()){if($force_check||(gmdate('U')-(int)$db['tic'])>3600){$revalidated=$this->revalidate_licenses($db['lic']);if(is_array($revalidated)){return $revalidated;}}}if($all_licenses){return $db['lic'];}$to_return=array();$author_linked_prods=self::owned_products($this->author);foreach($author_linked_prods as $pdata){if(isset($db['lic'][$pdata['slug']])){$to_return[$pdata['slug']]=$db['lic'][$pdata['slug']];}}return $to_return;}private function revalidate_licenses($licenses){$global_var_index='dike_'.md5(json_encode($licenses)).'_lic_revalidated';if(isset($GLOBALS[$global_var_index])){return $licenses;}$GLOBALS[$global_var_index]=true;$offline_licenses=array();foreach($licenses as $prod_slug=>$data){if(substr($data['tok'],0,4)=='olv-'){$offline_licenses[$prod_slug]=$data;unset($licenses[$prod_slug]);}}if(!empty($offline_licenses)&&empty($licenses)){$new=$offline_licenses;$lic_bkp=array();}else{$lic_bkp=(!is_array($licenses))?array():$licenses;$response=$this->call_lclh('revalidate_domain_tokens',array('domain'=>self::get_site_domain(),'licenses'=>(!is_array($licenses))?array():$licenses,'using_slugs'=>true,));if($response['status']!=200){$to_output=(is_array($response['content'])||is_object($response['content']))?json_encode($response['content']):$response['content'];$GLOBALS['dike_operation_error']='Licenses revalidation error - endpoint error - '.$to_output;add_action('admin_footer',function(){echo '
                    <div class="notice notice-error">
                        <p><strong>'.$GLOBALS['dike_operation_error'].'</p>
                    </div>';});$this->update_tic_anyway();return false;}else{$new=array();if(!isset($response['content']['licenses'])||!isset($response['content']['refused'])){$GLOBALS['dike_operation_error']='Licenses revalidation error - endpoint error - invalid data retrieved';add_action('admin_footer',function(){echo '
                        <div class="notice notice-error">
                            <p><strong>'.$GLOBALS['dike_operation_error'].'</p>
                        </div>';});$this->update_tic_anyway();return false;}if(isset($response['content']['licenses'])){foreach($response['content']['licenses']as $prod_slug=>$data){$new[$prod_slug]=array('tok'=>$data['token'],'set'=>(int)$data['support_expir'],'type'=>$data['token_subj'],'expir'=>(int)$data['token_exp'],'user'=>$data['username'],'shop'=>(isset($data['shop']))?$data['shop']:'envato',);}}if(!empty($offline_licenses)){$new=$offline_licenses+$new;}}}update_site_option($this->dbsk,$this->encrypt_array(array('tic'=>gmdate('U'),'lic'=>$new)));if(isset($GLOBALS['dike_db_cache'])){unset($GLOBALS['dike_db_cache']);}$refused=(isset($response['content']['refused']))?$response['content']['refused']:array();$this->setup_refused_lic_opt($lic_bkp,$new,$refused);return $new;}private function setup_refused_lic_opt($pre_check_licenses,$new_licenses,$refused_reasons){$opt_name='dike_refused_licenses';$opt_val=(array)get_site_option($opt_name,array());foreach($pre_check_licenses as $prod_slug=>$data){if(!isset($new_licenses[$prod_slug])){$opt_val[$prod_slug]=array('tok'=>$data['tok'],'reason'=>(isset($refused_reasons[$prod_slug]))?$refused_reasons[$prod_slug]:'');}}update_site_option($opt_name,$opt_val);}private function remove_refused_lic_opt_item($product_id){$opt_name='dike_refused_licenses';$opt_val=(array)get_site_option($opt_name,array());if(isset($opt_val[$product_id])){unset($opt_val[$product_id]);}update_site_option($opt_name,$opt_val);}private function update_tic_anyway(){$db=$this->get_db();if(!is_array($db)||empty($db['lic'])){$db=array('lic'=>array());}$db['tic']=gmdate('U');update_site_option($this->dbsk,$this->encrypt_array($db));if(isset($GLOBALS['dike_db_cache'])){unset($GLOBALS['dike_db_cache']);}}public function get_next_lic_check_countdown(){$db=$this->get_db();if(!is_array($db)||empty($db['tic'])||empty($db['lic'])||isset($_GET['refresh_licenses'])){return '';}$next_check_time=(int)$db['tic']+3600;$diff=$next_check_time-gmdate('U');if($diff<120){return '';}$refresh_again_link=(isset($_GET['refresh_licenses']))?'':'<br/><a href="'.admin_url('index.php?page=dike_wpd&author='.$this->author.'&refresh_licenses').'">refresh now</a>';return 'next licenses check in '.self::human_remaining_time($next_check_time,$short=false).$refresh_again_link;}}