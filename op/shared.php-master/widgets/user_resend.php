<?php

    tables_load_table ('users');
    tables_incomes_prepare ();

    if (is_array($insert))
    {
        if ($tables['users']['fields']['email']['pattern']!='' && !preg_match($tables['users']['fields']['email']['pattern'],$insert['user_email']))
        {
            form_set_error ('insert[user_email]', str_replace(array('[field_caption]'),array($tables['users']['fields']['email']['caption']),t('libs.tables.error_field_pattern','please specify [field_caption] correctly')));
        };
    };

    if (!$html_errors && is_array($insert))
    {
        $result = mysql_debug_query ("select * from users where user_email='".$insert['user_email']."' and user_status='".$users_settings['status_notactive']."' limit 1");
        if (mysql_num_rows($result))
        {
            $values += mysql_fetch_assoc($result);
            if (!mysql_num_rows(mysql_debug_query("select code_user from users_codes where code_user='".$values ['user_id']."' and code_type=1 and code_expires>=now() order by code_expires desc limit 1")))
            {
                $values ['user_code'] = md5(hash_with(5).time());
                mysql_debug_query ("insert into users_codes set code_type='1', code_code='".$values ['user_code']."', code_user='".$values ['user_id']."', code_expires=from_unixtime(unix_timestamp()+(24*3600))");
                $result = mysql_debug_query ("select * from templates_emails where email_id='".$templates_mail_activation."'");
                if (mysql_num_rows($result))
                {
                    $email = mysql_fetch_assoc ($result);
                    mail_utf8 ($values ['user_email'], '"'.$email['email_from_name'].'" <'.$email['email_from_email'].'>', html_replace($email[field_name_localized('email_subject','templates_emails')],$values), html_replace($email[field_name_localized('email_body','templates_emails')],$values));
                };
            };
            $html[$widget_name] = html_parse ($skins[$widget_name.'_success'], $values);
        }
        else
        {
            $html[$widget_name] = html_parse ($skins[$widget_name.'_fail'], $values+$insert);
        };
    }
    else
    {
        form_open ();
        form_add_hidden ('apage', $apage);
        form_add_edit ("insert[user_email]", $tables['users']['fields']['email']['caption'], $insert['user_email']);
        form_add_spacer ();
        form_add_submit (t('submit'));
        form_close ($widget_name);
    };


?>