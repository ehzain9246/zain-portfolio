<?php
/**
 * Simple Form Validator - PHP class to speed up the boring process of form validation
 * NOTE: messages ready for WordPress localization
 * 
 * @author Luca Montanari
 * @copyright 2023 Luca Montanari - https://lcweb.it
 * @version 1.1.0 - 14/02/2023
 */

if(!class_exists('simple_fv')) {
class simple_fv {
	
    private $ml_key = 'mg_ml'; // (string) localization key to be used in __() and _e() functions
    
    
	private $errors = array( // (array) errors container - array with error types indexes
		'required' 			=> array(),
		
		'wrong_int'			=> array(),
		'wrong_float'		=> array(),
		'wrong_mail'		=> array(),
		'wrong_date'		=> array(),
		'wrong_time'		=> array(),
		'wrong_url'			=> array(),
		'wrong_hex'			=> array(),
		'wrong_ip'			=> array(),
		'wrong_zip'			=> array(),
		'wrong_tel'			=> array(),
		
		'wrong_type'		=> array(),
		'allowed' 			=> array(),
		'forbidden' 		=> array(),
		'not_equal' 		=> array(),
		'size' 				=> array()
	);
	
    
	
	public $form_val = array(); // (array) form field values container (index => val)
	public $custom_error = array(); // (array) custom error container (subject => error message)
	
    
    
	
	/* INDEX RESULT
	 * given an array of index validation results - return the global response
	 *
	 * @param $results = ndex validation results array
	 */
	private function index_val_result($results) {
		$gr = true;
		foreach($results as $result) {
			if(!$result) {$gr = false; break;}	
		}
		return $gr;
	}
	
    
    
	
	/* ARRAY DI INDICI DA VALIDARE -> ARRAY DI VALORI 
	 * @param $indexes_array = array of associatives array containing the validation params
	 * @param $hide_err = hide the errors? (true/false)
	 */ 
	public function formHandle($indexes_array, $hide_err = false) {
		$results = array();
		
    	foreach($indexes_array as $index_val) {
			$index_results = array();
			
			$index = $index_val['index'];
			($hide_err) ? $label = '' : $label = $index_val['label'];
			
			// get the index value
			$passed_data = $this->getIndexVal($index);
			
			foreach($index_val as $key => $val) {
				$validate_type = $key;
				$validate_val = $val;

				// distinguo tra array e singoli
				if(is_array($passed_data)) {
					$a = 0;
					foreach($passed_data as $single_passed_data) {
						
						if(is_array($single_passed_data)) {
							$this->form_val[$index][$a] = array();
							foreach($single_passed_data as $arr_val) {
								$this->form_val[$index][$a][] = trim((string)$arr_val);	
							}
						}
						else {
							$this->form_val[$index][$a] = trim((string)$single_passed_data);
						}
						
						if($key != 'index' && $key != 'label') {
							
							// array counting validation
							if($key == 'min_array' || $key == 'max_array') {
								$index_results[] = $this->validate($validate_type, $validate_val, $passed_data, $label, $hide_err);
							}
							else {
								$index_results[] = $this->validate($validate_type, $validate_val, trim($single_passed_data), $label, $hide_err);
							}
						}
						$a++;	
					}
				}
				else {
					$this->form_val[$index] = $passed_data;
					
					if($key != 'index' && $key != 'label') {
						$index_results[] = $this->validate($validate_type, $validate_val, $passed_data, $label, $hide_err);	
					}
				}		
			}
			$results[] = $this->index_val_result($index_results);
		}
		
		return $results;
    }
	
	
    
    
	/* OR CONDITION 
	 * @param label = error's subject
	 * @param error_txt = the error's text
	 * @param fields = array of associatives array containing the validation params
	 */
	public function or_cond($label, $error_txt, $fields) {
		$results = $this->formHandle($fields, true); 

		$final = false;
		foreach($results as $result) {
			if($result) {$final = true; break;}	
		}
		
		if(!$final) { $this->custom_error[$label] = $error_txt; }
		return $final;		
	}
    
    
    
    
    /* RETURNS ALL FIELD INDEX VALUES (associative array)
	 * @param (array) $indexs = array of form field indexes
     * @param (bool) $use_validation_structure = whether to elaborate using the validation array structure
	 */
	public function getAllIndexVals($indexes, $use_validation_structure = false) {
		$vals = array();
        
        foreach($indexes as $index) {
            if($use_validation_structure) {
                $index = $index['index'];    
            }
            $vals[$index] = $this->getIndexVal($index);
        }
        
        return $vals;
	}
    
	
	
    
	/* GET THE INDEX VALUE
	 * search between $_POST, $_GET, $_REQUEST
	 *
	 * @param $index = index to search
	 */
	private function getIndexVal($index) {
		if(isset($_POST[$index])) {$index_val = $_POST[$index];}
		elseif(isset($_GET[$index])) {$index_val = $_GET[$index];}
		elseif(isset($_REQUEST[$index])) {$index_val = $_REQUEST[$index];}
		elseif(isset($_FILES[$index])) {$index_val = $index;} // return index - will get the val during validation
		else {$index_val = false;}
		
		if(!is_array($index_val)) {return trim($index_val);}
		else {return $index_val;}
	}
	
    
	
	/* VALIDATE FIELD VALUE
	 * @param $type = validation type
	 * @param $val = validation value
	 * @param $test = validate without saving in errors array (for OR condition)
	 * @param $index_val = index value to validate
	 * @param $label = field label
	 */
	private function validate($type, $val, $index_val, $label, $test=false) {
		
		// required
		if($type == 'required' && $val == true) {
			if($index_val == '') {
				if(!$test) {$this->errors['required'][] = $label;}
				return false;
			}	
			else {return true;}
		}
		
		
		// standard types
		if($type == 'type' && $index_val!='') {
			
			if($val == 'int') {
				if(substr($index_val,0,1) == '0' && strlen($index_val) > 1) {$index_val = substr($index_val,1);}

				if (!filter_var($index_val, FILTER_VALIDATE_INT) && $index_val != '0') {
					if(!$test) {$this->errors['wrong_int'][] = $label;}
					return false;
				}	
				else {return true;}
			}
			
			else if($val == 'float' && $index_val!='') {
				$index_val = str_replace(",", ".", $index_val);

				if($index_val != '0.00') {
					if(!filter_var($index_val, FILTER_VALIDATE_FLOAT)) {
						if(!$test) {$this->errors['wrong_float'][] = $label;}
						return false;
					}	
					else {return true;}
				}
				else {return true;}
			}
			
			else if($val == 'negative_int' && $index_val!='') {
				if(substr($index_val, 0, 1) == "-") {$index_val = substr($index_val, 1);}
				if(substr($index_val,0,1) == '0' && strlen($index_val) > 1) {$index_val = substr($index_val,1);}
				
				if (!filter_var($index_val, FILTER_VALIDATE_INT) && $index_val != '0') {
					if(!$test) {$this->errors['wrong_int'][] = $label;}
					return false;
				}
				else {return true;}	
			}
			
			else if($val == 'email' && $index_val!='') {
				if(!filter_var($index_val, FILTER_VALIDATE_EMAIL)) {
					if(!$test) {$this->errors['wrong_mail'][] = $label;}
					return false;
				}
				else {return true;}	
			}	
			
			else if($val == 'eu_date' && $index_val!='') { //dd/mm/yyyy
				$date = preg_split( '/[-\.\/ ]/', trim($index_val));
				
				$not_int = true;
				foreach($date as $date_part) {
					if(preg_match('/[\D]/', $date_part)) {$not_int = false; break;}	
				}
				
				if(!$not_int || count($date) != 3 || !checkdate($date[1], $date[0], $date[2])) {
					if(!$test) {$this->errors['wrong_date'][] = $label;}
					return false;
				}	
				else {return true;}
			}
			
			else if($val == 'us_date' && $index_val!='') { // mm/dd/yyyy
				$date = preg_split( '/[-\.\/ ]/', trim($index_val));
				
				$not_int = true;
				foreach($date as $date_part) {
					if(preg_match('/[\D]/', $date_part)) {$not_int = false; break;}	
				}
				
				if(!$not_int || count($date) != 3 || !checkdate($date[0], $date[1], $date[2])) {
					if(!$test) {$this->errors['wrong_date'][] = $label;}
					return false;
				}	
				else {return true;}
			}
			
			else if($val == 'iso_date' && $index_val!='') { // yyyy/mm/dd
				$date = preg_split( '/[-\.\/ ]/', trim($index_val));
				
				$not_int = true;
				foreach($date as $date_part) {
					if(preg_match('/[\D]/', $date_part)) {
                        $not_int = false; break;
                    }	
				}
				
				if(!$not_int || count($date) != 3 || !checkdate($date[1], $date[2], $date[0])) {
					if(!$test) {
                        $this->errors['wrong_date'][] = $label;
                    }
					return false;
				}	
				else {
                    return true;
                }
			}
			
			else if(in_array($val, array('12h_time', '24h_time')) && $index_val!='') {
				$arr = explode(':', $index_val);
                if(count($arr) != 2 || !is_numeric(trim($arr[0])) || !is_numeric(trim($arr[1]))) {
                    $this->errors['wrong_time'][] = $label;
                    return false;
                }
                
                $hour  = (int)trim($arr[0]);
				$mins  = (int)trim($arr[1]);
                $max_h = ($val == '12h_time') ? 12 : 24;
                
				if($hour < 0 || $hour >= $max_h || $mins < 0 || $mins >= 60) {
					if(!$test) {$this->errors['wrong_time'][] = $label;}
					return false;
				}	
				else {return true;}
			}
			
			else if($val == 'url' && $index_val!='') {
				if(!filter_var($index_val, FILTER_VALIDATE_URL)) {
					if(!$test) {$this->errors['wrong_url'][] = $label;}
					return false;
				}
				else {return true;}	
			}
			
			else if($val == 'hex' && $index_val!='') {
				$pattern = '/^#[a-f0-9]{6}$/i';
				if(!preg_match($pattern, $index_val)) {
					if(!$test) {$this->errors['wrong_hex'][] = $label;}
					return false;
				}	
				else {return true;}
			}	
			
			else if($val == 'ipv4' && $index_val!='') {
				$pattern = '/^(?:(?:25[0-5]|2[0-4]\d|(?:(?:1\d)?|[1-9]?)\d)\.){3}(?:25[0-5]|2[0-4]\d|(?:(?:1\d)?|[1-9]?)\d)$/';
				if(!preg_match($pattern, $index_val)) {
					if(!$test) {$this->errors['wrong_ip'][] = $label;}
					return false;
				}	
				else {return true;}
			}	
			
			else if($val == 'us_zipcode' && $index_val!='') {
				$pattern = '/(^\d{5}$)|(^\d{5}-\d{4}$)/';
				if(!preg_match($pattern, $index_val)) {
					if(!$test) {$this->errors['wrong_zip'][] = $label;}
					return false;
				}	
				else {return true;}
			}		
			
			
			// eg. (541) 754-3010
			else if($val == 'us_tel' && $index_val!='') {
				$pattern = '/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/';
				if(!preg_match($pattern, $index_val)) {
					if(!$test) {$this->errors['wrong_tel'][] = $label;}
					return false;
				}	
				else {return true;}
			}	
		}
		
		
		// preg_match
		if($type == 'preg_match' && $index_val!='') {
			if(!preg_match($val, $index_val)) {
				if(!$test) {$this->errors['wrong_type'][] = $label;}
				return false;
			}	
			else {return true;}
		}
		
		
		// min val
		if($type == 'min_val' && $index_val !== '') {
			if((float)$index_val < (float)$val) {
				if(!$test) {
					#$this->errors['value'][] = $label;
					$this->custom_error[$label] = __('minimum value is', $this->ml_key).' '.$val;
				}
				return false;
			}	
			else {return true;}
		}
		
		
		// max val
		if($type == 'max_val' && $index_val !== '') {
			if((float)$index_val > (float)$val) {
				if(!$test) {
					#$this->errors['value'][] = $label;
					$this->custom_error[$label] = __('maximum value is', $this->ml_key).' '.$val;
				}
				return false;
			}	
			else {return true;}
		}
		
		
		// min lenght
		if($type == 'min_len') {
			if(strlen($index_val) < (int)$val) {
				if(!$test) {
					#$this->errors['lenght'][] = $label;
					$this->custom_error[$label] = __('must be at least of', $this->ml_key).' '.$val.' '.__('characters', $this->ml_key);
				}
				return false;
			}	
			else {return true;}
		}
			
		
		// max lenght
		if($type == 'max_len') {
			if(strlen($index_val) > (int)$val) {
				if(!$test) {
					#$this->errors['lenght'][] = $label;
					$this->custom_error[$label] = __('maximum', $this->ml_key).' '.$val.' '.__('characters allowed', $this->ml_key);
				}
				return false;
			}	
			else {return true;}
		}
		
		
		// lenght obbligatoria
		if($type == 'right_len') {
			if(strlen($index_val) != (int)$val) {
				if(!$test) {
					#$this->errors['lenght'][] = $label;
					$this->custom_error[$label] = __('must be long', $this->ml_key).' '.$val.' '.__('characters', $this->ml_key);
				}
				return false;
			}	
			else {return true;}
		}
		
		
		// allowed
		if($type == 'allowed' && $index_val!='') {
			if(!in_array($index_val, $val)) {
				if(!$test) {$this->errors['allowed'][] = $label;}
				return false;
			}
			else {return true;}
		}	
		
		
		// forbidden
		if($type == 'forbidden' && $index_val!='') {
			if(in_array($index_val, $val)) {
				if(!$test) {$this->errors['forbidden'][] = $label;}
				return false;
			}
			else {return true;}
		}
			
			
		// equal to other field
		if($type == 'equal' && $index_val!='') {
			$equal_val = $this->getIndexVal($val);	
			if($index_val != $equal_val) {
				if(!$test) {$this->errors['not_equal'][] = $label;}
				return false;
			}
			else {return true;}
		}
		
		
		// min array count
		if($type == 'min_array') {
			if(!is_array($index_val) || count($index_val) < $val) {
				if(!$test) {
					#$this->errors['array_count'][] = $label;
					$this->custom_error[$label] = __('Select', $this->ml_key).' '.$val.' '.__('options', $this->ml_key);
				}
				return false;	
			}
		}
		
		
		// max array count
		if($type == 'max_array' && is_array($index_val)) {
			if(count($index_val) > $val) {
				if(!$test) {
					#$this->errors['array_count'][] = $label;
					$this->custom_error[$label] = __('Maximum', $this->ml_key).' '.$val.' '.__('options allowed', $this->ml_key);
				}
				return false;	
			}
		}
		
		
		///////////////////////////////////////////////////
		
		// upload required
		if($type == 'ul_required' && $val == true) {
			if($_FILES[$index_val]['error'] > 0) {
				if(!$test) {$this->errors['required'][] = $label;}
				return false;
			}	
			else {return true;}
		}
		
		// min filesize
		if($type == 'min_filesize' && $_FILES[$index_val]['error'] <= 0) {
			$filesize = $_FILES[$index_val]["size"] / 1024;
			if($filesize < $val) {
				if(!$test) {$this->errors['size'][] = $label;}
				return false;
			}	
			else {return true;}
		}
		
		// max filesize
		if($type == 'max_filesize' && $_FILES[$index_val]['error'] <= 0) {
			$filesize = $_FILES[$index_val]["size"] / 1024;
			if($filesize > $val) {
				if(!$test) {$this->errors['size'][] = $label;}
				return false;
			}	
			else {return true;}
		}
		
		// file mimetype
		if($type == 'mime_type' && $_FILES[$index_val]['error'] <= 0) {
			$file_type = $_FILES[$index_val]["type"];
			if(!in_array($file_type, $val)) {
				if(!$test) {$this->errors['wrong_type'][] = $label;}
				return false;
			}	
			else {return true;}
		}	
	}
	
    
    
	
	/* FORM VALUE ESCAPING
	 * return the form values with escaped strings
	 */
	public function escape_val() {
		$fv = $this->form_val;
		$new_fv = array();
		
		foreach($fv as $index=>$val) {
			if(!is_array($val)) { $new_fv[$index] = addslashes($val);}
			else {
				
				foreach($val as $subval) {
					$new_fv[$index][] = addslashes($subval);	
				}
			}
		}
		return $new_fv;
	}

    
    
	
	/* ERROR TRANSLITTERATION */
	private function errorTranslate($type, $label_array) {
		$message = ' - ';
		
		switch($type) {
			case 'required' : 
				(count($label_array) > 1) ? $message .= __('are required', $this->ml_key) : $message .= __('is required', $this->ml_key);
				break;
			
			case 'wrong_int' : 
				(count($label_array) > 1) ? $message .= __('are not valid integers', $this->ml_key) : $message .= __('is not a valid integer', $this->ml_key);
				break;
				
			case 'wrong_float' : 
				(count($label_array) > 1) ? $message .= __('are not valid floating numbers', $this->ml_key) : $message .= __('is not a valid floating number', $this->ml_key);
				break;	
				
			case 'wrong_mail' : 
				(count($label_array) > 1) ? $message .= __('are not valid e-mail addresses', $this->ml_key) : $message .= __('is not a valid e-mail address', $this->ml_key);
				break;		
				
			case 'wrong_date' : 
				(count($label_array) > 1) ? $message .= __('are not valid dates', $this->ml_key) : $message .= __('is not a valid date', $this->ml_key);
				break;		
				
			case 'wrong_time' : 
				(count($label_array) > 1) ? $message .= __('are not valid times', $this->ml_key) : $message .= __('is not a valid time', $this->ml_key);
				break;
				
			case 'wrong_url' : 
				(count($label_array) > 1) ? $message .= __('are not valid urls', $this->ml_key) : $message .= __('is not a valid url', $this->ml_key);
				break;	
				
			case 'wrong_hex' : 
				(count($label_array) > 1) ? $message .= __('are not valid hexadecimal colors', $this->ml_key) : $message .= __('is not a valid hexadecimal color', $this->ml_key);
				break;	
			
			case 'wrong_ip' : 
				(count($label_array) > 1) ? $message .= __('are not valid IP addresses', $this->ml_key) : $message .= __('is not a valid IP address', $this->ml_key);
				break;	
				
			case 'wrong_zip' : 
				(count($label_array) > 1) ? $message .= __('are not valid ZIP codes', $this->ml_key) : $message .= __('is not a valid ZIP code', $this->ml_key);
				break;
				
			case 'wrong_tel' : 
				(count($label_array) > 1) ? $message .= __('are not valid telephone numbers', $this->ml_key) : $message .= __('is not a valid telephone number', $this->ml_key);
				break;		
					
			case 'wrong_type' : $message .= __('invalid data inserted', $this->ml_key);
				break;	
				
			case 'allowed' : 
				(count($label_array) > 1) ? $message .= __('values are not between the allowed', $this->ml_key) : $message .= __('value is not between the allowed', $this->ml_key);
				break;
				
			case 'forbidden' : 
				(count($label_array) > 1) ? $message .= __('values are between the forbidden', $this->ml_key) : $message .= __('values is between the forbidden', $this->ml_key);
				break;
				
			case 'not_equal' : $message .= __("the value doesn't match", $this->ml_key); 
				break;
				
			case 'size' : $message .= __('file size is wrong', $this->ml_key);
				break;						
		}	
		
		return $message;
	}
	
	
    
    
	/* ERROR CREATOR */
	public function getErrors($type = 'string') {
		$errors_array = $this->errors;
		
		// validator errors
		foreach($errors_array as $err_type => $labels) {
			$labels = array_unique($labels);
			if(implode(', ', $labels) != '') {
				$errors[] = implode(', ', $labels) . $this->errorTranslate($err_type, $labels);	
			}
		}
		
		// custom message
		foreach($this->custom_error as $subj => $txt) {
			$errors[] = $subj . ' - ' . $txt;		
		}
		
		if(isset($errors)) {
			if($type == 'string') {return implode(' <br/> ', $errors);} 
			else {return $errors;}
		}
		else {
			return false;	
		}
	}
}
} // if class_exists() end 