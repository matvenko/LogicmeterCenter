<?php

    if (user_logged())
    {
        $result = $database->tables->messages->of ($user, 50);
        if (!$result)
        {
            $html[$widget_name] = html_parse ($skins[$widget_name.'_empty']);
        }
        else
        {
            foreach ($result as $message)
            {
                $value = array ();
                $value['body'] = $message->body;
                $value['date'] = datetime_to_text ($message->date);
                if (!$message->read)
                {
                    $values['items'] .= html_parse ($skins[$widget_name.'_item_new'], $value+$values);
                }
                else
                {
                    $values['items'] .= html_parse ($skins[$widget_name.'_item_old'], $value+$values);
                }
            }
            $html[$widget_name] = html_parse ($skins[$widget_name], $values);
        }
    }

    //$database->tables->messages->send (null, 12, 'ახალი შეტყობინება !');

?>
