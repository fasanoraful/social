<?PHP

/**
 * v1.1
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
 
// TESTING-TOOL: http://www.splitbrain.org/encode.php

if($DEMO == TRUE){
	$REPOSITORY = 'repository/-/';
}

// LOAD PLUGINS IF EXIST
$plugins = get_pluginname_array();
$plugin_html = '';
foreach($plugins as $plugin){
	require_once($dir.'plugins/'.$plugin.'.php');
	$_POST['plugin'][$plugin] = isset($_POST['plugin'][$plugin])?$_POST['plugin'][$plugin]:'';
	$plugin_html .= plugin_hook($plugin.'_plugin_html',$_POST['plugin'][$plugin]);
}

// SET FILENAME
$infile  = isset($_GET['file'])?$_GET['file']:'';
if(isset($DEMO) AND $DEMO == TRUE){
	$infile = 'demo';
}
$outfile = 'o.'.$infile.'.php';
if ((strpos($infile ,'.') == TRUE OR 
	strpos($infile ,'/') == TRUE OR 
	strpos($infile ,'\\') == TRUE OR 
	strtolower($infile) == 'indxex') AND
	!isset($_GET['urlcontroller'])
){
	die('<br><code><b>USAGE</b>: ?file=filename for filename.php (don\'t use dots in filename)</code>');
	
}
$exitError = '';
if (isset($_POST['filename']) and
	(strpos($_POST['filename'] ,'.') == TRUE OR 
	strpos($_POST['filename'] ,'/') == TRUE OR 
	strpos($_POST['filename'] ,'\\') == TRUE)
){
	$exitError = '<br><code><b>ERROR</b>: Filename is not allowed! (don\'t use dots or slashes in filename)</code>';
	$ERROR = TRUE;
	
}
$filenameError = '';
if ($outfile == 'o..php' AND isset($_POST['filename']) and $_POST['filename'] == '') {
	$filenameError = '<code><b>ERROR</b>: Filename is required! (don\'t use dots or slashes in filename)</code>';
	$ERROR = TRUE;
}

// CHECK GET & POST INPUT

// CHECK EXPIRATION DATE
$lockDateError = '';
if (isset($_POST['doLockDate']) AND 
	$_POST['doLockDate'] == 'on' AND 
	(
		!isset($_POST['lockDate']) OR 
		validate_date($_POST['lockDate']) != TRUE
	)
){
	$lockDateError = '<br><code><b>ERROR</b>: Please set correct Date!</code>';
	$ERROR = TRUE;
}
// CHECK LOCK IP
$lockIpError = '';
if (isset($_POST['doLockIp']) AND 
	$_POST['doLockIp'] == 'on' AND 
	(
		!isset($_POST['lockIp']) OR 
		validate_ip($_POST['lockIp']) != TRUE
	)
){
	$lockIpError = '<br><code><b>ERROR</b>: Please set correct IP!</code>';
	$ERROR = TRUE;
}
// CHECK LOCK DOMAIN(S) - IF (example.com|localhost) IS USED
$lockDomainError = '';
if ( isset($_POST['doLockDomain']) AND $_POST['doLockDomain'] == 'on' AND
     isset($_POST['lockDomain'])   AND $_POST['lockDomain'] != '') {
	preg_match('#^\((.*)\)$#', $_POST['lockDomain'], $treffer);
	$domains = @explode('|',$treffer[1]);
	if ( count($domains) > 1 ) {
		foreach ( $domains as $domain ) {
			if ( gethostbyname($domain) == $domain ) {
				$lockDomainError = '<br><code><b>ERROR</b>: Please set all Domains correct!</code>'.count($domains);
				$ERROR = TRUE;
			}
		}
	}
	else if ( gethostbyname($_POST['lockDomain']) == $_POST['lockDomain'] ) {
		$lockDomainError = '<br><code><b>ERROR</b>: Please set correct Domain!</code>';
		$ERROR = TRUE;
	}
}elseif(isset($_POST['doLockDomain']) AND $_POST['doLockDomain'] == 'on' AND
     $_POST['lockDomain'] == ''){
	$lockDomainError = '<br><code><b>ERROR</b>: Please set Domain!</code>';
	$ERROR = TRUE;
}
clearstatcache(); // DONT CACHE file_exists()
$data = '';
if (($infile != '' AND 
	!file_exists ($dir.$REPOSITORY.$infile.'.php')) OR 
	(isset($_GET['urlcontroller']) AND 
	$infile == '')
) {
	$exitError='<br><code><b>ERROR</b>: File not found!</code>';
	$data = '';
}else{
	// READ CODE
	$PSW = 'php'.'_strip_whitespace';
	if ( $infile != '' ) {
		if ( isset($_POST['dontMinify']) AND $_POST['dontMinify'] == 'on' ) {
			$data = get_contents($dir.$REPOSITORY.$infile.'.php');
		}else{
			if (function_exists($PSW)) { // PHP > 5.0.1. ONLY
				global $PSW;
				// It should be pointed out that this function does not work on files 
				// that make use of the short tag notation (<?).
				$data = $PSW($dir.$REPOSITORY.$infile.'.php'); // https://bugs.php.net/bug.php?id=29606
				
			}else{
				$data = get_contents($dir.$REPOSITORY.$infile.'.php');
				$data = compress_php_src($data);
			}
		}
	}elseif(isset($_POST['data']) and $_POST['data'] != '' ){
		$data = isset($_POST['data'])?$_POST['data']:'';
		$data = preg_replace(array("/<\?php/i","/<\?/","/\?>/"), '', $data); // DEL PHP OPEN/CLOSE TAGs
		if ( isset($_POST['dontMinify']) AND $_POST['dontMinify'] == 'on' ) {
			// do nothing
		}else{
			if ( function_exists($PSW) ) {
				global $PSW;
				$f = fopen($dir.$REPOSITORY.'temp.php','wb'); 
				fwrite($f,$data,strlen($data)); 
				fclose($f); 
				$data = $PSW($dir.$REPOSITORY.'temp.php');
				unlink($dir.$REPOSITORY.'temp.php');
			}else{
				$data = compress_php_src($data);
			}
		}
	}
	// BACKUP POST CODE
	if ( isset($_POST['filename']) AND $_POST['filename']!='' and !isset($_GET['file']) ){
		$datafile = ''.$_POST['filename'].'.php';
		$f = fopen($dir.$REPOSITORY.$datafile,'wb');
		fwrite($f,$data,strlen($data)); 
		fclose($f); 
	}
	$data = preg_replace(array("/<\?php/i","/<\?/","/\?>/"), '', $data); // DEL PHP OPEN/CLOSE TAGs
	$data2 = $data;
}

// HTML FORM
$COPYRIGHT = $COPYRIGHT[rand(0,count($COPYRIGHT)-1)];
if ( !isset($_GET['urlcontroller']) ) {
	/* http://www.askapache.com/online-tools/base64-image-converter/ */
	#echo '<style>div{border:1px solid red !important;}</style>'; // DEBUG ONLY
	echo '
	<h2>PHP Encoder &amp; Obfuscator
	<sup><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/
	9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAMDS
	URBVHjaYvz//z8DMli2bBnPv3//Sv/+/WsJxEJAzADE74D4+J8/f7rz8/O/IKsHCCBGZAOWLl1qANS8
	TkpKSpGPj4+BiYmJAchn+PHjB8Pr168Z7gMB0KCgsrKyCzA9AAEENwComQcoeV5ZWVnl169fDI8fP2b
	4+vUryHYGFhYWBnFxcbC6a9eu3QGK6dXV1X0H8QECiAlmElAwU1JSUuX3798Mt27duvf582cFIJsNhE
	HsmzdvPgMZDHSdCtArOTB9AAHEAmMABS25uLgY7t69ywDU5JOTk/MQyasPm5ubXR4+fHhNQUEBrBYmA
	RBAyAaIgvwMtA3Evs+ABkBiINeB1ABdKwoTBwggcBg8OpL0//hddYaXHznBfvbTPsPA/P83w/+/fxj+
	/4Hg37//Miy8YAQOVHGO9wze0mcY1NPOMQIEEMQFQENCgh0ZGBmYGBiZOYG0JwPDP0YGBhYOkCQwgH4
	z/P3zmaHJ9StQ/DdYy50Vx8A0QACBDQDZBFL4580qBhZ2EQZGRh6gQlYGBk4BoPA/BoZvHxn+frrN8O
	v7U4Z/f74xcCrmMPz/9RNsAEAAQQwAOpHhH8imXwwfrp4F+pObQUDLiuHxjm6gfjEGOTtXhg83rgINe
	M/AqyAIdPBfhv+/f4ENAAggqAG/wd74D3SqgLoaAxObCAMD4z+Gvz//Mlx+w8kgx/CLQUBZkeHHV3ag
	2m9gV8EMAAggcDr4DwxdkKkgv76/cgHoilMMDEx/GP79+Mfw8zvQqf+BLrt5neHDrUcQ7/77B9YDAgA
	BxAIzAGwq0AsCqgoMzFzCQAN+MShFpzMo/QZq+PSSgV9ZioHtEyvEBUDvwgwACCCoAb/Apv77AwqDq0
	ADeBkYOIABycIGFAcZ8IHh+/vXDL+/fmbgleMDBvc/iLeBACCA4AaAvMAuk8zAKc/MwMjGDolCJmBM/
	AN67dd3Bt7vn4E+/Al06F9IeIECHggAAghiADBK7q0rhYQFOOH8huI/cBqYwiAJ6+9fCBtkEBAABBgA
	crKyzeYSULMAAAAASUVORK5CYII="></sup>
	</h2><p>
	This script is a PHP source-code Encoder &amp; Obfuscator for 100% pure PHP Code.
	This tool use techniques to protect your code from reverse engineering 
	and modification. <br>
	It provides anti-theft protection for your scripts by allowing 
	you to encode, obfuscate, compress, scramble, set an expiration date, 
	domain/ip lock and encrypt your php source code.
	</p>';

# SYSTEM ERROR
	if($exitError!=''){
		echo $exitError;
		die('<br><br><form action="./"><p>
		<button type="submit" id="button-obfuscate" class="btn btn-primary">Try again</button></p></form>');
	}

# LINK
	if($EXPERTMODE==TRUE and $ERROR!=TRUE){
		echo '
		<p>Check the this example to see processed code:
		<!--div style="padding-bottom:7px"-->
		<a target="file" href="'.$url.'?file=demo&urlcontroller=true">'.
		$url.'?file=demo&urlcontroller=true</a>
		<!--/div--><br></p>';
	}
//  title="EXPERT-MODUS" data-toggle="popover" data-trigger="hover" data-placement="top" data-container=".form-group" data-content="Lorem Ipsum Dolore!"
# CODE-TEXTAREA
	echo '
	<form class="well well-sm" role="form" method="post">
	<h4><b>Input the complete Code of one of your PHP-Files</b> (use only one &lt;?php Tage and only one ?&gt; Tag)</h4><div class="form-group">
	<span style="display:block!important;" class="hint--error hint--left hint--rounded hint--bounce" data-hint="1. Paste your PHP into the Box!">
	<textarea';
	if(isset($DEMO) AND $DEMO == TRUE){ /* http://ksylvest.github.io/jquery-growl/ */
		echo ' onfocus="$.growl.warning({title:\'DEMO-MODE\',message:\'No own code allowed!\'});" ';
	}
	echo '
	name="data" class="form-control center-block" rows="5"
	onclick="this.focus();this.select()">'.trim($data).'</textarea></span></div>';

# PLUGINS
	if (isset($plugin_html) AND $plugin_html != '') {
		echo $plugin_html;
	}else{
		$_POST['encryption'] = 'on';
	}

# Use complete, single File Encryption
	echo '
	<div class="checkbox"><label><input type="checkbox" name="encryption" id="encryption value="yes"
	'.(isset($_POST['encryption'])?' checked="ckecked"':'').'><b>
Use complete, single File Encryption
	</b><small class="text-muted"> (to discourage code changes)</small></label></div>';

# Enter EXPERT-MODE
	if (!isset($ERROR))$hidden=' hidden';
	echo '<h6 style="cursor:pointer;" title="#EXPERT" class="toggle"><b>Enter EXPERT-MODUS</b> (only for professionals)</h6><div id="EXPERT" class="well'.$hidden.'">';

# Add a Copyright Tag
	echo '<div class="form-group row">
	<span style="display:block!important;" class="hint--top hint--rounded hint--bounce" data-hint="On every new Start, an entry from the File config/default.phg out of the $COPYRIGHT array is used. If you want always the same text use $COPYRIGHT=array(\'Your Text\');!">
	<div class="col-sm-12"><label>
Add a Copyright Tag
	<small class="text-muted"> (directly after the PHP start tag)</small></label>';
	$COPYRIGHT = isset($_POST['copyright'])?$_POST['copyright']:$COPYRIGHT;
	$copy_lines = substr_count($COPYRIGHT,"\n");
	if ($copy_lines>0) {
		echo '<textarea name="copyright" class="form-control center-block" rows="'.($copy_lines+1).'" 
	onclick="this.focus();this.select()">'.$COPYRIGHT.'</textarea>';
	}else{
		echo '<input type="text" class="form-control" id="copyright" name="copyright" 
	value="'.$COPYRIGHT.'">';
	}
	echo '</div></span></div>';

# Fix Domain
	echo '
	<div class="checkbox">
	<span class="hint--top hint--rounded hint--bounce" data-hint="Tick this if your PHP script only should work on a specific domain &mdash; if you want more then one Domain use ist like this (example.com|domain.org)">
	<label><input type="checkbox" id="doLockDomain" name="doLockDomain" 	onClick="if(document.getElementById(\'lockDomain\').style.visibility==\'hidden\'){document.getElementById(\'lockDomain\').style.visibility=\'visible\';}else{document.getElementById(\'lockDomain\').style.visibility=\'hidden\';}"'.(isset($_POST['doLockDomain'])?' checked="checked"':'').'>		
Fix Domain
	<small class="text-muted">
	(set the code functionality to a special domain &mdash; only if source is <b>encrypted</b>)</small>
	</label><label><input size="25" type="text" id="lockDomain" placeholder="(example.com|localhost)"
	value="'.(isset($_POST['lockDomain'])?$_POST['lockDomain']:$_SERVER['SERVER_NAME']).'" 
	name="lockDomain"'.(isset($_POST['doLockDomain'])?'':' style="visibility:hidden;"').'>
	</label>'.$lockDomainError.'</span></div>';

# Fix IP
	echo '
	<div class="checkbox">
	<span class="hint--top hint--rounded hint--bounce" data-hint="Tick this if your PHP script only should work on a specific ip">
	<label><input type="checkbox" id="doLockIp" name="doLockIp" 	onClick="if(document.getElementById(\'lockIp\').style.visibility==\'hidden\'){document.getElementById(\'lockIp\').style.visibility=\'visible\';}else{document.getElementById(\'lockIp\').style.visibility=\'hidden\';}"'.(isset($_POST['doLockIp'])?' checked="checked"':'').'>		
Fix IP
	<small class="text-muted">
	(set the code functionality to a special IP (IPv4/IPv6) &mdash; only if source is <b>encrypted</b>)</small>
	</label><label><input size="25" type="text" id="lockIp" placeholder="127.0.0.1"
	value="'.(isset($_POST['lockIp'])?$_POST['lockIp']:gethostbyname(gethostname())).'" 
	name="lockIp"'.(isset($_POST['doLockIp'])?'':' style="visibility:hidden;"').'>
	</label>'.$lockIpError.'</span></div>';

# Set Expiration Date
	echo '
	<div class="checkbox">
	<span class="hint--top hint--rounded hint--bounce" data-hint="Tick this if your PHP script only should work till a specific date">
	<label><input type="checkbox" id="doLockDate" name="doLockDate" onClick="if(document.getElementById(\'lockDate\').style.visibility==\'hidden\'){document.getElementById(\'lockDate\').style.visibility=\'visible\';}else{document.getElementById(\'lockDate\').style.visibility=\'hidden\';}"'.(isset($_POST['doLockDate'])?' checked="checked"':'').'>
Set Expiration Date		
	<small class="text-muted"> (blocks the code after the given date &mdash; only if source is <b>encrypted</b>)
	</small></label><label>
	<input size="25" type="text" id="lockDate" 
	value="'.(isset($_POST['lockDate'])?$_POST['lockDate']:date('d.m.Y',time()+86400*$expirationDays)).'" name="lockDate" placeholder="dd.mm.yyyy"'.(isset($_POST['doLockDate'])?'':' style="visibility:hidden;"').'>
	</label>'.$lockDateError.'</span></div>';

# Checksum Type
	echo '
	<span class="hint--top hint--rounded hint--bounce" data-hint="Select which checksum type you want to use &mdash; they are all the same safe!">
	<label for="checksumType">		
Checksum Type
	</label><div class="form-group row"><div class="col-sm-5">
	<select class="form-control" name="checksumType" id="checksumType" dir="rtl">
	'.(isset($_POST['checksumType'])?'<option>'.$_POST['checksumType'].'</option>':'').'
	<optgroup label="&hellip;"></optgroup>
	<option value="md5">md5 - 32 characters</option>
	<option value="sha1">sha1 - 40 characters</option>
	<option value="whirlpool">whirlpool - 128 characters</option>
	<option value="crc32">crc32 - 8 characters</option>
	</select></div><div class="col-sm-7" style="line-height: 1"><small class="text-muted">
	To protect the <i>encrypted</i> code being altered, we use a checksum. 
	Whenever the hash is not correspond to the string 
	the script will stop.</small></div></div></span>
	';

# Do not obfuscate names 
	echo '
	<div class="checkbox">
	<span class="hint--top hint--rounded hint--bounce" data-hint="Only use this when the names (variables &amp; functions) of the loader should not be encrypted &mdash; There is normally no reason to tick this!">
	<label><input type="checkbox" id="dontScrambleVars" 
	name="dontScrambleVars"'.(isset($_POST['dontScrambleVars'])?' checked="checked"':'').'>		
Do not obfuscate names 
	(variables &amp; functions) <small class="text-muted">
	(obfuscate names are harder to read and understand in <b>encrypted</b> code)</small>
	</label></span></div>';

# Do not minify source code
	echo '
	<div class="checkbox">
	<span class="hint--top hint--rounded hint--bounce" data-hint="Only use this when you don\'t whant comments and whitespace removed &mdash; There is normally no reason to tick this!">
	<label><input type="checkbox" id="dontMinify" name="dontMinify"'.
	(isset($_POST['dontMinify'])?' checked="checked"':'').'>
Do not minify source code
	<small class="text-muted"> (minify source code makes a smaller payload size)</small>
	</label></span></div>';

# Filename of your processed PHP Code
	if($infile == '') {
		echo '
		<div class="form-group row"><div class="col-sm-12">
		<span class="hint--top hint--rounded hint--bounce" data-hint="This is the Default Filename your encoded PHP-Code is saved in the Subfolder of your Repository &mdash; There is normally no reason to change this!">
		<label>
Filename of your processed PHP Code
		<small class="text-muted"> (don\'t use dots or slashes and no suffix like .php &mdash; Filename in Folder will be string.php for original Code and o.string.php for encoded version)</small></label>
		<input type="text" class="form-control" id="filename" name="filename" 
		value="'.((isset($_POST['filename']) AND $_POST['filename']!='')?$_POST['filename']:md5(time())).'">
		'.$filenameError.'</span></div></div>';
	}

# </EXPERT-MODE>
	echo '</div>';

# Process (SUBMIT-BUTTON)
	echo '<span class="hint--error hint--left hint--rounded hint--bounce" data-hint="2. Click to encode!">
	<p><button type="submit" id="button-obfuscate" class="btn btn-primary">
Process
	</button></p>
	</span>
	</form>';
}

// PLUGIN WORKER (e.g. OBFUSCATOR or LOOPER)
if ( isset($_POST['encryption']) ) {
	$data = hook('plugin_worker',$data,@$_POST['plugin']);
}

// COMPRESS
$data_encoded=remove_equalsign_ending(base64_encode(gzdeflate($data)));

// VAR & FUNCTION NAMES
if ( isset($_POST['dontScrambleVars']) AND $_POST['dontScrambleVars'] == 'on' ){ 
	$x5rb7Bygi1   = 'data';
	$IiwkZyk7JGQ9 = 'verify';
	$PXN1YnN0ci   = 'read';	
}else{
	$x5rb7Bygi1   = random_name();
	$IiwkZyk7JGQ9 = random_name();
	$PXN1YnN0ci   = random_name();
	while($IiwkZyk7JGQ9 == $PXN1YnN0ci){
		$PXN1YnN0ci = random_name();	
	}
}

// CHECK
$lockDomain = '';
if ( isset($_POST['doLockDomain']) AND $_POST['doLockDomain'] == 'on' AND isset($_POST['lockDomain']) ) {
	$lockDomain = 'if(!preg_match("/^'.$_POST['lockDomain'].'$/i", $_SERVER[\'SERVER_NAME\']))die("<tt>'.$domainErrorAlert.'</tt>");';
	$lockDomainNoAlert = 'if(!preg_match("/^'.$_POST['lockDomain'].'$/i", $_SERVER[\'SERVER_NAME\']))die();';

}
$lockIp = '';
if ( isset($_POST['doLockIp']) AND $_POST['doLockIp'] == 'on' AND isset($_POST['lockIp']) ) {
	$lockIp = 'if(!preg_match("/^'.$_POST['lockIp'].'$/i", gethostbyname(gethostname())))die("<tt>'.$ipErrorAlert.'</tt>");';
	$lockIpNoAlert = 'if(!preg_match("/^'.$_POST['lockIp'].'$/i", gethostbyname(gethostname())))die();';
}
$lockDate = '';
if ( isset($_POST['doLockDate']) AND $_POST['doLockDate'] == 'on' AND isset($_POST['lockDate']) ) {
	$lockDate = 'if(time()>'.strtotime($_POST['lockDate']). ')die("<tt>'.$dateErrorAlert.'</tt>");';
	$lockDateNoAlert = 'if(time()>'.strtotime($_POST['lockDate']). ')die();';
}
$CHECKSUMTYPE = isset($_POST['checksumType'])?$_POST['checksumType']:'crc32';
if (function_exists('hash')) {
	if ( $lockErrorAlert == TRUE ) {
		$verify_checksum_encoded = remove_equalsign_ending(base64_encode('if(!function_exists("'.$IiwkZyk7JGQ9.'")){function '.$IiwkZyk7JGQ9.'($a,$b,$c){$d=implode($c);$d=preg_replace("/__halt_compiler.*/","",$d);if($b==hash("'.$CHECKSUMTYPE.'","$d")){return(gzinflate(base64_decode($a)));}else{die("<tt>'.$CHECKSUMTYPE.' '.$hashErrorAlert.'</tt>");}}}'.$lockDomain.$lockIp.$lockDate));
	}else{
		$verify_checksum_encoded = remove_equalsign_ending(base64_encode('if(!function_exists("'.$IiwkZyk7JGQ9.'")){function '.$IiwkZyk7JGQ9.'($a,$b,$c){$d=implode($c);$d=preg_replace("/__halt_compiler.*/","",$d);if($b==hash("'.$CHECKSUMTYPE.'","$d")){return(gzinflate(base64_decode($a)));}else{die();}}}'.$lockDomainNoAlert.$lockIpNoAlert.$lockDateNoAlert));
	}
}else{
	if ( $lockErrorAlert == TRUE ) {
		$verify_checksum_encoded = remove_equalsign_ending(base64_encode('if(!function_exists("'.$IiwkZyk7JGQ9.'")){function '.$IiwkZyk7JGQ9.'($a,$b,$c){$d=implode($c);$d=preg_replace("/__halt_compiler.*/","",$d);if($b==bin2hex(mhash(MHASH_'.strtoupper($CHECKSUMTYPE).',"$d"))){return(gzinflate(base64_decode($a)));}else{die("<tt>'.$CHECKSUMTYPE.' '.$hashErrorAlert.'</tt>");}}}'.$lockDomain.$lockIp.$lockDate));
	}else{
		$verify_checksum_encoded = remove_equalsign_ending(base64_encode('if(!function_exists("'.$IiwkZyk7JGQ9.'")){function '.$IiwkZyk7JGQ9.'($a,$b,$c){$d=implode($c);$d=preg_replace("/__halt_compiler.*/","",$d);if($b==bin2hex(mhash(MHASH_'.strtoupper($CHECKSUMTYPE).',"$d"))){return(gzinflate(base64_decode($a)));}else{die();}}}'.$lockDomainNoAlert.$lockIpNoAlert.$lockDateNoAlert));
	}
}
// RANDOM
$random_data_encoded = random_string();

// RETRIEVE
if (function_exists('hash')) {
    $hash = hash("$CHECKSUMTYPE",$random_data_encoded); // PHP 5.1 ONLY
    #$hash = $CHECKSUMTYPE($random_data_encoded); // NOT SAFE FOR crc32 IN CASE OF: checksums will result in negative integers
}else{
	if($CHECKSUMTYPE=='md5'){// http://php.net/manual/en/mhash.constants.php 
		$hash = bin2hex(mhash(MHASH_MD5, $random_data_encoded));
	}elseif($CHECKSUMTYPE=='sha1'){
		$hash = bin2hex(mhash(MHASH_SHA1, $random_data_encoded));
	}elseif($CHECKSUMTYPE=='whirlpool'){
		$hash = bin2hex(mhash(MHASH_WHIRLPOOL, $random_data_encoded));
	}else{ // crc32
		$hash = bin2hex(mhash(MHASH_CRC32, $random_data_encoded));
	}
}
if ( $dontDecoyCode == TRUE OR $dontDecoyCode == 'on'){
	$random_data_encoded = '';
}
list($num1,$num2,$num3) = random_num();
// OBFUSCATED ONCE
$retrieve_data_encoded_array[] = base64_encode('function '.$PXN1YnN0ci.'($a,$b){$c=array(888,'.strlen($verify_checksum_encoded).','.strlen($hash).','.strlen($data_encoded).');if($b=='.$num2.'){$d=substr($a,$c[0]+$c[1],$c[2]);}elseif($b=='.$num1.'){$d=substr($a,$c[0],$c[1]);}elseif($b=='.$num3.'){$d=trim(substr($a,$c[0]+$c[1]+$c[2]));}return$d;}');
// OBFUSCATED TWICE
$retrieve_data_encoded_array[] = base64_encode('function '.$PXN1YnN0ci.'($a,$b){$c=array(888,'.strlen($verify_checksum_encoded).','.strlen($hash).','.strlen($data_encoded).');$k=gzinflate(base64_decode("KyrNTcosKQYA"));$O0O0=$k[6].$k[1].$k[3].$k[6].$k[5].$k[0];$O0=$k[5].$k[0].$k[4].$k[2];if($b=='.$num2.'){$d=$O0O0($a,$c[0]+$c[1],$c[2]);}elseif($b=='.$num1.'){$d=$O0O0($a,$c[0],$c[1]);}elseif($b=='.$num3.'){$d=$O0($O0O0($a,$c[0]+$c[1]+$c[2]));}return$d;}');
// OBFUSCATED THRICE
$retrieve_data_encoded_array[] = base64_encode('function '.$PXN1YnN0ci.'($a,$b){$c=array(888,'.strlen($verify_checksum_encoded).','.strlen($hash).','.strlen($data_encoded).');$k=gzinflate(base64_decode("KyrNTcosKQYA"));$O0O0=$k[6].$k[1].$k[3].$k[6].$k[5].$k[0];$O0="$k[5]$k[0]$k[4]$k[2]";if($b=='.$num2.'){$d=$O0O0($a,$c[0]+$c[1],$c[2]);}elseif($b=='.$num1.'){$d=$O0O0($a,$c[0],$c[1]);}elseif($b=='.$num3.'){$d=$O0($O0O0($a,$c[0]+$c[1]+$c[2]));}return$d;}');
$retrieve_data_encoded = $retrieve_data_encoded_array[rand(0,count($retrieve_data_encoded_array)-1)];
// DEMO
if(isset($DEMO) AND $DEMO == TRUE){
	$retrieve_data_encoded = base64_encode('die("<tt>Reverse engineering of this file is strictly prohibited.");');
}

// OUTPUT PART 1
if (function_exists('hash')) {
	$checksum = ' Checksum: '.hash("$CHECKSUMTYPE",$random_data_encoded); // PHP 5.1 ONLY
	#$checksum = ' Checksum: '.$CHECKSUMTYPE($random_data_encoded); // NOT SAFE FOR crc32 IN CASE OF: checksums will result in negative integers
}else{
	if($CHECKSUMTYPE=='md5'){// http://php.net/manual/en/mhash.constants.php 
		$checksum = ' Checksum: '.bin2hex(mhash(MHASH_MD5, $random_data_encoded));
	}elseif($CHECKSUMTYPE=='sha1'){
		$checksum = ' Checksum: '.bin2hex(mhash(MHASH_SHA1, $random_data_encoded));
	}elseif($CHECKSUMTYPE=='whirlpool'){
		$checksum = ' Checksum: '.bin2hex(mhash(MHASH_WHIRLPOOL, $random_data_encoded));
	}else{ // crc32
		$checksum = ' Checksum: '.bin2hex(mhash(MHASH_CRC32, $random_data_encoded));
	}
}
if ( isset($_POST['copyright']) AND $_POST['copyright'] != '') {
	$COPYRIGHT = '/* '.$_POST['copyright'].$checksum.' */ ';
}else{
	$COPYRIGHT = '/* '.$COPYRIGHT.$checksum.' */ ';
}
$out  = '<?php '.$COPYRIGHT;

// ###########################################################################################################
// SECURITY UPDATE START 01.04.2016
	#$out .= '$'.$x5rb7Bygi1.'=file(__FILE__);';
	// http://php.net/manual/de/function.eval.php#87296
	$security_update = remove_equalsign_ending(
	base64_encode('$'.$x5rb7Bygi1.'=file(preg_replace("@\(.*\(.*$@","",__FILE__));
if(preg_replace("@\(.*\(.*$@","",__FILE__)==__FILE__ or
preg_replace("@\(.*\(.*$@","",__LINE__) != 3)die("<tt>ERROR");'));
	$out .= 'eval(base64_decode("'.$security_update.'"));';
	$out .= 'eval(base64_decode("'.$retrieve_data_encoded.'"));';
// SECURITY UPDATE ENDE
// ###########################################################################################################

$out .= 'eval(base64_decode('.$PXN1YnN0ci.'($'.$x5rb7Bygi1.'[0],'.$num1.')));';
$out .= 'eval('.$IiwkZyk7JGQ9.'('.$PXN1YnN0ci.'($'.$x5rb7Bygi1.'[0],'.$num3.'),'.$PXN1YnN0ci.'($'.$x5rb7Bygi1.'[0],'.$num2.'),$'.$x5rb7Bygi1.'));';

// REPLACE
$stopper = '__halt_compiler();';
$search  = base64_encode('888');
$replace = base64_encode((strlen($out)+strlen($random_data_encoded)+strlen($stopper)));
$out     = str_replace($search, $replace, $out);

// CHECK FOR SYSTEM ERROR
if ( strlen($out)+strlen($random_data_encoded)+strlen($stopper) > 999){
	die('<br><code><b>SYSTEM-ERROR</b>: The "Copyright Tag" is to long! '.
      '<xmp>'.$COPYRIGHT.'</xmp>'.strlen($random_data_encoded).
      '</code>'); // FIX System-Error 28.09.2016
}

// CHECKSUM
if (function_exists('hash')) {
	$hash = hash("$CHECKSUMTYPE",$out); // PHP 5.1 ONLY
	#$hash = $CHECKSUMTYPE($out); // NOT SAFE FOR crc32 IN CASE OF: checksums will result in negative integers
}else{
	if($CHECKSUMTYPE=='md5'){// http://php.net/manual/en/mhash.constants.php 
		$hash = bin2hex(mhash(MHASH_MD5, $out));
	}elseif($CHECKSUMTYPE=='sha1'){
		$hash = bin2hex(mhash(MHASH_SHA1, $out));
	}elseif($CHECKSUMTYPE=='whirlpool'){
		$hash = bin2hex(mhash(MHASH_WHIRLPOOL, $out));
	}else{ // crc32
		$hash = bin2hex(mhash(MHASH_CRC32, $out));
	}
}

// OUTPUT PART 2
$out .= $stopper;
$out2 = $random_data_encoded.$verify_checksum_encoded.$hash.$data_encoded;

// WRITE
if ( !isset($_POST['encryption']) ) {
	// PLUGIN WORKER (e.g. OBFUSCATOR or LOOPER)
	$data2 = hook('plugin_worker',$data2,@$_POST['plugin']);
	$out = "<?php ".$COPYRIGHT.$data2;
  if (file_exists('plugins/g_compiler.php'))$out=$data2; // ADD g_compiler.php PLUGIN SUPPORT
	$out2 = '';
}
make_dir($dir.$REPOSITORY);
if ($outfile == 'o..php' AND $data != '') {
	$outfile = 'o.'.$_POST['filename'].'.php';
}
clean_up($CLEANUP); // delete_old_repository_files 
if ($data != '') {
	if(isset($DEMO) AND $DEMO == TRUE){
		$out = str_replace('Checksum: ', "Checksum:".chr(9), $out);
		$out = str_replace('<?php ', "<?PHP ", $out);
		$out2 .= base64_encode($out);
	}
	$f = fopen($dir.$REPOSITORY.$outfile,'wb'); 
	fwrite($f,$out.$out2,strlen($out.$out2)); 
	fclose($f); 
}else{
	$out='';
	$out2='';
}

// OUTPUT
$rows=' rows="5"';
$cols='';
if ( isset($_GET['urlcontroller']) ) {
	$rows='';
	$cols=' style="width:100%;height:500px;cursor:not-allowed;"';
	echo '<br>';
	if($exitError!=''){
		echo '
			<h2>PHP Encoder &amp; Obfuscator</h2>
			<p>If you want to process your Script by URL-Controller-Interface use the following syntax:</p>
			<p><code><b>'.$url.'?file=filename&urlcontroller=true</b></code></p>
			<p>Prozessed file will be shown on screen and saved in repository named:</p>
			<p><code><b>o.filename.php</b></code></p>
			<p>If you want to work with other config presets than default, use following syntax:</p>
			<p><code><b>'.$url.'?file=filename&cfg=your-config-name&urlcontroller=true</b></code></p>
		';
		die($exitError);}
}

// OUTPUT CODE
if ( isset($out) AND $out != '' AND !isset($ERROR) ) {
	echo '
	<div class="well well-sm"><h4><b>Preview only</b> (use encoded PHP Code from Repository Direcory, but beware: do not make any changes)</h4>
	<span style="display:block!important;" class="hint--error hint--left hint--rounded hint--bounce" data-hint="3. Use Code from Repository!">
	<textarea ';
	if(isset($DEMO) AND $DEMO == TRUE){ /* http://ksylvest.github.io/jquery-growl/ */
		echo ' onfocus="$.growl.error({title:\'DEMO-MODE\',message:\'PHP code does not work!!\'});" ';
	}
	echo 'onclick="this.focus();this.select()" class="form-control"'.$rows.$cols.' readonly="readonly">'.
	$out.$out2.'</textarea>
	</span>
	</div>';
}

// TEST
if(isset($DEBUG) and $DEBUG=='adilbo'){
	echo "<p><tt><b>\x54\162\x79\40\x74\157\x20\166\x69\145\x77\40\x43\157\x64\145\x20\141\x74</b>:</p><p><a target='1' href='\x68\164\x74\160\x3a\57\x2f\165\x6e\160\x68\160\x2e\156\x65\164'>\x75\156\x70\150\x70\56\x6e\145\x74</a> & <a target='2' href='\x68\164\x74\160\x3a\57\x2f\144\x64\145\x63\157\x64\145\x2e\143\x6f\155\x2f\160\x68\160\x64\145\x63\157\x64\145\x72\57'>\x64\144\x65\143\x6f\144\x65\56\x63\157\x6d</a> & <a target='3' href='\x68\164\x74\160\x3a\57\x2f\152\x6f\156\x68\142\x75\162\x6e\62\x2e\146\x72\145\x65\150\x6f\163\x74\151\x61\56\x63\157\x6d\57\x64\145\x63\157\x64\145\x2f'>\x50\110\x50\40\x44\145\x6f\142\x66\165\x73\143\x61\164\x6f\162</a> & <a target='4' href='\x68\164\x74\160\x3a\57\x2f\144\x65\143\x6f\144\x65\55\x70\150\x70\145\x6e\143\x6f\144\x65\56\x65\165\x2e\160\x6e\57'>\x70\150\x70\145\x6e\143\x6f\144\x65\40\x44\145\x63\157\x64\145\x72</a> & <a target='5' href='\x68\164\x74\160\x3a\57\x2f\63\x76\64\x6c\56\x6f\162\x67\57'>\x50\110\x50\40\x53\150\x65\154\x6c</a></p></tt>";
}

// FOOTER
if ( ((isset($_POST['encryption']) AND $out != '') OR
	(isset($_GET['file']) AND $out != '')) AND $DEBUG != 'adilbo' ) {
	echo '
		<p><div class="alert alert-warning"><b>
		? ATTENTION &mdash; With enough time and effort, this encryption can be undone !</b><br>
		This PHP Encoder &amp; Obfuscator offers a very high level of protection for your PHP work, 
		but an absolute protection can not be guaranteed because of the open architecture of PHP.
		If the PHP compiler of your customer can see the code (even if obscured), 
		then your customer can view the source code in an editor as well.
		A software pirate might try to discover the source code and 
		deobfuscate it to get nearer to the unobfuscated PHP source code.
		By using different techniques, these PHP Encoder &amp; Obfuscator will surely discourage every 
		software pirate to decipher your software in order to manipulate the source code or 
		even steal it. So we force the thieves to think twice and better buy your script.
		</div></p>
	';
}

/* EOF - END OF FILE */
