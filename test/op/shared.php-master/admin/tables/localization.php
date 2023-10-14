<?php

    //tables_languages_load ();

    //debug_var ($locales_insert);

    if ($languages_insert['language_name'] && $languages_insert['language_caption'])
    {
        tables_languages_add ($languages_insert['language_name'], $languages_insert['language_caption']);
    };

    if ($languages_delete)
    {
        tables_languages_delete ($languages_delete);
    };

    if ($languages_update && $language_name_old = mysql_value('languages','language_name','language_id',intval($languages_update['language_id'])))
    {
        if ($language_name_old!=$languages_update['language_name'])
        {
            tables_languages_rename ($language_name_old, $languages_update['language_name']);
        };
        mysql_debug_query ("update languages set language_caption='".$languages_update['language_caption']."' where language_id='".$languages_update['language_id']."'");
    };

    if ($languages)
    {
        form_open ();
        form_add_label ('Languages defined:');
        form_close ();
    }
    else
    {
        html_add_message ('Languages not defined');
    };

    if (!$languages_edit)
    {
        form_open ();
        form_add_hidden ('apage', $apage);
        form_add_edit ('languages_insert[language_name]', 'Name');
        form_add_edit ('languages_insert[language_caption]', 'Caption');
        form_add_submit ('Add');
        form_close ();
    };

    if ($languages)
    {
        if (intval($languages_edit))
        {
            $languages_edit = intval($languages_edit);
            $row = mysql_fetch_assoc (mysql_debug_query("select * from languages where language_id='".$languages_edit."'"));
            form_open ();
            form_add_hidden ('apage',$apage);
            form_add_hidden ('languages_update[language_id]',$languages_edit);
            form_add_edit ('languages_update[language_name]','Name',$row['language_name']);
            form_add_edit ('languages_update[language_caption]','Caption',$row['language_caption']);
            form_add_submit ('Save','Cancel');
            form_close ();
        }
        else
        {
            $result = mysql_debug_query ("select * from languages");
            if (mysql_num_fields($result))
            {
                table_open ();
                table_open_record_header ();
                table_add_cell_header ('Name');
                table_add_cell_header ('Caption');
                table_close_record(2);
                while ($row=mysql_fetch_assoc($result))
                {
                    if ($language_default==$row['language_name'])
                    {
                        table_open_record ('bgcolor=fede77');
                    }
                    else
                    {
                        table_open_record ();
                    };
                    table_add_cell ($row['language_name']);
                    table_add_cell ($row['language_caption']);
                    table_add_cell_delete_ ('delete.png',$redirect_url."?apage=".$apage."&languages_delete=".$row['language_name']);
                    table_add_cell_button_ ('edit.png',$redirect_url."?apage=".$apage."&languages_edit=".$row['language_id']);
                    table_close_record ();
                };
                table_close ();
            };
        };
    };

    //unset ($language_default);


    if ($languages && $language_default && isset($languages[$language_default]))
    {

        if (intval($locales_delete))
        {
            tables_locales_field_remove (intval($locales_delete));
        };

        if ($locales_insert['locale_table'] && $locales_insert['locale_field'])
        {
            tables_locales_field_add ($locales_insert['locale_table'], $locales_insert['locale_field']);
            //debug_echo ('localizing...');
        };

        form_open ();
        form_add_label ('Multilangual fields:');
        form_close ();

        form_open ();
        form_add_hidden ('apage', $apage);
        form_add_select ("locales_insert[locale_table]", 'Table', '0', 'tables', "table_name", "table_caption", "", '...', '0','0',"ajax_query('./actions/ajax.php?locales_table='+selected_index(this),'div_field')");
        form_add_custom (
        "
        <tr>
        <td><b>Field</b> &nbsp; </td>
        <td>
        <div id='div_field'>
        <select name='locales_insert[locale_field]' style='width:261' class='select' style='height:18'>
            <option value='0'> ... </option>
        </select>
        </div>
        </td>
        </tr>
        ");
        form_add_submit ('Add');
        form_close ();


        $result = mysql_debug_query ("select * from tables_fields_locales");
        if (mysql_num_rows($result))
        {
            table_open ();
            table_open_record_header ();
            table_add_cell_header ('Table');
            table_add_cell_header ('Field');
            table_close_record (2);

            while ($row = mysql_fetch_assoc ($result))
            {
                table_open_record ();
                table_add_cell ($row['locale_table']);
                table_add_cell ($row['locale_field']);
                table_add_cell_delete_ ('delete.png',$redirect_url."?apage=".$apage."&locales_delete=".$row['locale_id']);
                table_close_record ();
            };
            table_close ();
        };
    }
    elseif (!$languages)
    {
        html_add_message ("Before localizzeing fields<br>please define languages in system");
    }
    elseif (!$language_default)
    {
        html_add_message ("Before localizzeing fields<br>please define default language");
    }
    else
    {
        html_add_message ("Before localizzeing fields please select as default<br>language already existing language");
    };


?>