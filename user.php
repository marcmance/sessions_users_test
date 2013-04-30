<?php
	public class User extends Model {

		public function __construct() {
			$field_array = array(
							"user_id",
							"user_first_name",
							"user_last_name",
							"user_type",
							"salt",
							"remember_token"
							);
			//parent::__construct;
		}

		public function login($form_email, $form_password) {
			if(!isset($_SESSION['user_id'])) {
				$query = "SELECT user_id, user_email, password, salt, remember_token 
							FROM user 
							WHERE user_email = ?
							LIMIT 1";

				if ($stmt = $this->mysqli->prepare($query)) { 
					$stmt->bind_param('s', $form_email); // Bind "$email" to parameter.
					$stmt->execute(); // Execute the prepared query.
					$stmt->store_result();

					if($stmt->num_rows == 1) { 
						$stmt->bind_result($user_id, $user_email, $password, $salt, $remember_token); 
						$stmt->fetch();
						$password = hash('sha512', $password.$salt); 
						if($password == $form_password) {
							//after each login generate new remember token
							$this->generateRememberToken();
							//To do: update query with new remember_token
							return "Success";   
						}
						else {
							//echo wrong password
							//brute force error
							return "Wrong password";
						} 
					} 
					else {
						//user email does not exist
						return "Email does not exist";
					}
				}
				else {
					//echo mysqli error
				}
			}
		}

		public function isAdmin() {
			return $this->user_type == "admin" ? true : false;
		}

		public function getFullName() {
			return $this->first_name . " " . $this->last_name;
		}

		public function insertNewUser() {

		}

		private function generateRememberToken(){
			$this->remember_token = hash('sha512', $this->salt.microtime());
		}

		private function generateSalt() {
			$this->salt = hash('sha512', microtime());
		}

		/*
			protected $has_one =  array("parentTable"
									)
		*/

		public function join($table, $params) {
			if($in_array($table, $this->has_one, true)) {
				$join_query = "JOIN " . $table . " ON " . $table . "." . $table ."_id = " . $this->table . "." . $table "_id";
			}
			return $this;
		}
	}
?>