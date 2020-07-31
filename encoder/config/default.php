<?PHP
/* ---------------------------------------------------------------------------------------------------- */
// DEFAULT-CONFIG
/* ---------------------------------------------------------------------------------------------------- */
$DEMO             = FALSE; // HINT: if TRUE then $REPOSITORY = 'repository/-/'
$DEBUG            = '';   // 'YES' or leave empty for NO! - NEVER set this to 'YES' on your live webserver!
$CDN              = TRUE; // TRUE = Use CDN for Bootstap & jQuery / FALSE = load local saved files
$REPOSITORY       = 'repository/'.crc32($_SERVER['SERVER_NAME']).'/'; // ATTENTION: put slash / at the end!
$COPYRIGHT        = array(
  'adilbo PHP Encoder',
  'Copyright &copy; '.date('Y').' - All rights reserved.',
  'Do not change this code, or your script will not work.',
  'Reverse engineering of this file is strictly prohibited.',
  'File protected by copyright law and provided under license.',
  'All Rights Reserved. This file may not be redistributed in whole or significant part.',
  'I spent a lot of time developing this so i\'m kindly asking you to respect my work.',
);
$EXPERTMODE       = FALSE; // NEW
$CLEANUP          = 1; // IF > 0 THEN DELETE REPOSITORY "o." FILES OLDER THAN n MINUTES!
$dontDecoyCode    = FALSE; // FALSE = Default 
$expirationDays   = 30;    // Default Date (Today + $expirationDays) if you use 'Lock Date' Function
$lockErrorAlert   = TRUE;  // TRUE = enable user errors / FALSE = disable user errors	
$hashErrorAlert   = 'Code manipulation detected';      // Message on Hash Error
$dateErrorAlert   = 'Software license expired';        // Message on expired script
$domainErrorAlert = 'Code not allowed on this domain'; // Message on invalid host
$ipErrorAlert     = 'Code not allowed on this ip';     // Message on invalid ip // NEW

// Remove # at appropriate line for uncomment
#$_POST['dontMinify']        = 'on'; // OR '' for NO
#$_POST['doLockDate']        = 'on'; // HINT: IF SET USE doLockDate & lockDate
#$_POST['lockDate']          = date('d.m.Y',time()+86400*$expirationDays); // OR '16.11.2016'
#$_POST['doLockDomain']      = 'on'; // HINT: IF SET USE doLockDomain & lockDomain
#$_POST['lockDomain']        = '('.$_SERVER['SERVER_NAME'].'|localhost)'; // OR $_SERVER['SERVER_NAME']
#$_POST['dontScrambleVars']  = 'on'; // OR '' for NO
#$_POST['checksumType']      = 'md5'; // 'whirlpool' OR 'sha1' OR 'md5' OR 'crc32' 
#$_POST['encryption']         = 'on'; // OR '' for NO encryprion

// PLUGINS
#$_POST['plugin']['a_obfuscator'] = 'Variables & Functions'; // OR SET TO "Variables" OR "Functions"
// BETA "Strings" OR "Variables & Strings" OR "Functions & Strings" OR "Variables, Functions & Strings"
#$_POST['plugin']['c_encoder']    = 'Use Dynamic Encoder Algorithm'; // OR SET TO ''
#$_POST['plugin']['e_looper']     = '12'; // OR SET TO '', 'number' OR 'Random' for number from 1 to 128
