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
function  random_string          ($chrRandomLength='auto')
function  random_num             ($quant=3, $min=1, $max=200)
function  random_name            ()
function  time2date              ($timestamp, $format = 'd-m-Y')
function  validate_date          ($date)
function  compress_php_src       ($src)
function  get_contents           ($filename)
function  strip_tokens           ($code)
function  get_pluginname_array   ($plugins='plugins')
function  hook                   ($name)
function  clean_up               ($expire_time)
function  make_dir               ($dirpath, $mode=0777)
function  remove_equalsign_ending($string)
*/

function random_string($chrRandomLength='auto') {
	if ($chrRandomLength == 'auto') 
		$chrRandomLength = mt_rand(1,7); // FIX SECURITY UPDATE 28.09.2016
	$chrList = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$chrRepeatMin = 1;
	$chrRepeatMax = 7;
	return substr(str_shuffle(str_repeat($chrList, mt_rand($chrRepeatMin,$chrRepeatMax))),1,$chrRandomLength);
}

function random_num($quant=3, $min=1, $max=200) {
	$randary = array();
	while(!(count($randary) >= $quant || count($randary) > $max-$min))
		$randary[mt_rand($min,$max)] = true;
	return array_keys($randary);
}

function random_name() {   
	$iLength = 9; 
	$chrRandomArray = mt_rand(0,1);
	$aCharacters[0][]  = 'O'; // Oh
	$aCharacters[0][] .= '0'; // zero
	$aCharacters[1][]  = 'I'; // i
	$aCharacters[1][] .= 'l'; // L
	$aCharacters[1][] .= '1'; // one
	/*
	$aCharacters[2][]  = '_'; // underscore
	$aCharacters[2][] .= '1';
	$aCharacters[2][] .= '2';
	$aCharacters[2][] .= '3';
	$aCharacters[2][] .= '4';
	$aCharacters[2][] .= '5';
	$aCharacters[2][] .= '6';
	$aCharacters[2][] .= '7';
	$aCharacters[2][] .= '8';
	$aCharacters[2][] .= '9';
	*/
    for ($sRandomString = '', $i = 0; $i < $iLength; $i++) 
    	$sRandomString.= $aCharacters[$chrRandomArray][array_rand($aCharacters[$chrRandomArray])];
    return $aCharacters[$chrRandomArray][0].$sRandomString;
}

function time2date($timestamp, $format = 'd-m-Y') {
	$year = substr($timestamp, 0, 4);
	$month = substr($timestamp, 4, 2);
	$day = substr($timestamp, 6, 2);
	$date = new Datetime($year.'-'.$month.'-'.$day);
	return $date->format($format);
}

function validate_date($date) {
    list($d, $m, $y) = array_pad(explode('.', $date, 3), 3, 0);
	return ctype_digit("$d$m$y") && @checkdate($m, $d, $y);
}

function validate_ip($ip){
  if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$ip) and
	  !preg_match('/^(((?=(?>.*?(::))(?!.+\3)))\3?|([\dA-F]{1,4}(\3|:(?!$)|$)|\2))(?4){5}((?4){2}|((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?7)){3})\z/i',$ip)
     ){
  	return false; 
  }else{
  	return true; 
  }
}

function compress_php_src($src) {
    // Whitespaces left and right from this signs can be ignored
    static $IW = array(
        T_CONCAT_EQUAL,             // .=
        T_DOUBLE_ARROW,             // =>
        T_BOOLEAN_AND,              // &&
        T_BOOLEAN_OR,               // ||
        T_IS_EQUAL,                 // ==
        T_IS_NOT_EQUAL,             // != or <>
        T_IS_SMALLER_OR_EQUAL,      // <=
        T_IS_GREATER_OR_EQUAL,      // >=
        T_INC,                      // ++
        T_DEC,                      // --
        T_PLUS_EQUAL,               // +=
        T_MINUS_EQUAL,              // -=
        T_MUL_EQUAL,                // *=
        T_DIV_EQUAL,                // /=
        T_IS_IDENTICAL,             // ===
        T_IS_NOT_IDENTICAL,         // !==
        T_DOUBLE_COLON,             // ::
        T_PAAMAYIM_NEKUDOTAYIM,     // ::
        T_OBJECT_OPERATOR,          // ->
        T_DOLLAR_OPEN_CURLY_BRACES, // ${
        T_AND_EQUAL,                // &=
        T_MOD_EQUAL,                // %=
        T_XOR_EQUAL,                // ^=
        T_OR_EQUAL,                 // |=
        T_SL,                       // <<
        T_SR,                       // >>
        T_SL_EQUAL,                 // <<=
        T_SR_EQUAL,                 // >>=
    );
    if(is_file($src)) {
        if(!$src = get_contents($src)) {
            return false;
        }
    }
    $tokens = token_get_all($src);
    $new = "";
    $c = sizeof($tokens);
    $iw = false; // ignore whitespace
    $ih = false; // in HEREDOC
    $ls = "";    // last sign
    $ot = null;  // open tag
    for($i = 0; $i < $c; $i++) {
        $token = $tokens[$i];
        if(is_array($token)) {
            list($tn, $ts) = $token; // tokens: number, string, line
            $tname = token_name($tn);
            if($tn == T_INLINE_HTML) {
                $new .= $ts;
                $iw = false;
            } else {
                if($tn == T_OPEN_TAG) {
                    if(strpos($ts, " ") || strpos($ts, "\n") || strpos($ts, "\t") || strpos($ts, "\r")) {
                        $ts = rtrim($ts);
                    }
                    $ts .= " ";
                    $new .= $ts;
                    $ot = T_OPEN_TAG;
                    $iw = true;
                } elseif($tn == T_OPEN_TAG_WITH_ECHO) {
                    $new .= $ts;
                    $ot = T_OPEN_TAG_WITH_ECHO;
                    $iw = true;
                } elseif($tn == T_CLOSE_TAG) {
                    if($ot == T_OPEN_TAG_WITH_ECHO) {
                        $new = rtrim($new, "; ");
                    } else {
                        $ts = " ".$ts;
                    }
                    $new .= $ts;
                    $ot = null;
                    $iw = false;
                } elseif(in_array($tn, $IW)) {
                    $new .= $ts;
                    $iw = true;
                } elseif($tn == T_CONSTANT_ENCAPSED_STRING
                       || $tn == T_ENCAPSED_AND_WHITESPACE)
                {
                    if($ts[0] == '"') {
                        $ts = addcslashes($ts, "\n\t\r");
                    }
                    $new .= $ts;
                    $iw = true;
                } elseif($tn == T_WHITESPACE) {
                    $nt = @$tokens[$i+1];
                    if(!$iw && (!is_string($nt) || $nt == '$') && !in_array($nt[0], $IW)) {
                        $new .= " ";
                    }
                    $iw = false;
                } elseif($tn == T_START_HEREDOC) {
                    $new .= "<<<S\n";
                    $iw = false;
                    $ih = true; // in HEREDOC
                } elseif($tn == T_END_HEREDOC) {
                    $new .= "S;";
                    $iw = true;
                    $ih = false; // in HEREDOC
                    for($j = $i+1; $j < $c; $j++) {
                        if(is_string($tokens[$j]) && $tokens[$j] == ";") {
                            $i = $j;
                            break;
                        } else if($tokens[$j][0] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                } elseif($tn == T_COMMENT || $tn == T_DOC_COMMENT) {
                    $iw = true;
                } else {
                    if(!$ih) {
                        $ts = strtolower($ts);
                    }
                    $new .= $ts;
                    $iw = false;
                }
            }
            $ls = "";
        } else {
            if(($token != ";" && $token != ":") || $ls != $token) {
                $new .= $token;
                $ls = $token;
            }
            $iw = true;
        }
    }
    return $new;
}

function get_contents ($filename) {
    if(function_exists('fopen')){
        $handle = fopen ($filename, "rb");
        $get_contents_data = fread($handle, filesize($filename));
		fclose($handle);
    }elseif (function_exists('curl_exec')){ 
		$url = 'http'.(!empty($_SERVER['HTTPS'])?'s':'').':/'.$_SERVER['SERVER_NAME'].strtok($_SERVER["REQUEST_URI"],'?');
        $conn = curl_init($url.$filename);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($conn, CURLOPT_FRESH_CONNECT,  true);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        $get_contents_data = (curl_exec($conn));
        curl_close($conn);
    }else{
        $get_contents_data = FALSE;
    }
	return $get_contents_data;
}

function strip_tokens($code) {
    $args = func_get_args();
    $arg_count = count($args);
    if( $arg_count === 1 ) {
        $args[1] = T_COMMENT;
        $args[2] = T_DOC_COMMENT;
    }
    for( $i = 1; $i < $arg_count; ++$i )
        $strip[ $args[$i] ] = true;
    $newlines = array("\n" => true, "\r" => true);
    $tokens = token_get_all($code);
    reset($tokens);
    $return = '';
    $token = current($tokens);
    while( $token ) {
        if( !is_array($token) )
            $return.= $token;
        elseif(    !isset($strip[ $token[0] ]) )
            $return.= $token[1];
        else {
            for( $i = 0, $token_length = strlen($token[1]); $i < $token_length; ++$i )
                if( isset($newlines[ $token[1][$i] ]) )
                    $return.= $token[1][$i];
        }
        $token = next($tokens);
    }
	return $return;
}

function get_pluginname_array($plugins='plugins') {
	$plugin_name_array = array();
	global $dir;
	if($handle=@opendir($dir.$plugins)){
		while ( $file = readdir ($handle) ) {
			if ( $file != 'index.htm' AND $file[0] != '.' AND substr($file, -4, 1) == '.' ) {
				$plugin_name_array[] = substr($file,0,-4);
			}
		}
		closedir($handle);
		asort($plugin_name_array);
	}
	return $plugin_name_array;
}

function hook($name,$data,$post) {
	global $plugins;
	$hookContent = $data;
	foreach($plugins as $plugin) {
		$hookName = $plugin. '_' .$name;
		if(function_exists($hookName)) {
			$hookContent = $hookName($data,$_POST['plugin'][$plugin]);
			$data =	$hookContent;
		}
	}
	return $hookContent;
}

function plugin_hook($hookName,$post) {
	if(function_exists($hookName))
		return $hookName($post);
}

function clean_up($expire_time) {
	global $dir;
	global $REPOSITORY;
	global $DEBUG;
	foreach (glob($dir.$REPOSITORY.'o.*') as $Filename) {
		clearstatcache();
		$FileCreationTime = filemtime($Filename); // last change of file-content
		$FileAge = time() - $FileCreationTime; 
		if ($FileAge > ($expire_time * 60)){ // $expire_time IN MINUTES
			$hint = '';
			if($expire_time > 0){
				unlink($Filename);
				$hint = ' and was deleted';
			}
			if(isset($DEBUG) and $DEBUG=='adilbo'){
				print "<small>File <b>".basename($Filename)."</b> is older than <b>$expire_time</b> minutes".$hint."!</small><br>\n";
			}
		}
	}
	if(isset($DEBUG) and $DEBUG=='adilbo'){
		print "<br>\n";
	}
}

function make_dir($dirpath, $mode=0777) {
    return is_dir($dirpath) || mkdir($dirpath, $mode, true);
}

function remove_equalsign_ending($string){
	return rtrim($string,'=');
}

/* EOF */