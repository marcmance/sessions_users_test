<?php
	require_once('/connection2.php');
	class Model {
		protected $mysqli;
		protected $fields_array;
		protected $fields_var;
		protected $table_name;
		public $join_array; //when join function gets called
		
		public function __construct() {
			$this->mysqli = new mysqli(HOST_DB, USERNAME_DB, PASSWORD_DB, NAME_DB);
			$this->table_name = strtolower(get_class($this));
			$this->makeProperties();
			$this->join_array = array();
		}
		
		public function makeProperties() {
			if(isset($this->fields_array)) {
				foreach($this->fields_array as $k) {
					$this->{$k} = "";
				}
			}
		}
		
		public function getAll($select_fields = null) {
			if($select_fields == null) {
				$select_fields = $this->fields_array;
			}
			$fields = implode(",",$select_fields);

			$query = "SELECT ".$fields ." FROM ". $this->table_name;
			$results = null;
			if ($stmt = $this->mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->store_result();
				$results = $this->bind_results($stmt);
				$stmt->close();
				return $results;
			}
		}

		protected function _join() {

		}
		
		/**
		 * Find a single record.
		 *
		 * @param string  $param The value to search by
		 * @param string $find_by_column The column to search by
		 * @param array $select_fields The fields to select
		 *
		 * @return query result
		 */
		public function find($param, $find_by_column = null, $select_fields = null) {
			//if no input, select *
			if($select_fields == null) {
				$select_fields = $this->fields_array;
			}

			//default to primary key id
			if($find_by_column == null || $find_by_column == $this->table_name . "_id") {
				$param = intval($param); 
				$find_by_column = $this->table_name . "_id";
			}

			$fields = implode(",",$select_fields);
			
			$query = "SELECT ".$fields ." FROM ". $this->table_name . " WHERE " . $find_by_column . " = ? LIMIT 1";
			$params = array($param);
			$this->query($query, $params);
		}
		
		public function join($table, $select_fields = null) {
			$this->join_array[$table] = $select_fields;
			return $this;
		}
		
	
		public function query($query, $params = null) {		
			if ($stmt = $this->mysqli->prepare($query)) {
				if($params != null) {
					call_user_func_array(array($stmt,'bind_param'),$this->bind_params($params));
				}
				$stmt->execute();
				$stmt->store_result();
				$results = $this->bind_results($stmt);
				$stmt->close();
				return $results;
			}
			else {
				echo $this->mysqli->error();
			}
		}
		
		//dynamic function bind params
		protected function bind_params($params) {
			$binded_params = array('');                       
			foreach($params as $p) {  
				if(is_int($p)) {
					$binded_params[0] .= 'i';              //integer
				} elseif (is_float($p)) {
					$binded_params[0] .= 'd';              //double
				} elseif (is_string($p)) {
					$binded_params[0] .= 's';              //string
				} else {
					$binded_params[0] .= 'b';              //blob and unknown
				}
				array_push($binded_params, $p);
			}
			
			$refs = array();
			foreach ($binded_params as $key => $value) {
				$refs[$key] = & $binded_params[$key];
			}
			return $refs;
		}
		
		//dynamic function bind all fields
		protected function bind_results($stmt) {
			$fields_var = array();
			$results = null;
			
			/*
			foreach($this->fields_array as $f) {
				$$f = null; 
				$fields_var[$f] = &$$f; 
			}	*/
			$meta = $stmt->result_metadata();
			while ($field = $meta->fetch_field()) {
				$field_name = $field->name;
				$$field_name = null;
				$fields_var[$field_name] = &$$field_name;
			}
			call_user_func_array(array($stmt,'bind_result'),$fields_var);
			$results = array();
			
			if($stmt->num_rows == 1) {
				$stmt->fetch();
				foreach($fields_var as $k => $v) {
					$results[$k] = $v;
					$this->{$k} = $v;
				}
			}
			else if($stmt->num_rows > 1) {
				$i = 0;
				while($stmt->fetch()) {
					$results[$i] = array();
					foreach($fields_var as $k => $v) {
						$results[$i][$k] = $v;
					}
					$i++;
				}
			}
			return $results;
		}
		
		public function __destruct(){
			$this->mysqli->close();
		}
	}

?>