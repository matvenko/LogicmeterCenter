<?php

    tables_load_table ('transactions');

    $tables_parallel_caching = false;

    tables_incomes_prepare ();

    if (is_array($transaction))
    {
        if (!$transaction['user']) form_set_error ('transaction[user]');
        //if (!$transaction['on']) form_set_error ('transaction[on]');
        if (!$transaction['type']) form_set_error ('transaction[type]');
        if (!$transaction['system']) form_set_error ('transaction[system]');
        if (!floatval($transaction['amount'])) form_set_error ('transaction[amount]');

        if (!$html_errors)
        {
            $transaction_id = transactions_transaction_add ($transaction['user'], $transactions_settings['main_balance'], $transaction['system'], $transaction['type'], $transaction['amount'], 0, $transaction['currency']);
            if (!$transactions_error)
            {
                transactions_transaction_complete ($transaction_id);
                $transactions_success = true;
                unset ($transaction);
            };
        };
    };

    tables_delete_case ();

    tables_delete_case_multi ();

    tables_insert_case ();

    if ($update['transaction_status'])
    {
        $transactions_status = $update['transaction_status'];
        $update['transaction_status_'] = $update['transaction_status'];
        $update['transaction_status'] = mysql_value ('transactions','transaction_status','transaction_id',"'".$update['transaction_id']."'");
    };
    if (tables_update_case ())
    {
        $transaction = mysql_object ('transactions', 'transaction_id', $update['transaction_id']);
        if (transactions_module_exists($transaction['system']))
        {
            $transactions_state = 'update';
            include transactions_module_file ($transaction['system']);
            if (!$transactions_module_result && !transactions_error)
            {
                transactions_error_set ('general_module_fail');
            }
            elseif ($transactions_module_result && !$transactions_error)
            {
                transactions_transaction_status ($transaction['id'], $transactions_status);
            };
        }
        else
        {
            transactions_transaction_status ($update['transaction_id'], $update['transaction_status_']);
        };
    };

    if (is_array($multi_set) && is_array($multi_checked)  && $multi_update && $table['ismulti_update'])
    {
        foreach ($fields as $field_name => $field)
        {
            if ($field['multi_update'] || $field['on_update'])
            {
                if ($field['max'] && strlen($multi_set[$table_prefix.$field_name])>$field['max'])
                {
                    form_set_error ('multi_set['.$table_prefix.$field_name.']', str_replace(array('[field_caption]','[field_max]'),array($field['caption'],$field['max']),t('libs.tables.error_field_max','[field_caption] must be max [field_max] symbols')));
                };
                if ($field['min'] && strlen($multi_set[$table_prefix.$field_name])<$field['min'])
                {
                    form_set_error ('multi_set['.$table_prefix.$field_name.']', str_replace(array('[field_caption]','[field_min]'),array($field['caption'],$field['min']),t('libs.tables.error_field_min','[field_caption] must be min [field_min] symbols')));
                };
                if ($field['on_update']=='1' && ($field['type']=='date' || $field['type']=='time' || $field['type']=='datetime'))
                {
                    $multi_set_query .= $table_prefix.$field_name."='".to_nulldate()."', ";
                    $multi_set_passed [$field_name] = true;
                }
                if ($field['on_update']=='2')
                {
                    $multi_set_query .= $table_prefix.$field_name."='".user_id()."', ";
                    $multi_set_passed [$field_name] = true;
                };
                if ($field_name=='status')
                {
                    $multi_set_passed[$field_name] = true;
                };
                if (!$multi_set_passed[$field_name] && $multi_set[$table_prefix.$field_name]!='*')
                {
                    //debug_var ($field_name);
                    $multi_set_query .= $table_prefix.$field_name."='".$multi_set[$table_prefix.$field_name]."', ";
                };
            };
        };
        $multi_set_query = before_last (", ",$multi_set_query);
        if (!$html_errors)
        {
            foreach ($multi_checked as $multi_id => $multi_value)
            {
                #################
                $transactions_status = $multi_set['transaction_status'];
                $transaction = mysql_object ('transactions', 'transaction_id', $multi_id);
                if (transactions_module_exists($transaction['system']))
                {
                    $transactions_state = 'update';
                    include transactions_module_file ($transaction['system']);
                    if (!$transactions_module_result && !transactions_error)
                    {
                        transactions_error_set ('general_module_fail');
                    }
                    elseif ($transactions_module_result && !$transactions_error)
                    {
                        transactions_transaction_status ($transaction['id'], $transactions_status);
                    };
                }
                else
                {
                    transactions_transaction_status ($multi_id, $multi_set['transaction_status']);
                };
                #################
                //if ($transactions_error) $transactions_errors .= $transactions_error.'<br>';
                mysql_debug_query ("update $table_name set $multi_set_query where ".$table_prefix."id='".intval($multi_id)."' limit 1");
            };
        };
    };

    tables_position_case ();

    if (tables_edit_case())
    {
        tabs_open ($table_name);

        tables_edit_form ();

        form_restore ($transactions_systems[$row['transaction_system']]['name'].'_'.$transactions_types[$row['transaction_type']]['name'], $edit);

        tables_relations_byfield_slave ();

        tables_relations_byrow_master ();

        tables_relations_byrow_slave ();

        tabs_close ();

        //debug_echo ($transactions_systems[$row['transaction_system']]['name'].'_'.$transactions_types[$row['transaction_type']]['name']);
    }
    else
    {

        tables_order_prepare ();

        tables_search_prepare ();

        tables_select_query ();


        //if ($transaction) transactions_transaction_complete ($transaction);
        if ($html_errors) html_add_message (t('libs.transactions.error_fields_required','Please fill all transaction fields'));
        if (count($transactions_errors)>1) html_debug_var ($transactions_errors);
        elseif ($transactions_error) html_add_message ($transactions_error);
        if ($transactions_success) html_add_message (t('libs.transactions.transaction_added'));

        tabs_open ();
        tables_search_form ();
        tables_add_form ();
        tabs_open_panel (t('libs.transactions.transaction'));
        form_add_hidden ('apage',$apage);
        form_add_autocomplete ('transaction[user]', t('libs.transactions.user'), $transaction['user'], 'users', 'user_id', 'user_login', '', '261');
        //form_add_select ('transaction[on]', t('libs.transactions.balance'), $transaction['on']/*$transactions_settings['main_balance']*/, $fields['on']['sets']);
        form_add_select ('transaction[type]', t('libs.transactions.transaction'), $transaction['type'], array('0'=>'...')+$fields['type']['sets']);
        form_add_select ('transaction[system]', t('libs.transactions.system'), $transaction['system'], array('0'=>'...')+$fields['system']['sets']);
        form_add_edit ('transaction[amount]', t('libs.transactions.amount'), floatval($transaction['amount']));
        form_add_select ('transaction[currency]', t('libs.transactions.currency'), $transaction['currency'], array('0'=>'...')+$fields['system_currency']['sets']);
        form_add_submit (t('add'));
        tabs_close_panel ();
        tabs_close ();

        tables_result_table ();

    };

?>