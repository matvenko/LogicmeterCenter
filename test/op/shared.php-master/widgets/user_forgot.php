<?php

    if ($image)
    {
        srand ();
        $code_plain = hash_with (5, 'abcdefghjkmnpqrst23456789');
        setcookie ('code_hash', md5(md5(strtolower($code_plain))));
        image_flood ($code_plain);
        exit;
    };

    function register_field_required ($name)
    {
        if (isset($GLOBALS['fields'][$name]) && $GLOBALS['fields'][$name]['register'] && $GLOBALS['fields'][$name]['required'])
        {
            return true;
        }
        return false;
    }

    tables_load_table ('users');
    tables_incomes_prepare ();

    if (is_array($insert))
    {
        if (register_field_required('birth_date') && (!intval(before('-',$insert['user_birth_date'])) || !intval(between('-','-',$insert['user_birth_date'])) || !intval(after_last('-',$insert['user_birth_date']))))
        {
            form_set_error ('insert[user_birth_date]', str_replace(array('[field_caption]'),array($tables['users']['fields']['birth_date']['caption']),t('libs.tables.error_field_birthday','day month and year required<br>for [field_caption]')));
        };
        if (register_field_required('name_first') && !$insert['user_name_first'])
        {
            form_set_error ('insert[user_name_first]', str_replace(array('[field_caption]'),array($tables['users']['fields']['name_first']['caption']),t('libs.tables.error_field_required','please specify [field_caption]')));
        };
        if (register_field_required('name_last') && !$insert['user_name_last'])
        {
            form_set_error ('insert[user_name_last]', str_replace(array('[field_caption]'),array($tables['users']['fields']['name_last']['caption']),t('libs.tables.error_field_required','please specify [field_caption]')));
        };
        if ($tables['users']['fields']['email']['pattern']!='' && !preg_match($tables['users']['fields']['email']['pattern'],$insert['user_email']))
        {
            form_set_error ('insert[user_email]', str_replace(array('[field_caption]'),array($tables['users']['fields']['email']['caption']),t('libs.tables.error_field_pattern','please specify [field_caption] correctly')));
        };
        if (!$insert['code'] || $_COOKIE['code_hash']!=md5(md5(strtolower($insert['code']))))
        {
            form_set_error ('insert[image]');
            if (!$insert['code'])
            {
                form_set_error ('insert[code]', t('libs.tables.error_turing_specify','specify code on the image'));
            }
            else
            {
                form_set_error ('insert[code]', t('libs.tables.error_turing_match','code does not match'));
            };
        };
    };

    if (!$html_errors && is_array($insert))
    {
        $user_remember_query = '';
        if (register_field_required('name_first'))
        {
            $user_remember_query .= " and user_name_first like '".$insert['user_name_first']."'";
        };

        if (register_field_required('name_last'))
        {
            $user_remember_query .= " and user_name_last like '".$insert['user_name_last']."'";
        };

        if (register_field_required('birth_date'))
        {
            $user_remember_query .= " and user_birth_date='".$insert['user_birth_date']."'";
        };

        $result = mysql_debug_query ("select * from users where user_email='".$insert['user_email']."' ".$user_remember_query." limit 1");

        if (mysql_num_rows($result))
        {
            $values += mysql_fetch_assoc($result);
            if (!mysql_num_rows(mysql_debug_query("select code_user from users_codes where code_user='".$values ['user_id']."' and code_type=2 and code_expires>=now() order by code_expires desc limit 1")))
            {
                $values ['user_code'] = md5(hash_with(5).time());
                mysql_debug_query ("insert into users_codes set code_type='2', code_code='".$values ['user_code']."', code_user='".$values ['user_id']."', code_expires=from_unixtime(unix_timestamp()+(24*3600))");
                $result = mysql_debug_query ("select * from templates_emails where email_id='".$templates_mail_forgot."'");
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
            $values += $insert;
            $html[$widget_name] = html_parse ($skins[$widget_name.'_fail'], $values);
        };
        //html_add_redirect ($project_url.seo_encode(array('apage'=>$pages_settings['page_forgot_sent'])));
    }
    else
    {
        form_open ($widget_name);
        form_add_hidden ('apage', $apage);
        form_add_edit ("insert[user_email]", $tables['users']['fields']['email']['caption'], $insert['user_email']);
        form_add_spacer ();
        if (register_field_required('name_first'))
        {
            form_add_edit ("insert[user_name_first]", $tables['users']['fields']['name_first']['caption'], $insert['user_name_first']);
        };
        if (register_field_required('name_last'))
        {
            form_add_edit ("insert[user_name_last]", $tables['users']['fields']['name_last']['caption'], $insert['user_name_last']);
        };
        if (register_field_required('name_last') && !$insert['user_name_first'])
        {
            form_add_spacer ();
        };
        if (register_field_required('birth_date'))
        {
            form_add_birthday ("insert[user_birth_date]", $tables['users']['fields']['birth_date']['caption'], $insert['user_birth_date']);
            form_add_spacer ();
        };
        form_add_image ('insert[image]', t('libs.tables.specify_code_on_the_image'), $redirect_url."?apage=$apage&image=".md5($time));
        form_add_edit ('insert[code]', '');
        form_add_spacer ();

        form_add_submit (t('submit'));
        form_close ($widget_name);
    };


?>