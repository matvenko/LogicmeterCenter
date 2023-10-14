<?php


    if ($query_delete)
    {
        unset ($_SESSION['tables_querys'][$query_delete]);
    }

    if (!isset($_SESSION['tables_querys']))
    {
        $_SESSION['tables_querys'] = array ();
    }
    elseif ($_SESSION['tables_querys'])
    {
        table_open ();
        table_open_record_header ();
        table_add_cell_header (t('recent_queries'));
        table_close_record ();
    }
    foreach ($_SESSION['tables_querys'] as $key => $value)
    {
        table_open_record ();
        table_add_cell ($value);
        table_add_cell_delete_ ('delete.png', $redirect_url."?apage=".$apage."&query_delete=".$key);
        table_add_cell_button_ ('edit.png', $redirect_url."?apage=".$apage."&query_paste=".$key);
        table_close_record ();
    }
    if ($_SESSION['tables_querys'])
    {
        table_close ();
    }

    if ($query)
    {
        $query = str_replace("\'","'",$query);        
    };

    form_open();
    form_add_hidden ('apage',$apage);
    if ($query_paste)
    {
        form_add_memo ('query', '', $_SESSION['tables_querys'][$query_paste], '150');
    }
    else
    {
        form_add_memo ('query', '', $query, '150');
    };
    form_add_submit (t('execute'));
    form_close ();

    if ($query)
    {
        $result = mysql_debug_result ($query);
        if (!mysql_errno())
        {
            $query_hash = md5($query);

            if (isset($_SESSION['tables_querys'][$query_hash]))
            {
                unset($_SESSION['tables_querys'][$query_hash]);
            };
            $_SESSION['tables_querys'][$query_hash] = $query;
        }
    };

?>