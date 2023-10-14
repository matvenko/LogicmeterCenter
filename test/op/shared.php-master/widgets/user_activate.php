<?php

    if ($user_code)
    {
         $result = mysql_debug_query("select code_user from users_codes where code_code='".$user_code."' and code_type=1 and code_expires>=now() order by code_expires desc limit 1");
         if (@mysql_num_rows($result))
         {
             list ($user_activate) = mysql_fetch_row ($result);
         }
         else
         {
             $user_activate = false;
         };
    }
    else
    {
        $user_activate = false;
    };

    if ($user_activate && mysql_value('users','user_status','user_id',$user_activate)==$users_settings['status_active'])
    {
        $values += mysql_fetch_assoc (mysql_debug_query ("select * from users where user_id='".$user_activate."'"));
        $html[$widget_name] .= html_replace ($skins[$widget_name.'_already'], $values);
    }
    elseif ($user_activate)
    {
        $values += mysql_fetch_assoc (mysql_debug_query ("select * from users where user_id='".$user_activate."'"));
        mysql_debug_query ("update users set user_status=".$users_settings['status_active'].", user_email_last='".$values['user_email']."', user_email_last_date='".to_nulldate()."' where user_id='".$user_activate."'");
        mysql_debug_query("insert into users_stats_emails set email_user='".$values['user_id']."', email_email='".$values['user_email']."', email_date='".to_nulldate()."'");
        $html[$widget_name] .= html_replace ($skins[$widget_name.'_success'], $values);
    }
    else
    {
        $html[$widget_name] .= html_replace ($skins[$widget_name.'_fail'], $values);
    };

?>