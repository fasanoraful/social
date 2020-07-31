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

// $repeat_encode = Multiple encode if you feel paranoid ;)

set_time_limit(0);       // ignore php timeout
ignore_user_abort(true); // keep on going even if user pulls the plug*

if (!function_exists('e_looper_plugin_worker')) {
	function e_looper_plugin_worker($data,$post){
		global $DEBUG;
		global $first_l;
		if ( $post != '' ){
			$data = looper($data, $post);
		}
		if (isset($DEBUG) and $DEBUG == 'adilbo' and isset($post) and $post > 0 and $first_l == ''){
			$first_l++;
			echo '
				<div class="well well-sm"><h4><b>Looper PHP Code</b></h4>
				<textarea onclick="this.focus();this.select()" class="form-control" rows="5" readonly="readonly">'.
				$data.'</textarea></div>
			';
		}
		return $data;
	}
}

if (!function_exists('e_looper_plugin_html')){
	function e_looper_plugin_html($post){
		$html = '<div class="bg-info" style="border-radius:4px;padding:7px;margin-bottom:7px;">
			<label for="looper">		
			Looper &mdash; <i>PLUGIN</i>
			</label><div class="form-group row"><div class="col-sm-5">
			<select class="form-control" name="plugin[e_looper]" id="looper" dir="rtl">
			'.(isset($post)?'<option>'.$post.'</option>':'').'
			<optgroup label="&hellip;">
			<option value="">No Loop</option>
			<option value="1">1 x</option>
			<option value="7">7 x</option>
			<option value="12">12 x</option>
			<option value="32">32 x</option>
			<option value="64">64 x</option>
			<option value="128">128 x</option>
			<option value="Random">Random &mdash; from 1 to 128</option>
			</optgroup></select></div><div class="col-sm-7" style="line-height: 1"><small class="text-muted">
			How many times should the result be encrypted;<br>
			<b>It\'s no bullet proof protection and will increase the load size!</b>
			</small></div></div>
			</div>
		';
		return $html;
	}
}

function looper($data, $repeat_encode=1){
	global $DEBUG;
	if ($repeat_encode== 'Random'){
		$repeat_encode = rand(1,128);
	}
	# DEBUG
	#echo '<xmp style="color:red">'.substr($data,0,512).'</x'.'mp><b style="color:red">&hellip;</b>';
	# DEBUG
	if(strpos(strtolower($data), "<?php") !== false) $data = striptag($data);
	// $data = base64_encode(gzcompress($data, 9)); // DAS MUSS HIER WEG
	# DEBUG
	#echo '<xmp style="color:green">'.substr($data,0,100000).'</x'.'mp><b style="color:green">&hellip;</b>';
	#echo '<br><br><b style="color:GREY"><tt>LOOP</tt></b> ';
	# DEBUG
	for($i = 1;$i <= $repeat_encode;$i++){
		if($i == 1){
			$int_to_compress = 9; // COMPRESS ONLY IF RUN ONE TIME
		}else{
			$int_to_compress = 1; // NO COMPRESS IF RUN x TIMES (better for script executing time)
		}
		#echo '. '; // DEBUG
		if($i == $repeat_encode){
			$data = multi_in_encode($data, $i, $int_to_compress);
		}else{
			$data = multi_in_encode($data, $i, $int_to_compress);
		}
	}
	# DEBUG
	#echo '<xmp style="color:blue">'.$data.'</x'.'mp><b style="color:blue">&hellip;</b><br>';
	# DEBUG
	return $data;
}

// FUNCTION-POOL
function multi_in_encode($data, $step, $int_to_compress){
	global $DEBUG;
	$data = striptag($data);
	$collect_rand_var = array();
	if (isset($DEBUG) and $DEBUG == 'adilbo'){
		$output = '/* '.$step." */ @ini_set('memory_limit','-1');@ini_set('max_execution_time',0);@set_time_limit(0);";
	}else{
		$output = "@ini_set('memory_limit','-1');@ini_set('max_execution_time',0);@set_time_limit(0);";
	}
	$get_s = encode_looper();
	$output .= $get_s[0];
	$output .= "eval({$get_s[1]}({$get_s[2]}(\"".base64_encode(gzcompress($data, $int_to_compress))."\")));";
	return $output;
}

if(!function_exists("encode_looper")) {
	function encode_looper(){
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
		$data_function = "eval(str_rot13(gzuncompress(base64_decode(\"".base64_encode(gzcompress(str_rot13($data_function), 9))."\"))));";
		// USE LOOPER WITH LOADER ($loader):
		#$data_function = $loader.'eval($O0O($OO0($OOO("'.base64_encode(gzcompress(str_rot13($data_function), 9))."\"))));";
		$return_data = array($data_function, "\$".$rand_combine_1, "\$".$rand_combine_2);
		return $return_data;
	}
}

if(!function_exists("remove_trace")) {
	function remove_trace($string, $junk){
		return str_replace("=", $junk, $string);
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

function striptag($in){
  $pos = strpos(strtolower($in), "<?php");
  if (is_numeric($pos)){
      for ($i = $pos; $i <= $pos + 4 && strlen($in) >= 5; $i++) {
          $in[$i] = ' ';
      }
      return $in;
  }else{
      return $in;
  }
}
