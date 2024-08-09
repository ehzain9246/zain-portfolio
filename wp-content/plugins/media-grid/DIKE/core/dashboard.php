<?php global $dike_authors,$dike_products;$GLOBALS['is_dike_dashboard']=true;$author=sanitize_title($_GET['author']);$GLOBALS['dike_dashboad_author']=$author;if(!isset($dike_authors[$author])){wp_die('Author not found');}$author_data=$dike_authors[$author];$GLOBALS['dike_products'][$author]=apply_filters('dike_'.$author.'_products',$GLOBALS['dike_products'][$author]);$baseurl=$GLOBALS['dike_baseurl'];$dashboard_url=admin_url('index.php?page=dike_wpd&author='.$author);$lic_class=new dike_licenses($author);$force_check=(isset($_GET['refresh_licenses']))?true:false;$licenses=$lic_class->get_licenses($force_check); ?><div class="dike_wrap wrap"data-author="<?php echo esc_attr($author) ?>"><h1 class="wp-heading-inline"id="dike_main_head"><img alt="<?php echo esc_attr($author) ?>"class="dike_<?php echo esc_attr($author) ?>_dashboard"src="<?php echo esc_attr($author_data['logo_url']) ?>"> <span>Dashboard</span></h1><small id="dike_last_check_date"><?php echo $lic_class->get_next_lic_check_countdown(); ?></small><div id="dike_pre_list"><?php echo $author_data['pre_products_txt'] ?></div><?php $owned_products=dike_licenses::owned_products($author);$missing_prods=dike_licenses::missing_products($author);$has_active_theme=false;foreach($owned_products as $prod_path=>$pdata){if(!dike_licenses::prod_is_theme($prod_path)){continue;}$has_active_theme=true;echo '
        <h3>Active theme</h3>
        <ul class="dike_prod_list dike_owned_prods">';dike_output_enabled_prod_block($prod_path,$pdata,$licenses);echo '        
        </ul>';unset($owned_products[$prod_path]);}if(count($owned_products)){$top_margin_code=($has_active_theme)?'<br/><br/>':'';$maybe_s=(count($owned_products)>1)?'s':'';echo $top_margin_code.'    
        <h3>Enabled plugin'.$maybe_s.'</h3>
        <ul class="dike_prod_list dike_owned_prods">';foreach($owned_products as $prod_path=>$pdata){dike_output_enabled_prod_block($prod_path,$pdata,$licenses);}echo '
        </ul>';}if(!empty($missing_prods)){echo '
        <br/><br/><br/><hr/><br/><br/>';$missing_plugins=array();$missing_themes=array();foreach($missing_prods as $prod_path=>$pdata){(dike_licenses::prod_is_theme($prod_path))?$missing_themes[$prod_path]=$pdata:$missing_plugins[$prod_path]=$pdata;}if(!empty($missing_plugins)){$maybe_s=(count($missing_plugins)>1)?'s':'';echo '
            <h3>Plugin'.$maybe_s.' not enabled yet</h3>
            <ul class="dike_prod_list dike_missing_prods">';foreach($missing_plugins as $prod_path=>$pdata){dike_output_missing_prod_block($author,$pdata);}echo '
            </ul>';}if(!empty($missing_themes)){$top_margin_code=(!empty($missing_plugins))?'<br/><br/>':'';$maybe_s=(count($missing_themes)>1)?'s':'';echo $top_margin_code.'
            <h3>Other theme'.$maybe_s.' by '.$author_data['author_name'].'</h3>
            <ul class="dike_prod_list dike_missing_prods">';foreach($missing_themes as $prod_path=>$pdata){dike_output_missing_prod_block($author,$pdata);}echo '
            </ul>';}} ?></div><?php $prod_welcome_code='';if(isset($_GET['prod_welcome'])){foreach($dike_products[$author]as $prod_path=>$pdata){if($_GET['prod_welcome']==$pdata['slug']){$pwc_pdata=$pdata;}}$prod_welcome_code="\n    <div class='dike_prod_welcome_wrap'>\n        <img src='".esc_attr($pwc_pdata['logo_url'])."' />\n        \n        <h3>Thanks for choosing ".$pwc_pdata['name']."!</h3>\n        \n        <button class='button-secondary' onclick='window.lcwpm_close()'>\n            <span class='dashicons dashicons-thumbs-up'></span>\n            Let's start!\n        </button>\n    </div>";$prod_welcome_code=apply_filters('dike_'.$author.'_prod_welcome_code',$prod_welcome_code,$pwc_pdata,$_GET['prod_welcome']);}$failed_prod_lic_check_code='';if(isset($_GET['failed_lic_check'])){$pname=false;$shop_link=false;foreach($dike_products[$author]as $prod_path=>$pdata){if($_GET['failed_lic_check']==$pdata['slug']){$pname=$pdata['name'];$shop_link=$pdata['buy_prod_link'];break;}}if($pname){$failed_prod_lic_check_code='
        Please insert a valid license for <strong>'.$pname.'</strong> to unlock it!<br/>
        <a href="'.$shop_link.'" target="_blank" class="dike_failed_lic_check_link">Need a new license?</a>';}} ?>
<script type="text/javascript">
    (function() { 
    "use strict";    

    const nonce = '<?php echo wp_create_nonce('dike_nonce') ?>',
          refused_licenses = JSON.parse(`<?php echo json_encode((array)get_site_option('dike_refused_licenses',array())) ?>`); 
    

    for(const e in refused_licenses){const n=refused_licenses[e].tok;let t=refused_licenses[e].reason;switch(t){case"token_not_found":t="token not found in the database";break;case"domain_not_matching":t="domain not matching";break;case"token_expired":t="expired token";break;case"envato_lic_not_found":case"lic_not_found":t="shop license not found"}if(t.trim()&&(t="("+t+")"),!document.querySelector('.dike_prod[data-prod-slug="'+e+'"] .dike_insert_token_form fieldset'))break;document.querySelector('.dike_prod[data-prod-slug="'+e+'"] .dike_insert_token_form fieldset').insertAdjacentHTML("afterend",`<div class="dike_refused_license">\n                <span>\n                    <em>${n}</em><br/>\n                    <strong>Token refused <small>${t}</small></strong>\n                </span>\n                <input type="button" value="Use new token" class="button-secondary" />\n            </div>`)}document.querySelectorAll(".dike_refused_license input").forEach((function(e){e.addEventListener("click",(e=>{e.target.parentNode.remove()}))}));
    
    
    
    const reset_dike_url = () => {
        window.history.replaceState(null, null, "<?php echo $dashboard_url  ?>");        
    };
    
    
    
    -1!==window.location.href.indexOf("&refresh_licenses")&&reset_dike_url(),document.querySelectorAll(".dike_prod form").forEach((function(e){e.addEventListener("submit",(function(e){return e.preventDefault(),!1}))})),document.querySelectorAll('input[name="dike_prod_token"]').forEach((function(e){e.onkeyup=n=>{if(13===n.keyCode||13===n.which)return n.preventDefault(),e.parentNode.querySelector('input[name="dike_prod_activation_btn"]').click(),!1}}));
    

    document.querySelectorAll('input[name="dike_prod_activation_btn"]').forEach(function(el) {
        el.addEventListener('click', function(e) {
        
            const $btn      = e.target,
                  $wrap     = recursive_parent($btn, '.dike_prod'),
                  prod_slug = $wrap.getAttribute('data-prod-slug'),
                  $fieldset = $wrap.querySelector('fieldset'), 
                  token     = $wrap.querySelector('input[name="dike_prod_token"]').value.trim(),
                  purch_code= $wrap.querySelector('input[name="dike_purch_code"]').value.trim(),
                  username  = $wrap.querySelector('input[name="dike_username"]').value.trim();
            
            if(!token || $btn.classList.contains('dike_blinking_btn')) {
                return false;    
            }
            
            if(token.length != 32) {
                lc_wp_popup_message('error', "Validation token must be 32 characters long");
                return false;    
            }
            if(purch_code || username) {
                if(purch_code.length != 36) {
                    lc_wp_popup_message('error', "Purchase code must be 36 characters long");
                    return false;    
                }
                else if(!username) {
                    lc_wp_popup_message('error', "Username required for offline token");
                    return false;    
                }
            }
            
            $fieldset.disabled = true;
            $btn.classList.add('dike_blinking_btn');
            
            const crypted_params = encrypt_string(JSON.stringify({
                prod_slug   : prod_slug,
                domain      : window.location.hostname.replace('www.', '').replace('WWW.', ''),
                token       : token,
                purch_code  : purch_code,
                username    : username,
                unicum      : Math.random(),
            }));
            
            
            fetch(ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cache-Control': 'no-cache',
                },
                body: new URLSearchParams({
                    action      : 'dike_validate_domain_token',
                    author      : '<?php echo esc_attr($author) ?>',
                    data        : crypted_params,
                    dike_nonce : nonce,
                })
            })
            .then(response => response.json())
            .then(function(resp) {

                if(resp.status == 'error') {
                    lc_wp_popup_message('error', resp.message);    
                }
                else {
                    lc_wp_popup_message('success', resp.message);      
                    setTimeout(() => {
                        window.location.href = '<?php echo admin_url('index.php?page=dike_wpd&author='.$author) ?>';
                    }, 1150);
                }
                
                $fieldset.disabled = false;
                $btn.classList.remove('dike_blinking_btn');
            })
            .catch(function (err) {
                console.error(err);
                lc_wp_popup_message('error', 'Error performing the operation. Please try again');
                
                $fieldset.disabled = false;
                $btn.classList.remove('dike_blinking_btn');
            });
        });
    });
    
    
    
    document.querySelectorAll(".dike_offline_valid").forEach((function(e){e.addEventListener("click",(function(e){const t=recursive_parent(e.target,".dike_prod");t.classList.add("dike_purch_code_f_shown"),t.querySelector('input[name="dike_purch_code"]').classList.remove("dike_displaynone"),t.querySelector('input[name="dike_username"]').classList.remove("dike_displaynone"),e.target.remove()}))})),document.querySelectorAll('input[name="dike_change_token_btn"]').forEach((function(e){e.addEventListener("click",(function(e){const t=recursive_parent(e.target,".dike_prod");t.querySelector(".dike_inserted_token_form").remove(),t.querySelector(".dike_insert_token_form").classList.remove("dike_displaynone"),t.querySelector(".dike_offline_valid").style.marginBottom="21px"}))}));const encrypt_string=function(e){let t="";for(let n=0;n<e.length;n++){let i=88^e.charCodeAt(n);t+=String.fromCharCode(i)}return t};document.addEventListener("DOMContentLoaded",(function(e){setTimeout((()=>{document.querySelector(".dike_author_notice .notice-dismiss")&&document.querySelector(".dike_author_notice .notice-dismiss").addEventListener("click",(function(e){const t="dike_notice_"+document.querySelector(".dike_author_notice").getAttribute("data-notice-id")+"_dismissed",n=new Date;n.setTime(n.getTime()+5184e6);let i="expires="+n.toUTCString();document.cookie=t+"=1;"+i+";path=/"}))}),1700)}));

    
    const modal_closing_code = '<span class="dashicons dashicons-no-alt dike_close_modal"></span>';
    let changelog_modal_intval = null;
    

    document.addEventListener("DOMContentLoaded", function(event) {
        document.querySelectorAll('.dike_changelog_trigger').forEach(function(el) {
            
            el.addEventListener('click', function(e) {
                const slug          = recursive_parent(e.target, '.dike_prod').getAttribute('data-prod-slug'),
                      changelog_url = recursive_parent(e.target, '.dike_prod').getAttribute('data-prod-changelog'),
                      on_upd_prod   = '<?php echo(isset($_GET['prod_updated']))?$_GET['prod_cl']:'' ?>',
                      
                      prod_name = document.querySelector('.dike_prod[data-prod-slug="'+ slug +'"] h4').innerText.trim(),
                      prod_img  = "<img src='"+ document.querySelector('.dike_prod[data-prod-slug="'+ slug +'"] span img').getAttribute('src') +"' class='dike_changelog_img' />";
                
                
                let pre = 
                    '<div class="dike_cl_popup_head">';

                if(on_upd_prod == slug) {
                    pre += `
                    ${ prod_img }
                    <div>
                        <h3 class="dike_changelog_title">${ prod_name } successfully updated!</h3>
                        <h4 class="dike_changelog_title">In the following changelog you can see what changed:</h4>
                    </div>`;
                }
                
                else {
                    pre += prod_img +'<h3 class="dike_changelog_title">'+ prod_name +' changelog</h3>';
                }

                pre += '</div>';
                
                
                let code = `
                <div class="dike_modal_contents">
                    ${ pre }
                    <h2 class="dike_modal_loader">
                        <img src="<?php echo admin_url() ?>/images/spinner-2x.gif" alt="loading" />    
                    </h2>
                    <iframe src="${ changelog_url }" onload="window.dike_hide_modal_loader()"></iframe>
                </div>`;

                
                lc_wp_popup_message('modal', code + modal_closing_code);
                document.body.style.overflow = 'hidden';
                
                document.querySelector('.lcwpm_modal').classList.add('dike_changelog_wrap');
                
                document.querySelector('.dike_close_modal').addEventListener('click', function(e) {
                    close_lc_popup_modal();       
                });
                
                
                changelog_modal_intval = setInterval(() => {
                    if(document.querySelector('.dike_cl_popup_head')) {
                        
                        const title_h = document.querySelector('.dike_cl_popup_head').offsetHeight;
                        document.querySelector('.dike_changelog_wrap iframe').style.height = 'calc(100% - '+ title_h +'px)';
                    }
                }, 100);
            });
        });
        
        
        
        window.dike_hide_modal_loader = () => {
            document.querySelector('.dike_modal_loader').remove();        
        };<?php if(isset($_GET['prod_cl'])): ?>const spc_trig_selector = document.querySelector('.dike_prod[data-prod-slug="<?php echo esc_attr($_GET['prod_cl']) ?>"] .dike_changelog_trigger');
        if(spc_trig_selector) {
            spc_trig_selector.click();   
            reset_dike_url();
        }<?php endif; ?>});
        
    
    document.querySelectorAll('.dike_how_get_token').forEach(function(el) {   
        el.addEventListener('click', function(e) {
            
            const contents = `<?php echo $author_data['how_to_get_token_txt'] ?>`;
            
            lc_wp_popup_message('modal', contents + modal_closing_code);
            document.body.style.overflow = 'hidden';

            document.querySelector('.dike_close_modal').addEventListener('click', function(e) {
                close_lc_popup_modal();       
            });
        });
    });<?php if(!empty($failed_prod_lic_check_code)): ?>// product failed license check - popup a warning
    document.addEventListener("DOMContentLoaded", function(event) {
        lc_wp_popup_message('error', `<?php echo $failed_prod_lic_check_code  ?>`);
        reset_dike_url();
    });<?php endif; ?>
    document.querySelectorAll('.dike_lic_hub_lb').forEach(function(el) {   
        el.addEventListener('click', function(e) {
            e.preventDefault();
            
            const key = encodeURIComponent('Dht2pVqNcSoxdL/x7/ssq5JnFuFfCp6soX30CTsgwI5fNctopHx08o4zQH0G7xIPyVk6+C3kXuX2BSQp1VuUS+3xxXZ9LLd+MyrjXyqhAZUeCyALzOjVaxC24NarTVaXtg==');
            
            let code = `
            <div class="dike_modal_contents">
                <iframe src="https://<?php echo $author  ?>.dikelicensing.com?iframed=${ key }" onload="window.dike_hide_modal_loader()" class="dike_lic_hub_iframe"></iframe>
            </div>`;


            lc_wp_popup_message('modal', code + modal_closing_code);
            document.body.style.overflow = 'hidden';
            
            document.querySelector('.lcwpm_modal').classList.add('dike_lic_hub_iframe_wrap');

            document.querySelector('.dike_close_modal').addEventListener('click', function(e) {
                close_lc_popup_modal();       
            });
        });
    });
       
    

    document.onkeydown=function(e){let o=!1;o="key"in(e=e||window.event)?"Escape"===e.key||"Esc"===e.key:27===e.keyCode,o&&document.querySelector(".lcwpm_modal")&&close_lc_popup_modal()};const close_lc_popup_modal=()=>{window.lcwpm_close(),document.body.style.overflow="auto",changelog_modal_intval&&clearInterval(changelog_modal_intval)};document.addEventListener("DOMContentLoaded",(()=>{document.querySelectorAll(".dike_tooltip").forEach((function(e){e.addEventListener("click",(function(){e.classList.toggle("dike_tooltip_shown")})),e.addEventListener("mouseenter",(function(){e.classList.add("dike_tooltip_shown")})),e.addEventListener("mouseleave",(function(){e.classList.remove("dike_tooltip_shown")}))})),window.addEventListener("resize",(function(){document.querySelectorAll(".dike_tooltip_shown").forEach((function(e){e.classList.remove("dike_tooltip_shown")}))}))}));
    

    const wpm_sel_trick =  document.querySelector('.wp-submenu a[href="index.php?page=dike_wpd&author=<?php echo $_GET['author'] ?>"]');
    if(wpm_sel_trick) {
        wpm_sel_trick.classList.add('current');
        wpm_sel_trick.parentNode.classList.add('current');
    }
    
    const recursive_parent=(e,r)=>{let t=e;for(;null!=t.parentNode&&!t.matches(r);)t=t.parentNode;return t};
    
    
    document.addEventListener('DOMContentLoaded', () => {
        const $target = document.getElementById('footer-upgrade');
        
        if($target) {
            $target.innerText = '<?php echo esc_attr($author_data['menu_label']).' ' ?>v<?php echo $GLOBALS['dike_registr'] ?>';               
        }
    });
    
})();</script>