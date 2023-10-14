<?php
chdir("..");
include("init.php");

function service_error_code($code, $message){
    return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                <pay-response>
                	<status code=\"".$code."\">".$message."</status>
                	<timestamp>".time()."</timestamp>
                </pay-response>";
}

function service_ok_code($transaction_id = ""){
    $transaction = $transaction_id !== "" ? "\n<receipt-id>".$transaction_id."</receipt-id>" : "";
    return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                <pay-response>
                	<status code=\"0\">OK</status>
                	<timestamp>".time()."</timestamp>".$transaction."
                </pay-response>";
}


//*** log transaction ****
$fields_log['transaction_id'] = get('PAYMENT_ID');
$fields_log['add_time'] = current_time();
$fields_log['bank'] = "ipay";
$fields_log['parameters'] = serialize($_GET);

$query->insert_sql("payment_transactions_log", $fields_log);
//************************

//*** check user/password
if(get('USERNAME') !== $global_conf['ipay_user'] || get('PASSWORD') !== $global_conf['ipay_password']){
    echo service_error_code(2, "Incorrect User or password");
    exit;
}

//**** check hash
$hash_string = "";
foreach($_GET as $key => $value){
    $hash_string .= $key == "HASH_CODE" ? "" : $value;
}
$hash_string = str_replace(" ", "%20", $hash_string);
if(get('HASH_CODE') !== strtoupper(md5($hash_string.$global_conf['ipay_secret']))){
    echo service_error_code(3, "Hash code is invalid");
    exit;
}


//***** ping **********
if(get('OP') == "ping"){
    echo service_ok_code();
    exit;
}

//***** dept **********
if(get('OP') == "debt"){
    $user_info = $query->select_ar_sql("users_info", "*", "id = ".get_int('CUSTOMER_ID')." AND `status` = 1");
    
    if((int)$user_info['id'] == 0){
        echo service_error_code(6, "Customer does not exist");
        exit;
    }
    $user_class->current_user_id = $user_info['id'];
    $package_info = $math->package_payment(0, $user_info['mail']);
    
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <pay-response>
            	<status code=\"0\">OK</status>
            	<timestamp>".time()."</timestamp>
            	<debt>".round($user_info['balance'] - $package_info, 2)."</debt>
            	<additional-info>
            		<parameter name=\"first_name\">".$user_info['name']."</parameter>
            		<parameter name=\"last_name\">".$user_info['surname']."</parameter>
            	</additional-info>
            </pay-response>";
    exit;
}

//****** verify *******
if(get('OP') == "verify"){
    $user_info = $query->select_ar_sql("users_info", "*", "id = ".get_int('CUSTOMER_ID')." AND `status` = 1");
    
    if((int)$user_info['id'] == 0){
        echo service_error_code(6, "Customer does not exist");
        exit;
    }
    else{
        echo service_ok_code();
        exit;
    }
}

//***** Pay ***************
if(get('OP') == "pay"){
    $user_info = $query->select_ar_sql("users_info", "*", "id = ".get_int('CUSTOMER_ID')." AND `status` = 1");
    
    if((int)$user_info['id'] == 0){
        echo service_error_code(6, "Customer does not exist");
        exit;
    }
    else{
        //**** check transaction ID
        $query->where_vars['transaction_id'] = get('PAYMENT_ID');
        if($query->record_exist("payment_transactions", "transaction_id = '{{transaction_id}}'")){
           echo service_error_code(8, "PAYMENT_ID already exist");
           exit; 
        }
        
        $user_class->current_user_id = $user_info['id'];
        $user_class->current_user_email = $user_info['mail'];
        
        $payment_amount = get_int('PAY_AMOUNT') / 100;
        //**** add transaction 
		$fields['user_id'] = $user_info['id'];
		$fields['transaction_id'] = get('PAYMENT_ID');
		$fields['amount'] = $payment_amount;
		$fields['card_holder'] = get('EXTRA_INFO');
		$fields['transaction_time'] = current_time();
		$fields['add_time'] = current_time();
		$fields['bank'] = "ipay";
		$fields['ip'] = $_SERVER['REMOTE_ADDR'];
			
		$query->insert_sql("payment_transactions", $fields);
        
		//***** fill user balance
        $query->update_sql("users_other_info", array("balance" => $user_info['balance'] + $payment_amount), "user_id = ".(int)$user_class->current_user_id);
        
        //*** payment for unpaid packages
        $math->payment_from_user_account();
        
        echo service_ok_code(get('PAYMENT_ID'));
        exit;
    }
}

echo service_error_code(99, "Nothing to do");
exit;