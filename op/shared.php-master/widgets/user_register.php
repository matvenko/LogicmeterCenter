<?php

    if (!user_logged())
    {
        function register_field_required ($name)
        {
            if (isset($GLOBALS['fields'][$name]) && $GLOBALS['fields'][$name]['register'] && $GLOBALS['fields'][$name]['required'])
            {
                return true;
            }
            return false;
        }

        if ($image)
        {
            srand ();
            $code_plain = hash_with (5, 'abcdefghjkmnpqrst23456789');
            setcookie ('code_hash', md5(md5(strtolower($code_plain))));
            image_flood ($code_plain);
            exit;
        };

        tables_load_table ('users');

        tables_incomes_prepare ();

        if (is_array($register))
        {
            if (register_field_required('question'))
            {
                if (!$register['question'])
                {
                    form_set_error ('register[question]', t('libs.tables.error_question','you must selcet secret question'));
                }
                if (!$register['answer'])
                {
                    form_set_error ('register[answer]', t('libs.tables.error_answer','you must answer secret question'));
                }
            };
            if (true)
            {
                if (!$register['code'] || $_COOKIE['code_hash']!=md5(md5(strtolower($register['code']))))
                {
                    form_set_error ('register[image]');
                    if ($register['code'])
                    {
                        form_set_error ('register[code]', t('libs.tables.error_turing_specify','specify code on the image'));
                    }
                    else
                    {
                        form_set_error ('register[code]', t('libs.tables.error_turing_match','code does not match'));
                    };
                };
            };
            if (!$register['agree'])
            {
                form_set_error ('register[agree]', t('libs.tables.error_agree','you must agree rules to register'));
            }
        };

        if (is_array($insert))
        {
            if (strpos($insert['user_login'],'@'))
            {
                form_set_error ('insert[user_login]', str_replace('[field]',$fields['login']['caption'],t('libs.tables.error_login_email','[field] must not contain @ symbol')));
            }
        }


        if (!$users_settings['disable_last_email'] && $tables['users']['fields']['email']['unique'] && isset($tables['users']['fields']['email']) && is_array($insert))
        {
            if ($insert['user_email'] && mysql_num_rows(mysql_debug_query("select user_email_last from users where user_email_last='".$insert['user_email']."'")))
            {
                form_set_error ('insert[user_email]', str_replace(array('[field_caption]','[field_value]'),array($tables['users']['fields']['email']['caption'],$insert['user_email']),t('libs.tables.error_field_unique','[field_caption] [field_value]<br>already exists')));
            };
        };

        if ($user_new=tables_insert_case())
        {
            $values += mysql_fetch_assoc(mysql_debug_query("select * from users where user_id='".$user_new."'"));
            if ($referral)
            {
                if ($user_referrer = mysql_value('users','user_id','user_referral',"'".str_replace("'","",$referral)."'"))
                {
                    mysql_debug_query ("insert into users_referrals set referral_referrer='$user_referrer', referral_user='".$user_new."', referral_date='".to_nulldate()."'");
                };
            };
            if (!isset($register_fields['question']) || intval($register_fields['question']))
            {
                mysql_debug_query ("update users set user_referral='".hash_with(5,'abcdefghjkmnpqrst23456789')."' where user_id='".$user_new."'");
                mysql_debug_query ("insert into users_questions_answers set answer_user='".$user_new."', answer_question='".$register['question']."', answer_answer='".$register['answer']."'");
            };

            $values['user_code'] = md5(hash_with(5).time());
            mysql_debug_query ("insert into users_codes set code_type='1', code_code='".$values['user_code']."', code_user='".$user_new."', code_expires=from_unixtime(unix_timestamp()+(24*3600))");
            $result = mysql_debug_query ("select * from templates_emails where email_id='".$templates_mail_activation."'");
            if (mysql_num_rows($result))
            {
                $email = mysql_fetch_assoc ($result);
                @mail_utf8 ($values ['user_email'], '"'.$email['email_from_name'].'" <'.$email['email_from_email'].'>', html_replace($email[field_name_localized('email_subject','templates_emails')],$values), html_replace($email[field_name_localized('email_body','templates_emails')],$values));
            };

            $register_values = $values;
            if (defined('engine_parts'))
            {
                $html[$widget_name] = html_parse($skins[$widget_name.'_success'], $html_globals+$register_values);
            }
            else
            {
                $apage = $pages['user_success']['id'];
            }
        }
        else
        {
            tables_register_form ();
        };

        //debug_var ($html_errors);
    }
?>