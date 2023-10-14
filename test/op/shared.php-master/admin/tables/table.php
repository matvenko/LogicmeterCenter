<?php

    tables_incomes_prepare ();

    tables_delete_case ();

    tables_delete_case_multi ();

    tables_insert_case ();

    tables_update_case ();

    tables_update_case_multi ();

    tables_position_case ();

    if (tables_edit_case())
    {
        tabs_open ();

        tables_edit_form ();

        tables_relations_byfield_slave ();

        tables_relations_byrow_master ();

        tables_relations_byrow_slave ();

        tabs_close ();
    }
    else
    {

        tables_order_prepare ();

        tables_search_prepare ();

        tables_select_query ();

        tabs_open ();
        tables_search_form ();
        tables_add_form ();
        tabs_close ();

        tables_result_table ();

    };

?>