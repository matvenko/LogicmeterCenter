<?php

    ######################################################
    #### IGBLOBAL WIRE SYSTEM TRANSACTIONS PROCESSING ####
    ######################################################
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

    if (!function_exists('form_valid_iglobal_withdraw_cardno'))
    {
        function form_valid_iglobal_withdraw_cardno ($value, $where)
        {
            if ($value=='1000000000000001' || $value=='1000000000000002' || $value=='1000000000000003' || $value=='1000000000000004' || !transactions_card_valid($value))
            {
                form_set_error ($where,t('libs.transactions.card_not_valid'));
                return false;
            };
            return true;
        };
    };

    if (!function_exists('form_valid_iglobal_deposit_cardno'))
    {
        function form_valid_iglobal_deposit_cardno ($value, $where)
        {
            if ($value=='1000000000000001' || $value=='1000000000000002' || $value=='1000000000000003' || $value=='1000000000000004' || !transactions_card_valid($value))
            {
                form_set_error ($where,t('libs.transactions.card_not_valid'));
                return false;
            };
            return true;
        };
    };

    if (defined('engine_site'))
    {
        if ($transactions_system && $transactions_type && $transactions_systems[$transactions_system] && $transactions_types[$transactions_type] && $transactions_systems[$transactions_system]['methods'][$transactions_type] && $transactions_states[$transactions_state])
        {
            if (user_logged())
            {
                #### DEPOSIT [INIT] ####
                if (transactions_type_name($transactions_type)=='deposit' && $transactions_state=='init')
                {
                    if (isset($iglobal_deposit['amount']))
                    {
                        $iglobal_deposit['amount'] = round($iglobal_deposit['amount'],2);
                    };
                    if ($iglobal_deposit && form_valid('iglobal_deposit',$iglobal_deposit))
                    {
                        $transactions_object = transactions_transaction_object (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $iglobal_deposit['amount']);
                        if (!$transactions_error)
                        {
                            $transactions_id = transactions_transaction_add (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $iglobal_deposit['amount']);
                            if (!$transactions_error)
                            {
                                #### FOREIGN CODE ##########

                                $transactions_object['transfer_currency']['name'] = strtoupper($transactions_object['transfer_currency']['name']);

                                $iglobal_settings['url_success'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_success']));
                                $iglobal_settings['url_fail'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_fail']));

                                $iglobal_settings['password'] = 'dDjRlK2UPgKcO0rL';
                                //$iglobal_settings['description'] = 'Bookmakers balance';
                                //$iglobal_settings['userid'] = '0001';
                                //$iglobal_settings['merchantid'] = 'bmakers';
                                //$iglobal_settings['url_request'] = 'https://spg.seasus.com/Request';
                                ///////////////////////////////////////////////////////
                                //$iglobal_deposit['cardno'] = '1000000000000001';
                                //$iglobal_deposit['expiry_y'] = '2013';
                                //$iglobal_deposit['cvv'] = '034';
                                //$iglobal_deposit['expiry_m'] = '02';
                                //$iglobal_deposit['amount'] = 1;
                                //$iglobal_deposit['cardholder'] = 'Test user';

                                $data = "";
                                $data .= "userid=".$iglobal_settings['userid']."&";
                                $data .= "password=".$iglobal_settings['password']."&";
                                $data .= "type=1&";
                                $data .= "cardholder=".$iglobal_deposit['cardholder']."&";
                                $data .= "productdescription=".$iglobal_settings['description']."&";
                                $data .= "surname=".$user['name_last']."&";
                                $data .= "cardno=".$iglobal_deposit['cardno']."&";
                                $data .= "ip=".$user_ip."&";
                                $data .= "expiry_y=".$iglobal_deposit['expiry_y']."&";
                                $data .= "cvv=".$iglobal_deposit['cvv']."&";
                                $data .= "expiry_m=".$iglobal_deposit['expiry_m']."&";
                                $data .= "price=".round($transactions_object['transfer_amount']['sum'],2)."&";
                                $data .= "merchantid=".$iglobal_settings['merchantid']."&";
                                $data .= "name=".$user['name_first']."";

                                parse_str ($data, $data_);
                                $result = http_post ($iglobal_settings['url_request'], $data_);
                                $output = array();
                                parse_str ($result, $output);

                                //debug_var ($output);

                                if($output['status']=='1000')
                                {
                                     $transactions_object['saved'] = true;
                                     form_save ('iglobal_deposit', array('cvv'=>'')+$iglobal_deposit, $transactions_id, $user['id']);
                                     transactions_transaction_complete ($transactions_id);
                                     mysql_debug_query ("update transactions set transaction_key='".$output['banktransid']."', transaction_comment='".$output['auth']."' where transaction_id='".$transactions_id."'");
                                     html_add_redirect ($project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_success'])));
                                }
                                else
                                {
                                     html_parse_text ('cashier_transaction_fail');
                                     html_add_message (ucwords($output['report']));
                                     //html_add_p ();
                                     transactions_transaction_fail ($transactions_id);
                                };

                                //debug_var ($result);

                                #### FOREIGN CODE ##########

                            };
                        };
                    };
                    //debug_var ($result);
                    //debug_var ($output);
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
                        form_draw ('iglobal_deposit', $iglobal_deposit);
                    }
                    else
                    {
                        html_parse_text ('cashier_transaction_process');
                    };
                };

                if (transactions_type_name($transactions_type)=='withdraw' && $transactions_state=='init')
                {
                    if (isset($iglobal_withdraw['amount']))
                    {
                        $iglobal_withdraw['amount'] = round($iglobal_withdraw['amount'],2);
                    };
                    if ($iglobal_withdraw && form_valid('iglobal_withdraw',$iglobal_withdraw))
                    {
                        $transactions_object = transactions_transaction_object (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $iglobal_withdraw['amount']);
                        if (!$transactions_error)
                        {
                            $transactions_id = transactions_transaction_add (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $iglobal_withdraw['amount']);
                            if (!$transactions_error)
                            {
                                #### FOREIGN CODE ##########

                                $transactions_object['transfer_currency']['name'] = strtoupper($transactions_object['transfer_currency']['name']);

                                $iglobal_settings['url_success'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_success']));
                                $iglobal_settings['url_fail'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_fail']));

                                form_save ('iglobal_withdraw', $iglobal_withdraw, $transactions_id, $user['id']);
                                $transactions_object['saved'] = true;
                                html_add_redirect ($project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_success'])));

                            }
                            else
                            {
                                html_parse_text ('cashier_transaction_fail');
                            };
                        };
                    };
                    if ($transactions_error)
                    {
                        html_add_message ($transactions_error);
                    };
                    if (!$transactions_object['saved'])
                    {
                        form_draw ('iglobal_withdraw', $iglobal_withdraw);
                    };
                };
            };
        };

    };

    if (defined('engine_admin'))
    {
        $transactions_module_result = true;

        //debug_var ($transactions_systems[$transaction['system']]);
        //debug_var ($transactions_types[$transaction['type']]);
        //debug_var ($transactions_systems[$transaction['system']]['methods'][$transaction['type']]);
        //debug_var ($transactions_states);
        //debug_var (caption_to_name($transactions_statuses[$transaction['status']]['name']));
        //debug_echo (caption_to_name($transactions_statuses[$transactions_status]['name']));

        if ($transactions_systems[$transaction['system']] && $transactions_types[$transaction['type']] && $transactions_systems[$transaction['system']]['methods'][$transaction['type']] && $transactions_states[$transactions_state] && $transactions_statuses[$transactions_status] && transactions_can_update($transaction,$transactions_status))
        {
            if ($transactions_state=='update' && caption_to_name($transactions_statuses[$transactions_status]['name'])=='completed')
            {
                $transactions_module_result = false;
                form_load ($transactions_systems[$transaction['system']]['name'].'_'.$transactions_types[$transaction['type']]['name'], $transaction['id']);
                //debug_var ($iglobal_withdraw);

                $transaction['user'] = mysql_object ('users','user_id',$transaction['user']);
                $transaction['transfer_amount'] = transactions_transfer_amount ($transaction);

                //debug_var ($transaction);

                #### FOREIGN CODE ##########

                $iglobal_settings['password'] = 'dDjRlK2UPgKcO0rL';

                //$iglobal_withdraw['cardno'] = '1000000000000001';
                //$iglobal_withdraw['expiry_y'] = '2010';

                $data = "";
                $data .= "userid=".$iglobal_settings['userid']."&";
                $data .= "password=".$iglobal_settings['password']."&";
                $data .= "type=2&";
                $data .= "cardholder=".$iglobal_withdraw['cardholder']."&";
                $data .= "productdescription=".$iglobal_settings['description']."&";
                $data .= "surname=".$user['name_last']."&";
                $data .= "cardno=".$iglobal_withdraw['cardno']."&";
                $data .= "ip=".$user_ip."&";
                $data .= "expiry_y=".$iglobal_withdraw['expiry_y']."&";
                $data .= "cvv=".$iglobal_withdraw['cvv']."&";
                $data .= "expiry_m=".$iglobal_withdraw['expiry_m']."&";
                $data .= "price=".round($transaction['transfer_amount']['sum'],2)."&";
                $data .= "merchantid=".$iglobal_settings['merchantid']."&";
                $data .= "name=".$user['name_first']."";

                parse_str ($data, $data_);
                //debug_var ($data_);
                $result = http_post ($iglobal_settings['url_request'], $data_);
                $output = array();
                parse_str ($result, $output);

                //debug_var ($output);

                if($output['status']=='1000')
                {
                     $transaction['saved'] = true;
                     //transactions_transaction_complete ($transaction['id']);
                     mysql_debug_query ("update transactions set transaction_key='".$output['banktransid']."', transaction_comment='".$output['auth']."' where transaction_id='".$transaction['id']."'");
                     html_add_message (str_replace('[transaction_id]',int_zeros_right($transaction['id'],6), t('libs.bookmakers.transaction_success_id','Transaction [transaction_id] success')));
                     $transactions_module_result = true;
                }
                else
                {
                     html_add_message (ucwords($output['report']));
                     transactions_transaction_fail ($transaction['id']);
                     $transactions_module_result = false;
                };

                //debug_var ($result);
            };

        };

    };
?>