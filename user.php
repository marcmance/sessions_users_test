<?php
	public class User extends Model {
		public function __construct(){
			//parent::__construct;
		}
		public function loginUser($form_email, $form_password) {
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
							$_SESSION['user_id'] = $user_id; 
							$_SESSION['username'] = $username;
							$_SESSION['login_string'] = hash('sha512', $password.$remember_token);
							return true;   
						}
						else {
							//echo wrong password
							//brute force error
							return false;
						} 
					} 
					else {
						//user email does not exist
						return false;
					}
				}
				else {
					//echo mysqli error
				}
			}
		}
	}
?>