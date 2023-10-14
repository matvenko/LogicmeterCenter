<?php

    if ($facebook->getUser())
    {
        facebook_echo ("for the moment we have user");
    }


    $skins['facebook_register_begin'] = "
        <div id='facebook_register_container'>
            <script language='javascript'>
            function facebook_login ()
            {
                FB.login(function(response)
                 {
                   if (response.authResponse)
                   {
                        document.location =  ('http://lit.ge/facebook_register/');
                        //facebook_register_widget.process();
                   }
                   else
                   {

                   }
                 },
                 {scope: 'email'}
                 );
            }
            </script>
            <div style='margin:30px;background-color:#526da4;border: white 1px solid;padding:4px;width:380px;color:white;cursor:pointer;font-weight:bold;font-size:14px' onclick='facebook_login();'><img src='{image_dir_main}/facebook_login.gif' style='vertical-align:middle;'> გაიარეთ ავტორიზაცია Facebook-ის ექაუნთით</div>
        </div>
    ";


    $skins['facebook_register_login'] = "
    <div id='facebook_register_container'>
        <script language='javascript'>
        function facebook_login ()
        {
            FB.login(function(response)
             {
               if (response.authResponse)
               {
                    document.location =  ('http://lit.ge/facebook_register/');
                    //facebook_register_widget.process();
               }
               else
               {
                facebook_register_widget.process();
               }
             },
             {scope: 'email'}
             );
        }
        </script>
        <div style='margin:30px;background-color:#526da4;border: white 1px solid;padding:4px;width:135px;color:white;cursor:pointer;font-weight:bold;font-size:14px' onclick='facebook_login();'><img src='{image_dir_main}/facebook_login.gif' style='vertical-align:middle;'> ავტორიზაცია</div>
    </div>
    ";

    $skins['facebook_register_info'] = "
    <script language='javascript'>
        //facebook_register_widget.client.method = 'POST';
    </script>
    <tr>
        <td><img src='https://graph.facebook.com/{id}/picture' style='vertical-align:middle'></td>
        <td>{name_first} {name_last}</td>
    </tr>
    ";

    //$facebook = new facebook();

    //echo "0";

    if (!$facebook->logged())
    {
        $html[$widget_name] = html_parse ($skins[$widget_name.'_begin']);
    }
    elseif ($facebook->user())
    {
        if (!user_logged())
        {
            $html[$widget_name] = html_parse ($skins[$widget_name.'_login']);
        }
        else// if ($apage!==$pages_settings['page_login_default'] || defined('engine_parts'))
        {
            //debug_var ($pages_settings['page_login_default']);
            html_add_redirect ($redirect_url.'?apage='.$pages_settings['page_login_default'], 0, true);
            html_add_message ('თქვენ უკვე გააქტიურებული გაქვთ ფეისბუქის ექაუნთი. მიმდინარეობს გადამისამართება, გთხოვთ დაიცადოთ ან დააჭიროთ <a href="http://lit.ge/home/">აქ</a>.', $widget_name);
            //html_add_redirect ($redirect_url.'?apage='.$pages_settings['page_default'], 0, true);
            //$apage = $pages_settings['page_login_default'];
            if (defined('engine_parts'))
            {
                echo "<script>alert(11);document.location=('".$redirect_url.'?apage='.$pages_settings['page_login_default']."')</script>";
            }
        }
    }
    else
    {
        //echo "inside";
        //echo "3";
        //echo $facebook->getUser();
        if (is_array ($facebook_register))
        {
            if (!$facebook_register['password'])
            {
                form_set_error ('facebook_register[password]', "მიუთითეთ პაროლი");
            }
            elseif (strlen($facebook_register['password'])<6)
            {
                form_set_error ('facebook_register[password]', "პაროლი უნდა შეიცავდეს<br>მინიმუმ 6 სიმბოლოს");
            }
            elseif ($facebook_register['password_confirm'] && $facebook_register['password_confirm']!=$facebook_register['password'])
            {
                form_set_error ('facebook_register[password]', "პაროლები არ ემთხვევა");
                form_set_error ('facebook_register[password_confirm]', "პაროლები არ ემთხვევა");
            }

            if (!$facebook_register['password_confirm'])
            {
                form_set_error ('facebook_register[password_confirm]', "დაადასტურეთ პაროლი");
            }
        }

        if (is_array($facebook_apply))
        {
            if (user_logged())
            {
                $facebook_apply['username'] = user_login ();
            }
            if (!$facebook_apply['username'] && !user_logged())
            {
                form_set_error ('facebook_apply[username]', "მიუთითეთ მომხმარებელი");
            }
            elseif (!user_logged() && !mysql_value_query("select 1 from users where user_login='".protect_string($facebook_apply['username'])."' limit 1"))
            {
                form_set_error ('facebook_apply[password]', "არასწორი მომხმარებლის სახელი ან პაროლი");
            }
            elseif ($facebook_apply['password'] && !mysql_value_query("select 1 from users where user_login='".protect_string($facebook_apply['username'])."' and user_password='".user_password($facebook_apply['password'])."' limit 1"))
            {
                form_set_error ('facebook_apply[password]', "არასწორი მომხმარებლის სახელი ან პაროლი");
            }

            if (!$facebook_apply['password'])
            {
                form_set_error ('facebook_apply[password]', "მიუთითეთ პაროლი");
            }
        }

        if ((is_array($facebook_register) || is_array($facebook_apply)) && !$html_errors)
        {
            //echo "inside procedures";
            //debug_var ($facebook_apply);
            if ($facebook_apply)
            {
                //echo "inside apply";
                if ($facebook->apply ($facebook_apply['username'], $facebook_apply['password']))
                {
                    html_add_redirect ($redirect_url.'?apage='.$pages_settings['page_login_default'], 0, true);
                    html_add_message ('თქვენ წარმატებით გაიარეთ ფეისბუქის ავტორიზება. მიმდინარეობს გადამისამართება გთხოვთ დაიცადოთ ან დააჭიროთ <a href="http://lit.ge/home/">აქ</a>.', $widget_name);
                    if (defined('engine_parts'))
                    {
                        echo "<script>alert(22);document.location=('".$redirect_url.'?apage='.$pages_settings['page_login_default']."')</script>";
                    }
                }
            }
            elseif ($facebook_register && !user_logged())
            {
                if ($facebook->register ($facebook->email(), $facebook_register['password']))
                {
                    html_add_redirect ($redirect_url.'?apage='.$pages_settings['page_login_default'], 0, true);
                    html_add_message ('თქვენ წარმატებით გაიარეთ ფეისბუქის ავტორიზება. მიმდინარეობს გადამისამართება გთხოვთ დაიცადოთ ან დააჭიროთ <a href="http://lit.ge/home/">აქ</a>.', $widget_name);
                    if (defined('engine_parts'))
                    {
                        echo "<script>alert(33);document.location=('".$redirect_url.'?apage='.$pages_settings['page_login_default']."')</script>";
                    }
                }
            }
            if ($facebook->error)
            {
                //echo "inside error";
                switch ($facebook->error)
                {
                    case facebook::error_already_registered:
                        html_add_message ('თქვენ უკვე რეგისტრირებული ხართ', $widget_name);
                    break;
                    case facebook::error_email_exists:
                        html_add_message ('მომხმარებელი მითითებული ელ-ფოტით უკვე არსებობს', $widget_name);
                    break;
                    case facebook::error_email_notexists:
                        html_add_message ('მომხმარებელი მითითებული ელ-ფოტით არ არსებობს', $widget_name);
                    break;
                    case facebook::error_email_required:
                        html_add_message ('მიუთითეთ ელ-ფოსტა', $widget_name);
                    break;
                    case facebook::error_login_required:
                        html_add_message ('თქვენ არ ხართ ავტორიზებული ფეისბუქით', $widget_name);
                    break;
                    case facebook::error_password_incorrect:
                        html_add_message ('არასწორი მომხმარებლის პაროლი', $widget_name);
                    break;
                    case facebook::error_password_required:
                        html_add_message ('მიუთითეთ პაროლი', $widget_name);
                    break;

                    case facebook::error_username_required:
                        html_add_message ('მიუთითეთ მომხმარებელი', $widget_name);
                    break;
                    case facebook::error_username_notexists:
                        html_add_message ('არასწორი მომხმარებლის სახელი', $widget_name);
                    break;
                }
            }
        }
        else
        {

            if ($facebook->email())
            {
                //$html[$widget_name]
                $facebook_info = html_parse ($skins[$widget_name.'_info'], array('id'=>$facebook->login['id'],'name_first'=>$facebook->login['first_name'],'name_last'=>$facebook->login['last_name']));
                $html['user_register'] = '';
                if (!user_logged())
                {
                    $facebook_caption = "თუ";
                    $facebook_exists = $facebook->exists($facebook->email());
                    if (!$facebook_exists)
                    {
                        form_open ("facebook_register_create");
                        form_add_label ("<h1>Facebook-ით ავტორიზაცია</h1>");
                        form_add_custom ($facebook_info);
                        form_add_hidden ('apage', $apage);
                        form_add_spacer ();
                        form_add_password ('facebook_register[password]', "შეარჩიეთ პაროლი lit.ge-სთვის");
                        form_add_password ('facebook_register[password_confirm]', "დაადასტურეთ პაროლი");
                        form_add_submit ('დასრულება');
                        //form_add_button ('დასრულება', $widget_name."_widget.form='facebook_register_create_form'; ".$widget_name."_widget.process()");
                        form_close ($widget_name);
                    }
                    else
                    {
                        $facebook_caption = "თქვენ";
                        //html_add_message ("ფეისბუქის მეილით lit.ge-ს მომხმარებელი უკვე არსებობს");
                    }
                }

                if (user_logged())
                {
                    form_open ("facebook_register_attach");
                    form_add_hidden ('apage', $apage);
                    if (user_logged ())
                    {
                        form_add_label ("<h1>Facebook-ის აქტივაცია</h1>");
                    }
                    else
                    {
                        form_add_label ("<h1>Facebook-ის lit.ge-ს ანგარიშთან დაკავშირება</h1>");
                    }
                    form_add_custom ($facebook_info);
                    form_add_spacer ();
                    if (!user_logged())
                    {
                        if ($facebook_exists)
                        {
                            $facebook_login  = mysql_value_query("select user_login from users where user_id='".$facebook_exists."'");
                            form_add_label ("lit.ge-ს მომხმარებლის სახელი", $facebook_login);
                            form_add_hidden ("facebook_apply[username]", $facebook_login);
                        }
                        else
                        {
                            form_add_edit ('facebook_apply[username]', "მიუთითეთ lit.ge-ს მომხმარებლის სახელი", $facebook_apply['username']);
                        }
                    }
                    else
                    {
                        //form_add_label ("<b>მიაბით თქვენი Facebook-ის ანგარიში lit.ge-ს ანგარიშს.</b>");
                        form_add_spacer ();
                        form_add_label ("მომხმარებლის სახელი", "<b>".user_login()."</b>");
                    }
                    form_add_password ('facebook_apply[password]', "მიუთითეთ lit.ge-ს პაროლი");
                    form_add_submit ('დასრულება');
                    //form_add_button ('დასრულება', $widget_name."_widget.form='facebook_register_attach_form'; ".$widget_name."_widget.process()");
                    form_close ($widget_name);
                }
            }
            else
            {
                html_add_message ("მოხდა შეცდომა ფეისბუქის ელ-ფოსტის მისამართის მიღების დროს");
            }
        }

    }

    //debug_var ($facebook->login);


?>
