<?php
$sql_host = "localhost";
$sql_user = "logicmeter_user";
$sql_pass = "yL8coANDaJRn3Vh2";
$sql_db = "logicmeter_center";
$sql_db_l = "logicmeter"; //logicmeter db
	
//**** sessions
$global_conf['session_db'] = "sessions";

$global_conf['class_max_children'] = 12; // max children amount in class
$global_conf['class_max_children_rlego'] = 6;

$global_conf['private_key'] = '5YU58z3v4waEUhmU';

$global_conf['editor_upload_directory'] = "/upload/editor";
$global_conf['literacy_upload_directory'] = "/upload/literacy";

$global_conf['table_pref'] = 'gt_';

$global_conf['location'] = 'https://center.logicmeter.com/';
$global_conf['location_logicmeter'] = 'https://logicmeter.com/';
$global_conf['lang_ar'] = array('geo', 'eng');
$global_conf['logicmeter_src'] = "../main/";

//*** payment
//$global_conf['bog_url'] = "https://sb3d.georgiancard.ge/payment/start.wsm";
//$global_conf['bog_url'] = "https://logicmeter.com/op/redirect.php";
$global_conf['bog_url'] = "https://localhost";
$global_conf['bog_merchant_id'] = "C1F87D805479457AB9E0FA9EA50CE63E";
$global_conf['bog_page_id'] = "AA67216C79CFF66095D03C4CCFAA175E";
$global_conf['bog_account_id'] = "73F85C3C05A0943172D3B3C634612B64";
$global_conf['bog_back_url_success'] = "http://logicmeter.com/index.php?module=users&page=profile&type=children";
$global_conf['bog_back_url_fail'] = "http://logicmeter.com";
$global_conf['bog_merchant_trx'] = "zbvW3CDELRDb2Wp2";

//**** ipay
$global_conf['ipay_user'] = "logicmeter";
$global_conf['ipay_password'] = "Z7NqbzNyVFGvvstf";
$global_conf['ipay_secret'] = "H2BQ6HLqCt6ht4Vz";

$global_conf['debug_ip'] = array("::1", "127.0.0.1", "188.129.208.61", "192.168.113.13", "67.82.247.18");

define(DOC_ROOT, "");
define(MANAGE_DIR, "");

$global_conf['default_lang'] = 'geo';

$global_conf['admin_email'] = "admin@logicmeter.com";

$global_conf['contac_email'] = "info@logicmeter.com";

$send_mail['sendgrid_key'] = "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";
$send_mail['host'] = "ssl://logicmeter.com";
$send_mail['port'] = "465";
$send_mail['from_name'] = "Logicmeter";
$send_mail['username'] = "info@logicmeter.com";
$send_mail['password'] = "avarjishegoneba";
$send_mail['from'] = "Logicmeter <info@logicmeter.com>"
?>