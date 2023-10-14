<?php

    tables_load_table ('users');
    tables_incomes_prepare ();

    if (is_array($insert))
    {
        if (!$insert['user_password_old'])
        {
            form_set_error ('insert[user_password_old]', t('site.user_password.please_fill_password_old'));
        }
        elseif (!mysql_value('users','user_id','user_id',"'".user_id()."' and user_password='".md5($insert['user_password_old'].$user_password_salt)."'"))
        {
            form_set_error ('insert[user_password_old]', t('site.user_password.old_password_didnot_match'));
        };

        if (!$insert['user_password_new'])
        {
            form_set_error ('insert[user_password_new]', t('site.user_password.please_fill_password_new'));
        };

        if (!$insert['user_password_confirm'])
        {
            form_set_error ('insert[user_password_confirm]', t('site.user_password.please_fill_password_confirm'));
        };

        if ($insert['user_password_new'] && $insert['user_password_confirm'] && $insert['user_password_new']!=$insert['user_password_confirm'])
        {
            form_set_error ('insert[user_password_confirm]', t('site.user_password.passwords_didnot_match'));
        };
    };

    if (!$html_errors && is_array($insert))
    {
        mysql_debug_query ("update users set user_password='".md5($insert['user_password_new'].$user_password_salt)."', user_password_date='".to_nulldate()."' where user_id='".user_id()."'");
        if (!mysql_errno())
        {
            $_SESSION['password'] = md5($insert['user_password_new'].$user_password_salt);
            mysql_debug_query ("insert into users_stats_passwords set password_user='".user_id()."', password_date='".to_nulldate()."'");
            $result = mysql_debug_query ("select * from users where user_id='".user_id()."' limit 1");
            if (mysql_num_rows($result))
            {
                $values += mysql_fetch_assoc($result);
                $html[$widget_name] = html_parse ($skins[$widget_name.'_success'], $values);
            };
        }
        else
        {
            $html[$widget_name] = html_parse ($skins[$widget_name.'_fail'], $values);
        };
    }
    else
    {
        form_open ($widget_name);
        form_add_hidden ('apage', $apage);
        form_add_password ("insert[user_password_old]", t('site.user_password.password_old'), '');
        form_add_spacer ();
        form_add_password ("insert[user_password_new]", t('site.user_password.password_new'), '');
        form_add_password ("insert[user_password_confirm]", t('site.user_password.password_confirm'), '');
        form_add_spacer ();
        form_add_submit (t('change'));
        form_close ($widget_name);

    }
?>