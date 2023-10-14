<?php

    if (isset($newsletters_refresh))
    {
        mysql_debug_query ("delete from newsletters_users where user_user='".user_id()."'");
        if (is_array($newsletters_update))
        {
            foreach ($newsletters_update as $key => $value)
            {
                if ($value)
                {
                    mysql_debug_query ("insert into newsletters_users set user_user='".user_id()."', user_newsletter='".$key."'");
                };
            };
        };
    };

    $user_newsletters = mysql_array ('newsletters_users','user_newsletter','user_id',"where user_user='".user_id()."'", false);

    $result = mysql_debug_query ("select newsletter_id as `id`,".field_name_localized('newsletter_name','newsletters')." as `name` from newsletters order by ".field_name_localized('newsletter_name','newsletters')." asc");
    if (mysql_num_rows($result))
    {
        form_open ();
        form_add_hidden ('apage',$apage);
        form_add_hidden ('newsletters_refresh',$apage);
        while ($row=mysql_fetch_assoc($result))
        {
            form_add_checkbox ('newsletters_update['.$row['id'].']', $row['name'], $user_newsletters[$row['id']]);
        };
        form_add_spacer ();
        form_add_submit (t('save'));
        form_close ();
    };

?>