<?php
// TOOLSET TO DISPLAY AND MANAGE SETTINGS
// v1.5.6 - 22/03/2024

class mg_settings_engine {
    
    private static $prod_acronym = 'lcmg'; // (string)
	public static  $css_prefix = 'mg_'; // (string) prefix added to classes for customized styling
	public static  $ml_key = 'mg_ml'; // (string) multilanguage key
	
    
    ////////////////////////////////////////////////////////////////
    
    
    public $baseurl     = ''; // (string) settings page baseurl 
	public $submit_btn_name	= ''; // (string) submit btn name attribute value
	public $tabs 		= array(); // (array) multidimensional array composed by setting tabs (section id => tab name)
	public $structure	= array(); // (array) multidimensional array composed by sections and inner fields
	public $no_export_opts = array(); // (array) option keys array that shoulud not be included in the export array (eg. fields bound to WP IDs)
    
	protected $fields	= array(); // (array) associative array wrapping fields data from $structure
	private $js_vis_cond= array(); // (array) multidimensional array (field_id => condition's params) containing conditions to toggle fields. To be managed by js_vis_code()
	
	public $errors 			= ''; // (string) form validation errors (HTML code)
	public $form_data 		= array(); // (array) containing form's data (associative array(field_name => value))
    
	
	
	/* INIT - setup tabs and filter */ 
	public function __construct($submit_btn_name, $tabs, $structure) {
		
		$this->submit_btn_name 	= $submit_btn_name;
		$this->tabs 		= $tabs;
		$this->structure 	= $structure;
		
        // set baseurl
        $uri = explode('wp-admin/', $_SERVER['REQUEST_URI'])[1];
        $clean_url = explode('?', admin_url($uri))[0]; 
        
        $this->baseurl = add_query_arg(array('page' => $_GET['page']), $clean_url);
        if(isset($_GET['post_type'])) {
            $this->baseurl = add_query_arg(array('post_type' => $_GET['post_type']), $this->baseurl);        
        }
        
		
		// store fields
		foreach($structure as $sections) {
			foreach($sections as $section) {
				foreach($section as $sid => $sval) {
					if($sid == 'fields') {
						foreach($sval as $field_id => $fvals) {
							$this->fields[$field_id] = $fvals;	
						}
					}
				}
			}
		}
		
		
        // is importing?
        if(isset($_GET['lcwp_sf_import'])) {
            $this->handle_import_data();        
        }
        
        
		// form submitted - validate 
		if($this->form_submitted()) {
			$this->validate();
		}
		
		
		// form not submitted - fill $form_data with database
		else {
            // cache vals with a single DB call (WP 6.4>)
            if(function_exists('get_options')) {
                get_options($this->get_opts_array());
            }
            
			// use validation indexes
			foreach($this->get_fields_validation() as $fv) {
				$fid = $fv['index'];
				
				$def = (isset($fields[$fid]['def'])) ? $fields[$fid]['def'] : false;
				$this->form_data[$fid] = get_option($fid, $def);
			}
		}
	}
	
	
	/* know if form has been submitted and "simple_form_validator" class exists - return bool */
	public function form_submitted() {
		return (class_exists('simple_fv') && isset($_POST[ $this->submit_btn_name ])) ? true : false;
	}
	


	/* print settings code (tabs + fields) */
	public function get_code() {
		$form_action = str_replace(array('%7E', '&lcwp_sf_success'), array('~', ''), $_SERVER['REQUEST_URI']);

		echo '
        <form name="'. self::$css_prefix .'settings_form" method="post" class="lcwp_settings_form '. self::$css_prefix .'settings_form form-wrap" action="'. $form_action .'" novalidate>';
		
			// tabs
			echo $this->tabs_code();
			
			// sections and fields
			foreach($this->tabs as $tab_id => $tab_name) {
				if(!isset($this->structure[ $tab_id ])) {continue;}
				
				echo '<div id="'. $tab_id .'" class="lcwp_settings_block '. self::$css_prefix .'settings_block">';
				
				foreach($this->structure[ $tab_id ] as $sect_id => $section) {
					if(empty($section['fields'])) {
                        continue;
                    }
					
                    if($section['sect_name']) {
					   echo '<h3 class="lcwp_settings_sect_title '. self::$css_prefix .'settings_sect_title" id="'. $sect_id .'">'. $section['sect_name'] .'</h3>';
                    }
					
					// init table only if has normal fields
					$use_table = false;
					foreach($section['fields'] as $field_id => $f) {
						if($f['type'] != 'custom') {
							$use_table = true;
							break;	
						}
					}
					
					if($use_table) {
						echo '<table class="widefat lcwp_settings_table '. self::$css_prefix .'settings_block"><tbody>';
					}
					
						// fields code
						foreach($section['fields'] as $field_id => $f) {
							$this->opt_to_code($field_id, $f);
						}
					
					if($use_table) {
						echo '</tbody></table>';
					}
				}
					
				echo '</div>';
			}
			
			
			// javascript visibility toggle code
			$this->js_vis_code();
			
			
			// nonce & submit button
			echo '
			<input type="hidden" name="'. self::$css_prefix .'settings_nonce" value="'. wp_create_nonce('lcwp') .'" /> 
			<input type="submit" name="'. $this->submit_btn_name .'" value="'. esc_attr__('Update Options', self::$ml_key) .'" class="button-primary lcwp_settings_submit" />
			
		</form>';
	}
	
	
		
	/**************************************************************/
		
		
		
	/* tabs code */
	protected function tabs_code() {
		$code = '<h2 class="nav-tab-wrapper lcwp_settings_tabs '.self::$css_prefix.'settings_tabs">';
		
		foreach($this->tabs as $i => $v) {
			$code .= '<a class="nav-tab" href="#'. $i .'">'. $v .'</a>';		
		}
		return $code .'</h2>';
	}
		
		
		
	/* Passing field id and field's data array, returns its code basing on type */ 	
	public function opt_to_code($field_id, $field) {	
		$f = $field;
		
		// set field value
		if(!isset($this->form_data[$field_id])) {
			$val = '';	
		}
		else if(isset($this->form_data[$field_id]) && $this->form_data[$field_id] !== false || ($this->form_data[$field_id] === false && !isset($f['def']))) {
			$val = $this->form_data[$field_id];	
		} else {
			$val = (isset($f['def'])) ? $f['def'] : ''; 	
		}

		
		// CUSTOM FIELD - call external function
		if($f['type'] == 'custom') {
			
			// store js visibility params
			if(isset($f['js_vis'])) {
				$this->js_vis_cond[$field_id] = $f['js_vis'];	
			}
			
            if(isset($f['callback'])) {
                if(is_array($f['callback']) && count($f['callback']) < 2) {
                    return 'Class method callback - invalid callback format';
                }
                elseif(strpos($f['callback'], '::') !== false) {
                    $f['callback'] = explode('::', $f['callback']);   
                }
                
                return call_user_func($f['callback'], $field_id, $f, $val, $this->form_data);
            }	
		}
		
		
		//////
		
		
		// SPACER
		if($f['type'] == 'spacer') {
			$tr_hidden = (isset($f['hide']) && $f['hide']) ? 'lcwp_sf_displaynone' : '';
			echo '<tr class="'. self::$css_prefix . $field_id .' '. $tr_hidden .'"><td class="lcwp_sf_spacer" data-field-id="'. $field_id .'" colspan="3"></td></tr>';
			
			// store js visibility params
			if(isset($f['js_vis'])) {
				$this->js_vis_cond[$field_id] = $f['js_vis'];	
			}
			return true;
		}
		
		// MESSAGE
		if($f['type'] == 'message') {
			$tr_hidden = (isset($f['hide']) && $f['hide']) ? 'lcwp_sf_displaynone' : '';
			echo '<tr class="'. self::$css_prefix . $field_id .' '. $tr_hidden .'"><td class="lcwp_sf_message" data-field-id="'. $field_id .'" colspan="3">'. $f['content'] .'</td></tr>';
			
			// store js visibility params
			if(isset($f['js_vis'])) {
				$this->js_vis_cond[$field_id] = $f['js_vis'];	
			}
			return true;
		}
		
		// LABEL + MESSAGE
		if($f['type'] == 'label_message') {
			$tr_hidden = (isset($f['hide']) && $f['hide']) ? 'lcwp_sf_displaynone' : '';
			echo '
			<tr class="'. self::$css_prefix . $field_id .' '. $tr_hidden .'" '.$tr_hidden.'>
				<td class="lcwp_sf_label"><label>'. $f['label'] .'</label></td>
				<td class="lcwp_sf_message" data-field-id="'. $field_id .'" colspan="2">'. $f['content'] .'</td>
			</tr>';
			
			// store js visibility params
			if(isset($f['js_vis'])) {
				$this->js_vis_cond[$field_id] = $f['js_vis'];	
			}
			return true;
		}
		
		
		//////
		
		$tr_hidden = (isset($f['hide']) && $f['hide']) ? 'lcwp_sf_displaynone' : '';
		echo '<tr class="'. self::$css_prefix . $field_id .' '. $tr_hidden .'" '.$tr_hidden.'>';
		
		
		// if code editor - fill whole space
		if($f['type'] == 'code_editor') {
			echo '<td class="lcwp_sf_field" colspan="3">'; 	
			$is_fullwidth = true;
		}
		else {
		
			// label block
			echo '<td class="lcwp_sf_label"><label>'. $f['label'] .'</label></td>';
			
			// field - start
			$is_fullwidth = ((isset($f['fullwidth']) && $f['fullwidth']) || $f['type'] == 'textarea' || $f['type'] == 'wp_editor') ? true : false;
			echo ($is_fullwidth) ? '<td class="lcwp_sf_field" colspan="2">' : '<td class="lcwp_sf_field">';
		}
		
		
		
		// switch by type
		switch($f['type']) {
			
			// text
			case 'text' :
				$ph = (isset($f['placeh'])) ? $f['placeh'] : ''; 
				$ml = (isset($f['max_val_len'])) ? 'maxlength="'. (int)$f['max_val_len'] .'"' : '';
				
				echo '<input type="text" name="'. esc_attr($field_id) .'" value="'. esc_attr((string)$val) .'" '.$ml.' placeholder="'. esc_attr($ph) .'" autocomplete="off" />';
				break;
				
                
			// password
			case 'psw' :
				
				// if value exists - show a message and an hidden field + system to change val
				if(!empty($val)) {
					echo '
                    <div class="lcwp_sf_edit_psw">
						<span>'. esc_html__('Password already set!', self::$ml_key) .'</span>
						<a href="javascript:void(0)" rel="'. esc_attr($field_id) .'" title="'. esc_html__('change password', self::$ml_key) .'"><span class="dashicons dashicons-edit"></span></a>
						<input type="hidden" name="'. esc_attr($field_id) .'" value="|||lcwp_sf_psw_placeh|||" />
					</div>';
					
					if(!isset($GLOBALS['lcwp_sf_edit_psw_js'])) {
						$GLOBALS['lcwp_sf_edit_psw_js'] = true;
						
						?>
                        <script type="text/javascript">
                        (function($){
                            "use strict";
                            
                        	$(document).on('click', '.lcwp_sf_edit_psw a', function() {
								 $(this).parents('.lcwp_sf_edit_psw').replaceWith('<input type="password" name="'+ $(this).attr('rel') +'" value="" autocomplete="off" />');
							});  
                        })(jQuery);
						</script>
                        <?php	
					}
				}
				else {
					echo '<input type="password" name="'. $field_id .'" value="" autocomplete="off" />';
				}
					
				break;	
				
                
			// select
			case 'select' :
				$multiple_attr = (isset($f['multiple']) && $f['multiple']) ? 'multiple="multiple"' : '';
				$multiple_name = (isset($f['multiple']) && $f['multiple']) ? '[]' : '';
				
				echo '
				<select data-placeholder="'. esc_attr__('Select an option', self::$ml_key) .' .." name="'. esc_attr($field_id) . $multiple_name.'" class="lcwp_sf_select" autocomplete="off" '. $multiple_attr .'>';
				
				foreach((array)$f['val'] as $key => $name) {
					if(isset($f['multiple']) && $f['multiple']) {
						$sel = (in_array($key, (array)$val)) ? 'selected="selected"' : '';
					} else {
						$sel = ($key == (string)$val) ? 'selected="selected"' : '';
					}
					
					echo '<option value="'. esc_attr($key) .'" '.$sel.'>'. esc_html($name) .'</option>';	
				}
				
				echo '</select>';
				break;
			
                
			// checkbox
			case 'checkbox' :
				$sel = ($val) ? 'checked="checked"' : '';
				echo '
				<input type="checkbox" name="'. esc_attr($field_id) .'" value="1" class="lcwp_sf_check" '.$sel.' autocomplete="off" />';
				break;
			
                
			// textarea
			case 'textarea' :
				$ph = (isset($f['placeh'])) ? $f['placeh'] : ''; 
				echo '
				<textarea name="'. $field_id .'" placeholder="'. esc_attr($ph) .'" class="lcwp_sf_textarea" onkeyup="lcwp_sf_textAreaAdjust(this)" autocomplete="off">'. esc_textarea($val) .'</textarea>';
				break;
			
                
			// code editor
			case 'code_editor' :
				echo '
				<textarea id="'. esc_attr($field_id) .'" name="'. esc_attr($field_id) .'" autocomplete="off" class="lcwp_sf_code_editor" data-language="'. $f['language'] .'">'. esc_textarea($val) .'</textarea>';
				break;	
				
                
			// wp editor
			case 'wp_editor' :
				$args = array('textarea_rows'=> $f['rows']);
				wp_editor($val, $field_id, $args);
				break;	
			
                
			// number slider
			case 'slider' :
				if(!isset($f['value'])) {
                    $f['value'] = '';
                }
				$respect_limits = (!isset($f['respect_limits']) || !$f['respect_limits']) ? 0 : 1;
                
				echo '
            	<input type="number" value="'. (float)$val .'" name="'. esc_attr($field_id) .'" min="'. esc_attr($f['min_val']) .'" max="'. esc_attr($f['max_val']) .'" step="'. esc_attr($f['step']) .'" class="lcwp_sf_slider_input" autocomplete="off" data-unit="'. esc_attr($f['value']) .'" data-respect-limits="'. $respect_limits .'" />';
				break;
			
                
			// color
			case 'color' :
                $modes = (isset($f['extra_modes']) && is_array($f['extra_modes'])) ? $f['extra_modes'] : array(); // specific modes classes
                $def_color = (isset($f['def'])) ? $f['def'] : '#999999';
                
                echo '
				<input type="text" name="'. esc_attr($field_id) .'" value="'. esc_attr($val) .'" class="lcwp_sf_colpick" data-modes="'. implode(' ', $modes) .'" data-def-color="'. esc_attr($def_color) .'" autocomplete="off" />';
				break;
			
                
			// value and type
			case 'val_n_type' :
				echo '
				<input type="text" class="lcwp_sf_slider_input lcwp_sf_vnt_slider_input" name="'. esc_attr($field_id) .'" value="'. esc_attr($val) .'" maxlength="'. esc_attr($f['max_val_len']) .'" autocomplete="off" />

				<select name="'. esc_attr($field_id) .'_type" class="lcwp_sf_vnt_select" autocomplete="off">';
					
					$sel = get_option($field_id .'_type');
					foreach($f['types'] as $i => $val) {
						echo '<option val="'. esc_attr($i) .'" '. selected($sel, $i) .'>'. esc_html($val) .'</option>';	
					}

				echo '
				</select>';
				break;
			
                
			// 2 numbers
			case '2_numbers' :
				if(!is_array($val) || count($val) != 2) {
                    $val = $f['def'];
                }
				
				$min    = (isset($f['min_val'])) ? 'min="'. (int)$f['min_val'] .'"' : '';
				$max    = (isset($f['max_val'])) ? 'max="'. (int)$f['max_val'] .'"' : '';
				
				for($a=0; $a<2; $a++) {
					echo '<input type="number" name="'. esc_attr($field_id) .'[]" value="'. (int)$val[$a] .'" '.$min.' '.$max.' class="lcwp_sf_2num_input"  autocomplete="off" />' ;	
				}
				
				if(isset($f['value'])) {
					echo ' <span>'. $f['value'] .'</span>';
				}
				break;
				
                
			// 4 numbers
			case '4_numbers' :
				if(!is_array($val) || count($val) != 4) {
                    $val = $f['def'];
                }
				
				$min    = (isset($f['min_val'])) ? 'min="'. (int)$f['min_val'] .'"' : '';
				$max    = (isset($f['max_val'])) ? 'max="'. (int)$f['max_val'] .'"' : '';
				
				for($a=0; $a<4; $a++) {
					echo '<input type="text" name="'. esc_attr($field_id) .'[]" value="'. (int)$val[$a] .'" '.$min.' '.$max.' class="lcwp_sf_4num_input" autocomplete="off" />' ;	
				}
				
				if(isset($f['value'])) {
					echo ' <span>'. $f['value'] .'</span>';
				}
				break;
		}
		
		
		
		// has note?
		$note = '';
		if(isset($f['note']) && $f['note']) {
			$note = ($is_fullwidth) ? '<p class="lcwp_sf_note">'. $f['note'] .'</p>' : '<span class="lcwp_sf_note">'. $f['note'] .'</span>';	
		}

		// fullwidth or textarea or wp editor 
		if($is_fullwidth) {
			echo '
				'. $note .'
			</td>';	
		}
		else {
			echo '
			</td>
			<td>'. $note .'</td>';	
		}
		
		echo '</tr>';
		
		
		// store js visibility params
		if(isset($f['js_vis'])) {
			$this->js_vis_cond[$field_id] = $f['js_vis'];	
		}
	}
		
		
		
	
	/* 
	 * Handling js_vis_cond data, prints javascript code to dynamically hide fields basing on other ones 
	 * 
	 * $this->js_vis_cond elements structure: 
	 	field_id => array(
		  'linked_field' 	=> (string) the field ID (name attr) to match,
		  'value'			=> (bool|string|array) boolean if is a checkbox or array to match in_array() or a specific value
		) 
	 */
	private function js_vis_code() {	
		if(empty($this->js_vis_cond)) {return false;}
		
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function() {';
		
		foreach($this->js_vis_cond as $field_id => $data) : 
			$data['field_id'] = $field_id;
		?>	
          
			jQuery(document).on(
         		'change lcs-statuschange', 
          		'[name=<?php echo $data['linked_field'] ?>], [name="<?php echo $data['linked_field'] ?>[]"]', 
				<?php echo json_encode($data); ?>,
            	function(e) {
                  	let $linked = jQuery(this),
                        $field_wrap = jQuery('[name='+ e.data.field_id +'], [name="'+ e.data.field_id +'[]"], [data-field-id="'+ e.data.field_id +'"]').parents('tr'),
                        operator = (typeof(e.data.operator) == "undefined" || e.data.operator == "=") ? "equal" : "different", 

                        show = true;
                    
                    switch( typeof(e.data.condition) ) {
                    	case 'boolean' :
                        	if(
                            	(e.data.condition && !$linked.is(':checked')) ||
                                (!e.data.condition && $linked.is(':checked'))
                            ) {
                            	show = false;
                            }
                        	break;
                    
                    	case 'object' :
                        	if(jQuery.inArray( $linked.val(), e.data.condition ) === -1) {
                                show = (operator == "equal") ? false : true;
                            }
                            else {
                                show = (operator == "equal") ? true : false;   
                            }
                        	break;
                            
                        default :
                        	if(e.data.condition != $linked.val()) {
                            	show = (operator == "equal") ? false : true;
                            }
                            else {
                                show = (operator == "equal") ? true : false;    
                            }
                        	break;
                    } 
                    
                    (show) ? $field_wrap.removeClass('lcwp_sf_displaynone') : $field_wrap.addClass('lcwp_sf_displaynone');         		
       			}
			);
            
            // trigger on page's opening
            jQuery('[name=<?php echo $data['linked_field'] ?>]').trigger('change').trigger('lcs-statuschange');
            	
		<?php
		endforeach;
		
		echo '
			});
		</script>';
	}
		
		
		
	///////////////////////////////////////////////////////////////////////////////////	
	
	
	
	/* 
	 * get validation indexes for stored fields
	 * @return (array)
	 */
	private function get_fields_validation() {
		$indexes = array();
		$a = 0;
		
		foreach($this->fields as $fid => $fval) {
			if($fval['type'] == 'spacer' || $fval['type'] == 'message' || $fval['type'] == 'label_message') {continue;}
			if(isset($fval['no_save'])) {continue;}

			// custom field - manual index addition
			if($fval['type'] == 'custom') {
				if(!isset($fval['validation']) || !is_array($fval['validation'])) {
                    continue;
                }
				
				// allow multi-custom indexes
				if(isset($fval['validation'][0])) {
					foreach($fval['validation'] as $cust_val) {
						$indexes[$a] = $cust_val;
						$a++;	
					}
				}
				else {
					$indexes[$a] = $fval['validation'];
					$a++;			
				}
			}
			
			// dinamically create index
			else {
				$indexes[$a] = array();
				$indexes[$a]['index'] = $fid;
				$indexes[$a]['label'] = $fval['label'];
				
				// required
				if(isset($fval['required']) && $fval['required']) {
					$indexes[$a]['required'] = true;
				}
				
				// min-length
				if(isset($fval['minlen'])) {
					$indexes[$a]['min_len'] = $fval['minlen'];
				}
				
				// max-lenght
				if(isset($fval['maxlen'])) {
					$indexes[$a]['max_len'] = $fval['maxlen'];
				}
				
				// color field
				if($fval['type'] == 'color' && (!isset($fval['extra_modes']) || empty($fval['extra_modes']))) {
					$indexes[$a]['type'] = 'hex';
				}
				
				// specific types
				if(isset($fval['subtype'])) {
					$indexes[$a]['type'] = $fval['subtype'];
				}
		
				// numeric value range
				if(
                    (
                        ($fval['type'] == 'text' && isset($fval['subtype']) && in_array($fval['subtype'], array('int', 'float'))) ||  
                        $fval['type'] == 'val_n_type'
					) && 
					isset($fval['range_from']) && $fval['range_from'] !== '') 
				{	
					$indexes[$a]['min_val'] = (float)$fval['range_from'];
					$indexes[$a]['max_val'] = (float)$fval['range_to'];
				}
                
                // numeric value range for slider + input
				if($fval['type'] == 'slider' && isset($fval['respect_limits']) && $fval['respect_limits']) {	
					$indexes[$a]['min_val'] = (float)$fval['min_val'];
					$indexes[$a]['max_val'] = (float)$fval['max_val'];
				}
				
				// regex validation
				if(isset($fval['regex'])) {
					$indexes[$a]['preg_match'] = $fval['regex'];			
				}	
				
				$a++;
			}
			

			// special cases
			if($fval['type'] == 'val_n_type') {
				$indexes[$a] = array('index'=>$fid.'_type', 'label'=>$fval['label'].' type');	
				$a++;	
			}
		}
		
		return $indexes;
	}
	
	

	/* 
	 * Validate fields - stores errors in $errors and fetched data in $form_data
	 * "simple_form_validator" class must be included 
	 *
	 * @return (bool) true if no errors 
	 */
	public function validate() {
		$indexes = $this->get_fields_validation();
		
		$validator = new simple_fv;
		$validator->formHandle($indexes);
		
		$fdata = $validator->form_val;
		$this->errors = $validator->getErrors('array');	
		if(!$this->errors) {
            $this->errors = array();
        }
		
		
		// check nonce
		$noncename = self::$css_prefix .'settings_nonce';
		if(!isset($_POST[$noncename]) || !wp_verify_nonce($_POST[$noncename], 'lcwp')) {
			$this->errors = array('Cheating?');	
		}
		
		
		// clean data and save options
		foreach($fdata as $key => $val) {
			if(!is_array($val)) {
				$fdata[$key] = stripslashes($val);
			} else {
				$fdata[$key] = array();
				foreach($val as $arr_val) {
					$fdata[$key][] = (is_array($arr_val)) ? $arr_val : stripslashes($arr_val);
				}
			}
		}
		
		$this->form_data = $fdata;
		return (empty($this->errors)) ? true : false;
	}
	
	
	
	/* Save form data 
	 * @return (bool)
	 */	
	public function save_data() {
		foreach($this->form_data as $key => $val) {
			
			// skip if is a password placeholder
			if($val == '|||lcwp_sf_psw_placeh|||') {
                continue;
            }
            
            $f_type = (isset($this->fields[$key]) && isset($this->fields[$key]['type'])) ? $this->fields[$key]['type'] : false;
            switch($f_type) {
                
                case 'checkbox' :
                    $val = (!in_array($val, array(false, 1))) ? false : $val;
                    break; 
                    
                case 'text' :
                    $val = sanitize_text_field($val);
                    break;
                    
                case 'textarea' :
                    $val = sanitize_textarea_field($val);
                    break;
                    
                case 'code_editor' :
                case 'wp_editor' :
                    $val = wp_kses_post($val);
                    $val = str_replace(array('&gt;', '&lt;'), array('>', '<'), $val);
                    break;
                    
                default :
                    $val = (is_array($val)) ? $this->recursive_wp_kses_post($val) : wp_kses_post($val);
                    break;
            }
            
			($val === false) ? delete_option($key) : update_option($key, $val, false);
		}
	}
    
    
    
    /* applies wp_kses_post() recursively in case of array data */
    private function recursive_wp_kses_post($data) {
        foreach($data as $k => $v) {
            $data[$k] = (is_array($v)) ? $this->recursive_wp_kses_post($v) : wp_kses_post($v);
        }
        return $data;
    }
    
    
    
    /* 
     * Return error mesagge HTML 
     * @param (array) $errors - errors array could be populated by only error values or also specify an error subject as array element key
     */
    public function get_error_message_html($errors) {
        $err_elems = array();
        foreach($errors as $i => $v) {
            if(is_numeric($i)) {
                $err_elems[] = $v;	
            }
            else {
                $err_elems[] = $i .' - '. $v;	
            }
        }

        return '
        <div class="error lcwp_settings_result lcwp_sf_displaynone">
            <ul>
                <li>'. implode('</li><li>', $err_elems) .'</li>
            </ul>
        </div>';	    
    }
    
    
    
    /* Performs redirect after successful settings save */
    public function successful_save_redirect() {
        $redir_url = add_query_arg(array(
                'lcwp_sf_success' => ''
            ),
            $this->baseurl
        );
        
        if(isset($_GET['lcwp_sf_is_importing'])) {
            $redir_url .= '&lcwp_sf_is_importing';    
        }
        
        ob_end_clean(); // avoid issues with previously printed code
        wp_redirect($redir_url);
        wp_die();
    }
    
    
    
    /* Return success mesagge HTML */
    public function get_success_message_html() {
        if(isset($_GET['lcwp_sf_success'])) {
            $txt = (isset($_GET['lcwp_sf_is_importing'])) ? esc_html__('Options successfully imported!', self::$ml_key) : esc_html__('Options successfully saved!', self::$ml_key);
            
            return '
            <div class="updated lcwp_settings_result lcwp_sf_displaynone">
                <p>
                    <strong>'. $txt .'</strong>
                </p>
            </div>';	
        }
        
        return '';
    }
    
    
    
    /* Retrieves all the involved DB option names and return them as an array for smarter DB opeations */
    public function get_opts_array($cust_db_opt_keys = array()) {
        $names = (array)$cust_db_opt_keys;
        
        foreach($this->tabs as $tab_id => $tab_name) {
            foreach($this->structure[ $tab_id ] as $sect_id => $section) {  
                
				foreach($section['fields'] as $field_id => $f) {
                    if($f['type'] == 'spacer') {
                        continue;    
                    }
                    
                    if(in_array($f['type'], array('custom', 'message', 'label_message'))) {
                        if(isset($f['validation']) && is_array($f['validation'])) {
                            foreach($f['validation'] as $vr) {
                                if(isset($vr['index'])) {
                                    $names[] = (string)$vr['index'];     
                                }
                            }
                        }
                        
                        // allow custom keys declaration through structure param
                        if(isset($f['db_opt_keys']) && is_array($f['db_opt_keys'])) {
                            $names = array_merge($names, $f['db_opt_keys']);
                        }
                    }
                    else {
                        $names[] = $field_id;
                        
                        if($f['type'] == 'val_n_type') {
                            $names[] = $field_id .'_type';        
                        }
                    }
                }
            }
        }
        
        return array_unique($names);
    }
    
    
    
    /* Handle import data through URL attribute and set up $_POST data to use the settings engine */
    public function handle_import_data() {
        $data = json_decode(base64_decode($_GET['lcwp_sf_import']), true);
        $nonce_attr = self::$css_prefix .'settings_nonce';
        
        if(empty($data)) {
            echo $this->get_error_message_html(array(
                esc_html__('Import data not properly formed', self::$ml_key)
            ));
            return false;    
        }
        
        if(!isset($data['lcwp_sf_prod_acronym']) || $data['lcwp_sf_prod_acronym'] != self::$prod_acronym) {
            echo $this->get_error_message_html(array(
                esc_html__('Import data not related to this product', self::$ml_key)
            ));
            return false;    
        }
        
        $_POST[$this->submit_btn_name] = true;
        $_POST[$nonce_attr] = (isset($_GET[$nonce_attr])) ? $_GET[$nonce_attr] : false;     
        
        foreach($data as $key => $val) {
            if(in_array($key, array('lcwp_sf_prod_acronym', 'lcwp_sf_export_date'))) {
                continue;   
            }
            $_POST[$key] = $val;
        } 
    }
    
    
    
    /* Retrieving all options, compose a key>val array and returns a JSON string ready to be exported */
    public function get_export_json($cust_db_opt_keys = array()) {
        $opts = $this->get_opts_array($cust_db_opt_keys);
        
        $data = array(
            'lcwp_sf_prod_acronym'  => self::$prod_acronym,
            'lcwp_sf_export_date'   => gmdate('Y-m-d H:i:s'), 
        );
        foreach($opts as $opt_key) {
            if(in_array($opt_key, $this->no_export_opts)) {
                continue;
            }
            
            $data[$opt_key] = get_option($opt_key);    
        }
        
        return json_encode($data);
    }
        
    
    
    /* 
     * Returns the HTML code representing the export/import icons and related wizard. 
     * Ideally should be used in the H2 tag, representing the page's title 
     */
    public function import_export_btns($cust_db_opt_keys_to_export = array()) {
        $redir_url = add_query_arg(array(
                'lcwp_sf_import' => ''
            ),
            $this->baseurl
        );
        
        $code = '
        <span class="lcwp_sf_ie_btns_wrap">
            <i class="dashicons dashicons-database-export lcwp_sf_export_btn" title="'. esc_attr__('export settings', self::$ml_key) .'"></i>
            <i class="dashicons dashicons-database-import lcwp_sf_import_btn" title="'. esc_attr__('import settings', self::$ml_key) .'"></i>
        </span>
        
        <script type="text/javascript">
        (function($) { 
            "use strict";
        
            const import_baseurl = `'. $redir_url .'`;
        
        
            // export data
            $(document).on("click", ".lcwp_sf_export_btn", () => {
                const f = document.createElement("input");
                f.setAttribute("value", `'. base64_encode($this->get_export_json($cust_db_opt_keys_to_export)) .'`);
                document.body.appendChild(f);
                
                f.select();
                f.setSelectionRange(0, 9999999); // for mobile devices
                
                navigator.clipboard.writeText(f.value);
                document.body.removeChild(f);
                
                lc_wp_popup_message("success", `'. esc_html__("Export code successfully copied to your clipboard!", self::$ml_key) .'`);
            });
            
            
            // import data
            $(document).on("click", ".lcwp_sf_import_btn", () => {
                if(!confirm(`'. esc_html__('WARNING: this will override every matched option. Do you really want to import data?', self::$ml_key) .'`)) {
                    return false;    
                }
                
                let json_data = prompt(`'. esc_html__('Import data', self::$ml_key) .'`, "");
                if(json_data === null) {
                    return false;   
                }
                
                window.location.href = import_baseurl +"="+ encodeURIComponent(json_data.trim()) +"&lcwp_sf_is_importing&'. self::$css_prefix .'settings_nonce='. wp_create_nonce('lcwp') .'";
            });
            
            
        })(jQuery);
        </script>';
            
        return $code;    
    }
    
    
    
    
    ///////////////////////////////////////
    
    
    
    /* add array index after or before another one
     * @param (array) $to_inject = array(index => val)
     * @param (array) $array = array to alterate (eg. $structure['main_opts'])
     * @param (string) $what = target existing array key
     * @param (string) $where = before or after
     *
     * @return (array) new structure array 
     */
    public static function inject_array_elem($to_inject, $array, $what, $where = 'after') {
        $tot_elems = count($array);
        if(!$tot_elems) {return $to_inject;}

        $keys = array_keys($array);
        $pos = array_search($what, $keys); 
        if($pos === false) {return false;}

        $a = 0;
        $new_arr = array(); 
        foreach($array as $index => $val) {
            if($a == $pos && $where == 'before') {
                $new_arr = $new_arr + $to_inject;	
            }

            $new_arr[$index] = $val;

            if($a == $pos && $where == 'after') {
                $new_arr = $new_arr + $to_inject;	
            }

            $a++;
        }

        return $new_arr;
    }
}