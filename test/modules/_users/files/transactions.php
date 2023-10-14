<?php
$user_class->permission_end($module, 'manager');
global $out;

$out = user_head();

$replace_fields['_JANUARY'] = _JANUARY;
$replace_fields['_FABRUARY'] = _FABRUARY;
$replace_fields['_MARCH'] = _MARCH;
$replace_fields['_APRIL'] = _APRIL;
$replace_fields['_MAY'] = _MAY;
$replace_fields['_JUNE'] = _JUNE;
$replace_fields['_JULY'] = _JULY;
$replace_fields['_AUGUST'] = _AUGUST;
$replace_fields['_SEPTEMBER'] = _SEPTEMBER;
$replace_fields['_OCTOMBER'] = _OCTOMBER;
$replace_fields['_NOVEMBER'] = _NOVEMBER;
$replace_fields['_DECEMBER']= _DECEMBER;

$replace_fields['_W_OR'] = _W_OR;
$replace_fields['_W_SAM'] = _W_SAM;
$replace_fields['_W_OTX'] = _W_OTX;
$replace_fields['_W_XUT'] = _W_XUT;
$replace_fields['_W_PAR'] = _W_PAR;
$replace_fields['_W_SHAB'] = _W_SHAB;
$replace_fields['_W_KV'] = _W_KV;

$replace_fields['module'] = $module;
$replace_fields['_NAME'] = _NAME;
$replace_fields['_SURNAME'] = _SURNAME;
$replace_fields['_EMAIL'] = _EMAIL;
$replace_fields['_DATE'] = _DATE;
$replace_fields['_TEL'] = _TEL;
$replace_fields['_CODE'] = _CODE;
$replace_fields['_MONEY'] = _MONEY;
$replace_fields['_TYPE'] = _TYPE;
$replace_fields['_PAYMENT_TIME'] = _PAYMENT_TIME;
$replace_fields['_SUM'] = _SUM;

$date_from = set_var(get('date_from'), current_date(strtotime("-1 month")));
$date_to = set_var(get('date_to'), current_date());

$replace_fields['code'] = input_form("code", "textbox", $_GET, "", "width: 50px");
$date_from = set_var(get('date_from'), current_date(strtotime("-1 month")));
$date_to = set_var(get('date_to'), current_date());
$replace_fields['date_from'] = input_form("date_from", "textbox", $_GET, "", "", "calendar");
$replace_fields['date_to'] = input_form("date_to", "textbox", $_GET, "", "", "calendar");

$where = 1;
if(get('date_from') !== false){
	$query->where_vars['date_from'] = $date_from;
	$where .= " AND add_time > '{{date_from}}'";
	$search_value = 1;
}
if(get('date_to') !== false){
	$query->where_vars['date_to'] = $date_to;
	$where .= " AND add_time < '{{date_to}} 23:59:59'";
	$search_value = 1;
}

if(get_int('code') !== 0){
	$where .= " AND user_id = ".get_int('code');
}

$transactions_tmpl = $templates->split_template("list", "transactions");
$result = $query->select_sql("payment_transactions", "*", $where, "transaction_time DESC");
$replace_fields['transaction_amount'] = $query->amount_fields("payment_transactions", $where);
while($row = mysql_fetch_assoc($result)){
	$transactions_fields = $row;
	$user_info = $user_class->user_info($row['user_id']);
	$transactions_fields['name'] = $user_info['full_name'];
	
	$templates->gen_loop_html($transactions_fields, $transactions_tmpl);
}

$replace_fields['sum_money'] = $query->sum_sql("payment_transactions", "amount", $where);

$out .= $templates->gen_module_html($replace_fields, "transactions");
$html_out['module'] = $out;
