<?php

    if (user_logged())
    {
        $items = '';
        if (is_array($transactions_systems))
        {
            foreach ($transactions_systems as $key => $value)
            {
                if (intval($key) && $value['type']==1 && $value['methods'][$transactions_types['withdraw']['id']])
                {
                    $items .= html_parse ($skins[$widget_name.'_item'], array('name'=>$value['name'],'caption'=>$value['caption'],'link'=>$project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$key,'transactions_type'=>$transactions_types['withdraw']['id'], 'transactions_state'=>$transactions_states['init']['id'])))+$html_globals);
                };
            };
            $html[$widget_name] .= html_parse ($skins[$widget_name], array('items'=>$items)+$html_globals);
        };
    };

?>