<?php

    if ($transactions_system && $transactions_type && $transactions_systems[$transactions_system] && $transactions_types[$transactions_type] && $transactions_systems[$transactions_system]['methods'][$transactions_type] && $transactions_states[$transactions_state])
    {
        if (transactions_module_exists($transactions_system))
        {
            include transactions_module_file($transactions_system);
        }
        else
        {
            $apage = $pages_settings['page_transaction_notavalible'];
        };
    }
    else
    {

        $apage = $pages_settings['page_transaction_notavalible'];
    };

?>