<?php

class sql_func {
	private $connect;
	public $sql_host = "";
	public $sql_user = "";
	public $sql_pass = "";
	public $sql_error = "";
	public $sql_db = "";
	public $table_pref = "";
	public $where_vars = "";
	public $increase_value = array();
	public $decrease_value = array();


	function __construct($parameters = []) {
		global $sql_host, $sql_user, $sql_pass, $sql_db, $global_conf;

		$this->host = isset($parameters['host']) ? $parameters['host'] : $sql_host;
		$this->sql_user = isset($parameters['user']) ? $parameters['user'] : $sql_user;
		$this->sql_pass = isset($parameters['password']) ? $parameters['password'] : $sql_pass;
		$this->sql_db = isset($parameters['db']) ? $parameters['db'] : $sql_db;
		$this->table_pref = isset($parameters['table_pref']) ? $parameters['table_pref'] : $global_conf['table_pref'];

		$this->connect_sql();
	}

	function connect_sql() {
		$this->connect = mysqli_connect($this->host, $this->sql_user, $this->sql_pass, $this->sql_db);
		return $this->connect;
	}

	function change_db($db){
		$this->sql_db = $db;
		mysqli_select_db($this->connect, $this->sql_db);
	}

	function query_error($sql) {
		global $global_conf;
		if (in_array($_SERVER['REMOTE_ADDR'], $global_conf['debug_ip'])) {
			return "<font color=\"#D50000\"><b>Mysql Error</b></font><P>".mysqli_error($this->connect)."<P>".$sql;
		}
		else {
			return "<font color=\"#D50000\"><b>Mysql Error</b></font>";
		}
	}

	function query_sql($sql) {
		mysqli_set_charset($this->connect, "utf8");
		$result = mysqli_query($this->connect, $sql) or die($this->query_error($sql));
		return $result;
	}

	function assoc($result) {
		return mysqli_fetch_assoc($result);
	}

	function obj($result) {
		return mysqli_fetch_object($result);
	}

	function select_sql($table, $filds = "*", $where = "", $order = "", $limit = "", $group_by = "") {
		$sql = "SELECT ".$filds." FROM ".$this->table_pref.$table;
		if ($where !== "") {
			if (is_array($this->where_vars)) {
				foreach ($this->where_vars as $key => $value) {
					$where = str_replace("{{".$key."}}", $this->escape($value), $where);
				}
			}
			$sql .= " WHERE ".$where;
		}
		if ($group_by !== "")
			$sql .= " GROUP BY ".$group_by;
		if ($order !== "")
			$sql .= " ORDER BY ".$order;
		if ($limit !== "")
			$sql .= " LIMIT ".$limit;
		return $this->query_sql($sql);
	}


	function select_obj_sql($table, $filds = "*", $where = "", $order = "", $limit = "") {
		$result = $this->select_sql($table, $filds, $where, $order, $limit);
		return mysqli_fetch_object($result);
	}

	function select_ar_sql($table, $filds = "*", $where = "", $order = "", $limit = "") {
		$result = $this->select_sql($table, $filds, $where, $order, $limit);
		return mysqli_fetch_assoc($result);
	}

	function insert_sql($table, $fields) {
		$insert_field = $insert_value = '';
		foreach ($fields as $key => $value) {
			$insert_field .= ", `".$key."`";
			$insert_value .= ", '".$this->escape($value)."'";
		}
		$insert_field = substr($insert_field, 2);
		$insert_value = substr($insert_value, 2);
		$sql = "INSERT INTO ".$this->table_pref.$table." (".$insert_field.") VALUE (".$insert_value.")";
		$this->query_sql($sql);
		return mysqli_insert_id($this->connect);
		//$this->close_sql();
	}

	function update_sql($table, $fields, $where) {
		$update_field = $update_value = '';
		foreach ($fields as $key => $value) {
			if ($value === 'INVERSE') {
				$update_field .= ", `".$key."` = !`".$key."`";
			}
			elseif ($value === 'INCREASE') {
				$increase_value = array_key_exists($key, $this->increase_value) ? (int)$this->increase_value[$key] : 1;
				$update_field .= ", `".$key."` = ".$key."+".$increase_value;
			}
			elseif ($value === 'DECREASE') {
				$decrease_value = array_key_exists($key, $this->decrease_value) ? (int)$this->decrease_value[$key] : 1;
				$update_field .= ", `".$key."` = ".$key."-".$decrease_value;
			}
			else {
				$update_field .= ", `".$key."` = '".$this->escape($value)."'";
			}

		}
		$update_field = substr($update_field, 2);

		if ($where !== "") {
			if (is_array($this->where_vars)) {
				foreach ($this->where_vars as $key => $value) {
					$where = str_replace("{{".$key."}}", $this->escape($value), $where);
				}
			}
			$sql = " WHERE ".$where;
		}

		$sql = "UPDATE ".$this->table_pref.$table." SET ".$update_field." WHERE ".$where."";
		$this->query_sql($sql);
		//$this->close_sql();
		return mysqli_affected_rows($this->connect);
	}

	function custom_update_sql($table, $set, $where) {
		$sql = "UPDATE ".$this->table_pref.$table." SET ".$set." WHERE ".$where."";
		$this->query_sql($sql);
		//$this->close_sql();
	}

	function delete_sql($table, $where) {
		$sql = "DELETE FROM ".$this->table_pref.$table." WHERE ".$where;
		$this->query_sql($sql);
		//$this->close_sql();
	}

	function amount_fields($table, $where = 1, $search_field = '*', $group_by = '') {
		if ($group_by !== '') {
			$where .= " GROUP BY ".$group_by;
		}
		if ($where !== "") {
			if (is_array($this->where_vars)) {
				foreach ($this->where_vars as $key => $value) {
					$where = str_replace("{{".$key."}}", $this->escape($value), $where);
				}
			}
			$sql = " WHERE ".$where;
		}

		$sql = "select count(".$search_field.") from ".$this->table_pref.$table." where ".$where;
		$result = $this->query_sql($sql);
		$row = mysqli_fetch_array($result);
		return (int)$row[0];
	}

	function record_exist($table, $where = 1) {
		$row = $this->select_ar_sql($table, "id", $where, "", "0,1");
		return (int)$row['id'] == 0 ? false : true;
	}

	function tables_list() {
		return $this->query_sql("SHOW TABLES FROM ".$this->db."");
	}

	function table_exists($tablename) {
		$exists = mysqli_query("SELECT 1 FROM ".$this->table_pref.$tablename." LIMIT 0");
		if ($exists)
			return true;
		return false;
	}

	function table_info($table_name) {
		$result = $this->query_sql("SHOW TABLE STATUS WHERE NAME = '".$this->table_pref.$table_name."'");
		$info = $this->assoc($result);
		return $info;
	}

	function table_fields($tablename) {
		$result = $this->query_sql("SHOW COLUMNS FROM ".$this->table_pref.$tablename."");
		if (mysqli_num_rows($result) > 0) {
			while ($row = $this->assoc($result)) {
				$filds[] = $row['Field'];
			}
		}
		return $filds;
	}


	function close_sql() {
		mysqli_close($this->connect);
	}

	function escape($string) {
		$string = stripslashes($string);
		return mysqli_real_escape_string($this->connect, (string)$string);
	}

	function max_value($table, $field, $where = "") {
		@$result = $this->select_sql($table, "MAX(".$field.")", $where);
		$row = mysqli_fetch_array($result);
		return @$row[0];
	}

	function sum_sql($table, $field, $where = "") {
		@$result = $this->select_sql($table, "SUM(".$field.")", $where);
		$row = mysqli_fetch_array($result);
		return (float)$row[0];
	}

	function avg_sql($table, $field, $where = "") {
		@$result = $this->select_sql($table, "AVG(".$field.")", $where);
		$row = mysqli_fetch_array($result);
		return (float)$row[0];
	}

	function min_value($table, $field, $where = "") {
		@$result = $this->select_sql($table, "MIN(".$field.")", $where);
		$row = mysqli_fetch_array($result);
		if (is_numeric($row[0])) {
			$row[0] = (int)$row[0];
		}
		return @$row[0];
	}

//**** end of class *******
}


?>