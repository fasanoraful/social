<?PHP

/* ---------------------------------------------------------------------------------------------------- */
// PARANOID
/* ---------------------------------------------------------------------------------------------------- */
	include('default.php');
	$expirationDays            = 365; // one Year
	$lockErrorAlert            = FALSE;
	$_POST['doLockDate']       = 'on';
	$_POST['lockDate']         = date('d.m.Y',time()+86400*$expirationDays);
	$_POST['doLockDomain']     = 'on';
	$_POST['lockDomain']       = '('.$_SERVER['SERVER_NAME'].'|localhost)';
/* ---------------------------------------------------------------------------------------------------- */