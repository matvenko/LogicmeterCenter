<?php

    if (isset($notifications_refresh))
    {
        mysql_debug_query ("delete from notifications_users where user_user='".user_id()."'");
        if (is_array($notifications_update))
        {
            foreach ($notifications_update as $key => $value)
            {
                if ($value)
                {
                    mysql_debug_query ("insert into notifications_users set user_user='".user_id()."', user_notification='".$key."'");
                };
            };
        };
    };

    $user_notifications = mysql_array ('notifications_users','user_notification','user_id',"where user_user='".user_id()."'", false);

    $result = mysql_debug_query ("select notification_id as `id`,".field_name_localized('notification_caption','notifications')." as `caption` from notifications order by ".field_name_localized('notification_caption','notifications')." asc");
    if (mysql_num_rows($result))
    {
        form_open ();
        form_add_hidden ('apage',$apage);
        form_add_hidden ('notifications_refresh',$apage);
        while ($row=mysql_fetch_assoc($result))
        {
            form_add_checkbox ('notifications_update['.$row['id'].']', $row['caption'], $user_notifications[$row['id']]);
        };
        form_add_spacer ();
        form_add_submit (t('save'));
        form_close (false, true, $widget_name);
    };

?>