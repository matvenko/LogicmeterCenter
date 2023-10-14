<?php

    $tables_cache = array ();

    function xml_start_element ($parser, $name, $attrs)
    {
        global $table,$row,$cell,$value,$row_full,$values;
        if ($table && $row && strtolower($name)=='data') $value = true;
        if ($table && $row && strtolower($name)=='cell') $cell=true;
        if ($table && strtolower($name)=='row') $row=true;
        if (strtolower($name)=='table') $table=true;
    };

    function xml_between_element ($parser, $data)
    {
        global $table,$row,$cell,$value,$row_full,$values;
        if ($table && $row && $value)
        {
            $values [] = restrict_value($data);
            $row_full = true;
        };
    };

    function xml_end_element ($parser, $name)
    {
        global $row_id,$table,$row,$cell,$value,$row_full,$values,$table_name,$fields,$table_prefix,$import_row,$row_insert,$row_error;
        //debug_var ($name);
        if ($table && $row && $cell && strtolower($name)=='data') $value = false;
        if ($table && $row && strtolower($name)=='cell')
        {
            $cell = false;
            if (!$row_full) $values [] = '';
            else $row_full = false;
        };
        if ($table && strtolower($name)=='row')
        {
            //debug_var ($values);
            $row_id++;
            if ($row_id>=$import_row)
            {
                $cell_id = 0;
                $row_empty = true;
                foreach ($fields as $field_name => $field)
                {
                    if ($values[$cell_id]!='') $row_empty = false;
                    if ($field['default'] && !$values[$cell_id]) $values[$cell_id] = $field['default'];
                    if (($field['type']=='date' || $field['type']=='datetime') && $values[$cell_id]!='0000-00-00') $values[$cell_id] = date ('Y-m-d', strtotime($values[$cell_id]));
                    if ($field['type']=='int' || $field['type']=='bigint' || $field['type']=='smallint') $values[$cell_id] = intval ($values[$cell_id]);
                    $insert_query .= $table_prefix.$field_name."='".$values[$cell_id]."', ";
                    $cell_id++;
                };
                //debug_echo ($insert_query);
                $insert_query = before_last (", ", $insert_query);
                if (!$row_empty)
                {
                    mysql_debug_query ("insert into $table_name set $insert_query", true);
                    if (mysql_error())
                    {
                        //debug_echo(mysql_error());
                        $row_error++;
                    }
                    else
                    {
                        $row_insert++;
                    };
                };
                $insert_query = '';
            };
            $values = array();
            $row=false;
        };
        if (strtolower($name)=='table') $table=false;
        //if ($row_id==10) exit;
    };

    if ($import_table && is_array($import_defaults) && $_FILES['import_file'])
    {
        tables_load_settings ($import_table);
        foreach ($fields as $field_name => $field)
        {
            if ($field['input']=='calendar')
            {
                if ($_POST["import_defaults_".$table_prefix.$field_name]) $import_defaults[$table_prefix.$field_name] = $_POST["import_defaults_".$table_prefix.$field_name];
            };
        };
        foreach ($import_defaults as $import_field => $import_value)
        {
            $fields [after($table_prefix,$import_field)]['default'] = $import_value;
        };
        //debug_var ($fields);
        if (file_exists($_FILES['import_file']['tmp_name']))
        {
            //debug_var ($_FILES);
            $xml_reader = new xml_reader ($_FILES['import_file']['tmp_name'], 'xml_start_element', 'xml_end_element', 'xml_between_element');
            $xml_reader -> read ();
        };
        form_open ();
        form_add_label ("<img src='./images/warn.gif'>", $_FILES['import_file']['name']);
        form_add_label ("&nbsp;", intval($row_insert)." ჩანაწერი წარმატებით დაიმპორტდა");
        form_add_label ("&nbsp;", intval($row_error)." ჩანაწერის დაიმპორტების დროს დაფიქსირდა შეცდომა");
        form_add_hidden ('apage', $import_table);
        form_add_spacer ();
        form_add_submit ('ცხრილის დათვალიერება');
        form_close ();
    }
    elseif ($import_table)
    {
        tables_load_settings ($import_table);
        form_open ('post', '', 'multipart/form-data', 'form_import');
        form_add_hidden ('apage',$apage);
        form_add_hidden ('import_table', $import_table);
        form_add_label ('მონაცემების იმპორტი Excel XML ტიპის ფაილიდან');
        form_add_spacer ();
        form_add_label ('ცხრილი', $tables[$import_table]);
        form_add_spacer ();
        form_add_file ('import_file', 'XML ფაილი');
        form_add_edit ('import_row', 'საწყისი სტრიქონი', '2');
        form_add_spacer ();
        form_add_label ('საწყისი მნიშვნელობები');
        form_add_spacer ();
        foreach ($fields as $field_name => $field)
        {
            if ($field['input']=='edit') form_add_edit ("import_defaults[".$table_prefix.$field_name."]", $field['caption'], $field['default']);
            elseif  ($field['input']=='calendar') form_add_calendar ("import_defaults_".$table_prefix.$field_name, $field['caption'], $field['default'], 'form_import', 'yyyy-MM-dd', '', '228');
            elseif ($field['input']=='select') form_add_select ("import_defaults[".$table_prefix.$field_name."]", $field['caption'], $field['default'], $field['sets'], "", "", "", '...');
            elseif ($field['input']=='memo') form_add_memo ("import_defaults[".$table_prefix.$field_name."]", $field['caption'], $field['default'], '48', '6');
        };
        form_add_spacer ();
        form_add_submit ('შემდეგი');
        form_close ();
    }
    else
    {
        $tables_names = mysql_array ('tables','table_id','table_caption');
        form_open ('post', '', 'multipart/form-data', 'form_import');
        form_add_hidden ('apage',$apage);
        form_add_label ('მონაცემების იმპორტი Excel XML ტიპის ფაილიდან');
        form_add_spacer ();
        form_add_select ('import_table', 'ცხრილი', '', $tables_names);
        form_add_spacer ();
        form_add_submit ('შემდეგი');
        form_close ();
    };

    # CHANGELOG
    # 1:05 PM 3/30/2009 EMPTY DATE | LINE 45
?>