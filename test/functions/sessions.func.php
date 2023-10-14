<?php
/**
 * initial db
 */

$sess_db = new sql_func(array("db" => $global_conf['session_db']));

function sess_open($sess_path, $sess_name) {
	return true;
}

function sess_close() {
	return true;
}

function sess_read($sess_id) {
	GLOBAL $sess_db;

	$sess_db->where_vars['sess_id'] = $sess_id;
	$session_info = $sess_db->select_ar_sql("sessions", "data", "session_id = '{{sess_id}}'");
	if (!isset($session_info['data'])) {
		$fields['session_id'] = $sess_id;
		$fields['access_time'] = time();
		$sess_db->insert_sql("sessions", $fields);
		return '';
	}
	else {
		$sess_db->update_sql("sessions", array("access_time" => time()), "session_id = '{{sess_id}}'");
		return $session_info['data'];
	}
}

function sess_write($sess_id, $data) {
	GLOBAL $sess_db, $global_conf;

	$sess_db = new sql_func(array("db" => $global_conf['session_db']));
	$sess_db->where_vars['sess_id'] = $sess_id;
	$sess_db->update_sql("sessions", array("access_time" => time(), "data" => $data), "session_id = '{{sess_id}}'");

	return true;
}

function sess_destroy($sess_id) {
	GLOBAL $sess_db;

	$sess_db->where_vars['sess_id'] = $sess_id;
	$sess_db->delete_sql("sessions", "session_id = '{{sess_id}}'");
	return true;
}

function sess_gc($sess_maxlifetime) {
	GLOBAL $sess_db;

	$sess_db->delete_sql("sessions", "access_time + ".$sess_maxlifetime." < ".time());
	return true;
}