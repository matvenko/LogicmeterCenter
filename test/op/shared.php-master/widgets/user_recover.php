<?php

    if ($user_code)
    {
         $result = mysql_debug_query("select code_user from users_codes where code_code='".$user_code."' and code_type=2 and code_expires>=now() order by code_expires desc limit 1");
         if (@mysql_num_rows($result))
         {
             list ($user_recover) = mysql_fetch_row ($result);
         }
         else
         {
             $user_recover = false;
         };
    }
    else
    {
        $user_recover = false;
    };


    if ($user_recover)
    {
        $values += mysql_fetch_assoc (mysql_debug_query ("select * from users where user_id='".$user_recover."'"));
        $values ['user_password'] = hash_with($users_settings['password_recover_length']);
        mysql_debug_query ("update users set user_password='".md5($values ['user_password'].$user_password_salt)."', user_email_last='".$values ['user_email']."', user_password_date='".to_nulldate()."' where user_id='".$user_recover."'");
        if (!mysql_num_rows(mysql_debug_query("select email_id from users_stats_emails where email_user='".$values['user_id']."' and email_email='".$values['user_email']."'")))
        {
            mysql_debug_query("insert into users_stats_emails set email_user='".$values['user_id']."', email_email='".$values['user_email']."', email_date='".to_nulldate()."'");
        };
        mysql_debug_query ("insert into users_stats_passwords set password_user='".user_id()."', password_date='".to_nulldate()."'");
        $result = mysql_debug_query ("select * from templates_emails where email_id='".$templates_mail_recover."'");
        if (mysql_num_rows($result))
        {
            $email = mysql_fetch_assoc ($result);
            mail_utf8 ($values ['user_email'], '"'.$email['email_from_name'].'" <'.$email['email_from_email'].'>', html_replace($email[field_name_localized('email_subject','templates_emails')],$values), html_replace($email[field_name_localized('email_body','templates_emails')],$values));
            mysql_debug_query ("delete from users_codes where code_user='".$user_recover."' and code_type=2");
        };
        $html[$widget_name] = html_parse ($skins[$widget_name.'_success'], $values);
    }
    else
    {
        $html[$widget_name] = html_parse ($skins[$widget_name.'_fail'], $values);
    };

?>