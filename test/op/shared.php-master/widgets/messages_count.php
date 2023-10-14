<?php

    if (user_logged())
    {
        $values['count'] = $database->tables->messages->of ($user);
        if ($values['count'])
        {
            $values['new'] = html_parse ($skins[$widget_name.'_new'], $values);
        }
        $html[$widget_name] = html_parse ($skins[$widget_name], $values);
    }
?>
