<?php

    if (!is_array($menus))
    {
        $menus = array ();
        $result = mysql_debug_query ("select * from widgets_menus order by menu_widget,menu_order");
        if (mysql_num_rows($result))
        {
            while ($row = mysql_fetch_assoc($result))
            {
                foreach ($row as $key=>$value)
                {
                    $menus[$row['menu_widget']][$row['menu_id']][after('_',$key)] = $value;
                };
            };
        };
    };

    $menu_caption = after('menu_',field_name_localized('menu_caption','widgets_menus'));

    if (isset($menus[$widget['id']]))
    {
        foreach ($menus[$widget['id']] as $menu)
        {
            if ($pages[$menu['page']]['visible'] && ($menu['display']==0 || ($menu['display']==1 && user_logged()) || ($menu['display']==2 && !user_logged())))
            {
                $item = $html_globals + array('link'=>$project_url.seo_encode(array('lang'=>$lang, 'apage'=>$menu['page'])), 'caption'=>$menu[$menu_caption],'page'=>$menu['page'],'widget'=>$widget['name']);
                $item['caption'] = html_replace ($item['caption'],$html_globals);
                if ($item['link']{strlen($item['link'])-1}!='/')
                {
                    $item['link'] .= '/';
                }
                if ($apage==$menu['page'])
                {
                    $values['items'] .= html_parse ($skins[$widget['name']."_item_active"], $item);
                }
                else
                {
                    $values['items'] .= html_parse ($skins[$widget['name']."_item_passive"], $item);
                };
            };
        };
    }
    $html[$widget['name']] = html_parse ($skins[$widget['name']], $values);

?>