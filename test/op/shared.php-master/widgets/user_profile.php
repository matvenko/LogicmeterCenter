<?php

    $edit = user_id ();

    tables_load_table ('users');

    tables_incomes_prepare ();

    if (tables_update_case())
    {
        $values += mysql_fetch_assoc(mysql_debug_query ("select * from users where user_id='".user_id()."' limit 1"));
        $html[$widget_name.'_success'] = html_parse ($skins[$widget_name.'_success']);
    };

    tables_edit_form ();
    $values += $row;
    if ($values['user_email']!=$values['user_email_last'])
    {
        $html[$widget_name.'_validate'] = html_parse ($skins[$widget_name.'_validate']);
    };

?>