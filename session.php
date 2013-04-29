<?php
	public class Session {

		private $user_id;
		private $session_user;

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

	        $this->loginCheck();
		}
 
		public function loginCheck() {
			if(isset($_SESSION['user_id']) && isset($_SESSION['remember_token']) {
	        	$user = new $User();
	        	$user->find($_SESSION['user_id']);
	        	if($user != null && $user->remember_token == $_SESSION['remember_token']) {
	        		$this->session_user = $user;
	   				return true;
	        	}
	        	else {
	        		return false;
	        	}
	        }
		}

		public function logout() {
			$_SESSION = array();
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
			session_destroy();
		}

		public function getSessionUser() {
			return $this->session_user;
		}
	}
?>