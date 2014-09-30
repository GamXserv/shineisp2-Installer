<?php

// This installation script is a web frontend and a wrapper to [composer](https://getcomposer.org)
// Shrunk with http://code.google.com/p/php-compressor
session_start ();
date_default_timezone_set ( 'UTC' );
error_reporting ( E_ALL ^ E_NOTICE );
$serviceUrl = 'http://gamxserv.com/';

if (get_magic_quotes_gpc ()) {
	function stripslashes_gpc(&$value) {
		$value = stripslashes ( $value );
	}
	array_walk_recursive ( $_GET, 'stripslashes_gpc' );
	array_walk_recursive ( $_POST, 'stripslashes_gpc' );
}
foreach ( $_GET as $k => $v ) {
	$_GET [str_replace ( 'amp;', '', $k )] = preg_replace ( '/\W, /', '', $v );
}
$LL = array ();
$body = '';
$LL ['de'] = array (
		'install' => 'installieren',
		'do_you_really_want_to' => 'möchten Sie wirklich',
		'the_package' => 'das Paket',
		'uninstall' => 'deinstallieren',
		'back' => 'zurück',
		'next' => 'vor',
		'search' => 'suche',
		'no_description_available' => 'keine Beschreibung verfügbar',
		'please_wait' => 'bitte warten' 
);
$LL ['en'] = array (
		'install' => 'install',
		'do_you_really_want_to' => 'do you really want to',
		'the_package' => 'the package',
		'uninstall' => 'uninstall',
		'back' => 'back',
		'next' => 'next',
		'search' => 'search',
		'no_description_available' => 'no description available',
		'please_wait' => 'please wait' 
);
$lang = bl ( array (
		'de',
		'en' 
), 'en' );
$html = array (
		'<!doctype html>
<html>
<head>
<title></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link
	href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css"
	rel="stylesheet">
<link rel="stylesheet" href="Installer/css/main.css" type="text/css">
<style>
/* custom styles */

#spinner{
	position :absolute;
	display:none;
	z-index: 1;
	top: 40%;
	left: 50%;
	margin-left:-64px;
}

</style>

<link rel="stylesheet" type="text/css" href="vendor/isp1-installer/jquery-ui/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="vendor/isp1-installer/jquery-ui/jquery.min.js"></script>
<script type="text/javascript" src="vendor/isp1-installer/jquery-ui/jquery-ui.min.js"></script>

<script type="text/javascript">
if (typeof jQuery == "undefined") {
    document.writeln(unescape("%3Crel=\'stylesheet\' type=\'text/css\' href=\'http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css\' /%3E"));
    document.writeln(unescape("%3Cscript src=\'http://code.jquery.com/jquery-1.11.1.min.js\' type=\'text/javascript\'%3E%3C/script%3E"));
    document.writeln(unescape("%3Cscript src=\'http://code.jquery.com/ui/1.11.1/jquery-ui.min.js\' type=\'text/javascript\'%3E%3C/script%3E"));
}
</script>

<script>
$(document).ready(function()
{
	$("button").each(function()
	{
		if($(this).attr("rel"))
		{
			$(this).button({
				icons:{primary:"ui-icon-"+$(this).attr("rel")}, 
				text:false
			})
		}
	});

	$(".package").button().on("click", function()
	{
		var c = $(this).text() === "' . L ( 'install' ) . '",
			q = confirm("' . L ( 'do_you_really_want_to' ) . ' "+$(this).text()+" ' . L ( 'the_package' ) . ' \""+$(this).data("name")+"\"?");

		if (q)
		{
			
			$("#spinner").show();
			$.post("composer.php?' . http_build_query ( $_GET ) . '",
			{
				action: c,
				name: $(this).data("name")
				/*version: $(this).data("version")*/
			},
			function(d) {
				$("#spinner").hide();
				alert(d);
			});
			
			var options;
			if (c) {
				options = {
					label: "' . L ( 'uninstall' ) . '",
					icons: {
						primary: "ui-icon-trash"
					}
				};
			} else {
				options = {
					label: "' . L ( 'install' ) . '",
					icons: {
						primary: "ui-icon-disk"
					}
				};
			}
			$(this).button("option", options);
		}
	});
});
</script>
</head>
<body>
	<div id="wrap">
		
<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">
              ShineISP 
              | v2.0              </a>
		</div>
	</div>
</div>				<div class="container muph-flat-bg">
    <div class="page-header">
      <h1> ShineISP 2 Composer Installer </h1>
    </div>
    <p class="lead">Checked to see if composer was installed. If so, click the Icon Button and go ahead with the installation process. After Composer installed or updated click the next button to continue.</p>
    
',
		'

    <a href="javascript:void(0)" onClick="document.location.reload(true)">
        <button class="btn btn-lg btn-default pull-left"><i class="fa fa-refresh"></i> Refresh</button>
    </a>
    
    <a href="Installer/index.php?step=1">
    	<button class="btn btn-lg btn-success pull-right">Next</button>
    </a>
</div>   	
	</div>
	<div id="footer">
	<div class="container">
		<p>Thanks to Twitter Bootstrap and Mustache Template Engine</p>
	</div>
</div></body>

</html>
' 
);
function crpt($pass, $salt) {
	if (defined ( 'CRYPT_BLOWFISH' ) && CRYPT_BLOWFISH) {
		$msalt = '$2a$07$' . substr ( md5 ( $salt ), 0, 22 ) . '$';
		return $salt . ':' . md5 ( crypt ( $pass, $msalt ) );
	} else {
		return $salt . ':' . md5 ( md5 ( md5 ( md5 ( md5 ( md5 ( md5 ( $salt . $pass ) ) ) ) ) ) );
	}
}
function bl($arr = array('en'), $default = 'en') {
	$al = strtolower ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] );
	$ua = strtolower ( $_SERVER ['HTTP_USER_AGENT'] );
	foreach ( $arr as $k ) {
		if (strpos ( $al, $k ) === 0 || strpos ( $al, $k ) !== false) {
			return $k;
		}
	}
	foreach ( $arr as $k ) {
		if (preg_match ( "/[\[\( ]{$k}[;,_\-\)]/", $ua )) {
			return $k;
		}
	}
	return $default;
}
function L($str) {
	global $LL, $lang;
	if (! empty ( $lang ) && isset ( $LL [$lang] [$str] )) {
		return $LL [$lang] [$str];
	} else {
		$LL ['x'] [$str] = 1;
		return str_replace ( '_', ' ', $str );
	}
}
function download($url, $file = false) {
	$ch = curl_init ();
	$timeout = 5;
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	$data = curl_exec ( $ch );
	curl_close ( $ch );
	if ($file) {
		file_put_contents ( $file, $data );
		return true;
	} else {
		return $data;
	}
}
if (! is_writable ( __DIR__ )) {
	exit ( L ( 'directory_is_not_writable' ) );
}
if (! file_exists ( 'index.php' )) {
	file_put_contents ( 'index.php', '<?php header(\'location: public/\');' );
}
if (! file_exists ( 'projects' )) {
	mkdir ( 'projects', 0777 );
}
if (version_compare ( PHP_VERSION, '5.3.3' ) == - 1) {
	exit ( PHP_VERSION . ' ' . L ( 'this_php_version_is_too_low' ) );
}
if (isset ( $_GET ['logout'] )) {
	unset ( $_SESSION ['ACCEPTED'] );
}
$installedPackages = array ();
if (! file_exists ( 'composer.phar' )) {
	download ( 'https://getcomposer.org/installer', 'composer-dl.inc' );
	echo $html [0] . '<h3>composer downloaded</h3><a href="composer.php">please reload</a><pre>';
	chmod ( 'composer-dl.inc', 0777 );
	include 'composer-dl.inc';
	exit ( '</pre>' . $html [1] );
} else {
	$installAction = 'self-update';
	passthru ( 'php composer.phar ' . $installAction . ' --no-interaction', $out );
	echo $out;
	@unlink ( 'composer-dl.inc' );
}
if (file_exists ( 'composer.json' )) {
	$composerJson = json_decode ( file_get_contents ( 'composer.json' ), true );
} else {
	$composerJson = array (
			'minimum-stability' => 'dev',
			'require' => array (
					'php' => '>=5.3.3' 
			) 
	);
}

//$suggestions = json_decode ( download ( $serviceUrl . '/?' . http_build_query ( $_GET ) ), true );
$suggestions = json_decode ( file_get_contents ( 'composer.json' ), true );
if (! empty ( $_POST ['action'] )) {
	
	if (file_exists ( 'composer.lock' )) {
		$lock = json_decode ( file_get_contents ( 'composer.lock' ), true );
		if (isset ( $lock ['packages'] )) {
			foreach ( $lock ['packages'] as $p ) {
				$installedPackages [$p ['name']] = $p;
			}
		}
		$installAction = 'update';
		putenv ( 'PATH=' . $_SERVER ['PATH'] );
		putenv ( 'COMPOSER_HOME=' . __DIR__ );
		putenv ( 'HOME=' . __DIR__ );
		passthru ( 'php composer.phar ' . $installAction . ' --no-interaction', $out );
		echo $out;
	}else{
		$installAction = 'install';
	}
		//$json = stripslashes ( json_encode ( $composerJson ) );
		//file_put_contents ( 'composer.json', $json );
		//chmod ( 'composer.json', 0777 );
		putenv ( 'PATH=' . $_SERVER ['PATH'] );
		putenv ( 'COMPOSER_HOME=' . __DIR__ );
		putenv ( 'HOME=' . __DIR__ );
		passthru ( 'php composer.phar ' . $installAction . ' --no-interaction', $out );
		echo $out;
		foreach ( array (
				'vendor'
		) as $dir ) {
			$iterator = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $dir ) );
			foreach ( $iterator as $item ) {
				chmod ( $item, 0777 );
			}
		}
		unlink ( '.htaccess' );
		exit ();
	
}
$body .= '<table class="table table-striped table-condensed table-hover"><tbody><form id="packageheader" method="get" action="composer.php">' . '<span style="float:right">' . (! empty ( $suggestions ['stat'] ['back'] ) ? '<button type="button" rel="circle-triangle-w" type="button">' . L ( 'back' ) . '</button>' : '') . (! empty ( $suggestions ['stat'] ['next'] ) ? '<button type="button" rel="circle-triangle-e" type="button">' . L ( 'next' ) . '</button>' : '') . '</span>' . '</form>';


	$body .= '<tr><td>' . '<button class="package btn btn-lg btn-default pull-left" id="cb' . $suggestions ['name'] . '" type="button" ' . 'data-name="' . $suggestions ['name'] . '" data-version="' . $suggestions ['version'] . '" ' . 'rel="' . (isset ( $composerJson ['require'] [$k] ) ? 'trash">' . L ( 'uninstall' ) : 'disk">' . L ( 'install' )) . '</button>' . '</td> <td>' . (empty ( $suggestions ['description']  ) ? L ( 'no_description_available' ) : $suggestions  ['description']) . '</td></tr>';
	

$body .= '</tbody></table>';
$body .= '<img id="spinner" src="vendor/isp1-installer/images/spinner.gif" title="' . L ( 'please_wait' ) . '" />';
echo $html [0] . $body . $html [1];