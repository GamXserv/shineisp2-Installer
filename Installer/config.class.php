<?php
class Config {
	
	/* YOUR APP'S CONFIGS */
	const APP_NAME = "ShineISP"; // put here the name of your app
	const APP_VERS = "2.0"; // put here the version of your app
	const APP_CONFIG_FILE_PATH = "/config/autoload";
	const APP_CONFIG_FILE = "pdo.local.php"; // put here the config file of your app
	const APP_EMAIL_ADMIN = "andreapaciolla@gmail.com"; // put here the your email if you wanna receive an email after the app has been installed
	const APP_SHOW_PRIVACY = true; // true: muph will show the policy privacy. FALSE: muph will ignore any privacy policy
	                               
	// const APP_CONFIG_FILE_PATH = "";
	                               
	// write the php function you wanna use to encrypting the admin pwd into the db
	const APP_ENCRYPT_PWD = "md5"; // md5 - sha1 - crc32
	public static $requiredOptions = array (
			'writableDir' => true,
			'phpMin' => 5,
			'pdo' => true,
			'gd' => true,
			'curl' => true,
			'modRewrite' => false,
			'mysqli' => true,
			'mbstring' => true,
			'mcrypt' => true,
			'exec' => true,
			'safe_mode' => true,
			'register_globals' => true,
			'Zlib' => true 
	);
	
	/* DON'T TOUCH ANYTHING BELOW, PLEASE */
	const DIR_SEP = DIRECTORY_SEPARATOR;
	const ROOT = __FILE__;
	const TPL_DIR = "partials";
	
	// const LANG = "0"; // not present before version 2 which is not reached yet
	public static $MUPH = array (
			"author" => "Kurt Walakovits",
			"author_email" => "administrator@gamxserv.com",
			"author_web" => "gamxserv.com",
			"version" => "1.01",
			"released_on" => "25th sept, 2014",
			"updated_on" => "never" 
	);
}