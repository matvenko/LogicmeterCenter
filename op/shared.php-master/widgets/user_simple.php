<?php

    if (is_array($user_simple))
    {
        if (isset($user_simple['email']))
        {
            $user_simple['email'] = protect_string ($user_simple['email']);
        }
        if (isset($user_simple['password']))
        {
            $user_simple['password'] = protect_string ($user_simple['password']);
        }
        if (isset($user_simple['confirm']))
        {
            $user_simple['confirm'] = protect_string ($user_simple['confirm']);
        }
        if (!$user_simple['email'])
        {
            form_set_error ('user_simple[email]',t('user_simple.please_specify_email'));
        }
        else if (!is_email($user_simple['email']))
        {
            form_set_error ('user_simple[email]',t('user_simple.please_specify_valid_email'));
        }
        else if (mysql_value_query("select user_email from users where user_email='".$user_simple['email']."'"))
        {
            form_set_error ('user_simple[email]',t('user_simple.email_already_registered'));
        }

        if (!$user_simple['password'] || !$user_simple['confirm'])
        {
            if (!$user_simple['password'])
            {
                form_set_error ('user_simple[password]',t('user_simple.please_specify_password'));
            }
            if (!$user_simple['confirm'])
            {
                form_set_error ('user_simple[confirm]',t('user_simple.please_confirm_password'));
            }
        }
        else if ($user_simple['password'] && strlen($user_simple['password'])<$tables['users']['fields']['password']['min'])
        {
            form_set_error ('user_simple[password]',html_replace(t('user_simple.password_must_contain_min_symbols','Password must contain minimum [length] symbols'),array('length'=>$tables['users']['fields']['password']['min'])));
        }
        else if ($user_simple['password'] && $user_simple['confirm'] && $user_simple['password']!=$user_simple['confirm'])
        {
            form_set_error ('user_simple[password]');
            form_set_error ('user_simple[confirm]',t('user_simple.passwords_did_not_match'));
        }
        if (!$user_simple['rules'])
        {
            form_set_error ('user_simple[rules]');
        }
    }

    if (is_array($user_simple) && !$html_errors)
    {
        $user = array ();
        $user['user_email'] = $user_simple['email'];
        $user['user_login'] = $user_simple['email'];
        $user['user_joined'] = to_nulldate();
        $user['user_password'] = user_password ($password);

        mysql_debug_query ("insert into users set
            user_guid=uuid(),
            user_login='".$user['user_login']."',
            user_password='".$user['user_password']."',
            user_email='".$user['user_email']."',
            user_joined='".$user['user_joined']."',
            user_status=1
            ");
        if (!mysql_error())
        {
            $user['user_code'] = md5(hash_with(5).time());
            $user['user_id'] = mysql_insert_id();
            mysql_debug_query ("insert into users_codes set code_type='1', code_code='".$user['user_code']."', code_user='".$user['user_id']."', code_expires=from_unixtime(unix_timestamp()+(24*3600))");
            $values += $user;

            $result = mysql_debug_query ("select * from templates_emails where email_id='".$templates_mail_activation."'");
            if (mysql_num_rows($result))
            {
                $email = mysql_fetch_assoc ($result);
                @mail_utf8 (
                $values ['user_email'],
                '"'.$email['email_from_name'].'" <'.$email['email_from_email'].'>',
                html_replace($email[field_name_localized('email_subject','templates_emails')], $values),
                html_replace($email[field_name_localized('email_body','templates_emails')], $values));
            };

            if (defined('engine_parts'))
            {
                $html[$widget_name] = html_parse($skins[$widget_name.'_success'], $user+$values);
            }
            else
            {
                $apage = $pages['user_success']['id'];
            }
        }

    }
    else
    {
        $html[$widget_name] .= html_parse ($skins[$widget_name.'_rules']);
        form_open ();
        form_add_hidden ('apage', $apage);
        form_add_edit ('user_simple[email]', t('user_simple.email'), $user_simple['email']);
        form_add_spacer ();
        form_add_password ('user_simple[password]', t('user_simple.password'));
        form_add_password ('user_simple[confirm]', t('user_simple.confirm_password'));
        form_add_checkbox ('user_simple[rules]', "<a href='#' style='color:inherit' onclick=\"$('#user_simple_rules').dialog({height:500,width:500,modal:true})\">".t('user_simple.please_read_and_accept_site_rules').'</a>',$user_simple['rules']);
        form_add_spacer ();
        form_add_submit (t('register'));
        form_close ($widget_name);
    }


?>