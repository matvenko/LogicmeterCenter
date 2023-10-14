<?php


    if (true || $_SERVER['REMOTE_ADDR']=='217.11.164.7')
    {
        $skins['facebook_status_out'] = "
            <script language='javascript'>
            function facebook_login ()
            {
                FB.login(function(response)
                 {
                   if (response.authResponse)
                   {
                        document.location =  ('http://lit.ge/facebook_register/');
                   }
                   else
                   {

                   }
                 }
                 {permissions}
                 );
            }
            </script>
        <div class='bar' style='margin-left:5px;'>
            <div class='bar_item bar_item_left' style='border-radius:5px'>
                <img src='{image_dir_main}/facebook.png' style='cursor:pointer' onclick='facebook_login();' title='<span style=\"font-weight:bold;color:yellow\">გაიარეთ ავტორიზაცია ფეისბუქით</span>' {hint}>
            </div>
        </div>

        ";

        $skins['facebook_status_in'] = "
        <div class='bar' style='margin-left:5px;'>
            <img src='https://graph.facebook.com/{id}/picture' style='border-radius:5px;vertical-align:middle;width:27px;margin-right:3px;border:1px solid black;'>
        </div>
        ";

        //$facebook = new facebook();
        //echo $facebook->getLoginUrl();

//        if (!$facebook->logged() || (user_logged() && $facebook->user()!=user_id()) || (!user_logged()))
//        {
//            if (!user_logged())
//            {
//                $values['permissions'] = ", {scope: 'email'}";
//            }
//            $html[$widget_name] = html_parse ($skins[$widget_name.'_out'], $values);
//        }
//        else
//        {
//            $html[$widget_name] = html_parse ($skins[$widget_name.'_in'], array('id'=>$facebook->login['id'],'name_first'=>$facebook->login['first_name'],'name_last'=>$facebook->login['last_name']));
//        }


        if (!user_logged())
        {
            $values['permissions'] = ", {scope: 'email'}";
            $values['hint'] = "id='facebook_hint'";
            $html[$widget_name] = html_parse ($skins[$widget_name.'_out'], $values);
        }
        else
        {
            if ($user->facebook)
            {
                $html[$widget_name] = html_parse ($skins[$widget_name.'_in'], array('id'=>$user->facebook));
            }
            else
            {
                $html[$widget_name] = html_parse ($skins[$widget_name.'_out'], $values);
            }
        }
    }


?>
