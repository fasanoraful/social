<?PHP
/*
 _____ _____ _____    ____                _ 
|  _  |  |  |  _  |  |   _| ___ ___ ___ _| |___ ___ 
|   __|     |   __|  | |-_ | | |  _| . | . | -_|  _|
|__|  |__|__|__|     |____||_|_|___|___|___|___|_|

TODO
- IF ENCODE MORE THAN ONE FILE YOU CAN GET "Cannot redeclare function error"
  http://php.net/manual/de/language.oop5.paamayim-nekudotayim.php#94976
- IF people use mixed code (PHP & HTML) it should give a warning
*/

# FOR DEBUGGING ONLY: SET ERROR REPORTING ON
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(0);


/**
 * 
 * ============================================================================
 * (c) 2014-2015 www.adilbo.com - All rights reserved - Alle Rechte vorbehalten
 * ----------------------------------------------------------------------------
 *
 * Requirements: This script works as described as of PHP 4.2
 *
 * COPYRIGHT
 * This software is exclusively sold at CodeCanyon.net.  If you have downloaded 
 * this from another website or received it from someone else than me, then you 
 * are engaged in an illegal activity. You must erase this software immediately 
 * or buy a proper license from codecanyon.net/user/adilbo/portfolio?ref=adilbo
 * ============================================================================
 *
 */

// ENVIRONMENT SETUP
ini_set('memory_limit', '-1');
set_time_limit(0);
$dir = dirname(__FILE__).'/';
// ADD - Use of Port in Form (index.php) 19.02.2016
$port      = $_SERVER['SERVER_PORT'];
$protocol  = empty($_SERVER['HTTPS']) ? 'http' : 'https';
$disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";
// ADD - Use of IP and https in Form (index.php) 19.02.2016
$url = $protocol.'://'.$_SERVER['SERVER_NAME'].$disp_port.strtok($_SERVER["REQUEST_URI"],'?');

// HEADER
if (isset($CDN) and $CDN == TRUE){ // Load jQuery from a CDN and have a fallback to own host
	$CDN_HTML  = '<script src="https://code.jquery.com/jquery-2.0.2.min.js" type="text/javascript"></script>';
	$CDN_HTML .= '<script type="text/javascript">window.jQuery || document.write(unescape(\'%3Cscript 
	 type="text/javascript" src="resources/jquery-2.0.2.min.js"%3E%3C/script%3E\'))</script>';
	$CDN_HTML .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">';
	$CDN_HTML .= '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>';
	$CDN_HTML .= '<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.5/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">';
	$CDN_HTML .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.5/js/bootstrap-dialog.min.js"></script>';
}else{
	$CDN_HTML  = '<script src="'.$url.'resources/jquery-2.0.2.min.js" type="text/javascript"></script>';
	$CDN_HTML .= '<link rel="stylesheet" href="'.$url.'resources/bootstrap.min.css">';
	$CDN_HTML .= '<script src="'.$url.'resources/bootstrap.min.js"></script>';
	$CDN_HTML .= '<link href="'.$url.'resources/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">';
	$CDN_HTML .= '<script src="'.$url.'resources/bootstrap-dialog.min.js"></script>';
	
}
echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="utf-8">';
echo '<title>PHP Encoder &amp; Obfuscator</title>';
echo $CDN_HTML;
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">';
echo '<script src="'.$url.'resources/jquery.growl.js" type="text/javascript"></script>';
echo '<link href="'.$url.'resources/jquery.growl.css" rel="stylesheet" type="text/css">';
echo '<link href="'.$url.'resources/hint.css" rel="stylesheet"><!-- kushagragour.in/lab/hint -->';
echo '<link rel="stylesheet" href="'.$url.'resources/style.css">';
echo '<link rel="icon" href="'.$url.'resources/favicon.png" type="image/png">';
echo '</head>';
echo '<body>';
echo '<div class="container">';

// CHECK PHP VERSION
if ( version_compare(phpversion(), '4.2') < 0 ) {
	die('<br><code><b>ERROR</b>: PHP 4.2 or greater is required!</code>');
}

// CHECK INDIVIDUAL SETUP
if (isset($_GET['cfg']) and
	(strpos($_GET['cfg'] ,'.') == TRUE OR 
	strpos($_GET['cfg'] ,'/') == TRUE OR 
	strpos($_GET['cfg'] ,'\\') == TRUE) ){
	 die('<br><code><b>ERROR</b>: Configuration preset "'.$_GET['cfg'].'" not allowed! (don\'t use dots or slashes in filename)</code>');
}elseif(!isset($_GET['cfg'])){
	$_GET['cfg'] = 'default';
}elseif (isset($_GET['cfg']) AND !file_exists ($dir.'config/'.$_GET['cfg'].'.php')){
	die('<br><code><b>ERROR</b>: File not found!</code>');
}

// LOAD CONFIG
if(error_reporting()==0){
	(@include_once($dir.'config/'.$_GET['cfg'].'.php')) OR die('<br><code><b>ERROR</b>: Configuration preset "'.$_GET['cfg'].'.php" not found!</code>');
}else{
	include_once($dir.'config/'.$_GET['cfg'].'.php');
}

# FOR DEBUGGING ONLY: VIEW POST & GET VARs ON SCREEN
if (isset($DEBUG) AND $DEBUG != '') {
	print'<br><pre><b>POST</b><xmp>';print_r($_POST);print'</xmp><b>GET</b><br>';print_r($_GET);print'</pre>';
}

// LOAD LIB
if(error_reporting()==0){
	(@include_once($dir.'bin/lib.php')) OR die('<br><code><b>ERROR</b>: File "lib.php" not found!</code>');
}else{
	include_once($dir.'bin/lib.php');
}

// LOAD APP
if(error_reporting()==0){
	(@include_once($dir.'bin/encoder.php')) OR die('<br><code><b>ERROR</b>: File "encoder.php" not found!</code>');
}else{
	include_once($dir.'bin/encoder.php');
}

// EOF
echo "</div><div class=\"\x70\165\x6c\154\x2d\162\x69\147\x68\164\"><p class=\"\x74\145\x78\164\x2d\155\x75\164\x65\144\"> <a href=\"javascript:;\" onclick=\"BootstrapDialog.show({title:'Terms and Conditions of Use',message: $('<div></div>').load('".$url."tos.htm')});\">TOS</a> &mdash; \x70\157\x77\145\x72\145\x64\40\x62\171<a target=\"_blank\" href=\"\x68\164\x74\160\x3a\57\x2f\141\x64\151\x6c\142\x6f\56\x63\157\x6d\">\x61\144\x69\154\x62\157</a></p></div>
<script>
  $(function () {
    /* INITIALIZE popover with jQuery */
	$('[data-toggle=\"popover\"]').popover(); 
	/* TOGGLE content hidden/show */  
    $('.toggle').click(function (event) {
      event.preventDefault();
      var target = $(this).attr('title');
      $(target).toggleClass('hidden show');
    });
  });
</script>
</body></html>";
