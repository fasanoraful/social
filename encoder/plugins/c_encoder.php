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

if (!function_exists('c_encoder_plugin_worker')) {
	function c_encoder_plugin_worker($data,$post){
		global $DEBUG;
		global $first_c;
		if ( $post != '' ){
			$data = encoder($data, $post);
		}
		if (isset($DEBUG) and $DEBUG == 'adilbo' and isset($post) and $post != '' and $first_c == ''){
			$first_c++;
			echo '
				<div class="well well-sm"><h4><b>Dynamic Encoder PHP Code</b></h4>
				<textarea onclick="this.focus();this.select()" class="form-control" rows="5" readonly="readonly">'.
				$data.'</textarea></div>
			';
		}
		return $data;
	}
}

if (!function_exists('c_encoder_plugin_html')){
	function c_encoder_plugin_html($post){
		$html = '<div class="bg-info" style="border-radius:4px;padding:7px;margin-bottom:7px;">
			<label for="encoder">		
			Dynamic Encoder &mdash; <i>PLUGIN</i>
			</label><div class="form-group row"><div class="col-sm-5">
			<select class="form-control" name="plugin[c_encoder]" id="encoder" dir="rtl">
			'.(isset($post)?'<option>'.$post.'</option>':'').'
			<optgroup label="&hellip;">
			<option value="">No Encoding</option>
			<option value="Use Dynamic Encoder Algorithm">Use Dynamic Encoder Algorithm</option>
			</optgroup></select></div><div class="col-sm-7" style="line-height: 1"><small class="text-muted">
			Use the dynamic encryption algorithm;<br>
			<b>Use to encode single File or encode only single function!</b>
			</small></div></div>
			</div>
		';
		return $html;
	}
}

function encoder($data,$post){
	if($post==''){return $data;}
	# DEBUG
	#echo '<br><br><b style="color:GREY"><tt>DYNAMIC-ENCODER Präfix</tt></b> ';
	#echo '<xmp style="color:green">'.substr($data,0,100000).'</x'.'mp><b style="color:green">&hellip;</b>';
	# DEBUG
	$data = base64_encode(gzcompress($data, 9));
	$piece_pecah = rand(20,35);
	$junk_replace_last = rndStr(rand(6,12));
	$data = remove_trace($data, $junk_replace_last);
  // FIX - round function round up or down, therefore it didn’t encoded the whole content
	$get_piece = ceil(strlen($data)/$piece_pecah);
  // FIX - ceil function rounds always up, so it ensures that it allways process whole content
	$random_var = array();
	for($i = 0;$i <= $piece_pecah;$i++){
		$random_var[] = rndStr(rand(8,15));
	}
	$random_var_flip = array_flip($random_var);
	for($i = 0;$i <= $piece_pecah;$i++){
		$random_var_flip[$random_var[$i]] = "";
	}
	$take_piece = 0;
	for($i = 0;$i <= $piece_pecah;$i++){
		$take_piece++;
		if(strlen($data) <= $get_piece){
			$random_var_flip[$random_var[$i]] = substr($data, 0, $get_piece);
			break;
		}
		$random_var_flip[$random_var[$i]] = substr($data, 0, $get_piece);
		$data = substr($data, $get_piece);
	}
	$random_var_flip = array_filter($random_var_flip);
	$array_piece = count($random_var_flip);
	$tempfile = '';
	$repair_trace_var = rndStr(rand(5,9));
	$junk_var_1 = rndStr(rand(9,12));
	$junk_var_2 = rndStr(rand(9,12));
	$function_in_code = "\r\nfunction {$repair_trace_var}(\${$junk_var_1}, \${$junk_var_2}){ return str_replace(\${$junk_var_2}, \"=\", \${$junk_var_1}); }\r\n";
	$random_var_to_collect = rndStr(30);
	$tempfile .= "\${$random_var_to_collect} = \"\";";
	foreach($random_var_flip as $name => $value){
		$tempfile .= "\${$name} = \"{$value}\";";
		$tempfile .= "\$".rndStr(rand(8,20))." = \"".rndStr(rand(90,150))."\";";
		$tempfile .= "\$".$random_var_to_collect." .= \$".$name.";\n";
		$tempfile .= "\$".rndStr(rand(8,20))." = \"".rndStr(rand(90,150))."\";";
	}
	$get_s = encode_encoder();
	$tempfile .= $get_s[0];
	$tempfile .= $function_in_code;
	$tempfile .= "\r\neval({$get_s[1]}({$get_s[2]}({$repair_trace_var}(\${$random_var_to_collect}, \"{$junk_replace_last}\"))));";
	# DEBUG
	#echo '<br><br><b style="color:GREY"><tt>DYNAMIC-ENCODER Suffix</tt></b> ';
	#echo '<xmp style="color:green">'.substr($tempfile,0,100000).'</x'.'mp><b style="color:green">&hellip;</b>';
	# DEBUG
	return $tempfile;
}

if(!function_exists("remove_trace")) {
	function remove_trace($string, $junk){
		return str_replace("=", $junk, $string);
	}
}

if(!function_exists("encode_encoder")) {
	function encode_encoder(){
		for($i = 0;$i <= 7;$i++){
			$soh[] = rndStr(rand(8,12));
		}
		// <INTERN> encoder/ENVATO/_intern/_how_do_the_loader_work.php
		$loader  = '$k=gzinflate(base64_decode("K0rMNcvLTzEpLi0wTk8tMUxKjq8CAA"));';
		$loader .= '$O0O="$k[8]$k[14]$k[0]$k[18]$k[0]$k[5]$k[14]$k[15]$k[11]";';
		$loader .= '$OO0="$k[12]$k[19]$k[9]$k[4]$k[17]$k[5]$k[2]$k[10]$k[0]$k[13]$k[8]$k[8]";';
		$loader .= '$OOO="$k[16]$k[1]$k[8]$k[13]$k[3]$k[7]$k[18]$k[6]$k[13]$k[17]$k[5]$k[6]$k[13]";';
		// </INTERN>
		$rand_combine_1 = rndStr(rand(8,12));
		$rand_combine_2 = rndStr(rand(8,12));
		$data_function = "\r\${$soh[1]} = \"gzu\";\${$soh[2]} = \"ncomp\";\${$soh[3]} = \"ress\";
						\r\${$rand_combine_1} = \${$soh[1]}.\${$soh[2]}.\${$soh[3]};
						\r\${$soh[4]} = \"base\";\${$soh[5]} = \"64_d\";\${$soh[6]} = \"ec\";\${$soh[7]} = \"ode\";
						\r\${$rand_combine_2} = \${$soh[4]}.\${$soh[5]}.\${$soh[6]}.\${$soh[7]};";
		// USE LOOPER WITHOUT LOADER ($loader):
		#$data_function = "eval(str_rot13(gzuncompress(base64_decode(\"".base64_encode(gzcompress(str_rot13($data_function), 9))."\"))));";
		// USE LOOPER WITH LOADER ($loader):
		$data_function = $loader.'eval($O0O($OO0($OOO("'.base64_encode(gzcompress(str_rot13($data_function), 9))."\"))));";
		$return_data = array($data_function, "\$".$rand_combine_1, "\$".$rand_combine_2);
		return $return_data;
	}
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
