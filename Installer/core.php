<?php

// load the config file
require_once "config.class.php";

// load the controller class
require_once "lib/class.db.php";

// load the controller class
require_once "lib/controllers.php";

// load the file class
require_once "lib/class.file.php";

// load the file lang ( this will be dynamic after )
// require_once "lang" . Config::DIR_SEP . "en.php";

// load all the dependencies
require_once "vendor" . Config::DIR_SEP . "autoload.php";

/*
 * ?step=undefined -> welcome ?step=1 ?step=2 ?step=3 -> summary
 */
class Router {
	protected $route;
	protected $tpl;
	protected $template;
	protected $hash;
	protected $lang;
	protected $_db;
	protected $_file;
	protected $_langID;
	
	/**
	 *
	 * Constructor Dependency Injection
	 *
	 * @param $tpl instanceof
	 *        	Mustache_Engine
	 * @param $lang instanceof
	 *        	Lang
	 * @param $lang instanceof
	 *        	Config
	 *        	
	 */
	public function __construct($tpl, $db, $file) {
		$this->tpl = ($tpl instanceof Mustache_Engine) ? $tpl : die ( "ERROR: You must pass only Mustache_Engine object." );
		// $this->lang = ($lang instanceof Lang) ? $lang : die("ERROR: You must pass only Lang object.");
		$this->_db = ($db instanceof ControllerDB) ? $db : die ( "ERROR: You must pass only ControllerDB object." );
		$this->_file = ($file instanceof File) ? $file : die ( "ERROR: You must pass only File object." );
		$this->route = isset ( $_GET ['step'] ) ? $_GET ['step'] : null;
	}
	public function check() {
		$this->languageApply ();
		switch ($this->route) {
			case 1 :
				$this->first ();
				break;
			case 2 :
				$this->second ();
				break;
			case 3 :
				$this->third ();
				break;
			default :
				$this->welcome ();
				break;
		}
	}
	public function welcome() {
		$this->template = file_get_contents ( Config::TPL_DIR . Config::DIR_SEP . "welcome.mustache" );
		$this->hash = $this->lang->hash ['WELCOME'];
		$this->requiring ();
		$this->getLangs ();
		$this->render ();
	}
	public function first() {
		$this->template = file_get_contents ( Config::TPL_DIR . Config::DIR_SEP . "1step.mustache" );
		$this->hash = $this->lang->hash ['STEP_1'];
		$this->render ();
	}
	public function second() {
		if ($this->firstStepApply ()) {
			
			$this->template = file_get_contents ( Config::TPL_DIR . Config::DIR_SEP . "2step.mustache" );
			$this->hash = $this->lang->hash ['STEP_2'];
			$this->render ();
		} else {
			header ( "Location: index.php?step=1&error=" . $_GET ['error'] );
		}
	}
	public function third() {
		if ($this->secondStepApply ()) {
			$this->template = file_get_contents ( Config::TPL_DIR . Config::DIR_SEP . "3step.mustache" );
			$this->hash = $this->lang->hash ['STEP_3'];
			$this->render ();
		} else {
			header ( "Location: index.php?step=2&error=" . $_GET ['error'] );
		}
	}
	public function firstStepApply() {
		if ($this->_db->createConnection ()) {
			clearstatcache (); // die("queries.sql");
			                   // chmod(dirname(Config::ROOT).Config::DIR_SEP."query.txt", 777);
			                   
			// TODO---Need to search to locate sql files
			$root = $_SERVER ['DOCUMENT_ROOT'];
			$directory1 = $root . '/module/Admin/data/';
			$directory2 = $root . '/module/Base/data/';
			$directory3 = $root . '/module/Cms/data/';
			$directory4 = $root . '/module/Customer/data/';
			$directory5 = $root . '/module/Dummy/data/';
			$directory6 = $root . '/module/Product/data/';
			if (file_exists ( $directory1 . "data.sql" )) {
				$this->_db->query ( $this->_db->parseMuphVariables ( file_get_contents ( $directory1 . "data.sql" ) ) );
				$this->_db->query ( $this->_db->parseMuphVariables ( file_get_contents ( $directory2 . "data.sql" ) ) );
				$this->_db->query ( $this->_db->parseMuphVariables ( file_get_contents ( $directory3 . "data.sql" ) ) );
				$this->_db->query ( $this->_db->parseMuphVariables ( file_get_contents ( $directory4 . "data.sql" ) ) );
				$this->_db->query ( $this->_db->parseMuphVariables ( file_get_contents ( $directory5 . "data.sql" ) ) );
				$this->_db->query ( $this->_db->parseMuphVariables ( file_get_contents ( $directory6 . "data.sql" ) ) );
				return true;
			} else {
				$this->logError ( "Cannot find the file " . $directory1 . "data.sql" );
				return false; // die("cannot find the file data.sql"); // file queries.sql does not exist
			}
		} else {
			$this->logError ( "Cannot connect to the db" );
			return false; // die("cannot connect"); // cannot connect to the db
		}
	}
	public function secondStepApply() {
		if ($this->_db->createConnection ()) {
			$this->_db->setAdminCredentials ( Config::APP_ENCRYPT_PWD );
			clearstatcache (); // die("queries.sql");
			                   // chmod( dirname(Config::ROOT) . Config::DIR_SEP . "credtest.txt", 777 );
			
			if (file_exists ( dirname ( Config::ROOT ) . Config::DIR_SEP . "credtest.txt" )) {
				$todo = $this->_db->parseMuphVariables ( file_get_contents ( "credtest.txt" ) );
				
				$this->_db->query ( $todo );
				return true;
			} else {
				$this->logError ( "cannot find the file credtest.txt" ); // file queries.sql does not exist
				return false;
			}
		} else {
			$this->logError ( "Cannot create connection" );
			return false;
		}
	}
	
	/**
	 * Create the terms for each required option
	 */
	public function requiring() {
		foreach ( Config::$requiredOptions as $option => $value ) {
			if ($value) { // is required?
				$result = $this->hash ["OPTION_WRONG"];
				
				if ($option == "writableDir" && is_writable ( $_SERVER ['DOCUMENT_ROOT'] . Config::APP_CONFIG_FILE_PATH )) // is about dir?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "pdo" && defined ( 'PDO::ATTR_DRIVER_NAME' )) // is about PDO?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "curl" && in_array ( 'curl', get_loaded_extensions () )) // is about cURL?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "gd" && count ( gd_info () )) // is about gd?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "phpMin" && (version_compare ( phpversion (), '5.3.23' ) >= 0)) // is about php min version?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "modRewrite" && in_array ( 'mod_rewrite', apache_get_modules () )) // is about apache mod rewrite?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "mysqli" && function_exists ( 'mysqli_connect' )) // is about mysqli?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "exec" && function_exists ( 'exec' )) // is about exec?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "mbstring" && extension_loaded ( 'mbstring' )) // is about mbstring?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "mcrypt" && extension_loaded ( 'mcrypt' )) // is about mcrypt?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "safe_mode" && ini_get ( 'safe_mode' ) == 1) // is about safe_mode?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "register_globals" && ini_get ( 'register_globals' ) == 1) // is about register_globals?
					$result = $this->hash ["OPTION_OK"];
				
				if ($option == "Zlib" && function_exists ( "gzcompress" )) // is about Zlib?
					$result = $this->hash ["OPTION_OK"];
				
				array_push ( $this->hash ["OPTIONS"], array (
						"OPTION" => $option,
						"RESULT" => $result 
				) );
			}
		}
	}
	public function getLangs() {
		if ($handle = opendir ( "lang" . Config::DIR_SEP )) {
			
			while ( false !== ($entry = readdir ( $handle )) )
				if ($entry != "." && $entry != "..")
					array_push ( $this->hash ["LANGS"], array (
							"ID" => substr ( $entry, 0, 2 ) 
					) );
			
			closedir ( $handle );
		}
	}
	public function languageApply() {
		@session_start ();
		// die(print_r($_SESSION["language_choosen_id"]));
		// die(print_r($_POST["language"]));
		if (isset ( $_SESSION ["language_choosen_id"] ))
			$this->_langID = $_SESSION ["language_choosen_id"];
		
		if (isset ( $_POST ["language"] ))
			$this->_langID = $_POST ["language"];
		
		if (! isset ( $_SESSION ['language_choosen_id'] ) && ! isset ( $_POST ["language"] ))
			$this->_langID = "en";
			// die($this->_langID);
		
		$_SESSION ["language_choosen_id"] = $this->_langID;
		require_once "lang" . Config::DIR_SEP . $this->_langID . ".php";
		
		$this->lang = new Lang ();
	}
	public function logError($er) {
		$_GET ['error'] = trim ( $er );
	}
	public function render() {
		echo $this->tpl->render ( $this->template, $this->hash );
	}
}

$router = new Router ( new Mustache_Engine (), new ControllerDB (), new File ( Config::APP_CONFIG_FILE, $_SERVER ['DOCUMENT_ROOT'] . Config::APP_CONFIG_FILE_PATH ), chmod ( Config::APP_CONFIG_FILE, $_SERVER ['DOCUMENT_ROOT'] . Config::APP_CONFIG_FILE_PATH, 0755 ) );
;
?>