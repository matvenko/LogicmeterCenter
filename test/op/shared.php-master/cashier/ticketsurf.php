<?php

    #################################################
    #### ticketsurfWIRE SYSTEM TRANSACTIONS PROCESSING ####
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

    if (defined('engine_site'))
    {
        if ($transactions_system && $transactions_type && $transactions_systems[$transactions_system] && $transactions_types[$transactions_type] && $transactions_systems[$transactions_system]['methods'][$transactions_type] && $transactions_states[$transactions_state])
        {
            if (user_logged())
            {
                #### DEPOSIT [INIT] ####
                if (transactions_type_name($transactions_type)=='deposit' && $transactions_state=='init')
                {
                    if ($ticketsurf_deposit && form_valid('ticketsurf_deposit',$ticketsurf_deposit))
                    {
                        $transactions_object = transactions_transaction_object (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $ticketsurf_deposit['amount']);
                        if (!$transactions_error)
                        {
                            //debug_var ($transactions_object['transfer_amount']['sum']);
                            $transactions_id = transactions_transaction_add (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $ticketsurf_deposit['amount']);
                            if (!$transactions_error)
                            {
                                #### FOREIGN CODE ##########

                                $transactions_key = md5 ($transactions_id);
                                $transactions_object['transfer_currency']['name'] = strtoupper($transactions_object['transfer_currency']['name']);

                                mysql_debug_query ("update transactions set transaction_key='".$transactions_key."' where transaction_id='".$transactions_id."'");

                                $ticketsurf_deposit_settings['url_success'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_success']));
                                $ticketsurf_deposit_settings['url_fail'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_fail']));
                                if (!$ticketsurf_deposit_settings['url_post'])
                                {
                                    $ticketsurf_deposit_settings['url_post'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$transactions_system,'transactions_type'=>$transactions_type, 'transactions_state'=>'post'));
                                };

                                $ticketsurf_deposit_settings['mac'] = hash_hmac_ ($ticketsurf_deposit_settings['key'], "".$ticketsurf_deposit_settings['mid']."|".$transactions_key."|".$transactions_object['transfer_amount']['sum']."|".$transactions_object['transfer_currency']['name']."|".$ticketsurf_deposit_settings['key_id']."|".$ticketsurf_deposit_settings['desc']."|".$ticketsurf_deposit_settings['url_success']."|".$ticketsurf_deposit_settings['url_fail']."|".$ticketsurf_deposit_settings['url_post']."|".$ticketsurf_deposit_settings['debit_all']."|".$ticketsurf_deposit_settings['th']."");

                                //$temp = array("mac"=>$ticketsurf_deposit_settings['mac'],"mid"=>$ticketsurf_deposit_settings['mid'],"tid"=>$transactions_key,"amount"=>$transactions_object['transfer_amount']['sum'],"currency"=>$transactions_object['transfer_currency']['name'],"key_id"=>$ticketsurf_deposit_settings['key_id'],"product_desc"=>$ticketsurf_deposit_settings['desc'],"url_ok"=>$ticketsurf_deposit_settings['url_success'],"url_nok"=>$ticketsurf_deposit_settings['url_fail'],"url_s2s"=>$ticketsurf_deposit_settings['url_post'],"debit_all"=>$ticketsurf_deposit_settings['debit_all'],"th"=>$ticketsurf_deposit_settings['th']);
                                //debug_var ($temp);
                                $result = http_post ("https://nts0.ticket-surf.com/NTS/SCRIPTS/RT/ts_prepayment.php", array("mac"=>$ticketsurf_deposit_settings['mac'],"mid"=>$ticketsurf_deposit_settings['mid'],"tid"=>$transactions_key,"amount"=>$transactions_object['transfer_amount']['sum'],"currency"=>$transactions_object['transfer_currency']['name'],"key_id"=>$ticketsurf_deposit_settings['key_id'],"product_desc"=>$ticketsurf_deposit_settings['desc'],"url_ok"=>$ticketsurf_deposit_settings['url_success'],"url_nok"=>$ticketsurf_deposit_settings['url_fail'],"url_s2s"=>$ticketsurf_deposit_settings['url_post'],"debit_all"=>$ticketsurf_deposit_settings['debit_all'],"th"=>$ticketsurf_deposit_settings['th']));

                                if($result=="1")
                                {
                                     html_add_redirect ("https://nts0.ticket-surf.com/NTS/SCRIPTS/RT/ts_payment.php?lang=en&id=".$ticketsurf_deposit_settings['key_id']."&tid=".$transactions_key);
                                     $transactions_object['saved'] = true;
                                     form_save ('ticketsurf_deposit', $ticketsurf_deposit, $transactions_id, $user['id']);
                                }
                                else
                                {
                                     html_parse_text ('cashier_transaction_fail');
                                     transactions_transaction_fail ($transactions_id);
                                };

                                //debug_var ($result);

                                #### FOREIGN CODE ##########

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
                    if (!$transactions_object['saved'])
                    {
                        form_draw ('ticketsurf_deposit', $ticketsurf_deposit);
                    }
                    else
                    {
                        html_parse_text ('cashier_transaction_process');
                    };
                };
            };

            if (transactions_type_name($transactions_type)=='deposit' && $transactions_state=='post')
            {
                //debug_var ($_POST);

                $ticketsurf_deposit_settings['key'] = "b52df0974763ace55a0f3dced6bc0c68";

                $transactions_post['authid'] = $_POST['authid'];
                $transactions_post['mac'] = $_POST['mac'];
                $transactions_post['mid'] = $_POST['mid'];
                $transactions_post['tid'] = $_POST['tid'];
                $transactions_post['status'] = $_POST['status'];
                $transactions_post['ercode'] = $_POST['ercode'];
                $transactions_post['message'] = $_POST['message'];
                $transactions_post['amount'] = $_POST['amount'];
                $transactions_post['currency'] = $_POST['currency'];
                $transactions_post['product_desc'] = $_POST['product_desc'];

                $log = '$transactions_post[\'authid\'] = \''.$transactions_post['authid'].'\';
                $transactions_post[\'mac\'] = \''.$transactions_post['mac'].'\';
                $transactions_post[\'mid\'] = \''.$transactions_post['mid'].'\';
                $transactions_post[\'tid\'] = \''.$transactions_post['tid'].'\';
                $transactions_post[\'status\'] = \''.$transactions_post['status'].'\';
                $transactions_post[\'ercode\'] = \''.$transactions_post['ercode'].'\';
                $transactions_post[\'message\'] = \''.$transactions_post['message'].'\';
                $transactions_post[\'amount\'] = \''.$transactions_post['amount'].'\';
                $transactions_post[\'currency\'] = \''.$transactions_post['currency'].'\';
                $transactions_post[\'product_desc\'] = \''.$transactions_post['product_desc'].'\';';

                transactions_transaction_log ($log);

                html_add_message ('test');

                //debug_var ($redirect_url);

                form_open ('post', $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$transactions_system,'transactions_type'=>$transactions_type, 'transactions_state'=>'post')));
                form_add_hidden ('authid', '1331651');
                form_add_hidden ('mac', '303f96331d1efbab515b303d47d0022c');
                form_add_hidden ('mid', '735');
                form_add_hidden ('tid', '01161aaa0b6d1345dd8fe4e481144d84');
                form_add_hidden ('status', 'OK');
                form_add_hidden ('ercode', '0');
                form_add_hidden ('message', 'OK');
                form_add_hidden ('amount', '1.00');
                form_add_hidden ('currency', 'EUR');
                form_add_hidden ('product_desc', 'Your deposit at Bookmakers.pro');
                form_add_submit ('submit');
                form_close ();

                if ($transactions_post['tid'])
                {
                    $transactions_id = mysql_value ('transactions','transaction_id','transaction_key',"'".$transactions_post['tid']."'");

                    if ($transactions_id)
                    {
                        $ticketsurf_deposit_settings['mac'] = hash_hmac_($ticketsurf_deposit_settings['key'], $transactions_post['mid']."|".$transactions_post['tid']."|".$transactions_post['status']."|".$transactions_post['ercode']."|".$transactions_post['message']."|".$transactions_post['amount']."|".$transactions_post['currency']."|".$transactions_post['product_desc']);

                        if ($transactions_post['mac']==$ticketsurf_deposit_settings['mac'] && $transactions_post['status']=="OK")
                        {
                            transactions_transaction_complete ($transactions_id);
                            html_exit ("ACC=OK");
                        }
                        else
                        {
                            transactions_transaction_log ("ticketsurf: message [".$transactions_post['message']."] ercode[".$transactions_post['ercode']."] at transaction [".int_zeros_right($transaction_id,5)."]");
                            transactions_transaction_fail ($transactions_id);
                        }
                    }
                    else
                    {
                        transactions_transaction_log ("ticketsurf: with key ".$transactions_post['tid']." cant be found", 2);
                    };
                }
                else
                {
                    transactions_transaction_log ("ticketsurf: deposit has not post info", 2);
                };
                html_exit ();
            };
        };
    };

    if (defined('engine_admin'))
    {
        $transactions_module_result = true;
    };

?>