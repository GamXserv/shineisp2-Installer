<?php
class File {
	private $_handle;
	private $_filename;
	private $_pathname;
	public function __construct($filename, $pathname = false, $mode = 'a+') {
		$this->_filename = trim ( $filename );
		$this->_pathname = ($pathname) ? trim ( $pathname ) : $_SERVER ['DOCUMENT_ROOT'];
		$this->_pathname .= DIRECTORY_SEPARATOR;
		
		try {
			chmod ( $this->_pathname . $this->_filename, 0777 );
			if (! fopen ( $this->_pathname . $this->_filename, $mode ))
				throw new Exception ( "Error while opening the " . $this->_pathname . $this->_filename . " file" );
			else
				$this->_handle = fopen ( $this->_pathname . $this->_filename, $mode );
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		
		try {
			if (! chmod ( $this->_pathname . $this->_filename, "0777" ))
				throw new Exception ( "Error while setting the chmod" );
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	public function write($string) {
		fwrite ( $this->_handle, $this->parseMuphVariables ( $string ) );
	}
	public function read() {
		return file_get_contents ( $this->_filename );
	}
	public function parseMuphVariables($string) {
		return str_replace ( array (
				"{prefix}",
				"{dbname}",
				"{dbuser}",
				"{dbpass}",
				"{dbhost}" 
		), array (
				$_SESSION ['dbprefix'],
				$_SESSION ['dbname'],
				$_SESSION ['dbuser'],
				$_SESSION ['dbpass'],
				$_SESSION ['dbhost'] 
		), $string );
	}
	public function __destruct() {
		@fclose ( $this->_handle );
	}
}

?>