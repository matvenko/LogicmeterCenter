<?php

    $result = mysql_debug_query ("select news_id as `id`,
                                         ".field_name_localized('news_name','modules_newss')." as `name`,
                                         ".field_name_localized('news_headline','modules_newss')." as `headline`,
                                         ".field_name_localized('news_body','modules_newss')." as `body`,
                                         ".field_name_localized('news_tags','modules_newss')." as `tags`,
                                         news_date as `date`,
                                         news_image as `isimage`
                                         from modules_newss where ".field_name_localized('news_name','modules_newss')."!='' order by news_date desc limit ".intval($news_settings['items_limit'])."");
    if (mysql_num_rows($result))
    {
        while ($item = mysql_fetch_assoc($result))
        {
            $item['date'] = datetime_to_text ($item['date']);
            if (file_exists($uploads_images.tables_table_prefix($news_settings['news_table']).'image_'.$item['id'].'.jpg'))
            {
                $item['image'] = html_parse ($skins[$page['name']."_item_image"], $values+$item);
            };
            $html['page'] .= html_parse ($skins[$page['name']."_item"], $values+$item);
        };
    };

?>