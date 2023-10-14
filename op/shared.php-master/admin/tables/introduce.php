<?php

    if ($skins['table_open_record_1'])
    {
        $skins['table_open_record'] = $skins['table_open_record_1'];
        unset ($skins['table_open_record_1'],$skins['table_open_record_2']);
    };

    function db_table_icon ($db_table_id)
    {
        global $db_table_name;
        if (!$db_table_id)
        {
            $location .= "&load=".$GLOBALS['html_next'].'#'.$db_table_name;
            return "<a href='$redirect_url?intro_table_add=".$db_table_name.$location."'><img id='".$db_table_name."' src='./images/table.png' border=0></a>";
        }
        else
        {
            return "<span id='".$db_table_name."'></span>";
        };
        return '';
    };

    if ($intro_fields_delete)
    {
        $result_table = mysql_debug_query ("select table_id from tables where table_name='".$intro_fields_delete."'");
        if (mysql_num_rows($result_table))
        {
            list ($intro_table_id) = mysql_fetch_row ($result_table);
        };
        //mysql_debug_query ("delete from tables where table_id='".$intro_table_id."'", false);
        mysql_debug_query ("delete from tables_fields where field_table='".$intro_table_id."'", false);
    };

    if ($intro_table_add)
    {
        $intro_table_prefix = tables_table_prefix ($intro_table_add);
        $result_table = mysql_debug_query ("select table_id from tables where table_name='".$intro_table_add."'");
        if (!mysql_num_rows($result_table))
        {
            if (before('_',$intro_table_add) && mysql_value_exists('admin_units', 'unit_name', "'".before('_',$intro_table_add)."'"))
            {
                $intro_table_parent = before ('_',$intro_table_add);
                $intro_table_icon = mysql_value ('admin_units','unit_icon','unit_name',"'".$intro_table_parent."'");
                $intro_table_caption = name_to_caption (after('_', $intro_table_add));
            }
            else
            {
                $intro_table_parent = 'tables';
                $intro_table_icon = '';
                $intro_table_caption = name_to_caption ($intro_table_add);
            }
            mysql_debug_query ("
            insert into tables set
            table_name='".$intro_table_add."',
            table_caption='".$intro_table_caption."',
            table_readonly='0',
            table_order_field='id',
            table_order_method='asc',
            table_edit='1',
            table_delete='1',
            table_add='1',
            table_result='1',
            table_search='1',
            table_search_defaults='0',
            table_report='0',
            table_found='0',
            table_count='1',
            table_multi_delete='1',
            table_multi_update='0',
            table_rows='50',
            table_comments='0',
            table_icon='".$intro_table_icon."',
            table_parent='".$intro_table_parent."',
            table_button_add_submit='',
            table_button_edit_submit='',
            table_button_edit_cancel='',
            table_button_search_submit='',
            table_button_search_clear='',
            table_button_readonly_back='',
            table_button_multi_delete='',
            table_button_multi_update='',
            table_numerate='1'
            ", false);
        };
    };

    if ($intro_fields_add)
    {
        $intro_table_prefix = tables_table_prefix ($intro_fields_add);
        $result_table = mysql_debug_query ("select table_id from tables where table_name='".$intro_fields_add."'");
        if (mysql_num_rows($result_table))
        {
            list ($intro_table_id) = mysql_fetch_row ($result_table);
        };

        $result_fields = mysql_debug_query ("show fields from ".$intro_fields_add);
        while (list($intro_field_name,$intro_field_type,$intro_field_null,$intro_field_key,$intro_field_default,$intro_field_extra)=mysql_fetch_row($result_fields))
        {
            $intro_field_name = after ($intro_table_prefix, $intro_field_name);
            if ($intro_field_name!='id' && !mysql_value_exists ('tables_fields','field_name',"'".$intro_field_name."' and field_table='".$intro_table_id."'"))
            {
                if (before('(',$intro_field_type))
                {
                    $intro_field_type_type = before('(',$intro_field_type);
                    $intro_field_type_length = between('(',')',$intro_field_type);
                }
                else
                {
                    $intro_field_type_type = $intro_field_type;
                    $intro_field_type_length = '';
                };
                if ($intro_field_type_type=='int')
                {
                    $intro_field_input = 'autocomplete';
                }
                elseif ($intro_field_type_type=='smallint')
                {
                    $intro_field_input = 'select';
                }
                else
                {
                    $intro_field_input = 'edit';
                };
                mysql_debug_query ("insert into tables_fields set
                field_table = ".$intro_table_id.",
                field_name = '".$intro_field_name."',
                ".field_name_localized('field_caption','tables_fields')." = '".name_to_caption($intro_field_name)."',
                field_type = '".$intro_field_type_type."',
                field_input = '".$intro_field_input."',
                field_length = '".$intro_field_type_length."',
                field_default = '".strval($intro_field_default)."',
                field_required = '0',
                field_add = '1',
                field_edit = '1',
                field_search = '1',
                field_search_term = '',
                field_result = '1',
                field_register = '0',
                field_readonly = '0',
                field_report = '1',
                field_unique = '0',
                field_max = '0',
                field_min = '0',
                field_width = '0',
                field_height = '0',
                field_spacer = '0',
                field_nulldate = '0',
                field_comment = '',
                field_multi_update = '0',
                field_on_update = '0',
                field_on_insert = '0',
                field_order = '".intval(intval(mysql_value('tables_fields','max(field_order)',"field_table","'".$intro_table_id."'")+1))."'
                ", false);
            };
        };
    };

    $result_tables = mysql_debug_query ("show tables");
    if (mysql_num_rows($result_tables))
    {

        table_open ();
        table_open_record_header ();
        table_add_cell_header (t('table'));
        table_add_cell_header (t('status'));
        table_add_cell_header ("<img src='./images/table.png'>");
        table_add_cell_header (t('fields').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        table_add_cell_header (t('valid').'&nbsp;&nbsp;&nbsp;');
        table_add_cell_header (t('fixable'));
        table_close_record (2);

        while (list($db_table_name)=mysql_fetch_row($result_tables))
        {
            $db_table_prefix = tables_table_prefix ($db_table_name);
            $result_table = mysql_debug_query ("select table_id from tables where table_name='".$db_table_name."'");
            if (mysql_num_rows($result_table))
            {
                list ($db_table_id) = mysql_fetch_row ($result_table);
            }
            else
            {
                $db_table_id = false;
            };
            $result_fields = mysql_debug_query ("show fields from ".$db_table_name);
            $db_fields_count = mysql_num_rows($result_fields)-1;
            $db_fields_valid = 0;
            $db_fields_fixed = 0;
            while (list($db_field_name,$db_field_type,$db_field_null,$db_field_key,$db_field_extra)=mysql_fetch_row($result_fields))
            {
                if ($db_table_prefix.'id'!=$db_field_name && after($db_table_prefix,$db_field_name) && !before($db_table_prefix,$db_field_name))
                {
                    $db_fields_valid ++;
                    $result_fileds_fixed = mysql_debug_query ("select field_id from tables_fields,tables where field_table=table_id and table_name='".$db_table_name."' and field_name='".after($db_table_prefix,$db_field_name)."'");
                    if (mysql_num_rows($result_fileds_fixed))
                    {
                        $db_fields_fixed ++;
                    };
                };

            };
            if ($db_fields_valid==$db_fields_count && $db_fields_fixed==$db_fields_count)
            {
                #fede77
                table_open_record ('bgcolor=fede77');
                table_add_cell ($db_table_name);
                table_add_cell (html_add_percent ($db_fields_valid, $db_fields_count));
                table_add_cell (db_table_icon($db_table_id));
                table_add_cell ($db_fields_count);
                table_add_cell ($db_fields_valid);
                table_add_cell ($db_fields_fixed);
                if ($db_table_id)
                {
                    table_add_cell_delete_ ('delete.png', $redirect_url."?apage=".$apage."&intro_fields_delete=".$db_table_name);
                    table_add_cell ('&nbsp;&nbsp;&nbsp;');
                }
                else
                {
                    table_add_cell ('&nbsp;&nbsp;&nbsp;');
                    table_add_cell ('&nbsp;&nbsp;&nbsp;');
                };
            }
            elseif ($db_fields_valid==$db_fields_count)
            {
                table_open_record ();
                table_add_cell ($db_table_name);
                table_add_cell (html_add_percent ($db_fields_valid, $db_fields_count));
                table_add_cell (db_table_icon($db_table_id));
                table_add_cell ($db_fields_count);
                table_add_cell ($db_fields_valid);
                table_add_cell ($db_fields_fixed);
                if ($db_table_id)
                {
                    table_add_cell_delete_ ('delete.png', $redirect_url."?apage=".$apage."&intro_fields_delete=".$db_table_name);
                    table_add_cell_button_ ('edit.png', $redirect_url."?apage=".$apage."&intro_fields_add=".$db_table_name);
                }
                else
                {
                    table_add_cell ('&nbsp;&nbsp;&nbsp;');
                    table_add_cell ('&nbsp;&nbsp;&nbsp;');
                };
            }
            else
            {
                #fede77
                table_open_record ('bgcolor=#f8d0cb');
                table_add_cell ($db_table_name);
                table_add_cell (html_add_percent ($db_fields_valid, $db_fields_count));
                table_add_cell ('');
                table_add_cell ($db_fields_count);
                table_add_cell ($db_fields_valid);
                table_add_cell ($db_fields_fixed);
                table_add_cell ('&nbsp;&nbsp;&nbsp;');
                table_add_cell ('&nbsp;&nbsp;&nbsp;');
            };
            table_close_record ();
        };
        table_close ();
    };
?>