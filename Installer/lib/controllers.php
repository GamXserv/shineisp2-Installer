<?php
class ControllerDB {
	protected static $dbname;
	protected static $dbuser;
	protected static $dbpass;
	protected static $dbprefix;
	protected static $dbhost;
	protected static $_db;
	protected static $_admusr;
	protected static $_admpwd;
	protected static $_admeml;
	protected static $_admnae;
	public function __construct() {
		@session_start ();
		// getting the form db results from the first step
		if (isset ( $_POST ['dbopt'] )) {
			$this->init ();
		} else {
			self::$dbname = (isset ( $_SESSION ['dbname'] )) ? $_SESSION ['dbname'] : null;
			self::$dbuser = (isset ( $_SESSION ['dbuser'] )) ? $_SESSION ['dbuser'] : null;
			self::$dbpass = (isset ( $_SESSION ['dbpass'] )) ? $_SESSION ['dbpass'] : null;
			self::$dbprefix = (isset ( $_SESSION ['dbprefix'] )) ? $_SESSION ['dbprefix'] : null;
			self::$dbhost = (isset ( $_SESSION ['dbhost'] )) ? $_SESSION ['dbhost'] : null;
			self::$_admusr = (isset ( $_POST ['adminusr'] )) ? $_POST ['adminusr'] : null;
			self::$_admpwd = (isset ( $_POST ['adminpwd'] )) ? $_POST ['adminpwd'] : null;
			self::$_admeml = (isset ( $_POST ['adminema'] )) ? $_POST ['adminema'] : null;
			self::$_admnae = (isset ( $_POST ['adminnam'] )) ? $_POST ['adminnam'] : null;
		}
	}
	public function init() {
		self::$dbname = trim ( $_POST ['dbname'] );
		self::$dbuser = trim ( $_POST ['dbuser'] );
		self::$dbpass = trim ( $_POST ['dbpass'] );
		self::$dbprefix = trim ( $_POST ['dbprefix'] );
		self::$dbhost = trim ( $_POST ['dbhost'] );
		$this->copyDbInfoIntoSession ();
	}
	public function copyDbInfoIntoSession() {
		$_SESSION ['dbname'] = self::$dbname;
		$_SESSION ['dbuser'] = self::$dbuser;
		$_SESSION ['dbpass'] = self::$dbpass;
		$_SESSION ['dbprefix'] = self::$dbprefix;
		$_SESSION ['dbhost'] = self::$dbhost;
	}
	public function setAdminCredentials($cryptFunction = false) {
		if (isset ( $_POST ['adminopt'] )) {
			self::$_admusr = $_POST ['adminusr'];
			self::$_admeml = $_POST ['adminema'];
			self::$_admnae = $_POST ['adminnam'];
			
			switch ($cryptFunction) {
				case "md5" :
					self::$_admpwd = md5 ( $_POST ['adminpwd'] );
					break;
				case "sha1" :
					self::$_admpwd = sha1 ( $_POST ['adminpwd'] );
					break;
				case "crc32" :
					self::$_admpwd = crc32 ( $_POST ['adminpwd'] );
					break;
				default :
					self::$_admpwd = $_POST ['adminpwd'];
					break;
			}
		}
	}
	public function createConnection() {
		if (self::$_db = new db ( "mysql:host=" . self::$dbhost . ";dbname=" . self::$dbname, self::$dbuser, self::$dbpass ))
			return true;
		else
			return false;
	}
	public function query($sql) {
		self::$_db->run ( $sql );
	}
	public function parseMuphVariables($string) {
		return str_replace ( array (
				"{adminUsername}",
				"{adminPassword}",
				"{adminEmail}",
				"{adminName}",
				"{prefix}",
				"{dbname}",
				"{dbuser}",
				"{dbpass}",
				"{dbhost}" 
		), array (
				self::$_admusr,
				self::$_admpwd,
				self::$_admeml,
				self::$_admnae,
				self::$dbprefix,
				self::$dbname,
				self::$dbuser,
				self::$dbpass,
				self::$dbhost 
		), $string );
	}
}

?>