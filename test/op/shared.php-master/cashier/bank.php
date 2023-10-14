<?php

    #################################################
    #### BANKWIRE SYSTEM TRANSACTIONS PROCESSING ####
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
                    html_parse_text ($transactions_systems[$transactions_system]['name'].'_'.$transactions_types[$transactions_type]['name']);
                };

                #### WITHDRAW [INIT] ####
                if (transactions_type_name($transactions_type)=='withdraw' && $transactions_state=='init')
                {
                    if ($bank_withdraw && form_valid('bank_withdraw',$bank_withdraw))
                    {
                        $transactions_object = transactions_transaction_object (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $bank_withdraw['amount']);
                        if (!$transactions_error)
                        {
                            //debug_var ($transactions_object);
                            $transactions_id = transactions_transaction_add (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $bank_withdraw['amount']);
                            if (!$transactions_error)
                            {
                                $transactions_object['saved'] = true;
                                form_save ('bank_withdraw', $bank_withdraw, $transactions_id, $user['id']);
                            };
                        };
                    };
                    if ($transactions_error)
                    {
                        html_add_message ($transactions_error);
                    };
                    if (!$transactions_object['saved'])
                    {
                        form_draw ('bank_withdraw', $bank_withdraw);
                    }
                    else
                    {
                        html_add_redirect ($project_url.seo_encode(array('apage'=>$pages_settings['page_transaction_success'])));
                    };
                };
            };
        };
    };

    if (defined('engine_admin'))
    {
        $transactions_module_result = true;
    };

?>