<?php
	public class Session {

		private $user_id;

		public function __construct() {
			$session_name = 'sec_session_id'; // Set a custom session name
	        $secure = false; // Set to true if using https.
	        $httponly = true; // This stops javascript being able to access the session id. 
	 
	        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
	        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
	        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
	        session_name($session_name); // Sets the session name to the one set above.
	        session_start(); // Start the php session
	        session_regenerate_id(true); // regenerated the session, delete the old one.   

	        if(isset($_SESSION['user_id'])) {
	        	$this->user_id = $_SESSION['user_id'];
	        }
		}

		public function setUserSession($user_id) {
			$_SESSION['user_id'] = $user_id;
			$this->user_id = $user_id;
		}
 
		public function loginCheck() {

		}

		public function getUserId() {
			return $this->user_id;
		}
	}
?>