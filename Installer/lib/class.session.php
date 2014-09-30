<?php
class Session {
	private static $_sessionStarted = false;
	public static function start() {
		if (! self::$_sessionStarted) {
			if (session_status () == "PHP_SESSION_DISABLED")
				die ( "Sessions are disabled on this machine. Please active this function." );
			@session_start ();
			self::$_sessionStarted = true;
		}
	}
	public static function get($key, $skey = false) {
		if ($skey) {
			if (isset ( $_SESSION [$key] [$skey] ))
				return $_SESSION [$key] [$skey];
		} else {
			if (isset ( $_SESSION [$key] ))
				return $_SESSION [$key];
		}
		return false;
	}
	public static function set($key, $value) {
		$_SESSION [$key] = $value;
	}
	public static function destroy() {
		if (self::$_sessionStarted) {
			session_unset ();
			session_destroy ();
		}
	}
	public function display() {
		echo "<pre>";
		print_r ( $_SESSION );
		echo "</pre>";
	}
}

?>
