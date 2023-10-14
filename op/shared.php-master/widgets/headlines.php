<?php

    $result = mysql_debug_query ("select news_id as `id`,
                                         ".field_name_localized('news_name','modules_newss')." as `name`,
                                         ".field_name_localized('news_headline','modules_newss')." as `headline`,
                                         ".field_name_localized('news_body','modules_newss')." as `body`,
                                         ".field_name_localized('news_tags','modules_newss')." as `tags`,
                                         news_date as `date`,
                                         news_image as `isimage`
                                         from modules_newss where ".field_name_localized('news_name','modules_newss')."!='' order by news_date desc limit ".intval($headlines_settings['items_limit'])."");
    if (mysql_num_rows($result))
    {
        while ($item = mysql_fetch_assoc($result))
        {
            $values['items'] .= html_parse ($skins[$widget['name']."_item"], $values + $item + array('link_rss'=>$project_url.seo_encode(array('apage'=>$pages_settings['page_rss'])),'link_news'=>$project_url.seo_encode(array('apage'=>$pages_settings['page_news']))));
        };
        $html[$widget['name']] = html_parse ($skins[$widget['name']], $values + array('link_news'=>$project_url.seo_encode(array('apage'=>$pages_settings['page_rss']))));
    };
?>