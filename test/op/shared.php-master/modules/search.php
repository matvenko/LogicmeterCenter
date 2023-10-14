<?php

    import_variable ('search_pages', 'SESSION');
    form_open ();
    form_add_hidden ('apage', $apage);
    form_add_edit ('search_pages', '', $search_pages);
    form_add_submit (t('search'));
    form_close (2);

    if ($search_pages)
    {

        $search_keys = explode(" ", $search_pages);
        foreach ($search_keys as $key => $search_key)
        {
            $search_query .= " ".field_name_localized('page_caption','site_pages')." like '$search_key%' or ".field_name_localized('page_caption','site_pages')." like '% $search_key%' or "
                                .field_name_localized('page_description','site_pages')." like '$search_key%' or ".field_name_localized('page_description','site_pages')." like ' %$search_key%' or "
                                .field_name_localized('page_tags','site_pages')." like 'search_key%' or ".field_name_localized('page_tags','site_pages')." like ' %$search_key%' or";
        };

        $search_query = before_last ('or', $search_query);

        $result = mysql_debug_query ("select page_id as `id`,
                                             ".field_name_localized('page_caption','site_pages')." as `name`,
                                             ".field_name_localized('page_description','site_pages')." as `description`,
                                             ".field_name_localized('page_tags','site_pages')." as `tags`
                                      from site_pages
                                      where (".$search_query.") and (".field_name_localized('page_description','site_pages')."!='')
                                    ");

        $html['page'] .= html_parse ($skins[$page['name']."_term"], array('search_term'=>$search_pages));
        if (mysql_num_rows($result))
        {
            while ($item = mysql_fetch_assoc($result))
            {
                $item['link'] = $project_url.seo_encode(array('apage'=>$item['id']));
                $html['page'] .= html_parse ($skins[$page['name']."_item"], $values+$item);
            };
        }
        else
        {
            $html['page'] .= html_parse ($skins[$page['name']."_fail"], array('search_term'=>$search_pages));
        };
    };

?>