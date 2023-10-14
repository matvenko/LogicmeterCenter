<?php

    $table_name = 'modules_texts';

    if ($texts_insert)
    {
        if (is_array($texts_insert))
        {
            foreach ($texts_insert as $key => $value)
            {
                if (($languages && between_last('_','_',$key)=='text') || (!$languages && after_last('_',$key)=='text'))
                {
                    $texts_insert [$key] = str_replace(array("[","]"),array("{","}"),$value);
                };
            };
        };
        $result = mysql_debug_query ("select text_id from modules_texts where text_page='".$epage."'");
        if (mysql_num_rows($result))
        {
            mysql_debug_query ("update modules_texts set ".field_set_localized('text_text',$texts_insert,'modules_texts').", ".field_set_localized('text_desc',$texts_insert,'modules_texts')." where text_page='".$epage."' limit 1");
        }
        else
        {
            mysql_debug_query ("insert into modules_texts set ".field_set_localized('text_text',$texts_insert,'modules_texts').", ".field_set_localized('text_desc',$texts_insert,'modules_texts').", text_page='".$epage."'");
        };
    };

    $row = mysql_fetch_assoc (mysql_debug_query ("select * from modules_texts where text_page='".$epage."'"));

    if (is_array($row))
    {
        foreach ($row as $key => $value)
        {
            if (($languages && between_last('_','_',$key)=='text') || (!$languages && after_last('_',$key)=='text'))
            {
                $row [$key] = str_replace(array("{","}"),array("[","]"),$value);
            };
        };
    };

    $field_name_desc = 'text_desc';
    $field_name_text = 'text_text';
    $field_container = 'texts_insert';
    $field_value = $row;
    $field_table = 'modules_texts';

    //window_open ();
    tabs_open ();
    if ($languages && $tables_locales[$field_table][after('_',$field_name_text)])
    {
        foreach ($languages as $language_name => $language_caption)
        {
            tabs_open_panel_module ("<img src='./images/".$language_name.".png'>");
            form_add_label (t('admin.desc'));
            form_add_richtext ($field_container.'['.$field_name_desc.'_'.$language_name.']', '', $field_value[$field_name_desc.'_'.$language_name]);
            form_add_label (t('admin.text'));
            form_add_richtext ($field_container.'['.$field_name_text.'_'.$language_name.']', '', $field_value[$field_name_text.'_'.$language_name]);
            tabs_close_panel ();
        };
    }
    else
    {
        tabs_open_panel_module (t('admin.content'));
        form_add_richtext ($field_container.'['.$field_name_desc.']', $field_caption, $field_value[$field_name_desc]);
        form_add_richtext ($field_container.'['.$field_name_text.']', $field_caption, $field_value[$field_name_text]);
        tabs_close_panel ();
    };
    tabs_close ();
    //window_close ();

?>