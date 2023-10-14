<?php
    
    $result = mysql_debug_query ("select * from widgets_banners where banner_disabled=0");
    if (mysql_num_rows($result))
    {
        while ($row=mysql_fetch_assoc($result))
        {
            if ((!isset($html[$row['banner_name']]) && !isset($widgets[$row['banner_name']])) || $row['banner_name']==$widget_name)
            {
                if (isset($skins[$row['banner_name']]))
                {
                    $row['skin'] = &$skins[$row['banner_name']];
                }
                elseif (isset($skins[$widget_name]))
                {
                    $row['skin'] = &$skins[$widget_name];
                }
                else
                {
                    $row['skin'] = '{banner_source}';
                }
                $html[$row['banner_name']] = html_replace (html_parse($row['skin'], $row+$values),  $row+$values);
            }
        }
    }
    
?>
