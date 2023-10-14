<?php

    if (user_group()==1)
    {
        $user_programmer = true;
    }
    else
    {
        $user_programmer = false;
    };

    function setting_caption ($setting_group)
    {
        return ucwords(str_replace ('_',' ',$setting_group));
    };

    if ($settings_insert['setting_key'] && $settings_insert['setting_value'] && $user_programmer)
    {
        mysql_debug_query ("insert into settings set setting_key='".$settings_insert['setting_key']."', setting_value='".$settings_insert['setting_value']."', setting_group='".$settings_insert['setting_group']."', setting_key_type='".$settings_insert['setting_key_type']."', setting_value_type='".$settings_insert['setting_value_type']."'");
    };

    if ($settings_delete && $user_programmer)
    {
        mysql_debug_query ("delete from settings where setting_id='".intval($settings_delete)."'");
    };

    if ($settings_update)
    {
        mysql_debug_query ("update settings set setting_value='".$settings_update['setting_value']."' where setting_id='".$settings_update['setting_id']."'");
    };

    if ($settings_edit)
    {
        $settings_edit = intval ($settings_edit);
        $row = mysql_fetch_assoc (mysql_debug_query ("select * from settings where setting_id='".$settings_edit."'"));

        if ($row['setting_key_type'])
        {
            $settings_key_type = mysql_fetch_assoc (mysql_debug_query ("select * from settings_types where type_id='".$row['setting_value_type']."'"));
            list ($setting_caption) = mysql_fetch_row(mysql_debug_query("select ".$settings_key_type['type_options_caption']." from ".$settings_key_type['type_options_table']." where ".$settings_key_type['type_options_id']."='".$row['setting_value']."'"));
        }
        else
        {
            $setting_caption = setting_caption($row['setting_key']);
        };

        if ($row['setting_value_type'])
        {
            $settings_value_type = mysql_fetch_assoc (mysql_debug_query ("select * from settings_types where type_id='".$row['setting_value_type']."'"));
        };

        form_open ();
        form_add_hidden ('apage', $apage);
        form_add_hidden ('settings_update[setting_id]', $row['setting_id']);
        if ($row['setting_value_type'])
        {
            form_add_select ('settings_update[setting_value]', $setting_caption, $row['setting_value'], $settings_value_type['type_options_table'], $settings_value_type['type_options_id'], $settings_value_type['type_options_caption'], str_replace("|","'",$settings_value_type['type_options_where']));
        }
        else
        {
            form_add_edit ('settings_update[setting_value]', $setting_caption, $row['setting_value']);
        };
        form_add_submit (t('save'),t('cancel'));
        form_close (2);


    }
    else
    {
        if ($user_programmer)
        {
            if (isset($settings_insert_1))
            {

                if ($settings_insert_1['setting_value_type']) $settings_value_type = mysql_fetch_assoc (mysql_debug_query ("select * from settings_types where type_id='".$settings_insert_1['setting_value_type']."'"));
                if ($settings_insert_1['setting_key_type']) $settings_key_type = mysql_fetch_assoc (mysql_debug_query ("select * from settings_types where type_id='".$settings_insert_1['setting_key_type']."'"));

                tabs_open ();
                tabs_open_panel (t('add'));
                form_add_hidden ('apage', $apage);
                form_add_label (t('admin.settings.new_setting_step_2'));
                form_add_spacer ();
                form_add_hidden ('settings_insert[setting_value_type]', $settings_insert_1['setting_value_type']);
                form_add_hidden ('settings_insert[setting_key_type]', $settings_insert_1['setting_key_type']);
                if ($settings_insert_1['setting_group'])
                {
                    form_add_hidden ('settings_insert[setting_group]', $settings_insert_1['setting_group']);
                    form_add_label (t('admin.settings.group'), setting_caption($settings_insert_1['setting_group']));
                }
                else
                {
                    form_add_edit ('settings_insert[setting_group]', t('admin.settings.group'), '');
                }
                if ($settings_insert_1['setting_key_type'])
                {
                    form_add_select ('settings_insert[setting_key]', $settings_key_type['type_caption'], $settings_key_type['type_default'], $settings_key_type['type_options_table'], $settings_key_type['type_options_id'], $settings_key_type['type_options_caption'], str_replace("|","'",$settings_key_type['type_options_where']));
                }
                else
                {
                    form_add_edit ('settings_insert[setting_key]', t('admin.settings.key'), '');
                };
                if ($settings_insert_1['setting_value_type'])
                {
                    form_add_select ('settings_insert[setting_value]', $settings_value_type['type_caption'], $settings_value_type['type_default'], $settings_value_type['type_options_table'], $settings_value_type['type_options_id'], $settings_value_type['type_options_caption'], str_replace("|","'",$settings_value_type['type_options_where']));
                }
                else
                {
                    form_add_edit ('settings_insert[setting_value]', t('admin.settings.value'), '');
                };
                form_add_submit (t('finish'),t('cancel'));
                tabs_close_panel ();

            }
            else
            {
                tabs_open ();
                tabs_open_panel (t('add'));
                form_add_hidden ('apage', $apage);
                form_add_label (t('admin.settings.new_setting_step_1'));
                form_add_spacer ();
                form_add_select ('settings_insert_1[setting_group]', t('admin.settings.group'), '', 'settings', 'setting_group', "UPPER(REPLACE(setting_group,'_',' '))", "where setting_group!='' group by setting_group", t('admin.settings.custom','[Custom]'), '261', '');
                form_add_select ('settings_insert_1[setting_key_type]', t('admin.settings.key_type'), 0, 'settings_types', 'type_id', 'type_caption', '', t('admin.settings.custom','[Custom]'));
                form_add_select ('settings_insert_1[setting_value_type]', t('admin.settings.value_type'), 0, 'settings_types', 'type_id', 'type_caption', '', t('admin.settings.custom','[Custom]'));
                form_add_submit (t('next'));
                tabs_close_panel ();

            };
        }
        else
        {
            tabs_open ();
        };

        if ($value_type)
        {
            $search['settings']['setting_value_type'] = $value_type;
        };

        if ($search['settings']['setting_value_type'])
        {
            $settings_query = "where setting_value_type='".$search['settings']['setting_value_type']."'";
        }
        else
        {
            $settings_query = "";
        };

        tabs_open_panel (t('search'));
        form_add_hidden ('apage', $apage);
        form_add_select ('search[settings][setting_value_type]', t('admin.settings.value_type'), $search['settings']['setting_value_type'], 'settings_types', 'type_id', 'type_caption');
        form_add_submit (t('search'),t('clear'), $redirect_url."?apage=$apage&search[settings]=");
        tabs_close_panel (2);
        tabs_close ();

        if ($user_programmer)
        {
            $result = mysql_debug_query ("select * from settings order by setting_group,setting_key");
        }
        else
        {
            $result = mysql_debug_query ("select * from settings where setting_system=0 order by setting_group,setting_key");
        };

        if (mysql_num_rows($result))
        {
            table_open ();
            table_open_record_header ();
            table_add_cell_header (t('admin.settings.setting'));
            table_add_cell_header (t('admin.settings.value'));
            if ($user_programmer)
            {
                table_close_record (2);
            }
            else
            {
                table_close_record (1);
            };

            while ($row = mysql_fetch_assoc($result))
            {
                if ($setting_group_previous != $row['setting_group'])
                {
                    table_open_record_header ();
                    table_add_cell_header (setting_caption($row['setting_group']), 'colspan=4');
                    table_close_record (0);
                    $setting_group_previous = $row['setting_group'];
                };
                if (!$setting_group_previous) !$setting_group_previous = $row['setting_group'];
                if ($row['setting_key_type'])
                {
                    $settings_key_type = mysql_fetch_assoc (mysql_debug_query ("select * from settings_types where type_id='".$row['setting_key_type']."'"));
                    list ($row['setting_key']) = mysql_fetch_row(mysql_debug_query("select ".$settings_key_type['type_options_caption']." from ".$settings_key_type['type_options_table']." where ".$settings_key_type['type_options_id']."='".$row['setting_key']."'"));
                };
                if ($row['setting_value_type'])
                {
                    $settings_value_type = mysql_fetch_assoc (mysql_debug_query ("select * from settings_types where type_id='".$row['setting_value_type']."'"));
                    list ($row['setting_value']) = mysql_fetch_row(mysql_debug_query("select ".$settings_value_type['type_options_caption']." from ".$settings_value_type['type_options_table']." where ".$settings_value_type['type_options_id']."='".$row['setting_value']."'"));
                };
                table_open_record ();
                //table_add_cell ($row['setting_key_type']);
                table_add_cell (setting_caption($row['setting_key']));
                table_add_cell ($row['setting_value']);
                if ($user_programmer)
                {
                    table_add_cell_delete_ ('delete.png', $redirect_url."?apage=$apage&settings_delete=".$row['setting_id']);
                };
                table_add_cell_button_ ('edit.png', $redirect_url."?apage=$apage&settings_edit=".$row['setting_id']);
                table_close_record ();
            };
            table_close ();
        };
    };

?>