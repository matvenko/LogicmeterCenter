<?php

    if (user_logged())
    {

        tables_load_table ('transactions');

        
        //debug_var ($table);
        $table['readonly'] = true;
        $table['edit'] = false;
        $table['delete'] = false;
        $table['add'] = false;
        $table['multi_update'] = false;
        $table['multi_delete'] = false;
        $table['columns_search'] = 1;
        $table['ismulti_update'] = false;
        
        unset ($fields['on']);
        unset ($fields['user']);
        unset ($fields['system_amount']);
        unset ($fields['system_currency']);
        unset ($fields['system_com']);
        unset ($fields['key']);
        unset ($fields['external']);
        unset ($fields['group']);
        unset ($fields['pair']);
        
        tables_incomes_prepare ();

        tables_order_prepare ();

        tables_search_prepare ();

        $search_query .= " and transaction_user='".user_id()."'";

        tables_select_query ();

        tables_search_form ();

        tables_result_table ();


    };

?>