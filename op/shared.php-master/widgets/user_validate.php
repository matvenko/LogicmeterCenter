<?php

    if ($user_code)
    {
        if ($user_code)
        {
             $result = mysql_debug_query("select code_user from users_codes where code_code='".$user_code."' and code_type=3 and code_expires>=now() order by code_expires desc limit 1");
             if (@mysql_num_rows($result))
             {
                 list ($user_validate) = mysql_fetch_row ($result);
             }
             else
             {
                 $user_validate = false;
             };
        }
        else
        {
            $user_validate = false;
        };


        if ($user_validate)
        {
            $values += mysql_fetch_assoc (mysql_debug_query ("select * from users where user_id='".$user_validate."'"));
            mysql_debug_query ("update users set user_email_last='".$values['user_email']."', user_email_last_date='".to_nulldate()."' where user_id='".$user_validate."'");
            if (!mysql_num_rows(mysql_debug_query("select email_id from users_stats_emails where email_user='".$values['user_id']."' and email_email='".$values['user_email']."'")))
            {
                mysql_debug_query ("insert into users_stats_emails set email_user='".$values['user_id']."', email_email='".$values['user_email']."', email_date='".to_nulldate()."'");
            };
            mysql_debug_query ("delete from users_codes where code_user='".$user_validate."' and code_type=3");
            $html[$widget_name] = html_parse ($skins[$widget_name.'_success'], $values);
        }
        else
        {
            $html[$widget_name] = html_parse ($skins[$widget_name.'_fail'], $values);
        };
    }
    else
    {
        $result = mysql_fetch_assoc (mysql_debug_query ("select * from users where user_id='".user_id()."'"));
        if (!$result)
        {
            html_add_redirect ($redirect_url.'?apage='.$pages_settings['page_default']);
        }
        $values += $result;
        if ($values['user_email']!=$values['user_email_last'] && !mysql_num_rows(mysql_debug_query("select code_user from users_codes where code_code='".user_id()."' and code_type=3 and code_expires>=now() order by code_expires desc limit 1")))
        {
            $values ['user_code'] = md5(hash_with(5).time());
            mysql_debug_query ("insert into users_codes set code_type='3', code_code='".$values['user_code']."', code_user='".user_id()."', code_expires=from_unixtime(unix_timestamp()+(24*3600))");
            $result = mysql_debug_query ("select * from templates_emails where email_id='".$templates_mail_validate."'");
            if (mysql_num_rows($result))
            {
                $email = mysql_fetch_assoc ($result);
                mail_utf8 ($values ['user_email'], '"'.$email['email_from_name'].'" <'.$email['email_from_email'].'>', html_replace($email[field_name_localized('email_subject','templates_emails')],$values), html_replace($email[field_name_localized('email_body','templates_emails')],$values));
            };
        };
        $html[$widget_name] = html_parse ($skins[$widget_name.'_send'], $values);
    };


?>