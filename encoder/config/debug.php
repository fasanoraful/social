<?PHP

/* ---------------------------------------------------------------------------------------------------- */
// DEBUG
/* ---------------------------------------------------------------------------------------------------- */
	include('default.php');
	$DEBUG                     = 'Y';
	$CDN                       = FALSE;
	$dontDecoyCode             = TRUE;
	$expirationDays            = 1; // -1 FOR YESTERDAY
	$_POST['dontMinify']       = 'on';
	$_POST['doLockDate']       = 'on';
	$_POST['lockDate']         = date('d.m.Y',time()+86400*$expirationDays);
	$_POST['doLockDomain']     = 'on';
	$_POST['lockDomain']       = '('.$_SERVER['SERVER_NAME'].'|localhost)';
	$_POST['checksumType']     = 'crc32';
	$_POST['dontScrambleVars'] = 'on';
	$_POST['encryption']       = 'on';
/* ---------------------------------------------------------------------------------------------------- */
