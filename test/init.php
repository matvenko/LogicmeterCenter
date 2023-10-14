<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('session.gc_maxlifetime', 3600);
//ini_set('session.cookie_domain', '.logicmeter.com');

require_once("functions/functions.php");
require_once("functions/db.php");
//*** sessions
require_once("functions/sessions.func.php");
session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
session_start();

require_once("functions/image.php");
require_once("functions/user_info.php");
require_once("functions/tmpl.php");
require_once("functions/Mail.php");
require_once("Mail/mime.php");
date_default_timezone_set("Asia/Tbilisi");

$time_start = microtime_float();

$query = new sql_func();
//*** logicmeter
$query_l = new sql_func(array("db" => $sql_db_l));
$query_l->sql_db = $sql_db_l;

$templates = new tmpl();
$user_class = new user_class();
$module = load_module(get('module'));
$lang = load_lang(get('lang'));

require_once("blocks/ajax_calendar/index.php");
?>