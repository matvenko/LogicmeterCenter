<?php

    $tables_cache = array ();

    $user_admin = true;

    $table_numerates = array (0=>t('admin.tables.no'),1=>t('admin.tables.id'),2=>t('admin.tables.number'));
    $table_booleans = array (0=>t('admin.tables.no'),1=>t('admin.tables.yes'));
    $table_counts = array (0=>t('admin.tables.no'),1=>t('admin.tables.recordcount'),t('admin.tables.unseen'),t('admin.tables.unread'));
    $table_methods = array ("asc"=>t('admin.tables.asc'),"desc"=>t('admin.tables.desc'));
    $table_readonly = array ("0"=>t('admin.tables.edit'),"1"=>t('admin.tables.view'));

    $field_inputs = array ("edit"=>t('admin.tables.editbox'),"memo"=>t('admin.tables.memo'),"richtext"=>t('admin.tables.richtext'),"select"=>t('admin.tables.selecbox'),"country"=>t('admin.tables.country'),"region"=>t('admin.tables.region'),"city"=>t('admin.tables.city'),"district"=>t('admin.tables.district'),"autocomplete"=>t('admin.tables.autocomplete'),"checkbox"=>t('admin.tables.checkbox'), "calendar"=>t('admin.tables.calendar'), "password"=>t('admin.tables.password'), "file"=>t('admin.tables.file'), "image"=>t('admin.tables.image'), "percent"=>t('admin.tables.percentbox'), "read"=>t('admin.tables.read'), "position"=>t('admin.tables.position'), "birthday"=>t('admin.tables.birthday'));
    $field_types = array ("0"=>"...", "char"=>t('admin.tables.char'),"longtext"=>t('admin.tables.longtext'),"int"=>t('admin.tables.int'),"bigint"=>t('admin.tables.bigint'), "smallint"=>t('admin.tables.smallint'), "date"=>t('admin.tables.date'), "datetime"=>t('admin.tables.datetime'), "float"=>t('admin.tables.float'));
    $field_types_defaults = array ("checkbox"=>"smallint", "country"=>"int", "region"=>"int", "city"=>"int", "district"=>"int", "percent"=>"int", "edit"=>"char","memo"=>"longtext","richtext"=>"longtext","select"=>"int","autocomplete"=>"int","calendar"=>"date", "password"=>"char", "file"=>"char", "image"=>"smallint", "read"=>"smallint", "position"=>"int", "birthday"=>"date"); //date ის ტიპის input ს calendar უნდ ერქვას
    $field_lengths_defaults = array ("char"=>"255","int"=>"10", "smallint"=>"1","bigint"=>"10","float"=>"10,2");
    $field_booleans = array (0=>t('admin.tables.no'),1=>t('admin.tables.yes'));

    $field_on_updates = array (0=>t('admin.tables.do_noting'),1=>t('admin.tables.set_date'),2=>t('admin.tables.set_user'),3=>t('admin.tables.set_guid'));
    $field_on_inserts = array (0=>t('admin.tables.do_noting'),1=>t('admin.tables.set_date'),2=>t('admin.tables.set_user'),3=>t('admin.tables.set_guid'));

    //error_reporting (E_ALL);

    //$mysql_debug = true;

    function field_attributes ($row)
    {
        foreach ($row as $key => $value)
        {
            $key_ = after_last("_",$key);
            if (($key_=='add' || $key_=='search' || $key_=='result' || $key_=='edit' || $key_=='register' || $key_=='report') && $value)
            {
                $result.="<img src='./images/".$key_."_.png'> ";
            }
            elseif ($key_=='add' || $key_=='search' || $key_=='result' || $key_=='edit' || $key_=='register' || $key_=='report')
            {
                $result.="<img width=16 height=16 src='./images/spacer.gif'> ";
            };

        };
        return $result;
    };

    #### TABLE EMPTY ####

    if ($tables_empty)
    {
        $tables_truncate_name = mysql_value ('tables','table_name','table_id',"'".$tables_empty."'");
        if ($tables_truncate_name)
        {
            mysql_debug_query ("TRUNCATE TABLE `".$tables_truncate_name."`");
        };
    };

    #### TABLE DELETE ####

    if (isset($tables_delete))
    {
        $tables_delete_name = mysql_value ('tables','table_name','table_id',"'".$tables_delete."'");
        mysql_debug_query ("delete from tables where table_id='".intval($tables_delete)."'");
        if (!mysql_errno() && $tables_delete_name)
        {
            mysql_debug_query ("delete from tables_fields_sets using tables_fields,tables_fields_sets where field_table='".$tables_delete."' and field_id=set_field");
            mysql_debug_query ("delete from tables_fields where field_table='".$tables_delete."'");
            mysql_debug_query ("DROP TABLE `".$tables_delete_name."`");
        };
    };

    #### TABLE INSERT ####

    if (is_array($tables_insert) && $tables_insert['table_name'] && $tables_insert['table_caption'])
    {
        $tables_insert['table_name'] = geoutf8_to_geolatin(str_replace(" ", "_", $tables_insert['table_name']));
        $table_prefix = tables_table_prefix($tables_insert['table_name']);
        mysql_debug_query ("CREATE TABLE `".$tables_insert['table_name']."` (`".$table_prefix."id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY ( `".$table_prefix."id` ) ) ENGINE = MYISAM ");
        if (!mysql_errno())
        {
            mysql_debug_query ("
            insert into tables set
            table_name='".$tables_insert['table_name']."',
            table_caption='".$tables_insert['table_caption']."',
            table_readonly='".intval($tables_insert['table_readonly'])."',
            table_order_field='".$tables_insert['table_order_field']."',
            table_order_method='".$tables_insert['table_order_method']."',
            table_edit='".$tables_insert['table_edit']."',
            table_delete='".$tables_insert['table_delete']."',
            table_add='".$tables_insert['table_add']."',
            table_register='".$tables_insert['table_register']."',
            table_result='".$tables_insert['table_result']."',
            table_search='".$tables_insert['table_search']."',
            table_search_defaults='".intval($tables_insert['table_search_defaults'])."',
            table_report='".intval($tables_insert['table_report'])."',
            table_found='".intval($tables_insert['table_found'])."',
            table_count='".$tables_insert['table_count']."',
            table_multi_delete='".$tables_insert['table_multi_update']."',
            table_multi_update='".$tables_insert['table_multi_update']."',
            table_rows='".intval($tables_insert['table_rows'])."',
            table_columns_add='".intval($tables_insert['table_columns_add'])."',
            table_columns_register='".intval($tables_insert['table_columns_register'])."',
            table_columns_edit='".intval($tables_insert['table_columns_edit'])."',
            table_columns_search='".intval($tables_insert['table_columns_search'])."',
            table_columns_multi_update='".intval($tables_insert['table_columns_multi_update'])."',
            table_comments='".intval($tables_insert['table_comments'])."',
            table_icon='".$tables_insert['table_icon']."',
            table_parent='".$tables_insert['table_parent']."',
            table_button_add_submit='".$tables_insert['table_button_add_submit']."',
            table_button_register_submit='".$tables_insert['table_button_register_submit']."',
            table_button_register_cancel='".$tables_insert['table_button_register_cancel']."',
            table_button_edit_submit='".$tables_insert['table_button_edit_submit']."',
            table_button_edit_cancel='".$tables_insert['table_button_edit_cancel']."',
            table_button_search_submit='".$tables_insert['table_button_search_submit']."',
            table_button_search_clear='".$tables_insert['table_button_search_clear']."',
            table_button_readonly_back='".$tables_insert['table_button_readonly_back']."',
            table_button_multi_delete='".$tables_insert['table_button_multi_delete']."',
            table_button_multi_update='".$tables_insert['table_button_multi_update']."',
            table_numerate='".$tables_insert['table_numerate']."'
            ");
            if (mysql_errno())
            {
                mysql_debug_query ("DROP TABLE `".$tables_insert['table_name']."`");
            }
            else
            {
                $tables_edit = mysql_insert_id ();
                unset ($tables_insert);
            };
        };
    }
    elseif (is_array($tables_insert))
    {
        html_add_message (t('admin.tables.required_name'));
    };

    #### TABLE UPDATE ####

    if (is_array($tables_update))
    {
        mysql_debug_query ("
        update tables set
        table_caption='".$tables_update['table_caption']."',
        table_readonly='".$tables_update['table_readonly']."',
        table_order_field='".$tables_update['table_order_field']."',
        table_order_method='".$tables_update['table_order_method']."',
        table_edit='".$tables_update['table_edit']."',
        table_delete='".$tables_update['table_delete']."',
        table_result='".$tables_update['table_result']."',
        table_search='".$tables_update['table_search']."',
        table_search_defaults='".$tables_update['table_search_defaults']."',
        table_add='".$tables_update['table_add']."',
        table_register='".$tables_update['table_register']."',
        table_report='".$tables_update['table_report']."',
        table_found='".$tables_update['table_found']."',
        table_count='".$tables_update['table_count']."',
        table_rows='".intval($tables_update['table_rows'])."',
        table_columns_add='".intval($tables_update['table_columns_add'])."',
        table_columns_edit='".intval($tables_update['table_columns_edit'])."',
        table_columns_register='".intval($tables_update['table_columns_register'])."',
        table_columns_search='".intval($tables_update['table_columns_search'])."',
        table_columns_multi_update='".intval($tables_update['table_columns_multi_update'])."',
        table_comments='".$tables_update['table_comments']."',
        table_multi_update='".$tables_update['table_multi_update']."',
        table_multi_delete='".$tables_update['table_multi_delete']."',
        table_icon='".$tables_update['table_icon']."',
        table_parent='".$tables_update['table_parent']."',
        table_button_add_submit='".$tables_update['table_button_add_submit']."',
        table_button_register_submit='".$tables_update['table_button_register_submit']."',
        table_button_register_cancel='".$tables_update['table_button_register_cancel']."',
        table_button_edit_submit='".$tables_update['table_button_edit_submit']."',
        table_button_edit_cancel='".$tables_update['table_button_edit_cancel']."',
        table_button_search_submit='".$tables_update['table_button_search_submit']."',
        table_button_search_clear='".$tables_update['table_button_search_clear']."',
        table_button_readonly_back='".$tables_update['table_button_readonly_back']."',
        table_button_multi_delete='".$tables_update['table_button_multi_delete']."',
        table_button_multi_update='".$tables_update['table_button_multi_update']."',
        table_numerate='".$tables_update['table_numerate']."'
        where table_id='".intval($tables_update['table_id'])."'
        ");
    };

    #### TABLE EDIT ####

    if (intval($tables_edit))
    {

        #### TABLE EDIT FORM ####

        $tables_edit = intval ($tables_edit);
        $row = mysql_fetch_assoc (mysql_debug_query ("select * from tables where table_id='$tables_edit'"));

        if (!$fields_edit)
        {
            html_add ("<table><tr><td valign=top>");
            form_open ('post');
            form_add_hidden ('apage', $apage);
            form_add_hidden ('tables_update[table_id]', $tables_edit);
            form_add_label (t('admin.tables.table_options'));
            form_add_spacer ();
            if ($user_admin)
            {
                form_add_label (t('admin.tables.name'), $row['table_name']);
            };
            form_add_edit ('tables_update[table_caption]', t('admin.tables.caption'), $row['table_caption']);
            form_add_spacer ();
            form_add_select ('tables_update[table_numerate]', t('admin.tables.numeration'), $row['table_numerate'], $table_numerates);
            form_add_checkbox ('tables_update[table_readonly]', t('admin.tables.readonly'), $row['table_readonly']); # CHECKBOX
            form_add_checkbox ('tables_update[table_add]', t('admin.tables.add'), $row['table_add']); # CHECKBOX
            form_add_checkbox ('tables_update[table_register]', t('admin.tables.register'), $row['table_register']); # CHECKBOX
            form_add_checkbox ('tables_update[table_edit]', t('admin.tables.edit'), $row['table_edit']); # CHECKBOX
            form_add_checkbox ('tables_update[table_delete]', t('admin.tables.delete'), $row['table_delete']); # CHECKBOX
            form_add_checkbox ('tables_update[table_result]', t('admin.tables.results'), $row['table_result']); # CHECKBOX
            form_add_checkbox ('tables_update[table_search]', t('admin.tables.search'), $row['table_search']); # CHECKBOX
            form_add_checkbox ('tables_update[table_report]', t('admin.tables.report'), $row[table_report]); # CHECKBOX
            form_add_checkbox ('tables_update[table_found]', t('admin.tables.found'), $row[table_found]); # CHECKBOX
            form_add_select ('tables_update[table_count]', t('admin.tables.count'), $row[table_count], $table_counts);
            form_add_checkbox ('tables_update[table_multi_update]', t('admin.tables.multi_update'), $row[table_multi_update]); # CHECKBOX
            form_add_checkbox ('tables_update[table_multi_delete]', t('admin.tables.multi_delete'), $row[table_multi_delete]); # CHECKBOX
            form_add_checkbox ('tables_update[table_search_defaults]', t('admin.tables.search_defaults'), $row['table_search_defaults']); # CHECKBOX
            form_add_edit ('tables_update[table_rows]', t('admin.tables.max_rows'), $row[table_rows]);
            form_add_edit ('tables_update[table_columns_add]', t('admin.tables.add_columns'), $row[table_columns_add]);
            form_add_edit ('tables_update[table_columns_edit]', t('admin.tables.edit_columns'), $row[table_columns_edit]);
            form_add_edit ('tables_update[table_columns_search]', t('admin.tables.search_columns'), $row[table_columns_search]);
            form_add_edit ('tables_update[table_columns_register]', t('admin.tables.register_columns'), $row[table_columns_register]);
            form_add_edit ('tables_update[table_columns_multi_update]', t('admin.tables.multi_update_columns'), $row[table_columns_multi_update]);
            form_add_checkbox ('tables_update[table_comments]', t('admin.tables.comments'), $row[table_comments]); # CHECKBOX
            form_add_edit ('tables_update[table_icon]', t('admin.tables.icon'), $row['table_icon']);
            form_add_edit ('tables_update[table_parent]', t('admin.tables.parent'), $row['table_parent']);
            form_add_edit ('tables_update[table_button_add_submit]', t('admin.tables.button_add','Add'), $row['table_button_add_submit']);
            form_add_edit ('tables_update[table_button_register_submit]', t('admin.tables.button_register_submit','Register'), $row['table_button_register_submit']);
            form_add_edit ('tables_update[table_button_register_cancel]', t('admin.tables.button_register_cancel','Cancel'), $row['table_button_register_cancel']);
            form_add_edit ('tables_update[table_button_edit_submit]', t('admin.tables.button_edit_submit','Save'), $row['table_button_edit_submit']);
            form_add_edit ('tables_update[table_button_edit_cancel]', t('admin.tables.button_edit_cancel','Cancel'), $row['table_button_edit_cancel']);
            form_add_edit ('tables_update[table_button_search_submit]', t('admin.tables.button_search_submit','Search'), $row['table_button_search_submit']);
            form_add_edit ('tables_update[table_button_search_clear]', t('admin.tables.button_search_clear','Clear'), $row['table_button_search_clear']);
            form_add_edit ('tables_update[table_button_readonly_back]', t('admin.tables.button_readonly_back','Back'), $row['table_button_readonly_back']);
            form_add_edit ('tables_update[table_button_multi_update]', t('admin.tables.button_multi_update','Update'), $row['table_button_multi_update']);
            form_add_edit ('tables_update[table_button_multi_delete]', t('admin.tables.button_multi_delete','Delete'), $row['table_button_multi_delete']);
            //form_add_label (t('admin.tables.sorting'));
            form_add_select ('tables_update[table_order_field]', t('admin.tables.sort_field'), $row['table_order_field'], 'tables_fields','field_name','field_name',"where field_table='".$tables_edit."'");
            form_add_select ('tables_update[table_order_method]', t('admin.tables.sort_method'), $row['table_order_method'], $table_methods);
            form_add_spacer ();
            form_add_submit (t('admin.tables.save'),t('admin.tables.cancel'));
            form_close ();
            html_add ("</td>");
        };

        #### FIELD PREPARING VARIABLES ####

        $table_id = $tables_edit;
        $table_name = $row['table_name'];
        $table_prefix = tables_table_prefix ($table_name);

        #### FIELD UP ####
        if ($fields_up) mysql_move_up ('tables_fields','field_id','field_order',$fields_up,'field_table');

        #### FIELD DOWN ####
        if ($fields_down) mysql_move_down ('tables_fields','field_id','field_order',$fields_down,'field_table');

        #### FIELD DELETE ####

        if ($fields_delete)
        {
            $field_name = mysql_value ('tables_fields','field_name','field_id',"'".$fields_delete."'");
            if ($field_name)
            {
                if ($_SESSION[order][$table_name][field]==$field_name)
                {
                    $_SESSION[order][$table_name][field] = 'id';
                    $_SESSION[order][$table_name][method] = 'asc';
                };
                mysql_debug_query ("delete from tables_fields where field_id='".$fields_delete."'");
                mysql_debug_query ("delete from tables_fields_sets where set_field='".$fields_delete."'");
                mysql_debug_query ("ALTER TABLE `".$table_name."` DROP INDEX `".$table_prefix.$field_name."`");
                mysql_debug_query ("ALTER TABLE `".$table_name."` DROP  `".$table_prefix.$field_name."`");
            };
        };

        #### FIELD INSERT ####

        if (is_array($fields_insert) && $fields_insert[field_name] && $fields_insert[field_name_localized('field_caption','tables_fields')] && $fields_insert[field_input])
        {
            //if (!$fields_insert[field_type] || $fields_insert[field_type]=='longtext' || $fields_insert[field_type]=='date' || $fields_insert[field_type]=='datetime')
            if (!$fields_insert[field_type])
            {
                $fields_insert[field_type] = $field_types_defaults [$fields_insert[field_input]];
            };
            if (!$fields_insert[field_length])
            {
                $fields_insert[field_length] = $field_lengths_defaults [$fields_insert[field_type]];
            };

            $fields_insert[field_max] = intval($fields_insert[field_max]);
            $fields_insert[field_min] = intval($fields_insert[field_min]);
            $fields_insert[field_width] = intval($fields_insert[field_width]);
            $fields_insert[field_height] = intval($fields_insert[field_height]);

            mysql_debug_query ("insert into tables_fields set
            field_table = ".$table_id.",
            field_name = '".$fields_insert[field_name]."',
            ".field_set_localized ('field_caption', $fields_insert,'tables_fields').",
            field_type = '".$fields_insert[field_type]."',
            field_input = '".$fields_insert[field_input]."',
            field_length = '".$fields_insert[field_length]."',
            field_default = '".$fields_insert[field_default]."',
            field_required = '".$fields_insert[field_required]."',
            field_add = '".$fields_insert[field_add]."',
            field_edit = '".$fields_insert[field_edit]."',
            field_search = '".$fields_insert[field_search]."',
            field_search_term = '".$fields_insert[field_search_term]."',
            field_result = '".$fields_insert[field_result]."',
            field_register = '".$fields_insert[field_register]."',
            field_readonly = '".$fields_insert[field_readonly]."',
            field_group = '".$fields_insert[field_group]."',
            field_spacer = '".$fields_insert[field_spacer]."',
            field_nulldate = '".$fields_insert[field_nulldate]."',
            field_report = '".$fields_insert[field_report]."',
            field_unique = '".$fields_insert[field_unique]."',
            field_multilang = '".$fields_insert[field_multilang]."',
            field_max = '".$fields_insert[field_max]."',
            field_min = '".$fields_insert[field_min]."',
            field_width = '".$fields_insert[field_width]."',
            field_height = '".$fields_insert[field_height]."',
            field_comment = '".$fields_insert[field_comment]."',
            field_pattern = '".$fields_insert[field_pattern]."',
            field_multi_update = '".$fields_insert[field_multi_update]."',
            field_on_update = '".$fields_insert[field_on_update]."',
            field_on_insert = '".$fields_insert[field_on_insert]."',
            field_order = '".intval(intval(mysql_value('tables_fields','max(field_order)',"field_table","'".$table_id."'")+1))."'
            ");

            /*field_sets_table = '".$fields_insert[field_sets_table]."',
            field_sets_key = '".$fields_insert[field_sets_key]."',
            field_sets_value = '".$fields_insert[field_sets_value]."',*/
            if (!mysql_errno())
            {
                $fields_insert_id = mysql_insert_id();
                //debug_var ($fields_insert);
                if ($fields_insert[field_length]!='' && $fields_insert[field_type]!='longtext' && $fields_insert[field_type]!='date' && $fields_insert[field_type]!='datetime')
                {
                    $field_length = "(".$fields_insert[field_length].")";
                };
                if ($fields_insert[field_type]=='int' || $fields_insert[field_type]=='bigint' || $fields_insert[field_type]=='smallint' || $fields_insert[field_type]=='float')
                {
                    $field_unsigned = "UNSIGNED";
                    $fields_insert[field_default] = strval(intval($fields_insert[field_default]));
                };
                if ($fields_insert[field_default]!='' && $fields_insert[field_type]!='longtext')
                {
                    $field_default = "DEFAULT '".$fields_insert[field_default]."'";
                };
                mysql_debug_query ("ALTER TABLE `".$table_name."` ADD `".$table_prefix.$fields_insert[field_name]."` ".strtoupper($fields_insert[field_type])." ".$field_length." ".$field_unsigned." NOT NULL ".$field_default);
                if (!mysql_errno())
                {
                    if ($fields_insert[field_unique])
                    {
                        mysql_debug_query ("ALTER TABLE `".$table_name."` ADD UNIQUE ( `".$table_prefix.$fields_insert[field_name]."` )");
                    }
                    elseif ($fields_insert[field_type]=='int' || $fields_insert[field_type]=='bigint' || $fields_insert[field_type]=='smallint' || $fields_insert[field_type]=='float' || $fields_insert[field_type]=='date' || $fields_insert[field_type]=='datetime')
                    {
                        mysql_debug_query ("ALTER TABLE `".$table_name."` ADD INDEX ( `".$table_prefix.$fields_insert[field_name]."` )");
                    };
                }
                else
                {
                    mysql_debug_query ("delete from tables_fields where field_id='".$fields_insert_id."' limit 1");
                };
            };
        }
        elseif (is_array($fields_insert))
        {
            html_add_message (t('admin.tables.fields_required'));
        };

        #### FIELD UPDATE ####

        //debug_var ($fields_update);

        if (is_array($fields_update) && $fields_update[field_id] && $fields_update[field_name] && $fields_update[field_name_localized('field_caption','tables_fields')] &&  $fields_update[field_input])
        {
            $field_name_old = mysql_value ('tables_fields','field_name','field_id',"'".$fields_update[field_id]."'");
            //debug_var ($field_name_old);
            $field_unique_old = mysql_value ('tables_fields','field_unique','field_id',"'".$fields_update[field_id]."'");
            $field_type_old= mysql_value ('tables_fields','field_type','field_id',"'".$fields_update[field_id]."'");
            if (!$fields_update['field_type'])
            {
                $fields_update[field_type] = $field_types_defaults [$fields_update[field_input]];
            };
            if (!$fields_update[field_length])
            {
                $fields_update[field_length] = $field_lengths_defaults [$fields_update[field_type]];
            };
            $fields_update[field_max] = intval($fields_update[field_max]);
            $fields_update[field_min] = intval($fields_update[field_min]);
            $fields_update[field_width] = intval($fields_update[field_width]);
            $fields_update[field_height] = intval($fields_update[field_height]);
            mysql_debug_query ("update tables_fields set
            field_name = '".$fields_update[field_name]."',
            ".field_set_localized ('field_caption', $fields_update,'tables_fields').",
            field_type = '".$fields_update[field_type]."',
            field_input = '".$fields_update[field_input]."',
            field_length = '".$fields_update[field_length]."',
            field_default = '".$fields_update[field_default]."',
            field_required = '".$fields_update[field_required]."',
            field_add = '".$fields_update[field_add]."',
            field_edit = '".$fields_update[field_edit]."',
            field_search = '".$fields_update[field_search]."',
            field_search_term = '".$fields_update[field_search_term]."',
            field_result = '".$fields_update[field_result]."',
            field_register = '".$fields_update[field_register]."',
            field_readonly = '".$fields_update[field_readonly]."',
            field_group = '".$fields_update[field_group]."',
            field_spacer = '".$fields_update[field_spacer]."',
            field_nulldate = '".$fields_update[field_nulldate]."',
            field_report = '".$fields_update[field_report]."',
            field_unique = '".$fields_update[field_unique]."',
            field_multilang = '".$fields_update[field_multilang]."',
            field_max = '".$fields_update[field_max]."',
            field_min = '".$fields_update[field_min]."',
            field_width = '".$fields_update[field_width]."',
            field_height = '".$fields_update[field_height]."',
            field_comment = '".$fields_update[field_comment]."',
            field_pattern = '".$fields_update[field_pattern]."',
            field_multi_update = '".$fields_update[field_multi_update]."',
            field_on_update = '".$fields_update[field_on_update]."',
            field_on_insert = '".$fields_update[field_on_insert]."'
            where field_id = '".$fields_update[field_id]."'
            ");
            /*field_sets_table = '".$fields_update[field_sets_table]."',
            field_sets_value = '".$fields_update[field_sets_value]."',
            field_sets_key = '".$fields_update[field_sets_key]."',*/
            if (!mysql_errno())
            {
                if ($fields_update[field_length]!='' && $fields_update[field_type]!='longtext' && $fields_update[field_type]!='date' && $fields_update[field_type]!='datetime')
                {
                    $field_length = "(".$fields_update[field_length].")";
                };
                if ($fields_update[field_type]=='int' || $fields_update[field_type]=='bigint' || $fields_update[field_type]=='smallint'|| $fields_update[field_type]=='float')
                {
                    //$field_unsigned = "UNSIGNED";
                    $fields_update[field_default] = strval(intval($fields_update[field_default]));
                };
                if ($fields_update[field_default]!='' && $fields_update[field_type]!='longtext')
                {
                    $field_default = "DEFAULT '".$fields_update[field_default]."'";
                };
                if ($fields_update[field_unique] && ($fields_update[field_unique]!=$field_unique_old || $fields_update[field_name]!=$field_name_old))
                {
                    mysql_debug_query ("ALTER TABLE `".$table_name."` DROP INDEX `".$table_prefix.$field_name_old."`");
                    mysql_debug_query ("ALTER TABLE `".$table_name."` ADD UNIQUE ( `".$table_prefix.$fields_update[field_name]."` )");
                }
                elseif (($fields_update[field_type]=='int' || $fields_update[field_type]=='bigint' || $fields_update[field_type]=='smallint' || $fields_update[field_type]=='float' ||$fields_update[field_type]=='date' || $fields_update[field_type]=='datetime') && ($fields_update[field_type]!=$field_type_old || $fields_update[field_name]!=$field_name_old))
                {
                    mysql_debug_query ("ALTER TABLE `".$table_name."` DROP INDEX `".$table_prefix.$field_name_old."`");
                    mysql_debug_query ("ALTER TABLE `".$table_name."` ADD INDEX ( `".$table_prefix.$fields_update[field_name]."` )");
                }
                elseif ((!$fields_update[field_unique] && $fields_update[field_unique]!=$field_unique_old) || ( ($fields_update[field_type]!='int' && $fields_update[field_type]!='bigint' && $fields_update[field_type]!='float' && $fields_update[field_type]!='smallint' &&  $fields_update[field_type]!='date' && $fields_update[field_type]!='datetime') && $fields_update['field_type']!=$field_type_old  ))
                {
                    mysql_debug_query ("ALTER TABLE `".$table_name."` DROP INDEX `".$table_prefix.$field_name_old."`");
                };
                mysql_debug_query ("ALTER TABLE `".$table_name."` CHANGE `".$table_prefix.$field_name_old."` `".$table_prefix.$fields_update[field_name]."` ".strtoupper($fields_update[field_type])." ".$field_length." ".$field_unsigned." NOT NULL ".$field_default);

            };
        }
        elseif (is_array($fields_update))
        {
            html_add_message ("გთხოვთ შეავსოთ ვარსკვლავით მონიშნული ველები");
        };

        #### FIELD EDIT ####

        //$mysql_debug = true;
        if ($fields_edit)
        {

            $row_field = mysql_fetch_assoc(mysql_debug_query ("select * from tables_fields where field_id = '".$fields_edit."'"));

            form_open ('post');
            form_add_hidden ('apage', $apage);
            form_add_hidden ('tables_edit', $table_id);
            form_add_hidden ('fields_update[field_id]', $fields_edit);
            form_add_label (t('admin.tables.edit_field'));
            form_add_spacer ();
            if ($user_admin)
            {
                form_add_edit ('fields_update[field_name]', '<img src=./images/lock.png> '.t('admin.tables.name'), $row_field['field_name']);
            }
            else
            {
                form_add_hidden ('fields_update[field_name]', $row_field['field_name']);
            };
            form_add_edit_localized ('fields_update[field_caption]', '<img src=./images/lock.png> '.t('admin.tables.caption'), $row_field, 'tables_fields');
            //form_add_spacer ();
            form_add_select ('fields_update[field_input]', '<img src=./images/lock.png> '.t('admin.tables.control'), $row_field['field_input'], $field_inputs);
            form_add_spacer ();
            form_add_select ('fields_update[field_type]', t('admin.tables.type'), $row_field['field_type'], $field_types);
            form_add_edit ('fields_update[field_length]', t('admin.tables.length'), $row_field['field_length']);
            form_add_edit ('fields_update[field_default]', t('admin.tables.default'), $row_field['field_default']);
            form_add_checkbox ('fields_update[field_unique]', t('admin.tables.unique'), $row_field['field_unique']); # CHECKBOX
            form_add_checkbox ('fields_update[field_multilang]', t('admin.tables.multilang'), $row_field['field_multilang']); # CHECKBOX
            form_add_spacer ();
            form_add_checkbox ('fields_update[field_add]', t('admin.tables.in_add'), $row_field['field_add']); # CHECKBOX
            form_add_checkbox ('fields_update[field_search]', t('admin.tables.in_search'), $row_field['field_search']); # CHECKBOX
            form_add_checkbox ('fields_update[field_result]', t('admin.tables.in_results'), $row_field['field_result']); # CHECKBOX
            form_add_checkbox ('fields_update[field_edit]', t('admin.tables.in_edit'), $row_field['field_edit']); # CHECKBOX
            form_add_checkbox ('fields_update[field_register]', t('admin.tables.in_register'), $row_field['field_register']); # CHECKBOX
            form_add_checkbox ('fields_update[field_multi_update]', t('admin.tables.in_multi_update'), $row_field['field_multi_update']); # CHECKBOX
            form_add_checkbox ('fields_update[field_report]', t('admin.tables.in_report'), $row_field['field_report']); # CHECKBOX
            form_add_spacer ();
            form_add_checkbox ('fields_update[field_required]', t('admin.tables.required'), $row_field['field_required']); # CHECKBOX
            form_add_checkbox ('fields_update[field_readonly]', t('admin.tables.readonly'), $row_field['field_readonly']); # CHECKBOX
            form_add_checkbox ('fields_update[field_group]', t('admin.tables.group'), $row_field['field_group']); # CHECKBOX
            form_add_checkbox ('fields_update[field_spacer]', t('admin.tables.spacer'), $row_field['field_spacer']); # CHECKBOX
            form_add_checkbox ('fields_update[field_nulldate]', t('admin.tables.nulldate'), $row_field['field_nulldate']); # CHECKBOX
            form_add_spacer ();
            form_add_edit ('fields_update[field_min]', t('admin.tables.min'), $row_field['field_min']);
            form_add_edit ('fields_update[field_max]', t('admin.tables.max'), $row_field['field_max']);
            form_add_edit ('fields_update[field_width]', t('admin.tables.width'), $row_field['field_width']);
            form_add_edit ('fields_update[field_height]', t('admin.tables.height'), $row_field['field_height']);
            form_add_edit ('fields_update[field_pattern]', t('admin.tables.pattern'), $row_field['field_pattern']);
            form_add_spacer ();
            form_add_edit ('fields_update[field_comment]', t('admin.tables.comment'), $row_field['field_comment']);
            form_add_spacer ();
            /*form_add_label ('ამოსარჩევისთვის');
            form_add_edit ('fields_update[field_sets_table]', 'ცხრილი', $row_field['field_sets_table']);
            form_add_edit ('fields_update[field_sets_key]', 'იდ ველი', $row_field['field_sets_key']);
            form_add_edit ('fields_update[field_sets_value]', 'ტექსტის ველი', $row_field['field_sets_value']);
            form_add_spacer ();
            form_add_label ('თარიღისთვის');*/
            form_add_select ('fields_update[field_on_update]', t('admin.tables.on_update'), $row_field['field_on_update'], $field_on_updates);
            form_add_select ('fields_update[field_on_insert]', t('admin.tables.on_insert'), $row_field['field_on_insert'], $field_on_inserts);
            form_add_edit ('fields_update[field_search_term]', t('admin.tables.search_term'), $row_field['field_search_term']);
            form_add_spacer ();
            form_add_submit (t('admin.tables.save'),t('admin.tables.cancel'),$redirect_url.'?tables_edit='.$table_id);
            form_close ();

            if ($sets_delete)
            {
                mysql_debug_query ("delete from tables_fields_sets where set_id='".$sets_delete."'");
            };

            if (isset($sets_insert[set_key]) && isset($sets_insert[set_value]))
            {
                mysql_debug_query ("insert into tables_fields_sets set set_field='".$fields_edit."', set_key='".$sets_insert[set_key]."', ".field_set_localized_default('set_value',$sets_insert['set_value'],'tables_fields_sets'));
            };

            if ($row_field['field_input']=='select')
            {
                form_open ('post');
                form_add_hidden ('apage', $apage);
                form_add_hidden ('tables_edit', $table_id);
                form_add_hidden ('fields_edit', $fields_edit);
                form_add_label (t('admin.tables.options'));
                form_add_spacer ();
                form_add_edit ('sets_insert[set_key]', t('admin.tables.name'), intval(intval(mysql_value('tables_fields_sets','max(set_key)',"set_field","'".$fields_edit."'")+1)));
                form_add_edit ('sets_insert[set_value]', t('admin.tables.caption'), '');
                form_add_spacer ();
                form_add_submit (t('admin.tables.add'));
                form_close ();

                $result_sets = mysql_debug_query ("select * from tables_fields_sets where set_field='$fields_edit'");

                if (mysql_num_rows($result_sets))
                {
                    table_open ();
                    table_open_record_header ();
                    table_add_cell_header (t('admin.tables.name'));
                    table_add_cell_header (t('admin.tables.caption'));
                    table_close_record (1);

                    while ($row_set = mysql_fetch_assoc($result_sets))
                    {
                        table_open_record ();
                        table_add_cell ($row_set[set_key]);
                        table_add_cell ($row_set[field_name_localized('set_value','tables_fields_sets')]);
                        table_add_cell_delete_ ('delete.png', "?apage=$apage&tables_edit=$table_id&fields_edit=$fields_edit&sets_delete=$row_set[set_id]");
                        table_close_record ();
                    };
                    table_close ();
                };

            };

        }
        else
        {

            #### FIELD INSERT FORM ####

            html_add ("<td valign=top>");

            form_open ('post');
            form_add_hidden ('apage', $apage);
            form_add_hidden ('tables_edit', $table_id);
            form_add_label (t('admin.tables.add_field'));
            form_add_spacer ();
            if ($user_admin)
            {
                form_add_edit ('fields_insert[field_name]', '<img src=./images/lock.png> '.t('admin.tables.name'), intval(intval(mysql_value('tables_fields','max(field_name)',"field_table","'".$table_id."'"))+1));
            }
            else
            {
                form_add_hidden ('fields_insert[field_name]', intval(intval(mysql_value('tables_fields','max(field_name)',"field_table","'".$table_id."'"))+1));
            };
            form_add_edit_localized ('fields_insert[field_caption]', '<img src=./images/lock.png> '.t('admin.tables.caption'), '', 'tables_fields');
            //form_add_spacer ();
            form_add_select ('fields_insert[field_input]', '<img src=./images/lock.png> '.t('admin.tables.control'), $fields_insert[field_input], $field_inputs);
            form_add_spacer ();
            form_add_select ('fields_insert[field_type]', t('admin.tables.type'), '', $field_types);
            form_add_edit ('fields_insert[field_length]', t('admin.tables.length'), '');
            form_add_edit ('fields_insert[field_default]', t('admin.tables.default'), '');
            form_add_checkbox ('fields_insert[field_unique]', t('admin.tables.unique'), ''); # CHECKBOX
            form_add_checkbox ('fields_insert[field_multilang]', t('admin.tables.multilang'), ''); # CHECKBOX
            form_add_spacer ();
            form_add_checkbox ('fields_insert[field_add]', t('admin.tables.in_add'), '1'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_search]', t('admin.tables.in_search'), '1'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_result]', t('admin.tables.in_results'), '1'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_edit]', t('admin.tables.in_edit'), '1'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_multi_update]', t('admin.tables.in_multi_update'), '0'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_register]', t('admin.tables.in_register'), '1'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_report]', t('admin.tables.in_report'), '1'); # CHECKBOX
            form_add_spacer ();
            form_add_checkbox ('fields_insert[field_required]', t('admin.tables.required'), '0'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_readonly]', t('admin.tables.readonly'), '0'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_group]', t('admin.tables.group'), '0'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_spacer]', t('admin.tables.spacer'), '0'); # CHECKBOX
            form_add_checkbox ('fields_insert[field_nulldate]', t('admin.tables.nulldate'), '0'); # CHECKBOX
            form_add_spacer ();
            form_add_edit ('fields_insert[field_min]', t('admin.tables.min'), '0');
            form_add_edit ('fields_insert[field_max]', t('admin.tables.max'), '0');
            form_add_edit ('fields_insert[field_width]', t('admin.tables.width'), '0');
            form_add_edit ('fields_insert[field_height]', t('admin.tables.height'), '0');
            form_add_edit ('fields_insert[field_pattern]', t('admin.tables.pattern'), '');
            form_add_spacer ();
            /*form_add_label ('ამოსარჩევისთვის');
            form_add_edit ('fields_insert[field_sets_table]', 'ცხრილი', '');
            form_add_edit ('fields_insert[field_sets_key]', 'იდ', '');
            form_add_edit ('fields_insert[field_sets_value]', 'ტექსტი', '');
            form_add_spacer ();
            form_add_label ('თარიღისთვის');*/
            form_add_select ('fields_insert[field_on_update]', t('admin.tables.on_update'), '', $field_on_updates);
            form_add_select ('fields_insert[field_on_insert]', t('admin.tables.on_insert'), '', $field_on_inserts);
            form_add_edit ('fields_insert[field_search_term]', t('admin.tables.search_term'), '');
            form_add_spacer ();
            form_add_submit (t('admin.tables.add'));
            form_close ();

            html_add ("</td></tr></table>");

            #### FIELD LIST ####

            $result_fields = mysql_debug_query ("select * from tables_fields where field_table='$table_id' order by field_order");

            if (mysql_num_rows($result_fields))
            {
                table_open ();
                table_open_record_header ();
                table_add_cell_header ('#');
                table_add_cell_header (t('admin.tables.attributes'));
                if ($user_admin)
                {
                    table_add_cell_header (t('admin.tables.name'));
                };
                table_add_cell_header (t('admin.tables.caption'));
                table_add_cell_header (t('admin.tables.control'));
                table_add_cell_header (t('admin.tables.type'));
                table_add_cell_header (t('admin.tables.length'));
                table_add_cell_header (t('admin.tables.default'));
                table_close_record (4);

                while ($row_field = mysql_fetch_assoc($result_fields))
                {
                    table_open_record ();
                    table_add_cell_id ();
                    table_add_cell (field_attributes($row_field));
                    if ($user_admin)
                    {
                        table_add_cell ($row_field[field_name]);
                    };
                    table_add_cell ($row_field[field_name_localized('field_caption','tables_fields')]);
                    table_add_cell ($field_inputs[$row_field[field_input]]);
                    table_add_cell ($field_types[$row_field[field_type]]);
                    table_add_cell ($row_field[field_length]);
                    table_add_cell ($row_field[field_default]);
                    table_add_cell_button_ ('edit.png', "?apage=$apage&tables_edit=$table_id&fields_edit=$row_field[field_id]");
                    table_add_cell_delete_ ('delete.png', "?apage=$apage&tables_edit=$table_id&fields_delete=$row_field[field_id]");
                    table_add_cell_button_ ('up.png', "?apage=$apage&tables_edit=$table_id&fields_up=$row_field[field_id]");
                    table_add_cell_button_ ('down.png', "?apage=$apage&tables_edit=$table_id&fields_down=$row_field[field_id]");
                    table_close_record ();
                };
                table_close ();
            };
        };

    }
    else
    {

        #### TABLE ADD FORM ####

        form_open ('post');
        form_add_hidden ('apage', $apage);
        form_add_label (t('admin.tables.add_table'));
        form_add_spacer ();
        if ($user_admin)
        {
            form_add_edit ('tables_insert[table_name]', t('admin.tables.name'), "tables_".intval(intval(mysql_value('tables','max(table_id)'))+1));
        }
        else
        {
            form_add_hidden ('tables_insert[table_name]', "tables_".intval(intval(mysql_value('tables','max(table_id)'))+1));
        };
        form_add_edit ('tables_insert[table_caption]', t('admin.tables.caption'), '');
        form_add_spacer ();
        form_add_select ('tables_insert[table_numerate]', t('admin.tables.numeration'), '0', $table_numerates);
        form_add_checkbox ('tables_insert[table_readonly]', t('admin.tables.readonly'), '0'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_add]', t('admin.tables.add'), '1'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_register]', t('admin.tables.register'), '1'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_edit]', t('admin.tables.edit'), '1'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_delete]', t('admin.tables.delete'), '1'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_result]', t('admin.tables.results'), '1'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_search]', t('admin.tables.search'), '1'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_report]', t('admin.tables.report'), '0'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_found]', t('admin.tables.found'), '0'); # CHECKBOX
        form_add_select ('tables_insert[table_count]', t('admin.tables.count'), '1', $table_counts);
        form_add_checkbox ('tables_insert[table_search_defaults]', t('admin.tables.search_defaults'), '0'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_multi_update]', t('admin.tables.multi_update'), '1'); # CHECKBOX
        form_add_checkbox ('tables_insert[table_multi_delete]', t('admin.tables.multi_delete'), '1'); # CHECKBOX
        form_add_edit ('tables_insert[table_rows]', t('admin.tables.max_rows'), '50');
        form_add_edit ('tables_insert[table_columns_add]', t('admin.tables.add_columns'), '0');
        form_add_edit ('tables_insert[table_columns_edit]', t('admin.tables.edit_columns'), '0');
        form_add_edit ('tables_insert[table_columns_search]', t('admin.tables.search_columns'), '0');
        form_add_edit ('tables_insert[table_columns_register]', t('admin.tables.register_columns'), '0');
        form_add_edit ('tables_insert[table_columns_multi_update]', t('admin.tables.multi_update_columns'), '0');
        form_add_checkbox ('tables_insert[table_comments]', t('admin.tables.comments'), '0'); # CHECKBOX
        form_add_edit ('tables_insert[table_icon]', t('admin.tables.icon'), '');
        form_add_edit ('tables_insert[table_parent]', t('admin.tables.parent'), 'tables');
        form_add_edit ('tables_insert[table_button_add_submit]', t('admin.tables.button_add','Add'), '');
        form_add_edit ('tables_insert[table_button_register_submit]', t('admin.tables.button_register_submit','Register'), '');
        form_add_edit ('tables_insert[table_button_register_cancel]', t('admin.tables.button_register_cancel','Cancel'), '');
        form_add_edit ('tables_insert[table_button_edit_submit]', t('admin.tables.button_edit_submit','Save'), '');
        form_add_edit ('tables_insert[table_button_edit_cancel]', t('admin.tables.button_edit_cancel','Cancel'), '');
        form_add_edit ('tables_insert[table_button_search_submit]', t('admin.tables.button_search_submit','Search'), '');
        form_add_edit ('tables_insert[table_button_search_clear]', t('admin.tables.button_search_clear','Clear'), '');
        form_add_edit ('tables_insert[table_button_readonly_back]', t('admin.tables.button_readonly_back','Back'), '');
        form_add_edit ('tables_insert[table_button_multi_update]', t('admin.tables.button_multi_update','Update'), '');
        form_add_edit ('tables_insert[table_button_multi_delete]', t('admin.tables.button_multi_delete','Delete'), '');
        form_add_spacer ();
        form_add_submit (t('admin.tables.add'));
        form_close ();

        #### TABLE LIST ####

        $result = mysql_debug_query ("select * from tables order by table_name");

        if (mysql_num_rows($result))
        {
            table_open ();
            table_open_record_header ();
            table_add_cell_header ('#');
            table_add_cell_header (t('admin.tables.id'));
            table_add_cell_header ("<img src='".$project_url.$image_dir_main."/table.png'>");
            table_add_cell_header (t('admin.tables.caption'));
            table_add_cell_header (t('admin.tables.name'));
            table_add_cell_header (t('admin.tables.fields'));
            table_add_cell_header (t('admin.tables.rows'));
            table_close_record (3);

            while ($row = mysql_fetch_assoc($result))
            {
                table_open_record ();
                table_add_cell_id ();
                table_add_cell ($row[table_id]);
                if ($row['table_icon'])
                {
                    table_add_cell ("<img src='".$project_url.$image_dir_main."/".$row['table_icon']."'>");
                }
                else
                {
                    table_add_cell ("<img src='".$project_url.$image_dir_main."/table.png'>");
                };
                table_add_cell ($row['table_caption']);
                table_add_cell ($row['table_name']);
                table_add_cell (mysql_value('tables_fields','count(*)','field_table',"'".$row[table_id]."'"));
                table_add_cell (mysql_value("`".$row[table_name]."`",'count(*)'));
                table_add_cell_delete_ ('delete.png', "?apage=$apage&tables_delete=$row[table_id]");
                table_add_cell_delete_ ('empty.png', "?apage=$apage&tables_empty=$row[table_id]", "width=1", "EMPTY TABLE ?");
                table_add_cell_button_ ('edit.png', "?apage=$apage&tables_edit=$row[table_id]");
                table_close_record ();
            };
            table_close ();
        };
    };

?>
