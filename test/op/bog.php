<?php
//***** authentication
if (!isset($_SERVER['PHP_AUTH_USER'])){
	header ('WWW-Authenticate: Basic realm="logicmeter.com bog"');
	header ('HTTP/1.0 401 Unauthorized');
	exit;
}
elseif ($_SERVER['PHP_AUTH_USER']!='logicmeter' || $_SERVER['PHP_AUTH_PW']!='qDpvqNHxfmTK9nW9'){
	exit;
}

chdir("..");
include("init.php");

function transaction_answer($code, $message){
	return "<register-payment-response>
			  <result>
			    <code>".$code."</code>
			    <desc>".$message."</desc>
			  </result>
			</register-payment-response>";
}

//*** log transaction ****
$fields_log['transaction_id'] = get('trx_id');
$fields_log['order_n'] = get('o_order_n');
$fields_log['add_time'] = current_time();
$fields_log['bank'] = "bog";
$fields_log['parameters'] = serialize($_GET);
	
$query->insert_sql("payment_transactions_log", $fields_log);
//************************

if(get('o_order_n') !== false && get('merchant_trx') == false){
	$query->where_vars['order_n'] = get('o_order_n');
	$package_info = $query->select_ar_sql("math_user_packages", "*", "order_n = '{{order_n}}' AND paid = 0");
	//$payment_amount = $math->package_payment($package_info);
	if((int)$package_info['id'] !== 0 && get('merch_id') == $global_conf['bog_merchant_id']){
		echo "<payment-avail-response>
		  <result>
		    <code>1</code>
		    <desc>OK</desc>
		  </result>
		    <merchant-trx>".$global_conf['bog_merchant_trx']."</merchant-trx>
		  <purchase>
		    <shortDesc>Payment</shortDesc>
		    <longDesc>Payment</longDesc>
		    <account-amount>
		      <id>".$global_conf['bog_account_id']."</id>
		      <amount>".($package_info['payment_amount'] * 100)."</amount>
		      <currency>981</currency>
		      <exponent>2</exponent>
		    </account-amount>
		  </purchase>
		</payment-avail-response>";
	}
	else{
		echo transaction_answer(2, 'გადახდა შეუძლებელია');
	}
}
else if(get('merchant_trx') == $global_conf['bog_merchant_trx']){
	$query->where_vars['order_n'] = get('o_order_n');
	$package_info = $query->select_ar_sql("math_user_packages", "*", "order_n = '{{order_n}}' AND paid = 0 AND del = 0");
	$query->where_vars['transaction_id'] = get('trx_id');
	$transaction_info = $query->select_ar_sql("payment_transactions", "id", "transaction_id = '{{transaction_id}}'");
	
	if(get_int('result_code') == 1 && (int)$package_info['id'] !== 0 && get('merch_id') == $global_conf['bog_merchant_id'] && get('account_id') == $global_conf['bog_account_id']){
		
		if((int)$transaction_info['id'] == 0 && (float)($package_info['payment_amount']) == get_int('amount') / 100){ // tu am trx_id_it ara aris gatarebuli
			//**** add transaction 
			$fields['user_id'] = get_int('o_order_n');
			$fields['transaction_id'] = get('trx_id');
			$fields['amount'] = get_int('amount') / 100;
			$fields['order_n'] = get('o_order_n');
			$fields['card_number'] = get('p_maskedPan');
			$fields['card_holder'] = get('p_cardholder');
			$fields['transaction_time'] = substr(get('ts'), 0, 4)."-".substr(get('ts'), 4, 2)."-".substr(get('ts'), 6, 2)." ".substr(get('ts'), 9, 2).":".substr(get('ts'), 11, 2).":".substr(get('ts'), 13, 2);
			$fields['add_time'] = current_time();
			$fields['bank'] = "bog";
			$fields['ip'] = $_SERVER['REMOTE_ADDR'];
			
			$query->insert_sql("payment_transactions", $fields);
			
			
			//******* update package status
			$fields_package['paid'] = 1;
			$fields_package['payment_time'] = $fields['add_time'];
			$query->update_sql("math_user_packages", $fields_package, "id = ".(int)$package_info['id']);
			
			
			//***** update children status
			$children_ids = select_items("math_user_packages_children", "child_id", "child_id", "package_id = ".(int)$package_info['id']);
			if(count($children_ids) !== 0){
				$fields_children['paid_to'] = current_date(strtotime("+".$package_info['period']." month", $math->package_payment_time_period($package_info['parent_id'], $package_info['id'])));
				$fields_children['math'] = $package_info['math'];
				$fields_children['literacy'] = $package_info['literacy'];
				$fields_children['package_id'] = $package_info['id'];
				
				$query->update_sql("math_children", $fields_children, "id IN(".implode(',', $children_ids).")");
			}
			
			//***** send confirmation mail
			$mail_text = $query->select_ar_sql("mail_templates", "*", "name = 'PAYMENT_CONFIRMATION'");
			$user_info = $user_class->user_info($fields['user_id']);
			$package_info['td_style'] = "border-bottom: 1px solid #D6D6D6; height: 30px; padding-left: 10px;";
			if((int)$package_info['math'] == 1) {$packages[] = _MATH;}
			if((int)$package_info['literacy'] == 1) {$packages[] = _LITERACY;}
			$package_info['package'] = implode(", ", (array)$packages);
			//**** children
			$result_children = $query->select_sql("math_user_packages_children", "child_id", "del = 0 AND package_id = ".(int)$package_info['id']);
			while($row_children = mysql_fetch_assoc($result_children)){
				$child_info = $query->select_ar_sql("math_children", "*", "id = ".(int)$row_children['child_id']);
				$children[] = $child_info['name']." ".$child_info['surname'];
			}
			$package_info['children'] = implode(", ", (array)$children);
			$package_info['add_time'] = $fields['add_time'];
			
			$mail_text['tamplate'] = $templates->gen_html($package_info, $mail_text['tamplate'], 0);
			
			$global_conf['reply_to'] = 'no-reply@logicmeter.com';
			@send_mail($user_info['mail'], $mail_text['title'], $mail_text['tamplate']);
			
			echo transaction_answer(1, 'OK');
		}
		elseif((int)$transaction_info['id']){
			echo transaction_answer(1, 'ტრანზაქცია უკვე გატარებულია');
		}
		else{
			echo transaction_answer(2, 'შეცდომა');
		}
	}
	elseif(get_int('result_code') == 2){
		echo transaction_answer(1, 'წარუმატებელი გადახდა');
	}
	elseif((int)$transaction_info['id']){
		echo transaction_answer(1, 'ტრანზაქცია უკვე გატარებულია');
	}
	else{
		echo transaction_answer(2, 'გადახდა ვერ განხორციელდა');
	}
}
else{
	echo transaction_answer(2, 'უცნობი შეცდომა');
}
