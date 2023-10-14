<?php

    $result = mysql_debug_query ("select news_id as `id`,
                                         ".field_name_localized('news_name','modules_newss')." as `name`,
                                         ".field_name_localized('news_headline','modules_newss')." as `headline`,
                                         ".field_name_localized('news_body','modules_newss')." as `body`,
                                         ".field_name_localized('news_tags','modules_newss')." as `tags`,
                                         news_date as `date`,
                                         news_image as `isimage`
                                         from modules_newss where ".field_name_localized('news_name','modules_newss')."!='' order by news_date desc");
    if (mysql_num_rows($result))
    {
        while ($item = mysql_fetch_assoc($result))
        {
            /*
            //debug_echo ($uploads_images.tables_table_prefix($news_settings['news_table']).'image_'.$item['id'].'.jpg');
            if (file_exists($uploads_images.tables_table_prefix($news_settings['news_table']).'image_'.$item['id'].'.jpg'))
            {
                $item['image'] = html_parse ($skins[$page['name']."_image"], $values+$item);
            };
            */
            $item['url'] = $project_url.seo_encode(array('apage'=>$pages_settings['page_news'],'news'=>name_to_caption($item['name'])));
            if ($item['tags'])
            {
                $rss_tags = explode ($news_settings['tag_delimiter'], $item['tags']);
                $item['tags'] = '';
                foreach ($rss_tags as $key => $value)
                {
                    $item['tags'] .= html_parse ($skins[$page['name']."_tag"], array('name'=>$value));
                };
            };
            if (!$rss_settings['rss_date'])
            {
                $rss_settings['rss_date'] = date("D, d M Y H:i:s T", strtotime($item['date']));
            };
            $item['date'] = date("D, d M Y H:i:s T", strtotime($item['date']));
            $html['page'] .= html_parse ($skins[$page['name']."_item"], $values+$item);
        };
    };

    if (!is_array($rss_settings))
    {
        $rss_settings = array ();
    };

    foreach ($rss_settings as $key => $value)
    {
        html_define ($key, $value);
    };

    html_define ('rss_url', $project_url.seo_encode(array('apage'=>$pages_settings['page_rss'])));
    html_define ('rss_lang', $lang);
    html_define ('rss_build', date("D, d M Y H:i:s T"));

    $skins ['html'] = $skins['body'];

?>