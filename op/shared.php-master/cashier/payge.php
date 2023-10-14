<?php

    #################################################
    #### paygeWIRE SYSTEM TRANSACTIONS PROCESSING ####
    #################################################
    #### VARIABLES REQUIRED TO GET HERE
    #### $apage WITH TRANSACTION HANDLER PAGE ID
    #### $transaction_system
    #### $transaction_type
    #### $transaction_state
    #################################################
    #### SEO OPTIMIZED LINK PATTERN FOR THIS PAGE IS
    #### transactions_system/transactions_type/transactions_state
    #### FOR EXAMPLE LINKS LOOKS LIKE
    #### http://bookmakers.com/en/transaction/visa/deposit/init/
    #### http://bookmakers.com/en/transaction/visa/deposit/post/
    #################################################
    #### SEO OPTIMIZED LINK TO GET HERE IS ENCODED LIKE THIS
    #### $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$transactions_system,'transactions_type'=>$transactions_type, 'transactions_state'=>$transactions_state))
    #################################################

    define ('payge_system', 11);

    $payge_deposit_settings['test'] = '0';
    $payge_deposit_settings['state_completed'] = 'CANCELED';
    $payge_deposit_settings['state_canceled'] = 'COMPLETED';
    $payge_deposit_settings['state_error'] = 'ERROR';
    $payge_deposit_settings['url'] = 'https://www.pay.ge/pay';
    $payge_deposit_settings['url_callback'] = $project_url.'index.php?apage=52&transactions_system='.payge_system.'&transactions_type=1&transactions_state=complete&';
    $payge_deposit_settings['url_success'] = $project_url.'index.php?apage=52&transactions_system='.payge_system.'&transactions_type=1&transactions_state=success&';
    $payge_deposit_settings['url_error'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_fail']));
    $payge_deposit_settings['url_cancel'] = $payge_deposit_settings['url_error'];


    if (!defined('engine_admin'))
    {
        function payge_validate ()
        {
            transactions_transaction_log ('validating');
            if ($_SERVER['REMOTE_ADDR']!='109.238.228.82')
            {
                exit;
            }
            if ($_SERVER['HTTPS']!="on")
            {
                exit;
            }
//            if (!isset($_SERVER['PHP_AUTH_USER']))
//            {
//                header ('WWW-Authenticate: Basic realm="lit.ge payge"');
//                header ('HTTP/1.0 401 Unauthorized');
//                exit;
//            }
//            elseif ($_SERVER['PHP_AUTH_USER']!='' || $_SERVER['PHP_AUTH_PW']!='')
//            {
//                exit;
//            }
        }

        function payge_reader ($transaction_id)
        {
            $user_id = mysql_value_query ("select transaction_user from transactions where transaction_id='".$transaction_id."'");
            global $database;
            $user = $database->tables->users->load ($user_id);
            $order = mysql_object('dshop_orders', 'order_transaction', intval($transaction_id));
            if ($order)
            {
                mail_utf8 ('info@lit.ge, support@lit.ge, hazardland@gmail.com', 'info@lit.ge', '[რიდერის ჩუქება] '.$user->nickname, "
                მომხმარებელმა საიტის საშუალებით აჩუქა რიდერი:<p>
                <b>მყიდველი</b>
                <br>
                სახელი: ".$order['from']."<br>
                ტელეფონი: ".$order['phone']."<br>
                ელ-ფოსტა: ".$order['email']."
                <p>
                <b>ადრესატი</b>
                <br>
                სახელი: ".$order['to']."<br>
                ტელეფონი: ".$order['mobile']."<br>
                მისამართი: ".$order['address']."<p>
                <b>მილოცვის ტექსტი:</b> ".$order['comment']."
                ");
            }
            else
            {
                mail_utf8 ('info@lit.ge, support@lit.ge, hazardland@gmail.com', 'info@lit.ge', '[რიდერის შეძენა] '.$user->nickname, "
                მომხმარებელმა საიტის საშუალებით შეიძინა რიდერი:<p>
                სახელი: ".$user->nickname."<br>
                ტელეფონი: ".$user->phone_mobile."<br>
                მისამართი: ".$user->address_1."
                ");
            }
        }

        function payge_result ($code, $description, $transaction)
        {

            $check = hash('sha256', $code.$description.$transaction.$GLOBALS['payge_deposit_settings']['secret']);

            $result =
<<<XML
<result>
  <resultcode>$code</resultcode>
  <resultdesc>$description</resultdesc>
  <check>$check</check>
  <data></data>
</result>
XML;

            header('Content-type: text/xml');
            die($result);

        }
    }

    if (defined('engine_site'))
    {
        if ($transactions_system && $transactions_type && $transactions_systems[$transactions_system] && $transactions_types[$transactions_type] && $transactions_systems[$transactions_system]['methods'][$transactions_type] && $transactions_states[$transactions_state])
        {
            if (user_logged())
            {
                #### DEPOSIT [INIT] ####
                if (transactions_type_name($transactions_type)=='deposit' && $transactions_state=='init')
                {
                    $payge_deposit_to = false;
                    if ($payge_deposit['to'])
                    {
                        $payge_deposit['to'] = str_replace (array("'",'"',"\\"),"",$payge_deposit['to']);
                        $payge_deposit_to = mysql_value_query ("select user_id from users where user_login='".$payge_deposit['to']."' limit 1");
                        if (!$payge_deposit_to)
                        {
                            form_set_error ('payge_deposit[to]', 'მომხმარებელი არ მოიძებნა');
                        }
                    }

                    if ($payge_deposit && $payge_deposit['amount'] && !$html_errors)
                    {
                        $transactions_object = transactions_transaction_object (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $payge_deposit['amount']);
                        if (!$transactions_error)
                        {
                            //debug_var ($transactions_object['transfer_amount']['sum']);
                            $transactions_id = transactions_transaction_add (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $payge_deposit['amount'], $payge_deposit_to);
                            if (!$transactions_error)
                            {

                                $payge_string = $payge_deposit_settings['secret'].$payge_deposit_settings['merchant'].$transactions_id.(intval($payge_deposit['amount']*100)).'GEL'.'ბალანსის შევსება'.username().''.'KA'.$payge_deposit_settings['test'];
                                $payge_check = strtoupper (hash('sha256', $payge_string));
                                $payge_client  = htmlentities(username(),ENT_QUOTES,"UTF-8");
                                $payge_subject  = htmlentities('ბალანსის შევსება',ENT_QUOTES,"UTF-8");

                                form_open('test', $payge_deposit_settings['url']);
                                form_add_hidden ("merchant", $payge_deposit_settings['merchant']);
                                form_add_hidden ("ordercode", $transactions_id);
                                form_add_hidden ("amount", intval($payge_deposit['amount']*100));
                                form_add_hidden ("currency", 'GEL');
                                form_add_hidden ("description", $payge_subject);
                                form_add_hidden ("clientname", $payge_client);
                                form_add_hidden ("lng", 'KA');
                                form_add_hidden ("successurl", $payge_deposit_settings['url_success']);
                                form_add_hidden ("errorurl", $payge_deposit_settings['url_error']);
                                form_add_hidden ("cancelurl", $payge_deposit_settings['url_cancel']);
                                form_add_hidden ("callbackurl", $payge_deposit_settings['url_callback']);
                                form_add_hidden ("testmode", $payge_deposit_settings['test']);
                                form_add_hidden ("check", $payge_check);
                                form_add_label ("თქვენ გსურთ ".$payge_deposit['amount']." ლარის გადმორიცხვა lit.ge-ს ექაუნთის ბალანსზე");
                                form_add_submit ('გაგრძელება','გაუქმება');
                                form_close ();

                                #### FOREIGN CODE ##########

                                $transactions_object['saved'] = true;
                            };
                        };
                    };
                    if ($transactions_error)
                    {
                        html_add_message ($transactions_error);
                        if ($transactions_id)
                        {
                            transactions_transaction_fail ($transactions_id);
                        };
                    };
                    if ($html_errors || !$transactions_object['saved'])
                    {
                        form_open ('deposit_submit', $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$transactions_system,'transactions_type'=>$transactions_type, 'transactions_state'=>'init')));
                        form_add_label ('<b>ბალანსის შევსება</b>');
                        form_add_hidden ('apage', $apage);
                        form_add_label ('&nbsp;','<div style="width:280px"><font color="#507171"><img src="'.$project_url.'images/cashier/payge.png" style="vertical-align:middle;float:right"><span style="margin-top:10px">მითითებული თანხა მთლიანად ირიცხება მომხმარებლის ბალანსზე და <b>არ ემატება საკომისიო</b>.</span></font></div>');
                        form_add_spacer ();
                        form_add_edit ('payge_deposit[amount]', 'თანხა', $payge_deposit['amount'], '280px');
//                        form_add_label ("<div style='height:30px'>");
//                        form_add_label ('', '<div style="width:280px"><font color="#507171"><b>სხვა მომხმარებლისთვის</b> თანხის ჩასარიცხად, დამატებით მიუთითეთ მომხმარებლის სახელი (login):</font></div>');
//                        form_add_edit ('payge_deposit[to]', 'მომხმარებელი', $payge_deposit['to'], '280px');
//                        form_add_label ("<div style='height:10px'>");
                        form_add_submit ('გაგრძელება');
                        form_close ();
                    }
                    else
                    {
                        //html_parse_text ('cashier_transaction_process');
                    };
                };
            };

//            debug_var ($apage);
//            debug_var ($transactions_system);

            if ((transactions_type_name($transactions_type)=='deposit' || transactions_type_name($transactions_type)=='reader') && $transactions_state=='success')
            {
                //payge_validate ();
                //TODO: http basic auth
                //check secure ssl


                $payge_params = array();

                $payge_params['status']     = urldecode($_GET["status"]);
                $payge_params['transactioncode'] = urldecode($_GET['transactioncode']);
                $payge_params['datestring'] = urldecode($_GET['date']);
                $payge_params['amount']     = urldecode($_GET['amount']);
                $payge_params['currency']   = urldecode($_GET['currency']);
                $payge_params['ordercode']  = urldecode($_GET['ordercode']);
                $payge_params['paymethod']  = urldecode($_GET['paymethod']);
                $payge_params['payedamount']  = urldecode($_GET['payedamount']);
                $payge_params['customdata'] = urldecode($_GET['customdata']);
                $payge_params['testmode']   = urldecode($_GET['testmode']);
                $payge_check = urldecode($_GET['check']);

                $payge_string = $payge_params['status'].$payge_params['transactioncode'].$payge_params['datestring'].$payge_params['amount'].$payge_params['currency'].$payge_params['ordercode'].$payge_params['paymethod'].$payge_params['payedamount'].$payge_params['customdata'].$payge_params['testmode'];
                $payge_string .= $payge_deposit_settings['secret'];

                $payge_hash = hash ('sha256', $payge_string);
                $payge_id = intval($payge_params['ordercode']);

                if (strtoupper($payge_hash)==strtoupper($payge_check))
                {
                    if ($payge_id)
                    {
                        $transaction = mysql_object ('transactions', 'transaction_id', $payge_id);

                        if ($transaction)
                        {

                            if ($transaction['status']==1)
                            {
                                mysql_debug_query ("update transactions set transaction_key='".protect_string($payge_params['transactioncode'])."' where transaction_id='".$payge_id."'");
                                if ($payge_params['paymethod']=='PAYTERM')
                                {
                                    html_add_message ('თანხის გადასახდელად, გთხოვთ მიბრძანდეთ თვითმომსახურების ტერმინალთან, ტერმინალის მენიუში აირჩიოთ: ბანკები &Implies; ლიბერთი ბანკი &Implies; PAY.GE &Implies; შეიტანოთ გადახდის კოდი და მიყვეთ ინსტრუქციას. თანხის ჩარიცხვის შემდეგ, გადარიცხული თანხა აისახება თქვენს ბალანსზე.');
                                }
                                else
                                {
                                    if (is_object($state) && $state->avalible())
                                    {
                                        html_add_message ('ტრანზაქცია წარმატებით დასრულდა. გადმორიცხული თანხა მოკლე დროში აისახება თქვენს ბალანსზე.', 'page', 'transaction');
                                        $state->restore();
                                    }
                                    else
                                    {
                                        html_add_message ('ტრანზაქცია წარმატებით დასრულდა. გადმორიცხული თანხა მოკლე დროში აისახება თქვენს ბალანსზე.');
                                    }
                                }
                            }
                            else
                            {
                                html_add_message ('ტრანზაქცია უკვე შესრულებულია.');
                            }
                        }
                        else
                        {
                            html_add_message ('ტრანზაქცია არ მოიძებნა.');
                        }
                    }
                    else
                    {
                        html_add_message ('ტრანზაქცია მოწოდებული იქნა არასრული ინფორმაციით.');
                    }
                }
                else
                {
                    html_add_message ('დაფიქსირდა შეცდომა.');
                }

            };

            if ((transactions_type_name($transactions_type)=='deposit' || transactions_type_name($transactions_type)=='reader') && $transactions_state=='complete')
            {
                payge_validate ();

                //debug_var ($_REQUEST);
                //file_put_contents('./log.txt', var_export ($_REQUEST, true));
                //$payge_deposit_settings['key'] = "b52df0974763ace55a0f3dced6bc0c68";

                $payge_params['status'] = urldecode($_GET["status"]);
                $payge_params['transactioncode'] = urldecode($_GET['transactioncode']);
                $payge_params['amount'] = urldecode($_GET['amount']);
                $payge_params['currency'] = urldecode($_GET['currency']);
                $payge_params['ordercode'] = urldecode($_GET['ordercode']);
                $payge_params['paymethod'] = urldecode($_GET['paymethod']);
                $payge_params['payedamount']  = urldecode($_GET['payedamount']);
                $payge_params['customdata'] = urldecode($_GET['customdata']);
                $payge_params['testmode'] = urldecode($_GET['testmode']);
                $payge_check = urldecode($_GET['check']);


                $payge_string = $payge_params['status'].$payge_params['transactioncode'].$payge_params['amount'].$payge_params['currency'].$payge_params['ordercode'].$payge_params['paymethod'].$payge_params['payedamount'].$payge_params['customdata'];//.$payge_deposit_settings['test'];
                $payge_string .= $payge_deposit_settings['secret'];

                $payge_hash = hash('sha256', $payge_string);

                if (strcasecmp($payge_check, $payge_hash) != 0)
                {
                    payge_result('-3', 'Invalid checksum', $payge_params['transactioncode']);
                }

                if ($payge_params['transactioncode'])
                {
                    $payge_params['transactioncode'] = protect_string ($payge_params['transactioncode']);
                    $transactions_id = mysql_value_query ("select transaction_id from transactions where transaction_key='".$payge_params['transactioncode']."' and transaction_status=1");

                    if ($transactions_id)
                    {
                        //$payge_deposit_settings['mac'] = hash_hmac_($payge_deposit_settings['key'], $transactions_post['mid']."|".$transactions_post['tid']."|".$transactions_post['status']."|".$transactions_post['ercode']."|".$transactions_post['message']."|".$transactions_post['amount']."|".$transactions_post['currency']."|".$transactions_post['product_desc']);
                        $transaction = mysql_object ('transactions', 'transaction_id', $transactions_id);

                        if ($transaction['status']=="1")
                        {
                            if ($payge_params['status']=='COMPLETED' && (($payge_params['paymethod']!='PAYTERM') || ($payge_params['paymethod']=='PAYTERM' && floatval($payge_params['payedamount']>0))))
                            {
                                if ($payge_params['paymethod']=='PAYTERM' && $payge_params['payedamount']>0)
                                {
                                    $payge_params['payedamount'] = floatval($payge_params['payedamount'])/100;
                                    $transaction['system_amount'] = $payge_params['payedamount'];
                                    $transaction['user_amount'] = $payge_params['payedamount'];
                                    mysql_query ("update transactions set transaction_system_amount='".protect_string($payge_params['payedamount'])."', transaction_user_amount='".protect_string($payge_params['payedamount'])."', transaction_group=2 where transaction_id='".$transactions_id."' limit 1");
                                    if (mysql_errno())
                                    {
                                        payge_result('-1', 'System malfunction', $payge_params['transactioncode']);
                                    }
                                }
                                elseif ($payge_params['paymethod']=='PAYTERM' && $payge_params['payedamount']==0)
                                {
                                    transactions_transaction_fail ($transactions_id);
                                    payge_result('-3', 'System malfunction', $payge_params['transactioncode']);
                                }

                                transactions_transaction_complete ($transactions_id);

                                if (!$transactions_error)
                                {
                                    //$transaction = mysql_object ('transactions', 'transaction_id', $transactions_id);
                                    if ($transaction['external'] && $transaction['user']!=$transaction['external'])
                                    {
                                        $transaction_id_from = transactions_transaction_add ($transaction['user'], $transactions_settings['main_balance'], 4, 10, $transaction['system_amount']);
                                        if ($transactions_error)
                                        {
                                            transactions_transaction_fail ($transaction_id_from);
                                        }
                                        else
                                        {
                                            $transaction_id_to = transactions_transaction_add ($transaction['external'], $transactions_settings['main_balance'], 4, 11, $transaction['system_amount']);
                                            if ($transactions_error)
                                            {
                                                transactions_transaction_fail ($transaction_id_from);
                                                transactions_transaction_fail ($transaction_id_to);
                                            }
                                            else
                                            {
                                                transactions_transaction_complete ($transaction_id_from);
                                                transactions_transaction_complete ($transaction_id_to);
                                                $transaction_from_object = $database->tables->users->load ($transaction['user']);
                                                if ($transaction_from_object)
                                                {
                                                    $database->tables->messages->send ($transaction['user'], $transaction['external'], "მომხმარებელმა ".$transaction_from_object->login." ჩაგირიცხათ ".\db\round($transaction['system_amount'])." ლარი.");
                                                }
                                            }
                                        }
                                    }
                                    if (transactions_type_name(mysql_value_query("select transaction_type from transactions where transaction_id='".$transactions_id."'"))=='reader')
                                    {
                                        payge_reader ($transactions_id);
                                    }
                                    payge_result('0', 'Ok', $payge_params['transactioncode']);

                                }
                            }
                            elseif ($payge_params['status']=='CHECK')
                            {
                                mysql_query ("update transactions set transaction_group=1 where transaction_id='".$transactions_id."' limit 1");
                                payge_result('0', 'Ok', $payge_params['transactioncode']);
                            }
                        }
                        else
                        {
                            transactions_transaction_fail ($transactions_id);
                            payge_result('1', 'Transaction dublicate', $payge_params['transactioncode']);
                        }
                    }
                    else
                    {
                        payge_result('-2', 'Transaction not found', $payge_params['transactioncode']);
                    };
                }
                else
                {
                    payge_result('-3', 'No input data', $payge_params['transactioncode']);
                };
                payge_result ('-3', 'No input data', $payge_params['transactioncode']);

            };
        };
    };

    if (defined('engine_admin'))
    {
        $transactions_module_result = true;
    };

?>