<?php

    $tables_cache = array ();

    $array_relations_types = array("1"=>t('admin.relations.byfield'),"2"=>t('admin.relations.byrow'));
    $array_tables = mysql_array ('tables','table_name','table_caption');
    unset ($array_tables[0]);
    foreach ($array_tables as $key => $value)
    {
        $array_tables_fields [$key]['id'] = t('admin.relations.id');
        $array_tables_fields [$key] += mysql_array ('tables_fields','field_name',field_name_localized('field_caption','tables_fields'),"where field_table='".mysql_value('tables','table_id','table_name',"'".$key."'")."' order by field_order");
        unset ($array_tables_fields [$key][0]);
        foreach ($array_tables_fields[$key] as $field_name => $field_value)
        {
            if ($tables[$key]['fields'][$field_name]['multilang'])
            {
                $array_tables_fields[$key][before_last('_',$field_name)] = $field_value;
                unset ($array_tables_fields[$key][$field_name]);
            };
        };
    };
    unset ($field_name,$field_value);

    # INCOMES

    import_variable ('relations_search','SESSION','');
    //import_variable ('ppage','SESSION','');

    $table_name = 'relations';

    # DELETE

    if ($relations_delete)
    {
        mysql_query ("delete from tables_relations where relation_id='".intval($relations_delete)."' limit 1");
    };

    # INSERT

    if ($relations_insert['relation_type'] && $relations_insert['relation_slave_table'] && $relations_insert['relation_slave_field_id'] && $relations_insert['relation_slave_field_caption'] && $relations_insert['relation_master_table'] && $relations_insert['relation_master_field_id'] && $relations_insert['relation_master_field_caption'])
    {
        mysql_query ("insert into tables_relations set relation_type='".$relations_insert['relation_type']."',  relation_slave_table='".$relations_insert['relation_slave_table']."', relation_slave_field_id='".$relations_insert['relation_slave_field_id']."', relation_slave_field_caption='".$relations_insert['relation_slave_field_caption']."', relation_slave_field_group='".$relations_insert['relation_slave_field_group']."', relation_master_table='".$relations_insert['relation_master_table']."', relation_master_field_id='".$relations_insert['relation_master_field_id']."', relation_master_field_caption='".$relations_insert['relation_master_field_caption']."', relation_master_field_group='".$relations_insert['relation_master_field_group']."'");
    }
    elseif (is_array($relations_insert))
    {
        $relations_insert_1 = $relations_insert;
        html_add_message ("გთხოვთ მიუთითოთ ყველა საჭირო ველი");
    };

    # UPDATE

    if ($relations_update['relation_id'] && $relations_update['relation_type'] && $relations_update['relation_slave_table'] && $relations_update['relation_slave_field_id'] && $relations_update['relation_slave_field_caption'] && $relations_update['relation_master_table'] && $relations_update['relation_master_field_id'] && $relations_update['relation_master_field_caption'])
    {
        mysql_debug_query ("update tables_relations set relation_type='".$relations_update['relation_type']."',  relation_slave_table='".$relations_update['relation_slave_table']."', relation_slave_field_id='".$relations_update['relation_slave_field_id']."', relation_slave_field_caption='".$relations_update['relation_slave_field_caption']."', relation_master_table='".$relations_update['relation_master_table']."', relation_master_field_id='".$relations_update['relation_master_field_id']."', relation_master_field_caption='".$relations_update['relation_master_field_caption']."' where relation_id='".$relations_update['relation_id']."'");
    }
    elseif (is_array($relations_update))
    {
        $edit = $relations_update['relation_id'];
        html_add_message (t('admin.relations.fields_required'));
    };


    # CRITERIA

    $relations_select .= "1=1";

    # SELECT

    $pager = new pager (mysql_value('tables_relations',"count(relation_id)",'1',"1 and (".$relations_select.")"), 50, $ppage[$table_name]);
    $result = mysql_query ("select * from tables_relations where ".$relations_select." order by relation_slave_table asc limit ".($pager->first()-1).",50");

    # ADD

    if ($relations_insert_1['relation_type'] && $relations_insert_1['relation_slave_table'] && $relations_insert_1['relation_master_table'])
    {
        $relations_insert = $relations_insert_1;
        form_open ('post', '', '', 'relations_insert');
        form_add_hidden ('apage',$apage);
        form_add_label (t('admin.relations.rel_type'), $array_relations_types[$relations_insert['relation_type']]);
        form_add_hidden ('relations_insert[relation_type]', $relations_insert['relation_type']);
        form_add_spacer ();
        form_add_label (t('admin.relations.slave_table'), $array_tables[$relations_insert['relation_slave_table']]);
        form_add_hidden ('relations_insert[relation_slave_table]', $relations_insert['relation_slave_table']);
        form_add_select ('relations_insert[relation_slave_field_id]', t('admin.relations.slave_table_id'), '0', $array_tables_fields[$relations_insert['relation_slave_table']], "", "", "", '...');
        form_add_select ('relations_insert[relation_slave_field_caption]', t('admin.relations.slave_table_caption'), '0', $array_tables_fields[$relations_insert['relation_slave_table']], "", "", "", '...');
        form_add_select ('relations_insert[relation_slave_field_group]', t('admin.relations.slave_table_group'), '0', $array_tables_fields[$relations_insert['relation_slave_table']], "", "", "", '...');
        form_add_spacer ();
        form_add_label (t('admin.relations.master_table'), $array_tables[$relations_insert['relation_master_table']]);
        form_add_hidden ('relations_insert[relation_master_table]', $relations_insert['relation_master_table']);
        form_add_select ('relations_insert[relation_master_field_id]', t('admin.relations.master_table_id'), '0', $array_tables_fields[$relations_insert['relation_master_table']], "", "", "", '...');
        form_add_select ('relations_insert[relation_master_field_caption]', t('admin.relations.master_table_caption'), '0', $array_tables_fields[$relations_insert['relation_master_table']], "", "", "", '...');
        form_add_select ('relations_insert[relation_master_field_group]', t('admin.relations.master_table_group'), '0', $array_tables_fields[$relations_insert['relation_master_table']], "", "", "", '...');
        form_add_spacer ();
        form_add_submit (t('admin.relations.button_add'),t('admin.relations.button_cancel'));
        form_close ();

    }
    else
    {
        form_open ('post', '', '', 'relations_insert_1');
        form_add_hidden ('apage',$apage);
        form_add_select ('relations_insert_1[relation_type]', t('admin.relations.rel_type'), $relations_insert_1[relation_type], $array_relations_types, "", "", "", '...');
        form_add_spacer ();
        form_add_select ('relations_insert_1[relation_slave_table]', t('admin.relations.slave_table'), $relations_insert_1[relation_slave_table], $array_tables, "", "", "", '...');
        form_add_select ('relations_insert_1[relation_master_table]', t('admin.relations.master_table'), $relations_insert_1[relation_master_table], $array_tables, "", "", "", '...');
        form_add_spacer ();
        form_add_submit (t('admin.relations.button_next').' >');
        form_close ();
    };

    # SEARCH

    /*
    form_open ('post', '', '', 'relations_search');
    form_add_hidden ('apage',$apage);
    form_add_edit ('relations_search[relation_login]', 'ძებნა', $relations_search['relation_login'], '262', "keyboard_driver('relations_insert')");
    form_add_submit ('ძებნა','გაუქმება',"?apage=$apage&relations_search=");
    form_close ();
    */

    # TABLE

    html_add_br ();

    if (@mysql_num_rows($result))
    {

        table_open ();

        table_open_record_header ();
        table_add_cell_header ('<b>#</b>');
        table_add_cell_header (t('admin.relations.slave_table_short'));
        table_add_cell_header (t('admin.relations.slave_table_id_short'));
        table_add_cell_header (t('admin.relations.slave_table_caption_short'));
        table_add_cell_header (t('admin.relations.slave_table_group_short'));
        table_add_cell_header (t('admin.relations.master_table_short'));
        table_add_cell_header (t('admin.relations.master_table_id_short'));
        table_add_cell_header (t('admin.relations.master_table_caption_short'));
        table_add_cell_header (t('admin.relations.master_table_group_short'));
        table_add_cell_header (t('admin.relations.rel_type_short'));
        table_close_record (1);

        while ($row = mysql_fetch_assoc($result))
        {
            table_open_record ();
            table_add_cell ($row[relation_id]);
            table_add_cell ($array_tables[$row['relation_slave_table']]);
            table_add_cell ('<b>'.$array_tables_fields[$row['relation_slave_table']][$row['relation_slave_field_id']].'</b>');
            table_add_cell ($array_tables_fields[$row['relation_slave_table']][$row['relation_slave_field_caption']]);
            table_add_cell ($array_tables_fields[$row['relation_slave_table']][$row['relation_slave_field_group']]);
            table_add_cell ($array_tables[$row['relation_master_table']]);
            table_add_cell ('<b>'.$array_tables_fields[$row['relation_master_table']][$row['relation_master_field_id']].'</b>');
            table_add_cell ('<b><font color=black>'.$array_tables_fields[$row['relation_master_table']][$row['relation_master_field_caption']].'</font></b>');
            table_add_cell ($array_tables_fields[$row['relation_master_table']][$row['relation_master_field_group']]);
            table_add_cell ($array_relations_types[$row['relation_type']]);
            table_add_cell_delete_ ('delete.png', $redirect_url."?apage=$apage&relations_delete=".$row[relation_id]);
            //table_add_cell_button_ ('edit.png', $redirect_url."?apage=$apage&relations_edit=".$row[relation_id]);
            table_close_record ();
        };

        table_close ();

        html_add (pager (20,t('admin.relations.pages')));

    };


?>