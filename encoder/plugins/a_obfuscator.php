<?PHP

/**
 * 
 * ============================================================================
 * (c) 2014-2015 www.adilbo.com - All rights reserved - Alle Rechte vorbehalten
 * ----------------------------------------------------------------------------
 *
 * COPYRIGHT
 * This software is exclusively sold at CodeCanyon.net.  If you have downloaded 
 * this from another website or received it from someone else than me, then you 
 * are engaged in an illegal activity. You must erase this software immediately 
 * or buy a proper license from codecanyon.net/user/adilbo/portfolio?ref=adilbo
 * ============================================================================
 *
 */

/*
Variables                      $method[0]  = 'V'
Functions                      $method[0]  = 'F'
Variables & Functions          $method[12] = 'F'
Strings                        $method[0]  = 'S'
Variables & Strings            $method[12] = 'S'
Functions & Strings            $method[12] = 'S'
Variables, Functions & Strings $method[23] = 'S'
*/

if (!function_exists('a_obfuscator_plugin_worker')) {
	function a_obfuscator_plugin_worker($data,$post){
		global $DEBUG;
		global $first_o;
		if ( $post != '' ){
			$data = obfuscator($data, $post);
			if (isset($DEBUG) and $DEBUG == 'adilbo' and isset($post) and $post != '' and $first_o == ''){
				$first_o++; 
				echo '
					<div class="well well-sm"><h4><b>Obfuscated PHP Code</b></h4>
					<textarea onclick="this.focus();this.select()" class="form-control" rows="5" readonly="readonly">'.
					$data.'</textarea></div>
				';
			}
		}
		return $data;
	}
}

if (!function_exists('a_obfuscator_plugin_html')) {
	function a_obfuscator_plugin_html($post){
		$html = '<div class="bg-info" style="border-radius:4px;padding:7px;margin-bottom:7px;">
			<label for="obfuscator">		
			Obfuscator &mdash; <i>PLUGIN</i>
			</label><div class="form-group row"><div class="col-sm-5">
			<select class="form-control" name="plugin[a_obfuscator]" id="obfuscator" dir="rtl">
			'.(isset($post)?'<option>'.$post.'</option>':'').'
			<optgroup label="&hellip;">
			<option value="">No Obfuscation</option>
			<option value="Variables">Variables</option>
			<option value="Functions">Functions</option>
			<option value="Variables & Functions">Variables & Functions</option>
			<option value="Strings">Strings beta</option>
			<option value="Variables & Strings">Variables & Strings &mdash; beta</option>
			<option value="Functions & Strings">Functions & Strings &mdash; beta</option>
			<option value="Variables, Functions & Strings">Variables, Functions & Strings &ndash; beta</option>
			</optgroup></select></div><div class="col-sm-7" style="line-height: 1"><small class="text-muted">
			How hard should the result be encrypted; Variable names are lost forever!<br>
			<b>It\'s a bullet proof protection and will not increase the load size!</b>
			</small></div></div>
			</div>
		';
		return $html;
	}
}

function obfuscator($data,$method='Variables and Functions'){
	$except_list = array(
		"_POST",			"_GET",					"_FILES",				"_SERVER",
		"_REQUEST",			"_SESSION",				"_COOKIE",				"_ENV",
		"GLOBALS",			"argc",					"argv",					"this",
		"php_errormsg",		"HTTP_SERVER_VARS",		"HTTP_POST_VARS",		"HTTP_POST_FILES",
		"HTTP_GET_VARS",	"HTTP_COOKIE_VARS",		"HTTP_RAW_POST_DATA",	"http_response_header"
	);
	$method[11] = isset($method[11])?$method[11]:'';
	$method[12] = isset($method[12])?$method[12]:'';
	$method[25] = isset($method[25])?$method[25]:'';
	// Variables
	if($method[0] == 'V'){
		preg_match_all('/\$[A-Za-z0-9-_]+/', $data, $var_list);
		$wakil = array_values(array_unique($var_list[0]));
		$collect = array();
		foreach($wakil as $wakil_s){
			$collect[] = str_replace("$", "", $wakil_s);
		}
		foreach($except_list as $ignore){
			if(in_array($ignore, $collect, true)){
				$key = array_search($ignore, $collect);
				unset($collect[$key]);
			}
		}
		$collect_flip = array_flip($collect);
		foreach($collect_flip as $key => $value){
			$collect_flip[$key] = rndStr(rand(9, 12));
		}
		foreach($collect_flip as $key => $rand){
			$data = str_replace("$".$key, "$".$rand, $data);
		}
	}
	// Functions
	if($method[0] == 'F' OR $method[11] == 'F' OR $method[12] == 'F'){
		preg_match_all('/function[\s\n]+(\S+)[\s\n]*\(/', $data, $func_list);
		$func_list = $func_list[1];
		$func_list_flip = array_flip($func_list);
		foreach($func_list_flip as $key => $function){
			$func_list_flip[$key] = rndStr(rand(9, 12));
		}
		foreach($func_list_flip as $key => $rand){
			if(strpos($data, $key."(")){
				$data = str_replace($key."(", $rand."(", $data);
			}
		}
	}
	// Strings (beta)
	if($method[0] == 'S' OR $method[12] == 'S' OR $method[23] == 'S'){
		preg_match_all('/([\"\'])(?:(?=(\\\\?))\\2.)*?\\1/s', $data, $string_store);
		$string_store = array_unique($string_store[0]);
		$str_store_flip = array_flip($string_store);
		foreach($str_store_flip as $name => $value){
			if(strpos($data, $name) !== FALSE && strlen($name) > 4 && strpos($name, "\n") === FALSE){
				if(substr($name, 0, 1) == '"'){
					$str_store_flip[$name] = '"'.obfus(cutstr($name, '"', '"')).'"';
				}else{
					unset($str_store_flip[$name]);
				}
			}else{
				unset($str_store_flip[$name]);
			}
		}
		foreach($str_store_flip as $original => $replace){
			$data = str_replace($original, $replace, $data);
		}
	}
	return $data;
}

if(!function_exists("rndStr")) {
	function rndStr($length) {
		$keys = array_merge(range('A', 'Z'), range('a', 'z'));
		$key = "";
		for($i=0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}
		return $key;
	}
}

function obfus($string){
	#$string = s\/tr_split($string); // PHP 5 ONLY
	$array = array();
	for($i=0; $i < strlen($string); $i++){
        array_push($array,$string[$i]);
    }
	$i=0;
	foreach($array as &$char){
		$char = (++$i%2) ? "\x".dechex(ord($char)) : "\\".decoct(ord($char));
	}
	return implode('',$array);
}

function cutstr($data, $str1, $str2){
	$data = explode($str1, $data);
	$data = explode($str2, $data[1]);
	return $data[0];
}

