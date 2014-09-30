<?php
$root = $_SERVER ['DOCUMENT_ROOT'];
$dir = $root . '/Installer/';
if (PHP_OS === 'Windows') {
	exec ( "rd /s /q {$dir}" );
} else {
	exec ( "rm -rf {$dir}" );
}
?>