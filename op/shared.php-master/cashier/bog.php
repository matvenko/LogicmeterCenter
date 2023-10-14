<?php

    #################################################
    #### BOG SYSTEM TRANSACTIONS PROCESSING ####
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

    if (!defined('engine_admin'))
    {
        function bog_validate ()
        {
            transactions_transaction_log ('validating');
            if ($_SERVER['REMOTE_ADDR']!='213.131.36.62')
            {
                exit;
            }
            if ($_SERVER['HTTPS']!="on")
            {
                exit;
            }
            if (!isset($_SERVER['PHP_AUTH_USER']))
            {
                header ('WWW-Authenticate: Basic realm="lit.ge bog"');
                header ('HTTP/1.0 401 Unauthorized');
                exit;
            }
            elseif ($_SERVER['PHP_AUTH_USER']!='liter100bog' || $_SERVER['PHP_AUTH_PW']!='gob002!ARqED#@$t34asi')
            {
                exit;
            }
        }

        function bog_reader ($transaction_id)
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
                    $bog_deposit_to = false;
                    if ($bog_deposit['to'])
                    {
                        $bog_deposit['to'] = str_replace (array("'",'"',"\\"),"",$bog_deposit['to']);
                        $bog_deposit_to = mysql_value_query ("select user_id from users where user_login='".$bog_deposit['to']."' limit 1");
                        if (!$bog_deposit_to)
                        {
                            form_set_error ('bog_deposit[to]', 'მომხმარებელი არ მოიძებნა');
                        }
                    }

                    if ($bog_deposit && $bog_deposit['amount'] && !$html_errors)
                    {
                        $transactions_object = transactions_transaction_object (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $bog_deposit['amount']);
                        if (!$transactions_error)
                        {
                            //debug_var ($transactions_object['transfer_amount']['sum']);
                            $transactions_id = transactions_transaction_add (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $bog_deposit['amount'], $bog_deposit_to);
                            if (!$transactions_error)
                            {
                                #### FOREIGN CODE ##########

                                $transactions_key = md5 ($transactions_id);
                                $transactions_object['transfer_currency']['name'] = strtoupper($transactions_object['transfer_currency']['name']);

                                mysql_debug_query ("update transactions set transaction_key='".$transactions_key."' where transaction_id='".$transactions_id."'");

                                $bog_deposit_settings['url_success'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_success']));
                                $bog_deposit_settings['url_fail'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_fail']));
                                html_add_redirect ("https://sb3d.georgiancard.ge/payment/start.wsm?merch_id=0F38ADA0D892DFEC7F41AC103AB527E9&page_id=CCEC780D371DDC1897AD7C0B3BFA948D&back_url_s=".$bog_deposit_settings['url_success']."&back_url_f=".$bog_deposit_settings['url_fail']."&lang=KA&o.order_id=".$transactions_key, 0, true);
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
                        form_open ('test', $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$transactions_system,'transactions_type'=>$transactions_type, 'transactions_state'=>'init')));
                        if ($user_device==\shop\platform::pc)
                        {
                            form_open_columns ();
                        }
                        form_add_label ('<b>ბალანსის შევსება</b>');
                        form_add_hidden ('apage', $apage);
                        form_add_label ('<img src="'.$project_url.'images/warn.gif" style="vertical-align:middle">','<div style="width:280px"><font color="#507171"><img src="'.$project_url.'images/cashier/bog.png" style="vertical-align:middle;float:right">მითითებულ თანხას დაემატება ბანკის საკომისიო - 50 თეთრი.</font></div>');
                        form_add_spacer ();
                        form_add_edit ('bog_deposit[amount]', 'თანხა', $bog_deposit['amount'], '280px');
//                        form_add_label ("<div style='height:30px'>");
//                        form_add_label ('', '<div style="width:280px"><font color="#507171"><b>სხვა მომხმარებლისთვის</b> თანხის ჩასარიცხად, დამატებით მიუთითეთ მომხმარებლის სახელი (login):</font></div>');
//                        form_add_edit ('bog_deposit[to]', 'მომხმარებელი', $bog_deposit['to'], '280px');
//                        form_add_label ("<div style='height:10px'>");
                        form_add_submit ('გაგრძელება');
                        form_add_label ("<div style='height:10px'>");
                        form_add_label ('', '<div style="width:280px"><font color="#507171" style="font-size:10px">
<b>გაგრძელება გადაგიყვანთ ტრანზაქციის გვერდზე, სადაც უნდა შეიყვანოთ:</b><p>
<b>PAN:</b> ბარათის 16-ნიშნა ნომერი. იხილეთ ბარათის წინა მხარეს, შეიყვანეთ სრულად, ინტერვალის და ტირეს გარეშე;
<br><b>CVC2:</b> ბარათის ვერიფიკაციის კოდი. იხილეთ ბარათის უკანა მხარეს, შეიყვანეთ ბოლო 3 ციფრი;
<br><b>მოქმედების ვადა:</b>  იხილეთ ბარათის წინა მხარეს, შეიყვანეთ თვე, წელიწადი.</font></div>');
                        if ($user_device==\shop\platform::pc)
                        {
                            form_add_column ();
                            form_add_label ("<b style='margin-left:20px;'>ნიმუში</b>");
                            form_add_label ("<img style='margin-left:20px;' src='".$project_url.$image_dir."/visa_hint.jpg'>");
                            form_close_columns();
                        }
                        form_close ();
                    }
                    else
                    {
                        html_parse_text ('cashier_processing');
                    };
                };
            };

//            debug_var ($apage);
//            debug_var ($transactions_system);

            if ((transactions_type_name($transactions_type)=='deposit' || transactions_type_name($transactions_type)=='reader' || transactions_type_name($transactions_type)=='tablet') && $transactions_state=='avalible')
            {
                bog_validate ();
                //TODO: http basic auth
                //check secure ssl

                //$bog_deposit_settings['key'] = "b52df0974763ace55a0f3dced6bc0c68";

                $transactions_post['mid'] = $_REQUEST['merch_id'];
                $transactions_post['tid'] = $_REQUEST['o_order_id'];
                $transactions_post['date'] = $_REQUEST['ts'];
                $transactions_post['identify'] = $_REQUEST['trx_id'];
                $transactions_post['lang'] = $_REQUEST['lang'];
                if ($_REQUEST['OrderParams'])
                {

                }

                $log = '$transactions_post[\'tid\'] = \''.$transactions_post['TransactionID'].'\';
                $transactions_post[\'lang\'] = \''.$transactions_post['language'].'\';';

                transactions_transaction_log ($log);
                if ($transactions_post['tid'])
                {
                    $transaction = mysql_object ('transactions', 'transaction_key', $transactions_post['tid']);
                }

                if ($transactions_post['tid'] && $transaction['status']==1)
                {
                    if (transactions_type_name($transaction['type'])=='reader')
                    {
                        $transaction_message = 'იოტა რიდერის შეძენა';
                    }
                    elseif (transactions_type_name($transaction['type'])=='tablet')
                    {
                        $transaction_message = 'იოტაბის შეძენა';
                    }
                    else
                    {
                        $transaction_message = 'ბალანსის შევსება';
                    }
                    $transactions_id = $transaction['id'];

                    if ($transaction['id'])
                    {
                        transactions_transaction_log ("<payment-avail-response>
  <result>
    <code>1</code>
    <desc>OK</desc>
  </result>
    <merchant-trx>".$transaction['key']."</merchant-trx>
  <purchase>
    <shortDesc>".$transaction_message."</shortDesc>
    <longDesc>".$transaction_message."</longDesc>
    <account-amount>
      <id>F61406704E1C98145D9D91F29849FD7D</id>
      <amount>".intval(($transaction['system_amount']+$transaction['system_com'])*100)."</amount>
      <currency>981</currency>
      <exponent>2</exponent>
    </account-amount>
  </purchase>
</payment-avail-response>", 1);

                        html_exit ("<payment-avail-response>
  <result>
    <code>1</code>
    <desc>OK</desc>
  </result>
    <merchant-trx>".$transaction['key']."</merchant-trx>
  <purchase>
    <shortDesc>".$transaction_message."</shortDesc>
    <longDesc>".$transaction_message."</longDesc>
    <account-amount>
      <id>F61406704E1C98145D9D91F29849FD7D</id>
      <amount>".intval(($transaction['system_amount']+$transaction['system_com'])*100)."</amount>
      <currency>981</currency>
      <exponent>2</exponent>
    </account-amount>
  </purchase>
</payment-avail-response>");
                    }
                    else
                    {
                        transactions_transaction_log ("bog: avalible check - with key ".$transactions_post['tid']." cant be found", 2);
                    };
                }
                else
                {
                    transactions_transaction_log ("bog: avalible check - deposit has not post info", 2);
                };
                html_exit ("<payment-avail-response>
  <result>
    <code>2</code>
    <desc>ტრანზაქცია არ მოიძებნა</desc>
  </result>
</payment-avail-response>
                    ");
            };

            if ((transactions_type_name($transactions_type)=='deposit' || transactions_type_name($transactions_type)=='reader' || transactions_type_name($transactions_type)=='tablet') && $transactions_state=='complete')
            {
                bog_validate ();
                //debug_var ($_REQUEST);

                //$bog_deposit_settings['key'] = "b52df0974763ace55a0f3dced6bc0c68";

                $transactions_post['mid'] = $_REQUEST['merch_id'];
                $transactions_post['remote'] = $_REQUEST['trx_id'];
                $transactions_post['tid'] = $_REQUEST['o_order_id'];
                $transactions_post['result'] = $_REQUEST['result_code'];
                $transactions_post['amount'] = $_REQUEST['amount'];
                $transactions_post['account'] = $_REQUEST['account_id'];
                $transactions_post['ts'] = $_REQUEST['date'];
                if ($_REQUEST['OrderParams'])
                {

                }

                $log = '$transactions_post[\'tid\'] = \''.$transactions_post['TransactionID'].'\';
                $transactions_post[\'lang\'] = \''.$transactions_post['language'].'\';';

                transactions_transaction_log ($log);

                if ($transactions_post['tid'])
                {
                    $transactions_id = mysql_value_query ("select transaction_id from transactions where transaction_key='".$transactions_post['tid']."' and transaction_status=1");

                    if ($transactions_id)
                    {
                        //$bog_deposit_settings['mac'] = hash_hmac_($bog_deposit_settings['key'], $transactions_post['mid']."|".$transactions_post['tid']."|".$transactions_post['status']."|".$transactions_post['ercode']."|".$transactions_post['message']."|".$transactions_post['amount']."|".$transactions_post['currency']."|".$transactions_post['product_desc']);

                        if ($transactions_post['result']=="1")
                        {
                            transactions_transaction_complete ($transactions_id);

                            if (!$transactions_error)
                            {
                                $transaction = mysql_object ('transactions', 'transaction_id', $transactions_id);
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
                                    bog_reader ($transactions_id);
                                }
                                html_exit ("<register-payment-response>
  <result>
    <code>1</code>
    <desc>OK</desc>
  </result>
</register-payment-response>
");

                            }
                        }
                        else
                        {
                            transactions_transaction_log ("bog: avalible check - with key ".$transactions_post['tid']." cant be found", 2);
                            transactions_transaction_fail ($transactions_id);
                        }
                    }
                    else
                    {
                        transactions_transaction_log ("bog: with key ".$transactions_post['tid']." cant be found", 2);
                    };
                }
                else
                {
                    transactions_transaction_log ("bog: deposit has not post info", 2);
                };
                html_exit ("<register-payment-response>
  <result>
    <code>2</code>
    <desc>Temporary unavailable</desc>
  </result>
</register-payment-response>
");
            };
        };
    };

    if (defined('engine_admin'))
    {
        $transactions_module_result = true;
    };

?>