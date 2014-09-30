<?php
$root = $_SERVER ['DOCUMENT_ROOT'];
$dir = $root . '/Installer/';
if (PHP_OS === 'Windows') {
	exec ( "rd /s /q {$dir}" );
	
	echo 'The Installer Directory Has Been Deleted';
	
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'public/';
	header("Location: http://$host$uri/$extra");
	exit;
} else {
	exec ( "rm -rf {$dir}" );
	
	echo 'The Installer Directory Has Been Deleted';
	
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'public/';
	header("Location: http://$host$uri/$extra");
	exit;
}
?>