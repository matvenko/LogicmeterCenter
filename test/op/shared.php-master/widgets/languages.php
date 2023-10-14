<?php

    foreach ($languages as $key => $value)
    {
        if (intval($key))
        {
            if ($lang==$value['name'])
            {
                $values['items'] .= html_parse ($skins[$widget_name."_item_active"], $values+$value);
            }
            else
            {
                $values['items'] .= html_parse ($skins[$widget_name."_item_passive"], $values+$value);
            };
        };
    };
    $html[$widget_name] = html_parse ($skins[$widget_name], $values);

?>